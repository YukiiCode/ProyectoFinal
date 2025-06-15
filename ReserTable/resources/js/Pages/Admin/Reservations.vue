<template>
    <AdminLayout>
        <div class="container-fluid">
            <!-- Header Mejorado -->            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-1 flex items-center gap-2 transition-colors main-title">
                        <i class="pi pi-calendar-check text-primary text-2xl"></i>
                        {{ t('admin.reservations.title') }}
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 transition-colors">{{ t('admin.reservations.subtitle') }}</p>
                </div>
                <Button icon="pi pi-plus" :label="t('admin.reservations.new_reservation')" class="p-button-primary p-button-lg shadow interactive-element" @click="openCreateModal" />
            </div>

            <!-- Filtros Mejorados -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex flex-col gap-2 transition-colors">
                    <label class="font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2"><i class="pi pi-filter"></i> {{ t('common.status') }}</label>
                    <Dropdown 
                        v-model="filters.status" 
                        :options="statusOptions" 
                        optionLabel="label" 
                        optionValue="value" 
                        class="w-full"
                        :placeholder="t('admin.reservations.filter_status')"
                        :showClear="true"
                        @change="applyFilters"
                    />
                </div>                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex flex-col gap-2 transition-colors">
                    <label class="font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2"><i class="pi pi-calendar"></i> {{ t('common.date') }}</label>
                    <InputText 
                        type="date" 
                        v-model="filters.date" 
                        class="w-full"
                        @change="applyFilters"
                    />
                </div>                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex flex-col gap-2 transition-colors">
                    <label class="font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2"><i class="pi pi-table"></i> {{ t('admin.reservations.table') }}</label>
                    <InputText 
                        v-model="filters.table" 
                        class="w-full"
                        :placeholder="t('admin.reservations.filter_table')"
                        @input="applyFilters"
                    />
                </div>
                <div class="flex items-end">
                    <Button icon="pi pi-times" :label="t('common.filter')" class="p-button-outlined w-full" @click="clearFilters" />
                </div>
            </div>

            <!-- Tabla de Reservas Mejorada -->
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
                        <Column field="client_name" :header="t('admin.reservations.client')" sortable>
                            <template #body="{ data }">
                                <div>
                                    <div class="font-semibold">{{ data.client_name }}</div>
                                    <small class="text-gray-400">{{ data.client_email }}</small>
                                </div>
                            </template>
                        </Column>
                        <Column field="table_number" :header="t('admin.reservations.table')" sortable>
                            <template #body="{ data }">
                                <span class="font-bold text-primary">{{ t('admin.reservations.table') }} {{ data.table_number }}</span>
                            </template>
                        </Column>
                        <Column field="reservation_date" :header="t('admin.reservations.date_time')" sortable>
                            <template #body="{ data }">
                                <div>
                                    <div>{{ formatDate(data.reservation_date) }}</div>
                                    <small class="text-gray-400">{{ formatTime(data.reservation_date) }}</small>
                                </div>
                            </template>
                        </Column>
                        <Column field="party_size" :header="t('admin.reservations.party_size')" sortable>
                            <template #body="{ data }">
                                <span class="badge bg-blue-100 text-blue-800">{{ data.party_size }} {{ t('reservations.people') }}</span>
                            </template>
                        </Column>
                        <Column field="status" :header="t('common.status')" sortable>
                            <template #body="{ data }">
                                <span class="status-badge" :class="getStatusClass(data.status)">
                                    {{ getStatusText(data.status) }}
                                </span>
                            </template>
                        </Column>
                        <Column field="created_at" :header="t('common.created')" sortable>
                            <template #body="{ data }">
                                <span class="text-xs text-gray-400">{{ formatDate(data.created_at) }}</span>
                            </template>
                        </Column>
                        <Column :header="t('common.actions')">
                            <template #body="{ data }">
                                <div class="flex gap-2">                                    <Button 
                                        icon="pi pi-pencil" 
                                        class="p-button-rounded p-button-info p-button-sm" 
                                        @click="editReservation(data)"
                                        :title="t('common.edit')" 
                                    />
                                    <Button 
                                        icon="pi pi-check" 
                                        class="p-button-rounded p-button-success p-button-sm" 
                                        @click="confirmReservation(data)"
                                        :title="t('admin.reservations.confirm_reservation')" 
                                        v-if="data.status === 'pending'"
                                    />
                                    <Button 
                                        icon="pi pi-times" 
                                        class="p-button-rounded p-button-warning p-button-sm" 
                                        @click="cancelReservation(data)"
                                        :title="t('admin.reservations.cancel_reservation')" 
                                        v-if="data.status !== 'cancelled'"
                                    />
                                    <Button 
                                        icon="pi pi-trash" 
                                        class="p-button-rounded p-button-danger p-button-sm" 
                                        @click="deleteReservation(data)"
                                        :title="t('common.delete')" 
                                    />
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>

            <!-- Modal Mejorado -->
            <Dialog 
                v-model:visible="showModal" 
                :header="editingReservation ? t('admin.reservations.update_reservation') : t('admin.reservations.new_reservation')" 
                :modal="true" 
                :closable="true" 
                :style="{ width: '600px' }"
                class="rounded-xl shadow-lg"
            >
                <form @submit.prevent="submitReservation" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="font-semibold text-gray-700">{{ t('admin.reservations.client') }}</label>
                        <InputText 
                            v-model="form.client_name" 
                            class="w-full" 
                            required 
                            :placeholder="t('admin.reservations.client_name')"
                        />
                    </div>
                    <div class="col-span-2">
                        <label class="font-semibold text-gray-700">{{ t('admin.reservations.client_email') }}</label>
                        <InputText 
                            v-model="form.client_email" 
                            class="w-full" 
                            type="email"
                            required 
                            placeholder="email@ejemplo.com"
                        />
                    </div>
                    <div>
                        <label class="font-semibold text-gray-700">{{ t('admin.reservations.table') }}</label>
                        <Dropdown 
                            v-model="form.table_id" 
                            :options="availableTables" 
                            optionLabel="label" 
                            optionValue="value" 
                            class="w-full" 
                            :placeholder="t('admin.reservations.select_table')"
                        />
                    </div>
                    <div>
                        <label class="font-semibold text-gray-700">{{ t('admin.reservations.party_size') }}</label>
                        <InputText 
                            v-model="form.party_size" 
                            class="w-full" 
                            type="number" 
                            min="1" 
                            max="20" 
                            required 
                        />
                    </div>
                    <div>
                        <label class="font-semibold text-gray-700">{{ t('common.date') }}</label>
                        <InputText 
                            type="date" 
                            v-model="form.reservation_date" 
                            class="w-full" 
                            required 
                        />
                    </div>
                    <div>
                        <label class="font-semibold text-gray-700">{{ t('common.time') }}</label>
                        <InputText 
                            type="time" 
                            v-model="form.reservation_time" 
                            class="w-full" 
                            required 
                        />
                    </div>
                    <div class="col-span-2">
                        <label class="font-semibold text-gray-700">{{ t('common.status') }}</label>
                        <Dropdown 
                            v-model="form.status" 
                            :options="statusOptions" 
                            optionLabel="label" 
                            optionValue="value" 
                            class="w-full" 
                        />
                    </div>
                    <div class="col-span-2 flex justify-end gap-2 mt-4">
                        <Button 
                            :label="t('common.cancel')" 
                            icon="pi pi-times" 
                            class="p-button-text" 
                            @click="closeModal" 
                            type="button" 
                        />
                        <Button 
                            :label="editingReservation ? t('common.update') : t('common.create')" 
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
import { useNotifications } from '@/composables/useNotifications'
import { useI18n } from 'vue-i18n'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Dropdown from 'primevue/dropdown'

const { t } = useI18n()

const props = defineProps({
    reservations: Array
})

// Inicializar notificaciones
const { 
    reservationCreated, 
    reservationUpdated, 
    reservationCancelled, 
    reservationError,
    formError 
} = useNotifications()

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

const statusOptions = computed(() => [
    { label: t('admin.reservations.pending'), value: 'pending' },
    { label: t('admin.reservations.confirmed'), value: 'confirmed' },
    { label: t('admin.reservations.cancelled'), value: 'cancelled' },
    { label: t('admin.reservations.completed'), value: 'completed' }
]);

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
                reservationUpdated()
                closeModal()
            }
        })
    } else {
        form.post(route('admin.reservations.store'), {
            onSuccess: () => {
                reservationCreated()
                closeModal()
            }
        })
    }
}

const confirmReservation = (reservation) => {
    router.patch(route('admin.reservations.status', reservation.id), 
        { status: 'confirmed' }, 
        {
            onSuccess: () => {
                reservationUpdated()
            }
        }
    )
}

const cancelReservation = (reservation) => {
    if (confirm(t('admin.reservations.confirm_cancel'))) {
        router.patch(route('admin.reservations.status', reservation.id), 
            { status: 'cancelled' }, 
            {
                onSuccess: () => {
                    reservationCancelled()
                }
            }
        )
    }
}

const deleteReservation = (reservation) => {
    if (confirm(t('admin.reservations.confirm_delete'))) {
        router.delete(route('admin.reservations.destroy', reservation.id), {
            onSuccess: () => {
                reservationCancelled()
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
    const statusKeys = {
        'pending': 'admin.reservations.pending',
        'confirmed': 'admin.reservations.confirmed',
        'cancelled': 'admin.reservations.cancelled',
        'completed': 'admin.reservations.completed'
    }
    return statusKeys[status] ? t(statusKeys[status]) : status
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString(t('reservations.dateLocale'), {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatTime = (date) => {
    return new Date(date).toLocaleTimeString(t('reservations.dateLocale'), {
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
