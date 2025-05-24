<script setup>
import { Head } from '@inertiajs/vue3';
import TableMap from '@/Components/TableMap.vue';
</script>

<template>
    <Head title="Reserva" />
    <div class="container py-5">
        <h1 class="display-4 mb-4">Reserva tu mesa</h1>
        <p>Aquí podrás gestionar tus reservas.</p>
        <TableMap />
    </div>
</template>