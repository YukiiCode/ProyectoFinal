<template>  <aside class="fixed inset-y-0 left-0 z-50 w-64 transform bg-white dark:bg-gray-800 shadow-lg transition-all duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0" 
         :class="{ '-translate-x-full': !isOpen, 'translate-x-0': isOpen }">
    
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">ReserTable</h2>
        </div>
      </div>
      <!-- Close button for mobile -->
      <button @click="$emit('closeSidebar')" 
              class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 px-4 py-6 space-y-2">
        <!-- Dashboard -->
      <Link :href="route('admin.dashboard')" 
            :class="routeClasses('admin.dashboard')"
            class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z" />
        </svg>
        {{ t('navigation.dashboard') }}
      </Link>

      <!-- Reservations -->
      <Link :href="route('admin.reservations')" 
            :class="routeClasses('admin.reservations')"
            class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        {{ t('navigation.reservations') }}
      </Link>

      <!-- Tables -->
      <Link :href="route('admin.tables')" 
            :class="routeClasses('admin.tables')"
            class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
        </svg>
        {{ t('navigation.tables') }}
      </Link>

      <!-- Menu -->
      <Link :href="route('admin.menu')" 
            :class="routeClasses('admin.menu')"
            class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>        {{ t('navigation.menu') }}
      </Link>      <!-- Discount Coupons -->
      <Link href="/admin/discount-coupons" 
            :class="routeClasses('admin.discount-coupons')"
            class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
        </svg>
        {{ t('navigation.discounts') }}
      </Link><!-- Divider -->
      <div class="border-t border-gray-200 dark:border-gray-600 my-4"></div><!-- Users -->
      <Link :href="route('admin.users')" 
            :class="routeClasses('admin.users')"
            class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
        </svg>
        {{ t('navigation.staff') }}
      </Link>

      <!-- Settings -->
      <Link :href="route('admin.settings')" 
            :class="routeClasses('admin.settings')"
            class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        {{ t('navigation.configuration') }}
      </Link>

    </nav>    <!-- Sidebar Footer -->
    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <img class="w-8 h-8 rounded-full" 
               :src="$page.props.auth.user.profile_photo_url" 
               :alt="$page.props.auth.user.name">
        </div>
        <div class="ml-3 flex-1 min-w-0">
          <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
            {{ $page.props.auth.user.name }}
          </p>
          <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
            {{ $page.props.auth.user.email }}
          </p>
        </div>
      </div>
    </div>
    
  </aside>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  }
})

defineEmits(['closeSidebar'])

// Helper function to determine active route classes
const routeClasses = (routeName) => {
  const currentRoute = route().current()
  const isActive = currentRoute === routeName
  
  return isActive 
    ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 border-r-2 border-blue-700 dark:border-blue-400'
    : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100'
}
</script>