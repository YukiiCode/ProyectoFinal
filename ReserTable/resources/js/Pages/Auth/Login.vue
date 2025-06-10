<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import { useNotifications } from '@/composables/useNotifications';
import { useI18n } from 'vue-i18n';
import { ref } from 'vue';

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

const { t } = useI18n();
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
