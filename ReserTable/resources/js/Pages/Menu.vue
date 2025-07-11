<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import GlobalToast from '@/Components/GlobalToast.vue'
import ModernNavbar from '@/Components/ModernNavbar.vue'

const { t } = useI18n()

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

// Obtener icono de alérgeno
const getAllergenIcon = (allergen) => {
    // Si ya tiene icono emoji, usar ese
    if (allergen.icon && allergen.icon.match(/[\u{1F600}-\u{1F64F}]|[\u{1F300}-\u{1F5FF}]|[\u{1F680}-\u{1F6FF}]|[\u{1F1E0}-\u{1F1FF}]/u)) {
        return allergen.icon
    }
    
    // Mapeo de alérgenos a emojis
    const allergenIcons = {
        'Gluten': '🌾',
        'Lácteos': '🥛',
        'Lactosa': '🥛',
        'Huevos': '🥚',
        'Frutos Secos': '🥜',
        'Mariscos': '🦐',
        'Pescado': '🐟',
        'Apio': '🥬',
        'Mostaza': '🟡',
        'Soja': '🌱'
    }
    
    return allergenIcons[allergen.name] || '⚠️'
}

// Obtener color de fondo para alérgeno
const getAllergenColor = (allergen) => {
    const allergenColors = {
        'Gluten': 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200',
        'Lácteos': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        'Lactosa': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        'Huevos': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        'Frutos Secos': 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
        'Mariscos': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        'Pescado': 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200',
        'Apio': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'Mostaza': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        'Soja': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
    }
    
    return allergenColors[allergen.name] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
}

// Obtener imagen del producto
const getProductImage = (product) => {
    if (!product.image_svg) return null
    
    // Si es una URL externa, devolverla tal como está
    if (product.image_svg.startsWith('http')) {
        return product.image_svg
    }
    
    // Si es un archivo local, agregar el prefijo de storage
    return `/storage/${product.image_svg}`
}

// Manejar error de imagen
const handleImageError = (event) => {
    // Evitar bucle infinito - solo intentar una vez
    if (event.target.dataset.errorHandled) return
    event.target.dataset.errorHandled = 'true'
    
    // Al fallar la imagen, ocultar el elemento img y mostrar el emoji fallback
    event.target.style.display = 'none'
    const parent = event.target.closest('.relative')
    if (parent) {
        const emojiDiv = parent.querySelector('.emoji-fallback')
        if (emojiDiv) {
            emojiDiv.style.display = 'flex'
        }
    }
}
</script>

<template>
    <Head :title="t('menu.page_title', 'Menú - ReserTable')" />
    
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Modern Navigation -->
        <ModernNavbar current-page="menu" />

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
                            <!-- Imagen del producto - Altura fija -->
                            <div class="relative h-48 bg-gradient-to-br from-red-50 to-red-100 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-br from-red-400/10 to-orange-400/10"></div>
                                
                                <!-- Imagen real del producto si existe -->
                                <div v-if="product.image_svg" class="absolute inset-0">
                                    <img 
                                        :src="getProductImage(product)" 
                                        :alt="product.name"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                        @error="handleImageError"
                                    />
                                    <div class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition-colors duration-300"></div>
                                    <!-- Emoji fallback oculto -->
                                    <div class="emoji-fallback absolute inset-0 flex items-center justify-center text-6xl group-hover:scale-110 transition-transform duration-300" style="display: none;">
                                        {{ getCategoryIcon(product.category) }}
                                    </div>
                                </div>
                                
                                <!-- Emoji fallback si no hay imagen -->
                                <div v-else class="relative z-10 text-6xl group-hover:scale-110 transition-transform duration-300">
                                    {{ getCategoryIcon(product.category) }}
                                </div>
                                
                                <!-- Precio destacado -->
                                <div class="absolute top-3 right-3 bg-red-600 text-white px-3 py-1 rounded-full font-bold text-sm shadow-lg z-20">
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
                                
                                <!-- Alérgenos mejorados - Siempre al final -->
                                <div v-if="product.allergens && product.allergens.length > 0" class="mt-auto">
                                    <div class="flex items-center mb-2">
                                        <svg class="w-4 h-4 text-amber-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-xs font-semibold text-amber-700 dark:text-amber-300">
                                            {{ t('menu.allergen_warning', 'Contiene alérgenos') }}
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap gap-1.5">
                                        <span 
                                            v-for="allergen in product.allergens" 
                                            :key="allergen.id"
                                            :title="`${t('menu.allergen', 'Alérgeno')}: ${allergen.name}`"
                                            :class="getAllergenColor(allergen)"
                                            class="inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-full border transition-all duration-200 hover:scale-105"
                                        >
                                            <span class="mr-1">{{ getAllergenIcon(allergen) }}</span>
                                            <span class="hidden sm:inline">{{ allergen.name }}</span>
                                            <span class="sm:hidden">{{ allergen.name.substring(0, 3) }}</span>
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
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    line-clamp: 3;
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
