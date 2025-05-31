<template>
    <div class="min-h-screen transition-colors duration-300" 
         :class="isDarkMode ? 'bg-gray-900' : 'bg-gray-50'">
        <ThemeManager />
        <AppTopbar @menu-toggle="onMenuToggle" />
        
        <!-- Sidebar -->
        <div class="fixed top-0 left-0 z-40 w-64 h-screen shadow-lg border-r transition-all duration-300 ease-in-out sidebar-scroll lg:translate-x-0" 
             :class="[
                 isDarkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200',
                 { '-translate-x-full': !mobileMenuActive && !isDesktop(), 'translate-x-0': mobileMenuActive || isDesktop() }
             ]">
            <AppSidebar />
        </div>

        <!-- Main content -->
        <div class="pt-16 lg:pl-64 min-h-screen transition-colors duration-300" 
             :class="isDarkMode ? 'bg-gray-900' : 'bg-gray-50'">
            <div class="p-6" :class="isDarkMode ? 'bg-gray-900' : 'bg-gray-50'">
                <slot />
            </div>
        </div>

        <!-- Mobile overlay -->
        <div v-if="mobileMenuActive && !isDesktop()" 
             class="fixed inset-0 z-30 bg-gray-900 bg-opacity-50 lg:hidden mobile-sidebar-overlay"
             @click="hideMenu"></div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AppTopbar from './AppTopbar.vue'
import AppSidebar from './AppSidebar.vue'
import ThemeManager from '@/Components/ThemeManager.vue'

const mobileMenuActive = ref(false)
const page = usePage()

// Función para aplicar el modo oscuro (declarada antes del watcher)
const applyDarkMode = (isDark) => {
    if (isDark) {
        document.documentElement.classList.add('dark')
        document.body.classList.add('dark-mode')
        document.body.style.backgroundColor = 'rgb(17, 24, 39)'
        document.documentElement.style.backgroundColor = 'rgb(17, 24, 39)'
    } else {
        document.documentElement.classList.remove('dark')
        document.body.classList.remove('dark-mode')
        document.body.style.backgroundColor = ''
        document.documentElement.style.backgroundColor = ''
    }
}

// Obtener configuraciones del usuario desde el backend
const isDarkMode = computed(() => {
    return page.props.auth?.user?.settings?.dark_mode || false
})

// Aplicar modo oscuro cuando cambie
watch(isDarkMode, (newValue) => {
    applyDarkMode(newValue)
}, { immediate: true })

const onMenuToggle = () => {
    if (isDesktop()) {
        // En desktop, el sidebar siempre está visible
        return
    } else {
        mobileMenuActive.value = !mobileMenuActive.value
    }
}

const hideMenu = () => {
    mobileMenuActive.value = false
}

const isDesktop = () => {
    return window.innerWidth >= 1024 // lg breakpoint in Tailwind
}

const onDocumentClick = () => {
    if (!isDesktop()) {
        mobileMenuActive.value = false
    }
}

onMounted(() => {
    document.addEventListener('click', onDocumentClick)
    // Aplicar modo oscuro al montar el componente
    applyDarkMode(isDarkMode.value)
})

onBeforeUnmount(() => {
    document.removeEventListener('click', onDocumentClick)
})
</script>
