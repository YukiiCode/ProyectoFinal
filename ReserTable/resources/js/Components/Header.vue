<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import SettingsDropdown from '@/Components/SettingsDropdown.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const user = usePage().props.auth?.user;
</script>

<template>
    <header class="bg-primary dark:bg-gray-800 text-white py-3 shadow-md transition-colors duration-300">
        <div class="container d-flex justify-content-between align-items-center">
            <Link :href="route('home')" class="fw-bold fs-4 text-white dark:text-gray-100 text-decoration-none hover:text-gray-200 dark:hover:text-white transition-colors">
                <i class="pi pi-utensils me-2"></i>
                {{ t('footer.company_name') }}
            </Link>
            
            <div class="d-flex align-items-center gap-3">
                <!-- Dropdown de configuraciones -->
                <SettingsDropdown />
                
                <nav class="d-flex align-items-center gap-2">
                    <slot>
                        <template v-if="user">                            <Link :href="route('admin.dashboard')" class="btn btn-outline-light btn-sm hover:bg-white hover:text-primary dark:hover:bg-gray-700 dark:hover:text-white transition-all">
                                <i class="pi pi-user me-1"></i>
                                {{ t('common.profile') }}
                            </Link>
                            <Link :href="route('logout')" method="post" as="button" class="btn btn-light btn-sm hover:bg-gray-100 transition-all">
                                <i class="pi pi-sign-out me-1"></i>
                                {{ t('common.logout') }}
                            </Link>
                        </template>
                        <template v-else>
                            <Link :href="route('login')" class="btn btn-outline-light btn-sm hover:bg-white hover:text-primary dark:hover:bg-gray-700 dark:hover:text-white transition-all">
                                <i class="pi pi-sign-in me-1"></i>
                                {{ t('common.login') }}
                            </Link>
                            <Link v-if="$page.props.canRegister" :href="route('register')" class="btn btn-light btn-sm hover:bg-gray-100 transition-all">
                                <i class="pi pi-user-plus me-1"></i>
                                {{ t('common.register') }}
                            </Link>
                        </template>
                    </slot>
                </nav>
            </div>
        </div>
    </header>
</template>

<style scoped>
/* Mejoras para el header en modo oscuro */
.btn-outline-light:hover {
    background-color: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.3);
}

.dark .btn-outline-light:hover {
    background-color: rgba(75, 85, 99, 0.5);
    border-color: rgba(156, 163, 175, 0.5);
}

header {
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.dark header {
    border-bottom: 1px solid rgba(75, 85, 99, 0.3);
}
</style>
