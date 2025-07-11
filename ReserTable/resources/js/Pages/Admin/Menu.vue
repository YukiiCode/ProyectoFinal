<template>
    <AdminLayout>
        <div class="container-fluid">            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-dark">{{ t('admin.menu.title') }}</h1>
                    <p class="text-muted mb-0">{{ t('admin.menu.subtitle') }}</p>
                </div>
                <button class="btn btn-primary" @click="openCreateModal">
                    <i class="fas fa-plus me-2"></i>
                    {{ t('admin.menu.new_product') }}
                </button>
            </div>            <!-- Filters -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="form-label">{{ t('admin.menu.category') }}</label>
                    <Dropdown 
                        v-model="filters.category" 
                        :options="categoryOptions" 
                        optionLabel="label" 
                        optionValue="value" 
                        class="w-100"
                        :placeholder="t('admin.menu.all_categories')"
                        :showClear="true"
                        @change="applyFilters"
                    />
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ t('admin.menu.availability') }}</label>
                    <Dropdown 
                        v-model="filters.available" 
                        :options="availabilityOptions" 
                        optionLabel="label" 
                        optionValue="value" 
                        class="w-100"
                        :placeholder="t('admin.menu.all_statuses')"
                        :showClear="true"
                        @change="applyFilters"
                    />
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-outline-secondary w-100" @click="clearFilters">
                        <i class="fas fa-times me-2"></i>
                        {{ t('admin.menu.clear_filters') }}
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
                            >                                <Column field="image" :header="t('admin.menu.image')" style="width: 80px">
                                    <template #body="{ data }">
                                        <div class="product-image-container">                                            <img 
                                                :src="data.image_path || 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDMwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIzMDAiIGhlaWdodD0iMjAwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0xMjUgNjVIMTc1VjExNUgxMjVWNjVaIiBzdHJva2U9IiM5Q0EzQUYiIHN0cm9rZS13aWR0aD0iMiIgZmlsbD0ibm9uZSIvPgo8Y2lyY2xlIGN4PSIxNDAiIGN5PSI4MCIgcj0iNSIgZmlsbD0iIzlDQTNBRiIvPgo8cGF0aCBkPSJNMTMwIDEwMEwxNDUgODVMMTYwIDEwMEwxNzAgOTBWMTEwSDE0MFYxMDBIMTMwWiIgZmlsbD0iIzlDQTNBRiIvPgo8dGV4dCB4PSIxNTAiIHk9IjE0MCIgZm9udC1mYW1pbHk9IkFyaWFsLCBzYW5zLXNlcmlmIiBmb250LXNpemU9IjEyIiBmaWxsPSIjNkI3Mjg0IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5TaW4gSW1hZ2VuPC90ZXh0Pgo8L3N2Zz4K'" 
                                                :alt="data.name"
                                                class="product-image"
                                                @error="handleImageError"
                                            />
                                        </div>
                                    </template>
                                </Column>
                                <Column field="name" :header="t('common.name')" sortable>
                                    <template #body="{ data }">
                                        <div>
                                            <div class="fw-medium">{{ data.name }}</div>
                                            <small class="text-muted">{{ data.description ? data.description.substring(0, 60) + '...' : t('admin.menu.no_description') }}</small>
                                        </div>
                                    </template>
                                </Column>
                                <Column field="category" :header="t('admin.menu.category')" sortable>
                                    <template #body="{ data }">
                                        <span class="category-badge">{{ data.category }}</span>
                                    </template>
                                </Column>
                                <Column field="price" :header="t('admin.menu.price')" sortable>
                                    <template #body="{ data }">
                                        <span class="fw-bold text-success">€{{ parseFloat(data.price).toFixed(2) }}</span>
                                    </template>
                                </Column>
                                <Column field="available" :header="t('admin.menu.available')" sortable>
                                    <template #body="{ data }">
                                        <span class="status-badge" :class="data.available ? 'status-badge-success' : 'status-badge-danger'">
                                            {{ data.available ? t('admin.menu.available') : t('admin.menu.not_available') }}
                                        </span>
                                    </template>
                                </Column>
                                <Column field="created_at" :header="t('common.created')" sortable>
                                    <template #body="{ data }">
                                        {{ formatDate(data.created_at) }}
                                    </template>
                                </Column>
                                <Column :header="t('common.actions')" style="width: 120px">
                                    <template #body="{ data }">
                                        <div class="d-flex gap-1">                                            <Button 
                                                icon="pi pi-pencil" 
                                                class="p-button-rounded p-button-info p-button-sm" 
                                                @click="editProduct(data)"
                                                :title="t('common.edit')"
                                                style="width: 36px; height: 36px;"
                                            />
                                            <Button 
                                                :icon="data.available ? 'pi pi-eye-slash' : 'pi pi-eye'" 
                                                :class="data.available ? 'p-button-warning' : 'p-button-success'"
                                                class="p-button-rounded p-button-sm" 
                                                @click="toggleAvailability(data)"
                                                :title="data.available ? t('admin.menu.hide') : t('admin.menu.show')"
                                                style="width: 36px; height: 36px;"
                                            />
                                            <Button 
                                                icon="pi pi-trash" 
                                                class="p-button-rounded p-button-danger p-button-sm" 
                                                @click="deleteProduct(data)"
                                                :title="t('common.delete')"
                                                style="width: 36px; height: 36px;"
                                            />
                                        </div>
                                    </template>
                                </Column>
                            </DataTable>
                        </div>
                    </div>
                </div>
            </div>            <!-- Create/Edit Modal -->
            <Dialog 
                v-model:visible="showModal" 
                :header="editingProduct ? t('admin.menu.edit_product') : t('admin.menu.new_product')" 
                :modal="true" 
                :closable="true" 
                :style="{ width: '700px' }"
            >
                <form @submit.prevent="submitProduct" class="row g-3">
                    <div class="col-12">
                        <label class="form-label">{{ t('admin.menu.product_name') }}</label>
                        <InputText 
                            v-model="form.name" 
                            class="w-100" 
                            required 
                            :placeholder="t('admin.menu.product_name_placeholder')"
                        />
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ t('admin.menu.description') }}</label>
                        <Textarea 
                            v-model="form.description" 
                            class="w-100" 
                            rows="3"
                            :placeholder="t('admin.menu.description_placeholder')"
                        />
                    </div>
                    <div class="col-6">
                        <label class="form-label">{{ t('admin.menu.category') }}</label>
                        <Dropdown 
                            v-model="form.category" 
                            :options="categoryOptions" 
                            optionLabel="label" 
                            optionValue="value" 
                            class="w-100" 
                            :placeholder="t('admin.menu.select_category')"
                        />
                    </div>
                    <div class="col-6">
                        <label class="form-label">{{ t('admin.menu.price_euro') }}</label>
                        <InputText 
                            v-model="form.price" 
                            class="w-100" 
                            type="number"
                            step="0.01"
                            required 
                            :min="0"
                            placeholder="0.00"
                        />
                    </div>                    <div class="col-12">
                        <label class="form-label">{{ t('admin.menu.image') }}</label>
                        
                        <!-- Vista previa de imagen actual -->
                        <div v-if="form.image_path || imagePreview" class="mb-3">
                            <div class="image-preview-container">
                                <img 
                                    :src="imagePreview || form.image_path" 
                                    :alt="form.name || 'Preview'"
                                    class="image-preview"
                                />
                                <button 
                                    type="button" 
                                    class="btn btn-sm btn-danger remove-image-btn"
                                    @click="removeImage"
                                    :title="t('common.remove')"
                                >
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Opciones de imagen -->
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label small">{{ t('admin.menu.upload_image') }}</label>
                                <input 
                                    type="file" 
                                    class="form-control form-control-sm" 
                                    accept="image/*"
                                    @change="handleImageUpload"
                                    ref="fileInput"
                                />
                                <small class="text-muted">{{ t('admin.menu.max_size_2mb') }}</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">{{ t('admin.menu.or_image_url') }}</label>
                                <InputText 
                                    v-model="form.image_path" 
                                    class="w-100" 
                                    size="small"
                                    :placeholder="t('admin.menu.image_url_placeholder')"
                                    @input="handleUrlInput"
                                />
                            </div>
                        </div>
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
                                {{ t('admin.menu.product_available') }}
                            </label>
                        </div>
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                        <Button 
                            :label="t('common.cancel')" 
                            icon="pi pi-times" 
                            class="p-button-text" 
                            @click="closeModal" 
                            type="button" 
                        />
                        <Button 
                            :label="editingProduct ? t('common.update') : t('common.create')" 
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
import { useI18n } from 'vue-i18n'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Dropdown from 'primevue/dropdown'
import Textarea from 'primevue/textarea'

const { t } = useI18n()

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
    image: null, // Para archivos subidos
    available: true,
})

const imagePreview = ref(null)
const fileInput = ref(null)

const categoryOptions = computed(() => [
    { label: t('admin.menu.categories.appetizers'), value: 'entrantes' },
    { label: t('admin.menu.categories.main_courses'), value: 'principales' },
    { label: t('admin.menu.categories.desserts'), value: 'postres' },
    { label: t('admin.menu.categories.beverages'), value: 'bebidas' },
    { label: t('admin.menu.categories.wines'), value: 'vinos' },
    { label: t('admin.menu.categories.coffees'), value: 'cafes' }
])

const availabilityOptions = computed(() => [
    { label: t('admin.menu.available'), value: true },
    { label: t('admin.menu.not_available'), value: false }
])

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
    imagePreview.value = null
    if (fileInput.value) {
        fileInput.value.value = ''
    }
    showModal.value = true
}

const editProduct = (product) => {
    editingProduct.value = product
    form.name = product.name
    form.description = product.description || ''
    form.category = product.category
    form.price = product.price
    form.image_path = product.image_path || ''
    form.image = null
    form.available = product.available
    imagePreview.value = null
    if (fileInput.value) {
        fileInput.value.value = ''
    }
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    editingProduct.value = null
    form.reset()
    form.available = true
    imagePreview.value = null
    if (fileInput.value) {
        fileInput.value.value = ''
    }
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
    if (confirm(t('admin.menu.confirm_delete_product'))) {
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
    // Evitar bucle infinito - solo intentar una vez
    if (event.target.dataset.errorHandled) return
    event.target.dataset.errorHandled = 'true'
    
    // Usar placeholder SVG inline
    event.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDMwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIzMDAiIGhlaWdodD0iMjAwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0xMjUgNjVIMTc1VjExNUgxMjVWNjVaIiBzdHJva2U9IiM5Q0EzQUYiIHN0cm9rZS13aWR0aD0iMiIgZmlsbD0ibm9uZSIvPgo8Y2lyY2xlIGN4PSIxNDAiIGN5PSI4MCIgcj0iNSIgZmlsbD0iIzlDQTNBRiIvPgo8cGF0aCBkPSJNMTMwIDEwMEwxNDUgODVMMTYwIDEwMEwxNzAgOTBWMTEwSDE0MFYxMDBIMTMwWiIgZmlsbD0iIzlDQTNBRiIvPgo8dGV4dCB4PSIxNTAiIHk9IjE0MCIgZm9udC1mYW1pbHk9IkFyaWFsLCBzYW5zLXNlcmlmIiBmb250LXNpemU9IjEyIiBmaWxsPSIjNkI3Mjg0IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5TaW4gSW1hZ2VuPC90ZXh0Pgo8L3N2Zz4K'
}

const handleImageUpload = (event) => {
    const file = event.target.files[0]
    if (file) {
        // Validar tamaño (2MB máximo)
        if (file.size > 2 * 1024 * 1024) {
            alert(t('admin.menu.image_too_large'))
            event.target.value = ''
            return
        }

        // Validar tipo de archivo
        if (!file.type.match(/^image\/(jpeg|jpg|png|gif|webp)$/)) {
            alert(t('admin.menu.invalid_image_type'))
            event.target.value = ''
            return
        }

        form.image = file
        form.image_path = '' // Limpiar URL si se sube un archivo

        // Crear preview
        const reader = new FileReader()
        reader.onload = (e) => {
            imagePreview.value = e.target.result
        }
        reader.readAsDataURL(file)
    }
}

const handleUrlInput = () => {
    if (form.image_path) {
        form.image = null // Limpiar archivo si se ingresa URL
        imagePreview.value = null // Limpiar preview
        if (fileInput.value) {
            fileInput.value.value = '' // Limpiar input de archivo
        }
    }
}

const removeImage = () => {
    form.image = null
    form.image_path = ''
    imagePreview.value = null
    if (fileInput.value) {
        fileInput.value.value = ''
    }
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

.image-preview-container {
    position: relative;
    display: inline-block;
    border-radius: 8px;
    overflow: hidden;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
}

.image-preview {
    width: 150px;
    height: 100px;
    object-fit: cover;
    display: block;
}

.remove-image-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    opacity: 0.8;
    transition: opacity 0.2s;
}

.remove-image-btn:hover {
    opacity: 1;
}

.image-preview-container:hover .remove-image-btn {
    opacity: 1;
}
</style>
