<template>    <button 
        @click="(e) => toggle(e)"
        type="button"
        class="p-2 rounded-lg transition-all duration-300 dark-mode-switch"
        :class="buttonClasses"
        aria-label="Cambiar modo oscuro"
    >
        <div class="relative">
            <transition name="toggle-fade" mode="out-in">
                <i v-if="isDarkMode" 
                   key="light" 
                   class="pi pi-sun dark-icon transform transition-transform duration-500"
                   style="color: #FFD700;" 
                ></i>
                <i v-else 
                   key="dark" 
                   class="pi pi-moon light-icon transform transition-transform duration-500"
                   style="color: #3498db;" 
                ></i>
            </transition>
        </div>
        <span v-if="showLabel" class="ml-2 transition-all duration-300">
            {{ isDarkMode ? 'Modo claro' : 'Modo oscuro' }}
        </span>
    </button>
</template>

<script setup>
import { computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';

const props = defineProps({
    size: {
        type: String,
        default: 'md', // sm, md, lg
    },
    variant: {
        type: String,
        default: 'default', // default, primary, minimal
    },
    showLabel: {
        type: Boolean,
        default: false,
    },
});

const page = usePage();

const isDarkMode = computed(() => {
    return page.props.auth?.user?.settings?.dark_mode || false;
});

const buttonClasses = computed(() => {
    const classes = {
        // Tamaños
        'text-sm': props.size === 'sm',
        'text-base': props.size === 'md',
        'text-lg': props.size === 'lg',
        
        // Variantes
        'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700': props.variant === 'default',
        'bg-primary-600 hover:bg-primary-700 text-white': props.variant === 'primary',
        'text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400': props.variant === 'minimal',
        
        // Efectos especiales
        'hover:shadow-md': true,
        'hover:scale-105': true,
        'transform': true,
    };
    
    return classes;
});

const toggle = (event) => {
    // Aplicar el cambio inmediatamente para una mejor experiencia de usuario    
    const newDarkModeState = !isDarkMode.value;
    applyDarkMode(newDarkModeState);
    
    // Obtener la posición del botón para el efecto de onda desde ese punto
    const rect = event.currentTarget.getBoundingClientRect();
    const x = (rect.left + rect.right) / 2;
    const y = (rect.top + rect.bottom) / 2;
    
    // Establecer el punto de origen de la onda en las variables CSS
    document.documentElement.style.setProperty('--x', `${x}px`);
    document.documentElement.style.setProperty('--y', `${y}px`);
    
    // Marcar algunos elementos importantes para animaciones especiales
    document.querySelectorAll('.admin-card, .card, .p-button, button, h1, h2').forEach(el => {
        el.classList.add('animated-element');
    });
    
    // Animación avanzada de transición para toda la página
    document.body.classList.add('theme-transitioning');
    
    // Agregar una clase que indique la dirección del cambio para efectos específicos
    if (newDarkModeState) {
        document.body.classList.add('to-dark-mode');
        document.body.classList.remove('to-light-mode');
        
        // Efectos específicos para la transición a modo oscuro
        document.querySelectorAll('h1, h2').forEach(heading => {
            heading.classList.add('main-title');
        });
    } else {
        document.body.classList.add('to-light-mode');
        document.body.classList.remove('to-dark-mode');
        
        // Efectos específicos para la transición a modo claro
        document.querySelectorAll('.card, .admin-card').forEach(card => {
            card.style.animation = 'light-reveal 0.8s forwards';
        });
    }
    
    // Eliminar clases después de que termine la transición
    setTimeout(() => {
        document.body.classList.remove('theme-transitioning', 'to-dark-mode', 'to-light-mode');
        document.querySelectorAll('.animated-element').forEach(el => {
            el.classList.remove('animated-element');
            el.style.animation = '';
        });
    }, 1000);
      // Enviar request para persistir el cambio si el usuario está autenticado
    if (page.props.auth?.user) {
        router.patch(route('admin.settings.dark-mode'), {
            dark_mode: newDarkModeState
        }, {
            preserveState: true,
            preserveScroll: true
        });
    } else {
        // Si el usuario no está autenticado, guardar en localStorage
        localStorage.setItem('darkMode', newDarkModeState ? 'true' : 'false');
    }
};

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
        
        // Añadir atributos de datos para CSS avanzado
        document.querySelectorAll('.admin-card, .card, .button, .menu-item').forEach(element => {
            element.setAttribute('data-theme-transition', 'true');
        });
        
        // Añadir clases especiales para efectos visuales
        document.querySelectorAll('h1, h2').forEach(heading => {
            heading.classList.add('main-title');
        });
        
        document.querySelectorAll('.p-button, .btn').forEach(button => {
            button.classList.add('interactive-element');
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
        
        // Añadir atributo data para CSS avanzado incluso al volver al tema claro
        document.querySelectorAll('.admin-card, .card, .button, .menu-item').forEach(element => {
            element.setAttribute('data-theme-transition', 'true');
        });
        
        // Remover clases especiales para efectos visuales
        document.querySelectorAll('h1, h2').forEach(heading => {
            heading.classList.remove('main-title');
        });
    }
    
    // Reiniciar atributos después de la transición
    setTimeout(() => {
        document.querySelectorAll('[data-theme-transition]').forEach(el => {
            el.removeAttribute('data-theme-transition');
        });
    }, 800);
    
    // Notificar a otros componentes sobre el cambio de tema mediante un evento personalizado
    window.dispatchEvent(new CustomEvent('theme-changed', { 
        detail: { isDarkMode: isDark } 
    }));
};
</script>
