<template>
    <!-- Este componente no tiene representación visual, solo lógica -->
</template>

<script setup>
import { onMounted, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

// Función para aplicar el modo oscuro según la configuración del usuario
const applyUserThemeSettings = () => {
    const userSettings = page.props.auth?.user?.settings;
    
    if (userSettings) {
        // Usuario autenticado: aplicar configuraciones del usuario
        // Aplicar modo oscuro
        if (userSettings.dark_mode) {
            applyDarkMode(true);
        } else {
            applyDarkMode(false);
        }
        
        // Aplicar color del tema
        if (userSettings.theme_color) {
            document.documentElement.setAttribute('data-theme-color', userSettings.theme_color);
        }
    } else {
        // Usuario no autenticado: comprobar localStorage
        const storedDarkMode = localStorage.getItem('darkMode');
        
        if (storedDarkMode !== null) {
            // Usar la preferencia guardada en localStorage
            applyDarkMode(storedDarkMode === 'true');
        } else {
            // Verificar si hay preferencia de sistema para modo oscuro
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (prefersDark) {
                applyDarkMode(true);
                localStorage.setItem('darkMode', 'true');
                document.documentElement.setAttribute('data-prefers-dark', 'true');
            }
        }
    }
};

// Función centralizada para aplicar el modo oscuro
const applyDarkMode = (isDark) => {
    if (isDark) {
        document.documentElement.classList.add('dark');
        document.body.classList.add('dark-mode', 'dark');
        
        // Forzar estilos importantes
        document.documentElement.style.colorScheme = 'dark';
        document.body.style.backgroundColor = 'rgb(17, 24, 39)';
        document.body.style.color = 'rgb(243, 244, 246)';
        
        // Aplicar clases a elementos comunes
        const elements = document.querySelectorAll('.card, .admin-card, .bg-white, .bg-gray-50, .bg-gray-100');
        elements.forEach(el => {
            el.classList.add('dark-element');
        });
        
    } else {
        document.documentElement.classList.remove('dark');
        document.body.classList.remove('dark-mode', 'dark');
        
        // Restaurar estilos por defecto
        document.documentElement.style.colorScheme = 'light';
        document.body.style.backgroundColor = '';
        document.body.style.color = '';
        
        // Remover clases dark de elementos
        const elements = document.querySelectorAll('.dark-element');
        elements.forEach(el => {
            el.classList.remove('dark-element');
        });
    }
    
    // Disparar evento personalizado para notificar el cambio
    window.dispatchEvent(new CustomEvent('theme-changed', { 
        detail: { isDarkMode: isDark } 
    }));
};

// Observar cambios en los props y aplicar tema cuando cambien
watch(
    () => page.props.auth?.user?.settings,
    () => {
        applyUserThemeSettings();
    },
    { deep: true }
);

// Aplicar tema al montar el componente
onMounted(() => {
    applyUserThemeSettings();
    
    // Escuchar cambios en la preferencia del sistema
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        if (!page.props.auth?.user?.settings) {
            // Solo aplicar preferencia del sistema si el usuario no tiene configuración personalizada
            applyDarkMode(e.matches);
        }
    });
    
    // Seguimiento de la posición del ratón para efectos de ondas
    document.addEventListener('mousemove', (e) => {
        document.documentElement.style.setProperty('--x', `${e.clientX}px`);
        document.documentElement.style.setProperty('--y', `${e.clientY}px`);
    });
    
    // Detectar si estamos en una tablet/móvil para deshabilitar efectos pesados
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    if (isMobile) {
        document.documentElement.classList.add('mobile-device');
    }
    
    // Observador de mutaciones para aplicar estilos a elementos dinámicos
    const observer = new MutationObserver((mutations) => {
        const isDark = document.documentElement.classList.contains('dark');
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1) { // Element node
                    if (isDark && (node.classList?.contains('card') || 
                                  node.classList?.contains('admin-card') ||
                                  node.classList?.contains('bg-white'))) {
                        node.classList.add('dark-element');
                    }
                }
            });
        });
    });
    
    observer.observe(document.body, { 
        childList: true, 
        subtree: true 
    });
});
</script>
