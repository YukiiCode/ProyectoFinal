<script setup>
import { ref, computed } from 'vue';
import { Modal } from 'bootstrap';
import DiscountCouponValidator from '@/Components/DiscountCouponValidator.vue';

const props = defineProps({
    items: {
        type: Array,
        default: () => []
    }
});

const cart = ref([]);
const orderSuccess = ref(false);
const orderError = ref('');
const appliedCoupon = ref(null);
const discount = ref(0);

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
    return cart.value.reduce((sum, item) => sum + (parseFloat((item.price+'').replace('€','')) * item.qty), 0).toLocaleString('es-ES', { style: 'currency', currency: 'EUR' });
});

const totalWithDiscount = computed(() => {
    const subtotal = cart.value.reduce((sum, item) => sum + (parseFloat((item.price+'').replace('€','')) * item.qty), 0);
    const finalTotal = subtotal - discount.value;
    return Math.max(0, finalTotal).toLocaleString('es-ES', { style: 'currency', currency: 'EUR' });
});

const onCouponValidated = (coupon) => {
    if (coupon) {
        appliedCoupon.value = coupon;
        
        // Calcular descuento basado en el total actual
        const subtotal = cart.value.reduce((sum, item) => sum + (parseFloat((item.price+'').replace('€','')) * item.qty), 0);
        
        if (coupon.discount_type === 'percentage') {
            discount.value = subtotal * (coupon.value / 100);
        } else {
            discount.value = coupon.value;
        }
    } else {
        appliedCoupon.value = null;
        discount.value = 0;
    }
};

const removeCoupon = () => {
    appliedCoupon.value = null;
    discount.value = 0;
};
async function confirmOrder() {
    orderSuccess.value = false;
    orderError.value = '';
    try {
        const orderData = { 
            items: cart.value,
            discount_coupon_id: appliedCoupon.value?.id || null
        };
        await axios.post('/api/orders', orderData);
        orderSuccess.value = true;
        cart.value = [];
        appliedCoupon.value = null;
        discount.value = 0;
    } catch (e) {
        orderError.value = e.response?.data?.message || 'Error al realizar el pedido.';
    }
}
</script>

<template>
    <section class="container my-5" id="menu">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold text-primary dark:text-blue-400 mb-3 transition-colors">
                <i class="pi pi-utensils me-2"></i>
                Nuestro Menú
            </h2>
            <p class="lead text-gray-600 dark:text-gray-300">
                Cada plato es una experiencia única preparada con amor y dedicación
            </p>
        </div>
        
        <div v-if="!items.length" class="text-center my-5">
            <div class="d-flex justify-content-center align-items-center">
                <span class="spinner-border text-primary me-3" role="status" aria-hidden="true"></span>
                <span class="text-gray-600 dark:text-gray-300">Cargando menú...</span>
            </div>
        </div>
        <div v-else class="row g-4">
            <div v-for="item in items" :key="item.name" class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm hover:shadow-lg dark:bg-gray-800 dark:border-gray-700 transition-all duration-300 menu-item-card">
                    <div class="position-relative overflow-hidden">
                        <img :src="item.image || 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=400&q=80'" 
                             class="card-img-top menu-item-image" 
                             :alt="item.name" 
                             style="object-fit:cover; height:200px;">
                        <div class="card-img-overlay d-flex align-items-end justify-content-end p-2">
                            <span class="badge bg-success fs-6 shadow">Disponible</span>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column dark:text-gray-100">
                        <h5 class="card-title fw-bold text-dark dark:text-gray-100 mb-2">{{ item.name }}</h5>
                        <p class="card-text flex-grow-1 text-gray-600 dark:text-gray-300 small">{{ item.description }}</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="fw-bold text-success fs-5">
                                <i class="pi pi-euro me-1"></i>
                                {{ parseFloat(item.price).toFixed(2) }}
                            </span>
                            <button class="btn btn-primary btn-sm hover:bg-primary-dark transition-all px-3" @click="addToCart(item)">
                                <i class="pi pi-plus me-1"></i>
                                Añadir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Botón del carrito flotante -->
        <div class="fixed-bottom d-flex justify-content-end p-4" v-if="cart.length > 0">
            <button class="btn btn-primary btn-lg shadow-lg cart-floating-btn" data-bs-toggle="modal" data-bs-target="#cartModal">
                <i class="pi pi-shopping-cart me-2"></i>
                Ver Pedido ({{ cart.length }})
                <span class="badge bg-light text-primary ms-2">{{ totalPrice }}</span>
            </button>
        </div>
        
        <!-- Modal Carrito de Pedido -->
        <div class="modal fade" id="cartModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content dark:bg-gray-800 dark:border-gray-700">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="pi pi-shopping-cart me-2"></i>
                            Tu Pedido
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body dark:text-gray-100">
                        <div v-if="cart.length === 0" class="text-center text-muted dark:text-gray-400 py-5">
                            <i class="pi pi-shopping-cart fs-1 mb-3 d-block text-gray-400"></i>
                            <p class="mb-0">El carrito está vacío.</p>
                            <small>Añade algunos platos de nuestro menú</small>
                        </div>
                        <div v-else>
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <h6 class="text-primary dark:text-blue-400">Productos seleccionados</h6>
                                </div>
                                <div class="col-md-4 text-end">
                                    <h6 class="text-primary dark:text-blue-400">Cantidad y precio</h6>
                                </div>
                            </div>
                            <div class="cart-items" style="max-height: 400px; overflow-y: auto;">
                                <div v-for="(item, idx) in cart" :key="item.name" class="cart-item border-bottom dark:border-gray-600 pb-3 mb-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-center">
                                                <img :src="item.image || 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=100&q=80'" 
                                                     class="rounded me-3" 
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-1 text-dark dark:text-gray-100">{{ item.name }}</h6>
                                                    <small class="text-muted dark:text-gray-400">€{{ parseFloat(item.price).toFixed(2) }} por unidad</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center justify-content-end">
                                                <div class="btn-group me-3" role="group">
                                                    <button class="btn btn-outline-secondary btn-sm dark:border-gray-600 dark:text-gray-300" @click="decrement(idx)">
                                                        <i class="pi pi-minus"></i>
                                                    </button>
                                                    <span class="btn btn-outline-secondary btn-sm dark:border-gray-600 dark:text-gray-300 border-start-0 border-end-0">{{ item.qty }}</span>
                                                    <button class="btn btn-outline-secondary btn-sm dark:border-gray-600 dark:text-gray-300" @click="increment(idx)">
                                                        <i class="pi pi-plus"></i>
                                                    </button>
                                                </div>
                                                <button class="btn btn-outline-danger btn-sm" @click="removeFromCart(idx)">
                                                    <i class="pi pi-trash"></i>
                                                </button>
                                            </div>
                                            <div class="text-end mt-2">
                                                <span class="fw-bold text-success">€{{ (parseFloat(item.price) * item.qty).toFixed(2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>                        </div>
                        
                        <!-- Validador de Cupones de Descuento -->
                        <div v-if="cart.length > 0" class="border-top dark:border-gray-600 pt-3 mt-3">
                            <h6 class="text-primary dark:text-blue-400 mb-3">
                                <i class="pi pi-tag me-2"></i>
                                Código de Descuento
                            </h6>
                            <DiscountCouponValidator 
                                @coupon-validated="onCouponValidated"
                                :show-header="false"
                                class="mb-3"
                            />
                            
                            <!-- Cupón aplicado -->
                            <div v-if="appliedCoupon" class="alert alert-info">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="pi pi-check-circle me-2"></i>
                                        <strong>{{ appliedCoupon.code }}</strong> aplicado
                                        <br>
                                        <small>
                                            Descuento: 
                                            <span v-if="appliedCoupon.discount_type === 'percentage'">
                                                {{ appliedCoupon.value }}%
                                            </span>
                                            <span v-else>
                                                €{{ appliedCoupon.value }}
                                            </span>
                                        </small>
                                    </div>
                                    <button 
                                        type="button" 
                                        class="btn btn-sm btn-outline-secondary"
                                        @click="removeCoupon"
                                    >
                                        <i class="pi pi-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div v-if="cart.length > 0" class="border-top dark:border-gray-600 pt-3 mt-3">
                            <div class="row">
                                <div class="col text-end">
                                    <!-- Subtotal -->
                                    <div v-if="appliedCoupon" class="mb-2">
                                        <span class="text-muted">Subtotal: {{ totalPrice }}</span>
                                    </div>
                                    <!-- Descuento -->
                                    <div v-if="appliedCoupon && discount > 0" class="mb-2 text-success">
                                        <span>Descuento: -€{{ discount.toFixed(2) }}</span>
                                    </div>
                                    <!-- Total final -->
                                    <h5 class="mb-0">
                                        <strong>Total: <span class="text-success">{{ appliedCoupon ? totalWithDiscount : totalPrice }}</span></strong>
                                    </h5>
                                </div>
                            </div>
                        </div>
                        
                        <div v-if="orderSuccess" class="alert alert-success mt-3">
                            <i class="pi pi-check-circle me-2"></i>
                            ¡Pedido realizado con éxito!
                        </div>
                        <div v-if="orderError" class="alert alert-danger mt-3">
                            <i class="pi pi-exclamation-triangle me-2"></i>
                            {{ orderError }}
                        </div>
                    </div>
                    <div class="modal-footer dark:border-gray-600">
                        <button type="button" class="btn btn-secondary dark:bg-gray-600 dark:border-gray-600" data-bs-dismiss="modal">
                            <i class="pi pi-times me-1"></i>
                            Cerrar
                        </button>
                        <button v-if="cart.length > 0" type="button" class="btn btn-success" @click="confirmOrder">
                            <i class="pi pi-check me-1"></i>
                            Confirmar Pedido
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.menu-item-card {
    border-radius: 15px;
    overflow: hidden;
}

.menu-item-card:hover .menu-item-image {
    transform: scale(1.05);
}

.menu-item-image {
    transition: transform 0.3s ease;
}

.cart-floating-btn {
    border-radius: 50px;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(13, 110, 253, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(13, 110, 253, 0);
    }
}

.cart-item:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}

/* Modo oscuro específico */
.dark .modal-content {
    background-color: rgb(31, 41, 55) !important;
    border-color: rgb(75, 85, 99) !important;
}

.dark .btn-outline-secondary {
    border-color: rgb(75, 85, 99) !important;
    color: rgb(209, 213, 219) !important;
}

.dark .btn-outline-secondary:hover {
    background-color: rgb(75, 85, 99) !important;
    color: white !important;
}
</style>
