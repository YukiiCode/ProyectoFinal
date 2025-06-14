<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import GlobalToast from '@/Components/GlobalToast.vue'
import ModernNavbar from '@/Components/ModernNavbar.vue'

const { t } = useI18n()
const page = usePage()

// Props del controlador
const featuredProducts = computed(() => page.props.featuredProducts || [])
</script>

<template>
    <Head title="ReserTable - Reservas de Restaurante" />
      <div class="min-h-screen bg-white dark:bg-gray-900 transition-colors duration-300">
        <!-- Modern Navigation -->
        <ModernNavbar current-page="home" />

        <!-- Hero Section -->
        <section class="relative overflow-hidden">
            <!-- Background with gradient and pattern -->
            <div class="absolute inset-0 bg-gradient-to-br from-red-50 via-orange-50 to-yellow-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900"></div>
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZGVmcz48cGF0dGVybiBpZD0iZ3JpZCIgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiBwYXR0ZXJuVW5pdHM9InVzZXJTcGFjZU9uVXNlIj48cGF0aCBkPSJNIDQwIDAgTCAwIDAgMCA0MCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjZjMxMjYwIiBzdHJva2Utd2lkdGg9IjEiIG9wYWNpdHk9IjAuMDUiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-30"></div>
            
            <div class="relative container mx-auto px-4 py-24 lg:py-32">
                <div class="max-w-4xl mx-auto text-center">
                    <!-- Main heading -->
                    <h1 class="text-5xl md:text-7xl font-bold mb-8 leading-tight">
                        <span class="bg-gradient-to-r from-gray-900 via-red-600 to-red-500 dark:from-white dark:via-red-400 dark:to-red-300 bg-clip-text text-transparent">
                            {{ t('welcome.title') }}
                        </span>
                    </h1>
                    
                    <!-- Subtitle -->
                    <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-300 mb-12 max-w-3xl mx-auto leading-relaxed">
                        {{ t('welcome.subtitle') }}
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                        <Link href="/reservas" 
                              class="group bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-bold py-4 px-8 rounded-full transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center space-x-2">
                            <span>📅</span>
                            <span>{{ t('welcome.reserve_now') }}</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </Link>
                        <Link href="/menu" 
                              class="group border-2 border-gray-300 dark:border-gray-600 hover:border-red-500 dark:hover:border-red-400 text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 font-bold py-4 px-8 rounded-full transition-all duration-300 flex items-center space-x-2">
                            <span>📖</span>
                            <span>{{ t('welcome.view_menu') }}</span>
                        </Link>
                    </div>
                </div>
            </div>        </section>        <!-- Featured Menu Section -->
        <section class="py-16 bg-gradient-to-br from-gray-50 via-white to-red-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 relative overflow-hidden">
            <!-- Animated background elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-red-400/20 to-orange-400/20 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-br from-yellow-400/20 to-red-400/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-gradient-to-br from-orange-400/10 to-red-400/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 4s;"></div>
            </div>
            
            <div class="container mx-auto px-4 relative">
                <!-- Section Header -->
                <div class="text-center mb-12">
                    <div class="inline-flex items-center space-x-3 bg-gradient-to-r from-red-100 to-orange-100 dark:from-red-900/30 dark:to-orange-900/30 backdrop-blur-sm text-red-600 dark:text-red-400 px-4 py-2 rounded-full text-sm font-semibold mb-6 shadow-lg border border-red-200/50 dark:border-red-700/50">
                        <span class="text-lg">🍽️</span>
                        <span class="uppercase tracking-wide">{{ t('welcome.featured') }}</span>
                    </div>
                    <h2 class="text-3xl md:text-5xl font-black mb-6 leading-tight">
                        <span class="bg-gradient-to-r from-gray-900 via-red-600 to-red-500 dark:from-white dark:via-red-400 dark:to-red-300 bg-clip-text text-transparent">
                            {{ t('welcome.featured_menu') }}
                        </span>
                    </h2>
                    <p class="text-lg md:text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto leading-relaxed">
                        {{ t('welcome.featured_menu_description') }}
                    </p>
                </div>                <!-- Main Featured Product Display -->
                <div v-if="featuredProducts.length > 0" class="mb-16">
                    <div class="relative">
                        <!-- Main featured card -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700 relative">
                            <!-- Gradient overlay -->
                            <div class="absolute inset-0 bg-gradient-to-br from-red-50/50 via-transparent to-orange-50/50 dark:from-red-900/20 dark:via-transparent dark:to-orange-900/20"></div>
                            
                            <div class="relative grid lg:grid-cols-2 gap-0">
                                <!-- Product Information -->
                                <div class="p-8 lg:p-12 flex flex-col justify-center">
                                    <!-- Featured badge -->
                                    <div class="inline-flex items-center space-x-2 bg-gradient-to-r from-red-600 to-red-500 text-white px-3 py-1.5 rounded-xl text-sm font-bold mb-6 w-fit shadow-lg">
                                        <span class="text-base">⭐</span>
                                        <span class="uppercase tracking-wide">{{ t('welcome.chef_special') }}</span>
                                    </div>
                                    
                                    <!-- Product name -->
                                    <h3 class="text-2xl lg:text-3xl font-black text-gray-900 dark:text-white mb-4 leading-tight">
                                        {{ featuredProducts[0].name }}
                                    </h3>
                                    
                                    <!-- Product description -->
                                    <p class="text-base text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                                        {{ featuredProducts[0].description }}
                                    </p>
                                    
                                    <!-- Price and details -->
                                    <div class="flex items-center justify-between mb-6">
                                        <div class="flex items-center space-x-3">
                                            <div class="text-3xl font-black text-red-600 dark:text-red-400">
                                                €{{ featuredProducts[0].price }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-semibold">
                                                {{ featuredProducts[0].category }}
                                            </div>
                                        </div>
                                    </div>
                                      <!-- Allergens -->
                                    <div v-if="featuredProducts[0].allergens && featuredProducts[0].allergens.length > 0" class="flex items-center space-x-3">
                                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">{{ t('common.allergens') }}:</span>
                                        <div class="flex space-x-1">
                                            <div v-for="allergen in featuredProducts[0].allergens.slice(0, 4)" :key="allergen.id"
                                                 class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900/50 rounded-lg flex items-center justify-center shadow-sm border border-yellow-200 dark:border-yellow-700/50"
                                                 :title="allergen.name">
                                                <span class="text-sm">{{ allergen.icon }}</span>
                                            </div>
                                            <div v-if="featuredProducts[0].allergens.length > 4" 
                                                 class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300 shadow-sm">
                                                +{{ featuredProducts[0].allergens.length - 4 }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Visual/Image Section -->
                                <div class="relative bg-gradient-to-br from-red-100 via-orange-100 to-yellow-100 dark:from-red-900/30 dark:via-orange-900/30 dark:to-yellow-900/30 min-h-[400px] lg:min-h-full flex items-center justify-center overflow-hidden">
                                    <!-- Large decorative emoji -->
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="text-[12rem] opacity-20 select-none">🍝</div>
                                    </div>
                                    
                                    <!-- Floating ingredient elements -->
                                    <div class="absolute inset-0">
                                        <div class="absolute top-16 right-16 w-20 h-20 bg-white dark:bg-gray-700 rounded-2xl shadow-xl flex items-center justify-center animate-bounce" style="animation-delay: 0s;">
                                            <span class="text-3xl">�</span>
                                        </div>
                                        <div class="absolute top-32 left-16 w-16 h-16 bg-white dark:bg-gray-700 rounded-2xl shadow-xl flex items-center justify-center animate-bounce" style="animation-delay: 1s;">
                                            <span class="text-2xl">🧄</span>
                                        </div>
                                        <div class="absolute bottom-32 right-20 w-18 h-18 bg-white dark:bg-gray-700 rounded-2xl shadow-xl flex items-center justify-center animate-bounce" style="animation-delay: 2s;">
                                            <span class="text-2xl">�</span>
                                        </div>
                                        <div class="absolute bottom-16 left-20 w-14 h-14 bg-white dark:bg-gray-700 rounded-2xl shadow-xl flex items-center justify-center animate-bounce" style="animation-delay: 0.5s;">
                                            <span class="text-xl">�</span>
                                        </div>
                                        <div class="absolute top-1/2 right-8 w-12 h-12 bg-white dark:bg-gray-700 rounded-2xl shadow-xl flex items-center justify-center animate-bounce" style="animation-delay: 1.5s;">
                                            <span class="text-lg">�️</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Gradient overlay for depth -->
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/10 via-transparent to-transparent"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                <!-- Additional Featured Products -->
                <div v-if="featuredProducts.length > 1" class="mb-16">
                    <div class="text-center mb-8">
                        <h3 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white mb-3">
                            {{ t('welcome.more_specialties') }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ t('welcome.more_specialties_description') }}
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="product in featuredProducts.slice(1, 4)" :key="product.id" 
                             class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-all duration-500 transform hover:-translate-y-1 border border-gray-100 dark:border-gray-700 overflow-hidden">
                            
                            <!-- Product image area -->
                            <div class="h-40 bg-gradient-to-br from-red-100 to-orange-100 dark:from-red-900/30 dark:to-orange-900/30 relative overflow-hidden">
                                <div class="absolute inset-0 flex items-center justify-center text-5xl opacity-30">
                                    🍽️
                                </div>
                                <div class="absolute top-3 right-3 bg-red-600 text-white px-2 py-1 rounded-lg text-sm font-bold">
                                    €{{ product.price }}
                                </div>
                            </div>
                            
                            <!-- Product info -->
                            <div class="p-5">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors">
                                        {{ product.name }}
                                    </h4>
                                </div>
                                
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-3 line-clamp-2">
                                    {{ product.description }}
                                </p>
                                
                                <!-- Category -->
                                <div class="inline-flex items-center px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-xs font-medium mb-3">
                                    {{ product.category }}
                                </div>
                                  <!-- Allergens -->
                                <div v-if="product.allergens && product.allergens.length > 0" class="flex items-center space-x-2">
                                    <div class="flex space-x-1">
                                        <span v-for="allergen in product.allergens.slice(0, 3)" :key="allergen.id"
                                              class="w-5 h-5 bg-yellow-100 dark:bg-yellow-900/50 rounded-md flex items-center justify-center text-xs"
                                              :title="allergen.name">
                                            {{ allergen.icon }}
                                        </span>
                                        <span v-if="product.allergens.length > 3" 
                                              class="w-5 h-5 bg-gray-100 dark:bg-gray-700 rounded-md flex items-center justify-center text-xs font-bold text-gray-500 dark:text-gray-400">
                                            +{{ product.allergens.length - 3 }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                <!-- Final CTA -->
                <div class="text-center pt-8 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-6">
                        {{ t('welcome.explore_complete_menu_text') }}
                    </p>
                    <Link href="/menu" 
                          class="group inline-flex items-center space-x-4 bg-gradient-to-r from-red-600 via-red-500 to-orange-500 hover:from-red-700 hover:via-red-600 hover:to-orange-600 text-white font-bold py-5 px-10 rounded-2xl transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:scale-105 text-lg">
                        <span>🍽️</span>
                        <span>{{ t('welcome.view_complete_menu') }}</span>
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center group-hover:rotate-45 transition-transform duration-300">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </Link>
                </div>
            </div>
        </section>        <!-- Features Section -->
        <section class="py-8 bg-white dark:bg-gray-800">
            <div class="container mx-auto px-4">
                <div class="text-center mb-6">
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        {{ t('welcome.why_choose_us') }}
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ t('welcome.why_choose_us_description') }}
                    </p>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-3xl mx-auto">
                    <div class="text-center group">
                        <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                            <span class="text-white text-xl">⚡</span>
                        </div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">
                            {{ t('welcome.features.fast_service.title') }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 text-xs leading-relaxed">
                            {{ t('welcome.features.fast_service.description') }}
                        </p>
                    </div>
                    <div class="text-center group">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                            <span class="text-white text-xl">👨‍🍳</span>
                        </div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">
                            {{ t('welcome.features.quality_food.title') }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 text-xs leading-relaxed">
                            {{ t('welcome.features.quality_food.description') }}
                        </p>
                    </div>
                    <div class="text-center group">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                            <span class="text-white text-xl">🏆</span>
                        </div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">
                            {{ t('welcome.features.great_atmosphere.title') }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 text-xs leading-relaxed">
                            {{ t('welcome.features.great_atmosphere.description') }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-24 bg-gradient-to-br from-red-600 via-red-700 to-red-800 text-white relative overflow-hidden">
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="relative container mx-auto px-4 text-center">
                <h2 class="text-4xl md:text-6xl font-bold mb-8">
                    {{ t('welcome.cta.title') }}
                </h2>
                <p class="text-xl md:text-2xl mb-12 max-w-3xl mx-auto text-red-100">
                    {{ t('welcome.cta.description') }}
                </p>
                <Link href="/reservas" 
                      class="inline-flex items-center space-x-3 bg-white text-red-600 hover:bg-red-50 font-bold py-6 px-10 rounded-full text-xl transition-all duration-300 transform hover:scale-105 shadow-2xl">
                    <span>🍽️</span>
                    <span>{{ t('welcome.cta.button') }}</span>
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </Link>
            </div>
        </section>        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12">
            <div class="container mx-auto px-4 text-center">
                <div class="mb-8">
                    <div class="flex items-center justify-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold">R</span>
                        </div>
                        <span class="text-3xl font-bold text-white">ReserTable</span>
                    </div>
                    <p class="text-gray-400 max-w-md mx-auto">
                        {{ t('welcome.footer.tagline') }}
                    </p>
                </div>
                <div class="text-gray-500 text-sm">
                    © 2025 ReserTable. {{ t('welcome.footer.rights') }}
                </div>
            </div>
        </footer>
    </div>

    <!-- Toast Global para notificaciones -->
    <GlobalToast />
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Animación de flotación personalizada */
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

/* Efectos de glassmorphism */
.glass {
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.18);
}

/* Gradiente personalizado para texto */
.gradient-text {
    background: linear-gradient(45deg, #ef4444, #f97316, #eab308);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Animaciones de hover mejoradas */
.hover-lift {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.hover-lift:hover {
    transform: translateY(-8px);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

/* Animación de rotación suave */
.rotate-hover {
    transition: transform 0.3s ease-in-out;
}

.group:hover .rotate-hover {
    transform: rotate(45deg);
}

/* Efectos de pulso personalizados */
@keyframes custom-pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.8;
        transform: scale(1.05);
    }
}

.animate-custom-pulse {
    animation: custom-pulse 3s ease-in-out infinite;
}

/* Mejoras para elementos flotantes */
.floating-ingredient {
    animation: float 4s ease-in-out infinite;
}

.floating-ingredient:nth-child(2) {
    animation-delay: 0.5s;
}

.floating-ingredient:nth-child(3) {
    animation-delay: 1s;
}

.floating-ingredient:nth-child(4) {
    animation-delay: 1.5s;
}

.floating-ingredient:nth-child(5) {
    animation-delay: 2s;
}

/* Transiciones suaves para modo oscuro */
* {
    transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
}

/* Sombras mejoradas */
.shadow-3xl {
    box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.25);
}

/* Bordes con gradiente */
.border-gradient {
    border: 2px solid;
    border-image: linear-gradient(45deg, #ef4444, #f97316) 1;
}

/* Efectos de resplandor */
.glow-red {
    box-shadow: 0 0 20px rgba(239, 68, 68, 0.3);
}

.glow-red:hover {
    box-shadow: 0 0 30px rgba(239, 68, 68, 0.5);
}
</style>
