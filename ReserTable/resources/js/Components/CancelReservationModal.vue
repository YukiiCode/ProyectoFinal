<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ t('client.dashboard.cancel_reservation') }}
                </h3>
            </div>
            
            <!-- Content -->
            <div class="px-6 py-4">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="text-3xl">⚠️</div>
                    <div>
                        <p class="text-gray-900 dark:text-white font-medium">
                            {{ t('client.dashboard.cancel_reservation_confirm') }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ t('client.dashboard.cancel_reservation_warning') }}
                        </p>
                    </div>
                </div>
                
                <!-- Reservation Details -->
                <div v-if="reservation" class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="text-xl">🍽️</div>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white">
                                {{ t('reservations.table') }} {{ reservation.table.table_number }}
                            </h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ formatDate(reservation.reservation_date) }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ reservation.party_size }} {{ reservation.party_size === 1 ? t('reservations.person') : t('reservations.people') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 rounded-b-lg flex justify-end space-x-3">
                <button
                    @click="$emit('close')"
                    class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors"
                >
                    {{ t('client.dashboard.cancel_keep_btn') }}
                </button>
                <button
                    @click="$emit('confirm')"
                    :disabled="loading"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center space-x-2"
                >
                    <svg v-if="loading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>{{ t('client.dashboard.cancel_confirm_btn') }}</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

defineProps({
    show: {
        type: Boolean,
        default: false
    },
    reservation: {
        type: Object,
        default: null
    },
    loading: {
        type: Boolean,
        default: false
    }
});

defineEmits(['close', 'confirm']);

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>
