<script setup>
import { ref, onMounted, computed, nextTick, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';

// Textos estáticos para evitar problemas con i18n
const texts = {
    client: {
        reservations: {
            select_date_time: 'Selecciona fecha y hora',
            table_status: 'Estado de las Mesas',
            available: 'Disponible',
            reserved: 'Reservada',
            occupied: 'Ocupada',
            not_available: 'No Disponible',
            too_small: 'Muy pequeña',
            instructions: 'Instrucciones',
            select_date_first: 'Selecciona fecha y hora, luego haz clic en una mesa disponible para reservar.',
            make_reservation: 'Hacer Reserva',
            table_not_available: 'Esta mesa no está disponible para reserva',
            table_too_small: 'Esta mesa es muy pequeña para el número de personas seleccionado',
            error_checking_availability: 'Error al verificar disponibilidad',
            reservation_success: '¡Reserva realizada con éxito!',
            reservation_error: 'Error al realizar la reserva',
            person: 'persona',
            people: 'personas'
        }
    }
};

const props = defineProps({
    tables: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['table-selected', 'reservation-made']);

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

// Formulario para crear reserva usando Inertia
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
            label: i === 1 ? `${i} ${texts.client.reservations.person}` : `${i} ${texts.client.reservations.people}`,
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
        alert(texts.client.reservations.table_not_available);
        return;
    }
    
    if (table.capacity < reservationForm.party_size) {
        alert(`${texts.client.reservations.table_too_small}. Capacidad: ${table.capacity}, Solicitado: ${reservationForm.party_size}`);
        return;
    }
    
    selectedTable.value = table;
    reservationForm.table_id = table.id;
    showReservationModal.value = true;
};

// Obtener mesas disponibles para fecha/hora específica usando Inertia
const checkAvailability = () => {
    if (!reservationForm.reservation_date || !reservationForm.reservation_time) {
        return;
    }
    
    // Combinar fecha y hora
    const date = new Date(reservationForm.reservation_date);
    const [hours, minutes] = reservationForm.reservation_time.split(':');
    date.setHours(parseInt(hours), parseInt(minutes), 0, 0);
    
    isLoadingTables.value = true;
    
    // Usar Inertia para verificar disponibilidad
    router.get('/client/reservations/available-tables', {
        reservation_date: date.toISOString(),
        party_size: reservationForm.party_size
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['availableTables'],
        onSuccess: (page) => {
            if (page.props.availableTables) {
                availableTables.value = page.props.availableTables;
                
                // Actualizar estado de disponibilidad de las mesas
                tables.value.forEach(table => {
                    const isAvailable = availableTables.value.some(availableTable => 
                        availableTable.id === table.id
                    );
                    table.available_for_reservation = isAvailable && table.capacity >= reservationForm.party_size;
                });
            }
        },
        onError: () => {
            console.error('Error checking availability');
            alert(texts.client.reservations.error_checking_availability);
        },
        onFinish: () => {
            isLoadingTables.value = false;
        }
    });
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

// Enviar reserva usando Inertia
const submitReservation = () => {
    // Combinar fecha y hora
    const date = new Date(reservationForm.reservation_date);
    const [hours, minutes] = reservationForm.reservation_time.split(':');
    date.setHours(parseInt(hours), parseInt(minutes), 0, 0);
    
    reservationForm.transform((data) => ({
        ...data,
        reservation_date: date.toISOString(),
    })).post('/client/reservations', {
        onSuccess: () => {
            alert(texts.client.reservations.reservation_success);
            emit('reservation-made');
            closeReservationModal();
        },
        onError: (errors) => {
            const errorMessage = errors.message || texts.client.reservations.reservation_error;
            alert(errorMessage);
        }
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
            return texts.client.reservations.too_small;
        }
        return texts.client.reservations.not_available;
    }
    
    switch(table.status) {
        case 'available': return texts.client.reservations.available;
        case 'occupied': return texts.client.reservations.occupied;
        case 'reserved': return texts.client.reservations.reserved;
        default: return 'Estado desconocido';
    }
};

// Formatear fecha para mostrar
const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES');
};
</script>

<template>
    <div class="client-table-map">
        <!-- Controles de reserva -->
        <div class="reservation-controls mb-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    📅 {{ texts.client.reservations.select_date_time }}
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Fecha
                        </label>
                        <input
                            type="date"
                            v-model="reservationForm.reservation_date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Hora
                        </label>
                        <select
                            v-model="reservationForm.reservation_time"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">Seleccionar hora</option>
                            <option v-for="time in timeOptions" :key="time.value" :value="time.value">
                                {{ time.label }}
                            </option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Personas
                        </label>
                        <select
                            v-model="reservationForm.party_size"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option v-for="option in partySizeOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                    </div>
                    
                    <div class="flex items-end">
                        <button
                            @click="checkAvailability"
                            :disabled="isLoadingTables"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg disabled:opacity-50"
                        >
                            {{ isLoadingTables ? 'Verificando...' : 'Verificar Disponibilidad' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leyenda -->
        <div class="map-legend mb-4 p-4 bg-white dark:bg-gray-800 rounded-xl shadow-lg">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 text-center">
                ℹ️ {{ texts.client.reservations.table_status }}
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="flex items-center justify-center p-3 bg-green-50 dark:bg-green-900/30 rounded-lg">
                    <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
                    <span class="text-green-700 dark:text-green-300 font-medium">{{ texts.client.reservations.available }}</span>
                </div>
                <div class="flex items-center justify-center p-3 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg">
                    <div class="w-4 h-4 bg-yellow-500 rounded-full mr-3"></div>
                    <span class="text-yellow-700 dark:text-yellow-300 font-medium">{{ texts.client.reservations.reserved }}</span>
                </div>
                <div class="flex items-center justify-center p-3 bg-red-50 dark:bg-red-900/30 rounded-lg">
                    <div class="w-4 h-4 bg-red-500 rounded-full mr-3"></div>
                    <span class="text-red-700 dark:text-red-300 font-medium">{{ texts.client.reservations.occupied }}</span>
                </div>
                <div class="flex items-center justify-center p-3 bg-gray-50 dark:bg-gray-900/30 rounded-lg">
                    <div class="w-4 h-4 bg-gray-500 rounded-full mr-3"></div>
                    <span class="text-gray-700 dark:text-gray-300 font-medium">{{ texts.client.reservations.not_available }}</span>
                </div>
            </div>
        </div>

        <!-- Mapa -->
        <div class="map-container">
            <div
                class="restaurant-map relative bg-white dark:bg-gray-800 rounded-xl shadow-lg"
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
                    class="table-marker absolute transition-all duration-200 user-select-none"
                    :class="{
                        'selected': selectedTable?.id === table.id,
                        'cursor-pointer': table.available_for_reservation,
                        'cursor-not-allowed': !table.available_for_reservation
                    }"
                    :style="{
                        left: `calc(${table.x}% - 30px)`,
                        top: `calc(${table.y}% - 30px)`,
                        width: '60px',
                        height: '60px',
                        zIndex: selectedTable?.id === table.id ? 20 : 10
                    }"
                    @click="handleTableClick(table, $event)"
                    :title="getTableStatusText(table)"
                >
                    <!-- SVG de la mesa -->
                    <svg
                        viewBox="0 0 200 200"
                        class="w-full h-full transition-transform duration-200"
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
                                stroke="#333"
                                stroke-width="4"
                            />
                            <!-- Número de mesa -->
                            <text
                                x="100"
                                y="110"
                                text-anchor="middle"
                                dominant-baseline="middle"
                                fill="#fff"
                                font-size="40"
                                font-weight="bold"
                            >
                                {{ table.table_number || table.id }}
                            </text>
                        </g>
                    </svg>

                    <!-- Información de la mesa -->
                    <div class="table-info absolute -bottom-8 left-1/2 transform -translate-x-1/2 text-center">
                        <div class="text-xs bg-white dark:bg-gray-800 px-2 py-1 rounded shadow">
                            <div class="font-semibold text-gray-900 dark:text-gray-100">{{ table.capacity }} personas</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instrucciones -->
            <div class="map-instructions mt-4 text-center">
                <p class="text-gray-600 dark:text-gray-300">
                    <strong>{{ texts.client.reservations.instructions }}:</strong> 
                    {{ texts.client.reservations.select_date_first }}
                </p>
            </div>
        </div>

        <!-- Modal de reserva -->
        <div v-if="showReservationModal" 
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-xl max-w-md w-full mx-4 shadow-2xl">
                <div class="bg-blue-600 text-white p-6 rounded-t-xl">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold">
                            {{ texts.client.reservations.make_reservation }}
                        </h3>
                        <button @click="closeReservationModal"
                                class="text-white hover:text-gray-200 text-2xl">
                            ✕
                        </button>
                    </div>
                </div>
                
                <div v-if="selectedTable" class="p-6">
                    <!-- Información de la mesa -->
                    <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg mb-4">
                        <div class="text-blue-800 dark:text-blue-200">
                            <div class="font-semibold">Mesa {{ selectedTable.table_number || selectedTable.id }}</div>
                            <div class="text-sm">Capacidad: {{ selectedTable.capacity }} personas</div>
                            <div class="text-sm">Fecha: {{ formatDate(reservationForm.reservation_date) }}</div>
                            <div class="text-sm">Hora: {{ reservationForm.reservation_time }}</div>
                            <div class="text-sm">Personas: {{ reservationForm.party_size }}</div>
                        </div>
                    </div>

                    <!-- Formulario de reserva -->
                    <form @submit.prevent="submitReservation" class="space-y-4">
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">
                                Comentarios especiales (opcional)
                            </label>
                            <textarea
                                v-model="reservationForm.special_requests"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:outline-none"
                                placeholder="Alguna solicitud especial..."
                            ></textarea>
                        </div>
                        
                        <button
                            type="submit"
                            :disabled="reservationForm.processing"
                            class="w-full bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-medium py-3 px-6 rounded-lg transition-all duration-200"
                        >
                            {{ reservationForm.processing ? 'Reservando...' : '✅ Confirmar Reserva' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
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
}

.table-marker:hover {
    transform: scale(1.05);
    z-index: 15 !important;
    filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.2));
}

.table-marker.selected {
    transform: scale(1.1);
    z-index: 20 !important;
    filter: drop-shadow(0 6px 16px rgba(59, 130, 246, 0.4));
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
    
    .reservation-controls .grid {
        grid-template-columns: 1fr;
    }
}
</style>
