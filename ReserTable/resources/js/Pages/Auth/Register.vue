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
import Checkbox from 'primevue/checkbox';
import Password from 'primevue/password';
import Message from 'primevue/message';

const { t } = useI18n();
const { showSuccess, showError } = useNotifications();

const form = useForm({
    name: '',
    email: '',
    phone: '',
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
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                {{ t('auth.create_account') }}
            </h2>
            <p class="text-gray-600 dark:text-gray-400">
                {{ t('auth.register_subtitle') }}
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Campo Nombre -->
            <div class="field">
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="pi pi-user mr-2"></i>
                    {{ t('auth.full_name') }}
                </label>
                <InputText
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="w-full"
                    :class="{ 'p-invalid': form.errors.name }"
                    :placeholder="t('auth.full_name_placeholder')"
                    autocomplete="name"
                    autofocus
                />
                <small v-if="form.errors.name" class="p-error">
                    <i class="pi pi-exclamation-triangle mr-1"></i>
                    {{ form.errors.name }}
                </small>
            </div>

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
                />
                <small v-if="form.errors.email" class="p-error">
                    <i class="pi pi-exclamation-triangle mr-1"></i>
                    {{ form.errors.email }}
                </small>
            </div>

            <!-- Campo Teléfono -->
            <div class="field">
                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="pi pi-phone mr-2"></i>
                    {{ t('auth.phone') }}
                </label>
                <InputText
                    id="phone"
                    v-model="form.phone"
                    type="tel"
                    class="w-full"
                    :class="{ 'p-invalid': form.errors.phone }"
                    :placeholder="t('auth.phone_placeholder')"
                    autocomplete="tel"
                />
                <small v-if="form.errors.phone" class="p-error">
                    <i class="pi pi-exclamation-triangle mr-1"></i>
                    {{ form.errors.phone }}
                </small>
            </div>

            <!-- Campo Password -->
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
                    autocomplete="new-password"
                    input-class="w-full"
                />
                <small v-if="form.errors.password" class="p-error">
                    <i class="pi pi-exclamation-triangle mr-1"></i>
                    {{ form.errors.password }}
                </small>
            </div>

            <!-- Campo Confirmar Password -->
            <div class="field">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="pi pi-lock mr-2"></i>
                    {{ t('auth.confirm_password') }}
                </label>
                <Password
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    :feedback="false"
                    :toggle-mask="true"
                    class="w-full"
                    :class="{ 'p-invalid': form.errors.password_confirmation }"
                    :placeholder="t('auth.confirm_password_placeholder')"
                    autocomplete="new-password"
                    input-class="w-full"
                />
                <small v-if="form.errors.password_confirmation" class="p-error">
                    <i class="pi pi-exclamation-triangle mr-1"></i>
                    {{ form.errors.password_confirmation }}
                </small>
            </div>

            <!-- Términos y condiciones -->
            <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="flex items-start">
                <Checkbox
                    id="terms"
                    v-model="form.terms"
                    :binary="true"
                    class="mr-2 mt-1"
                />
                <label for="terms" class="text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                    {{ t('auth.accept_terms_start') }} 
                    <a target="_blank" :href="route('terms.show')" class="font-medium text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-200 transition-colors duration-200">
                        {{ t('auth.terms_of_service') }}
                    </a> 
                    {{ t('auth.and') }} 
                    <a target="_blank" :href="route('policy.show')" class="font-medium text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-200 transition-colors duration-200">
                        {{ t('auth.privacy_policy') }}
                    </a>
                </label>
                <small v-if="form.errors.terms" class="p-error block w-full mt-1">
                    <i class="pi pi-exclamation-triangle mr-1"></i>
                    {{ form.errors.terms }}
                </small>
            </div>

            <!-- Botón de envío -->
            <Button
                type="submit"
                :label="form.processing ? t('auth.creating_account') : t('auth.create_account')"
                icon="pi pi-user-plus"
                class="w-full p-3 text-lg"
                :loading="form.processing"
                loading-icon="pi pi-spinner pi-spin"
                severity="primary"
            />
        </form>

        <!-- Enlaces adicionales -->
        <div class="text-center mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ t('auth.already_have_account') }} 
                <Link 
                    :href="route('login')" 
                    class="font-medium text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-200 transition-colors duration-200 ml-1"
                >
                    {{ t('auth.login_here') }}
                </Link>
            </p>
        </div>
    </AuthenticationCard>
</template>
