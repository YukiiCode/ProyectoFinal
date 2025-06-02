<template>
    <Toast position="top-right" class="global-toast" />
</template>

<script setup>
import Toast from 'primevue/toast';
</script>

<style>
/* Estilos globales para el Toast - no scoped para que afecte a toda la app */
.global-toast .p-toast {
    z-index: 9999;
}

.global-toast .p-toast-message {
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    background: rgba(255, 255, 255, 0.85) !important;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    margin-bottom: 1rem;
    min-width: 350px;
    color: #1f2937 !important;
}

/* Toast de éxito */
.global-toast .p-toast-message-success {
    background: rgba(34, 197, 94, 0.9) !important;
    border-color: rgba(34, 197, 94, 0.3);
    color: white !important;
}

.global-toast .p-toast-message-success .p-toast-message-icon,
.global-toast .p-toast-message-success .p-toast-summary,
.global-toast .p-toast-message-success .p-toast-detail {
    color: white !important;
}

/* Toast de error */
.global-toast .p-toast-message-error {
    background: rgba(239, 68, 68, 0.9) !important;
    border-color: rgba(239, 68, 68, 0.3);
    color: white !important;
}

.global-toast .p-toast-message-error .p-toast-message-icon,
.global-toast .p-toast-message-error .p-toast-summary,
.global-toast .p-toast-message-error .p-toast-detail {
    color: white !important;
}

/* Toast de advertencia */
.global-toast .p-toast-message-warn {
    background: rgba(245, 158, 11, 0.9) !important;
    border-color: rgba(245, 158, 11, 0.3);
    color: white !important;
}

.global-toast .p-toast-message-warn .p-toast-message-icon,
.global-toast .p-toast-message-warn .p-toast-summary,
.global-toast .p-toast-message-warn .p-toast-detail {
    color: white !important;
}

/* Toast de info */
.global-toast .p-toast-message-info {
    background: rgba(59, 130, 246, 0.9) !important;
    border-color: rgba(59, 130, 246, 0.3);
    color: white !important;
}

.global-toast .p-toast-message-info .p-toast-message-icon,
.global-toast .p-toast-message-info .p-toast-summary,
.global-toast .p-toast-message-info .p-toast-detail {
    color: white !important;
}

/* Animaciones mejoradas */
.global-toast .p-toast-message {
    animation: toastSlideIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes toastSlideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Modo oscuro */
:global(.dark) .global-toast .p-toast-message {
    background: rgba(30, 41, 59, 0.9) !important;
    border-color: rgba(148, 163, 184, 0.2);
    color: #f1f5f9 !important;
}

:global(.dark) .global-toast .p-toast-message-success {
    background: rgba(34, 197, 94, 0.9) !important;
    border-color: rgba(34, 197, 94, 0.3);
}

:global(.dark) .global-toast .p-toast-message-error {
    background: rgba(239, 68, 68, 0.9) !important;
    border-color: rgba(239, 68, 68, 0.3);
}

:global(.dark) .global-toast .p-toast-message-warn {
    background: rgba(245, 158, 11, 0.9) !important;
    border-color: rgba(245, 158, 11, 0.3);
}

:global(.dark) .global-toast .p-toast-message-info {
    background: rgba(59, 130, 246, 0.9) !important;
    border-color: rgba(59, 130, 246, 0.3);
}

/* Textos en modo oscuro */
:global(.dark) .global-toast .p-toast-summary,
:global(.dark) .global-toast .p-toast-detail {
    color: #f1f5f9 !important;
}

/* Botón de cerrar */
.global-toast .p-toast-icon-close-icon {
    color: currentColor;
    opacity: 0.7;
    transition: opacity 0.2s;
}

.global-toast .p-toast-icon-close:hover .p-toast-icon-close-icon {
    opacity: 1;
}

/* Responsive */
@media (max-width: 768px) {
    .global-toast .p-toast {
        left: 1rem !important;
        right: 1rem !important;
        top: 1rem !important;
        width: auto !important;
    }
    
    .global-toast .p-toast-message {
        min-width: auto;
        margin: 0 0 0.5rem 0;
    }
}
</style>
