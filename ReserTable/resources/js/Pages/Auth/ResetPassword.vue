<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import { useNotifications } from '@/composables/useNotifications';
import { useI18n } from 'vue-i18n';

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

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <!-- Título del formulario -->
        <div class="form-header">
            <h2 class="form-title">{{ t('auth.reset_password') }}</h2>
            <p class="form-subtitle">{{ t('auth.reset_password_subtitle') }}</p>
        </div>

        <form @submit.prevent="submit" class="reset-form">
            <!-- Campo Email -->
            <div class="input-group">
                <label for="email" class="input-label">
                    <i class="fas fa-envelope"></i>
                    {{ t('auth.email') }}
                </label>
                <div class="input-wrapper">
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="form-input"
                        :class="{ 'error': form.errors.email }"
                        :placeholder="t('auth.email_placeholder')"
                        required
                        autofocus
                        autocomplete="username"
                        readonly
                    />
                </div>
                <div v-if="form.errors.email" class="input-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ form.errors.email }}
                </div>
            </div>

            <!-- Campo Nueva Contraseña -->
            <div class="input-group">
                <label for="password" class="input-label">
                    <i class="fas fa-lock"></i>
                    {{ t('auth.new_password') }}
                </label>
                <div class="input-wrapper">
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="form-input"
                        :class="{ 'error': form.errors.password }"
                        :placeholder="t('auth.new_password_placeholder')"
                        required
                        autocomplete="new-password"
                    />
                </div>
                <div v-if="form.errors.password" class="input-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ form.errors.password }}
                </div>
            </div>

            <!-- Campo Confirmar Contraseña -->
            <div class="input-group">
                <label for="password_confirmation" class="input-label">
                    <i class="fas fa-lock"></i>
                    {{ t('auth.confirm_new_password') }}
                </label>
                <div class="input-wrapper">
                    <input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="form-input"
                        :class="{ 'error': form.errors.password_confirmation }"
                        :placeholder="t('auth.confirm_new_password_placeholder')"
                        required
                        autocomplete="new-password"
                    />
                </div>
                <div v-if="form.errors.password_confirmation" class="input-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ form.errors.password_confirmation }}
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="form-actions">
                <button 
                    type="submit" 
                    class="btn-primary"
                    :class="{ 'loading': form.processing }" 
                    :disabled="form.processing"
                >
                    <span v-if="!form.processing" class="btn-content">
                        <i class="fas fa-key"></i>
                        {{ t('auth.reset_password') }}
                    </span>
                    <span v-else class="btn-loading">
                        <i class="fas fa-spinner fa-spin"></i>
                        {{ t('auth.resetting') }}
                    </span>
                </button>
            </div>
        </form>

        <!-- Enlaces adicionales -->
        <div class="auth-footer">
            <p class="auth-footer-text">
                {{ t('auth.remembered_password') }} 
                <Link :href="route('login')" class="auth-link">
                    {{ t('auth.back_to_login') }}
                </Link>
            </p>
        </div>
    </AuthenticationCard>
</template>
