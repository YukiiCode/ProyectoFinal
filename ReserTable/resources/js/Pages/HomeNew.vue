<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { ref, computed } from 'vue';
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import ThemeManager from '@/Components/ThemeManager.vue';

const { t } = useI18n();
const page = usePage();

// Props del controlador
const featuredProducts = computed(() => page.props.featuredProducts || []);
const categories = computed(() => page.props.categories || []);
const coupons = computed(() => page.props.coupons || []);
const stats = computed(() => page.props.stats || {});

// Referencias reactivas para modales
const showCouponsModal = ref(false);

// Formatear fecha para cupones
const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
};

// Formatear porcentaje de descuento
const formatDiscount = (coupon) => {
    if (coupon.discount_type === 'percentage') {
        return `${coupon.discount_value}%`;
    } else {
        return `€${coupon.discount_value}`;
    }
};
</script>

<template>
    <Head :title="t('home.title')" />
    <ThemeManager />
    
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
        <Header />
        
        <!-- Hero Section -->
        <section class="relative bg-gradient-to-br from-red-600 via-red-700 to-red-800 text-white overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: url('data:image/svg+xml;utf8,<svg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;><g fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;><g fill=&quot;%23ffffff&quot; fill-opacity=&quot;0.1&quot;><circle cx=&quot;9&quot; cy=&quot;9&quot; r=&quot;2&quot;/><circle cx=&quot;51&quot; cy=&quot;51&quot; r=&quot;2&quot;/><circle cx=&quot;51&quot; cy=&quot;9&quot; r=&quot;2&quot;/><circle cx=&quot;9&quot; cy=&quot;51&quot; r=&quot;2&quot;/></g></g></svg>')"></div>
            </div>
            
            <div class="relative container mx-auto px-4 py-20">
                <div class="text-center max-w-4xl mx-auto">
                    <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
                        {{ t('welcome.title') }}
                    </h1>
                    <p class="text-xl md:text-2xl mb-8 text-red-100 max-w-2xl mx-auto">
                        {{ t('welcome.subtitle') }}
                    </p>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <Link :href="route('reserva')" 
                              class="bg-yellow-400 hover:bg-yellow-300 text-gray-900 font-bold py-4 px-8 rounded-full transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="pi pi-calendar-plus mr-2"></i>
                            {{ t('welcome.reserve_now') }}
                        </Link>
                        <Link :href="route('menu')" 
                              class="bg-transparent border-2 border-white hover:bg-white hover:text-red-600 text-white font-bold py-4 px-8 rounded-full transition-all duration-300">
                            <i class="pi pi-book mr-2"></i>
                            {{ t('welcome.view_menu') }}
                        </Link>
                    </div>
                </div>
            </div>
            
            <!-- Wave separator -->
            <div class="absolute bottom-0 left-0 right-0">
                <svg viewBox="0 0 1200 120" preserveAspectRatio="none" class="relative block w-full h-16 fill-gray-50 dark:fill-gray-900">
                    <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z"></path>
                </svg>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-16 bg-gray-50 dark:bg-gray-900">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg">
                        <div class="text-3xl font-bold text-red-600 dark:text-red-400 mb-2">{{ stats.total_tables }}</div>
                        <div class="text-gray-600 dark:text-gray-300">Mesas Totales</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg">
                        <div class="text-3xl font-bold text-green-600 dark:text-green-400 mb-2">{{ stats.available_tables }}</div>
                        <div class="text-gray-600 dark:text-gray-300">Disponibles</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg">
                        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">{{ stats.total_products }}</div>
                        <div class="text-gray-600 dark:text-gray-300">Productos</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg">
                        <div class="text-3xl font-bold text-purple-600 dark:text-purple-400 mb-2">{{ stats.categories_count }}</div>
                        <div class="text-gray-600 dark:text-gray-300">Categorías</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Products -->
        <section class="py-20 bg-white dark:bg-gray-800">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        {{ t('welcome.featured_menu') }}
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                        {{ t('welcome.featured_menu_description') }}
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="product in featuredProducts.slice(0, 6)" :key="product.id"
                         class="bg-gray-50 dark:bg-gray-700 rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                        <div class="aspect-video bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center">
                            <i class="pi pi-utensils text-4xl text-white"></i>
                        </div>
                        <div class="p-6">
                            <div class="mb-3">
                                <span class="inline-block bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 text-sm px-3 py-1 rounded-full">
                                    {{ product.category }}
                                </span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ product.name }}</h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4 line-clamp-3">{{ product.description }}</p>
                            
                            <!-- Allergens -->
                            <div v-if="product.allergens && product.allergens.length > 0" class="mb-4">
                                <div class="flex flex-wrap gap-1">
                                    <span v-for="allergen in product.allergens.slice(0, 3)" :key="allergen.id"
                                          class="inline-block bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 text-xs px-2 py-1 rounded">
                                        {{ allergen.name }}
                                    </span>
                                    <span v-if="product.allergens.length > 3"
                                          class="inline-block bg-gray-100 dark:bg-gray-600 text-gray-600 dark:text-gray-300 text-xs px-2 py-1 rounded">
                                        +{{ product.allergens.length - 3 }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-2xl font-bold text-red-600 dark:text-red-400">
                                    €{{ parseFloat(product.price).toFixed(2) }}
                                </span>
                                <Link :href="route('menu')" 
                                      class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">
                                    Ver Menú
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-12">
                    <Link :href="route('menu')" 
                          class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white font-bold py-4 px-8 rounded-full transition-all duration-300 transform hover:scale-105">
                        <i class="pi pi-book mr-2"></i>
                        {{ t('welcome.view_complete_menu') }}
                        <i class="pi pi-arrow-right ml-2"></i>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Categories Section -->
        <section class="py-20 bg-gray-50 dark:bg-gray-900">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        Nuestras Categorías
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-300">
                        Explora nuestra variedad de deliciosos platos
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="category in categories" :key="category.id"
                         class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 text-center group">
                        <div class="w-16 h-16 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                            <i class="pi pi-tag text-2xl text-red-600 dark:text-red-400"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ category.name }}</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-3">{{ category.description }}</p>
                        <div class="text-sm text-red-600 dark:text-red-400 font-medium">
                            {{ category.products_count }} productos
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Discount Coupons Section -->
        <section v-if="coupons.length > 0" class="py-20 bg-red-600 dark:bg-red-800 text-white">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold mb-4">
                        ¡Ofertas Especiales!
                    </h2>
                    <p class="text-xl text-red-100">
                        Aprovecha nuestros descuentos disponibles
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="coupon in coupons.slice(0, 4)" :key="coupon.id"
                         class="bg-white text-gray-900 p-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-red-600 mb-2">
                                {{ formatDiscount(coupon) }}
                            </div>
                            <div class="text-sm uppercase tracking-wide text-gray-500 mb-3">
                                Descuento
                            </div>
                            <div class="bg-gray-100 p-3 rounded-lg mb-4">
                                <div class="text-lg font-mono font-bold text-gray-900">{{ coupon.code }}</div>
                            </div>
                            <p class="text-sm text-gray-600 mb-3">{{ coupon.description }}</p>
                            <div class="text-xs text-gray-500">
                                Expira: {{ formatDate(coupon.expires_at) }}
                            </div>
                            <div v-if="coupon.minimum_amount > 0" class="text-xs text-gray-500 mt-1">
                                Mínimo: €{{ coupon.minimum_amount }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-12">
                    <button @click="showCouponsModal = true" 
                            class="bg-yellow-400 hover:bg-yellow-300 text-gray-900 font-bold py-4 px-8 rounded-full transition-all duration-300">
                        <i class="pi pi-percentage mr-2"></i>
                        Ver Todos los Cupones
                    </button>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 bg-gray-900 text-white">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-4xl font-bold mb-4">
                    ¿Listo para Reservar?
                </h2>
                <p class="text-xl text-gray-300 mb-8 max-w-2xl mx-auto">
                    Reserva tu mesa ahora y disfruta de una experiencia gastronómica única
                </p>
                <Link :href="route('reserva')" 
                      class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white font-bold py-4 px-8 rounded-full transition-all duration-300 transform hover:scale-105">
                    <i class="pi pi-calendar-plus mr-2"></i>
                    Reservar Mesa
                </Link>
            </div>
        </section>

        <Footer />

        <!-- Coupons Modal -->
        <div v-if="showCouponsModal" @click="showCouponsModal = false"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div @click.stop class="bg-white dark:bg-gray-800 rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Todos los Cupones Disponibles
                        </h3>
                        <button @click="showCouponsModal = false" 
                                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            <i class="pi pi-times text-xl"></i>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div v-for="coupon in coupons" :key="coupon.id"
                             class="border border-gray-200 dark:border-gray-600 p-4 rounded-xl">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-red-600 dark:text-red-400 mb-2">
                                    {{ formatDiscount(coupon) }}
                                </div>
                                <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded-lg mb-3">
                                    <div class="font-mono font-bold text-gray-900 dark:text-white">{{ coupon.code }}</div>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">{{ coupon.description }}</p>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Expira: {{ formatDate(coupon.expires_at) }}
                                </div>
                                <div v-if="coupon.minimum_amount > 0" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Mínimo: €{{ coupon.minimum_amount }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
