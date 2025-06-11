<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import { useNotifications } from '@/composables/useNotifications';
import { useI18n } from 'vue-i18n';
import ThemeManager from '@/Components/ThemeManager.vue';
import AuthControls from '@/Components/AuthControls.vue';

// PrimeVue components
import Button from 'primevue/button';
import Message from 'primevue/message';

const props = defineProps({
    status: String,
});

const { t } = useI18n();
const { showSuccess } = useNotifications();

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'), {
        onSuccess: () => {
            showSuccess(t('auth.link_sent'), t('auth.verification_sent'));
        }
    });
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <Head :title="t('auth.verify_email_title')" />
    <ThemeManager />
    <AuthControls />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <!-- Mensaje de estado con PrimeVue -->
        <Message 
            v-if="verificationLinkSent" 
            severity="success" 
            :closable="false"
            class="mb-4"
        >
            <i class="pi pi-check-circle mr-2"></i>
            {{ t('auth.verification_link_sent') }}
        </Message>

        <!-- Título del formulario -->
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                {{ t('auth.verify_email') }}
            </h2>
            <p class="text-gray-600 dark:text-gray-400">
                {{ t('auth.verify_email_subtitle') }}
            </p>
        </div>

        <!-- Descripción -->
        <div class="text-gray-600 dark:text-gray-400 mb-6 text-center">
            {{ t('auth.verify_email_description') }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Botón de reenvío -->
            <Button
                type="submit"
                :label="form.processing ? t('auth.sending') : t('auth.resend_verification')"
                icon="pi pi-send"
                class="w-full p-3 text-lg"
                :loading="form.processing"
                loading-icon="pi pi-spinner pi-spin"
                severity="primary"
            />
        </form>

        <!-- Enlaces adicionales -->
        <div class="text-center mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 space-y-2">
            <div>
                <Link 
                    :href="route('profile.show')" 
                    class="text-sm text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-200 transition-colors duration-200"
                >
                    <i class="pi pi-user mr-1"></i>
                    {{ t('auth.edit_profile') }}
                </Link>
            </div>
            <div>
                <Link 
                    :href="route('logout')" 
                    method="post" 
                    as="button"
                    class="text-sm text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition-colors duration-200"
                >
                    <i class="pi pi-sign-out mr-1"></i>
                    {{ t('auth.logout') }}
                </Link>
            </div>
        </div>
    </AuthenticationCard>
</template>
