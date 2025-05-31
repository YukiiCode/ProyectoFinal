<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import { ref, computed } from 'vue'

// Sample data
const recentReservations = ref([
    {
        cliente: 'María García',
        mesa: '05',
        fecha: '2025-05-31',
        hora: '19:30',
        personas: 4,
        estado: 'confirmada'
    },
    {
        cliente: 'Juan Pérez',
        mesa: '12',
        fecha: '2025-05-31',
        hora: '20:00',
        personas: 2,
        estado: 'pendiente'
    },
    {
        cliente: 'Ana Rodríguez',
        mesa: '08',
        fecha: '2025-05-31',
        hora: '21:00',
        personas: 6,
        estado: 'confirmada'
    },
    {
        cliente: 'Carlos López',
        mesa: '03',
        fecha: '2025-05-31',
        hora: '19:00',
        personas: 3,
        estado: 'cancelada'
    }
])

const currentDate = computed(() => {
    return new Date().toLocaleDateString('es-ES', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
})

const getStatusColor = (status) => {
    const colors = {
        'confirmada': 'success',
        'pendiente': 'warning',
        'cancelada': 'danger'
    }
    return colors[status] || 'secondary'
}
</script>

<template>
    <AdminLayout>
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-1 text-dark">Dashboard</h1>
                            <p class="text-muted mb-0">Bienvenido al panel de control de ReserTable</p>
                        </div>
                        <div>
                            <span class="badge bg-primary">{{ currentDate }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="admin-card h-100">
                        <div class="admin-card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Reservas Hoy</h6>
                                    <h3 class="text-primary mb-0">24</h3>
                                </div>
                                <div class="text-primary">
                                    <i class="pi pi-calendar fs-1"></i>
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="status-badge status-badge-success">
                                    <i class="pi pi-arrow-up me-1"></i>12%
                                </span>
                                <span class="text-muted ms-2">vs ayer</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="admin-card h-100">
                        <div class="admin-card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Mesas Ocupadas</h6>
                                    <h3 class="text-warning mb-0">18/25</h3>
                                </div>
                                <div class="text-warning">
                                    <i class="pi pi-table fs-1"></i>
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="status-badge status-badge-warning">72% ocupación</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="admin-card h-100">
                        <div class="admin-card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Ingresos Hoy</h6>
                                    <h3 class="text-success mb-0">€1,240</h3>
                                </div>
                                <div class="text-success">
                                    <i class="pi pi-euro fs-1"></i>
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="status-badge status-badge-success">
                                    <i class="pi pi-arrow-up me-1"></i>8%
                                </span>
                                <span class="text-muted ms-2">vs ayer</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="admin-card h-100">
                        <div class="admin-card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Clientes Nuevos</h6>
                                    <h3 class="text-info mb-0">12</h3>
                                </div>
                                <div class="text-info">
                                    <i class="pi pi-users fs-1"></i>
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="status-badge status-badge-success">
                                    <i class="pi pi-arrow-up me-1"></i>15%
                                </span>
                                <span class="text-muted ms-2">este mes</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="row">
                <!-- Recent Reservations -->
                <div class="col-lg-8 mb-4">
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="admin-card-title mb-0">Reservas Recientes</h5>
                                <button class="btn btn-admin-primary btn-sm">
                                    <i class="pi pi-plus me-1"></i>
                                    Nueva Reserva
                                </button>
                            </div>
                        </div>
                        <div class="admin-card-body">
                            <DataTable :value="recentReservations" class="border-0">
                                <Column field="cliente" header="Cliente">
                                    <template #body="slotProps">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 32px; height: 32px;">
                                                <span class="text-white fw-bold small">
                                                    {{ slotProps.data.cliente.charAt(0) }}
                                                </span>
                                            </div>
                                            <span>{{ slotProps.data.cliente }}</span>
                                        </div>
                                    </template>
                                </Column>
                                <Column field="mesa" header="Mesa"></Column>
                                <Column field="fecha" header="Fecha"></Column>
                                <Column field="hora" header="Hora"></Column>
                                <Column field="personas" header="Personas"></Column>
                                <Column field="estado" header="Estado">
                                    <template #body="slotProps">
                                        <span :class="`status-badge status-badge-${getStatusColor(slotProps.data.estado)}`">
                                            {{ slotProps.data.estado }}
                                        </span>
                                    </template>
                                </Column>
                            </DataTable>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="col-lg-4 mb-4">
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title mb-0">Acciones Rápidas</h5>
                        </div>
                        <div class="admin-card-body">
                            <div class="d-grid gap-2">
                                <button class="btn btn-admin-primary">
                                    <i class="pi pi-plus me-2"></i>
                                    Nueva Reserva
                                </button>
                                <button class="btn btn-outline-primary">
                                    <i class="pi pi-table me-2"></i>
                                    Ver Mapa de Mesas
                                </button>
                                <button class="btn btn-outline-success">
                                    <i class="pi pi-users me-2"></i>
                                    Gestionar Clientes
                                </button>
                                <button class="btn btn-outline-info">
                                    <i class="pi pi-chart-bar me-2"></i>
                                    Ver Reportes
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Summary -->
                    <div class="admin-card mt-3">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title mb-0">Resumen de Hoy</h5>
                        </div>
                        <div class="admin-card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="text-center">
                                        <h4 class="text-primary mb-1">24</h4>
                                        <small class="text-muted">Reservas</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <h4 class="text-success mb-1">18</h4>
                                        <small class="text-muted">Confirmadas</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <h4 class="text-warning mb-1">3</h4>
                                        <small class="text-muted">Pendientes</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <h4 class="text-danger mb-1">1</h4>
                                        <small class="text-muted">Canceladas</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
