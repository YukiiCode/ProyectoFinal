<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import { useNotifications } from '@/composables/useNotifications';

const props = defineProps({
    status: String,
});

const { showSuccess } = useNotifications();

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'), {
        onSuccess: () => {
            showSuccess('Enlace enviado', 'Hemos enviado un nuevo enlace de verificación a tu correo');
        }
    });
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <Head title="Verificar Correo Electrónico - ReserTable" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <!-- Título del formulario -->
        <div class="form-header">
            <h2 class="form-title">Verifica tu correo electrónico</h2>
            <p class="form-subtitle">Necesitamos verificar tu dirección de correo antes de continuar</p>
        </div>

        <!-- Descripción -->
        <div class="form-description">
            Antes de continuar, por favor verifica tu dirección de correo electrónico haciendo clic en el enlace que acabamos de enviarte. Si no recibiste el correo, con gusto te enviaremos otro.
        </div>

        <!-- Mensaje de estado -->
        <div v-if="verificationLinkSent" class="status-message success">
            <i class="fas fa-check-circle"></i>
            Un nuevo enlace de verificación ha sido enviado a tu correo electrónico.
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
                        Reenviar correo de verificación
                    </span>
                    <span v-else class="btn-loading">
                        <i class="fas fa-spinner fa-spin"></i>
                        Enviando...
                    </span>
                </button>
            </div>
        </form>

        <!-- Enlaces adicionales -->
        <div class="auth-footer">
            <div class="auth-footer-links">
                <Link :href="route('profile.show')" class="auth-link">
                    <i class="fas fa-user"></i>
                    Editar Perfil
                </Link>
                
                <Link :href="route('logout')" method="post" as="button" class="auth-link logout-link">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar Sesión
                </Link>
            </div>
        </div>
    </AuthenticationCard>
</template>
