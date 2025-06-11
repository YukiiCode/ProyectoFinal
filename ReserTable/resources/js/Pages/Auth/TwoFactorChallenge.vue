<script setup>
import { nextTick, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import { useNotifications } from '@/composables/useNotifications';
import { useI18n } from 'vue-i18n';
import ThemeManager from '@/Components/ThemeManager.vue';
import AuthControls from '@/Components/AuthControls.vue';

// PrimeVue components
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';

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
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                {{ t('auth.two_factor_title').replace(' - ReserTable', '') }}
            </h2>
            <p class="text-gray-600 dark:text-gray-400">
                {{ t('auth.two_factor_subtitle') }}
            </p>
        </div>

        <!-- Descripción -->
        <div class="text-gray-600 dark:text-gray-400 mb-6 text-center">
            <template v-if="! recovery">
                {{ t('auth.two_factor_code_description') }}
            </template>
            <template v-else>
                {{ t('auth.two_factor_recovery_description') }}
            </template>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Campo Código de Autenticación -->
            <div v-if="! recovery" class="field">
                <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="pi pi-mobile mr-2"></i>
                    {{ t('auth.authentication_code') }}
                </label>
                <InputText
                    id="code"
                    ref="codeInput"
                    v-model="form.code"
                    type="text"
                    class="w-full"
                    :class="{ 'p-invalid': form.errors.code }"
                    placeholder="123456"
                    autocomplete="one-time-code"
                    autofocus
                />
                <small v-if="form.errors.code" class="p-error">
                    <i class="pi pi-exclamation-triangle mr-1"></i>
                    {{ form.errors.code }}
                </small>
            </div>

            <!-- Campo Código de Recuperación -->
            <div v-else class="field">
                <label for="recovery_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="pi pi-key mr-2"></i>
                    {{ t('auth.recovery_code') }}
                </label>
                <InputText
                    id="recovery_code"
                    ref="recoveryCodeInput"
                    v-model="form.recovery_code"
                    type="text"
                    class="w-full"
                    :class="{ 'p-invalid': form.errors.recovery_code }"
                    placeholder="xxxx-xxxx"
                    autocomplete="one-time-code"
                />
                <small v-if="form.errors.recovery_code" class="p-error">
                    <i class="pi pi-exclamation-triangle mr-1"></i>
                    {{ form.errors.recovery_code }}
                </small>
            </div>

            <!-- Botón de envío -->
            <Button
                type="submit"
                :label="form.processing ? t('auth.verifying') : t('auth.login')"
                icon="pi pi-check"
                class="w-full p-3 text-lg"
                :loading="form.processing"
                loading-icon="pi pi-spinner pi-spin"
                severity="primary"
            />

            <!-- Toggle entre código y recuperación -->
            <div class="text-center">
                <button 
                    type="button"
                    @click="toggleRecovery"
                    class="text-sm text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-200 transition-colors duration-200"
                >
                    <template v-if="! recovery">
                        <i class="pi pi-key mr-1"></i>
                        {{ t('auth.use_recovery_code') }}
                    </template>
                    <template v-else>
                        <i class="pi pi-mobile mr-1"></i>
                        {{ t('auth.use_authentication_code') }}
                    </template>
                </button>
            </div>
        </form>
    </AuthenticationCard>
</template>
