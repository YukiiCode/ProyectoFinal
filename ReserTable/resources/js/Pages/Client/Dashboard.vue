<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import ModernNavbar from '@/Components/ModernNavbar.vue';
import StatCard from '@/Components/StatCard.vue';
import CancelReservationModal from '@/Components/CancelReservationModal.vue';

const { t } = useI18n();

defineProps({
    auth: Object,
    client: Object,
    stats: Object,
    recent_reservations: Array,
    upcoming_reservations: Array
});

// Estado para el modal de cancelación
const showCancelModal = ref(false);
const reservationToCancel = ref(null);
const cancelLoading = ref(false);

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
        confirmed: 'bg-green-100 text-green-800',
        pending: 'bg-yellow-100 text-yellow-800',
        cancelled: 'bg-red-100 text-red-800',
        completed: 'bg-blue-100 text-blue-800'
    };
    return statusClasses[status] || 'bg-gray-100 text-gray-800';
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
            // Mostrar mensaje de éxito
            router.reload();
        },
        onError: () => {
            cancelLoading.value = false;
        }
    });
};
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <Head :title="t('client.dashboard.title')" />
        
        <ModernNavbar :auth="auth" />
        
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        {{ t('client.dashboard.welcome', { name: auth.user.name }) }}
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ t('client.dashboard.title') }}
                    </p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <StatCard
                    :title="t('client.dashboard.stats.total_reservations')"
                    :value="stats.total_reservations"
                    icon="📅"
                    color="blue"
                />
                <StatCard
                    :title="t('client.dashboard.stats.pending_reservations')"
                    :value="stats.pending_reservations"
                    icon="⏳"
                    color="yellow"
                />
                <StatCard
                    :title="t('client.dashboard.stats.confirmed_reservations')"
                    :value="stats.confirmed_reservations"
                    icon="✅"
                    color="green"
                />                <StatCard
                    :title="t('client.dashboard.stats.completed_reservations')"
                    :value="stats.completed_reservations"
                    icon="🎉"
                    color="purple"
                />
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                        {{ t('client.dashboard.quick_actions.title') }}
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="/reservas" class="block p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                            <div class="flex items-center">
                                <div class="text-2xl mr-3">📅</div>
                                <div>
                                    <h3 class="font-medium text-blue-900 dark:text-blue-100">
                                        {{ t('client.dashboard.quick_actions.new_reservation') }}
                                    </h3>
                                    <p class="text-sm text-blue-700 dark:text-blue-300">
                                        {{ t('client.dashboard.quick_actions.new_reservation_desc') }}
                                    </p>
                                </div>
                            </div>
                        </a>
                        
                        <a href="/menu" class="block p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                            <div class="flex items-center">
                                <div class="text-2xl mr-3">🍽️</div>
                                <div>
                                    <h3 class="font-medium text-green-900 dark:text-green-100">
                                        {{ t('client.dashboard.quick_actions.view_menu') }}
                                    </h3>
                                    <p class="text-sm text-green-700 dark:text-green-300">
                                        {{ t('client.dashboard.quick_actions.view_menu_desc') }}
                                    </p>
                                </div>
                            </div>
                        </a>
                        
                        <a href="/mi-perfil" class="block p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                            <div class="flex items-center">
                                <div class="text-2xl mr-3">👤</div>
                                <div>
                                    <h3 class="font-medium text-purple-900 dark:text-purple-100">
                                        {{ t('client.dashboard.quick_actions.my_profile') }}
                                    </h3>
                                    <p class="text-sm text-purple-700 dark:text-purple-300">
                                        {{ t('client.dashboard.quick_actions.my_profile_desc') }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Reservations -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                            {{ t('client.dashboard.recent_reservations') }}
                        </h2>
                        <a href="/mi-cuenta" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium">
                            {{ t('client.dashboard.view_all') }}
                        </a>
                    </div>

                    <div v-if="recent_reservations.length === 0" class="text-center py-8">
                        <div class="text-6xl mb-4">📅</div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                            {{ t('client.dashboard.no_reservations') }}
                        </h3>                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            {{ t('client.dashboard.no_reservations_desc') }}
                        </p>
                        <a href="/reservas" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            {{ t('client.dashboard.first_reservation') }}
                        </a>
                    </div>                    <div v-else class="space-y-4">
                        <div
                            v-for="reservation in recent_reservations"
                            :key="reservation.id"
                            class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg"
                        >
                            <div class="flex items-center space-x-4">
                                <div class="text-2xl">🍽️</div>
                                <div>                                    <h4 class="font-medium text-gray-900 dark:text-white">
                                        {{ t('reservations.table') }} {{ reservation.table.table_number }}
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ formatDate(reservation.reservation_date) }}
                                    </p>                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ reservation.party_size }} {{ reservation.party_size === 1 ? t('reservations.person') : t('reservations.people') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span :class="['px-2 py-1 rounded-full text-xs font-medium', getStatusClass(reservation.status)]">
                                    {{ getStatusText(reservation.status) }}
                                </span>
                                <!-- Cancel Button -->
                                <button
                                    v-if="reservation.can_cancel"
                                    @click="openCancelModal(reservation)"
                                    class="px-3 py-1 text-xs bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 rounded-lg transition-colors flex items-center space-x-1"
                                    :title="t('client.dashboard.cancel_reservation')"
                                >
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <span>{{ t('common.cancel') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>                </div>
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
