<script setup>
import { ref, onMounted, computed, nextTick, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { useNotifications } from '@/composables/useNotifications';
import interact from 'interact.js';

// Inicializar notificaciones
const { showWarning, showError, showSuccess } = useNotifications();

// Textos estáticos por ahora
const texts = {
    reservations: {
        people: 'personas',
        reserve_table: 'Reservar Mesa',
        date_time: 'Fecha y Hora',
        discount_code: 'Código de Descuento',
        applied: 'aplicado',
        discount: 'Descuento',
        select_date_time: 'Selecciona fecha y hora',
        reservation_success: '¡Reserva realizada con éxito!',
        redirecting: 'Redirigiendo...',
        confirm_reservation: 'Confirmar Reserva',
        reservation_error: 'Error al realizar la reserva'
    }
};

const props = defineProps({
    tables: {
        type: Array,
        default: () => []
    },
    showReservationControls: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['tables-updated', 'reservation-made']);

const tables = ref([]);
const selectedTable = ref(null);
const reservationDateTime = ref(null);
const reservationSuccess = ref(false);
const reservationError = ref('');
const appliedCoupon = ref(null);
const showReservationModal = ref(false);
const isEmployee = computed(() => usePage().props.auth?.user?.role === 'employee');

const mapWidth = 800;
const mapHeight = 500;

// Escuchar mensajes flash para mostrar notificaciones
const flashMessages = computed(() => usePage().props.flash || {});

// Watch para mostrar notificaciones automáticamente
watch(flashMessages, (newMessages) => {
    if (newMessages.success) {
        showSuccess(newMessages.success, 'Éxito');
    }
    if (newMessages.error) {
        showError(newMessages.error, 'Error');
    }
    if (newMessages.warning) {
        showWarning(newMessages.warning, 'Atención');
    }
    if (newMessages.login_required) {
        showWarning(
            'Para hacer una reserva necesitas iniciar sesión o registrarte.',
            'Inicio de sesión requerido'
        );
    }
}, { deep: true });

// Inicializar mesas desde props
const initializeTables = () => {
    tables.value = props.tables.map(table => ({
        id: table.id,
        x: table.position_x || 50,
        y: table.position_y || 50,
        status: table.status,
        capacity: table.capacity,
        table_number: table.table_number
    }));
};

// Watch para actualizar cuando cambien las props
watch(() => props.tables, () => {
    initializeTables();
}, { deep: true });

// Watch para mensajes flash (notificaciones automáticas)
watch(() => usePage().props.flash, (flash) => {
    if (flash?.warning) {
        showWarning(flash.warning, flash.title || 'Aviso');
    }
    if (flash?.error) {
        showError(flash.error, flash.title || 'Error');
    }
    if (flash?.success) {
        showSuccess(flash.success, flash.title || 'Éxito');
    }
    if (flash?.message) {
        showWarning(flash.message, flash.title || 'Información');
    }
}, { deep: true });

onMounted(async () => {
    initializeTables();
    await nextTick();
    
    if (isEmployee.value) {
        interact('.table-marker.draggable').draggable({
            modifiers: [
                interact.modifiers.restrictRect({
                    restriction: 'parent',
                    endOnly: true
                })
            ],
            listeners: {
                move(event) {
                    const target = event.target;
                    const x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
                    const y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;
                    target.style.transform = `translate(${x}px, ${y}px)`;
                    target.setAttribute('data-x', x);
                    target.setAttribute('data-y', y);
                },
                end(event) {
                    const target = event.target;
                    const container = target.parentElement;
                    const rect = container.getBoundingClientRect();
                    const markerRect = target.getBoundingClientRect();
                    
                    // Calcula el centro del marcador
                    const centerX = markerRect.left - rect.left + markerRect.width / 2;
                    const centerY = markerRect.top - rect.top + markerRect.height / 2;
                    const newX = Math.round((centerX / rect.width) * 100);
                    const newY = Math.round((centerY / rect.height) * 100);
                    
                    // Actualizar en la base de datos con Inertia
                    router.put(`/admin/tables/${target.dataset.id}`, {
                        position_x: newX,
                        position_y: newY
                    }, {
                        preserveState: true,
                        preserveScroll: true,
                        onSuccess: () => {
                            emit('tables-updated');
                            // Actualizar la posición local
                            const tableIndex = tables.value.findIndex(t => t.id == target.dataset.id);
                            if (tableIndex !== -1) {
                                tables.value[tableIndex].x = newX;
                                tables.value[tableIndex].y = newY;
                            }
                        },
                        onError: () => {
                            console.error('Error updating table position');
                        }
                    });
                    
                    target.style.transform = '';
                    target.setAttribute('data-x', 0);
                    target.setAttribute('data-y', 0);
                }
            }
        });
    }
});

// Selección de mesa (solo si está disponible)
const selectTable = (table) => {
    if (!props.showReservationControls) return;
    
    // Verificar si el usuario está autenticado
    const user = usePage().props.auth?.user;
    
    if (!user) {
        // Usuario no autenticado - mostrar notificación y redirigir
        showWarning(
            'Para hacer una reserva necesitas iniciar sesión o registrarte.',
            'Inicio de sesión requerido'
        );
        
        // Redirigir al login después de un breve delay
        setTimeout(() => {
            router.visit('/login');
        }, 2000);
        return;
    }
    
    if (table.status === 'available') {
        selectedTable.value = table;
        reservationDateTime.value = null;
        reservationSuccess.value = false;
        reservationError.value = '';
        appliedCoupon.value = null;
        showReservationModal.value = true;
    }
};

const closeReservationModal = () => {
    showReservationModal.value = false;
    selectedTable.value = null;
};

const removeCoupon = () => {
    appliedCoupon.value = null;
};

// Confirmar reserva
const confirmReservation = () => {
    reservationError.value = '';
    reservationSuccess.value = false;
    
    if (!reservationDateTime.value) {
        reservationError.value = texts.reservations.select_date_time;
        showError(texts.reservations.select_date_time, 'Datos incompletos');
        return;
    }
    
    const reservationData = {
        table_id: selectedTable.value.id,
        reservation_date: reservationDateTime.value,
        party_size: selectedTable.value.capacity,
        discount_coupon_id: appliedCoupon.value?.id || null
    };
    
    router.post('/public/reservations', reservationData, {
        preserveState: true,
        onSuccess: () => {
            reservationSuccess.value = true;
            showSuccess(
                'Tu reserva ha sido confirmada exitosamente. ¡Te esperamos!',
                'Reserva confirmada'
            );
            emit('reservation-made');
            
            // Cerrar modal después de 3 segundos
            setTimeout(() => {
                closeReservationModal();
            }, 3000);
        },        onError: (errors) => {
            const errorMessage = errors.message || texts.reservations.reservation_error;
            reservationError.value = errorMessage;
            showError(errorMessage, 'Error en la reserva');
        }
    });
};

// Colores según estado
const tableColor = (status) => {
    switch(status) {
        case 'available': return '#4CAF50';
        case 'occupied': return '#f44336';
        case 'reserved': return '#ffeb3b';
        default: return '#ccc';
    }
};

// Patas de las mesas en SVG
const tableLegs = [
  { x: 40, y: 170, width: 30, height: 30 },
  { x: 130, y: 170, width: 30, height: 30 },
];
</script>

<template>
    <div class="w-full">
        <!-- Leyenda de estados de las mesas -->
        <div class="mb-4 p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                <i class="pi pi-info-circle mr-2 text-blue-500"></i>
                Estado de las mesas
            </h3>            <div class="flex flex-wrap gap-4 text-sm">
                <div class="flex items-center">
                    <div class="w-4 h-4 rounded mr-2 border border-gray-300 bg-green-500"></div>
                    <span class="text-gray-700 dark:text-gray-300">Disponible</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 rounded mr-2 border border-gray-300 bg-yellow-400"></div>
                    <span class="text-gray-700 dark:text-gray-300">Reservada</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 rounded mr-2 border border-gray-300 bg-red-500"></div>
                    <span class="text-gray-700 dark:text-gray-300">Ocupada</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 rounded mr-2 border border-gray-300 bg-gray-400"></div>
                    <span class="text-gray-700 dark:text-gray-300">No disponible</span>
                </div>
            </div>
        </div>

        <div class="flex justify-center">
            <div
                class="relative bg-cover bg-center rounded-2xl shadow-lg"
                :style="{
                    backgroundImage: 'url(/images/restaurant-parquet-bg.svg)',
                    backgroundSize: 'cover',
                    backgroundPosition: 'center',
                    backgroundRepeat: 'no-repeat',
                    width: mapWidth + 'px',
                    height: mapHeight + 'px'
                }"
            >
                <!-- Mesas con SVG -->
                <div
                    v-for="table in tables"
                    :key="table.id"
                    class="table-marker absolute cursor-pointer transition-all duration-300 user-select-none"
                    :class="[table.status, { 'draggable border-2 border-dashed border-blue-500': isEmployee }]"
                    :data-id="table.id"
                    :style="{
                        left: `calc(${table.x}% - 35px)`,
                        top: `calc(${table.y}% - 35px)`,
                        width: '70px',
                        height: '70px',
                        zIndex: selectedTable?.id === table.id ? 20 : 1
                    }"
                    @click="() => selectTable(table)"
                >
                    <!-- SVG de la mesa -->
                    <svg
                        viewBox="0 0 200 200"
                        class="table-svg w-full h-full transition-transform duration-300"
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
                                :fill="tableColor(table.status)"
                                stroke="#333"
                                stroke-width="4"
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
                                fill="#5a4e4e"
                            />
                        </g>
                    </svg>
                    
                    <!-- Información de la mesa -->
                    <div class="table-info absolute -bottom-9 left-1/2 transform -translate-x-1/2 text-center text-gray-900 dark:text-gray-100 font-semibold text-sm whitespace-nowrap">
                        <div class="table-number">Mesa {{ table.table_number || table.id }}</div>
                        <div class="table-capacity text-xs text-gray-600 dark:text-gray-300">{{ table.capacity }} {{ texts.reservations.people }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Reserva -->
        <div v-if="showReservationModal" 
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-xl max-w-md w-full mx-4 shadow-2xl">
                <div class="bg-blue-600 text-white p-6 rounded-t-xl">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold">
                            📅 {{ texts.reservations.reserve_table }} {{ selectedTable?.table_number || selectedTable?.id }}
                        </h3>
                        <button @click="closeReservationModal" 
                                class="text-white hover:text-gray-200 text-2xl">
                            ✕
                        </button>
                    </div>
                </div>
                
                <div class="p-6">
                    <form @submit.prevent="confirmReservation" class="space-y-4">
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">
                                📅 {{ texts.reservations.date_time }}
                            </label>
                            <input
                                type="datetime-local"
                                v-model="reservationDateTime"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 dark:focus:border-blue-400 focus:outline-none"
                                required
                            />
                        </div>
                        
                        <!-- Información de la mesa -->
                        <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <div class="text-blue-800 dark:text-blue-200">
                                <div class="font-semibold">Mesa {{ selectedTable?.table_number || selectedTable?.id }}</div>
                                <div class="text-sm">Capacidad: {{ selectedTable?.capacity }} personas</div>
                            </div>
                        </div>
                        
                        <!-- Cupón aplicado (simplificado) -->
                        <div v-if="appliedCoupon" class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg p-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <div class="text-green-800 dark:text-green-200 font-medium">
                                        ✅ {{ appliedCoupon.code }} {{ texts.reservations.applied }}
                                    </div>
                                    <div class="text-sm text-green-600 dark:text-green-400">
                                        {{ texts.reservations.discount }}: 
                                        <span v-if="appliedCoupon.discount_type === 'percentage'">
                                            {{ appliedCoupon.value }}%
                                        </span>
                                        <span v-else>
                                            €{{ appliedCoupon.value }}
                                        </span>
                                    </div>
                                </div>
                                <button 
                                    type="button" 
                                    @click="removeCoupon"
                                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                >
                                    ✕
                                </button>
                            </div>
                        </div>
                        
                        <div v-if="reservationError" class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 rounded-lg p-3">
                            ⚠️ {{ reservationError }}
                        </div>
                        
                        <div v-if="reservationSuccess" class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 rounded-lg p-3">
                            ✅ {{ texts.reservations.reservation_success }}
                            <br>
                            <small class="text-green-600 dark:text-green-400">{{ texts.reservations.redirecting }}</small>
                        </div>
                        
                        <button
                            v-if="!reservationSuccess"
                            type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-all duration-200"
                        >
                            ✅ {{ texts.reservations.confirm_reservation }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.table-marker:hover .table-svg {
    transform: scale(1.05) rotate(-2deg);
    filter: drop-shadow(0 8px 20px rgba(0, 0, 0, 0.2));
}

.table-svg.available {
    filter: drop-shadow(0 4px 6px rgba(0, 128, 0, 0.3));
}

.table-svg.reserved {
    filter: drop-shadow(0 4px 6px rgba(255, 235, 59, 0.5));
}

.table-svg.occupied {
    filter: drop-shadow(0 4px 6px rgba(244, 67, 54, 0.5));
}

.draggable {
    cursor: grab;
}

.draggable:active {
    cursor: grabbing;
}

.user-select-none {
    user-select: none;
}

/* Estilos para la leyenda */
.legend-color {
    width: 16px !important;
    height: 16px !important;
    border-radius: 50% !important;
    display: inline-block !important;
    margin-right: 8px !important;
    border: 1px solid rgba(0, 0, 0, 0.1) !important;
}

/* Estilos adicionales para el mapa */
.restaurant-map {
    background-color: #f8f9fa;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
}

.table-marker {
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    user-select: none;
    z-index: 10;
}

.table-marker:hover {
    transform: scale(1.05);
    z-index: 20;
}

/* Información de las mesas */
.table-info {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(0, 0, 0, 0.1);
    border-radius: 4px;
    padding: 2px 4px;
    backdrop-filter: blur(5px);
}

/* Modo oscuro */
:global(.dark) .table-info {
    background: rgba(30, 30, 30, 0.9);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: white;
}
</style>
