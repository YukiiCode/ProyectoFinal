<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import { useNotifications } from '@/composables/useNotifications';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const { showSuccess, showError } = useNotifications();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
        onSuccess: () => {
            showSuccess('Bienvenido a ReserTable', 'Inicio de sesión exitoso');
        },
        onError: () => {
            showError('Credenciales incorrectas. Por favor, inténtalo de nuevo.', 'Error de autenticación');
        }
    });
};
</script>

<template>
    <Head title="Iniciar Sesión - ReserTable" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <!-- Mensaje de estado -->
        <div v-if="status" class="status-message success">
            <i class="fas fa-check-circle"></i>
            {{ status }}
        </div>

        <!-- Título del formulario -->
        <div class="form-header">
            <h2 class="form-title">Bienvenido de nuevo</h2>
            <p class="form-subtitle">Inicia sesión en tu cuenta para continuar</p>
        </div>

        <form @submit.prevent="submit" class="login-form">
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

            <!-- Campo Password -->
            <div class="input-group">
                <label for="password" class="input-label">
                    <i class="fas fa-lock"></i>
                    Contraseña
                </label>
                <div class="input-wrapper">
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="form-input"
                        :class="{ 'error': form.errors.password }"
                        placeholder="Tu contraseña"
                        required
                        autocomplete="current-password"
                    />
                </div>
                <div v-if="form.errors.password" class="input-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ form.errors.password }}
                </div>
            </div>

            <!-- Remember me -->
            <div class="form-options">
                <label class="checkbox-label">
                    <input 
                        v-model="form.remember" 
                        type="checkbox" 
                        name="remember" 
                        class="checkbox-input"
                    />
                    <span class="checkbox-custom"></span>
                    <span class="checkbox-text">Recordar mi sesión</span>
                </label>
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
                        Iniciar Sesión
                    </span>
                    <span v-else class="btn-loading">
                        <i class="fas fa-spinner fa-spin"></i>
                        Iniciando sesión...
                    </span>
                </button>

                <Link 
                    v-if="canResetPassword" 
                    :href="route('password.request')" 
                    class="forgot-password-link"
                >
                    <i class="fas fa-key"></i>
                    ¿Olvidaste tu contraseña?
                </Link>
            </div>
        </form>

        <!-- Enlaces adicionales -->
        <div class="auth-footer">
            <p class="auth-footer-text">
                ¿No tienes cuenta? 
                <Link :href="route('register')" class="auth-link">
                    Regístrate aquí
                </Link>
            </p>
        </div>
    </AuthenticationCard>
</template>
