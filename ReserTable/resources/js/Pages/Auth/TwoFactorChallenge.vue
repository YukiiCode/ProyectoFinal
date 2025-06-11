<script setup>
import { nextTick, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import { useNotifications } from '@/composables/useNotifications';
import { useI18n } from 'vue-i18n';
import ThemeManager from '@/Components/ThemeManager.vue';
import AuthControls from '@/Components/AuthControls.vue';

const { t } = useI18n();
const { showError } = useNotifications();

const recovery = ref(false);

const form = useForm({
    code: '',
    recovery_code: '',
});

const recoveryCodeInput = ref(null);
const codeInput = ref(null);

const toggleRecovery = async () => {
    recovery.value ^= true;

    await nextTick();

    if (recovery.value) {
        recoveryCodeInput.value.focus();
        form.code = '';
    } else {
        codeInput.value.focus();
        form.recovery_code = '';
    }
};

const submit = () => {
    form.post(route('two-factor.login'), {
        onError: () => {
            showError(t('auth.password_incorrect'), t('auth.verify_password'));
        }
    });
};
</script>

<template>
    <Head :title="t('auth.two_factor_title')" />
    <ThemeManager />
    <AuthControls />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <!-- Título del formulario -->
        <div class="form-header">
            <h2 class="form-title">{{ t('auth.two_factor_title').replace(' - ReserTable', '') }}</h2>
            <p class="form-subtitle">{{ t('auth.two_factor_subtitle') }}</p>
        </div>

        <!-- Descripción -->
        <div class="form-description">
            <template v-if="! recovery">
                {{ t('auth.two_factor_code_description') }}
            </template>
            <template v-else>
                {{ t('auth.two_factor_recovery_description') }}
            </template>
        </div>

        <form @submit.prevent="submit" class="two-factor-form">
            <!-- Campo Código de Autenticación -->
            <div v-if="! recovery" class="input-group">
                <label for="code" class="input-label">
                    <i class="fas fa-mobile-alt"></i>
                    {{ t('auth.authentication_code') }}
                </label>
                <div class="input-wrapper">
                    <input
                        id="code"
                        ref="codeInput"
                        v-model="form.code"
                        type="text"
                        inputmode="numeric"
                        class="form-input"
                        :class="{ 'error': form.errors.code }"
                        placeholder="123456"
                        required
                        autofocus
                        autocomplete="one-time-code"
                    />
                </div>
                <div v-if="form.errors.code" class="input-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ form.errors.code }}
                </div>
            </div>

            <!-- Campo Código de Recuperación -->
            <div v-else class="input-group">
                <label for="recovery_code" class="input-label">
                    <i class="fas fa-key"></i>
                    {{ t('auth.recovery_code') }}
                </label>
                <div class="input-wrapper">
                    <input
                        id="recovery_code"
                        ref="recoveryCodeInput"
                        v-model="form.recovery_code"
                        type="text"
                        class="form-input"
                        :class="{ 'error': form.errors.recovery_code }"
                        placeholder="xxxxxxxx-xxxx-xxxx"
                        required
                        autocomplete="one-time-code"
                    />
                </div>
                <div v-if="form.errors.recovery_code" class="input-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ form.errors.recovery_code }}
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
                        <i class="fas fa-sign-in-alt"></i>
                        {{ t('auth.login') }}
                    </span>
                    <span v-else class="btn-loading">
                        <i class="fas fa-spinner fa-spin"></i>
                        {{ t('auth.verifying') }}
                    </span>
                </button>
            </div>

            <!-- Toggle de recuperación -->
            <div class="auth-footer">
                <button type="button" class="recovery-toggle" @click.prevent="toggleRecovery">
                    <template v-if="! recovery">
                        <i class="fas fa-life-ring"></i>
                        {{ t('auth.use_recovery_code') }}
                    </template>
                    <template v-else>
                        <i class="fas fa-mobile-alt"></i>
                        {{ t('auth.use_authentication_code') }}
                    </template>
                </button>
            </div>
        </form>
    </AuthenticationCard>
</template>
