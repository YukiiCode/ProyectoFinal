<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import { useNotifications } from '@/composables/useNotifications';
import { useI18n } from 'vue-i18n';
import { ref } from 'vue';
import ThemeManager from '@/Components/ThemeManager.vue';
import AuthControls from '@/Components/AuthControls.vue';

// PrimeVue components
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Checkbox from 'primevue/checkbox';
import Password from 'primevue/password';
import Message from 'primevue/message';
import Card from 'primevue/card';
import FloatLabel from 'primevue/floatlabel';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const { t, locale } = useI18n();
const { showSuccess, showError } = useNotifications();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const passwordVisible = ref(false);

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
        onSuccess: () => {
            showSuccess(t('auth.welcome_back'), t('auth.login_successful'));
        },
        onError: () => {
            showError(t('auth.login_error'), t('auth.authentication_error'));
        }
    });
};
</script>

<template>
    <Head :title="t('auth.login_title')" />
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
                {{ t('auth.welcome_back') }}
            </h2>
            <p class="text-gray-600 dark:text-gray-400">
                {{ t('auth.login_subtitle') }}
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Campo Email con PrimeVue -->
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

            <!-- Campo Password con PrimeVue -->
            <div class="field">
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="pi pi-lock mr-2"></i>
                    {{ t('auth.password') }}
                </label>
                <Password
                    id="password"
                    v-model="form.password"
                    :feedback="false"
                    :toggle-mask="true"
                    class="w-full"
                    :class="{ 'p-invalid': form.errors.password }"
                    :placeholder="t('auth.password_placeholder')"
                    autocomplete="current-password"
                    input-class="w-full"
                />
                <small v-if="form.errors.password" class="p-error">
                    <i class="pi pi-exclamation-triangle mr-1"></i>
                    {{ form.errors.password }}
                </small>
            </div>

            <!-- Remember me con PrimeVue Checkbox -->
            <div class="flex items-center">
                <Checkbox
                    id="remember"
                    v-model="form.remember"
                    :binary="true"
                    class="mr-2"
                />
                <label for="remember" class="text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                    {{ t('auth.remember_me') }}
                </label>
            </div>

            <!-- Botón de envío con PrimeVue -->
            <Button
                type="submit"
                :label="t('auth.login')"
                icon="pi pi-sign-in"
                class="w-full p-3 text-lg"
                :loading="form.processing"
                loading-icon="pi pi-spinner pi-spin"
                severity="primary"
            />

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400">
                        {{ t('auth.or_continue_with') }}
                    </span>
                </div>
            </div>

            <!-- Botón de Google -->
            <a
                :href="route('auth.google')"
                class="w-full inline-flex justify-center items-center px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200"
            >
                <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                {{ t('auth.continue_with_google') }}
            </a>

            <!-- Enlace de contraseña olvidada -->
            <div v-if="canResetPassword" class="text-center">
                <Link 
                    :href="route('password.request')" 
                    class="text-sm text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-200 transition-colors duration-200"
                >
                    <i class="pi pi-key mr-1"></i>
                    {{ t('auth.forgot_password') }}
                </Link>
            </div>
        </form>

        <!-- Enlaces adicionales -->
        <div class="text-center mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ t('auth.no_account') }} 
                <Link 
                    :href="route('register')" 
                    class="font-medium text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-200 transition-colors duration-200 ml-1"
                >
                    {{ t('auth.register_here') }}
                </Link>
            </p>
        </div>
    </AuthenticationCard>
</template>
