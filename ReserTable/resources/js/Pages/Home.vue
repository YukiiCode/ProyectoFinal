<script setup>
import { Head, Link, usePage, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import GlobalToast from '@/Components/GlobalToast.vue'
import SettingsDropdown from '@/Components/SettingsDropdown.vue'

const { t } = useI18n()
const page = usePage()

// Props del controlador
const featuredProducts = computed(() => page.props.featuredProducts || [])
const categories = computed(() => page.props.categories || [])
const coupons = computed(() => page.props.coupons || [])
const stats = computed(() => page.props.stats || {})

// Usuario autenticado
const user = computed(() => page.props.auth?.user || null)
const isAuthenticated = computed(() => !!user.value)

// Función para cerrar sesión
const logout = () => {
    if (confirm(t('auth.confirm_logout') || '¿Estás seguro de que quieres cerrar sesión?')) {
        router.post('/logout')
    }
}

// Referencias reactivas para modales
const showCouponsModal = ref(false)

// Formatear fecha para cupones
const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    })
}

// Formatear porcentaje de descuento
const formatDiscount = (coupon) => {
    if (coupon.discount_type === 'percentage') {
        return `${coupon.discount_value}%`
    } else {
        return `€${coupon.discount_value}`
    }
}
</script>

<template>
    <Head title="ReserTable - Reservas de Restaurante" />
    
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
        <!-- Simple Header -->
        <nav class="bg-white dark:bg-gray-800 shadow-lg">
            <div class="container mx-auto px-4">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <Link href="/" class="text-2xl font-bold text-red-600 dark:text-red-400">
                            ReserTable
                        </Link>
                    </div>                    <div class="flex items-center space-x-4">                        <div class="hidden md:flex space-x-8">
                            <Link href="/" class="text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400">
                                {{ t('common.home') }}
                            </Link>
                            <Link href="/menu" class="text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400">
                                {{ t('common.menu') }}
                            </Link>
                            <a href="#reservas" class="text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400">
                                {{ t('common.reservations') }}
                            </a>                        </div>
                        
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
                            <Link href="/login" class="text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 text-sm font-medium">
                                {{ t('common.login') }}
                            </Link>
                            <Link href="/register" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                {{ t('common.register') }}
                            </Link>
                        </div>
                        
                        <!-- Settings Dropdown -->
                        <SettingsDropdown variant="public" />
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Hero Section -->
        <section class="relative bg-gradient-to-br from-red-600 via-red-700 to-red-800 text-white overflow-hidden">
            <div class="relative container mx-auto px-4 py-20">                <div class="text-center max-w-4xl mx-auto">
                    <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
                        {{ t('welcome.title') }}
                    </h1>
                    <p class="text-xl md:text-2xl mb-8 text-red-100 max-w-2xl mx-auto">
                        {{ t('welcome.subtitle') }}
                    </p>
                      <!-- CTA Buttons -->                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <Link href="/reservas" class="bg-yellow-400 hover:bg-yellow-300 text-gray-900 font-bold py-4 px-8 rounded-full transition-all duration-300 transform hover:scale-105 shadow-lg">
                            📅 {{ t('welcome.reserve_now') }}
                        </Link>
                        <Link href="/menu" class="bg-transparent border-2 border-white hover:bg-white hover:text-red-600 text-white font-bold py-4 px-8 rounded-full transition-all duration-300">
                            📖 {{ t('welcome.view_menu') }}
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-16 bg-gray-50 dark:bg-gray-900">
            <div class="container mx-auto px-4">                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg">
                        <div class="text-3xl font-bold text-red-600 dark:text-red-400 mb-2">{{ stats.total_tables || 0 }}</div>
                        <div class="text-gray-600 dark:text-gray-300">{{ t('welcome.stats.total_tables') }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg">
                        <div class="text-3xl font-bold text-green-600 dark:text-green-400 mb-2">{{ stats.available_tables || 0 }}</div>
                        <div class="text-gray-600 dark:text-gray-300">{{ t('welcome.stats.available_tables') }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg">
                        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">{{ stats.total_products || 0 }}</div>
                        <div class="text-gray-600 dark:text-gray-300">{{ t('welcome.stats.total_products') }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg">
                        <div class="text-3xl font-bold text-purple-600 dark:text-purple-400 mb-2">{{ stats.categories_count || 0 }}</div>
                        <div class="text-gray-600 dark:text-gray-300">{{ t('welcome.stats.categories') }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Products Section -->
        <section class="py-16 bg-white dark:bg-gray-800">
            <div class="container mx-auto px-4">                <div class="text-center mb-12">
                    <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        {{ t('welcome.featured_menu') }}
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                        {{ t('welcome.featured_menu_description') }}
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="product in featuredProducts" :key="product.id" 
                         class="bg-gray-50 dark:bg-gray-700 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow duration-300">
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                                {{ product.name }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                {{ product.description }}
                            </p>
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-2xl font-bold text-red-600 dark:text-red-400">
                                    €{{ product.price }}
                                </span>
                                <span class="bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 px-3 py-1 rounded-full text-sm">
                                    {{ product.category }}
                                </span>
                            </div>                            <div v-if="product.allergens && product.allergens.length > 0" class="mb-4">
                                <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ t('welcome.allergens') }}:</div>
                                <div class="flex flex-wrap gap-2">
                                    <span v-for="allergen in product.allergens" :key="allergen.id"
                                          class="bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 px-2 py-1 rounded text-xs">
                                        {{ allergen.name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Categories Section -->
        <section class="py-16 bg-gray-50 dark:bg-gray-900">
            <div class="container mx-auto px-4">                <div class="text-center mb-12">
                    <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        {{ t('welcome.categories_section.title') }}
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                        {{ t('welcome.categories_section.description') }}
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="category in categories" :key="category.id"
                         class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 cursor-pointer">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                            {{ category.name }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            {{ category.description }}
                        </p>
                        <div class="text-red-600 dark:text-red-400 font-semibold">
                            {{ category.products_count }} platos
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Coupons Section -->
        <section class="py-16 bg-white dark:bg-gray-800">
            <div class="container mx-auto px-4">                <div class="text-center mb-12">
                    <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        {{ t('welcome.offers_section.title') }}
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                        {{ t('welcome.offers_section.description') }}
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <div v-for="coupon in coupons" :key="coupon.id"
                         class="bg-gradient-to-br from-yellow-400 to-yellow-500 p-6 rounded-xl shadow-lg text-gray-900 relative overflow-hidden">
                        <div class="relative z-10">
                            <div class="text-3xl font-bold mb-2">
                                {{ formatDiscount(coupon) }} OFF
                            </div>
                            <div class="text-lg font-semibold mb-2">
                                {{ coupon.description }}
                            </div>                            <div class="text-sm mb-2">
                                {{ t('welcome.offers_section.code') }}: <span class="font-mono font-bold">{{ coupon.code }}</span>
                            </div>
                            <div class="text-xs">
                                {{ t('welcome.offers_section.expires') }} {{ formatDate(coupon.expires_at) }}
                            </div>
                            <div v-if="coupon.minimum_amount > 0" class="text-xs">
                                {{ t('welcome.offers_section.minimum') }} €{{ coupon.minimum_amount }}
                            </div>
                        </div>
                        <div class="absolute top-0 right-0 w-20 h-20 bg-white opacity-10 rounded-full -mr-10 -mt-10"></div>
                    </div>
                </div>
                
                <div class="text-center mt-8">
                    <button @click="showCouponsModal = true"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition-colors duration-300">
                        Ver Todos los Cupones
                    </button>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 bg-gradient-to-r from-red-600 to-red-800 text-white">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-4xl md:text-5xl font-bold mb-6">
                    ¿Listo para una experiencia única?
                </h2>
                <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
                    Reserva tu mesa ahora y déjanos sorprenderte
                </p>                <Link href="/reservas" class="bg-yellow-400 hover:bg-yellow-300 text-gray-900 font-bold py-4 px-8 rounded-full text-xl transition-all duration-300 transform hover:scale-105 shadow-lg">
                    Hacer Reserva
                </Link>
            </div>
        </section>

        <!-- Simple Footer -->
        <footer class="bg-gray-900 text-white py-12">
            <div class="container mx-auto px-4">
                <div class="text-center">
                    <div class="text-3xl font-bold text-red-400 mb-4">ReserTable</div>
                    <p class="text-gray-400 mb-6">Tu experiencia gastronómica perfecta nos espera</p>
                    <div class="text-gray-500 text-sm">
                        © 2025 ReserTable. Todos los derechos reservados.
                    </div>
                </div>
            </div>
        </footer>

        <!-- Modal para mostrar todos los cupones -->
        <div v-if="showCouponsModal" 
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-xl max-w-4xl w-full mx-4 max-h-[80vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Todos los Cupones Disponibles
                    </h3>
                    <button @click="showCouponsModal = false"
                            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        ✕
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="coupon in coupons" :key="coupon.id"
                         class="bg-gradient-to-br from-yellow-400 to-yellow-500 p-4 rounded-lg">
                        <div class="text-gray-900">
                            <div class="text-2xl font-bold mb-2">
                                {{ formatDiscount(coupon) }} OFF
                            </div>
                            <div class="text-sm font-semibold mb-1">
                                {{ coupon.description }}
                            </div>                            <div class="text-xs mb-1">
                                {{ t('welcome.offers_section.code') }}: <span class="font-mono font-bold">{{ coupon.code }}</span>
                            </div>
                            <div class="text-xs">
                                {{ t('welcome.offers_section.expires') }}: {{ formatDate(coupon.expires_at) }}
                            </div>
                        </div>                </div>
            </div>
        </div>

        <!-- Toast Global para notificaciones -->
        <GlobalToast />
    </div>
    </div>
</template>

<style scoped>
/* Estilos específicos para el componente */
</style>
