<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { ref, computed } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import ModernNavbar from '@/Components/ModernNavbar.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

const { t } = useI18n();

const props = defineProps({
    auth: Object,
    client: Object,
    flash: Object,
});

// Estados reactivos
const isEditing = ref(false);
const showDeleteModal = ref(false);
const deleteLoading = ref(false);
const showCancelDeletionModal = ref(false);

// Formulario de edición de perfil
const profileForm = useForm({
    name: props.client.name,
    email: props.client.email,
    phone: props.client.phone || '',
});

// Formulario de preferencias de email
const preferencesForm = useForm({
    promotional_emails: props.client.promotional_emails || false,
    reservation_reminders: props.client.reservation_reminders || false,
});

// Formulario de eliminación de cuenta
const deleteForm = useForm({
    deletion_reason: '',
    confirm_deletion: false,
});

// Función para iniciar la edición
const startEditing = () => {
    isEditing.value = true;
};

// Función para cancelar la edición
const cancelEditing = () => {
    isEditing.value = false;
    profileForm.reset();
    profileForm.name = props.client.name;
    profileForm.email = props.client.email;
    profileForm.phone = props.client.phone || '';
};

// Función para guardar cambios del perfil
const saveProfile = () => {
    profileForm.put('/mi-perfil', {
        onSuccess: () => {
            isEditing.value = false;
        },
        preserveScroll: true,
    });
};

// Función para actualizar preferencias de email
const updateEmailPreferences = () => {
    preferencesForm.patch('/mi-perfil/preferencias-email', {
        preserveScroll: true,
    });
};

// Función para solicitar eliminación de cuenta
const requestAccountDeletion = () => {
    if (!deleteForm.confirm_deletion) {
        return;
    }
    
    deleteLoading.value = true;
    
    router.post('/mi-perfil/eliminar-cuenta', {
        deletion_reason: deleteForm.deletion_reason,
    }, {
        onSuccess: () => {
            showDeleteModal.value = false;
            deleteForm.reset();
            deleteLoading.value = false;
        },
        onError: () => {
            deleteLoading.value = false;
        }
    });
};

// Función para cancelar solicitud de eliminación
const cancelAccountDeletion = () => {
    router.delete('/mi-perfil/cancelar-eliminacion', {
        onSuccess: () => {
            showCancelDeletionModal.value = false;
        }
    });
};

// Función para formatear fecha
const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

// Calcular estadísticas del cliente
const reservationStats = computed(() => {
    if (!props.client.reservations) return {};
    
    const reservations = props.client.reservations;
    return {
        total: reservations.length,
        pending: reservations.filter(r => r.status === 'pending').length,
        confirmed: reservations.filter(r => r.status === 'confirmed').length,
        completed: reservations.filter(r => r.status === 'completed').length,
        cancelled: reservations.filter(r => r.status === 'cancelled').length,
    };
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <Head :title="t('client.profile.title')" />
        
        <ModernNavbar :auth="auth" />
        
        <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center space-x-4">
                            <!-- Avatar -->
                            <div class="flex-shrink-0">
                                <div v-if="client.avatar" class="h-16 w-16 rounded-full overflow-hidden">
                                    <img 
                                        :src="client.avatar" 
                                        :alt="client.name"
                                        class="h-full w-full object-cover"
                                    />
                                </div>
                                <div v-else class="h-16 w-16 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                    <svg class="h-8 w-8 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                                    {{ t('client.profile.title') }}
                                </h1>
                                <p class="text-gray-600 dark:text-gray-400">
                                    Administra tu información personal y preferencias
                                </p>
                                <!-- Indicador de autenticación con Google -->
                                <div v-if="client.auth_provider === 'google'" class="mt-2 flex items-center text-sm text-blue-600 dark:text-blue-400">
                                    <svg class="w-4 h-4 mr-1" viewBox="0 0 24 24">
                                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                    </svg>
                                    Conectado con Google
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex space-x-3">
                            <a href="/mi-cuenta" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                {{ t('client.profile.view_account') }}
                            </a>
                            <a href="/mi-dashboard" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                {{ t('client.profile.back_to_dashboard') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mensaje de éxito -->
            <div v-if="flash?.success" class="mb-6 bg-green-100 dark:bg-green-900/30 border border-green-400 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg">
                {{ flash.success }}
            </div>

            <!-- Información básica del perfil -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                            {{ t('client.profile.account_info') }}
                        </h2>
                        <button
                            v-if="!isEditing"
                            @click="startEditing"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                        >
                            {{ t('client.profile.edit_profile') }}
                        </button>
                    </div>

                    <!-- Vista de solo lectura -->
                    <div v-if="!isEditing" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ t('common.name') }}
                            </label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 px-3 py-2 rounded-lg">
                                {{ client.name }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ t('common.email') }}
                            </label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 px-3 py-2 rounded-lg">
                                {{ client.email }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ t('common.phone') }}
                            </label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 px-3 py-2 rounded-lg">
                                {{ client.phone || 'No especificado' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ t('client.profile.member_since') }}
                            </label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 px-3 py-2 rounded-lg">
                                {{ formatDate(client.created_at) }}
                            </p>
                        </div>
                    </div>

                    <!-- Formulario de edición -->
                    <form v-else @submit.prevent="saveProfile" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ t('common.name') }} *
                                </label>
                                <input
                                    v-model="profileForm.name"
                                    type="text"
                                    required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    :class="{ 'border-red-500': profileForm.errors.name }"
                                >
                                <p v-if="profileForm.errors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ profileForm.errors.name }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ t('common.email') }} *
                                </label>
                                <input
                                    v-model="profileForm.email"
                                    type="email"
                                    required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    :class="{ 'border-red-500': profileForm.errors.email }"
                                >
                                <p v-if="profileForm.errors.email" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ profileForm.errors.email }}
                                </p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ t('common.phone') }}
                                </label>
                                <input
                                    v-model="profileForm.phone"
                                    type="tel"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    :class="{ 'border-red-500': profileForm.errors.phone }"
                                >
                                <p v-if="profileForm.errors.phone" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ profileForm.errors.phone }}
                                </p>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button
                                type="button"
                                @click="cancelEditing"
                                class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors"
                            >
                                {{ t('client.profile.cancel_edit') }}
                            </button>
                            <button
                                type="submit"
                                :disabled="profileForm.processing"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center space-x-2"
                            >
                                <svg v-if="profileForm.processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ t('client.profile.save_changes') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Preferencias de Email -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">
                        {{ t('client.profile.email_preferences.title') }}
                    </h2>

                    <form @submit.prevent="updateEmailPreferences" class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ t('client.profile.email_preferences.promotional_emails') }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ t('client.profile.email_preferences.promotional_emails_desc') }}
                                </p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input
                                    v-model="preferencesForm.promotional_emails"
                                    type="checkbox"
                                    class="sr-only peer"
                                >
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ t('client.profile.email_preferences.reservation_reminders') }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ t('client.profile.email_preferences.reservation_reminders_desc') }}
                                </p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input
                                    v-model="preferencesForm.reservation_reminders"
                                    type="checkbox"
                                    class="sr-only peer"
                                >
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="submit"
                                :disabled="preferencesForm.processing"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center space-x-2"
                            >
                                <svg v-if="preferencesForm.processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ t('client.profile.email_preferences.save_preferences') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Zona de Peligro - Eliminación de cuenta -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-red-600 dark:text-red-400 mb-4">
                        {{ t('client.profile.account_deletion.danger_zone') }}
                    </h2>

                    <!-- Si ya hay una solicitud de eliminación -->
                    <div v-if="client.account_deletion_requested_at" class="bg-yellow-100 dark:bg-yellow-900/30 border border-yellow-400 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <div class="text-2xl mr-3">⚠️</div>
                            <div>
                                <h3 class="text-lg font-medium text-yellow-800 dark:text-yellow-200">
                                    {{ t('client.profile.account_deletion.deletion_pending') }}
                                </h3>
                                <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                                    {{ t('client.profile.account_deletion.deletion_pending_desc') }}
                                </p>
                                <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-2">
                                    Solicitado el: {{ formatDate(client.account_deletion_requested_at) }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button
                                @click="showCancelDeletionModal = true"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                            >
                                {{ t('client.profile.account_deletion.cancel_deletion') }}
                            </button>
                        </div>
                    </div>

                    <!-- Si no hay solicitud pendiente -->
                    <div v-else>
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-4">
                            <p class="text-sm text-red-700 dark:text-red-300">
                                {{ t('client.profile.account_deletion.warning_message') }}
                            </p>
                        </div>

                        <button
                            @click="showDeleteModal = true"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                        >
                            {{ t('client.profile.account_deletion.request_deletion') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Eliminación de Cuenta -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ t('client.profile.account_deletion.deletion_title') }}
                </h3>
            </div>
            
            <form @submit.prevent="requestAccountDeletion" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ t('client.profile.account_deletion.deletion_reason') }}
                    </label>
                    <textarea
                        v-model="deleteForm.deletion_reason"
                        rows="3"
                        :placeholder="t('client.profile.account_deletion.deletion_reason_placeholder')"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                    ></textarea>
                </div>

                <div class="flex items-center">
                    <input
                        v-model="deleteForm.confirm_deletion"
                        type="checkbox"
                        class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                    >
                    <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                        {{ t('client.profile.account_deletion.confirm_deletion') }}
                    </label>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button
                        type="button"
                        @click="showDeleteModal = false; deleteForm.reset()"
                        class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors"
                    >
                        {{ t('common.cancel') }}
                    </button>
                    <button
                        type="submit"
                        :disabled="!deleteForm.confirm_deletion || deleteLoading"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center space-x-2"
                    >
                        <svg v-if="deleteLoading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>{{ t('client.profile.account_deletion.confirm_deletion_btn') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Cancelar Eliminación -->
    <ConfirmModal
        :show="showCancelDeletionModal"
        :title="t('client.profile.account_deletion.cancel_deletion')"
        :message="'¿Estás seguro de que deseas cancelar la solicitud de eliminación de tu cuenta?'"
        :confirm-text="'Sí, cancelar solicitud'"
        :cancel-text="'No, mantener solicitud'"
        @confirm="cancelAccountDeletion"
        @cancel="showCancelDeletionModal = false"
    />
</template>
