// Inicialización temprana del tema antes de que Vue se monte
// Este script se ejecuta inmediatamente para evitar el flash de tema incorrecto

(function() {
    'use strict';
      // Función para aplicar el tema inmediatamente
    function applyThemeEarly() {
        // Verificar si hay datos del usuario en la página (para usuarios autenticados)
        const userDataScript = document.querySelector('div[data-page]');
        let userSettings = null;
        
        if (userDataScript) {
            try {
                const pageData = JSON.parse(userDataScript.getAttribute('data-page'));
                userSettings = pageData.props?.auth?.user?.settings;
            } catch (e) {
                console.warn('Error parsing user data:', e);
            }
        }
        
        if (userSettings) {
            // Usuario autenticado: aplicar configuraciones del usuario
            if (userSettings.dark_mode) {
                applyDarkModeEarly(true);
            } else {
                applyDarkModeEarly(false);
            }
            
            // Aplicar color del tema si existe
            if (userSettings.theme_color) {
                document.documentElement.setAttribute('data-theme-color', userSettings.theme_color);
            }
        } else {
            // Usuario no autenticado: comprobar localStorage
            const storedDarkMode = localStorage.getItem('darkMode');
            
            if (storedDarkMode !== null) {
                // Usar la preferencia guardada en localStorage
                applyDarkModeEarly(storedDarkMode === 'true');
            } else {
                // Verificar preferencia del sistema
                const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (prefersDark) {
                    applyDarkModeEarly(true);
                    localStorage.setItem('darkMode', 'true');
                    document.documentElement.setAttribute('data-prefers-dark', 'true');
                }
            }
        }
    }
    
    // Función para aplicar modo oscuro inmediatamente
    function applyDarkModeEarly(isDark) {
        if (isDark) {
            document.documentElement.classList.add('dark');
            document.body.classList.add('dark-mode', 'dark');
            
            // Forzar estilos importantes inmediatamente
            document.documentElement.style.colorScheme = 'dark';
            document.body.style.backgroundColor = 'rgb(17, 24, 39)';
            document.body.style.color = 'rgb(243, 244, 246)';
            
        } else {
            document.documentElement.classList.remove('dark');
            document.body.classList.remove('dark-mode', 'dark');
            
            // Restaurar estilos por defecto
            document.documentElement.style.colorScheme = 'light';
            document.body.style.backgroundColor = '';
            document.body.style.color = '';
        }
    }
    
    // Aplicar tema inmediatamente
    applyThemeEarly();
    
    // También aplicar cuando el DOM esté completamente cargado
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', applyThemeEarly);
    }
      // Escuchar cambios en la preferencia del sistema
    if (window.matchMedia) {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
            // Solo aplicar si no hay configuración de usuario guardada
            if (!localStorage.getItem('darkMode')) {
                applyDarkModeEarly(e.matches);
            }
        });
    }
})();
