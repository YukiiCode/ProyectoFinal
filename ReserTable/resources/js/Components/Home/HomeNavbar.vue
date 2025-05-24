<script setup>
import { Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
const page = usePage();
</script>

<template>
    <nav class="bg-red-700 p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <Link :href="route('home')" class="text-white text-2xl font-bold">La Trattoria</Link>
            <div>
                <Link :href="route('home')" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium">Inicio</Link>
                <Link :href="route('reserva')" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium">Reservar</Link>
                <Link :href="route('menu')" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium">Menú</Link>
                <Link v-if="page.props.auth.user" :href="route('dashboard')" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium">Mi Perfil</Link>
                <Link v-else :href="route('login')" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium">Login</Link>
                <Link v-if="!page.props.auth.user" :href="route('register')" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium">Registro</Link>
                <Link href="#" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium">Descuentos</Link>
            </div>
        </div>
    </nav>
</template>
