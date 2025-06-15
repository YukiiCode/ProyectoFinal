<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import ModernNavbar from '@/Components/ModernNavbar.vue';
import CancelReservationModal from '@/Components/CancelReservationModal.vue';

const { t } = useI18n();

const props = defineProps({
    auth: Object,
    client: Object,
    reservations: Array,
});

// Estado para el modal de cancelación
const showCancelModal = ref(false);
const reservationToCancel = ref(null);
const cancelLoading = ref(false);

// Estado para filtros
const statusFilter = ref('all');
const searchQuery = ref('');

// Reservas filtradas
const filteredReservations = computed(() => {
    let filtered = props.reservations;
    
    // Filtrar por estado
    if (statusFilter.value !== 'all') {
        filtered = filtered.filter(reservation => reservation.status === statusFilter.value);
    }
    
    // Filtrar por búsqueda
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(reservation => 
            reservation.table.table_number.toString().includes(query) ||
            reservation.special_requests?.toLowerCase().includes(query)
        );
    }
    
    return filtered;
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getStatusClass = (status) => {
    const statusClasses = {
        confirmed: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
        pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
        cancelled: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
        completed: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400'
    };
    return statusClasses[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
};

const getStatusText = (status) => {
    switch (status) {
        case 'confirmed':
            return t('reservations.confirmed');
        case 'pending':
            return t('reservations.pending');
        case 'cancelled':
            return t('reservations.cancelled');
        case 'completed':
            return t('reservations.completed');
        default:
            return status;
    }
};

// Funciones para manejar la cancelación
const openCancelModal = (reservation) => {
    reservationToCancel.value = reservation;
    showCancelModal.value = true;
};

const closeCancelModal = () => {
    showCancelModal.value = false;
    reservationToCancel.value = null;
    cancelLoading.value = false;
};

const confirmCancelReservation = () => {
    if (!reservationToCancel.value) return;
    
    cancelLoading.value = true;
    
    router.put(`/client/reservations/${reservationToCancel.value.id}/cancel`, {}, {
        onSuccess: () => {
            closeCancelModal();
            router.reload();
        },
        onError: () => {
            cancelLoading.value = false;
        }
    });
};

// Estadísticas calculadas
const stats = computed(() => {
    const total = props.reservations.length;
    const pending = props.reservations.filter(r => r.status === 'pending').length;
    const confirmed = props.reservations.filter(r => r.status === 'confirmed').length;
    const completed = props.reservations.filter(r => r.status === 'completed').length;
    const cancelled = props.reservations.filter(r => r.status === 'cancelled').length;
    
    return { total, pending, confirmed, completed, cancelled };
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <Head :title="t('client.dashboard.title')" />
        
        <ModernNavbar :auth="auth" />
        
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                                {{ t('client.dashboard.title') }}
                            </h1>
                            <p class="text-gray-600 dark:text-gray-400">
                                Gestiona todas tus reservas
                            </p>
                        </div>
                        <a href="/reservas" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Nueva Reserva
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.total }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pendientes</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.pending }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Confirmadas</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.confirmed }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Completadas</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.completed }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Canceladas</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.cancelled }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                        <div class="flex items-center space-x-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Estado
                                </label>
                                <select 
                                    v-model="statusFilter" 
                                    class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="all">Todos</option>
                                    <option value="pending">Pendientes</option>
                                    <option value="confirmed">Confirmadas</option>
                                    <option value="completed">Completadas</option>
                                    <option value="cancelled">Canceladas</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Buscar
                                </label>
                                <input 
                                    v-model="searchQuery"
                                    type="text" 
                                    placeholder="Mesa, solicitudes especiales..."
                                    class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                            </div>
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            {{ filteredReservations.length }} de {{ reservations.length }} reservas
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reservations List -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">
                        Mis Reservas
                    </h2>

                    <div v-if="filteredReservations.length === 0" class="text-center py-12">
                        <div class="text-6xl mb-4">📅</div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                            No se encontraron reservas
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            No hay reservas que coincidan con los filtros seleccionados.
                        </p>
                    </div>

                    <div v-else class="space-y-4">
                        <div
                            v-for="reservation in filteredReservations"
                            :key="reservation.id"
                            class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="text-3xl">🍽️</div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 dark:text-white">
                                            {{ t('reservations.table') }} {{ reservation.table.table_number }}
                                        </h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ formatDate(reservation.reservation_date) }}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ reservation.party_size }} {{ reservation.party_size === 1 ? t('reservations.person') : t('reservations.people') }}
                                        </p>
                                        <p v-if="reservation.special_requests" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            <strong>Solicitudes especiales:</strong> {{ reservation.special_requests }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span :class="['px-3 py-1 rounded-full text-sm font-medium', getStatusClass(reservation.status)]">
                                        {{ getStatusText(reservation.status) }}
                                    </span>
                                    <!-- Cancel Button -->
                                    <button
                                        v-if="reservation.can_cancel"
                                        @click="openCancelModal(reservation)"
                                        class="px-3 py-2 text-sm bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 rounded-lg transition-colors flex items-center space-x-2"
                                        :title="t('client.dashboard.cancel_reservation')"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <span>{{ t('common.cancel') }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Cancel Reservation Modal -->
    <CancelReservationModal
        :show="showCancelModal"
        :reservation="reservationToCancel"
        :loading="cancelLoading"
        @close="closeCancelModal"
        @confirm="confirmCancelReservation"
    />
</template>
