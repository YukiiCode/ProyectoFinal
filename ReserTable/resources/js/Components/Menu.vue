<script setup>
const props = defineProps({
    items: {
        type: Array,
        default: () => [
            {
                name: 'Lasaña Clásica',
                description: 'Capas de pasta fresca, ragú de ternera, bechamel y queso parmesano.',
                price: '€14.50',
                image: 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=400&q=80'
            },
            {
                name: 'Pizza Margherita',
                description: 'Salsa de tomate San Marzano, mozzarella fresca, albahaca y aceite de oliva virgen extra.',
                price: '€12.00',
                image: 'https://images.unsplash.com/photo-1519864600265-abb23847ef2c?auto=format&fit=crop&w=400&q=80'
            },
            {
                name: 'Risotto ai Funghi Porcini',
                description: 'Arroz Carnaroli cremoso con setas porcini frescas y un toque de trufa.',
                price: '€16.00',
                image: 'https://images.unsplash.com/photo-1464306076886-debca5e8a6b0?auto=format&fit=crop&w=400&q=80'
            },
            {
                name: 'Tiramisú Casero',
                description: 'Bizcochos de soletilla bañados en café, crema de mascarpone y cacao.',
                price: '€7.50',
                image: 'https://images.unsplash.com/photo-1504674900247-ec6b0b1b798e?auto=format&fit=crop&w=400&q=80'
            }
        ]
    }
});
</script>

<template>
    <section class="container my-5" id="menu">
        <h2 class="text-center mb-4 display-5 fw-bold text-primary">Nuestro Menú</h2>
        <div class="row g-4">
            <div v-for="item in items" :key="item.name" class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <img :src="item.image" class="card-img-top" :alt="item.name" style="object-fit:cover; height:200px;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ item.name }}</h5>
                        <p class="card-text flex-grow-1">{{ item.description }}</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="fw-bold text-success">{{ item.price }}</span>
                            <button class="btn btn-outline-primary btn-sm" disabled>Próximamente pedido online</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
