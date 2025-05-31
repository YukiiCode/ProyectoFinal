<template>
    <div class="fixed top-0 left-0 right-0 z-30 h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm transition-colors duration-300">
        <div class="flex items-center justify-between h-full px-4">            <!-- Brand -->
            <Link href="/" class="flex items-center text-xl font-bold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                <i class="pi pi-home mr-2 text-xl"></i>
                <span>ReserTable Admin</span>
            </Link>            <div class="flex items-center space-x-4">
                <!-- Mobile menu toggle -->
                <button 
                    class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200 lg:hidden" 
                    @click="onMenuToggle"
                    type="button"
                >
                    <i class="pi pi-bars text-lg"></i>
                </button>                <!-- Dark mode toggle -->
                <DarkModeToggle />

                <!-- User menu dropdown -->
                <div class="relative">
                    <button 
                        class="flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200" 
                        @click="onTopBarMenuButton"
                        type="button"
                    >
                        <i class="pi pi-ellipsis-v"></i>
                    </button>
                    
                    <!-- Dropdown menu -->                    <div v-if="topbarMenuActive" 
                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50 transition-colors duration-300">
                        <button @click="onTopBarMenuButton" 
                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <i class="pi pi-calendar mr-3 text-gray-400 dark:text-gray-500"></i>
                            <span>Calendario</span>
                        </button>
                        <button @click="onTopBarMenuButton" 
                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <i class="pi pi-inbox mr-3 text-gray-400 dark:text-gray-500"></i>
                            <span>Mensajes</span>
                        </button>
                        <button @click="onTopBarMenuButton" 
                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <i class="pi pi-user mr-3 text-gray-400 dark:text-gray-500"></i>
                            <span>Perfil</span>
                        </button>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <button @click="logout" 
                                class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                            <i class="pi pi-sign-out mr-3 text-red-400 dark:text-red-500"></i>
                            <span>Salir</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router, Link, usePage } from '@inertiajs/vue3'
import DarkModeToggle from '@/Components/DarkModeToggle.vue'

const emit = defineEmits(['menu-toggle'])
const page = usePage()

const topbarMenuActive = ref(false)

const isDarkMode = computed(() => {
    return page.props.auth?.user?.settings?.dark_mode || false
})

const onMenuToggle = () => {
    emit('menu-toggle')
}

const onTopBarMenuButton = () => {
    topbarMenuActive.value = !topbarMenuActive.value
}

const logout = () => {
    router.post(route('logout'))
}

// Ya no necesitamos estas funciones porque ahora usamos el componente DarkModeToggle
</script>
