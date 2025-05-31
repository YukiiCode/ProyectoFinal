<template>
    <div class="min-h-screen bg-gray-50">
        <AppTopbar @menu-toggle="onMenuToggle" />
        
        <!-- Sidebar -->
        <div class="fixed top-0 left-0 z-40 w-64 h-screen bg-white shadow-lg border-r border-gray-200 transition-transform duration-300 ease-in-out sidebar-scroll lg:translate-x-0" 
             :class="{ '-translate-x-full': !mobileMenuActive && !isDesktop(), 'translate-x-0': mobileMenuActive || isDesktop() }">
            <AppSidebar />
        </div>

        <!-- Main content -->
        <div class="pt-16 lg:pl-64 min-h-screen">
            <div class="p-6">
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
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import AppTopbar from './AppTopbar.vue'
import AppSidebar from './AppSidebar.vue'

const mobileMenuActive = ref(false)

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
})

onBeforeUnmount(() => {
    document.removeEventListener('click', onDocumentClick)
})
</script>
