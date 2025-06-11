<template>
  <AdminLayout :title="t('admin.coupons.advanced_management_title')">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ t('admin.coupons.advanced_management_title') }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        <!-- Panel de estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                  <i class="pi pi-ticket-alt text-blue-600 dark:text-blue-300"></i>
                </div>
              </div>
              <div class="ml-4">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('admin.coupons.total_coupons') }}</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.total_coupons }}</div>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                  <i class="pi pi-check-circle text-green-600 dark:text-green-300"></i>
                </div>
              </div>
              <div class="ml-4">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('admin.coupons.active_coupons') }}</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.active_coupons }}</div>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center">
                  <i class="pi pi-clock text-yellow-600 dark:text-yellow-300"></i>
                </div>
              </div>
              <div class="ml-4">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('admin.coupons.used_coupons') }}</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.used_coupons }}</div>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                  <i class="pi pi-euro text-purple-600 dark:text-purple-300"></i>
                </div>
              </div>
              <div class="ml-4">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ t('admin.coupons.total_savings') }}</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">€{{ stats.total_savings }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Herramientas de gestión -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Generación masiva de cupones -->
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                <i class="pi pi-copy mr-2"></i>
                {{ t('admin.coupons.bulk_generation_title') }}
              </h3>
              
              <form @submit.prevent="generateBulkCoupons" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                      {{ t('admin.coupons.count') }}
                    </label>
                    <input
                      v-model="bulkForm.count"
                      type="number"
                      min="1"
                      max="100"
                      required
                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                      {{ t('admin.coupons.prefix') }} ({{ t('common.optional') }})
                    </label>
                    <input
                      v-model="bulkForm.prefix"
                      type="text"
                      maxlength="10"
                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      placeholder="BULK"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                      {{ t('admin.coupons.discount_type') }}
                    </label>
                    <select
                      v-model="bulkForm.discount_type"
                      required
                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                      <option value="percentage">{{ t('admin.coupons.percentage') }}</option>
                      <option value="fixed">{{ t('admin.coupons.fixed_amount') }}</option>
                    </select>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                      {{ t('admin.coupons.discount_value') }}
                    </label>
                    <input
                      v-model="bulkForm.value"
                      type="number"
                      step="0.01"
                      min="0"
                      :max="bulkForm.discount_type === 'percentage' ? 100 : null"
                      required
                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                      {{ t('admin.coupons.valid_from') }}
                    </label>
                    <input
                      v-model="bulkForm.valid_from"
                      type="date"
                      required
                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                      {{ t('admin.coupons.valid_until') }}
                    </label>
                    <input
                      v-model="bulkForm.valid_to"
                      type="date"
                      required
                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                  </div>

                  <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                      {{ t('admin.coupons.max_uses') }} ({{ t('common.optional') }})
                    </label>
                    <input
                      v-model="bulkForm.max_uses"
                      type="number"
                      min="1"
                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      :placeholder="t('admin.coupons.unlimited')"
                    />
                  </div>
                </div>

                <div class="flex justify-end">
                  <button
                    type="submit"
                    :disabled="bulkForm.processing"
                    class="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200"
                  >
                    <i class="pi pi-plus mr-2"></i>
                    {{ t('admin.coupons.generate_coupons') }}
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- Validador de cupones -->
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                <i class="pi pi-search mr-2"></i>
                {{ t('admin.coupons.validator_title') }}
              </h3>
              
              <DiscountCouponValidator
                :auto-validate="true"
                @coupon-validated="onCouponValidated"
              />
            </div>
          </div>
        </div>

        <!-- Cupones recientes -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                <i class="pi pi-list mr-2"></i>
                {{ t('admin.coupons.recent_coupons') }}
              </h3>
              <Link
                :href="route('admin.discount-coupons.index')"
                class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm font-medium"
              >
                {{ t('admin.coupons.view_all') }} →
              </Link>
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      {{ t('admin.coupons.code') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      {{ t('admin.coupons.type') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      {{ t('admin.coupons.discount') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      {{ t('admin.coupons.status_title') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                      {{ t('admin.coupons.uses') }}
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                  <tr v-for="coupon in recentCoupons" :key="coupon.id">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                      {{ coupon.code }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                      <span 
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="coupon.type === 'global' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300'"
                      >
                        {{ coupon.type === 'global' ? t('admin.coupons.global') : t('admin.coupons.custom') }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                      {{ coupon.discount_type === 'percentage' ? coupon.value + '%' : '€' + coupon.value }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                      <span 
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="getStatusClass(coupon.status)"
                      >
                        {{ getStatusLabel(coupon.status) }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                      {{ coupon.used_count }} / {{ coupon.max_uses || '∞' }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast para notificaciones -->
    <div
      v-if="notification.show"
      class="fixed top-4 right-4 z-50 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg p-4 max-w-sm"
      :class="{
        'border-green-200 dark:border-green-700': notification.type === 'success',
        'border-red-200 dark:border-red-700': notification.type === 'error'
      }"
    >
      <div class="flex items-center">
        <i
          class="mr-3"
          :class="{
            'pi pi-check-circle text-green-600 dark:text-green-400': notification.type === 'success',
            'pi pi-exclamation-triangle text-red-600 dark:text-red-400': notification.type === 'error'
          }"
        ></i>
        <span class="text-gray-900 dark:text-white">{{ notification.message }}</span>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DiscountCouponValidator from '@/Components/DiscountCouponValidator.vue'

const { t } = useI18n()

const props = defineProps({
  stats: Object,
  recentCoupons: Array
})

const bulkForm = useForm({
  count: 10,
  prefix: 'BULK',
  discount_type: 'percentage',
  value: 10,
  valid_from: new Date().toISOString().split('T')[0],
  valid_to: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
  max_uses: null
})

const notification = reactive({
  show: false,
  type: 'success',
  message: ''
})

const showNotification = (type, message) => {
  notification.type = type
  notification.message = message
  notification.show = true
  
  setTimeout(() => {
    notification.show = false
  }, 5000)
}

const generateBulkCoupons = () => {
  bulkForm.post(route('admin.discount-coupons.bulk-generate'), {
    onSuccess: (page) => {
      showNotification('success', t('admin.coupons.bulk_generated_success', { count: bulkForm.count }))
      bulkForm.reset()
      // Recargar datos
      router.reload({ only: ['stats', 'recentCoupons'] })
    },
    onError: (errors) => {
      showNotification('error', t('admin.coupons.generation_error'))
    }
  })
}

const onCouponValidated = (coupon) => {
  if (coupon) {
    showNotification('success', t('admin.coupons.coupon_validated', { code: coupon.code }))
  }
}

const getStatusClass = (status) => {
  switch (status) {
    case 'active':
      return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
    case 'expired':
      return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
    case 'depleted':
      return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
    case 'inactive':
      return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
  }
}

const getStatusLabel = (status) => {
  switch (status) {
    case 'active':
      return t('admin.coupons.status.active')
    case 'expired':
      return t('admin.coupons.status.expired')
    case 'depleted':
      return t('admin.coupons.status.depleted')
    case 'inactive':
      return t('admin.coupons.status.inactive')
    default:
      return t('admin.coupons.status.unknown')
  }
}
</script>
