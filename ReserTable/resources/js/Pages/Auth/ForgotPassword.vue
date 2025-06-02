<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import { useNotifications } from '@/composables/useNotifications';

defineProps({
    status: String,
});

const { showSuccess, showError } = useNotifications();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'), {
        onSuccess: () => {
            showSuccess('Enlace enviado', 'Revisa tu correo electrónico para restablecer tu contraseña');
        },
        onError: () => {
            showError('No pudimos encontrar tu cuenta', 'Verifica que el email sea correcto');
        }
    });
};
</script>

<template>
    <Head title="Recuperar Contraseña - ReserTable" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <!-- Título del formulario -->
        <div class="form-header">
            <h2 class="form-title">¿Olvidaste tu contraseña?</h2>
            <p class="form-subtitle">No te preocupes, te enviaremos un enlace para restablecerla</p>
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
                    Correo Electrónico
                </label>
                <div class="input-wrapper">
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="form-input"
                        :class="{ 'error': form.errors.email }"
                        placeholder="tu@email.com"
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
                        Enviar enlace de recuperación
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
            <p class="auth-footer-text">
                ¿Recordaste tu contraseña? 
                <Link :href="route('login')" class="auth-link">
                    Volver al inicio de sesión
                </Link>
            </p>
        </div>
    </AuthenticationCard>
</template>


