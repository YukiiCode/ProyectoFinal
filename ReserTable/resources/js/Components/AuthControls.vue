<template>
    <div class="auth-controls">
        <!-- Controles de idioma y tema -->
        <div class="controls-container">
            <!-- Selector de idioma -->
            <div class="language-selector">
                <button 
                    @click="toggleLanguage"
                    class="control-button"
                    :title="t('common.change_language')"
                >
                    <i class="pi pi-globe"></i>
                    <span class="control-text">{{ currentLanguage.toUpperCase() }}</span>
                </button>
            </div>

            <!-- Toggle de tema -->
            <div class="theme-toggle">
                <button 
                    @click="toggleTheme"
                    class="control-button"
                    :title="isDarkMode ? t('common.light_mode') : t('common.dark_mode')"
                >
                    <i :class="isDarkMode ? 'pi pi-sun' : 'pi pi-moon'"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t, locale } = useI18n();

const isDarkMode = ref(false);
const currentLanguage = computed(() => locale.value);

// Función para alternar idioma
const toggleLanguage = () => {
    const newLocale = locale.value === 'es' ? 'en' : 'es';
    locale.value = newLocale;
    localStorage.setItem('locale', newLocale);
    
    // Actualizar el documento HTML
    document.documentElement.lang = newLocale;
};

// Función para alternar tema
const toggleTheme = () => {
    isDarkMode.value = !isDarkMode.value;
    
    // Aplicar/remover clase dark
    if (isDarkMode.value) {
        document.documentElement.classList.add('dark');
        document.body.classList.add('dark-mode', 'dark');
        localStorage.setItem('darkMode', 'true');
    } else {
        document.documentElement.classList.remove('dark');
        document.body.classList.remove('dark-mode', 'dark');
        localStorage.setItem('darkMode', 'false');
    }
    
    // Disparar evento personalizado
    window.dispatchEvent(new CustomEvent('theme-changed', { 
        detail: { isDarkMode: isDarkMode.value } 
    }));
};

// Inicializar estado del tema
onMounted(() => {
    // Verificar estado inicial del tema
    isDarkMode.value = document.documentElement.classList.contains('dark');
    
    // Escuchar cambios de tema
    window.addEventListener('theme-changed', (e) => {
        isDarkMode.value = e.detail.isDarkMode;
    });
});
</script>

<style scoped>
.auth-controls {
    position: fixed;
    top: 1rem;
    right: 1rem;
    z-index: 1000;
}

.controls-container {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.control-button {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.5rem;
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(0, 0, 0, 0.1);
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.875rem;
    color: #374151;
    backdrop-filter: blur(10px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.control-button:hover {
    background: rgba(255, 255, 255, 1);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.control-text {
    font-weight: 600;
    font-size: 0.75rem;
}

/* Estilos para modo oscuro */
:global(.dark) .control-button {
    background: rgba(31, 41, 55, 0.9);
    border-color: rgba(255, 255, 255, 0.1);
    color: #f3f4f6;
}

:global(.dark) .control-button:hover {
    background: rgba(31, 41, 55, 1);
    border-color: rgba(255, 255, 255, 0.2);
}

/* Animaciones */
.control-button i {
    transition: transform 0.2s ease;
}

.control-button:hover i {
    transform: scale(1.1);
}

/* Responsive */
@media (max-width: 640px) {
    .auth-controls {
        top: 0.75rem;
        right: 0.75rem;
    }
    
    .control-button {
        padding: 0.375rem;
        font-size: 0.75rem;
    }
    
    .controls-container {
        gap: 0.375rem;
    }
}
</style>
