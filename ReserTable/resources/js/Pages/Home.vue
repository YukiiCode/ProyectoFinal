<script setup>
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import HomeAbout from '@/Components/Home/HomeAbout.vue';
import HomeFeatures from '@/Components/Home/HomeFeatures.vue';
import { ref, onMounted } from 'vue';
import TableMap from '@/Components/TableMap.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const showTableMap = ref(false);
const products = usePage().props.products || [];

onMounted(() => {
    // Opcional: inicializar modales Bootstrap si lo necesitas
});
</script>

<template>
    <div class="d-flex flex-column min-vh-100 bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
        <Header />
        <main class="container flex-grow-1 mx-auto py-12 px-4">
            <HomeAbout />

            <!-- Resumen del menú en el Home -->
            <section class="my-12" id="menu-resumen">
                <h2 class="text-3xl font-semibold mb-8 text-center text-red-700 dark:text-red-500">{{ t('welcome.featured_menu') }}</h2>
                <div class="row g-4 justify-content-center">
                    <div v-if="products.length === 0" class="text-center text-muted">No hay productos en el menú.</div>
                    <div v-else v-for="item in products.slice(0,3)" :key="item.name" class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm">
                            <img :src="item.image || 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=400&q=80'" class="card-img-top" :alt="item.name" style="object-fit:cover; height:200px;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ item.name }}</h5>
                                <p class="card-text flex-grow-1">{{ item.description }}</p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="fw-bold text-success">€{{ parseFloat(item.price).toFixed(2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                <div class="text-center mt-4">
                    <Link :href="route('menu')" class="btn btn-outline-primary btn-lg">
                        {{ t('welcome.view_complete_menu') }}
                    </Link>
                </div>
            </section>

            <HomeFeatures />
        </main>
        <div class="text-center my-5">
            <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#tableMapModal">
                {{ t('home.hero.cta_reserve') }}
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
        <Footer />
    </div>
</template>

<style scoped>
.h-96 {
    height: 24rem;
}
</style>
