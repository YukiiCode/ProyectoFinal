<script setup>
import { Link, usePage, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import SettingsDropdown from '@/Components/SettingsDropdown.vue'

const { t } = useI18n()
const page = usePage()

const props = defineProps({
    currentPage: {
        type: String,
        default: 'home'
    }
})

// Estado del menú móvil
const isMobileMenuOpen = ref(false)

// Usuario autenticado
const user = computed(() => page.props.auth?.user || null)
const isAuthenticated = computed(() => !!user.value)

// Función para cerrar sesión
const logout = () => {
    if (confirm(t('auth.confirm_logout') || '¿Estás seguro de que quieres cerrar sesión?')) {
        router.post('/logout')
    }
}

// Función para determinar si un enlace está activo
const isActiveLink = (pageName) => {
    return props.currentPage === pageName
}

// Clases para enlaces activos e inactivos
const getLinkClasses = (pageName) => {
    if (isActiveLink(pageName)) {
        return 'text-red-600 dark:text-red-400 font-semibold'
    }
    return 'text-gray-600 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 transition-colors'
}

// Clases para enlaces móviles
const getMobileLinkClasses = (pageName) => {
    if (isActiveLink(pageName)) {
        return 'text-red-600 dark:text-red-400 font-semibold bg-red-50 dark:bg-red-900/20'
    }
    return 'text-gray-600 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 hover:bg-gray-50 dark:hover:bg-gray-700'
}

// Función para cerrar menú móvil
const closeMobileMenu = () => {
    isMobileMenuOpen.value = false
}
</script>

<template>
    <!-- Modern Navigation -->
    <nav class="bg-white dark:bg-gray-800 shadow-md sticky top-0 z-50 backdrop-blur-sm bg-white/95 dark:bg-gray-800/95">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <Link href="/" class="flex items-center space-x-2" @click="closeMobileMenu">
                    <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center">
                        <span class="text-white font-bold text-xl">R</span>
                    </div>
                    <span class="text-2xl font-bold bg-gradient-to-r from-red-600 to-red-500 bg-clip-text text-transparent">
                        ReserTable
                    </span>
                </Link>

                <!-- Desktop Navigation Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <Link href="/" :class="getLinkClasses('home')">
                        {{ t('common.home') }}
                    </Link>
                    <Link href="/menu" :class="getLinkClasses('menu')">
                        {{ t('common.menu') }}
                    </Link>
                    <Link href="/reservas" :class="getLinkClasses('reservations')">
                        {{ t('common.reservations') }}
                    </Link>
                </div>

                <!-- Desktop Auth & Settings -->
                <div class="hidden md:flex items-center space-x-4">
                    <div v-if="isAuthenticated" class="flex items-center space-x-4">
                        <div class="hidden lg:flex items-center space-x-2">
                            <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">{{ user.name.charAt(0).toUpperCase() }}</span>
                            </div>
                            <span class="text-gray-900 dark:text-white font-medium">{{ user.name }}</span>
                        </div>
                        <button 
                            @click="logout"
                            class="text-gray-600 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 transition-colors"
                        >
                            {{ t('common.logout') }}
                        </button>
                    </div>
                    <div v-else class="flex items-center space-x-3">
                        <Link href="/login" class="text-gray-600 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 transition-colors font-medium">
                            {{ t('common.login') }}
                        </Link>
                        <Link href="/register" class="bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white px-6 py-2.5 rounded-full font-medium transition-all duration-300 shadow-lg hover:shadow-xl">
                            {{ t('common.register') }}
                        </Link>
                    </div>
                    <SettingsDropdown variant="public" />
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center space-x-2">
                    <SettingsDropdown variant="public" />
                    <button 
                        @click="isMobileMenuOpen = !isMobileMenuOpen"
                        class="p-2 rounded-md text-gray-600 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                        :class="{ 'text-red-600 dark:text-red-400': isMobileMenuOpen }"
                    >
                        <svg v-if="!isMobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div v-show="isMobileMenuOpen" class="md:hidden bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
            <div class="px-4 py-2 space-y-1">
                <!-- Navigation Links -->
                <Link 
                    href="/" 
                    :class="getMobileLinkClasses('home')"
                    class="block px-3 py-2 rounded-md text-base font-medium transition-colors"
                    @click="closeMobileMenu"
                >
                    {{ t('common.home') }}
                </Link>
                <Link 
                    href="/menu" 
                    :class="getMobileLinkClasses('menu')"
                    class="block px-3 py-2 rounded-md text-base font-medium transition-colors"
                    @click="closeMobileMenu"
                >
                    {{ t('common.menu') }}
                </Link>
                <Link 
                    href="/reservas" 
                    :class="getMobileLinkClasses('reservations')"
                    class="block px-3 py-2 rounded-md text-base font-medium transition-colors"
                    @click="closeMobileMenu"
                >
                    {{ t('common.reservations') }}
                </Link>

                <!-- Mobile Auth Section -->
                <div class="pt-4 pb-2 border-t border-gray-200 dark:border-gray-700">
                    <div v-if="isAuthenticated" class="space-y-2">
                        <!-- User Info -->
                        <div class="flex items-center space-x-3 px-3 py-2">
                            <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">{{ user.name.charAt(0).toUpperCase() }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ user.name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ user.email }}</p>
                            </div>
                        </div>
                        <button 
                            @click="logout"
                            class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-600 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                        >
                            {{ t('common.logout') }}
                        </button>
                    </div>
                    <div v-else class="space-y-2">
                        <Link 
                            href="/login" 
                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                            @click="closeMobileMenu"
                        >
                            {{ t('common.login') }}
                        </Link>
                        <Link 
                            href="/register" 
                            class="block mx-3 px-4 py-2 rounded-full text-base font-medium bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white text-center shadow-lg transition-all duration-300"
                            @click="closeMobileMenu"
                        >
                            {{ t('common.register') }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>
