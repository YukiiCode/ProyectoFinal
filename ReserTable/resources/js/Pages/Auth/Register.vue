<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import { useNotifications } from '@/composables/useNotifications';
import { useI18n } from 'vue-i18n';
import ThemeManager from '@/Components/ThemeManager.vue';
import AuthControls from '@/Components/AuthControls.vue';

const { t } = useI18n();
const { showSuccess, showError } = useNotifications();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
        onSuccess: () => {
            showSuccess(t('auth.registration_success'), t('auth.account_created'));
        },
        onError: (errors) => {
            if (errors.email) {
                showError(t('auth.email_already_registered'), t('auth.registration_error'));
            } else {
                showError(t('auth.verify_data'), t('auth.registration_error'));
            }
        }
    });
};
</script>

<template>
    <Head :title="t('auth.register_title')" />
    <ThemeManager />
    <AuthControls />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <!-- Título del formulario -->
        <div class="form-header">
            <h2 class="form-title">{{ t('auth.create_account') }}</h2>
            <p class="form-subtitle">{{ t('auth.register_subtitle') }}</p>
        </div>

        <form @submit.prevent="submit" class="register-form">
            <!-- Campo Nombre -->
            <div class="input-group">
                <label for="name" class="input-label">
                    <i class="fas fa-user"></i>
                    {{ t('auth.full_name') }}
                </label>
                <div class="input-wrapper">
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="form-input"
                        :class="{ 'error': form.errors.name }"
                        :placeholder="t('auth.full_name_placeholder')"
                        required
                        autofocus
                        autocomplete="name"
                    />
                </div>
                <div v-if="form.errors.name" class="input-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ form.errors.name }}
                </div>
            </div>

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
                        autocomplete="username"
                    />
                </div>
                <div v-if="form.errors.email" class="input-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ form.errors.email }}
                </div>
            </div>

            <!-- Campo Password -->
            <div class="input-group">
                <label for="password" class="input-label">
                    <i class="fas fa-lock"></i>
                    {{ t('auth.password') }}
                </label>
                <div class="input-wrapper">
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="form-input"
                        :class="{ 'error': form.errors.password }"
                        :placeholder="t('auth.password_placeholder')"
                        required
                        autocomplete="new-password"
                    />
                </div>
                <div v-if="form.errors.password" class="input-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ form.errors.password }}
                </div>
            </div>

            <!-- Campo Confirmar Password -->
            <div class="input-group">
                <label for="password_confirmation" class="input-label">
                    <i class="fas fa-lock"></i>
                    {{ t('auth.confirm_password') }}
                </label>
                <div class="input-wrapper">
                    <input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="form-input"
                        :class="{ 'error': form.errors.password_confirmation }"
                        :placeholder="t('auth.confirm_password_placeholder')"
                        required
                        autocomplete="new-password"
                    />
                </div>
                <div v-if="form.errors.password_confirmation" class="input-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ form.errors.password_confirmation }}
                </div>
            </div>

            <!-- Términos y condiciones -->
            <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="form-options">
                <label class="checkbox-label">
                    <input 
                        v-model="form.terms" 
                        type="checkbox" 
                        name="terms" 
                        class="checkbox-input"
                        required
                    />
                    <span class="checkbox-custom"></span>
                    <span class="checkbox-text">
                        {{ t('auth.accept_terms_start') }} 
                        <a target="_blank" :href="route('terms.show')" class="auth-link">{{ t('auth.terms_of_service') }}</a> 
                        {{ t('auth.and') }} 
                        <a target="_blank" :href="route('policy.show')" class="auth-link">{{ t('auth.privacy_policy') }}</a>
                    </span>
                </label>
                <div v-if="form.errors.terms" class="input-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ form.errors.terms }}
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
                        <i class="fas fa-user-plus"></i>
                        {{ t('auth.create_account') }}
                    </span>
                    <span v-else class="btn-loading">
                        <i class="fas fa-spinner fa-spin"></i>
                        {{ t('auth.creating_account') }}
                    </span>
                </button>
            </div>
        </form>

        <!-- Enlaces adicionales -->
        <div class="auth-footer">
            <p class="auth-footer-text">
                {{ t('auth.already_have_account') }} 
                <Link :href="route('login')" class="auth-link">
                    {{ t('auth.login_here') }}
                </Link>
            </p>
        </div>
    </AuthenticationCard>
</template>
