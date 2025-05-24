<script setup>
import Menu from '@/Components/Menu.vue';

const menuItems = [
    { name: 'Lasaña Clásica', description: 'Capas de pasta fresca, ragú de ternera, bechamel y queso parmesano.', price: '€14.50', image: 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=400&q=80' },
    { name: 'Pizza Margherita', description: 'Salsa de tomate San Marzano, mozzarella fresca, albahaca y aceite de oliva virgen extra.', price: '€12.00', image: 'https://images.unsplash.com/photo-1519864600265-abb23847ef2c?auto=format&fit=crop&w=400&q=80' },
    { name: 'Risotto ai Funghi Porcini', description: 'Arroz Carnaroli cremoso con setas porcini frescas y un toque de trufa.', price: '€16.00', image: 'https://images.unsplash.com/photo-1464306076886-debca5e8a6b0?auto=format&fit=crop&w=400&q=80' },
    { name: 'Tiramisú Casero', description: 'Bizcochos de soletilla bañados en café, crema de mascarpone y cacao.', price: '€7.50', image: 'https://images.unsplash.com/photo-1504674900247-ec6b0b1b798e?auto=format&fit=crop&w=400&q=80' },
    // Puedes añadir más platos aquí
];
</script>

<template>
    <Menu :items="menuItems" />
</template>
