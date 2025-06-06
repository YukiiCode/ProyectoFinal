<template>
  <AdminLayout title="Detalles del Cupón">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Detalles del Cupón de Descuento
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
              <div class="flex items-center space-x-4">
                <div>
                  <h3 class="text-2xl font-bold text-gray-900">{{ coupon.code }}</h3>
                  <p class="text-sm text-gray-600">
                    Creado el {{ formatDate(coupon.created_at) }}
                  </p>
                </div>
                <span
                  :class="[
                    'inline-flex px-3 py-1 text-sm font-semibold rounded-full',
                    getStatusClass(coupon)
                  ]"
                >
                  {{ getStatusText(coupon) }}
                </span>
              </div>
              <div class="flex space-x-3">
                <Link
                  :href="route('admin.discount-coupons.edit', coupon.id)"
                  class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  <PencilIcon class="w-4 h-4 mr-2" />
                  Editar
                </Link>
                <Link
                  :href="route('admin.discount-coupons.index')"
                  class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  <ArrowLeftIcon class="w-4 h-4 mr-2" />
                  Volver
                </Link>
              </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
              <!-- Información principal -->
              <div class="lg:col-span-2 space-y-6">
                <!-- Detalles del descuento -->
                <div class="bg-gray-50 rounded-lg p-6">
                  <h4 class="text-lg font-medium text-gray-900 mb-4">Información del Descuento</h4>
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Tipo de Cupón</dt>
                      <dd class="mt-1">
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
                      </dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Tipo de Descuento</dt>
                      <dd class="mt-1 text-sm text-gray-900">
                        {{ coupon.discount_type === 'percentage' ? 'Porcentaje' : 'Cantidad Fija' }}
                      </dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Valor del Descuento</dt>
                      <dd class="mt-1 text-2xl font-bold text-indigo-600">
                        {{ formatDiscount(coupon) }}
                      </dd>
                    </div>
                    <div v-if="coupon.client">
                      <dt class="text-sm font-medium text-gray-500">Cliente Asignado</dt>
                      <dd class="mt-1 text-sm text-gray-900">
                        <div>{{ coupon.client.name }}</div>
                        <div class="text-gray-500">{{ coupon.client.email }}</div>
                      </dd>
                    </div>
                  </div>
                </div>

                <!-- Validez y uso -->
                <div class="bg-gray-50 rounded-lg p-6">
                  <h4 class="text-lg font-medium text-gray-900 mb-4">Validez y Uso</h4>
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Válido desde</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ formatDate(coupon.valid_from) }}</dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Válido hasta</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ formatDate(coupon.valid_to) }}</dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Días restantes</dt>
                      <dd class="mt-1 text-sm text-gray-900">
                        <span :class="getDaysRemainingClass()">
                          {{ getDaysRemaining() }}
                        </span>
                      </dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Máximo de usos</dt>
                      <dd class="mt-1 text-sm text-gray-900">
                        {{ coupon.max_uses || 'Ilimitados' }}
                      </dd>
                    </div>
                  </div>
                </div>

                <!-- Historial de uso (placeholder - se puede implementar después) -->
                <div class="bg-gray-50 rounded-lg p-6">
                  <h4 class="text-lg font-medium text-gray-900 mb-4">Historial de Uso</h4>
                  <div class="text-center py-8 text-gray-500">
                    <GiftIcon class="w-12 h-12 mx-auto mb-4 text-gray-300" />
                    <p>El historial detallado de uso estará disponible próximamente</p>
                  </div>
                </div>
              </div>

              <!-- Estadísticas -->
              <div class="space-y-6">
                <!-- Estadísticas de uso -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                  <h4 class="text-lg font-medium text-gray-900 mb-4">Estadísticas</h4>
                  <div class="space-y-4">
                    <!-- Usos -->
                    <div>
                      <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Usos</span>
                        <span class="font-medium">
                          {{ coupon.used_count }} / {{ coupon.max_uses || '∞' }}
                        </span>
                      </div>
                      <div class="mt-2 bg-gray-200 rounded-full h-2">
                        <div
                          class="bg-indigo-600 h-2 rounded-full transition-all duration-300"
                          :style="{ width: getUsagePercentage() + '%' }"
                        ></div>
                      </div>
                    </div>

                    <!-- Días de validez -->
                    <div>
                      <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Validez</span>
                        <span class="font-medium">{{ getValidityPercentage() }}%</span>
                      </div>
                      <div class="mt-2 bg-gray-200 rounded-full h-2">
                        <div
                          :class="[
                            'h-2 rounded-full transition-all duration-300',
                            getValidityPercentage() > 50 ? 'bg-green-600' : 
                            getValidityPercentage() > 20 ? 'bg-yellow-600' : 'bg-red-600'
                          ]"
                          :style="{ width: getValidityPercentage() + '%' }"
                        ></div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Acciones rápidas -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                  <h4 class="text-lg font-medium text-gray-900 mb-4">Acciones</h4>
                  <div class="space-y-3">
                    <button
                      @click="copyCode"
                      class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                      <ClipboardDocumentIcon class="w-4 h-4 mr-2" />
                      Copiar Código
                    </button>
                    
                    <Link
                      v-if="coupon.type === 'global'"
                      :href="getShareUrl()"
                      target="_blank"
                      class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                      <ShareIcon class="w-4 h-4 mr-2" />
                      Generar Enlace
                    </Link>

                    <button
                      @click="confirmDisable"
                      :disabled="!isActive"
                      class="w-full inline-flex items-center justify-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      <XMarkIcon class="w-4 h-4 mr-2" />
                      {{ isActive ? 'Desactivar' : 'Inactivo' }}
                    </button>
                  </div>
                </div>

                <!-- Información adicional -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                  <div class="flex">
                    <InformationCircleIcon class="w-5 h-5 text-blue-400 mt-0.5" />
                    <div class="ml-3">
                      <h4 class="text-sm font-medium text-blue-800">Información</h4>
                      <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                          <li>Los cupones se validan automáticamente al hacer reservas</li>
                          <li>Los cupones expirados no se pueden usar</li>
                          <li>Los cambios se aplican inmediatamente</li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast de confirmación -->
    <div
      v-if="showToast"
      class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg transition-all duration-300 z-50"
    >
      {{ toastMessage }}
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import {
  PencilIcon,
  ArrowLeftIcon,
  ClipboardDocumentIcon,
  ShareIcon,
  XMarkIcon,
  InformationCircleIcon,
  GiftIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  coupon: Object
})

const showToast = ref(false)
const toastMessage = ref('')

const isActive = computed(() => {
  const today = new Date()
  const validFrom = new Date(props.coupon.valid_from)
  const validTo = new Date(props.coupon.valid_to)
  
  return today >= validFrom && today <= validTo && 
         (!props.coupon.max_uses || props.coupon.used_count < props.coupon.max_uses)
})

const formatDiscount = (coupon) => {
  if (coupon.discount_type === 'percentage') {
    return `${coupon.value}%`
  }
  return `€${coupon.value}`
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const getDaysRemaining = () => {
  const today = new Date()
  const validTo = new Date(props.coupon.valid_to)
  const diffTime = validTo - today
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  
  if (diffDays < 0) return 'Expirado'
  if (diffDays === 0) return 'Expira hoy'
  if (diffDays === 1) return '1 día'
  return `${diffDays} días`
}

const getDaysRemainingClass = () => {
  const today = new Date()
  const validTo = new Date(props.coupon.valid_to)
  const diffTime = validTo - today
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  
  if (diffDays < 0) return 'text-red-600 font-medium'
  if (diffDays <= 7) return 'text-yellow-600 font-medium'
  return 'text-green-600 font-medium'
}

const getUsagePercentage = () => {
  if (!props.coupon.max_uses) return 0
  return Math.min(100, (props.coupon.used_count / props.coupon.max_uses) * 100)
}

const getValidityPercentage = () => {
  const validFrom = new Date(props.coupon.valid_from)
  const validTo = new Date(props.coupon.valid_to)
  const today = new Date()
  
  const totalDays = (validTo - validFrom) / (1000 * 60 * 60 * 24)
  const remainingDays = Math.max(0, (validTo - today) / (1000 * 60 * 60 * 24))
  
  return Math.max(0, Math.min(100, (remainingDays / totalDays) * 100))
}

const getStatusClass = (coupon) => {
  if (!isActive.value) {
    return 'bg-red-100 text-red-800'
  }
  return 'bg-green-100 text-green-800'
}

const getStatusText = (coupon) => {
  if (!isActive.value) {
    return 'Inactivo'
  }
  return 'Activo'
}

const copyCode = async () => {
  try {
    await navigator.clipboard.writeText(props.coupon.code)
    showToastMessage('Código copiado al portapapeles')
  } catch (error) {
    showToastMessage('Error al copiar el código')
  }
}

const getShareUrl = () => {
  return `${window.location.origin}/promocion/${props.coupon.code}`
}

const confirmDisable = () => {
  if (confirm('¿Estás seguro de que deseas desactivar este cupón?')) {
    // Implementar lógica de desactivación
    showToastMessage('Funcionalidad de desactivación en desarrollo')
  }
}

const showToastMessage = (message) => {
  toastMessage.value = message
  showToast.value = true
  setTimeout(() => {
    showToast.value = false
  }, 3000)
}
</script>
