<script setup>
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import HomeAbout from '@/Components/Home/HomeAbout.vue';
import HomeFeatures from '@/Components/Home/HomeFeatures.vue';
import ThemeManager from '@/Components/ThemeManager.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});

const menuItems = [
    { name: 'Lasaña Clásica', description: 'Capas de pasta fresca, ragú de ternera, bechamel y queso parmesano.', price: '€14.50', image: 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=400&q=80' },
    { name: 'Pizza Margherita', description: 'Salsa de tomate San Marzano, mozzarella fresca, albahaca y aceite de oliva virgen extra.', price: '€12.00' },
    { name: 'Risotto ai Funghi Porcini', description: 'Arroz Carnaroli cremoso con setas porcini frescas y un toque de trufa.', price: '€16.00', image: 'https://images.unsplash.com/photo-1464306076886-debca5e8a6b0?auto=format&fit=crop&w=400&q=80' },
];
</script>

<template>
    <Head :title="t('welcome.title')" />
    <ThemeManager />
    <div class="d-flex flex-column min-vh-100 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 transition-colors duration-300">
        <Header />
        <main class="container flex-grow-1 mx-auto py-12 px-4">
            <!-- Hero Section -->
            <section class="text-center mb-12 py-5">
                <div class="hero-content">
                    <h1 class="display-4 fw-bold mb-4 text-primary dark:text-blue-400 transition-colors">
                        {{ t('welcome.title') }}
                    </h1>
                    <p class="lead mb-5 text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                        {{ t('welcome.subtitle') }}
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                        <Link :href="route('reserva')" class="btn btn-primary btn-lg px-5 py-3 shadow-lg hover:shadow-xl transition-all">
                            <i class="pi pi-calendar-plus me-2"></i>
                            {{ t('welcome.reserve_now') }}
                        </Link>
                        <Link :href="route('menu')" class="btn btn-outline-primary btn-lg px-5 py-3 hover:shadow-lg transition-all">
                            <i class="pi pi-book me-2"></i>
                            {{ t('welcome.view_menu') }}
                        </Link>
                    </div>
                </div>
            </section>
            
            <HomeAbout />
            
            <!-- Resumen del menú -->
            <section class="my-12" id="menu-resumen">
                <div class="text-center mb-8">
                    <h2 class="text-3xl fw-bold mb-3 text-red-700 dark:text-red-400 transition-colors">
                        <i class="pi pi-star me-2"></i>
                        {{ t('welcome.featured_menu') }}
                    </h2>
                    <p class="lead text-gray-600 dark:text-gray-300">
                        {{ t('welcome.featured_menu_description') }}
                    </p>
                </div>
                
                <div class="row g-4 justify-content-center">
                    <div v-for="item in menuItems" :key="item.name" class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm hover:shadow-lg dark:bg-gray-800 dark:border-gray-700 transition-all duration-300 menu-card">
                            <div class="position-relative overflow-hidden">
                                <img :src="item.image" class="card-img-top" :alt="item.name" style="object-fit:cover; height:200px; transition: transform 0.3s ease;">
                                <div class="card-img-overlay d-flex align-items-end p-0">
                                    <div class="w-100 bg-gradient-to-t from-black/70 to-transparent p-3">
                                        <span class="badge bg-primary text-white">{{ t('welcome.featured') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column dark:text-gray-100">
                                <h5 class="card-title fw-bold text-dark dark:text-gray-100">{{ item.name }}</h5>
                                <p class="card-text flex-grow-1 text-gray-600 dark:text-gray-300 small">{{ item.description }}</p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="fw-bold text-success fs-5">{{ item.price }}</span>
                                    <Link :href="route('menu')" class="btn btn-outline-primary btn-sm hover:bg-primary hover:text-white transition-all">
                                        <i class="pi pi-eye me-1"></i>
                                        {{ t('welcome.view_more') }}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-5">
                    <Link :href="route('menu')" class="btn btn-outline-primary btn-lg px-5 py-3 hover:bg-primary hover:text-white transition-all">
                        <i class="pi pi-book me-2"></i>
                        {{ t('welcome.view_complete_menu') }}
                        <i class="pi pi-arrow-right ms-2"></i>
                    </Link>
                </div>
            </section>
            
            <HomeFeatures />
        </main>
        <Footer />
    </div>
</template>

<style scoped>
/* Estilos generales */
body {
  margin: 0;
  font-family: 'Nunito', sans-serif;
  background-color: #f8fafc;
  color: #333;
}

a {
  text-decoration: none;
}

h1, h2, h3, h4, h5, h6 {
  margin: 0;
  padding: 0;
  font-weight: 600;
}

p {
  margin: 0;
  padding: 0;
}

/* Estilos para el layout */
.d-flex {
  display: flex !important;
}

.flex-column {
  flex-direction: column !important;
}

.min-vh-100 {
  min-height: 100vh !important;
}

.bg-gray-100 {
  background-color: #f7fafc !important;
}

.dark .bg-gray-900 {
  background-color: #1a202c !important;
}

.text-gray-800 {
  color: #2d3748 !important;
}

.dark .text-gray-200 {
  color: #edf2f4 !important;
}

/* Estilos para el header */
header {
  background-color: #fff;
  padding: 1rem 0;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.logo {
  font-size: 1.5rem;
  font-weight: 700;
  color: #333;
}

.nav-link {
  margin: 0 1rem;
  color: #333;
  transition: color 0.3s;
}

.nav-link:hover {
  color: #007bff;
}

/* Estilos para el footer */
footer {
  background-color: #333;
  color: #fff;
  padding: 2rem 0;
  text-align: center;
}

.footer-link {
  color: #fff;
  transition: color 0.3s;
}

.footer-link:hover {
  color: #007bff;
}

/* Estilos para las secciones */
section {
  margin: 2rem 0;
}

.text-center {
  text-align: center;
}

.display-4 {
  font-size: 2.5rem;
}

.fw-bold {
  font-weight: 700;
}

.lead {
  font-size: 1.25rem;
  font-weight: 300;
}

.card {
  border: none;
  border-radius: 0.5rem;
  overflow: hidden;
}

.card-img-top {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.card-body {
  padding: 1.5rem;
}

.btn-primary {
  background-color: #007bff;
  border-color: #007bff;
}

.btn-primary:hover {
  background-color: #0056b3;
  border-color: #0056b3;
}

.btn-outline-primary {
  color: #007bff;
  border-color: #007bff;
}

.btn-outline-primary:hover {
  background-color: #007bff;
  color: #fff;
}

/* Media queries */
@media (max-width: 768px) {
  .display-4 {
    font-size: 2rem;
  }

  .lead {
    font-size: 1.125rem;
  }
}
</style>