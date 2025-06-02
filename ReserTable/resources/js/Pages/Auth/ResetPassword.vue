<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import { useNotifications } from '@/composables/useNotifications';

const props = defineProps({
    email: String,
    token: String,
});

const { showSuccess, showError } = useNotifications();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
        onSuccess: () => {
            showSuccess('Contraseña actualizada', 'Tu contraseña ha sido restablecida exitosamente');
        },
        onError: () => {
            showError('Error al restablecer contraseña', 'Verifica que los datos sean correctos');
        }
    });
};
</script>

<template>
    <Head title="Restablecer Contraseña - ReserTable" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <!-- Título del formulario -->
        <div class="form-header">
            <h2 class="form-title">Restablecer contraseña</h2>
            <p class="form-subtitle">Ingresa tu nueva contraseña para acceder a tu cuenta</p>
        </div>

        <form @submit.prevent="submit" class="reset-form">
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
                        readonly
                    />
                </div>
                <div v-if="form.errors.email" class="input-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ form.errors.email }}
                </div>
            </div>

            <!-- Campo Nueva Contraseña -->
            <div class="input-group">
                <label for="password" class="input-label">
                    <i class="fas fa-lock"></i>
                    Nueva Contraseña
                </label>
                <div class="input-wrapper">
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="form-input"
                        :class="{ 'error': form.errors.password }"
                        placeholder="Tu nueva contraseña"
                        required
                        autocomplete="new-password"
                    />
                </div>
                <div v-if="form.errors.password" class="input-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ form.errors.password }}
                </div>
            </div>

            <!-- Campo Confirmar Contraseña -->
            <div class="input-group">
                <label for="password_confirmation" class="input-label">
                    <i class="fas fa-lock"></i>
                    Confirmar Nueva Contraseña
                </label>
                <div class="input-wrapper">
                    <input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="form-input"
                        :class="{ 'error': form.errors.password_confirmation }"
                        placeholder="Confirma tu nueva contraseña"
                        required
                        autocomplete="new-password"
                    />
                </div>
                <div v-if="form.errors.password_confirmation" class="input-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ form.errors.password_confirmation }}
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
                        <i class="fas fa-key"></i>
                        Restablecer Contraseña
                    </span>
                    <span v-else class="btn-loading">
                        <i class="fas fa-spinner fa-spin"></i>
                        Restableciendo...
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
