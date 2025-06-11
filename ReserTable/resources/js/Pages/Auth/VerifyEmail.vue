<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import { useNotifications } from '@/composables/useNotifications';
import { useI18n } from 'vue-i18n';
import ThemeManager from '@/Components/ThemeManager.vue';

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

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <!-- Título del formulario -->
        <div class="form-header">
            <h2 class="form-title">{{ t('auth.verify_email') }}</h2>
            <p class="form-subtitle">{{ t('auth.verify_email_subtitle') }}</p>
        </div>

        <!-- Descripción -->
        <div class="form-description">
            {{ t('auth.verify_email_description') }}
        </div>

        <!-- Mensaje de estado -->
        <div v-if="verificationLinkSent" class="status-message success">
            <i class="fas fa-check-circle"></i>
            {{ t('auth.verification_link_sent') }}
        </div>

        <form @submit.prevent="submit" class="verify-form">
            <!-- Botones de acción -->
            <div class="form-actions">
                <button 
                    type="submit" 
                    class="btn-primary"
                    :class="{ 'loading': form.processing }" 
                    :disabled="form.processing"
                >
                    <span v-if="!form.processing" class="btn-content">
                        <i class="fas fa-envelope"></i>
                        {{ t('auth.resend_verification') }}
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
            <div class="auth-footer-links">
                <Link :href="route('profile.show')" class="auth-link">
                    <i class="fas fa-user"></i>
                    {{ t('auth.edit_profile') }}
                </Link>
                
                <Link :href="route('logout')" method="post" as="button" class="auth-link logout-link">
                    <i class="fas fa-sign-out-alt"></i>
                    {{ t('auth.logout') }}
                </Link>
            </div>
        </div>
    </AuthenticationCard>
</template>
