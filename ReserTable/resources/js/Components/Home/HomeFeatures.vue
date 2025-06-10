<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const page = usePage();
</script>

<template>    <section id="features" class="mb-16">
        <h2 class="text-3xl font-semibold mb-8 text-center text-red-700 dark:text-red-500">
            <i class="pi pi-star mr-2"></i>
            {{ t('home.features.title') }}
        </h2>
        <div class="grid md:grid-cols-3 gap-8 text-center">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-200 dark:border-gray-700">
                <div class="mb-4">
                    <i class="pi pi-calendar text-3xl text-red-600 dark:text-red-400"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ t('home.features.online_reservations.title') }}</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">{{ t('home.features.online_reservations.description') }}</p>
                <Link :href="route('reserva')" class="inline-block bg-red-600 text-white px-6 py-2 rounded-md hover:bg-red-700 transition-colors duration-200 font-medium">
                    <i class="pi pi-calendar-plus mr-2"></i>
                    {{ t('welcome.reserve_now') }}
                </Link>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-200 dark:border-gray-700">
                <div class="mb-4">
                    <i class="pi pi-percentage text-3xl text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ t('home.features.authentic_menu.title') }}</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">{{ t('home.features.authentic_menu.description') }}</p>
                <Link href="#" class="inline-block bg-yellow-500 text-gray-900 px-6 py-2 rounded-md hover:bg-yellow-600 transition-colors duration-200 font-medium">
                    <i class="pi pi-tag mr-2"></i>
                    {{ t('welcome.view_menu') }}
                </Link>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-200 dark:border-gray-700">
                <div class="mb-4">
                    <i class="pi pi-user text-3xl text-gray-600 dark:text-gray-400"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ t('home.features.cozy_atmosphere.title') }}</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">{{ t('home.features.cozy_atmosphere.description') }}</p>                <Link v-if="page.props.auth.user" :href="route('admin.dashboard')" class="inline-block bg-gray-700 text-white px-6 py-2 rounded-md hover:bg-gray-800 transition-colors duration-200 font-medium">
                    <i class="pi pi-user-edit mr-2"></i>
                    {{ t('common.profile') }}
                </Link>
                <Link v-else :href="route('login')" class="inline-block bg-gray-700 text-white px-6 py-2 rounded-md hover:bg-gray-800 transition-colors duration-200 font-medium">
                    <i class="pi pi-sign-in mr-2"></i>
                    {{ t('common.login') }}
                </Link>
            </div>
        </div>
    </section>
</template>
