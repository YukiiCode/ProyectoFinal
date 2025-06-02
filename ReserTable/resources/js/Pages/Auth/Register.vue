<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import { useNotifications } from '@/composables/useNotifications';

const { showSuccess, showError } = useNotifications();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
        onSuccess: () => {
            showSuccess('¡Bienvenido a ReserTable!', 'Tu cuenta ha sido creada exitosamente');
        },
        onError: (errors) => {
            if (errors.email) {
                showError('El email ya está registrado', 'Error de registro');
            } else {
                showError('Por favor, verifica los datos ingresados', 'Error de registro');
            }
        }
    });
};
</script>

<template>
    <Head title="Registro - ReserTable" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <!-- Título del formulario -->
        <div class="form-header">
            <h2 class="form-title">Crear cuenta</h2>
            <p class="form-subtitle">Únete a ReserTable y comienza a gestionar tu restaurante</p>
        </div>

        <form @submit.prevent="submit" class="register-form">
            <!-- Campo Nombre -->
            <div class="input-group">
                <label for="name" class="input-label">
                    <i class="fas fa-user"></i>
                    Nombre completo
                </label>
                <div class="input-wrapper">
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="form-input"
                        :class="{ 'error': form.errors.name }"
                        placeholder="Tu nombre completo"
                        required
                        autofocus
                        autocomplete="name"
                    />
                </div>
                <div v-if="form.errors.name" class="input-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ form.errors.name }}
                </div>
            </div>

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
                        autocomplete="new-password"
                    />
                </div>
                <div v-if="form.errors.password" class="input-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ form.errors.password }}
                </div>
            </div>

            <!-- Campo Confirmar Password -->
            <div class="input-group">
                <label for="password_confirmation" class="input-label">
                    <i class="fas fa-lock"></i>
                    Confirmar contraseña
                </label>
                <div class="input-wrapper">
                    <input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="form-input"
                        :class="{ 'error': form.errors.password_confirmation }"
                        placeholder="Confirma tu contraseña"
                        required
                        autocomplete="new-password"
                    />
                </div>
                <div v-if="form.errors.password_confirmation" class="input-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ form.errors.password_confirmation }}
                </div>
            </div>

            <!-- Términos y condiciones -->
            <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="form-options">
                <label class="checkbox-label">
                    <input 
                        v-model="form.terms" 
                        type="checkbox" 
                        name="terms" 
                        class="checkbox-input"
                        required
                    />
                    <span class="checkbox-custom"></span>
                    <span class="checkbox-text">
                        Acepto los 
                        <a target="_blank" :href="route('terms.show')" class="auth-link">Términos de Servicio</a> 
                        y la 
                        <a target="_blank" :href="route('policy.show')" class="auth-link">Política de Privacidad</a>
                    </span>
                </label>
                <div v-if="form.errors.terms" class="input-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ form.errors.terms }}
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
                        <i class="fas fa-user-plus"></i>
                        Crear cuenta
                    </span>
                    <span v-else class="btn-loading">
                        <i class="fas fa-spinner fa-spin"></i>
                        Creando cuenta...
                    </span>
                </button>
            </div>
        </form>

        <!-- Enlaces adicionales -->
        <div class="auth-footer">
            <p class="auth-footer-text">
                ¿Ya tienes cuenta? 
                <Link :href="route('login')" class="auth-link">
                    Inicia sesión aquí
                </Link>
            </p>
        </div>
    </AuthenticationCard>
</template>
