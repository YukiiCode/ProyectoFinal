<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import { useNotifications } from '@/composables/useNotifications';
import { ref } from 'vue';

// PrimeVue components
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Checkbox from 'primevue/checkbox';
import Password from 'primevue/password';
import Message from 'primevue/message';

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

const showPassword = ref(false);

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

        <!-- Mensaje de estado con PrimeVue Message -->
        <Message v-if="status" severity="success" :closable="false" class="auth-status-message">
            {{ status }}
        </Message>

        <!-- Título del formulario -->
        <div class="form-header">
            <h2 class="form-title">Bienvenido de nuevo</h2>
            <p class="form-subtitle">Inicia sesión en tu cuenta para continuar</p>
        </div>

        <form @submit.prevent="submit" class="login-form">
            <!-- Campo Email con PrimeVue InputText -->
            <div class="input-group">
                <label for="email" class="input-label">
                    <i class="pi pi-envelope"></i>
                    Correo Electrónico
                </label>
                <div class="input-wrapper">
                    <InputText
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="p-inputtext-auth"
                        :class="{ 'p-invalid': form.errors.email }"
                        placeholder="tu@email.com"
                        required
                        autofocus
                        autocomplete="username"
                    />
                </div>
                <small v-if="form.errors.email" class="p-error input-error">
                    <i class="pi pi-exclamation-triangle"></i>
                    {{ form.errors.email }}
                </small>
            </div>

            <!-- Campo Password con PrimeVue Password -->
            <div class="input-group">
                <label for="password" class="input-label">
                    <i class="pi pi-lock"></i>
                    Contraseña
                </label>
                <div class="input-wrapper">
                    <Password
                        id="password"
                        v-model="form.password"
                        class="p-password-auth"
                        :class="{ 'p-invalid': form.errors.password }"
                        placeholder="Tu contraseña"
                        :feedback="false"
                        :toggle-mask="true"
                        autocomplete="current-password"
                        required
                    />
                </div>
                <small v-if="form.errors.password" class="p-error input-error">
                    <i class="pi pi-exclamation-triangle"></i>
                    {{ form.errors.password }}
                </small>
            </div>

            <!-- Remember me con PrimeVue Checkbox -->
            <div class="form-options">
                <div class="checkbox-wrapper">
                    <Checkbox 
                        v-model="form.remember" 
                        input-id="remember"
                        binary
                        class="p-checkbox-auth"
                    />
                    <label for="remember" class="checkbox-text">
                        Recordar mi sesión
                    </label>
                </div>
            </div>

            <!-- Botones de acción con PrimeVue Button -->
            <div class="form-actions">
                <Button 
                    type="submit" 
                    class="p-button-auth p-button-primary"
                    :loading="form.processing"
                    :disabled="form.processing"
                    icon="pi pi-sign-in"
                    label="Iniciar Sesión"
                    :loading-icon="form.processing ? 'pi pi-spin pi-spinner' : undefined"
                />

                <Link 
                    v-if="canResetPassword" 
                    :href="route('password.request')" 
                    class="forgot-password-link"
                >
                    <i class="pi pi-key"></i>
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

<style scoped>
/* Estilos adicionales específicos para esta implementación con PrimeVue */
.auth-status-message {
    margin-bottom: 1.5rem;
    border-radius: 0.75rem;
}

.p-inputtext-auth {
    width: 100%;
}

.p-password-auth {
    width: 100%;
}

.p-password-auth .p-password-input {
    width: 100%;
}

.checkbox-wrapper {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.checkbox-text {
    font-size: 0.875rem;
    color: rgb(107, 114, 128);
    user-select: none;
    cursor: pointer;
}

.p-button-auth {
    width: 100%;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: 0.75rem;
    transition: all 0.2s ease-in-out;
}

.p-button-auth.p-button-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    border: none;
    box-shadow: 0 4px 14px 0 rgba(59, 130, 246, 0.25);
}

.p-button-auth.p-button-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px 0 rgba(59, 130, 246, 0.35);
}

.p-button-auth.p-button-primary:active {
    transform: translateY(0);
}

/* Dark mode adaptations for PrimeVue components */
html.dark .p-inputtext-auth,
html.dark .p-password-auth .p-password-input {
    background-color: rgb(31, 41, 55);
    border-color: rgb(75, 85, 99);
    color: rgb(243, 244, 246);
}

html.dark .p-inputtext-auth:focus,
html.dark .p-password-auth .p-password-input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

html.dark .checkbox-text {
    color: rgb(156, 163, 175);
}

html.dark .p-checkbox-auth .p-checkbox-box {
    background-color: rgb(31, 41, 55);
    border-color: rgb(75, 85, 99);
}

html.dark .p-checkbox-auth .p-checkbox-box.p-highlight {
    background-color: #3b82f6;
    border-color: #3b82f6;
}
</style>
