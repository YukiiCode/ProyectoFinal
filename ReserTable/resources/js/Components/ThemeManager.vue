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
            document.documentElement.classList.add('dark');
            document.body.classList.add('dark-mode');
        } else {
            document.documentElement.classList.remove('dark');
            document.body.classList.remove('dark-mode');
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
            if (storedDarkMode === 'true') {
                document.documentElement.classList.add('dark');
                document.body.classList.add('dark-mode');
            } else {
                document.documentElement.classList.remove('dark');
                document.body.classList.remove('dark-mode');
            }
        } else {            // Verificar si hay preferencia de sistema para modo oscuro
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (prefersDark) {
                document.documentElement.classList.add('dark');
                document.body.classList.add('dark-mode');
                localStorage.setItem('darkMode', 'true');
                
                // Aplicamos también los efectos dinámicos 
                document.documentElement.setAttribute('data-prefers-dark', 'true');
            }
        }
    }
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
            if (e.matches) {
                document.documentElement.classList.add('dark');
                document.body.classList.add('dark-mode');
            } else {
                document.documentElement.classList.remove('dark');
                document.body.classList.remove('dark-mode');
            }
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
});
</script>
