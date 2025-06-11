<template>
    <AdminLayout>
        <div class="container-fluid">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-dark">{{ t('admin.tables.title') }}</h1>
                    <p class="text-muted mb-0">{{ t('admin.tables.subtitle') }}</p>
                </div>
                <div class="d-flex gap-2">
                    <Button 
                        :label="activeView === 'table' ? t('admin.tables.view_map') : t('admin.tables.view_table')"
                        :icon="activeView === 'table' ? 'pi pi-map' : 'pi pi-table'"
                        class="p-button-outlined"
                        @click="toggleView"
                    />
                    <button class="btn btn-primary" @click="openCreateModal">
                        <i class="fas fa-plus me-2"></i>
                        {{ t('admin.tables.new_table') }}
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
                            {{ t('admin.tables.table_view') }}
                        </button>
                    </li>
                    <li class="nav-item">
                        <button 
                            class="nav-link"
                            :class="{ active: activeView === 'map' }"
                            @click="activeView = 'map'"
                        >
                            <i class="pi pi-map me-2"></i>
                            {{ t('admin.tables.interactive_map') }}
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
                                <Column field="table_number" :header="t('admin.tables.table_number')" sortable>
                                    <template #body="{ data }">
                                        <span class="fw-bold">{{ t('admin.tables.table_number') }} {{ data.table_number }}</span>
                                    </template>
                                </Column>
                                <Column field="capacity" :header="t('admin.tables.capacity')" sortable>
                                    <template #body="{ data }">
                                        <span>{{ data.capacity }} {{ t('admin.tables.people') }}</span>
                                    </template>
                                </Column>
                                <Column field="status" :header="t('common.status')" sortable>
                                    <template #body="{ data }">
                                        <span class="status-badge" :class="getStatusClass(data.status)">
                                            {{ getStatusText(data.status) }}
                                        </span>
                                    </template>
                                </Column>
                                <Column field="position" :header="t('admin.tables.position')">
                                    <template #body="{ data }">
                                        <small class="text-muted">X: {{ data.position_x }}, Y: {{ data.position_y }}</small>
                                    </template>
                                </Column>
                                <Column field="created_at" :header="t('common.created')" sortable>
                                    <template #body="{ data }">
                                        {{ formatDate(data.created_at) }}
                                    </template>
                                </Column>
                                <Column :header="t('common.actions')">
                                    <template #body="{ data }">
                                        <div class="d-flex gap-1">
                                            <Button 
                                                icon="pi pi-pencil" 
                                                class="p-button-rounded p-button-info p-button-sm" 
                                                @click="editTable(data)"
                                                :title="t('common.edit')"
                                                style="width: 36px; height: 36px;"
                                            />
                                            <Button 
                                                icon="pi pi-trash" 
                                                class="p-button-rounded p-button-danger p-button-sm" 
                                                @click="deleteTable(data)"
                                                :title="t('common.delete')"
                                                style="width: 36px; height: 36px;"
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
                :header="editingTable ? t('admin.tables.edit_table') : t('admin.tables.new_table')" 
                :modal="true" 
                :closable="true" 
                :style="{ width: '500px' }"
            >
                <form @submit.prevent="submitTable" class="row g-3">
                    <div class="col-12">
                        <label class="form-label">{{ t('admin.tables.table_number') }}</label>
                        <InputText 
                            v-model="form.table_number" 
                            class="w-100" 
                            type="number"
                            required 
                            :min="1"
                        />
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ t('admin.tables.capacity') }}</label>
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
                        <label class="form-label">{{ t('common.status') }}</label>
                        <Dropdown 
                            v-model="form.status" 
                            :options="statusOptions" 
                            optionLabel="label" 
                            optionValue="value" 
                            class="w-100" 
                        />
                    </div>
                    <div class="col-6">
                        <label class="form-label">{{ t('admin.tables.position_x') }}</label>
                        <InputText 
                            v-model="form.position_x" 
                            class="w-100" 
                            type="number"
                            :min="0"
                        />
                    </div>
                    <div class="col-6">
                        <label class="form-label">{{ t('admin.tables.position_y') }}</label>
                        <InputText 
                            v-model="form.position_y" 
                            class="w-100" 
                            type="number"
                            :min="0"
                        />
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
                            :label="editingTable ? t('common.update') : t('common.create')" 
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
import { useForm, router, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useNotifications } from '@/composables/useNotifications'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import AdminTableMap from '@/Components/AdminTableMap.vue'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Dropdown from 'primevue/dropdown'

const { t } = useI18n()

const props = defineProps({
    tables: Array
})

// Inicializar notificaciones
const { tableCreated, tableDeleted, formError, showSuccess } = useNotifications()

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

const statusOptions = computed(() => [
    { label: t('admin.tables.status_available'), value: 'available' },
    { label: t('admin.tables.status_reserved'), value: 'reserved' },
    { label: t('admin.tables.status_occupied'), value: 'occupied' }
]);

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
                showSuccess(t('admin.tables.table_updated'), t('admin.tables.table_updated'))
                closeModal()
                // Refresh the page or update tables list
                router.get(route('admin.tables'))
            },
            onError: (errors) => {
                console.error('Error updating table:', errors)
                formError(t('admin.tables.update_error'))
            }
        })
    } else {
        form.post(route('admin.tables.store'), {
            onSuccess: () => {
                tableCreated(form.table_number)
                closeModal()
                // Refresh the page or update tables list
                router.get(route('admin.tables'))
            },
            onError: (errors) => {
                console.error('Error creating table:', errors)
                formError(t('admin.tables.create_error'))
            }
        })
    }
}

const deleteTable = (table) => {
    if (confirm(t('admin.tables.confirm_delete'))) {
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
        'available': t('admin.tables.available'),
        'reserved': t('admin.tables.reserved'),
        'occupied': t('admin.tables.occupied')
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
