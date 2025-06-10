<template>
  <div class="discount-coupon-validator">
    <div class="mb-4">
      <label for="coupon-code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        {{ $t('coupons.discountCode') }}
      </label>
      <div class="flex rounded-md shadow-sm">
        <input
          id="coupon-code"
          v-model="couponCode"
          type="text"
          :placeholder="$t('coupons.enterDiscountCode')"
          class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
          :class="{
            'border-red-300': validationState === 'error',
            'border-green-300': validationState === 'valid'
          }"
          @input="handleInput"
          @keyup.enter="validateCoupon"
        />
        <button
          type="button"
          @click="validateCoupon"
          :disabled="isValidating || !couponCode.trim()"
          class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-500 text-sm hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-600 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
        >
          <svg v-if="isValidating" class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <i v-else class="pi pi-check"></i>
          {{ $t('coupons.validate') }}
        </button>
      </div>
    </div>

    <!-- Estado de validación -->
    <div v-if="validationMessage" class="mb-4">
      <div 
        class="p-3 rounded-md text-sm"
        :class="{
          'bg-red-50 text-red-700 border border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800': validationState === 'error',
          'bg-green-50 text-green-700 border border-green-200 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800': validationState === 'valid',
          'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800': validationState === 'info'
        }"
      >
        <div class="flex items-center">
          <i 
            class="mr-2"
            :class="{
              'pi pi-exclamation-triangle': validationState === 'error',
              'pi pi-check-circle': validationState === 'valid',
              'pi pi-info-circle': validationState === 'info'
            }"
          ></i>
          {{ validationMessage }}
        </div>
      </div>
    </div>    <!-- Detalles del cupón válido -->
    <div v-if="validCoupon && validationState === 'valid'" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-4">
      <h4 class="font-semibold text-gray-900 dark:text-white mb-3">{{ $t('coupons.discountDetails') }}</h4>
      <div class="grid grid-cols-2 gap-4 text-sm">
        <div>
          <span class="text-gray-500 dark:text-gray-400">{{ $t('coupons.code') }}:</span>
          <span class="font-medium text-gray-900 dark:text-white ml-2">{{ validCoupon.code }}</span>
        </div>
        <div>
          <span class="text-gray-500 dark:text-gray-400">{{ $t('coupons.type') }}:</span>
          <span class="font-medium text-gray-900 dark:text-white ml-2">
            {{ validCoupon.type === 'global' ? $t('coupons.global') : $t('coupons.personalized') }}
          </span>
        </div>
        <div>
          <span class="text-gray-500 dark:text-gray-400">{{ $t('coupons.discount') }}:</span>
          <span class="font-medium text-green-600 dark:text-green-400 ml-2">
            {{ validCoupon.discount_type === 'percentage' ? validCoupon.value + '%' : '€' + validCoupon.value }}
          </span>
        </div>
        <div>
          <span class="text-gray-500 dark:text-gray-400">{{ $t('coupons.validUntil') }}:</span>
          <span class="font-medium text-gray-900 dark:text-white ml-2">{{ formatDate(validCoupon.valid_to) }}</span>
        </div>
        <div v-if="validCoupon.max_uses">
          <span class="text-gray-500 dark:text-gray-400">{{ $t('coupons.remainingUses') }}:</span>
          <span class="font-medium text-gray-900 dark:text-white ml-2">
            {{ validCoupon.max_uses - validCoupon.used_count }}
          </span>
        </div>
      </div>
      
      <!-- Botón para aplicar cupón -->
      <div class="mt-4">
        <button
          @click="applyCoupon"
          class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200"
        >
          {{ $t('coupons.applyDiscount') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, defineEmits } from 'vue'
import { router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
  clientId: {
    type: Number,
    default: null
  },
  autoValidate: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['couponValidated', 'couponApplied'])

const couponCode = ref('')
const isValidating = ref(false)
const validationState = ref('') // 'valid', 'error', 'info', ''
const validationMessage = ref('')
const validCoupon = ref(null)

let validationTimeout = null

const handleInput = () => {
  validationState.value = ''
  validationMessage.value = ''
  validCoupon.value = null
  
  if (props.autoValidate && couponCode.value.length >= 3) {
    clearTimeout(validationTimeout)
    validationTimeout = setTimeout(() => {
      validateCoupon()
    }, 500)
  }
}

const validateCoupon = async () => {
  if (!couponCode.value.trim()) {
    validationState.value = 'error'
    validationMessage.value = t('coupons.validation.enterCode')
    return
  }

  isValidating.value = true
  validationState.value = 'info'
  validationMessage.value = t('coupons.validation.validating')

  try {
    const response = await fetch(route('api.discount-coupons.validate'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({
        code: couponCode.value.trim(),
        client_id: props.clientId
      })
    })

    const data = await response.json()

    if (data.valid) {
      validationState.value = 'valid'
      validationMessage.value = data.message
      validCoupon.value = data.coupon
      emit('couponValidated', data.coupon)
    } else {
      validationState.value = 'error'
      validationMessage.value = data.message
      validCoupon.value = null
      emit('couponValidated', null)
    }
  } catch (error) {
    console.error('Error validating coupon:', error)
    validationState.value = 'error'
    validationMessage.value = t('coupons.validation.error')
    validCoupon.value = null
    emit('couponValidated', null)
  } finally {
    isValidating.value = false
  }
}

const applyCoupon = () => {
  if (validCoupon.value) {
    emit('couponApplied', validCoupon.value)
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString(t('coupons.dateLocale'), {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

defineExpose({
  validateCoupon,
  clearCoupon: () => {
    couponCode.value = ''
    validationState.value = ''
    validationMessage.value = ''
    validCoupon.value = null
  },
  getCoupon: () => validCoupon.value
})
</script>

<style scoped>
.discount-coupon-validator {
  max-width: 28rem;
}
</style>
