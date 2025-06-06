<template>
  <AdminLayout title="Crear Cupón de Descuento">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Crear Cupón de Descuento
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <form @submit.prevent="submit">
            <div class="p-6 space-y-6">
              <!-- Header -->
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-lg font-medium text-gray-900">Nuevo Cupón</h3>
                  <p class="mt-1 text-sm text-gray-600">
                    Crea un nuevo código de descuento para el restaurante
                  </p>
                </div>
                <Link
                  :href="route('admin.discount-coupons.index')"
                  class="text-gray-600 hover:text-gray-900"
                >
                  <ArrowLeftIcon class="w-5 h-5" />
                </Link>
              </div>

              <!-- Información básica -->
              <div class="border-t border-gray-200 pt-6">
                <h4 class="text-md font-medium text-gray-900 mb-4">Información Básica</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <!-- Código -->
                  <div class="md:col-span-2">
                    <label for="code" class="block text-sm font-medium text-gray-700">
                      Código del Cupón *
                    </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <input
                        id="code"
                        v-model="form.code"
                        type="text"
                        required
                        maxlength="50"
                        class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        :class="{ 'border-red-300': form.errors.code }"
                        placeholder="Ej: DESCUENTO20, NAVIDAD2024"
                      />
                      <button
                        type="button"
                        @click="generateRandomCode"
                        class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-500 text-sm hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                      >
                        <ArrowPathIcon class="w-4 h-4" />
                      </button>
                    </div>
                    <div v-if="form.errors.code" class="mt-2 text-sm text-red-600">
                      {{ form.errors.code }}
                    </div>
                    <p class="mt-1 text-sm text-gray-500">
                      El código será convertido automáticamente a mayúsculas
                    </p>
                  </div>

                  <!-- Tipo de cupón -->
                  <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">
                      Tipo de Cupón *
                    </label>
                    <select
                      id="type"
                      v-model="form.type"
                      required
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      :class="{ 'border-red-300': form.errors.type }"
                    >
                      <option value="">Seleccionar tipo</option>
                      <option value="global">Global (Todos los clientes)</option>
                      <option value="personalized">Personalizado (Un cliente específico)</option>
                    </select>
                    <div v-if="form.errors.type" class="mt-2 text-sm text-red-600">
                      {{ form.errors.type }}
                    </div>
                  </div>

                  <!-- Cliente (solo si es personalizado) -->
                  <div v-if="form.type === 'personalized'">
                    <label for="client_id" class="block text-sm font-medium text-gray-700">
                      Cliente *
                    </label>
                    <select
                      id="client_id"
                      v-model="form.client_id"
                      :required="form.type === 'personalized'"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      :class="{ 'border-red-300': form.errors.client_id }"
                    >
                      <option value="">Seleccionar cliente</option>
                      <option v-for="client in clients" :key="client.id" :value="client.id">
                        {{ client.name }} - {{ client.email }}
                      </option>
                    </select>
                    <div v-if="form.errors.client_id" class="mt-2 text-sm text-red-600">
                      {{ form.errors.client_id }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- Configuración del descuento -->
              <div class="border-t border-gray-200 pt-6">
                <h4 class="text-md font-medium text-gray-900 mb-4">Configuración del Descuento</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <!-- Tipo de descuento -->
                  <div>
                    <label for="discount_type" class="block text-sm font-medium text-gray-700">
                      Tipo de Descuento *
                    </label>
                    <select
                      id="discount_type"
                      v-model="form.discount_type"
                      required
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      :class="{ 'border-red-300': form.errors.discount_type }"
                    >
                      <option value="">Seleccionar tipo</option>
                      <option value="percentage">Porcentaje (%)</option>
                      <option value="fixed">Cantidad fija (€)</option>
                    </select>
                    <div v-if="form.errors.discount_type" class="mt-2 text-sm text-red-600">
                      {{ form.errors.discount_type }}
                    </div>
                  </div>

                  <!-- Valor del descuento -->
                  <div>
                    <label for="value" class="block text-sm font-medium text-gray-700">
                      Valor del Descuento *
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <input
                        id="value"
                        v-model="form.value"
                        type="number"
                        step="0.01"
                        min="0"
                        :max="form.discount_type === 'percentage' ? 100 : undefined"
                        required
                        class="block w-full px-3 py-2 pr-12 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        :class="{ 'border-red-300': form.errors.value }"
                        :placeholder="form.discount_type === 'percentage' ? 'Ej: 20' : 'Ej: 5.00'"
                      />
                      <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">
                          {{ form.discount_type === 'percentage' ? '%' : '€' }}
                        </span>
                      </div>
                    </div>
                    <div v-if="form.errors.value" class="mt-2 text-sm text-red-600">
                      {{ form.errors.value }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- Validez y límites -->
              <div class="border-t border-gray-200 pt-6">
                <h4 class="text-md font-medium text-gray-900 mb-4">Validez y Límites</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <!-- Fecha de inicio -->
                  <div>
                    <label for="valid_from" class="block text-sm font-medium text-gray-700">
                      Válido desde *
                    </label>
                    <input
                      id="valid_from"
                      v-model="form.valid_from"
                      type="date"
                      required
                      :min="today"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      :class="{ 'border-red-300': form.errors.valid_from }"
                    />
                    <div v-if="form.errors.valid_from" class="mt-2 text-sm text-red-600">
                      {{ form.errors.valid_from }}
                    </div>
                  </div>

                  <!-- Fecha de fin -->
                  <div>
                    <label for="valid_to" class="block text-sm font-medium text-gray-700">
                      Válido hasta *
                    </label>
                    <input
                      id="valid_to"
                      v-model="form.valid_to"
                      type="date"
                      required
                      :min="form.valid_from || today"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      :class="{ 'border-red-300': form.errors.valid_to }"
                    />
                    <div v-if="form.errors.valid_to" class="mt-2 text-sm text-red-600">
                      {{ form.errors.valid_to }}
                    </div>
                  </div>

                  <!-- Máximo de usos -->
                  <div class="md:col-span-2">
                    <label for="max_uses" class="block text-sm font-medium text-gray-700">
                      Máximo de usos
                    </label>
                    <input
                      id="max_uses"
                      v-model="form.max_uses"
                      type="number"
                      min="1"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      :class="{ 'border-red-300': form.errors.max_uses }"
                      placeholder="Dejar vacío para usos ilimitados"
                    />
                    <div v-if="form.errors.max_uses" class="mt-2 text-sm text-red-600">
                      {{ form.errors.max_uses }}
                    </div>
                    <p class="mt-1 text-sm text-gray-500">
                      Si no se especifica, el cupón podrá usarse ilimitadamente durante su período de validez
                    </p>
                  </div>
                </div>
              </div>

              <!-- Resumen del cupón -->
              <div v-if="isFormValid" class="border-t border-gray-200 pt-6">
                <h4 class="text-md font-medium text-gray-900 mb-4">Resumen del Cupón</h4>
                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                  <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Código:</span>
                    <span class="text-sm font-medium">{{ form.code.toUpperCase() }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Tipo:</span>
                    <span class="text-sm font-medium">
                      {{ form.type === 'global' ? 'Global' : 'Personalizado' }}
                    </span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Descuento:</span>
                    <span class="text-sm font-medium">
                      {{ form.discount_type === 'percentage' ? `${form.value}%` : `€${form.value}` }}
                    </span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Validez:</span>
                    <span class="text-sm font-medium">
                      {{ formatDate(form.valid_from) }} - {{ formatDate(form.valid_to) }}
                    </span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Usos máximos:</span>
                    <span class="text-sm font-medium">
                      {{ form.max_uses || 'Ilimitados' }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Botones de acción -->
              <div class="border-t border-gray-200 pt-6">
                <div class="flex justify-end space-x-3">
                  <SecondaryButton @click="$inertia.visit(route('admin.discount-coupons.index'))">
                    Cancelar
                  </SecondaryButton>
                  <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                  >
                    <span v-if="form.processing">Creando...</span>
                    <span v-else>Crear Cupón</span>
                  </PrimaryButton>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import { ArrowLeftIcon, ArrowPathIcon } from '@heroicons/vue/24/outline'
import axios from 'axios'

const props = defineProps({
  clients: Array
})

const form = useForm({
  code: '',
  type: '',
  discount_type: '',
  value: '',
  valid_from: '',
  valid_to: '',
  max_uses: '',
  client_id: ''
})

const today = new Date().toISOString().split('T')[0]

const isFormValid = computed(() => {
  return form.code && form.type && form.discount_type && form.value && form.valid_from && form.valid_to
})

const generateRandomCode = async () => {
  try {
    const response = await axios.get(route('admin.discount-coupons.generate-code'))
    form.code = response.data.code
  } catch (error) {
    console.error('Error generando código:', error)
  }
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('es-ES')
}

const submit = () => {
  form.post(route('admin.discount-coupons.store'))
}
</script>
