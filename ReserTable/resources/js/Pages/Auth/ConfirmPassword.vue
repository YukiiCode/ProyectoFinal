<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import { useNotifications } from '@/composables/useNotifications';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const { showError } = useNotifications();

const form = useForm({
    password: '',
});

const passwordInput = ref(null);

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => {
            form.reset();
            passwordInput.value?.focus();
        },
        onError: () => {
            showError(t('auth.password_incorrect'), t('auth.verify_password'));
        }
    });
};
</script>

<template>
    <Head :title="t('auth.confirm_password_title')" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <!-- Título del formulario -->
        <div class="form-header">
            <h2 class="form-title">{{ t('auth.confirm_password_title').replace(' - ReserTable', '') }}</h2>
            <p class="form-subtitle">{{ t('auth.confirm_password_subtitle') }}</p>
        </div>

        <form @submit.prevent="submit" class="confirm-form">
            <!-- Campo Password -->
            <div class="input-group">
                <label for="password" class="input-label">
                    <i class="fas fa-lock"></i>
                    {{ t('auth.password') }}
                </label>
                <div class="input-wrapper">
                    <input
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="form-input"
                        :class="{ 'error': form.errors.password }"
                        :placeholder="t('auth.password_placeholder')"
                        required
                        autocomplete="current-password"
                        autofocus
                    />
                </div>
                <div v-if="form.errors.password" class="input-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ form.errors.password }}
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
                        <i class="fas fa-check"></i>
                        {{ t('auth.confirm_button') }}
                    </span>
                    <span v-else class="btn-loading">
                        <i class="fas fa-spinner fa-spin"></i>
                        {{ t('auth.confirming') }}
                    </span>
                </button>
            </div>
        </form>
    </AuthenticationCard>
</template>
