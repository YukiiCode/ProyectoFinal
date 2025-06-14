<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import ThemeManager from '@/Components/ThemeManager.vue';
import { useI18n } from 'vue-i18n';
import { ref, computed } from 'vue';

const { t } = useI18n();

const props = defineProps({
    tables: {
        type: Array,
        default: () => []
    },
    availableTimes: {
        type: Array,
        default: () => []
    }
});

// Estado local
const selectedTable = ref(null);
const availableTables = ref(props.tables);

// Formulario de reserva usando Inertia
const form = useForm({
    table_id: null,
    reservation_date: '',
    reservation_time: '',
    party_size: 2,
    special_requests: ''
});

// Computed para verificar si el formulario está completo
const canMakeReservation = computed(() => {
    return form.table_id && 
           form.reservation_date && 
           form.reservation_time && 
           form.party_size >= 1;
});

// Manejar selección de mesa
const handleTableSelected = (table) => {
    selectedTable.value = table;
    form.table_id = table.id;
};

// Verificar disponibilidad de mesas
const checkAvailability = () => {
    if (!form.reservation_date || !form.reservation_time || !form.party_size) {
        return;
    }

    // Usar router.get de Inertia para obtener mesas disponibles
    router.get('/client/reservations/available-tables', {
        date: form.reservation_date,
        time: form.reservation_time,
        party_size: form.party_size
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['tables'],
        onSuccess: (page) => {
            // page.props contendrá las mesas actualizadas si el backend las provee
            if (page.props.tables) {
                availableTables.value = page.props.tables;
            }
        },
        onError: (errors) => {
            console.error('Error checking availability:', errors);
        }
    });
};

// Enviar reserva
const submitReservation = () => {
    form.post('/client/reservations', {
        onSuccess: () => {
            // La redirección será manejada por el controlador
        },
        onError: (errors) => {
            console.error('Error making reservation:', errors);
        }
    });
};

// Obtener color de estado de mesa
const getTableStatusColor = (status) => {
    switch(status) {
        case 'available': return '#22C55E'; // Verde
        case 'occupied': return '#EF4444';  // Rojo
        case 'reserved': return '#F59E0B';  // Amarillo
        default: return '#9CA3AF';          // Gris
    }
};

// Verificar si una mesa está disponible para el tamaño de grupo
const isTableSuitable = (table) => {
    return table.capacity >= form.party_size && table.status === 'available';
};
</script>

<template>
    <Head :title="t('reservations.title')" />
    <ThemeManager />
    
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
        <div class="container mx-auto px-4 py-12">
            <!-- Header de la página -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-100 dark:bg-blue-900 rounded-full mb-6">
                    <i class="pi pi-calendar text-3xl text-blue-600 dark:text-blue-400"></i>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                    {{ t('reservations.title') }}
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                    {{ t('reservations.subtitle') }}
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Formulario de reserva -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                        Detalles de la Reserva
                    </h2>
                    
                    <form @submit.prevent="submitReservation" class="space-y-6">
                        <!-- Fecha -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Fecha de la reserva
                            </label>
                            <input
                                type="date"
                                v-model="form.reservation_date"
                                @change="checkAvailability"
                                :min="new Date().toISOString().split('T')[0]"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                required
                            />
                            <div v-if="form.errors.reservation_date" class="text-red-500 text-sm mt-1">
                                {{ form.errors.reservation_date }}
                            </div>
                        </div>

                        <!-- Hora -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Hora de la reserva
                            </label>
                            <select
                                v-model="form.reservation_time"
                                @change="checkAvailability"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                required
                            >
                                <option value="">Seleccionar hora</option>
                                <option v-for="time in availableTimes" :key="time" :value="time">
                                    {{ time }}
                                </option>
                            </select>
                            <div v-if="form.errors.reservation_time" class="text-red-500 text-sm mt-1">
                                {{ form.errors.reservation_time }}
                            </div>
                        </div>

                        <!-- Número de personas -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Número de personas
                            </label>
                            <input
                                type="number"
                                v-model.number="form.party_size"
                                @change="checkAvailability"
                                min="1"
                                max="20"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                required
                            />
                            <div v-if="form.errors.party_size" class="text-red-500 text-sm mt-1">
                                {{ form.errors.party_size }}
                            </div>
                        </div>

                        <!-- Mesa seleccionada -->
                        <div v-if="selectedTable">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Mesa seleccionada
                            </label>
                            <div class="p-3 bg-green-50 dark:bg-green-900/30 rounded-md border border-green-200 dark:border-green-700">
                                <p class="text-green-800 dark:text-green-200">
                                    <strong>Mesa {{ selectedTable.table_number }}</strong> - Capacidad: {{ selectedTable.capacity }} personas
                                </p>
                            </div>
                        </div>

                        <!-- Solicitudes especiales -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Solicitudes especiales (opcional)
                            </label>
                            <textarea
                                v-model="form.special_requests"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Ej: Celebración de cumpleaños, necesidades dietéticas, etc."
                            ></textarea>
                        </div>

                        <!-- Errores generales -->
                        <div v-if="form.errors.table_id" class="text-red-500 text-sm">
                            {{ form.errors.table_id }}
                        </div>

                        <!-- Botón de envío -->
                        <button
                            type="submit"
                            :disabled="!canMakeReservation || form.processing"
                            class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-bold py-3 px-4 rounded-md transition duration-300"
                        >
                            <span v-if="form.processing">Procesando...</span>
                            <span v-else>Realizar Reserva</span>
                        </button>
                    </form>
                </div>

                <!-- Mapa de mesas -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                        Seleccionar Mesa
                    </h2>
                    
                    <!-- Leyenda -->
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">
                            Estado de las mesas:
                        </h3>
                        <div class="grid grid-cols-3 gap-2 text-xs">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                                <span class="text-gray-700 dark:text-gray-300">Disponible</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                                <span class="text-gray-700 dark:text-gray-300">Reservada</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                                <span class="text-gray-700 dark:text-gray-300">Ocupada</span>
                            </div>
                        </div>
                    </div>

                    <!-- Mapa de mesas simplificado -->
                    <div class="relative bg-yellow-50 dark:bg-gray-900 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg" style="height: 400px;">
                        <div
                            v-for="table in availableTables"
                            :key="table.id"
                            @click="handleTableSelected(table)"
                            :class="[
                                'absolute cursor-pointer transition-all duration-200 transform hover:scale-110',
                                selectedTable?.id === table.id ? 'ring-4 ring-blue-500' : '',
                                isTableSuitable(table) ? 'opacity-100' : 'opacity-50 cursor-not-allowed'
                            ]"
                            :style="{
                                left: table.position_x + '%',
                                top: table.position_y + '%',
                                transform: 'translate(-50%, -50%)'
                            }"
                        >
                            <!-- Mesa circular -->
                            <div 
                                class="w-12 h-12 rounded-full border-4 border-gray-800 dark:border-gray-200 flex items-center justify-center text-white font-bold text-sm shadow-lg"
                                :style="{ backgroundColor: getTableStatusColor(table.status) }"
                            >
                                {{ table.table_number }}
                            </div>
                            <!-- Info de capacidad -->
                            <div class="absolute -bottom-6 left-1/2 transform -translate-x-1/2 text-xs text-center">
                                <div class="bg-white dark:bg-gray-800 px-2 py-1 rounded shadow text-gray-700 dark:text-gray-300">
                                    {{ table.capacity }}p
                                </div>
                            </div>
                        </div>

                        <!-- Mensaje cuando no hay fecha/hora seleccionada -->
                        <div v-if="!form.reservation_date || !form.reservation_time" 
                             class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center text-gray-500 dark:text-gray-400">
                                <i class="pi pi-info-circle text-3xl mb-2"></i>
                                <p>Selecciona fecha y hora para ver las mesas disponibles</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>