<script setup>
import { Head, router } from '@inertiajs/vue3';
import ThemeManager from '@/Components/ThemeManager.vue';
import { useI18n } from 'vue-i18n';
import { ref, computed } from 'vue';

const { t } = useI18n();

const props = defineProps({
    products: {
        type: Array,
        default: () => []
    },
    categories: {
        type: Array,
        default: () => []
    },
    allergens: {
        type: Array,
        default: () => []
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

// Estado local
const selectedProduct = ref(null);
const searchQuery = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category || '');
const showAllergenFilter = ref(false);
const selectedAllergens = ref([]);

// Productos filtrados
const filteredProducts = computed(() => {
    let products = props.products;

    // Filtrar por búsqueda
    if (searchQuery.value) {
        products = products.filter(product =>
            product.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            product.description.toLowerCase().includes(searchQuery.value.toLowerCase())
        );
    }

    // Filtrar por categoría
    if (selectedCategory.value) {
        products = products.filter(product => product.category_id == selectedCategory.value);
    }

    // Filtrar por alérgenos (excluir productos que contengan alérgenos seleccionados)
    if (selectedAllergens.value.length > 0) {
        products = products.filter(product => {
            const productAllergenIds = product.allergens.map(a => a.id);
            return !selectedAllergens.value.some(allergenId => 
                productAllergenIds.includes(allergenId)
            );
        });
    }

    return products;
});

// Funciones de filtrado
const applyFilters = () => {
    const params = {};
    
    if (searchQuery.value) params.search = searchQuery.value;
    if (selectedCategory.value) params.category = selectedCategory.value;
    
    router.get('/client/menu', params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedCategory.value = '';
    selectedAllergens.value = [];
    router.get('/client/menu');
};

// Funciones de producto
const showProductDetails = (product) => {
    selectedProduct.value = product;
};

const closeProductDetails = () => {
    selectedProduct.value = null;
};

// Función para obtener el icono del alérgeno
const getAllergenIcon = (allergen) => {
    // Mapeo de alérgenos comunes a iconos
    const iconMap = {
        'gluten': '🌾',
        'lactosa': '🥛',
        'huevos': '🥚',
        'frutos secos': '🥜',
        'mariscos': '🦐',
        'pescado': '🐟',
        'soja': '🌱',
        'mostaza': '🌭',
        'apio': '🥬',
        'sulfitos': '🍷',
        'sesamo': '🌰',
        'lupinos': '🌸'
    };
    
    return allergen.icon || iconMap[allergen.name.toLowerCase()] || '⚠️';
};

// Función para formatear precio
const formatPrice = (price) => {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR'
    }).format(price);
};
</script>

<template>
    <Head :title="t('menu.title')" />
    <ThemeManager />
    
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
        <div class="container mx-auto px-4 py-12">
            <!-- Header -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-orange-100 dark:bg-orange-900 rounded-full mb-6">
                    <i class="pi pi-book text-3xl text-orange-600 dark:text-orange-400"></i>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                    Nuestro Menú
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                    Descubre nuestros deliciosos platos con información detallada sobre ingredientes y alérgenos
                </p>
            </div>

            <!-- Filtros -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Búsqueda -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Buscar platos
                        </label>
                        <input
                            type="text"
                            v-model="searchQuery"
                            @input="applyFilters"
                            placeholder="Nombre del plato..."
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                        />
                    </div>

                    <!-- Categorías -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Categoría
                        </label>
                        <select
                            v-model="selectedCategory"
                            @change="applyFilters"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="">Todas las categorías</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Filtro de alérgenos -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Excluir alérgenos
                        </label>
                        <button
                            @click="showAllergenFilter = !showAllergenFilter"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white text-left"
                        >
                            <span v-if="selectedAllergens.length === 0">Seleccionar alérgenos</span>
                            <span v-else>{{ selectedAllergens.length }} alérgenos excluidos</span>
                            <i class="pi pi-chevron-down float-right mt-1"></i>
                        </button>
                    </div>

                    <!-- Botón limpiar filtros -->
                    <div class="flex items-end">
                        <button
                            @click="clearFilters"
                            class="w-full px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition duration-300"
                        >
                            Limpiar filtros
                        </button>
                    </div>
                </div>

                <!-- Panel de alérgenos -->
                <div v-if="showAllergenFilter" class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">
                        Excluir productos que contengan:
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        <label
                            v-for="allergen in allergens"
                            :key="allergen.id"
                            class="flex items-center cursor-pointer"
                        >
                            <input
                                type="checkbox"
                                :value="allergen.id"
                                v-model="selectedAllergens"
                                class="mr-2"
                            />
                            <span class="text-lg mr-2">{{ getAllergenIcon(allergen) }}</span>
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ allergen.name }}</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Lista de productos -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="product in filteredProducts"
                    :key="product.id"
                    @click="showProductDetails(product)"
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden cursor-pointer transform transition-all duration-300 hover:scale-105 hover:shadow-xl"
                >
                    <!-- Imagen del producto (placeholder) -->
                    <div class="h-48 bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center">
                        <i class="pi pi-image text-4xl text-white opacity-50"></i>
                    </div>

                    <!-- Información del producto -->
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                {{ product.name }}
                            </h3>
                            <span class="text-lg font-bold text-orange-600 dark:text-orange-400">
                                {{ formatPrice(product.price) }}
                            </span>
                        </div>

                        <p class="text-gray-600 dark:text-gray-300 text-sm mb-3 line-clamp-2">
                            {{ product.description }}
                        </p>

                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                {{ product.category }}
                            </span>

                            <!-- Alérgenos -->
                            <div v-if="product.allergens.length > 0" class="flex space-x-1">
                                <span
                                    v-for="allergen in product.allergens.slice(0, 3)"
                                    :key="allergen.id"
                                    :title="allergen.name"
                                    class="text-lg"
                                >
                                    {{ getAllergenIcon(allergen) }}
                                </span>
                                <span v-if="product.allergens.length > 3" class="text-xs text-gray-500">
                                    +{{ product.allergens.length - 3 }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mensaje cuando no hay productos -->
            <div v-if="filteredProducts.length === 0" class="text-center py-12">
                <i class="pi pi-search text-6xl text-gray-400 dark:text-gray-600 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                    No se encontraron productos
                </h3>
                <p class="text-gray-600 dark:text-gray-300">
                    Intenta ajustar tus filtros para ver más opciones
                </p>
            </div>
        </div>
    </div>

    <!-- Modal de detalles del producto -->
    <div
        v-if="selectedProduct"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
        @click="closeProductDetails"
    >
        <div
            @click.stop
            class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full max-h-90vh overflow-y-auto"
        >
            <!-- Header del modal -->
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ selectedProduct.name }}
                        </h2>
                        <p class="text-lg font-semibold text-orange-600 dark:text-orange-400">
                            {{ formatPrice(selectedProduct.price) }}
                        </p>
                    </div>
                    <button
                        @click="closeProductDetails"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                    >
                        <i class="pi pi-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Contenido del modal -->
            <div class="p-6">
                <!-- Descripción -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                        Descripción
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        {{ selectedProduct.description }}
                    </p>
                </div>

                <!-- Ingredientes -->
                <div v-if="selectedProduct.ingredients.length > 0" class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">
                        Ingredientes
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div
                            v-for="ingredient in selectedProduct.ingredients"
                            :key="ingredient.id"
                            class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-700 rounded"
                        >
                            <span class="text-gray-900 dark:text-gray-100">{{ ingredient.name }}</span>
                            <span v-if="ingredient.quantity" class="text-sm text-gray-500 dark:text-gray-400">
                                {{ ingredient.quantity }} {{ ingredient.unit }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Alérgenos -->
                <div v-if="selectedProduct.allergens.length > 0" class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">
                        <i class="pi pi-exclamation-triangle text-orange-500 mr-2"></i>
                        Contiene Alérgenos
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <div
                            v-for="allergen in selectedProduct.allergens"
                            :key="allergen.id"
                            class="flex items-center p-3 bg-orange-50 dark:bg-orange-900/30 border border-orange-200 dark:border-orange-700 rounded-lg"
                        >
                            <span class="text-2xl mr-3">{{ getAllergenIcon(allergen) }}</span>
                            <div>
                                <p class="font-semibold text-orange-800 dark:text-orange-200">
                                    {{ allergen.name }}
                                </p>
                                <p v-if="allergen.description" class="text-xs text-orange-600 dark:text-orange-300">
                                    {{ allergen.description }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advertencia si no tiene alérgenos -->
                <div v-else class="mb-6">
                    <div class="p-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-lg">
                        <p class="text-green-800 dark:text-green-200 font-semibold">
                            <i class="pi pi-check-circle mr-2"></i>
                            Este plato no contiene alérgenos conocidos
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.max-h-90vh {
    max-height: 90vh;
}
</style>
