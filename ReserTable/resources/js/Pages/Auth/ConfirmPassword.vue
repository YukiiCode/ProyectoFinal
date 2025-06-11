<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import { useNotifications } from '@/composables/useNotifications';
import { useI18n } from 'vue-i18n';
import ThemeManager from '@/Components/ThemeManager.vue';
import AuthControls from '@/Components/AuthControls.vue';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';

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
    <ThemeManager />
    <AuthControls />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <!-- Título del formulario -->
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                {{ t('auth.confirm_password_title').replace(' - ReserTable', '') }}
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg">
                {{ t('auth.confirm_password_subtitle') }}
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Campo Password -->
            <div class="field">
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-lock mr-2 text-blue-500"></i>
                    {{ t('auth.password') }}
                </label>
                <Password
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    class="w-full"
                    :class="{ 'p-invalid': form.errors.password }"
                    :placeholder="t('auth.password_placeholder')"
                    :feedback="false"
                    toggle-mask
                    required
                    autocomplete="current-password"
                    autofocus
                />
                <small v-if="form.errors.password" class="p-error">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    {{ form.errors.password }}
                </small>
            </div>

            <!-- Botón de acción -->
            <div class="form-actions">
                <Button 
                    type="submit" 
                    class="w-full p-3 text-lg font-semibold"
                    :loading="form.processing"
                    :disabled="form.processing"
                    icon="fas fa-check"
                    :label="form.processing ? t('auth.confirming') : t('auth.confirm_button')"
                />
            </div>
        </form>
    </AuthenticationCard>
</template>
