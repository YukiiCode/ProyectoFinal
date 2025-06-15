<script setup>
import { ref, onMounted, computed, nextTick, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { useNotifications } from '@/composables/useNotifications';
import { useI18n } from 'vue-i18n';
import interact from 'interact.js';

// Inicializar notificaciones e i18n
const { showWarning, showError, showSuccess } = useNotifications();
const { t } = useI18n();

// Función auxiliar para traducciones con fallback
const translate = (key, fallback = '') => {
    try {
        const translation = t(key);
        return translation !== key ? translation : fallback;
    } catch (error) {
        console.warn(`Translation key "${key}" not found, using fallback:`, fallback);
        return fallback;
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
const reservationForm = ref({
    table_id: null,
    reservation_date: '',
    reservation_time: '',
    party_size: 1,
    duration_hours: 1,
    special_requests: '',
    discount_coupon_id: null
});
const availableCoupons = ref([]);
const selectedCoupon = ref(null);
const availableHours = ref([]);
const loadingHours = ref(false);
const showReservationModal = ref(false);
const reservationSuccess = ref(false);
const reservationError = ref('');
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

onMounted(async () => {
    initializeTables();
    await nextTick();
    
    // Cargar horas disponibles si ya hay fecha y mesa seleccionadas
    if (reservationForm.value.reservation_date && selectedTable.value) {
        await loadAvailableHours();
    }
    
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
    
    // Refrescar el estado de las mesas cada 30 segundos
    if (props.showReservationControls) {
        const refreshInterval = setInterval(() => {
            refreshTableStatus();
        }, 30000); // 30 segundos
        
        // Limpiar el intervalo cuando el componente se desmonte
        const cleanup = () => clearInterval(refreshInterval);
        if (typeof window !== 'undefined') {
            window.addEventListener('beforeunload', cleanup);
        }
    }
});

// Selección de mesa (solo si está disponible)
const selectTable = async (table) => {
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
        resetForm();
        reservationForm.value.table_id = table.id;
        reservationForm.value.party_size = table.capacity;
        
        // Cargar cupones disponibles
        await loadAvailableCoupons();
        
        // Si ya hay una fecha seleccionada, cargar las horas disponibles
        if (reservationForm.value.reservation_date) {
            await loadAvailableHours();
        }
        
        showReservationModal.value = true;
    }
};

const closeReservationModal = () => {
    showReservationModal.value = false;
    selectedTable.value = null;
    resetForm();
};

// Calcular fecha mínima (hoy)
const minDate = computed(() => {
    return new Date().toISOString().split('T')[0];
});

// Calcular hora mínima según fecha seleccionada
const minTime = computed(() => {
    const today = new Date().toISOString().split('T')[0];
    if (reservationForm.value.reservation_date === today) {
        const now = new Date();
        now.setHours(now.getHours() + 1);
        return now.toTimeString().slice(0, 5); // formato hh:mm
    }
    return "09:00"; // Hora de apertura por defecto
});

// Función para cargar cupones disponibles
const loadAvailableCoupons = async () => {
    try {
        const response = await fetch('/public/coupons/available');
        const data = await response.json();
        availableCoupons.value = data.coupons || [];
    } catch (error) {
        console.log('No se pudieron cargar los cupones');
        availableCoupons.value = [];
    }
};

// Función para cargar horas disponibles
const loadAvailableHours = async () => {
    if (!reservationForm.value.reservation_date || !selectedTable.value) {
        availableHours.value = [];
        return;
    }

    loadingHours.value = true;
    console.log('Loading hours for:', reservationForm.value.reservation_date, 'table:', selectedTable.value.id);
    
    try {
        const url = `/public/reservations/available-hours?date=${reservationForm.value.reservation_date}&table_id=${selectedTable.value.id}`;
        console.log('Fetching from URL:', url);
        
        const response = await fetch(url);
        const data = await response.json();
        
        console.log('Response data:', data);
        
        // Formatear las horas para mostrar mejor
        availableHours.value = (data.hours || []).map(hour => ({
            ...hour,
            display: hour.display || hour.time // Usar display del backend o time como fallback
        }));
          console.log('Available hours set:', availableHours.value);
    } catch (error) {
        console.error('Error loading available hours:', error);
        availableHours.value = [];
        showError('Error cargando horarios disponibles', 'Error');
    } finally {
        loadingHours.value = false;
    }
};

// Watch para cargar horas cuando cambie la fecha
watch(() => reservationForm.value.reservation_date, () => {
    reservationForm.value.reservation_time = '';
    loadAvailableHours();
});

// Watch para cargar horas cuando cambie la mesa seleccionada
watch(() => selectedTable.value, (newTable) => {
    if (newTable && reservationForm.value.reservation_date) {
        reservationForm.value.reservation_time = '';
        loadAvailableHours();
    }
});

// Función para seleccionar una hora
const selectHour = (hour) => {
    if (hour.available) {
        reservationForm.value.reservation_time = hour.time;
    } else {
        // Mostrar mensaje explicativo si la hora no está disponible
        let message = translate('reservations.hour_not_available', 'Esta hora no está disponible');
        if (hour.reason === 'past_time') {
            message = translate('reservations.hour_past_time', 'No se pueden hacer reservas en horarios pasados');
        } else if (hour.reason === 'reserved') {
            message = translate('reservations.hour_reserved', 'Esta hora ya tiene una reserva');
        }
        showWarning(message, translate('reservations.hour_not_available', 'Hora no disponible'));
    }
};

// Aplicar cupón
const applyCoupon = (coupon) => {
    selectedCoupon.value = coupon;
    reservationForm.value.discount_coupon_id = coupon.id;
    showSuccess(`Cupón ${coupon.code} aplicado`, 'Descuento aplicado');
};

// Remover cupón
const removeCoupon = () => {
    selectedCoupon.value = null;
    reservationForm.value.discount_coupon_id = null;
    showSuccess('Cupón removido', 'Descuento eliminado');
};

// Resetear formulario
const resetForm = () => {
    reservationForm.value = {
        table_id: null,
        reservation_date: '',
        reservation_time: '',
        party_size: 1,
        duration_hours: 1,
        special_requests: '',
        discount_coupon_id: null
    };
    selectedCoupon.value = null;
    reservationSuccess.value = false;
    reservationError.value = '';
};

// Confirmar reserva
const confirmReservation = () => {
    reservationError.value = '';
    reservationSuccess.value = false;
    
    // Validar campos requeridos
    if (!reservationForm.value.reservation_date || !reservationForm.value.reservation_time) {
        reservationError.value = 'Por favor selecciona una fecha y hora para la reserva';
        showError('Por favor selecciona una fecha y hora para la reserva', 'Datos incompletos');
        return;
    }
    
    // Construir datos de la reserva
    const reservationData = {
        table_id: reservationForm.value.table_id,
        reservation_date: reservationForm.value.reservation_date,
        reservation_time: reservationForm.value.reservation_time,
        party_size: reservationForm.value.party_size,
        duration_hours: reservationForm.value.duration_hours,
        special_requests: reservationForm.value.special_requests,
        discount_coupon_id: reservationForm.value.discount_coupon_id
    };
    
    router.post('/public/reservations', reservationData, {
        preserveState: true,        onSuccess: () => {
            reservationSuccess.value = true;
            showSuccess(
                'Tu reserva ha sido confirmada exitosamente. ¡Te esperamos!',
                'Reserva confirmada'
            );
            
            // Actualizar el estado de la mesa a 'reserved'
            const tableIndex = tables.value.findIndex(t => t.id === selectedTable.value.id);
            if (tableIndex !== -1) {
                tables.value[tableIndex].status = 'reserved';
            }
              // Actualizar inmediatamente la hora reservada en la lista local
            const reservedTime = reservationForm.value.reservation_time;
            if (reservedTime) {
                const hourIndex = availableHours.value.findIndex(h => h.time === reservedTime);
                if (hourIndex !== -1) {
                    availableHours.value[hourIndex].available = false;
                    availableHours.value[hourIndex].reason = 'reserved';
                }
            }
            
            // Recargar las horas disponibles para reflejar la nueva reserva
            if (reservationForm.value.reservation_date) {
                setTimeout(() => {
                    loadAvailableHours();
                }, 1000); // Esperar 1 segundo antes de recargar
            }
            
            emit('reservation-made');
            
            // Cerrar modal después de 3 segundos
            setTimeout(() => {
                closeReservationModal();
            }, 3000);
        },
        onError: (errors) => {
            console.log('Errores recibidos:', errors);
            
            // Manejar diferentes tipos de errores
            let errorMessage = 'Error al realizar la reserva';
            
            if (errors.message) {
                errorMessage = errors.message;
            } else if (errors.table_id) {
                errorMessage = 'Mesa no válida: ' + errors.table_id[0];
            } else if (errors.reservation_date) {
                errorMessage = 'Fecha no válida: ' + errors.reservation_date[0];
            } else if (errors.reservation_time) {
                errorMessage = 'Hora no válida: ' + errors.reservation_time[0];
            } else if (errors.party_size) {
                errorMessage = 'Número de personas no válido: ' + errors.party_size[0];
            } else if (typeof errors === 'string') {
                errorMessage = errors;
            }
            
            reservationError.value = errorMessage;
            showError(errorMessage, 'Error en la reserva');
        }
    });
};

// Función para refrescar el estado de las mesas
const refreshTableStatus = async () => {
    try {
        const response = await fetch('/public/tables');
        const data = await response.json();
        if (data.tables) {
            // Actualizar solo el estado, manteniendo las posiciones actuales
            data.tables.forEach(updatedTable => {
                const tableIndex = tables.value.findIndex(t => t.id === updatedTable.id);
                if (tableIndex !== -1) {
                    tables.value[tableIndex].status = updatedTable.status;
                }
            });
        }
    } catch (error) {
        console.error('Error refreshing table status:', error);
    }
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
        <!-- Leyenda de estados de las mesas -->        <div class="mb-4 p-4 bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-600">            <h3 style="font-size: 14px; font-weight: 600; color: #000; margin-bottom: 12px; display: flex; align-items: center;">
                <svg style="width: 16px; height: 16px; margin-right: 8px; color: #3b82f6;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                {{ translate('reservations.table_status', 'Estado de las mesas') }}
            </h3><div style="display: flex; flex-wrap: wrap; gap: 16px; font-size: 14px;">                <div style="display: flex; align-items: center;">
                    <div style="width: 20px; height: 20px; background-color: #22c55e; border: 2px solid #000; margin-right: 8px; border-radius: 4px;"></div>
                    <span style="color: #000; font-weight: 500;">{{ translate('reservations.available', 'Disponible') }}</span>
                </div>
                <div style="display: flex; align-items: center;">
                    <div style="width: 20px; height: 20px; background-color: #fbbf24; border: 2px solid #000; margin-right: 8px; border-radius: 4px;"></div>
                    <span style="color: #000; font-weight: 500;">{{ translate('reservations.reserved', 'Reservada') }}</span>
                </div>
                <div style="display: flex; align-items: center;">
                    <div style="width: 20px; height: 20px; background-color: #ef4444; border: 2px solid #000; margin-right: 8px; border-radius: 4px;"></div>
                    <span style="color: #000; font-weight: 500;">{{ translate('reservations.occupied', 'Ocupada') }}</span>
                </div>
                <div style="display: flex; align-items: center;">
                    <div style="width: 20px; height: 20px; background-color: #6b7280; border: 2px solid #000; margin-right: 8px; border-radius: 4px;"></div>
                    <span style="color: #000; font-weight: 500;">{{ translate('reservations.unavailable', 'No disponible') }}</span>
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
                    </svg>                    <!-- Información de la mesa -->
                    <div class="table-info absolute -bottom-9 left-1/2 transform -translate-x-1/2 text-center text-gray-900 dark:text-gray-100 font-semibold text-sm whitespace-nowrap">
                        <div class="table-number">Mesa {{ table.table_number || table.id }}</div>
                        <div class="table-capacity text-xs text-gray-600 dark:text-gray-300">{{ table.capacity }} {{ translate('reservations.people', 'personas') }}</div>
                    </div>
                </div>
            </div>
        </div>        <!-- Modal de Reserva -->
        <div v-if="showReservationModal" 
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-xl max-w-lg w-full mx-4 shadow-2xl max-h-[90vh] overflow-y-auto">
                <div class="bg-blue-600 text-white p-6 rounded-t-xl">
                    <div class="flex justify-between items-center">                        <h3 class="text-xl font-bold">
                            📅 {{ translate('reservations.reserve_table', 'Reservar Mesa') }} {{ selectedTable?.table_number || selectedTable?.id }}
                        </h3>
                        <button @click="closeReservationModal" 
                                class="text-white hover:text-gray-200 text-2xl">
                            ✕
                        </button>
                    </div>
                </div>
                
                <div class="p-6">
                    <form @submit.prevent="confirmReservation" class="space-y-4">
                        <!-- Información de la mesa -->
                        <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg p-4">                            <div class="text-blue-800 dark:text-blue-200">
                                <div class="font-semibold">Mesa {{ selectedTable?.table_number || selectedTable?.id }}</div>
                                <div class="text-sm">{{ translate('reservations.capacity', 'Capacidad') }}: {{ selectedTable?.capacity }} {{ translate('reservations.people', 'personas') }}</div>
                                <div class="text-sm">{{ translate('reservations.duration', 'Duración') }}: 1 {{ translate('reservations.hour', 'hora') }}</div>
                            </div>
                        </div>                        <!-- Fecha -->
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">
                                📅 {{ translate('reservations.date', 'Fecha') }}
                            </label>
                            <input
                                type="date"
                                v-model="reservationForm.reservation_date"
                                :min="minDate"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 dark:focus:border-blue-400 focus:outline-none"
                                required
                            />
                        </div>                        <!-- Hora -->
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">
                                🕒 {{ translate('reservations.time', 'Hora') }}
                            </label>
                            
                            <!-- Selector de horas visual -->
                            <div v-if="reservationForm.reservation_date" class="space-y-3">                                <div v-if="loadingHours" class="text-center py-4">
                                    <div class="inline-flex items-center">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        {{ translate('reservations.loading_hours', 'Cargando horarios disponibles...') }}
                                    </div>
                                </div>
                                  <div v-else-if="availableHours.length > 0" class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                                    <button
                                        v-for="hour in availableHours"
                                        :key="hour.time"
                                        type="button"
                                        @click="selectHour(hour)"
                                        :disabled="!hour.available"                                        :class="{
                                            'bg-blue-500 text-white border-blue-500 shadow-md ring-2 ring-blue-300': reservationForm.reservation_time === hour.time,
                                            'bg-red-100 text-red-500 border-red-300 cursor-not-allowed opacity-70': !hour.available && hour.reason === 'reserved',
                                            'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed opacity-50': !hour.available && hour.reason === 'past_time',
                                            'bg-white text-gray-700 border-gray-300 hover:bg-blue-50 hover:border-blue-300 hover:shadow-sm': hour.available && reservationForm.reservation_time !== hour.time
                                        }"
                                        class="px-3 py-2 border rounded-lg text-sm font-medium transition-all duration-200 flex items-center justify-center min-h-[2.5rem]"
                                    >                                        <div class="text-center">
                                            <div class="font-semibold">{{ hour.display }}</div>
                                            <div v-if="!hour.available" class="text-xs mt-1 text-red-500">
                                                <span v-if="hour.reason === 'past_time'">⏰ Pasado</span>
                                                <span v-else-if="hour.reason === 'reserved'">🚫 Reservado</span>
                                                <span v-else>❌ No disponible</span>
                                            </div>
                                            <div v-else class="text-xs text-green-500 mt-1">✅ Disponible</div>
                                        </div>
                                    </button>
                                </div>
                                
                                <div v-else class="text-center py-4 text-gray-500 dark:text-gray-400">
                                    {{ translate('reservations.no_hours_available', 'No hay horarios disponibles para esta fecha') }}
                                </div>
                            </div>
                            
                            <div v-else class="text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                {{ translate('reservations.select_date_first', 'Selecciona una fecha para ver los horarios disponibles') }}
                            </div>
                            
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                {{ translate('reservations.minimum_advance_notice', 'Mínimo 1 hora de anticipación') }}
                            </p>
                        </div>

                        <!-- Número de personas -->
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">
                                👥 {{ translate('reservations.party_size', 'Personas') }}
                            </label>
                            <select
                                v-model="reservationForm.party_size"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 dark:focus:border-blue-400 focus:outline-none"
                                required
                            >
                                <option v-for="n in selectedTable?.capacity || 4" :key="n" :value="n">
                                    {{ n }} {{ n === 1 ? translate('reservations.person', 'persona') : translate('reservations.people', 'personas') }}
                                </option>
                            </select>
                        </div>

                        <!-- Peticiones especiales -->
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">
                                💬 {{ translate('reservations.special_requests', 'Peticiones especiales') }}
                            </label>
                            <textarea
                                v-model="reservationForm.special_requests"
                                :placeholder="translate('reservations.special_requests_placeholder', 'Déjanos saber si tienes alguna petición especial...')"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 dark:focus:border-blue-400 focus:outline-none resize-none"
                            ></textarea>
                        </div>                        <!-- Cupones disponibles -->
                        <div v-if="availableCoupons.length > 0 && !selectedCoupon">
                            <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">
                                🎟️ {{ translate('reservations.available_coupons', 'Cupones disponibles') }}
                            </label>
                            <div class="space-y-2 max-h-32 overflow-y-auto">
                                <div
                                    v-for="coupon in availableCoupons"
                                    :key="coupon.id"
                                    @click="applyCoupon(coupon)"
                                    class="p-3 border border-green-200 dark:border-green-800 rounded-lg bg-green-50 dark:bg-green-900/30 cursor-pointer hover:bg-green-100 dark:hover:bg-green-900/50 transition-colors"
                                >
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <div class="font-medium text-green-800 dark:text-green-200">
                                                {{ coupon.code }}
                                            </div>
                                            <div class="text-sm text-green-600 dark:text-green-400">
                                                {{ coupon.description }}
                                            </div>
                                        </div>
                                        <div class="text-green-700 dark:text-green-300 font-bold">
                                            <span v-if="coupon.discount_type === 'percentage'">
                                                -{{ coupon.value }}%
                                            </span>
                                            <span v-else>
                                                -€{{ coupon.value }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cupón aplicado -->
                        <div v-if="selectedCoupon" class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg p-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <div class="text-green-800 dark:text-green-200 font-medium">
                                        ✅ {{ selectedCoupon.code }} {{ translate('reservations.applied', 'aplicado') }}
                                    </div>
                                    <div class="text-sm text-green-600 dark:text-green-400">
                                        {{ selectedCoupon.description }}
                                    </div>
                                    <div class="text-sm text-green-600 dark:text-green-400">
                                        {{ translate('reservations.discount', 'Descuento') }}: 
                                        <span v-if="selectedCoupon.discount_type === 'percentage'">
                                            {{ selectedCoupon.value }}%
                                        </span>
                                        <span v-else>
                                            €{{ selectedCoupon.value }}
                                        </span>
                                    </div>
                                </div>
                                <button 
                                    type="button" 
                                    @click="removeCoupon"
                                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 p-1"
                                >
                                    ✕
                                </button>
                            </div>
                        </div>
                        
                        <div v-if="reservationError" class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 rounded-lg p-3">
                            ⚠️ {{ reservationError }}
                        </div>
                          <div v-if="reservationSuccess" class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 rounded-lg p-3">
                            ✅ {{ translate('reservations.reservation_success', '¡Reserva realizada con éxito!') }}
                            <br>
                            <small class="text-green-600 dark:text-green-400">{{ translate('reservations.redirecting', 'Redirigiendo...') }}</small>
                        </div>
                        
                        <button
                            v-if="!reservationSuccess"
                            type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-all duration-200"
                        >
                            ✅ {{ translate('reservations.confirm_reservation', 'Confirmar Reserva') }}
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
