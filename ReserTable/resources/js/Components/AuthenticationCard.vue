<template>
    <div class="auth-container">
        <!-- Fondo de imagen de restaurante -->
        <div class="auth-background">
            <div class="auth-overlay"></div>
        </div>
        
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
