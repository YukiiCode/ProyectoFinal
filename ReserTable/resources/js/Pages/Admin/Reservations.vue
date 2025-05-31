<template>
    <AdminLayout>
        <div class="container-fluid">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-dark">Gestión de Reservas</h1>
                    <p class="text-muted mb-0">Administra todas las reservas del restaurante</p>
                </div>
                <button class="btn btn-primary" @click="openCreateModal">
                    <i class="fas fa-plus me-2"></i>
                    Nueva Reserva
                </button>
            </div>

            <!-- Filters -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <Dropdown 
                        v-model="filters.status" 
                        :options="statusOptions" 
                        optionLabel="label" 
                        optionValue="value" 
                        class="w-100"
                        placeholder="Todos los estados"
                        :showClear="true"
                        @change="applyFilters"
                    />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha</label>
                    <InputText 
                        type="date" 
                        v-model="filters.date" 
                        class="w-100"
                        @change="applyFilters"
                    />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Mesa</label>
                    <InputText 
                        v-model="filters.table" 
                        class="w-100"
                        placeholder="Número de mesa"
                        @input="applyFilters"
                    />
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-outline-secondary w-100" @click="clearFilters">
                        <i class="fas fa-times me-2"></i>
                        Limpiar Filtros
                    </button>
                </div>
            </div>

            <!-- Reservations Table -->
            <div class="row">
                <div class="col-12">
                    <div class="admin-card">
                        <div class="admin-card-body">
                            <DataTable 
                                :value="filteredReservations" 
                                responsive-layout="scroll" 
                                striped-rows 
                                paginator 
                                :rows="15" 
                                :rows-per-page-options="[10,15,25]"
                                class="p-datatable-sm"
                            >
                                <Column field="client_name" header="Cliente" sortable>
                                    <template #body="{ data }">
                                        <div>
                                            <div class="fw-medium">{{ data.client_name }}</div>
                                            <small class="text-muted">{{ data.client_email }}</small>
                                        </div>
                                    </template>
                                </Column>
                                <Column field="table_number" header="Mesa" sortable>
                                    <template #body="{ data }">
                                        <span class="fw-bold">Mesa {{ data.table_number }}</span>
                                    </template>
                                </Column>
                                <Column field="reservation_date" header="Fecha y Hora" sortable>
                                    <template #body="{ data }">
                                        <div>
                                            <div>{{ formatDate(data.reservation_date) }}</div>
                                            <small class="text-muted">{{ formatTime(data.reservation_date) }}</small>
                                        </div>
                                    </template>
                                </Column>
                                <Column field="party_size" header="Personas" sortable>
                                    <template #body="{ data }">
                                        <span>{{ data.party_size }} personas</span>
                                    </template>
                                </Column>
                                <Column field="status" header="Estado" sortable>
                                    <template #body="{ data }">
                                        <span class="status-badge" :class="getStatusClass(data.status)">
                                            {{ getStatusText(data.status) }}
                                        </span>
                                    </template>
                                </Column>
                                <Column field="created_at" header="Creada" sortable>
                                    <template #body="{ data }">
                                        {{ formatDate(data.created_at) }}
                                    </template>
                                </Column>
                                <Column header="Acciones">
                                    <template #body="{ data }">
                                        <div class="d-flex gap-2">
                                            <Button 
                                                icon="pi pi-pencil" 
                                                class="p-button-rounded p-button-info p-button-sm" 
                                                @click="editReservation(data)"
                                                v-tooltip="'Editar'" 
                                            />
                                            <Button 
                                                icon="pi pi-check" 
                                                class="p-button-rounded p-button-success p-button-sm" 
                                                @click="confirmReservation(data)"
                                                v-tooltip="'Confirmar'" 
                                                v-if="data.status === 'pending'"
                                            />
                                            <Button 
                                                icon="pi pi-times" 
                                                class="p-button-rounded p-button-warning p-button-sm" 
                                                @click="cancelReservation(data)"
                                                v-tooltip="'Cancelar'" 
                                                v-if="data.status !== 'cancelled'"
                                            />
                                            <Button 
                                                icon="pi pi-trash" 
                                                class="p-button-rounded p-button-danger p-button-sm" 
                                                @click="deleteReservation(data)"
                                                v-tooltip="'Eliminar'" 
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
                :header="editingReservation ? 'Editar Reserva' : 'Nueva Reserva'" 
                :modal="true" 
                :closable="true" 
                :style="{ width: '600px' }"
            >
                <form @submit.prevent="submitReservation" class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Cliente</label>
                        <InputText 
                            v-model="form.client_name" 
                            class="w-100" 
                            required 
                            placeholder="Nombre del cliente"
                        />
                    </div>
                    <div class="col-12">
                        <label class="form-label">Email del Cliente</label>
                        <InputText 
                            v-model="form.client_email" 
                            class="w-100" 
                            type="email"
                            required 
                            placeholder="email@ejemplo.com"
                        />
                    </div>
                    <div class="col-6">
                        <label class="form-label">Mesa</label>
                        <Dropdown 
                            v-model="form.table_id" 
                            :options="availableTables" 
                            optionLabel="label" 
                            optionValue="value" 
                            class="w-100" 
                            placeholder="Seleccionar mesa"
                        />
                    </div>
                    <div class="col-6">
                        <label class="form-label">Número de Personas</label>
                        <InputText 
                            v-model="form.party_size" 
                            class="w-100" 
                            type="number"
                            required 
                            :min="1"
                            :max="20"
                        />
                    </div>
                    <div class="col-6">
                        <label class="form-label">Fecha</label>
                        <InputText 
                            v-model="form.reservation_date" 
                            class="w-100" 
                            type="date"
                            required 
                        />
                    </div>
                    <div class="col-6">
                        <label class="form-label">Hora</label>
                        <InputText 
                            v-model="form.reservation_time" 
                            class="w-100" 
                            type="time"
                            required 
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
                    <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                        <Button 
                            label="Cancelar" 
                            icon="pi pi-times" 
                            class="p-button-text" 
                            @click="closeModal" 
                            type="button" 
                        />
                        <Button 
                            :label="editingReservation ? 'Actualizar' : 'Crear'" 
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

const props = defineProps({
    reservations: Array
})

const reservations = ref(props.reservations || [])
const showModal = ref(false)
const editingReservation = ref(null)

const filters = ref({
    status: null,
    date: null,
    table: null
})

const form = useForm({
    client_name: '',
    client_email: '',
    table_id: '',
    party_size: '',
    reservation_date: '',
    reservation_time: '',
    status: 'pending',
})

const statusOptions = [
    { label: 'Pendiente', value: 'pending' },
    { label: 'Confirmada', value: 'confirmed' },
    { label: 'Cancelada', value: 'cancelled' },
    { label: 'Completada', value: 'completed' }
]

// Mock data for tables - in real app would come from props or API
const availableTables = computed(() => {
    const tables = []
    for (let i = 1; i <= 20; i++) {
        tables.push({
            label: `Mesa ${i}`,
            value: i
        })
    }
    return tables
})

const filteredReservations = computed(() => {
    let filtered = reservations.value

    if (filters.value.status) {
        filtered = filtered.filter(r => r.status === filters.value.status)
    }

    if (filters.value.date) {
        filtered = filtered.filter(r => {
            const reservationDate = new Date(r.reservation_date).toISOString().split('T')[0]
            return reservationDate === filters.value.date
        })
    }

    if (filters.value.table) {
        filtered = filtered.filter(r => 
            r.table_number.toString().includes(filters.value.table)
        )
    }

    return filtered
})

const openCreateModal = () => {
    editingReservation.value = null
    form.reset()
    form.status = 'pending'
    showModal.value = true
}

const editReservation = (reservation) => {
    editingReservation.value = reservation
    const dateTime = new Date(reservation.reservation_date)
    form.client_name = reservation.client_name
    form.client_email = reservation.client_email
    form.table_id = reservation.table_id
    form.party_size = reservation.party_size
    form.reservation_date = dateTime.toISOString().split('T')[0]
    form.reservation_time = dateTime.toTimeString().split(' ')[0].substring(0, 5)
    form.status = reservation.status
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    editingReservation.value = null
    form.reset()
    form.status = 'pending'
}

const submitReservation = () => {
    const dateTime = `${form.reservation_date} ${form.reservation_time}:00`
    form.reservation_date = dateTime

    if (editingReservation.value) {
        form.put(route('admin.reservations.update', editingReservation.value.id), {
            onSuccess: () => {
                closeModal()
                router.get(route('admin.reservations'))
            }
        })
    } else {
        form.post(route('admin.reservations.store'), {
            onSuccess: () => {
                closeModal()
                router.get(route('admin.reservations'))
            }
        })
    }
}

const confirmReservation = (reservation) => {
    router.patch(route('admin.reservations.status', reservation.id), 
        { status: 'confirmed' }, 
        {
            onSuccess: () => {
                router.get(route('admin.reservations'))
            }
        }
    )
}

const cancelReservation = (reservation) => {
    if (confirm('¿Estás seguro de que quieres cancelar esta reserva?')) {
        router.patch(route('admin.reservations.status', reservation.id), 
            { status: 'cancelled' }, 
            {
                onSuccess: () => {
                    router.get(route('admin.reservations'))
                }
            }
        )
    }
}

const deleteReservation = (reservation) => {
    if (confirm('¿Estás seguro de que quieres eliminar esta reserva?')) {
        router.delete(route('admin.reservations.destroy', reservation.id), {
            onSuccess: () => {
                router.get(route('admin.reservations'))
            }
        })
    }
}

const applyFilters = () => {
    // Los filtros se aplican automáticamente por el computed
}

const clearFilters = () => {
    filters.value = {
        status: null,
        date: null,
        table: null
    }
}

const getStatusClass = (status) => {
    const classes = {
        'pending': 'status-badge-warning',
        'confirmed': 'status-badge-success',
        'cancelled': 'status-badge-danger',
        'completed': 'status-badge-info'
    }
    return classes[status] || 'status-badge-secondary'
}

const getStatusText = (status) => {
    const texts = {
        'pending': 'Pendiente',
        'confirmed': 'Confirmada',
        'cancelled': 'Cancelada',
        'completed': 'Completada'
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

const formatTime = (date) => {
    return new Date(date).toLocaleTimeString('es-ES', {
        hour: '2-digit',
        minute: '2-digit'
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

.status-badge-info {
    background-color: #cff4fc;
    color: #055160;
}

.status-badge-secondary {
    background-color: #e2e3e5;
    color: #41464b;
}
</style>
