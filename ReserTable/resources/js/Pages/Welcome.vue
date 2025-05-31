<script setup>
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import HomeAbout from '@/Components/Home/HomeAbout.vue';
import HomeFeatures from '@/Components/Home/HomeFeatures.vue';
import { Head, Link } from '@inertiajs/vue3';

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
    <Head title="Bienvenido a El Sabor Auténtico" />
    <div class="d-flex flex-column min-vh-100 bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
        <Header />
        <main class="container flex-grow-1 mx-auto py-12 px-4">
            <section class="text-center mb-12">
                <h1 class="display-4 fw-bold mb-3">Bienvenido a El Sabor Auténtico</h1>
                <p class="lead mb-4">Descubre una experiencia culinaria inolvidable donde cada bocado cuenta una historia.</p>
                <Link :href="route('reserva')" class="btn btn-primary btn-lg">¡Reserva tu Mesa Ahora!</Link>
            </section>
            <HomeAbout />
            <!-- Resumen del menú -->
            <section class="my-12" id="menu-resumen">
                <h2 class="text-3xl font-semibold mb-8 text-center text-red-700 dark:text-red-500">Nuestro Menú Destacado</h2>
                <div class="row g-4 justify-content-center">
                    <div v-for="item in menuItems" :key="item.name" class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm">
                            <img :src="item.image" class="card-img-top" :alt="item.name" style="object-fit:cover; height:200px;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ item.name }}</h5>
                                <p class="card-text flex-grow-1">{{ item.description }}</p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="fw-bold text-success">{{ item.price }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <Link :href="route('menu')" class="btn btn-outline-primary btn-lg">
                        Ver Menú Completo
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