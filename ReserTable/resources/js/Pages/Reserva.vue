<script setup>
import { Head } from '@inertiajs/vue3';
import TableMap from '@/Components/TableMap.vue';
import ThemeManager from '@/Components/ThemeManager.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>

<template>
    <Head :title="t('reservations.title')" />
    <ThemeManager />
    
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
        <div class="container mx-auto px-4 py-12">
            <!-- Header de la página -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-100 dark:bg-blue-900 rounded-full mb-6">
                    <i class="pi pi-calendar text-3xl text-blue-600 dark:text-blue-400"></i>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                    {{ t('reservations.title') }}
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                    {{ t('reservations.subtitle') }}
                </p>
            </div>

            <!-- Leyenda de estados -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8 max-w-4xl mx-auto">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 text-center">
                    <i class="pi pi-info-circle mr-2"></i>
                    {{ t('reservations.table_status') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex items-center justify-center p-3 bg-green-50 dark:bg-green-900/30 rounded-lg">
                        <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
                        <span class="text-green-700 dark:text-green-300 font-medium">{{ t('reservations.available') }}</span>
                    </div>
                    <div class="flex items-center justify-center p-3 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg">
                        <div class="w-4 h-4 bg-yellow-500 rounded-full mr-3"></div>
                        <span class="text-yellow-700 dark:text-yellow-300 font-medium">{{ t('reservations.reserved') }}</span>
                    </div>
                    <div class="flex items-center justify-center p-3 bg-red-50 dark:bg-red-900/30 rounded-lg">
                        <div class="w-4 h-4 bg-red-500 rounded-full mr-3"></div>
                        <span class="text-red-700 dark:text-red-300 font-medium">{{ t('reservations.occupied') }}</span>
                    </div>
                </div>
            </div>

            <!-- Mapa de mesas -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                <TableMap />
            </div>
        </div>
    </div>
</template>