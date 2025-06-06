<template>
    <AdminLayout>
        <div class="container-fluid">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-dark">Gestión del Menú</h1>
                    <p class="text-muted mb-0">Administra los productos del menú</p>
                </div>
                <button class="btn btn-primary" @click="openCreateModal">
                    <i class="fas fa-plus me-2"></i>
                    Nuevo Producto
                </button>
            </div>

            <!-- Filters -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="form-label">Categoría</label>
                    <Dropdown 
                        v-model="filters.category" 
                        :options="categoryOptions" 
                        optionLabel="label" 
                        optionValue="value" 
                        class="w-100"
                        placeholder="Todas las categorías"
                        :showClear="true"
                        @change="applyFilters"
                    />
                </div>
                <div class="col-md-4">
                    <label class="form-label">Disponibilidad</label>
                    <Dropdown 
                        v-model="filters.available" 
                        :options="availabilityOptions" 
                        optionLabel="label" 
                        optionValue="value" 
                        class="w-100"
                        placeholder="Todos"
                        :showClear="true"
                        @change="applyFilters"
                    />
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-outline-secondary w-100" @click="clearFilters">
                        <i class="fas fa-times me-2"></i>
                        Limpiar Filtros
                    </button>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="row">
                <div class="col-12">
                    <div class="admin-card">
                        <div class="admin-card-body">
                            <DataTable 
                                :value="filteredProducts" 
                                responsive-layout="scroll" 
                                striped-rows 
                                paginator 
                                :rows="12" 
                                :rows-per-page-options="[8,12,20]"
                                class="p-datatable-sm"
                            >
                                <Column field="image" header="Imagen" style="width: 80px">
                                    <template #body="{ data }">
                                        <div class="product-image-container">
                                            <img 
                                                :src="data.image_path || '/images/placeholder-dish.jpg'" 
                                                :alt="data.name"
                                                class="product-image"
                                                @error="handleImageError"
                                            />
                                        </div>
                                    </template>
                                </Column>
                                <Column field="name" header="Nombre" sortable>
                                    <template #body="{ data }">
                                        <div>
                                            <div class="fw-medium">{{ data.name }}</div>
                                            <small class="text-muted">{{ data.description ? data.description.substring(0, 60) + '...' : 'Sin descripción' }}</small>
                                        </div>
                                    </template>
                                </Column>
                                <Column field="category" header="Categoría" sortable>
                                    <template #body="{ data }">
                                        <span class="category-badge">{{ data.category }}</span>
                                    </template>
                                </Column>
                                <Column field="price" header="Precio" sortable>
                                    <template #body="{ data }">
                                        <span class="fw-bold text-success">€{{ parseFloat(data.price).toFixed(2) }}</span>
                                    </template>
                                </Column>
                                <Column field="available" header="Disponible" sortable>
                                    <template #body="{ data }">
                                        <span class="status-badge" :class="data.available ? 'status-badge-success' : 'status-badge-danger'">
                                            {{ data.available ? 'Disponible' : 'No disponible' }}
                                        </span>
                                    </template>
                                </Column>
                                <Column field="created_at" header="Creado" sortable>
                                    <template #body="{ data }">
                                        {{ formatDate(data.created_at) }}
                                    </template>
                                </Column>                                <Column header="Acciones" style="width: 120px">
                                    <template #body="{ data }">
                                        <div class="d-flex gap-1">
                                            <Button 
                                                icon="pi pi-pencil" 
                                                class="p-button-rounded p-button-info p-button-sm" 
                                                @click="editProduct(data)"
                                                title="Editar"
                                                style="width: 36px; height: 36px;"
                                            />
                                            <Button 
                                                :icon="data.available ? 'pi pi-eye-slash' : 'pi pi-eye'" 
                                                :class="data.available ? 'p-button-warning' : 'p-button-success'"
                                                class="p-button-rounded p-button-sm" 
                                                @click="toggleAvailability(data)"
                                                :title="data.available ? 'Ocultar' : 'Mostrar'"
                                                style="width: 36px; height: 36px;"
                                            />
                                            <Button 
                                                icon="pi pi-trash" 
                                                class="p-button-rounded p-button-danger p-button-sm" 
                                                @click="deleteProduct(data)"
                                                title="Eliminar"
                                                style="width: 36px; height: 36px;"
                                            />
                                        </div>
                                    </template>
                                </Column>
                            </DataTable>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Create/Edit Modal -->
            <Dialog 
                v-model:visible="showModal" 
                :header="editingProduct ? 'Editar Producto' : 'Nuevo Producto'" 
                :modal="true" 
                :closable="true" 
                :style="{ width: '700px' }"
            >
                <form @submit.prevent="submitProduct" class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Nombre del Producto</label>
                        <InputText 
                            v-model="form.name" 
                            class="w-100" 
                            required 
                            placeholder="Nombre del plato o bebida"
                        />
                    </div>
                    <div class="col-12">
                        <label class="form-label">Descripción</label>
                        <Textarea 
                            v-model="form.description" 
                            class="w-100" 
                            rows="3"
                            placeholder="Descripción detallada del producto"
                        />
                    </div>
                    <div class="col-6">
                        <label class="form-label">Categoría</label>
                        <Dropdown 
                            v-model="form.category" 
                            :options="categoryOptions" 
                            optionLabel="label" 
                            optionValue="value" 
                            class="w-100" 
                            placeholder="Seleccionar categoría"
                        />
                    </div>
                    <div class="col-6">
                        <label class="form-label">Precio (€)</label>
                        <InputText 
                            v-model="form.price" 
                            class="w-100" 
                            type="number"
                            step="0.01"
                            required 
                            :min="0"
                            placeholder="0.00"
                        />
                    </div>
                    <div class="col-12">
                        <label class="form-label">URL de Imagen</label>
                        <InputText 
                            v-model="form.image_path" 
                            class="w-100" 
                            placeholder="https://ejemplo.com/imagen.jpg"
                        />
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                v-model="form.available" 
                                id="available"
                            >
                            <label class="form-check-label" for="available">
                                Producto disponible
                            </label>
                        </div>
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                        <Button 
                            label="Cancelar" 
                            icon="pi pi-times" 
                            class="p-button-text" 
                            @click="closeModal" 
                            type="button" 
                        />
                        <Button 
                            :label="editingProduct ? 'Actualizar' : 'Crear'" 
                            icon="pi pi-check" 
                            type="submit" 
                            :loading="form.processing"
                        />
                    </div>
                </form>
            </Dialog>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Dropdown from 'primevue/dropdown'
import Textarea from 'primevue/textarea'

const props = defineProps({
    products: Array
})

const products = ref(props.products || [])
const showModal = ref(false)
const editingProduct = ref(null)

const filters = ref({
    category: null,
    available: null
})

const form = useForm({
    name: '',
    description: '',
    category: '',
    price: '',
    image_path: '',
    available: true,
})

const categoryOptions = [
    { label: 'Entrantes', value: 'entrantes' },
    { label: 'Principales', value: 'principales' },
    { label: 'Postres', value: 'postres' },
    { label: 'Bebidas', value: 'bebidas' },
    { label: 'Vinos', value: 'vinos' },
    { label: 'Cafés', value: 'cafes' }
]

const availabilityOptions = [
    { label: 'Disponible', value: true },
    { label: 'No disponible', value: false }
]

const filteredProducts = computed(() => {
    let filtered = products.value

    if (filters.value.category) {
        filtered = filtered.filter(p => p.category === filters.value.category)
    }

    if (filters.value.available !== null) {
        filtered = filtered.filter(p => p.available === filters.value.available)
    }

    return filtered
})

const openCreateModal = () => {
    editingProduct.value = null
    form.reset()
    form.available = true
    showModal.value = true
}

const editProduct = (product) => {
    editingProduct.value = product
    form.name = product.name
    form.description = product.description || ''
    form.category = product.category
    form.price = product.price
    form.image_path = product.image_path || ''
    form.available = product.available
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    editingProduct.value = null
    form.reset()
    form.available = true
}

const submitProduct = () => {
    if (editingProduct.value) {
        form.put(route('admin.menu.update', editingProduct.value.id), {
            onSuccess: () => {
                closeModal()
                router.get(route('admin.menu'))
            }
        })
    } else {
        form.post(route('admin.menu.store'), {
            onSuccess: () => {
                closeModal()
                router.get(route('admin.menu'))
            }
        })
    }
}

const toggleAvailability = (product) => {
    router.patch(route('admin.menu.toggle', product.id), {
        available: !product.available
    }, {
        onSuccess: () => {
            router.get(route('admin.menu'))
        }
    })
}

const deleteProduct = (product) => {
    if (confirm('¿Estás seguro de que quieres eliminar este producto?')) {
        router.delete(route('admin.menu.destroy', product.id), {
            onSuccess: () => {
                router.get(route('admin.menu'))
            }
        })
    }
}

const applyFilters = () => {
    // Los filtros se aplican automáticamente por el computed
}

const clearFilters = () => {
    filters.value = {
        category: null,
        available: null
    }
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const handleImageError = (event) => {
    event.target.src = '/images/placeholder-dish.jpg'
}
</script>

<style scoped>
.product-image-container {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    overflow: hidden;
    background-color: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.category-badge {
    background-color: #e3f2fd;
    color: #1565c0;
    padding: 0.25rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
}

.status-badge-success {
    background-color: #d1e7dd;
    color: #0f5132;
}

.status-badge-danger {
    background-color: #f8d7da;
    color: #721c24;
}
</style>
