<template>
    <AdminLayout>
        <div class="container-fluid">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-dark">{{ t('admin.dashboard.title') }}</h1>
                    <p class="text-muted mb-0">{{ t('admin.dashboard.welcome') }}</p>
                </div>
                <div class="text-muted">
                    {{ new Date().toLocaleDateString('es-ES', { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                    }) }}
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="admin-card h-100">
                        <div class="admin-card-body d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                    <i class="fas fa-calendar-check text-primary fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-muted small">{{ t('admin.dashboard.total_reservations') }}</div>
                                <div class="h4 mb-0 text-dark">{{ stats.total_reservations }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="admin-card h-100">
                        <div class="admin-card-body d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                    <i class="fas fa-calendar-day text-success fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-muted small">{{ t('admin.dashboard.today_reservations') }}</div>
                                <div class="h4 mb-0 text-dark">{{ stats.today_reservations }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="admin-card h-100">
                        <div class="admin-card-body d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                    <i class="fas fa-chair text-info fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-muted small">{{ t('admin.dashboard.available_tables') }}</div>
                                <div class="h4 mb-0 text-dark">{{ stats.available_tables }}/{{ stats.total_tables }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                  <div class="col-xl-3 col-md-6">
                    <div class="admin-card h-100">
                        <div class="admin-card-body d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                    <i class="fas fa-users text-warning fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-muted small">{{ t('admin.dashboard.total_users') }}</div>
                                <div class="h4 mb-0 text-dark">{{ stats.total_users }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Second Row Stats Cards - Cupones -->
            <div class="row g-4 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="admin-card h-100">
                        <div class="admin-card-body d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-danger bg-opacity-10 rounded-3 p-3">
                                    <i class="fas fa-ticket-alt text-danger fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-muted small">{{ t('admin.dashboard.active_coupons') }}</div>
                                <div class="h4 mb-0 text-dark">{{ stats.active_coupons || 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="admin-card h-100">
                        <div class="admin-card-body d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-secondary bg-opacity-10 rounded-3 p-3">
                                    <i class="fas fa-tags text-secondary fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-muted small">{{ t('admin.dashboard.total_coupons') }}</div>
                                <div class="h4 mb-0 text-dark">{{ stats.total_coupons || 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="admin-card h-100">
                        <div class="admin-card-body d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                    <i class="fas fa-chart-line text-success fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-muted small">{{ t('admin.dashboard.used_coupons') }}</div>
                                <div class="h4 mb-0 text-dark">{{ stats.used_coupons || 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="admin-card h-100">
                        <div class="admin-card-body d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                    <i class="fas fa-percentage text-info fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-muted small">{{ t('admin.dashboard.total_savings') }}</div>
                                <div class="h4 mb-0 text-dark">€{{ stats.total_savings || '0.00' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Reservas Recientes -->
                <div class="col-lg-8">
                    <div class="admin-card h-100">
                        <div class="admin-card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="admin-card-title">{{ t('admin.dashboard.recent_reservations') }}</h5>
                                <Link :href="route('admin.reservations')" class="btn btn-sm btn-outline-primary">
                                    {{ t('admin.dashboard.view_all') }}
                                </Link>
                            </div>
                        </div>
                        <div class="admin-card-body p-0">
                            <div v-if="recent_reservations.length === 0" class="text-center py-5 text-muted">
                                <i class="fas fa-calendar-times fa-3x mb-3 opacity-50"></i>
                                <p>{{ t('admin.dashboard.no_reservations') }}</p>
                            </div>
                            <div v-else class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="border-0 py-3">{{ t('admin.dashboard.client') }}</th>
                                            <th class="border-0 py-3">{{ t('admin.dashboard.table') }}</th>
                                            <th class="border-0 py-3">{{ t('admin.dashboard.date') }}</th>
                                            <th class="border-0 py-3">{{ t('admin.dashboard.people') }}</th>
                                            <th class="border-0 py-3">{{ t('admin.dashboard.status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="reservation in recent_reservations" :key="reservation.id">
                                            <td class="py-3">
                                                <div>
                                                    <div class="fw-medium">{{ reservation.client_name }}</div>
                                                    <small class="text-muted">{{ reservation.client_email }}</small>
                                                </div>
                                            </td>
                                            <td class="py-3">Mesa {{ reservation.table_number }}</td>
                                            <td class="py-3">
                                                {{ formatDate(reservation.reservation_date) }}
                                            </td>
                                            <td class="py-3">{{ reservation.party_size }}</td>
                                            <td class="py-3">
                                                <span class="status-badge" :class="getStatusClass(reservation.status)">
                                                    {{ getStatusText(reservation.status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráfico Semanal -->
                <div class="col-lg-4">
                    <div class="admin-card h-100">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title">{{ t('admin.dashboard.weekly_reservations') }}</h5>
                        </div>
                        <div class="admin-card-body">
                            <div v-for="day in weekly_reservations" :key="day.date" class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <div class="fw-medium">{{ getDayName(day.day) }}</div>
                                    <small class="text-muted">{{ formatShortDate(day.date) }}</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="progress me-3" style="width: 100px; height: 8px;">
                                        <div 
                                            class="progress-bar bg-primary" 
                                            :style="{ width: getPercentage(day.count, Math.max(...weekly_reservations.map(d => d.count))) + '%' }"
                                        ></div>
                                    </div>
                                    <span class="badge bg-primary rounded-pill">{{ day.count }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accesos Rápidos -->
            <div class="row g-4 mt-2">
                <div class="col-12">
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title">{{ t('admin.dashboard.quick_actions') }}</h5>
                        </div>                        <div class="admin-card-body">
                            <div class="row g-3">
                                <div class="col-md-3 col-lg-2">
                                    <Link :href="route('admin.reservations')" class="btn btn-outline-primary w-100 py-3">
                                        <i class="fas fa-calendar-plus fa-2x mb-2 d-block"></i>
                                        {{ t('admin.dashboard.manage_reservations') }}
                                    </Link>
                                </div>
                                <div class="col-md-3 col-lg-2">
                                    <Link :href="route('admin.tables')" class="btn btn-outline-success w-100 py-3">
                                        <i class="fas fa-chair fa-2x mb-2 d-block"></i>
                                        {{ t('admin.dashboard.manage_tables') }}
                                    </Link>
                                </div>
                                <div class="col-md-3 col-lg-2">
                                    <Link :href="route('admin.menu')" class="btn btn-outline-warning w-100 py-3">
                                        <i class="fas fa-utensils fa-2x mb-2 d-block"></i>
                                        {{ t('admin.dashboard.manage_menu') }}
                                    </Link>
                                </div>
                                <div class="col-md-3 col-lg-2">
                                    <Link :href="route('admin.users')" class="btn btn-outline-info w-100 py-3">
                                        <i class="fas fa-users fa-2x mb-2 d-block"></i>
                                        {{ t('admin.dashboard.manage_users') }}
                                    </Link>
                                </div>
                                <div class="col-md-3 col-lg-2">
                                    <Link href="/admin/discount-coupons" class="btn btn-outline-danger w-100 py-3">
                                        <i class="fas fa-ticket-alt fa-2x mb-2 d-block"></i>
                                        {{ t('admin.dashboard.manage_coupons') }}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { defineProps } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const { t } = useI18n()

const props = defineProps({
    stats: Object,
    recent_reservations: Array,
    weekly_reservations: Array,
})

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatShortDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES', {
        month: 'short',
        day: 'numeric'
    })
}

const getDayName = (day) => {
    const days = {
        'Monday': t('common.days.monday_short'),
        'Tuesday': t('common.days.tuesday_short'),
        'Wednesday': t('common.days.wednesday_short'),
        'Thursday': t('common.days.thursday_short'),
        'Friday': t('common.days.friday_short'),
        'Saturday': t('common.days.saturday_short'),
        'Sunday': t('common.days.sunday_short')
    }
    return days[day] || day
}

const getStatusClass = (status) => {
    const classes = {
        'confirmed': 'status-badge-success',
        'pending': 'status-badge-warning',
        'cancelled': 'status-badge-danger',
        'completed': 'status-badge-success'
    }
    return classes[status] || 'status-badge-warning'
}

const getStatusText = (status) => {
    const texts = {
        'confirmed': 'Confirmada',
        'pending': 'Pendiente',
        'cancelled': 'Cancelada',
        'completed': 'Completada'
    }
    return texts[status] || status
}

const getPercentage = (value, max) => {
    return max > 0 ? (value / max) * 100 : 0
}
</script>
