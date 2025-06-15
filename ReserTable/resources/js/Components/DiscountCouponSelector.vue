<template>
  <div class="bg-white rounded-lg border border-gray-200 p-6">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-medium text-gray-900">Códigos de Descuento</h3>
      <TagIcon class="w-5 h-5 text-gray-400" />
    </div>

    <!-- Cupones disponibles -->
    <div v-if="availableCoupons.length > 0" class="mb-6">
      <h4 class="text-sm font-medium text-gray-700 mb-3">Cupones Disponibles</h4>
      <div class="grid gap-3">
        <div
          v-for="coupon in availableCoupons"
          :key="coupon.id"
          class="border border-gray-200 rounded-lg p-3 hover:border-indigo-300 cursor-pointer transition-colors"
          :class="{ 'border-indigo-500 bg-indigo-50': selectedCoupon?.id === coupon.id }"
          @click="selectCoupon(coupon)"
        >
          <div class="flex items-center justify-between">
            <div>
              <div class="font-medium text-sm text-gray-900">{{ coupon.code }}</div>
              <div class="text-xs text-gray-500">
                {{ formatDiscount(coupon) }} de descuento
              </div>
            </div>
            <div class="text-right">
              <div class="text-xs text-gray-500">
                Válido hasta {{ formatDate(coupon.valid_to) }}
              </div>
              <div v-if="selectedCoupon?.id === coupon.id" class="mt-1">
                <CheckIcon class="w-4 h-4 text-indigo-600" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Aplicar código manualmente -->
    <div class="border-t border-gray-200 pt-4">
      <label for="coupon-code" class="block text-sm font-medium text-gray-700 mb-2">
        ¿Tienes un código de descuento?
      </label>
      <div class="flex space-x-3">
        <div class="flex-1">
          <input
            id="coupon-code"
            v-model="manualCode"
            type="text"
            placeholder="Ingresa tu código aquí"
            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            :class="{ 'border-red-300': validationError }"
            @keyup.enter="validateManualCode"
            @input="clearValidationError"
          />
          <div v-if="validationError" class="mt-1 text-sm text-red-600">
            {{ validationError }}
          </div>
        </div>
        <button
          @click="validateManualCode"
          :disabled="!manualCode.trim() || isValidating"
          class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="isValidating">Validando...</span>
          <span v-else>Aplicar</span>
        </button>
      </div>
    </div>

    <!-- Código aplicado -->
    <div v-if="appliedCoupon" class="mt-4 p-3 bg-green-50 border border-green-200 rounded-md">
      <div class="flex items-center">
        <CheckCircleIcon class="w-5 h-5 text-green-600 mr-2" />
        <div class="flex-1">
          <div class="text-sm font-medium text-green-800">
            Código "{{ appliedCoupon.code }}" aplicado
          </div>
          <div class="text-sm text-green-600">
            Descuento: {{ formatDiscount(appliedCoupon) }}
          </div>
        </div>
        <button
          @click="removeCoupon"
          class="text-green-600 hover:text-green-800"
        >
          <XMarkIcon class="w-4 h-4" />
        </button>
      </div>
    </div>

    <!-- Cálculo del descuento -->
    <div v-if="appliedCoupon && subtotal > 0" class="mt-4 pt-4 border-t border-gray-200">
      <div class="space-y-2 text-sm">
        <div class="flex justify-between">
          <span class="text-gray-600">Subtotal:</span>
          <span class="font-medium">€{{ subtotal.toFixed(2) }}</span>
        </div>
        <div class="flex justify-between text-green-600">
          <span>Descuento ({{ appliedCoupon.code }}):</span>
          <span class="font-medium">-€{{ discountAmount.toFixed(2) }}</span>
        </div>
        <div class="flex justify-between text-lg font-semibold text-gray-900 pt-2 border-t border-gray-200">
          <span>Total:</span>
          <span>€{{ finalTotal.toFixed(2) }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import {
  TagIcon,
  CheckIcon,
  CheckCircleIcon,
  XMarkIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  clientId: {
    type: Number,
    default: null
  },
  subtotal: {
    type: Number,
    default: 0
  }
})

const emit = defineEmits(['coupon-applied', 'coupon-removed'])

// Estado
const availableCoupons = ref([])
const selectedCoupon = ref(null)
const appliedCoupon = ref(null)
const manualCode = ref('')
const validationError = ref('')
const isValidating = ref(false)

// Computed
const discountAmount = computed(() => {
  if (!appliedCoupon.value || props.subtotal <= 0) return 0
  
  if (appliedCoupon.value.discount_type === 'percentage') {
    return (props.subtotal * appliedCoupon.value.value) / 100
  } else {
    return Math.min(appliedCoupon.value.value, props.subtotal)
  }
})

const finalTotal = computed(() => {
  return Math.max(0, props.subtotal - discountAmount.value)
})

// Métodos
const loadAvailableCoupons = async () => {
  if (!props.clientId) return
  
  try {
    router.get('/public/coupons/available', {
      client_id: props.clientId
    }, {
      preserveState: true,
      preserveScroll: true,
      only: ['availableCoupons'],
      onSuccess: (page) => {
        availableCoupons.value = page.props.availableCoupons || []
      }
    })
  } catch (error) {
    console.error('Error cargando cupones:', error)
  }
}

const selectCoupon = (coupon) => {
  if (selectedCoupon.value?.id === coupon.id) {
    selectedCoupon.value = null
    appliedCoupon.value = null
    emit('coupon-removed')
  } else {
    selectedCoupon.value = coupon
    appliedCoupon.value = coupon
    manualCode.value = coupon.code
    validationError.value = ''
    emit('coupon-applied', {
      coupon: coupon,
      discountAmount: discountAmount.value
    })
  }
}

const validateManualCode = async () => {
  if (!manualCode.value.trim()) return
  
  isValidating.value = true
  validationError.value = ''
  
  try {
    router.post('/public/coupons/validate', {
      code: manualCode.value.trim(),
      client_id: props.clientId
    }, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: (page) => {
        const response = page.props.validationResult
        if (response && response.valid) {
          appliedCoupon.value = response.coupon
          selectedCoupon.value = response.coupon
          emit('coupon-applied', {
            coupon: response.coupon,
            discountAmount: discountAmount.value
          })
        }
        isValidating.value = false
      },
      onError: (errors) => {
        if (errors.message) {
          validationError.value = errors.message
        } else {
          validationError.value = 'Error al validar el código de descuento'
        }
        isValidating.value = false
      }
    })
  } catch (error) {
    validationError.value = 'Error al validar el código de descuento'
    isValidating.value = false
  }
}
}

const removeCoupon = () => {
  appliedCoupon.value = null
  selectedCoupon.value = null
  manualCode.value = ''
  validationError.value = ''
  emit('coupon-removed')
}

const clearValidationError = () => {
  validationError.value = ''
}

const formatDiscount = (coupon) => {
  if (coupon.discount_type === 'percentage') {
    return `${coupon.value}%`
  }
  return `€${coupon.value}`
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('es-ES')
}

// Watchers
watch(() => props.clientId, () => {
  loadAvailableCoupons()
}, { immediate: true })

watch(() => props.subtotal, () => {
  if (appliedCoupon.value) {
    emit('coupon-applied', {
      coupon: appliedCoupon.value,
      discountAmount: discountAmount.value
    })
  }
})

// Exponer métodos para el componente padre
defineExpose({
  getAppliedCoupon: () => appliedCoupon.value,
  getDiscountAmount: () => discountAmount.value,
  removeCoupon
})
</script>
