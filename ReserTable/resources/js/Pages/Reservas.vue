<script setup>
import { Head, usePage, router } from '@inertiajs/vue3'
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import TableMapInertia from '@/Components/TableMapInertia.vue'
import GlobalToast from '@/Components/GlobalToast.vue'
import SettingsDropdown from '@/Components/SettingsDropdown.vue'

const { t } = useI18n()
const page = usePage()

const props = defineProps({
    tables: {
        type: Array,
        default: () => []
    }
})

// Usuario autenticado
const user = computed(() => page.props.auth?.user || null)
const isAuthenticated = computed(() => !!user.value)

// Función para cerrar sesión
const logout = () => {
    if (confirm(t('auth.confirm_logout') || '¿Estás seguro de que quieres cerrar sesión?')) {
        router.post('/logout')
    }
}

const handleReservationMade = () => {
    // Recargar la página o actualizar los datos
    window.location.reload()
}
</script>

<template>
    <Head :title="t('reservations.page_title')" />
    
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Header Simple -->
        <nav class="bg-white dark:bg-gray-800 shadow-lg">
            <div class="container mx-auto px-4">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <a href="/" class="text-2xl font-bold text-red-600 dark:text-red-400">
                            ReserTable
                        </a>
                    </div>                    <div class="flex items-center space-x-4">                        <div class="hidden md:flex space-x-8">
                            <a href="/" class="text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400">
                                {{ t('common.home') }}
                            </a>
                            <a href="/reservas" class="text-red-600 dark:text-red-400 font-semibold">
                                {{ t('common.reservations') }}
                            </a>
                            <a href="/menu" class="text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400">
                                {{ t('common.menu') }}
                            </a>
                        </div>
                        
                        <!-- Auth Status -->
                        <div v-if="isAuthenticated" class="flex items-center space-x-4">
                            <div class="hidden sm:flex items-center space-x-2 text-sm">
                                <span class="text-gray-600 dark:text-gray-400">{{ t('common.welcome') }},</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ user.name }}</span>
                            </div>
                            <button 
                                @click="logout"
                                class="text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 text-sm font-medium"
                            >
                                {{ t('common.logout') }}
                            </button>
                        </div>
                        <div v-else class="flex items-center space-x-4">
                            <a href="/login" class="text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 text-sm font-medium">
                                {{ t('common.login') }}
                            </a>
                            <a href="/register" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                {{ t('common.register') }}
                            </a>
                        </div>
                        
                        <!-- Settings Dropdown -->
                        <SettingsDropdown variant="public" />
                    </div>
                </div>
            </div>
        </nav>        <!-- Contenido Principal -->
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
