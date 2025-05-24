<script setup>
import { Link, usePage } from '@inertiajs/vue3';
const page = usePage();
</script>

<template>
    <section id="features" class="mb-16">
        <h2 class="text-3xl font-semibold mb-8 text-center text-red-700 dark:text-red-500">Explora Nuestros Servicios</h2>
        <div class="grid md:grid-cols-3 gap-8 text-center">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Reservas Online</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">Asegura tu mesa de forma rápida y sencilla a través de nuestro sistema de reservas.</p>
                <Link :href="route('reserva')" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition">
                    Reservar Ahora
                </Link>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Códigos de Descuento</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">Regístrate y accede a promociones exclusivas para nuestros clientes fieles.</p>
                <Link href="#" class="bg-yellow-500 text-gray-900 px-4 py-2 rounded-md hover:bg-yellow-600 transition">
                    Ver Descuentos
                </Link>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Tu Perfil de Cliente</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">Gestiona tus reservas, preferencias y accede a tu historial.</p>
                <Link v-if="page.props.auth.user" :href="route('dashboard')" class="bg-gray-700 text-white px-4 py-2 rounded-md hover:bg-gray-800 transition">
                    Ir a Mi Perfil
                </Link>
                <Link v-else :href="route('login')" class="bg-gray-700 text-white px-4 py-2 rounded-md hover:bg-gray-800 transition">
                    Iniciar Sesión
                </Link>
            </div>
        </div>
    </section>
</template>
