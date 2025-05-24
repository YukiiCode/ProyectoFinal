<script setup>
import HomeHead from '@/Components/Home/HomeHead.vue';
import HomeNavbar from '@/Components/Home/HomeNavbar.vue';
import HomeAbout from '@/Components/Home/HomeAbout.vue';
import HomeFeatures from '@/Components/Home/HomeFeatures.vue';
import HomeFooter from '@/Components/Home/HomeFooter.vue';
import { ref, onMounted } from 'vue';
import TableMap from '@/Components/TableMap.vue';
import Menu from '@/Components/Menu.vue';
import { Link } from '@inertiajs/vue3';

const menuItems = [
    { name: 'Lasaña Clásica', description: 'Capas de pasta fresca, ragú de ternera, bechamel y queso parmesano.', price: '€14.50', image: 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=400&q=80' },
    { name: 'Pizza Margherita', description: 'Salsa de tomate San Marzano, mozzarella fresca, albahaca y aceite de oliva virgen extra.', price: '€12.00' },
    { name: 'Risotto ai Funghi Porcini', description: 'Arroz Carnaroli cremoso con setas porcini frescas y un toque de trufa.', price: '€16.00', image: 'https://images.unsplash.com/photo-1464306076886-debca5e8a6b0?auto=format&fit=crop&w=400&q=80' },
    { name: 'Tiramisú Casero', description: 'Bizcochos de soletilla bañados en café, crema de mascarpone y cacao.', price: '€7.50', image: 'https://images.unsplash.com/photo-1504674900247-ec6b0b1b798e?auto=format&fit=crop&w=400&q=80' },
];

const showTableMap = ref(false);

onMounted(() => {
    // Opcional: inicializar modales Bootstrap si lo necesitas
});
</script>

<template>
    <div class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 min-h-screen">
        <HomeHead />
        <HomeNavbar />

        <main class="container mx-auto py-12 px-4">
            <HomeAbout />

            <!-- Resumen del menú en el Home -->
            <section class="my-12" id="menu-resumen">
                <h2 class="text-3xl font-semibold mb-8 text-center text-red-700 dark:text-red-500">Nuestro Menú Destacado</h2>
                <div class="row g-4 justify-content-center">
                    <div v-for="item in menuItems.slice(0,3)" :key="item.name" class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm">
                            <img :src="item.image" class="card-img-top" :alt="item.name" style="object-fit:cover; height:200px;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ item.name }}</h5>
                                <p class="card-text flex-grow-1">{{ item.description }}</p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="fw-bold text-success">{{ item.price }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <Link :href="route('menu')" class="btn btn-outline-primary btn-lg">
                        Ver Menú Completo
                    </Link>
                </div>
            </section>

            <HomeFeatures />
        </main>
        <HomeFooter />
    </div>

    <div class="text-center my-5">
        <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#tableMapModal">
            Reservar Mesa en el Mapa Interactivo
        </button>
    </div>

    <!-- Modal con el mapa de mesas -->
    <div class="modal fade" id="tableMapModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Mapa Interactivo de Mesas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <TableMap />
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.h-96 {
    height: 24rem;
}
</style>
