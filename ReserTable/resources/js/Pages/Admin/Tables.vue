<template>
    <AdminLayout>
        <div class="container-fluid">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-dark">Gestión de Mesas</h1>
                    <p class="text-muted mb-0">Administra las mesas del restaurante</p>
                </div>
                <div class="d-flex gap-2">
                    <Button 
                        :label="activeView === 'table' ? 'Ver Mapa' : 'Ver Tabla'"
                        :icon="activeView === 'table' ? 'pi pi-map' : 'pi pi-table'"
                        class="p-button-outlined"
                        @click="toggleView"
                    />
                    <button class="btn btn-primary" @click="openCreateModal">
                        <i class="fas fa-plus me-2"></i>
                        Nueva Mesa
                    </button>
                </div>
            </div>

            <!-- View Toggle Tabs -->
            <div class="mb-4">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <button 
                            class="nav-link"
                            :class="{ active: activeView === 'table' }"
                            @click="activeView = 'table'"
                        >
                            <i class="pi pi-table me-2"></i>
                            Vista de Tabla
                        </button>
                    </li>
                    <li class="nav-item">
                        <button 
                            class="nav-link"
                            :class="{ active: activeView === 'map' }"
                            @click="activeView = 'map'"
                        >
                            <i class="pi pi-map me-2"></i>
                            Mapa Interactivo
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Vista de Tabla -->
            <div v-if="activeView === 'table'" class="row">
                <div class="col-12">
                    <div class="admin-card">
                        <div class="admin-card-body">
                            <DataTable 
                                :value="tables" 
                                responsive-layout="scroll" 
                                striped-rows 
                                paginator 
                                :rows="10" 
                                :rows-per-page-options="[5,10,20]"
                                class="p-datatable-sm"
                            >
                                <Column field="table_number" header="Número de Mesa" sortable>
                                    <template #body="{ data }">
                                        <span class="fw-bold">Mesa {{ data.table_number }}</span>
                                    </template>
                                </Column>
                                <Column field="capacity" header="Capacidad" sortable>
                                    <template #body="{ data }">
                                        <span>{{ data.capacity }} personas</span>
                                    </template>
                                </Column>
                                <Column field="status" header="Estado" sortable>
                                    <template #body="{ data }">
                                        <span class="status-badge" :class="getStatusClass(data.status)">
                                            {{ getStatusText(data.status) }}
                                        </span>
                                    </template>
                                </Column>
                                <Column field="position" header="Posición">
                                    <template #body="{ data }">
                                        <small class="text-muted">X: {{ data.position_x }}, Y: {{ data.position_y }}</small>
                                    </template>
                                </Column>
                                <Column field="created_at" header="Creada" sortable>
                                    <template #body="{ data }">
                                        {{ formatDate(data.created_at) }}
                                    </template>
                                </Column>
                                <Column header="Acciones">
                                    <template #body="{ data }">
                                        <div class="d-flex gap-2">                                            <Button 
                                                icon="pi pi-pencil" 
                                                class="p-button-rounded p-button-info p-button-sm" 
                                                @click="editTable(data)"
                                                title="Editar" 
                                            />
                                            <Button 
                                                icon="pi pi-trash" 
                                                class="p-button-rounded p-button-danger p-button-sm" 
                                                @click="deleteTable(data)"
                                                title="Eliminar" 
                                            />
                                        </div>
                                    </template>
                                </Column>                            </DataTable>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vista de Mapa -->
            <div v-if="activeView === 'map'" class="row">
                <div class="col-12">
                    <div class="admin-card">
                        <div class="admin-card-body p-4">
                            <AdminTableMap 
                                :tables="tables"
                                @table-created="handleTableCreated"
                                @table-updated="handleTableUpdated"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Create/Edit Modal -->
            <Dialog 
                v-model:visible="showModal" 
                :header="editingTable ? 'Editar Mesa' : 'Nueva Mesa'" 
                :modal="true" 
                :closable="true" 
                :style="{ width: '500px' }"
            >
                <form @submit.prevent="submitTable" class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Número de Mesa</label>
                        <InputText 
                            v-model="form.table_number" 
                            class="w-100" 
                            type="number"
                            required 
                            :min="1"
                        />
                    </div>
                    <div class="col-12">
                        <label class="form-label">Capacidad</label>
                        <InputText 
                            v-model="form.capacity" 
                            class="w-100" 
                            type="number"
                            required 
                            :min="1"
                            :max="20"
                        />
                    </div>
                    <div class="col-12">
                        <label class="form-label">Estado</label>
                        <Dropdown 
                            v-model="form.status" 
                            :options="statusOptions" 
                            optionLabel="label" 
                            optionValue="value" 
                            class="w-100" 
                        />
                    </div>
                    <div class="col-6">
                        <label class="form-label">Posición X</label>
                        <InputText 
                            v-model="form.position_x" 
                            class="w-100" 
                            type="number"
                            :min="0"
                        />
                    </div>
                    <div class="col-6">
                        <label class="form-label">Posición Y</label>
                        <InputText 
                            v-model="form.position_y" 
                            class="w-100" 
                            type="number"
                            :min="0"
                        />
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
                            :label="editingTable ? 'Actualizar' : 'Crear'" 
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
import { ref } from 'vue'
import { useForm, router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import AdminTableMap from '@/Components/AdminTableMap.vue'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Dropdown from 'primevue/dropdown'

const props = defineProps({
    tables: Array
})

const tables = ref(props.tables || [])
const showModal = ref(false)
const editingTable = ref(null)
const activeView = ref('map') // Cambiar por defecto a vista de mapa

const form = useForm({
    table_number: '',
    capacity: '',
    status: 'available',
    position_x: 0,
    position_y: 0,
})

const statusOptions = [
    { label: 'Disponible', value: 'available' },
    { label: 'Reservada', value: 'reserved' },
    { label: 'Ocupada', value: 'occupied' }
]

const openCreateModal = () => {
    editingTable.value = null
    form.reset()
    form.status = 'available'
    showModal.value = true
}

const editTable = (table) => {
    editingTable.value = table
    form.table_number = table.table_number
    form.capacity = table.capacity
    form.status = table.status
    form.position_x = table.position_x
    form.position_y = table.position_y
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    editingTable.value = null
    form.reset()
    form.status = 'available'
}

const submitTable = () => {
    if (editingTable.value) {
        form.put(route('admin.tables.update', editingTable.value.id), {
            onSuccess: () => {
                closeModal()
                // Refresh the page or update tables list
                router.get(route('admin.tables'))
            }
        })
    } else {
        form.post(route('admin.tables.store'), {
            onSuccess: () => {
                closeModal()
                // Refresh the page or update tables list
                router.get(route('admin.tables'))
            }
        })
    }
}

const deleteTable = (table) => {
    if (confirm('¿Estás seguro de que quieres eliminar esta mesa?')) {
        router.delete(route('admin.tables.destroy', table.id), {
            onSuccess: () => {
                // Refresh the page or update tables list
                router.get(route('admin.tables'))
            }
        })
    }
}

// Alternar entre vistas
const toggleView = () => {
    activeView.value = activeView.value === 'table' ? 'map' : 'table'
}

// Manejar eventos del mapa
const handleTableCreated = () => {
    // Refrescar datos después de crear mesa desde el mapa
    router.get(route('admin.tables'), {}, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            tables.value = usePage().props.tables || []
        }
    })
}

const handleTableUpdated = () => {
    // Refrescar datos después de actualizar mesa desde el mapa
    router.get(route('admin.tables'), {}, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            tables.value = usePage().props.tables || []
        }
    })
}

const getStatusClass = (status) => {
    const classes = {
        'available': 'status-badge-success',
        'reserved': 'status-badge-warning',
        'occupied': 'status-badge-danger'
    }
    return classes[status] || 'status-badge-secondary'
}

const getStatusText = (status) => {
    const texts = {
        'available': 'Disponible',
        'reserved': 'Reservada',
        'occupied': 'Ocupada'
    }
    return texts[status] || status
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}
</script>

<style scoped>
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

.status-badge-warning {
    background-color: #fff3cd;
    color: #664d03;
}

.status-badge-danger {
    background-color: #f8d7da;
    color: #721c24;
}

.status-badge-secondary {
    background-color: #e2e3e5;
    color: #41464b;
}
</style>
