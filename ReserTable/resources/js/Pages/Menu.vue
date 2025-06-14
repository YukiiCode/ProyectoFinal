<script setup>
import { Head, Link, usePage, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import GlobalToast from '@/Components/GlobalToast.vue'
import SettingsDropdown from '@/Components/SettingsDropdown.vue'

const { t } = useI18n()
const page = usePage()

const props = defineProps({
    products: {
        type: Array,
        default: () => []
    },
    categories: {
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

// Estado para filtrado
const selectedCategory = ref(null)
const searchQuery = ref('')

// Productos filtrados
const filteredProducts = computed(() => {
    let filtered = props.products

    // Filtrar por categoría
    if (selectedCategory.value) {
        filtered = filtered.filter(product => product.category_id === selectedCategory.value)
    }

    // Filtrar por búsqueda
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(product => 
            product.name.toLowerCase().includes(query) ||
            product.description.toLowerCase().includes(query) ||
            product.category.toLowerCase().includes(query)
        )
    }

    return filtered
})

// Productos agrupados por categoría
const productsByCategory = computed(() => {
    const grouped = {}
    
    filteredProducts.value.forEach(product => {
        const categoryName = product.category
        if (!grouped[categoryName]) {
            grouped[categoryName] = {
                name: categoryName,
                emoji: getCategoryIcon(categoryName),
                products: []
            }
        }
        grouped[categoryName].products.push(product)
    })
    
    // Ordenar categorías por nombre
    return Object.values(grouped).sort((a, b) => a.name.localeCompare(b.name))
})

// Formatear precio
const formatPrice = (price) => {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR'
    }).format(price)
}

// Limpiar filtros
const clearFilters = () => {
    selectedCategory.value = null
    searchQuery.value = ''
}

// Obtener icono de categoría
const getCategoryIcon = (categoryName) => {
    const icons = {
        'Entrantes': '🥗',
        'Pasta': '🍝',
        'Pizza': '🍕',
        'Carnes': '🥩',
        'Pescados': '🐟',
        'Postres': '🍰',
        'Bebidas': '🍷'
    }
    return icons[categoryName] || '🍽️'
}
</script>

<template>
    <Head :title="t('menu.page_title', 'Menú - ReserTable')" />
    
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Header Simple -->
        <nav class="bg-white dark:bg-gray-800 shadow-lg">
            <div class="container mx-auto px-4">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <Link href="/" class="text-2xl font-bold text-red-600 dark:text-red-400">
                            ReserTable
                        </Link>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="hidden md:flex space-x-8">
                            <Link href="/" class="text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400">
                                {{ t('common.home') }}
                            </Link>
                            <Link href="/reservas" class="text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400">
                                {{ t('common.reservations') }}
                            </Link>
                            <Link href="/menu" class="text-red-600 dark:text-red-400 font-semibold">
                                {{ t('common.menu') }}
                            </Link>
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

        <!-- Contenido Principal -->
        <div class="container mx-auto px-4 py-8">
            <!-- Título -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    {{ t('menu.title', 'Nuestro Menú') }}
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    {{ t('menu.subtitle', 'Descubre nuestra selección de platos tradicionales preparados con ingredientes frescos') }}
                </p>
            </div>

            <!-- Filtros Compactos -->
            <div class="mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <div class="flex flex-col sm:flex-row gap-4 items-stretch">
                        <!-- Búsqueda -->
                        <div class="flex-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input
                                v-model="searchQuery"
                                type="text"
                                :placeholder="t('menu.search_placeholder', 'Buscar platos...')"
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                            />
                        </div>
                        
                        <!-- Filtro por categoría -->
                        <div class="sm:w-72">
                            <select 
                                v-model="selectedCategory"
                                class="w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            >
                                <option :value="null">{{ t('menu.all_categories', 'Todas las categorías') }}</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }} ({{ category.products_count }})
                                </option>
                            </select>
                        </div>
                        
                        <!-- Botón limpiar filtros -->
                        <button
                            v-if="selectedCategory || searchQuery"
                            @click="clearFilters"
                            class="sm:w-auto px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors font-medium shadow-md hover:shadow-lg flex items-center justify-center gap-2"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <span class="hidden sm:inline">{{ t('menu.clear_filters', 'Limpiar') }}</span>
                        </button>
                    </div>
                    
                    <!-- Contador de resultados compacto -->
                    <div v-if="searchQuery || selectedCategory" class="mt-3 text-center">
                        <span class="text-gray-600 dark:text-gray-400 text-sm bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full">
                            {{ filteredProducts.length }} {{ t('menu.results_found', 'resultados') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Lista de Productos Agrupados por Categoría -->
            <div v-if="productsByCategory.length > 0" class="space-y-12">
                <div v-for="category in productsByCategory" :key="category.name" class="category-section">
                    <!-- Título de Categoría -->
                    <div class="flex items-center mb-8">
                        <div class="text-5xl mr-4">{{ category.emoji }}</div>
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
                                {{ category.name }}
                            </h2>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">
                                {{ t('menu.products_count', '{count} platos', { count: category.products.length }) }}
                            </p>
                        </div>
                        <div class="flex-1 ml-6 h-px bg-gradient-to-r from-red-500 to-transparent"></div>
                    </div>
                    
                    <!-- Grid de Productos de la Categoría -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <div 
                            v-for="product in category.products" 
                            :key="product.id"
                            class="group bg-white dark:bg-gray-800 rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex flex-col h-full"
                        >
                            <!-- Imagen con emoji - Altura fija -->
                            <div class="relative h-48 bg-gradient-to-br from-red-50 to-red-100 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-br from-red-400/10 to-orange-400/10"></div>
                                <div class="relative z-10 text-6xl group-hover:scale-110 transition-transform duration-300">
                                    {{ getCategoryIcon(product.category) }}
                                </div>
                                <!-- Precio destacado -->
                                <div class="absolute top-3 right-3 bg-red-600 text-white px-3 py-1 rounded-full font-bold text-sm shadow-lg">
                                    {{ formatPrice(product.price) }}
                                </div>
                            </div>
                            
                            <!-- Contenido - Flex grow para altura uniforme -->
                            <div class="p-5 flex flex-col flex-grow">
                                <div class="flex-grow">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors line-clamp-2">
                                        {{ product.name }}
                                    </h3>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed line-clamp-3 mb-3">
                                        {{ product.description }}
                                    </p>
                                </div>
                                
                                <!-- Alérgenos - Siempre al final -->
                                <div v-if="product.allergens && product.allergens.length > 0" class="mt-auto">
                                    <div class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-2">
                                        {{ t('menu.contains', 'Contiene') }}:
                                    </div>
                                    <div class="flex flex-wrap gap-1">
                                        <span 
                                            v-for="allergen in product.allergens.slice(0, 4)" 
                                            :key="allergen.id"
                                            :title="allergen.name"
                                            class="text-xs bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-200 px-2 py-1 rounded-full border border-yellow-200 dark:border-yellow-700"
                                        >
                                            {{ allergen.icon || allergen.name.charAt(0).toUpperCase() }}
                                        </span>
                                        <span 
                                            v-if="product.allergens.length > 4"
                                            class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 px-2 py-1 rounded-full"
                                        >
                                            +{{ product.allergens.length - 4 }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mensaje mejorado si no hay productos -->
            <div v-else class="text-center py-16">
                <div class="max-w-md mx-auto">
                    <div class="text-8xl mb-6 animate-bounce">🔍</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        {{ t('menu.no_products', 'No se encontraron productos') }}
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">
                        {{ t('menu.try_different_filters', 'Intenta con diferentes filtros o busca otro término') }}
                    </p>
                    <button
                        @click="clearFilters"
                        class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl font-medium transition-colors duration-200 shadow-lg hover:shadow-xl"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        {{ t('menu.show_all', 'Ver todos los platos') }}
                    </button>
                </div>
            </div>

            <!-- Call to Action -->
            <div class="mt-16 text-center">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 max-w-2xl mx-auto">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        {{ t('menu.cta_title', '¿Te gustó lo que viste?') }}
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        {{ t('menu.cta_subtitle', 'Reserva tu mesa ahora y disfruta de una experiencia gastronómica única') }}
                    </p>
                    <Link 
                        href="/reservas"
                        class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg text-lg font-semibold transition-colors inline-block"
                    >
                        {{ t('menu.cta_button', 'Reservar Mesa') }}
                    </Link>
                </div>
            </div>
        </div>

        <!-- Footer Simple -->
        <footer class="bg-gray-900 text-white py-8 mt-16">
            <div class="container mx-auto px-4 text-center">
                <div class="text-2xl font-bold text-red-400 mb-2">ReserTable</div>
                <p class="text-gray-400">{{ t('menu.footer_tagline', 'Tu experiencia gastronómica perfecta') }}</p>
            </div>
        </footer>

        <!-- Toast Global para notificaciones -->
        <GlobalToast />
    </div>
</template>

<style scoped>
/* Estilos específicos para la página del menú */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.category-section {
    scroll-margin-top: 2rem;
}

/* Animaciones suaves para las tarjetas */
.group:hover {
    transform: translateY(-4px);
}

/* Efectos de gradiente para los headers de categoría */
.category-section h2 {
    background: linear-gradient(135deg, #dc2626, #ea580c);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Mejoras para modo oscuro */
.dark .category-section h2 {
    background: linear-gradient(135deg, #f87171, #fb923c);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
</style>
