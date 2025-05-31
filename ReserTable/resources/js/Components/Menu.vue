<script setup>
import { ref, computed } from 'vue';
import { Modal } from 'bootstrap';

const props = defineProps({
    items: {
        type: Array,
        default: () => []
    }
});

const cart = ref([]);
const orderSuccess = ref(false);
const orderError = ref('');

function addToCart(item) {
    const idx = cart.value.findIndex(i => i.name === item.name);
    if (idx !== -1) {
        cart.value[idx].qty++;
    } else {
        cart.value.push({ ...item, qty: 1 });
    }
}
function increment(idx) {
    cart.value[idx].qty++;
}
function decrement(idx) {
    if (cart.value[idx].qty > 1) cart.value[idx].qty--;
}
function removeFromCart(idx) {
    cart.value.splice(idx, 1);
}
const totalPrice = computed(() => {
    return cart.value.reduce((sum, item) => sum + parseFloat((item.price+'').replace('€','')), 0).toLocaleString('es-ES', { style: 'currency', currency: 'EUR' });
});
async function confirmOrder() {
    orderSuccess.value = false;
    orderError.value = '';
    try {
        await axios.post('/api/orders', { items: cart.value });
        orderSuccess.value = true;
        cart.value = [];
    } catch (e) {
        orderError.value = e.response?.data?.message || 'Error al realizar el pedido.';
    }
}
</script>

<template>
    <section class="container my-5" id="menu">
        <h2 class="text-center mb-4 display-5 fw-bold text-primary">Nuestro Menú</h2>
        <div v-if="!items.length" class="text-center my-5">
            <span class="spinner-border"></span> Cargando menú...
        </div>
        <div v-else class="row g-4">
            <div v-for="item in items" :key="item.name" class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <img :src="item.image || 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=400&q=80'" class="card-img-top" :alt="item.name" style="object-fit:cover; height:200px;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ item.name }}</h5>
                        <p class="card-text flex-grow-1">{{ item.description }}</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="fw-bold text-success">€{{ parseFloat(item.price).toFixed(2) }}</span>
                            <button class="btn btn-primary btn-sm" @click="addToCart(item)">Añadir al pedido</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Carrito de Pedido -->
        <div class="modal fade" id="cartModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Tu Pedido</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="cart.length === 0" class="text-center text-muted">El carrito está vacío.</div>
                        <ul v-else class="list-group mb-3">
                            <li v-for="(item, idx) in cart" :key="item.name" class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ item.name }}</strong>
                                    <div class="small text-muted">€{{ parseFloat(item.price).toFixed(2) }}</div>
                                </div>
                                <div>
                                    <button class="btn btn-outline-secondary btn-sm me-2" @click="decrement(idx)">-</button>
                                    <span>{{ item.qty }}</span>
                                    <button class="btn btn-outline-secondary btn-sm ms-2" @click="increment(idx)">+</button>
                                    <button class="btn btn-danger btn-sm ms-3" @click="removeFromCart(idx)">&times;</button>
                                </div>
                            </li>
                        </ul>
                        <div v-if="cart.length > 0" class="text-end fw-bold mb-2">
                            Total: {{ totalPrice }}
                        </div>
                        <div v-if="orderSuccess" class="alert alert-success">¡Pedido realizado con éxito!</div>
                        <div v-if="orderError" class="alert alert-danger">{{ orderError }}</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button v-if="cart.length > 0" type="button" class="btn btn-success" @click="confirmOrder">Confirmar Pedido</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-5">
            <button class="btn btn-outline-primary btn-lg" data-bs-toggle="modal" data-bs-target="#cartModal">
                Ver Pedido ({{ cart.length }})
            </button>
        </div>
    </section>
</template>
