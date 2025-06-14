<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import TableMapInertia from '@/Components/TableMapInertia.vue'
import GlobalToast from '@/Components/GlobalToast.vue'
import ModernNavbar from '@/Components/ModernNavbar.vue'

const { t } = useI18n()

const props = defineProps({
    tables: {
        type: Array,
        default: () => []
    }
})

const handleReservationMade = () => {
    // Recargar la página o actualizar los datos
    window.location.reload()
}
</script>

<template>
    <Head :title="t('reservations.page_title')" />
      <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Modern Navigation -->
        <ModernNavbar current-page="reservations" />
        <div class="container mx-auto px-4 py-8">
            <!-- Título -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    {{ t('reservations.title') }}
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    {{ t('reservations.subtitle') }}
                </p>
            </div>

            <!-- Componente del Mapa de Mesas -->
            <div class="max-w-6xl mx-auto">                <TableMapInertia 
                    :tables="tables"
                    @reservation-made="handleReservationMade"
                />
            </div>

            <!-- Información Adicional -->
            <div class="mt-12 text-center">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 max-w-4xl mx-auto">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                        {{ t('reservations.info.title') }}
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="bg-blue-100 dark:bg-blue-900/30 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl">🕒</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                {{ t('reservations.info.schedule.title') }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 whitespace-pre-line">
                                {{ t('reservations.info.schedule.description') }}
                            </p>
                        </div>
                        
                        <div class="text-center">
                            <div class="bg-green-100 dark:bg-green-900/30 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl">✅</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                {{ t('reservations.info.confirmation.title') }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 whitespace-pre-line">
                                {{ t('reservations.info.confirmation.description') }}
                            </p>
                        </div>
                        
                        <div class="text-center">
                            <div class="bg-yellow-100 dark:bg-yellow-900/30 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl">🎯</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                {{ t('reservations.info.table_selection.title') }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 whitespace-pre-line">
                                {{ t('reservations.info.table_selection.description') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>        <!-- Footer Simple -->
        <footer class="bg-gray-900 text-white py-8 mt-16">
            <div class="container mx-auto px-4 text-center">
                <div class="text-2xl font-bold text-red-400 mb-2">ReserTable</div>
                <p class="text-gray-400">{{ t('reservations.footer.tagline') }}</p>
            </div>
        </footer>

        <!-- Toast Global para notificaciones -->
        <GlobalToast />
    </div>
</template>

<style scoped>
/* Estilos específicos para la página de reservas */
</style>
