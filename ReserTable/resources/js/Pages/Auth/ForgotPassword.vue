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
import Message from 'primevue/message';

defineProps({
    status: String,
});

const { t } = useI18n();
const { showSuccess, showError } = useNotifications();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'), {
        onSuccess: () => {
            showSuccess(t('auth.link_sent'), t('auth.check_email_reset'));
        },
        onError: () => {
            showError(t('auth.account_not_found'), t('auth.verify_email_correct'));
        }
    });
};
</script>

<template>
    <Head :title="t('auth.forgot_password_title')" />
    <ThemeManager />
    <AuthControls />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <!-- Mensaje de estado con PrimeVue -->
        <Message 
            v-if="status" 
            severity="success" 
            :closable="false"
            class="mb-4"
        >
            <i class="pi pi-check-circle mr-2"></i>
            {{ status }}
        </Message>

        <!-- Título del formulario -->
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                {{ t('auth.forgot_password') }}
            </h2>
            <p class="text-gray-600 dark:text-gray-400">
                {{ t('auth.forgot_password_subtitle') }}
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
                    autofocus
                />
                <small v-if="form.errors.email" class="p-error">
                    <i class="pi pi-exclamation-triangle mr-1"></i>
                    {{ form.errors.email }}
                </small>
            </div>

            <!-- Botón de envío -->
            <Button
                type="submit"
                :label="form.processing ? t('auth.sending') : t('auth.send_reset_link')"
                icon="pi pi-send"
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


