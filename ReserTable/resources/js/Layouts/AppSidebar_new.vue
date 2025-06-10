<template>    
    <div class="h-full bg-white overflow-y-auto sidebar-scroll">
        <!-- Sidebar Header -->
        <div class="flex items-center justify-center h-16 border-b border-gray-200 bg-white">
            <Link href="/admin/dashboard" class="flex items-center text-xl font-bold text-blue-600 hover:text-blue-700 transition-colors">
                <i class="pi pi-home mr-2 text-xl"></i>
                <span>ReserTable</span>
            </Link>
        </div>

        <!-- Sidebar Menu -->
        <nav class="py-4 space-y-1">
            <!-- Dashboard -->
            <Link href="/admin/dashboard" 
                  class="flex items-center px-4 py-3 mx-2 text-gray-700 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors duration-200"
                  :class="{ 'bg-blue-50 text-blue-700 border-r-2 border-blue-600': $page.url === '/admin/dashboard' }">
                <i class="pi pi-home w-5 h-5 mr-3"></i>
                <span>{{ t('navigation.dashboard') }}</span>
            </Link>

            <!-- Reservas -->
            <div>
                <button @click="toggleSubmenu('reservas')" 
                        class="flex items-center w-full px-4 py-3 mx-2 text-gray-700 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors duration-200">
                    <i class="pi pi-calendar w-5 h-5 mr-3"></i>
                    <span class="flex-1 text-left">{{ t('navigation.reservations') }}</span>
                    <i class="pi pi-angle-down transition-transform duration-200" 
                       :class="{ 'rotate-180': activeSubmenus.reservas }"></i>
                </button>
                <div v-show="activeSubmenus.reservas" class="pl-4 mt-1 space-y-1">
                    <Link href="/admin/reservations" 
                          class="flex items-center px-4 py-2 mx-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <i class="pi pi-circle w-3 h-3 mr-3"></i>
                        <span>{{ t('navigation.all_reservations') }}</span>
                    </Link>
                    <Link href="/admin/reservations/calendar" 
                          class="flex items-center px-4 py-2 mx-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <i class="pi pi-circle w-3 h-3 mr-3"></i>
                        <span>{{ t('navigation.calendar') }}</span>
                    </Link>
                    <Link href="/admin/reservations/create" 
                          class="flex items-center px-4 py-2 mx-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <i class="pi pi-circle w-3 h-3 mr-3"></i>
                        <span>{{ t('navigation.new_reservation') }}</span>
                    </Link>
                </div>
            </div>

            <!-- Mesas -->
            <div>
                <button @click="toggleSubmenu('mesas')" 
                        class="flex items-center w-full px-4 py-3 mx-2 text-gray-700 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors duration-200">
                    <i class="pi pi-table w-5 h-5 mr-3"></i>
                    <span class="flex-1 text-left">{{ t('navigation.tables') }}</span>
                    <i class="pi pi-angle-down transition-transform duration-200" 
                       :class="{ 'rotate-180': activeSubmenus.mesas }"></i>
                </button>
                <div v-show="activeSubmenus.mesas" class="pl-4 mt-1 space-y-1">
                    <Link href="/admin/tables" 
                          class="flex items-center px-4 py-2 mx-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <i class="pi pi-circle w-3 h-3 mr-3"></i>
                        <span>{{ t('navigation.table_management') }}</span>
                    </Link>
                    <Link href="/admin/tables/layout" 
                          class="flex items-center px-4 py-2 mx-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <i class="pi pi-circle w-3 h-3 mr-3"></i>
                        <span>{{ t('navigation.layout_design') }}</span>
                    </Link>
                </div>
            </div>

            <!-- Clientes -->
            <Link href="/admin/clients" 
                  class="flex items-center px-4 py-3 mx-2 text-gray-700 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors duration-200">
                <i class="pi pi-users w-5 h-5 mr-3"></i>
                <span>{{ t('navigation.clients') }}</span>
            </Link>

            <!-- Menú -->
            <div>
                <button @click="toggleSubmenu('menu')" 
                        class="flex items-center w-full px-4 py-3 mx-2 text-gray-700 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors duration-200">
                    <i class="pi pi-book w-5 h-5 mr-3"></i>
                    <span class="flex-1 text-left">{{ t('navigation.menu') }}</span>
                    <i class="pi pi-angle-down transition-transform duration-200" 
                       :class="{ 'rotate-180': activeSubmenus.menu }"></i>
                </button>
                <div v-show="activeSubmenus.menu" class="pl-4 mt-1 space-y-1">
                    <Link href="/admin/menu/categories" 
                          class="flex items-center px-4 py-2 mx-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <i class="pi pi-circle w-3 h-3 mr-3"></i>
                        <span>{{ t('navigation.categories') }}</span>
                    </Link>
                    <Link href="/admin/menu/products" 
                          class="flex items-center px-4 py-2 mx-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <i class="pi pi-circle w-3 h-3 mr-3"></i>
                        <span>{{ t('navigation.products') }}</span>
                    </Link>
                    <Link href="/admin/menu/allergens" 
                          class="flex items-center px-4 py-2 mx-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <i class="pi pi-circle w-3 h-3 mr-3"></i>
                        <span>{{ t('navigation.allergens') }}</span>
                    </Link>
                </div>
            </div>
                        <span>Alérgenos</span>
                    </Link>
                </div>
            </div>

            <!-- Pedidos -->
            <Link href="/admin/orders" 
                  class="flex items-center px-4 py-3 mx-2 text-gray-700 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors duration-200">
                <i class="pi pi-shopping-cart w-5 h-5 mr-3"></i>
                <span>{{ t('navigation.orders') }}</span>
            </Link>

            <!-- Inventario -->
            <Link href="/admin/inventory" 
                  class="flex items-center px-4 py-3 mx-2 text-gray-700 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors duration-200">
                <i class="pi pi-box w-5 h-5 mr-3"></i>
                <span>{{ t('navigation.inventory') }}</span>
            </Link>

            <!-- Descuentos -->
            <Link href="/admin/discounts" 
                  class="flex items-center px-4 py-3 mx-2 text-gray-700 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors duration-200">
                <i class="pi pi-percentage w-5 h-5 mr-3"></i>
                <span>{{ t('navigation.discounts') }}</span>
            </Link>

            <!-- Reportes -->
            <div>
                <button @click="toggleSubmenu('reportes')" 
                        class="flex items-center w-full px-4 py-3 mx-2 text-gray-700 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors duration-200">
                    <i class="pi pi-chart-bar w-5 h-5 mr-3"></i>
                    <span class="flex-1 text-left">{{ t('navigation.reports') }}</span>
                    <i class="pi pi-angle-down transition-transform duration-200" 
                       :class="{ 'rotate-180': activeSubmenus.reportes }"></i>
                </button>
                <div v-show="activeSubmenus.reportes" class="pl-4 mt-1 space-y-1">
                    <Link href="/admin/reports/sales" 
                          class="flex items-center px-4 py-2 mx-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <i class="pi pi-circle w-3 h-3 mr-3"></i>
                        <span>{{ t('navigation.sales') }}</span>
                    </Link>
                    <Link href="/admin/reports/reservations" 
                          class="flex items-center px-4 py-2 mx-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <i class="pi pi-circle w-3 h-3 mr-3"></i>
                        <span>{{ t('navigation.reservations_report') }}</span>
                    </Link>
                    <Link href="/admin/reports/tables" 
                          class="flex items-center px-4 py-2 mx-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <i class="pi pi-circle w-3 h-3 mr-3"></i>
                        <span>{{ t('navigation.table_occupancy') }}</span>
                    </Link>
                </div>
            </div>

            <!-- Personal -->
            <div>
                <button @click="toggleSubmenu('personal')" 
                        class="flex items-center w-full px-4 py-3 mx-2 text-gray-700 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors duration-200">
                    <i class="pi pi-user-plus w-5 h-5 mr-3"></i>
                    <span class="flex-1 text-left">{{ t('navigation.staff') }}</span>
                    <i class="pi pi-angle-down transition-transform duration-200" 
                       :class="{ 'rotate-180': activeSubmenus.personal }"></i>
                </button>
                <div v-show="activeSubmenus.personal" class="pl-4 mt-1 space-y-1">
                    <Link href="/admin/staff" 
                          class="flex items-center px-4 py-2 mx-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <i class="pi pi-circle w-3 h-3 mr-3"></i>
                        <span>{{ t('navigation.employees') }}</span>
                    </Link>
                    <Link href="/admin/staff/roles" 
                          class="flex items-center px-4 py-2 mx-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <i class="pi pi-circle w-3 h-3 mr-3"></i>
                        <span>{{ t('navigation.roles') }}</span>
                    </Link>
                    <Link href="/admin/staff/schedules" 
                          class="flex items-center px-4 py-2 mx-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <i class="pi pi-circle w-3 h-3 mr-3"></i>
                        <span>{{ t('navigation.schedules') }}</span>
                    </Link>
                </div>
            </div>

            <!-- Configuración -->
            <div>
                <button @click="toggleSubmenu('configuracion')" 
                        class="flex items-center w-full px-4 py-3 mx-2 text-gray-700 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors duration-200">
                    <i class="pi pi-cog w-5 h-5 mr-3"></i>
                    <span class="flex-1 text-left">{{ t('navigation.configuration') }}</span>
                    <i class="pi pi-angle-down transition-transform duration-200" 
                       :class="{ 'rotate-180': activeSubmenus.configuracion }"></i>
                </button>
                <div v-show="activeSubmenus.configuracion" class="pl-4 mt-1 space-y-1">
                    <Link href="/admin/settings/restaurant" 
                          class="flex items-center px-4 py-2 mx-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <i class="pi pi-circle w-3 h-3 mr-3"></i>
                        <span>{{ t('navigation.restaurant') }}</span>
                    </Link>
                    <Link href="/admin/settings/system" 
                          class="flex items-center px-4 py-2 mx-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <i class="pi pi-circle w-3 h-3 mr-3"></i>
                        <span>{{ t('navigation.system') }}</span>
                    </Link>
                    <Link href="/admin/settings/notifications" 
                          class="flex items-center px-4 py-2 mx-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <i class="pi pi-circle w-3 h-3 mr-3"></i>
                        <span>{{ t('navigation.notifications') }}</span>
                    </Link>
                </div>
            </div>
        </nav>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const activeSubmenus = ref({
    reservas: false,
    mesas: false,
    menu: false,
    reportes: false,
    personal: false,
    configuracion: false
})

const toggleSubmenu = (submenu) => {
    activeSubmenus.value[submenu] = !activeSubmenus.value[submenu]
}
</script>
