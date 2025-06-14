<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { useI18n } from 'vue-i18n';
import ThemeManager from '@/Components/ThemeManager.vue';

const { t } = useI18n();

const props = defineProps({
    availableTables: Array,
    availableTimes: Array,
});

const form = useForm({
    table_id: null,
    reservation_date: '',
    reservation_time: '',
    party_size: 2,
    notes: '',
});

const availableTables = ref([]);
const selectedDate = ref('');
const selectedTime = ref('');
const selectedPartySize = ref(2);
const showTableMap = ref(false);
const isLoading = ref(false);

// Computed
const minDate = computed(() => {
    const today = new Date();
    return today.toISOString().split('T')[0];
});

const maxDate = computed(() => {
    const maxDate = new Date();
    maxDate.setDate(maxDate.getDate() + 30); // 30 días adelante
    return maxDate.toISOString().split('T')[0];
});

// Methods
const searchAvailableTables = async () => {
    if (!selectedDate.value || !selectedTime.value || !selectedPartySize.value) {
        return;
    }

    isLoading.value = true;
    try {
        const response = await axios.get('/client/reservations/available-tables', {
            params: {
                reservation_date: `${selectedDate.value} ${selectedTime.value}`,
                party_size: selectedPartySize.value,
            }
        });

        if (response.data.success) {
            availableTables.value = response.data.tables;
            showTableMap.value = true;
        }
    } catch (error) {
        console.error('Error fetching available tables:', error);
        alert('Error al buscar mesas disponibles');
    } finally {
        isLoading.value = false;
    }
};

const selectTable = (table) => {
    form.table_id = table.id;
    form.reservation_date = selectedDate.value;
    form.reservation_time = selectedTime.value;
    form.party_size = selectedPartySize.value;
};

const submitReservation = () => {
    form.post('/client/reservations', {
        onSuccess: () => {
            alert('¡Reserva creada exitosamente!');
        },
        onError: (errors) => {
            console.error('Errors:', errors);
        }
    });
};

const getTableStatusColor = (status) => {
    switch (status) {
        case 'available':
            return 'bg-green-500 hover:bg-green-600';
        case 'reserved':
            return 'bg-yellow-500';
        case 'occupied':
            return 'bg-red-500';
        default:
            return 'bg-gray-500';
    }
};

const getTableStatusText = (status) => {
    switch (status) {
        case 'available':
            return t('reservations.available');
        case 'reserved':
            return t('reservations.reserved');
        case 'occupied':
            return t('reservations.occupied');
        default:
            return 'Desconocido';
    }
};
</script>

<template>
    <Head :title="t('reservations.title')" />
    <ThemeManager />
    
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
        <div class="container mx-auto px-4 py-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                    {{ t('reservations.title') }}
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-300">
                    {{ t('reservations.subtitle') }}
                </p>
            </div>

            <!-- Formulario de búsqueda -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8 max-w-4xl mx-auto">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">
                    Selecciona fecha, hora y número de personas
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Fecha
                        </label>
                        <input
                            v-model="selectedDate"
                            type="date"
                            :min="minDate"
                            :max="maxDate"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100"
                            required
                        />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Hora
                        </label>
                        <select
                            v-model="selectedTime"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100"
                            required
                        >
                            <option value="">Seleccionar hora</option>
                            <option v-for="time in availableTimes" :key="time" :value="time">
                                {{ time }}
                            </option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Personas
                        </label>
                        <select
                            v-model="selectedPartySize"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100"
                            required
                        >
                            <option v-for="n in 12" :key="n" :value="n">
                                {{ n }} {{ n === 1 ? 'persona' : 'personas' }}
                            </option>
                        </select>
                    </div>
                    
                    <div class="flex items-end">
                        <button
                            @click="searchAvailableTables"
                            :disabled="!selectedDate || !selectedTime || !selectedPartySize || isLoading"
                            class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-medium rounded-lg transition-colors duration-200"
                        >
                            <span v-if="isLoading">Buscando...</span>
                            <span v-else>Buscar Mesas</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mapa de mesas disponibles -->
            <div v-if="showTableMap && availableTables.length > 0" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">
                    Mesas disponibles para {{ selectedDate }} a las {{ selectedTime }}
                </h3>
                
                <!-- Layout del restaurante -->
                <div class="relative w-full h-96 bg-gray-100 dark:bg-gray-700 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 overflow-hidden">
                    <!-- Mesas -->
                    <div
                        v-for="table in availableTables"
                        :key="table.id"
                        :style="{
                            left: `${table.position_x || 10 + (table.id % 10) * 8}%`,
                            top: `${table.position_y || 10 + (Math.floor(table.id / 10) % 8) * 10}%`
                        }"
                        class="absolute cursor-pointer transform -translate-x-1/2 -translate-y-1/2"
                        @click="selectTable(table)"
                    >
                        <div
                            :class="[
                                'w-16 h-16 rounded-full flex items-center justify-center text-white font-bold shadow-lg transition-all duration-200 hover:scale-110',
                                form.table_id === table.id ? 'bg-blue-600 ring-4 ring-blue-300' : 'bg-green-500 hover:bg-green-600'
                            ]"
                        >
                            {{ table.table_number }}
                        </div>
                        <div class="text-center mt-2 text-xs text-gray-600 dark:text-gray-300">
                            {{ table.capacity }} pers.
                        </div>
                    </div>
                </div>
                
                <!-- Leyenda -->
                <div class="mt-4 flex justify-center space-x-6">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-green-500 rounded-full mr-2"></div>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Disponible</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-blue-600 rounded-full mr-2"></div>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Seleccionada</span>
                    </div>
                </div>
            </div>

            <!-- Sin mesas disponibles -->
            <div v-else-if="showTableMap && availableTables.length === 0" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8 text-center">
                <div class="text-yellow-500 text-6xl mb-4">
                    <i class="pi pi-exclamation-triangle"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                    No hay mesas disponibles
                </h3>
                <p class="text-gray-600 dark:text-gray-300">
                    Para la fecha y hora seleccionadas no hay mesas disponibles con capacidad para {{ selectedPartySize }} personas.
                    Por favor, intenta con otra fecha u hora.
                </p>
            </div>

            <!-- Formulario de confirmación -->
            <div v-if="form.table_id" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 max-w-2xl mx-auto">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">
                    Confirmar Reserva
                </h3>
                
                <div class="space-y-4 mb-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">Mesa</span>
                            <p class="font-medium text-gray-900 dark:text-gray-100">
                                Mesa {{ availableTables.find(t => t.id === form.table_id)?.table_number }}
                            </p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">Capacidad</span>
                            <p class="font-medium text-gray-900 dark:text-gray-100">
                                {{ availableTables.find(t => t.id === form.table_id)?.capacity }} personas
                            </p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">Fecha</span>
                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ form.reservation_date }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">Hora</span>
                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ form.reservation_time }}</p>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Notas especiales (opcional)
                        </label>
                        <textarea
                            v-model="form.notes"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100"
                            rows="3"
                            placeholder="Menciona alguna necesidad especial, celebración, etc."
                        ></textarea>
                    </div>
                </div>
                
                <div class="flex space-x-4">
                    <button
                        @click="form.table_id = null; showTableMap = false"
                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
                    >
                        Cancelar
                    </button>
                    <button
                        @click="submitReservation"
                        :disabled="form.processing"
                        class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-medium rounded-lg transition-colors duration-200"
                    >
                        <span v-if="form.processing">Procesando...</span>
                        <span v-else>Confirmar Reserva</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
