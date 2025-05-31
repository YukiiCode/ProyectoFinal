<template>
    <div class="layout-footer">
        <div class="footer-content">
            <span>© {{ currentYear }} ReserTable - Sistema de Gestión de Restaurante</span>
            <span class="footer-links">
                <a href="#" class="footer-link">Soporte</a>
                <a href="#" class="footer-link">Términos</a>
                <a href="#" class="footer-link">Privacidad</a>
            </span>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const currentYear = computed(() => new Date().getFullYear())
</script>

<style scoped>
.layout-footer {
    padding: 1rem 2rem;
    border-top: 1px solid var(--surface-border);
    background-color: var(--surface-card);
    margin-top: auto;
}

.footer-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: var(--text-color-secondary);
    font-size: 0.875rem;
}

.footer-links {
    display: flex;
    gap: 1rem;
}

.footer-link {
    color: var(--text-color-secondary);
    text-decoration: none;
    transition: color 0.15s;
}

.footer-link:hover {
    color: var(--primary-color);
}

/* Mobile responsiveness */
@media screen and (max-width: 768px) {
    .footer-content {
        flex-direction: column;
        gap: 0.5rem;
        text-align: center;
    }
    
    .footer-links {
        justify-content: center;
    }
}
</style>
