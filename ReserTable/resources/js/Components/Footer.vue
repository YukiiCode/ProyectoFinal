<script setup>
import { usePage } from '@inertiajs/vue3';
const laravelVersion = usePage().props.laravelVersion ?? '';
const phpVersion = usePage().props.phpVersion ?? '';
</script>

<template>
    <footer class="bg-dark text-white py-4 mt-auto">
        <div class="container text-center">
            <p class="mb-1">&copy; {{ new Date().getFullYear() }} El Sabor Auténtico. Todos los derechos reservados.</p>
            <p class="small mb-1">Plataforma de reservas por ReserTable</p>
            <p class="small text-white-50" v-if="laravelVersion && phpVersion">
                Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
            </p>
            <slot name="extra" />
        </div>
    </footer>
</template>
