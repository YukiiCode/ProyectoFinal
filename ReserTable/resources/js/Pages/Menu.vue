<script setup>
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import Menu from '@/Components/Menu.vue';
import { usePage } from '@inertiajs/vue3';

const products = usePage().props.products || [];
</script>

<template>
    <div class="d-flex flex-column min-vh-100 bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
        <Header />
        <main class="container flex-grow-1 mx-auto py-12 px-4">
            <div class="alert alert-info text-center mb-4">
                ¡Ahora puedes añadir productos a tu pedido y confirmarlo online!
            </div>
            <Menu :items="products" />
        </main>
        <Footer />
    </div>
</template>
