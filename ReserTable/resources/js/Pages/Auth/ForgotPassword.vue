<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import { useNotifications } from '@/composables/useNotifications';
import { useI18n } from 'vue-i18n';
import ThemeManager from '@/Components/ThemeManager.vue';

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

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <!-- Título del formulario -->
        <div class="form-header">
            <h2 class="form-title">{{ t('auth.forgot_password') }}</h2>
            <p class="form-subtitle">{{ t('auth.forgot_password_subtitle') }}</p>
        </div>

        <!-- Mensaje de estado -->
        <div v-if="status" class="status-message success">
            <i class="fas fa-check-circle"></i>
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="forgot-form">
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
                    />
                </div>
                <div v-if="form.errors.email" class="input-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ form.errors.email }}
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
                        <i class="fas fa-paper-plane"></i>
                        {{ t('auth.send_reset_link') }}
                    </span>
                    <span v-else class="btn-loading">
                        <i class="fas fa-spinner fa-spin"></i>
                        {{ t('auth.sending') }}
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


