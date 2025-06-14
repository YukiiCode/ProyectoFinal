<script setup>
import { ref, onMounted, computed, nextTick, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useNotifications } from '@/composables/useNotifications';
import { useI18n } from 'vue-i18n';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Calendar from 'primevue/calendar';
import Dropdown from 'primevue/dropdown';

const { t } = useI18n();

const props = defineProps({
    tables: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['table-selected', 'reservation-made']);

// Inicializar notificaciones
const { showSuccess, showError } = useNotifications();

const tables = ref([]);
const selectedTable = ref(null);
const showReservationModal = ref(false);
const availableTables = ref([]);
const isLoadingTables = ref(false);

const mapWidth = 900;
const mapHeight = 600;

// Detectar modo oscuro
const isDarkMode = ref(false);

// Función para actualizar el estado del modo oscuro
const updateDarkMode = () => {
    isDarkMode.value = document.documentElement.classList.contains('dark');
};

// Observador para cambios en el modo oscuro
const observeDarkMode = () => {
    const observer = new MutationObserver(() => {
        updateDarkMode();
    });
    
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
    
    return observer;
};

// Formulario para crear reserva
const reservationForm = useForm({
    table_id: null,
    reservation_date: null,
    reservation_time: null,
    party_size: 2,
    special_requests: '',
});

// Opciones de horarios
const timeOptions = computed(() => {
    const times = [];
    for (let hour = 12; hour <= 22; hour++) {
        for (let minute = 0; minute < 60; minute += 30) {
            const timeString = `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;
            times.push({
                label: timeString,
                value: timeString
            });
        }
    }
    return times;
});

// Opciones de número de personas
const partySizeOptions = computed(() => {
    const options = [];
    for (let i = 1; i <= 12; i++) {
        options.push({
            label: i === 1 ? `${i} ${t('client.reservations.person')}` : `${i} ${t('client.reservations.people')}`,
            value: i
        });
    }
    return options;
});

// Función para validar y corregir posiciones
const validatePosition = (x, y) => {
    const numX = Number(x);
    const numY = Number(y);
    
    if (isNaN(numX) || isNaN(numY) || numX < 5 || numX > 95 || numY < 5 || numY > 95) {
        console.warn(`Posición inválida detectada: x=${x}, y=${y}. Usando posición central.`);
        return { x: 50, y: 50 };
    }
    
    return { x: numX, y: numY };
};

// Inicializar mesas desde props
const initializeTables = () => {
    tables.value = props.tables.map(table => {
        const validPosition = validatePosition(table.position_x, table.position_y);
        
        return {
            id: table.id,
            x: validPosition.x,
            y: validPosition.y,
            status: table.status,
            capacity: table.capacity,
            table_number: table.table_number,
            available_for_reservation: table.status === 'available'
        };
    });
};

// Inicializar componente
onMounted(async () => {
    initializeTables();
    updateDarkMode();
    observeDarkMode();
});

// Watch para actualizar cuando cambien las props
watch(() => props.tables, () => {
    initializeTables();
}, { deep: true });

// Manejar clic en mesa (solo para reserva)
const handleTableClick = (table, event) => {
    event.preventDefault();
    event.stopPropagation();
    
    if (!table.available_for_reservation) {
        showError(t('client.reservations.table_not_available'));
        return;
    }
    
    if (table.capacity < reservationForm.party_size) {
        showError(t('client.reservations.table_too_small', { 
            capacity: table.capacity, 
            requested: reservationForm.party_size 
        }));
        return;
    }
    
    selectedTable.value = table;
    reservationForm.table_id = table.id;
    showReservationModal.value = true;
};

// Obtener mesas disponibles para fecha/hora específica
const checkAvailability = async () => {
    if (!reservationForm.reservation_date || !reservationForm.reservation_time) {
        return;
    }
    
    // Combinar fecha y hora
    const date = new Date(reservationForm.reservation_date);
    const [hours, minutes] = reservationForm.reservation_time.split(':');
    date.setHours(parseInt(hours), parseInt(minutes), 0, 0);
    
    isLoadingTables.value = true;
    
    try {
        const response = await fetch('/client/reservations/available-tables?' + new URLSearchParams({
            reservation_date: date.toISOString(),
            party_size: reservationForm.party_size
        }));
        
        const data = await response.json();
        
        if (data.success) {
            availableTables.value = data.tables;
            
            // Actualizar estado de disponibilidad de las mesas
            tables.value.forEach(table => {
                const isAvailable = availableTables.value.some(availableTable => 
                    availableTable.id === table.id
                );
                table.available_for_reservation = isAvailable && table.capacity >= reservationForm.party_size;
            });
        }
    } catch (error) {
        console.error('Error checking availability:', error);
        showError(t('client.reservations.error_checking_availability'));
    } finally {
        isLoadingTables.value = false;
    }
};

// Watch para verificar disponibilidad cuando cambie fecha/hora/tamaño del grupo
watch([
    () => reservationForm.reservation_date,
    () => reservationForm.reservation_time,
    () => reservationForm.party_size
], () => {
    checkAvailability();
});

// Cerrar modal
const closeReservationModal = () => {
    showReservationModal.value = false;
    selectedTable.value = null;
    reservationForm.reset();
};

// Enviar reserva
const submitReservation = () => {
    // Combinar fecha y hora
    const date = new Date(reservationForm.reservation_date);
    const [hours, minutes] = reservationForm.reservation_time.split(':');
    date.setHours(parseInt(hours), parseInt(minutes), 0, 0);
    
    const reservationData = {
        table_id: reservationForm.table_id,
        reservation_date: date.toISOString(),
        party_size: reservationForm.party_size,
        special_requests: reservationForm.special_requests,
    };
    
    fetch('/client/reservations', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(reservationData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess(t('client.reservations.reservation_success'));
            emit('reservation-made', data.reservation);
            closeReservationModal();
        } else {
            showError(data.message || t('client.reservations.reservation_error'));
        }
    })
    .catch(error => {
        console.error('Error making reservation:', error);
        showError(t('client.reservations.reservation_error'));
    });
};

// Colores según estado y disponibilidad
const tableColor = (table) => {
    if (!table.available_for_reservation) {
        return '#9CA3AF'; // Gris - No disponible
    }
    
    switch(table.status) {
        case 'available': return '#22C55E'; // Verde
        case 'occupied': return '#EF4444';  // Rojo
        case 'reserved': return '#F59E0B';  // Amarillo/Naranja
        default: return '#9CA3AF';          // Gris
    }
};

// Obtener texto del estado de la mesa
const getTableStatusText = (table) => {
    if (!table.available_for_reservation) {
        if (table.capacity < reservationForm.party_size) {
            return t('client.reservations.too_small');
        }
        return t('client.reservations.not_available');
    }
    
    switch(table.status) {
        case 'available': return t('client.reservations.available');
        case 'occupied': return t('client.reservations.occupied');
        case 'reserved': return t('client.reservations.reserved');
        default: return t('client.reservations.unknown');
    }
};

// Patas de las mesas en SVG
const tableLegs = [
    { x: 40, y: 170, width: 30, height: 30 },
    { x: 130, y: 170, width: 30, height: 30 },
];
</script>

<template>
    <div class="client-table-map">
        <!-- Controles de reserva -->
        <div class="reservation-controls mb-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    <i class="pi pi-calendar mr-2"></i>
                    {{ t('client.reservations.select_date_time') }}
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ t('client.reservations.date') }}
                        </label>
                        <Calendar 
                            v-model="reservationForm.reservation_date"
                            :minDate="new Date()"
                            dateFormat="dd/mm/yy"
                            class="w-full"
                            :placeholder="t('client.reservations.select_date')"
                        />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ t('client.reservations.time') }}
                        </label>
                        <Dropdown 
                            v-model="reservationForm.reservation_time"
                            :options="timeOptions"
                            optionLabel="label"
                            optionValue="value"
                            class="w-full"
                            :placeholder="t('client.reservations.select_time')"
                        />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ t('client.reservations.party_size') }}
                        </label>
                        <Dropdown 
                            v-model="reservationForm.party_size"
                            :options="partySizeOptions"
                            optionLabel="label"
                            optionValue="value"
                            class="w-full"
                        />
                    </div>
                    
                    <div class="flex items-end">
                        <Button 
                            :label="t('client.reservations.check_availability')"
                            icon="pi pi-search"
                            class="w-full"
                            @click="checkAvailability"
                            :loading="isLoadingTables"
                            :disabled="!reservationForm.reservation_date || !reservationForm.reservation_time"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Leyenda -->
        <div class="map-legend mb-4 p-4 bg-white dark:bg-gray-800 rounded-xl shadow-lg">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 text-center">
                <i class="pi pi-info-circle mr-2"></i>
                {{ t('client.reservations.table_status') }}
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="flex items-center justify-center p-3 bg-green-50 dark:bg-green-900/30 rounded-lg">
                    <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
                    <span class="text-green-700 dark:text-green-300 font-medium">{{ t('client.reservations.available') }}</span>
                </div>
                <div class="flex items-center justify-center p-3 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg">
                    <div class="w-4 h-4 bg-yellow-500 rounded-full mr-3"></div>
                    <span class="text-yellow-700 dark:text-yellow-300 font-medium">{{ t('client.reservations.reserved') }}</span>
                </div>
                <div class="flex items-center justify-center p-3 bg-red-50 dark:bg-red-900/30 rounded-lg">
                    <div class="w-4 h-4 bg-red-500 rounded-full mr-3"></div>
                    <span class="text-red-700 dark:text-red-300 font-medium">{{ t('client.reservations.occupied') }}</span>
                </div>
                <div class="flex items-center justify-center p-3 bg-gray-50 dark:bg-gray-900/30 rounded-lg">
                    <div class="w-4 h-4 bg-gray-500 rounded-full mr-3"></div>
                    <span class="text-gray-700 dark:text-gray-300 font-medium">{{ t('client.reservations.not_available') }}</span>
                </div>
            </div>
        </div>

        <!-- Mapa -->
        <div class="map-container">
            <div
                class="restaurant-map position-relative bg-white dark:bg-gray-800 rounded-xl shadow-lg"
                :class="{ 'dark-mode': isDarkMode }"
                :style="{
                    backgroundImage: 'url(/images/restaurant-parquet-bg.svg)',
                    backgroundSize: 'cover',
                    backgroundPosition: 'center',
                    backgroundRepeat: 'no-repeat',
                    width: mapWidth + 'px',
                    height: mapHeight + 'px',
                }"
            >
                <!-- Mesas -->
                <div
                    v-for="table in tables"
                    :key="table.id"
                    class="table-marker position-absolute"
                    :class="{
                        'available': table.available_for_reservation,
                        'selected': selectedTable?.id === table.id
                    }"
                    :style="{
                        left: `calc(${table.x}% - 35px)`,
                        top: `calc(${table.y}% - 35px)`,
                        width: '70px',
                        height: '70px',
                        cursor: table.available_for_reservation ? 'pointer' : 'not-allowed'
                    }"
                    @click="handleTableClick(table, $event)"
                    :title="getTableStatusText(table)"
                >
                    <!-- SVG de la mesa -->
                    <svg
                        viewBox="0 0 200 200"
                        class="table-svg"
                        :class="table.status"
                    >
                        <g>
                            <!-- Tablero de la mesa -->
                            <rect
                                x="25"
                                y="25"
                                width="150"
                                height="150"
                                rx="15"
                                ry="15"
                                :fill="tableColor(table)"
                                stroke="var(--surface-600)"
                                stroke-width="3"
                            />
                            <!-- Patas de la mesa -->
                            <rect
                                v-for="(leg, index) in tableLegs"
                                :key="index"
                                :x="leg.x"
                                :y="leg.y"
                                :width="leg.width"
                                :height="leg.height"
                                rx="8"
                                ry="8"
                                fill="var(--surface-700)"
                            />
                            <!-- Icono de disponibilidad -->
                            <text
                                v-if="table.available_for_reservation"
                                x="100"
                                y="110"
                                text-anchor="middle"
                                class="table-icon"
                                font-size="24"
                                fill="white"
                            >✓</text>
                            <text
                                v-else
                                x="100"
                                y="110"
                                text-anchor="middle"
                                class="table-icon"
                                font-size="24"
                                fill="white"
                            >✗</text>
                        </g>
                    </svg>

                    <!-- Información de la mesa -->
                    <div class="table-info">
                        <div class="table-number">{{ table.table_number || table.id }}</div>
                        <div class="table-capacity">{{ table.capacity }} {{ t('client.reservations.people') }}</div>
                    </div>
                </div>
            </div>

            <!-- Instrucciones -->
            <div class="map-instructions mt-4 text-center">
                <p class="text-gray-600 dark:text-gray-300">
                    <strong>{{ t('client.reservations.instructions') }}:</strong> 
                    {{ t('client.reservations.select_date_first') }}
                </p>
            </div>
        </div>

        <!-- Modal de reserva -->
        <Dialog 
            v-model:visible="showReservationModal" 
            :header="t('client.reservations.make_reservation')" 
            :modal="true" 
            :closable="true" 
            :style="{ width: '500px' }"
            class="reservation-dialog"
        >
            <div v-if="selectedTable" class="space-y-4">
                <!-- Información de la mesa -->
                <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg">
                    <h4 class="font-semibold text-blue-900 dark:text-blue-100">
                        {{ t('client.reservations.selected_table') }}: {{ selectedTable.table_number }}
                    </h4>
                    <p class="text-blue-700 dark:text-blue-300">
                        {{ t('client.reservations.capacity') }}: {{ selectedTable.capacity }} {{ t('client.reservations.people') }}
                    </p>
                </div>

                <!-- Formulario de reserva -->
                <form @submit.prevent="submitReservation" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('client.reservations.date') }}
                            </label>
                            <Calendar 
                                v-model="reservationForm.reservation_date"
                                :minDate="new Date()"
                                dateFormat="dd/mm/yy"
                                class="w-full"
                                required
                            />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('client.reservations.time') }}
                            </label>
                            <Dropdown 
                                v-model="reservationForm.reservation_time"
                                :options="timeOptions"
                                optionLabel="label"
                                optionValue="value"
                                class="w-full"
                                required
                            />
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ t('client.reservations.party_size') }}
                        </label>
                        <Dropdown 
                            v-model="reservationForm.party_size"
                            :options="partySizeOptions"
                            optionLabel="label"
                            optionValue="value"
                            class="w-full"
                            required
                        />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ t('client.reservations.special_requests') }} ({{ t('common.optional') }})
                        </label>
                        <InputText 
                            v-model="reservationForm.special_requests"
                            class="w-full"
                            :placeholder="t('client.reservations.special_requests_placeholder')"
                        />
                    </div>
                    
                    <div class="flex justify-end gap-3 pt-4">
                        <Button 
                            :label="t('common.cancel')" 
                            icon="pi pi-times" 
                            class="p-button-text" 
                            @click="closeReservationModal" 
                            type="button" 
                        />
                        <Button 
                            :label="t('client.reservations.confirm_reservation')" 
                            icon="pi pi-check" 
                            type="submit"
                            :loading="reservationForm.processing"
                        />
                    </div>
                </form>
            </div>
        </Dialog>
    </div>
</template>

<style scoped>
.client-table-map {
    width: 100%;
}

.map-container {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.restaurant-map {
    cursor: default;
    transition: all 0.3s ease;
    background-blend-mode: normal;
    position: relative;
    overflow: hidden;
}

.restaurant-map::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: transparent;
    transition: background-color 0.3s ease;
    pointer-events: none;
    z-index: 1;
}

.restaurant-map.dark-mode::before {
    background: rgba(0, 0, 0, 0.3);
}

.table-marker {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    user-select: none;
    z-index: 10;
    position: absolute !important;
}

.table-marker.available:hover {
    transform: scale(1.05);
    z-index: 15 !important;
    filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.2));
}

.table-marker.selected {
    transform: scale(1.1);
    z-index: 20 !important;
    filter: drop-shadow(0 6px 16px rgba(59, 130, 246, 0.4));
}

.table-svg {
    width: 100%;
    height: 100%;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.1));
}

.table-marker:hover:not(.available) {
    opacity: 0.7;
}

.table-info {
    position: absolute;
    bottom: -30px;
    left: 50%;
    transform: translateX(-50%);
    text-align: center;
    color: var(--text-color);
    font-weight: 600;
    white-space: nowrap;
    font-size: 0.75rem;
    background: rgba(255, 255, 255, 0.95);
    padding: 3px 8px;
    border-radius: 6px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(5px);
}

.table-number {
    font-size: 0.8rem;
    margin-bottom: 1px;
}

.table-capacity {
    font-size: 0.7rem;
    color: var(--text-color-secondary);
}

.table-icon {
    font-weight: bold;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
}

.reservation-controls {
    max-width: 100%;
}

.map-legend {
    max-width: 100%;
}

.map-instructions {
    max-width: 600px;
}

/* Responsive */
@media (max-width: 768px) {
    .restaurant-map {
        width: 100% !important;
        max-width: 400px;
        height: 300px !important;
    }
    
    .table-marker {
        width: 50px !important;
        height: 50px !important;
    }
    
    .table-info {
        bottom: -25px;
        font-size: 0.7rem;
        padding: 2px 6px;
    }
    
    .reservation-controls .grid {
        grid-template-columns: 1fr;
    }
}
</style>
