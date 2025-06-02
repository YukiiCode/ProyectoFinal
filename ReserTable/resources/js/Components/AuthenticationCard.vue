<template>
    <div class="auth-container">
        
        <!-- Contenido principal -->
        <div class="auth-content">
            <!-- Logo/Branding -->
            <div class="auth-logo-section">
                <slot name="logo" />
            </div>

            <!-- Card de formulario -->
            <div class="auth-card">
                <slot />
            </div>
        </div>
    </div>
</template>
