<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import { useNotifications } from '@/composables/useNotifications';
import { useI18n } from 'vue-i18n';
import ThemeManager from '@/Components/ThemeManager.vue';
import AuthControls from '@/Components/AuthControls.vue';

// PrimeVue components
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';

const props = defineProps({
    email: String,
    token: String,
});

const { t } = useI18n();
const { showSuccess, showError } = useNotifications();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
        onSuccess: () => {
            showSuccess(t('auth.password_updated'), t('auth.password_reset_success'));
        },
        onError: () => {
            showError(t('auth.reset_password_error'), t('auth.verify_data_correct'));
        }
    });
};
</script>

<template>
    <Head :title="t('auth.reset_password_title')" />
    <ThemeManager />
    <AuthControls />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <!-- Título del formulario -->
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                {{ t('auth.reset_password') }}
            </h2>
            <p class="text-gray-600 dark:text-gray-400">
                {{ t('auth.reset_password_subtitle') }}
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Campo Email -->
            <div class="field">
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="pi pi-envelope mr-2"></i>
                    {{ t('auth.email') }}
                </label>
                <InputText
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="w-full"
                    :class="{ 'p-invalid': form.errors.email }"
                    :placeholder="t('auth.email_placeholder')"
                    autocomplete="username"
                    readonly
                />
                <small v-if="form.errors.email" class="p-error">
                    <i class="pi pi-exclamation-triangle mr-1"></i>
                    {{ form.errors.email }}
                </small>
            </div>

            <!-- Campo Nueva Contraseña -->
            <div class="field">
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="pi pi-lock mr-2"></i>
                    {{ t('auth.new_password') }}
                </label>
                <Password
                    id="password"
                    v-model="form.password"
                    :feedback="true"
                    :toggle-mask="true"
                    class="w-full"
                    :class="{ 'p-invalid': form.errors.password }"
                    :placeholder="t('auth.new_password_placeholder')"
                    autocomplete="new-password"
                    input-class="w-full"
                    autofocus
                />
                <small v-if="form.errors.password" class="p-error">
                    <i class="pi pi-exclamation-triangle mr-1"></i>
                    {{ form.errors.password }}
                </small>
            </div>

            <!-- Campo Confirmar Nueva Contraseña -->
            <div class="field">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="pi pi-lock mr-2"></i>
                    {{ t('auth.confirm_new_password') }}
                </label>
                <Password
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    :feedback="false"
                    :toggle-mask="true"
                    class="w-full"
                    :class="{ 'p-invalid': form.errors.password_confirmation }"
                    :placeholder="t('auth.confirm_new_password_placeholder')"
                    autocomplete="new-password"
                    input-class="w-full"
                />
                <small v-if="form.errors.password_confirmation" class="p-error">
                    <i class="pi pi-exclamation-triangle mr-1"></i>
                    {{ form.errors.password_confirmation }}
                </small>
            </div>

            <!-- Botón de envío -->
            <Button
                type="submit"
                :label="form.processing ? t('auth.resetting') : t('auth.reset_password')"
                icon="pi pi-refresh"
                class="w-full p-3 text-lg"
                :loading="form.processing"
                loading-icon="pi pi-spinner pi-spin"
                severity="primary"
            />

            <!-- Enlace para volver al login -->
            <div class="text-center">
                <Link 
                    :href="route('login')" 
                    class="text-sm text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-200 transition-colors duration-200"
                >
                    <i class="pi pi-arrow-left mr-1"></i>
                    {{ t('auth.back_to_login') }}
                </Link>
            </div>
        </form>
    </AuthenticationCard>
</template>
