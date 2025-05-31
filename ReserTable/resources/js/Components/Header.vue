<script setup>
import { Link, usePage } from '@inertiajs/vue3';

const user = usePage().props.auth?.user;
</script>

<template>
    <header class="bg-primary text-white py-3 shadow">
        <div class="container d-flex justify-content-between align-items-center">
            <Link :href="route('home')" class="fw-bold fs-4 text-white text-decoration-none">
                El Sabor Auténtico
            </Link>
            <nav>
                <slot>
                    <template v-if="user">
                        <Link :href="route('dashboard')" class="btn btn-outline-light btn-sm me-2">Mi Cuenta</Link>
                        <Link :href="route('logout')" method="post" as="button" class="btn btn-light btn-sm">Cerrar Sesión</Link>
                    </template>
                    <template v-else>
                        <Link :href="route('login')" class="btn btn-outline-light btn-sm me-2">Iniciar Sesión</Link>
                        <Link v-if="$page.props.canRegister" :href="route('register')" class="btn btn-light btn-sm">Registrarse</Link>
                    </template>
                </slot>
            </nav>
        </div>
    </header>
</template>
