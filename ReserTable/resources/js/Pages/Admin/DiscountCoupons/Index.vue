<template>  <AdminLayout :title="t('admin.coupons.title')">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ t('admin.coupons.title') }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <!-- Header con botón crear -->
          <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
              <div>
                <h3 class="text-lg font-medium text-gray-900">{{ t('admin.coupons.management') }}</h3>
                <p class="mt-1 text-sm text-gray-600">
                  {{ t('admin.coupons.description') }}
                </p>
              </div>
              <Link
                :href="route('admin.discount-coupons.create')"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                <PlusIcon class="w-4 h-4 mr-2" />
                {{ t('admin.coupons.new_coupon') }}
              </Link>
            </div>
          </div><!-- Filtros -->
          <div class="p-6 border-b border-gray-200 bg-gray-50 form-section">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  {{ t('admin.coupons.search_code') }}
                </label>
                <input
                  v-model="filters.search"
                  type="text"
                  :placeholder="t('admin.coupons.code_placeholder')"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  {{ t('coupons.type') }}
                </label>
                <select
                  v-model="filters.type"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="">{{ t('admin.coupons.all_types') }}</option>
                  <option value="global">{{ t('coupons.global') }}</option>
                  <option value="personalized">{{ t('coupons.personalized') }}</option>
                </select>
              </div>              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  {{ t('common.status') }}
                </label>
                <select
                  v-model="filters.status"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="">{{ t('admin.coupons.all_statuses') }}</option>
                  <option value="active">{{ t('admin.coupons.active') }}</option>
                  <option value="expired">{{ t('admin.coupons.expired') }}</option>
                  <option value="exhausted">{{ t('admin.coupons.exhausted') }}</option>
                </select>
              </div>
              <div class="flex items-end">                <button
                  @click="clearFilters"
                  class="w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  {{ t('admin.coupons.clear_filters') }}
                </button>
              </div>
            </div>          </div>

          <!-- Pestañas de navegación -->
          <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
              <button
                @click="activeTab = 'list'"
                :class="[
                  activeTab === 'list'
                    ? 'border-indigo-500 text-indigo-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                ]"
              >
                <i class="pi pi-list mr-2"></i>
                Lista de Cupones
              </button>
              <button
                @click="activeTab = 'advanced'"
                :class="[
                  activeTab === 'advanced'
                    ? 'border-indigo-500 text-indigo-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                ]"
              >                <i class="pi pi-cog mr-2"></i>
                {{ t('admin.coupons.advanced_management') }}
              </button>
            </nav>
          </div>

          <!-- Contenido de la pestaña Lista -->
          <div v-show="activeTab === 'list'" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ t('coupons.code') }}
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ t('coupons.type') }}
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ t('coupons.discount') }}
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ t('admin.coupons.validity') }}
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ t('admin.coupons.uses') }}
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ t('common.status') }}
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ t('admin.coupons.client') }}
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ t('common.actions') }}
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="coupon in filteredCoupons" :key="coupon.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="text-sm font-medium text-gray-900">
                        {{ coupon.code }}
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      :class="[
                        'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                        coupon.type === 'global' 
                          ? 'bg-blue-100 text-blue-800' 
                          : 'bg-purple-100 text-purple-800'
                      ]"
                    >
                      {{ coupon.type === 'global' ? 'Global' : 'Personalizado' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ formatDiscount(coupon) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div>
                      <div>{{ formatDate(coupon.valid_from) }}</div>
                      <div>{{ formatDate(coupon.valid_to) }}</div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ coupon.used_count }} / {{ coupon.max_uses || '∞' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      :class="[
                        'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                        getStatusClass(coupon)
                      ]"
                    >
                      {{ getStatusText(coupon) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ coupon.client?.name || '-' }}
                  </td>                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end space-x-1">
                      <Link
                        :href="route('admin.discount-coupons.show', coupon.id)"
                        class="inline-flex items-center justify-center p-2 text-indigo-600 bg-indigo-50 hover:text-white hover:bg-indigo-600 rounded-md transition-all duration-200 border border-indigo-200"
                        title="Ver detalles"
                      >
                        <EyeIcon class="w-5 h-5" />
                      </Link>                      <Link
                        :href="route('admin.discount-coupons.edit', coupon.id)"
                        class="inline-flex items-center justify-center p-2 text-yellow-600 bg-yellow-50 hover:text-white hover:bg-yellow-600 rounded-md transition-all duration-200 border border-yellow-200"
                        :title="t('common.edit')"
                      >
                        <PencilIcon class="w-5 h-5" />
                      </Link>
                      <button
                        @click="confirmDelete(coupon)"
                        class="inline-flex items-center justify-center p-2 text-red-600 bg-red-50 hover:text-white hover:bg-red-600 rounded-md transition-all duration-200 border border-red-200"
                        :title="t('common.delete')"
                      >
                        <TrashIcon class="w-5 h-5" />
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Paginación -->          <div v-if="coupons.data.length === 0" class="p-6 text-center text-gray-500">
            {{ t('admin.coupons.no_coupons_found') }}
          </div>
            <div v-else class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <Pagination :links="coupons.links" />
          </div>
          </div>

          <!-- Contenido de la pestaña Gestión Avanzada -->
          <div v-show="activeTab === 'advanced'" class="p-6">
            <!-- Panel de estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
              <div class="bg-gray-50 overflow-hidden shadow rounded-lg p-6">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                      <i class="pi pi-ticket text-blue-600"></i>
                    </div>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500">Total Cupones</div>
                    <div class="text-2xl font-bold text-gray-900">{{ stats.total_coupons || 0 }}</div>
                  </div>
                </div>
              </div>

              <div class="bg-gray-50 overflow-hidden shadow rounded-lg p-6">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                      <i class="pi pi-check-circle text-green-600"></i>
                    </div>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500">Cupones Activos</div>
                    <div class="text-2xl font-bold text-gray-900">{{ stats.active_coupons || 0 }}</div>
                  </div>
                </div>
              </div>

              <div class="bg-gray-50 overflow-hidden shadow rounded-lg p-6">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                      <i class="pi pi-clock text-yellow-600"></i>
                    </div>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500">Cupones Usados</div>
                    <div class="text-2xl font-bold text-gray-900">{{ stats.used_coupons || 0 }}</div>
                  </div>
                </div>
              </div>

              <div class="bg-gray-50 overflow-hidden shadow rounded-lg p-6">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                      <i class="pi pi-euro text-purple-600"></i>
                    </div>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500">Ahorro Total</div>
                    <div class="text-2xl font-bold text-gray-900">€{{ stats.total_savings || 0 }}</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Herramientas de gestión -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">              <!-- Generación masiva de cupones -->
              <div class="bg-white border border-gray-200 rounded-lg shadow-sm form-section">
                <div class="p-6">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="pi pi-copy mr-2"></i>
                    Generación Masiva de Cupones
                  </h3>
                  
                  <form @submit.prevent="generateBulkCoupons" class="space-y-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Cantidad</label>
                      <input 
                        v-model="bulkForm.quantity" 
                        type="number" 
                        min="1" 
                        max="100"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                      />
                    </div>
                    
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Prefijo del código</label>
                      <input 
                        v-model="bulkForm.prefix" 
                        type="text" 
                        placeholder="Ej: VERANO2025"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      />
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                      <div>
                        <label class="block text-sm font-medium text-gray-700">Tipo de descuento</label>
                        <select 
                          v-model="bulkForm.discount_type"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                          <option value="percentage">Porcentaje</option>
                          <option value="fixed">Cantidad fija</option>
                        </select>
                      </div>
                      
                      <div>
                        <label class="block text-sm font-medium text-gray-700">Valor</label>
                        <input 
                          v-model="bulkForm.value" 
                          type="number" 
                          :step="bulkForm.discount_type === 'percentage' ? '1' : '0.01'"
                          :max="bulkForm.discount_type === 'percentage' ? '100' : ''"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                          required
                        />
                      </div>
                    </div>
                    
                    <button 
                      type="submit" 
                      :disabled="bulkForm.processing"
                      class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                    >
                      <span v-if="bulkForm.processing" class="inline-flex items-center">
                        <i class="pi pi-spin pi-spinner mr-2"></i>
                        Generando...
                      </span>
                      <span v-else>Generar Cupones</span>
                    </button>
                  </form>
                </div>
              </div>              <!-- Herramientas de análisis -->
              <div class="bg-white border border-gray-200 rounded-lg shadow-sm form-section">
                <div class="p-6">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="pi pi-chart-bar mr-2"></i>
                    Herramientas de Análisis
                  </h3>
                  
                  <div class="space-y-4">
                    <button 
                      @click="exportCoupons"
                      class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                      <i class="pi pi-download mr-2"></i>
                      Exportar Cupones
                    </button>
                    
                    <button 
                      @click="generateReport"
                      class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                      <i class="pi pi-file-pdf mr-2"></i>
                      Generar Reporte
                    </button>
                    
                    <button 
                      @click="cleanupExpired"
                      class="w-full flex justify-center py-2 px-4 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    >
                      <i class="pi pi-trash mr-2"></i>
                      Limpiar Expirados
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
   

    <!-- Modal de confirmación de eliminación -->
    <ConfirmationModal :show="showDeleteModal" @close="showDeleteModal = false">
      <template #title>
        Eliminar Cupón de Descuento
      </template>

      <template #content>
        ¿Estás seguro de que deseas eliminar el cupón "{{ couponToDelete?.code }}"? 
        Esta acción no se puede deshacer.
      </template>

      <template #footer>
        <SecondaryButton @click="showDeleteModal = false">
          Cancelar
        </SecondaryButton>

        <DangerButton
          class="ml-3"
          :class="{ 'opacity-25': deleteForm.processing }"
          :disabled="deleteForm.processing"
          @click="deleteCoupon"
        >
          Eliminar
        </DangerButton>
      </template>
    </ConfirmationModal>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import axios from 'axios'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import DangerButton from '@/Components/DangerButton.vue'
import { 
  PlusIcon, 
  EyeIcon, 
  PencilIcon, 
  TrashIcon 
} from '@heroicons/vue/24/outline'

const { t } = useI18n()

const props = defineProps({
  coupons: Object
})

// Estado de las pestañas
const activeTab = ref('list')

// Estadísticas (simuladas por ahora)
const stats = ref({
  total_coupons: 0,
  active_coupons: 0,
  used_coupons: 0,
  total_savings: 0
})

// Formulario para generación masiva
const bulkForm = useForm({
  quantity: 10,
  prefix: '',
  discount_type: 'percentage',
  value: 10,
  processing: false
})

const filters = ref({
  search: '',
  type: '',
  status: ''
})

const showDeleteModal = ref(false)
const couponToDelete = ref(null)

const deleteForm = useForm({})

const filteredCoupons = computed(() => {
  let filtered = props.coupons.data

  if (filters.value.search) {
    filtered = filtered.filter(coupon => 
      coupon.code.toLowerCase().includes(filters.value.search.toLowerCase())
    )
  }

  if (filters.value.type) {
    filtered = filtered.filter(coupon => coupon.type === filters.value.type)
  }

  if (filters.value.status) {
    filtered = filtered.filter(coupon => {
      const status = getCouponStatus(coupon)
      return status === filters.value.status
    })
  }

  return filtered
})

const formatDiscount = (coupon) => {
  if (coupon.discount_type === 'percentage') {
    return `${coupon.value}%`
  }
  return `€${coupon.value}`
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('es-ES')
}

const getCouponStatus = (coupon) => {
  const today = new Date()
  const validFrom = new Date(coupon.valid_from)
  const validTo = new Date(coupon.valid_to)

  if (today < validFrom || today > validTo) {
    return 'expired'
  }

  if (coupon.max_uses && coupon.used_count >= coupon.max_uses) {
    return 'exhausted'
  }

  return 'active'
}

const getStatusClass = (coupon) => {
  const status = getCouponStatus(coupon)
  
  switch (status) {
    case 'active':
      return 'bg-green-100 text-green-800'
    case 'expired':
      return 'bg-red-100 text-red-800'
    case 'exhausted':
      return 'bg-yellow-100 text-yellow-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const getStatusText = (coupon) => {
  const status = getCouponStatus(coupon)
  
  switch (status) {
    case 'active':
      return 'Activo'
    case 'expired':
      return 'Expirado'
    case 'exhausted':
      return 'Agotado'
    default:
      return 'Inactivo'
  }
}

const clearFilters = () => {
  filters.value = {
    search: '',
    type: '',
    status: ''
  }
}

const confirmDelete = (coupon) => {
  couponToDelete.value = coupon
  showDeleteModal.value = true
}

const deleteCoupon = () => {
  deleteForm.delete(route('admin.discount-coupons.destroy', couponToDelete.value.id), {
    onSuccess: () => {
      showDeleteModal.value = false
      couponToDelete.value = null
    }
  })
}

// Funciones para gestión avanzada
const generateBulkCoupons = async () => {
  bulkForm.processing = true
  
  try {
    await bulkForm.post(route('admin.discount-coupons.bulk-generate'), {
      onSuccess: () => {
        // Reiniciar formulario
        bulkForm.reset()
        // Recargar estadísticas
        loadStats()
      }
    })
  } catch (error) {
    console.error('Error generando cupones masivos:', error)
  } finally {
    bulkForm.processing = false
  }
}

const exportCoupons = () => {
  window.open(route('admin.discount-coupons.export'), '_blank')
}

const generateReport = () => {
  window.open(route('admin.discount-coupons.report'), '_blank')
}

const cleanupExpired = async () => {
  if (confirm('¿Estás seguro de que quieres eliminar todos los cupones expirados?')) {
    try {
      await axios.post(route('admin.discount-coupons.cleanup'))
      loadStats()
    } catch (error) {
      console.error('Error limpiando cupones expirados:', error)
    }
  }
}

const loadStats = async () => {
  try {
    const response = await axios.get(route('admin.discount-coupons.stats'))
    stats.value = response.data
  } catch (error) {
    console.error('Error cargando estadísticas:', error)
  }
}

// Cargar estadísticas al montar el componente
watch(activeTab, (newTab) => {
  if (newTab === 'advanced') {
    loadStats()
  }
})
</script>
