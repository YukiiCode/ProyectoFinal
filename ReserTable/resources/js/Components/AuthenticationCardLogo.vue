<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <Link :href="'/'" class="logo-link">
        <div class="logo-container">
            <!-- Ícono de ReserTable -->
            <div class="logo-icon">
                <svg
                    width="60"
                    height="60"
                    viewBox="0 0 60 60"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                    class="restaurant-icon"
                >
                    <!-- Mesa circular -->
                    <circle cx="30" cy="30" r="18" fill="currentColor" opacity="0.9"/>
                    <circle cx="30" cy="30" r="15" fill="none" stroke="currentColor" stroke-width="2"/>
                    
                    <!-- Sillas alrededor de la mesa -->
                    <rect x="28" y="8" width="4" height="8" rx="2" fill="currentColor"/>
                    <rect x="44" y="28" width="8" height="4" rx="2" fill="currentColor"/>
                    <rect x="28" y="44" width="4" height="8" rx="2" fill="currentColor"/>
                    <rect x="8" y="28" width="8" height="4" rx="2" fill="currentColor"/>
                    
                    <!-- Elementos decorativos (cubiertos) -->
                    <circle cx="30" cy="30" r="3" fill="currentColor" opacity="0.6"/>
                    <line x1="26" y1="26" x2="28" y2="28" stroke="currentColor" stroke-width="1.5" opacity="0.7"/>
                    <line x1="34" y1="26" x2="32" y2="28" stroke="currentColor" stroke-width="1.5" opacity="0.7"/>
                    <line x1="34" y1="34" x2="32" y2="32" stroke="currentColor" stroke-width="1.5" opacity="0.7"/>
                    <line x1="26" y1="34" x2="28" y2="32" stroke="currentColor" stroke-width="1.5" opacity="0.7"/>
                </svg>
            </div>
            
            <!-- Texto del logo -->
            <div class="logo-text">
                <h1 class="brand-name">ReserTable</h1>
                <p class="brand-tagline">Sistema de Reservas</p>
            </div>
        </div>
    </Link>
</template>