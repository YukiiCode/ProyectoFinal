<script setup>
import { Link } from '@inertiajs/vue3';
defineProps({
    menuItems: Array
});
</script>

<template>
    <section id="menu" class="mb-16">
        <h2 class="text-3xl font-semibold mb-8 text-center text-red-700 dark:text-red-500">Nuestro Menú Estrella</h2>
        <div class="grid md:grid-cols-2 lg:grid-cols-2 gap-8">
            <div v-for="item in menuItems" :key="item.name" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ item.name }}</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-3">{{ item.description }}</p>
                <p class="text-lg font-semibold text-yellow-600 dark:text-yellow-400">{{ item.price }}</p>
            </div>
        </div>
        <div class="text-center mt-8">
            <Link href="#" class="text-red-600 dark:text-red-400 hover:underline font-semibold">
                Ver Menú Completo (Próximamente)
            </Link>
        </div>
    </section>
</template>
