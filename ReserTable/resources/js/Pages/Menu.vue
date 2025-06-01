<script setup>
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import Menu from '@/Components/Menu.vue';
import ThemeManager from '@/Components/ThemeManager.vue';
import { usePage, Head } from '@inertiajs/vue3';

const products = usePage().props.products || [];
</script>

<template>
    <Head title="Nuestro Menú - El Sabor Auténtico" />
    <ThemeManager />
    <div class="d-flex flex-column min-vh-100 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 transition-colors duration-300">
        <Header />
        <main class="container flex-grow-1 mx-auto py-12 px-4">
            <!-- Header de página -->
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-primary dark:text-blue-400 mb-3">
                    <i class="pi pi-book me-2"></i>
                    Nuestro Menú
                </h1>
                <p class="lead text-gray-600 dark:text-gray-300 mb-4">
                    Explora nuestra selección de platos tradicionales preparados con ingredientes frescos y de la mejor calidad
                </p>
            </div>
            
            <!-- Alerta informativa -->
            <div class="alert alert-info dark:bg-blue-900 dark:border-blue-700 dark:text-blue-100 text-center mb-5 border-start border-5 border-info">
                <i class="pi pi-info-circle me-2"></i>
                <strong>¡Novedad!</strong> Ahora puedes añadir productos a tu pedido y confirmarlo online para una experiencia más rápida.
            </div>
            
            <Menu :items="products" />
        </main>
        <Footer />
    </div>
</template>

<style scoped>
/* Mejoras para el alert en modo oscuro */
.alert-info.dark\:bg-blue-900 {
    background-color: rgb(30, 58, 138);
    border-color: rgb(29, 78, 216);
    color: rgb(219, 234, 254);
}

.dark .alert-info {
    background-color: rgb(30, 58, 138) !important;
    border-color: rgb(29, 78, 216) !important;
    color: rgb(219, 234, 254) !important;
}
</style>
