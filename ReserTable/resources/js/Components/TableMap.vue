<script setup>
import { ref, onMounted, computed, nextTick } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import interact from 'interact.js';

// Eliminamos useI18n por ahora para evitar problemas
// import { useI18n } from 'vue-i18n';
// const { t } = useI18n();

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
    }
});

const emit = defineEmits(['tables-updated', 'reservation-made']);

const tables = ref([]);
const selectedTable = ref(null);
const reservationDateTime = ref(null);
const reservationSuccess = ref(false);
const reservationError = ref('');
const appliedCoupon = ref(null);
const isEmployee = computed(() => usePage().props.auth?.user?.role === 'employee');

const mapWidth = 800;
const mapHeight = 500;

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
                async end(event) {
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
const showReservationModal = ref(false);

const selectTable = (table) => {
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

const onCouponValidated = (coupon) => {
    appliedCoupon.value = coupon;
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
        return;
    }
    
    const reservationData = {
        table_id: selectedTable.value.id,
        reservation_date: reservationDateTime.value,
        party_size: selectedTable.value.capacity,
        discount_coupon_id: appliedCoupon.value?.id || null
    };
    
    router.post('/client/reservations', reservationData, {
        preserveState: true,
        onSuccess: () => {
            reservationSuccess.value = true;
            emit('reservation-made');
            
            // Cerrar modal después de 2 segundos
            setTimeout(() => {
                closeReservationModal();
            }, 2000);
        },
        onError: (errors) => {
            reservationError.value = errors.message || texts.reservations.reservation_error;
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
    <div class="container my-4">
        <div class="d-flex justify-content-center">
            <div
                class="position-relative"
                :style="{
                    backgroundImage: 'url(/images/restaurant-bg.jpg)',
                    backgroundSize: 'cover',
                    width: mapWidth + 'px',
                    height: mapHeight + 'px',
                    borderRadius: '16px',
                    boxShadow: '0 4px 16px #0003'
                }"
            >
                <!-- Mesas con SVG -->
                <div
                    v-for="table in tables"
                    :key="table.id"
                    class="table-marker position-absolute"
                    :class="[table.status, { draggable: isEmployee }]"
                    :data-id="table.id"
                    :style="{
                        left: `calc(${table.x}% - 35px)`,
                        top: `calc(${table.y}% - 35px)`,
                        width: '70px',
                        height: '70px',
                        zIndex: selectedTable?.id === table.id ? 2 : 1
                    }"
                    @click="() => selectTable(table)"
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
                    <div class="table-info">
                        <div class="table-number text-gray-900 dark:text-gray-100">{{ table.id }}</div>
                        <div class="table-capacity text-gray-600 dark:text-gray-300">{{ table.capacity }} {{ texts.reservations.people }}</div>
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
                            📅 {{ texts.reservations.reserve_table }} {{ selectedTable?.id }}
                        </h3>
                        <button @click="closeReservationModal" 
                                class="text-white hover:text-gray-200">
                            ✕
                        </button>
                    </div>
                </div>
                
                <div class="p-6">
                    <form @submit.prevent="confirmReservation" class="space-y-4">
                            <div class="mb-4">
                                <label class="form-label text-gray-700 dark:text-gray-300 font-medium">
                                    <i class="pi pi-calendar mr-2"></i>
                                    {{ t('reservations.date_time') }}
                                </label>
                                <input
                                    type="datetime-local"
                                    v-model="reservationDateTime"
                                    class="form-control bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 focus:border-blue-500 dark:focus:border-blue-400"
                                    required
                                />
                            </div>
                            
                            <!-- Validador de Cupones de Descuento -->
                            <div class="mb-4">
                                <h6 class="text-gray-700 dark:text-gray-300 mb-3">
                                    <i class="pi pi-tag mr-2"></i>
                                    {{ t('reservations.discount_code') }}
                                </h6>
                                <DiscountCouponValidator 
                                    @coupon-validated="onCouponValidated"
                                    :show-header="false"
                                    class="mb-3"
                                />
                                
                                <!-- Cupón aplicado -->
                                <div v-if="appliedCoupon" class="alert alert-info">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="pi pi-check-circle mr-2"></i>
                                            <strong>{{ appliedCoupon.code }}</strong> {{ t('reservations.applied') }}
                                            <br>
                                            <small>
                                                {{ t('reservations.discount') }}: 
                                                <span v-if="appliedCoupon.discount_type === 'percentage'">
                                                    {{ appliedCoupon.value }}%
                                                </span>
                                                <span v-else>
                                                    €{{ appliedCoupon.value }}
                                                </span>
                                            </small>
                                        </div>
                                        <button 
                                            type="button" 
                                            class="btn btn-sm btn-outline-secondary"
                                            @click="removeCoupon"
                                        >
                                            <i class="pi pi-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div v-if="reservationError" class="alert alert-danger bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 rounded-lg p-3 mb-4">
                                <i class="pi pi-exclamation-triangle mr-2"></i>
                                {{ reservationError }}
                            </div>
                            <div v-if="reservationSuccess" class="alert alert-success bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 rounded-lg p-3 mb-4">
                                <i class="pi pi-check-circle mr-2"></i>
                                {{ t('reservations.reservation_success') }} <br>
                                <small class="text-green-600 dark:text-green-400">{{ t('reservations.redirecting') }}</small>
                            </div>
                            
                            <button
                                v-if="!reservationSuccess"
                                type="submit"
                                class="btn btn-primary w-100 bg-blue-600 hover:bg-blue-700 border-blue-600 hover:border-blue-700 text-white font-medium py-3 rounded-lg transition-all duration-200"
                            >
                                <i class="pi pi-check mr-2"></i>
                                {{ t('reservations.confirm_reservation') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.table-marker {
    cursor: pointer;
    transition: transform 0.3s, box-shadow 0.3s;
    user-select: none;
}

.table-svg {
    width: 100%;
    height: 100%;
    transition: transform 0.3s;
}

.table-marker:hover .table-svg {
    transform: scale(1.05) rotate(-2deg);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
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

.table-info {
    position: absolute;
    bottom: -35px;
    left: 50%;
    transform: translateX(-50%);
    text-align: center;
    color: #333;
    font-weight: 600;
    white-space: nowrap;
}

.table-number {
    font-size: 1.1rem;
    margin-bottom: 4px;
}

.table-capacity {
    font-size: 0.9rem;
    color: #666;
}

.draggable {
    border: 2px dashed #2196F3 !important;
    cursor: grab;
}

.draggable:active {
    cursor: grabbing;
}

.modal-header {
    border-bottom: none;
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
}

.modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}
</style>