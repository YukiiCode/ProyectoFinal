<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>

<template>
    <Head :title="t('home.hero.title')" />
    <header class="bg-cover bg-center h-96" style="background-image: url('https://images.unsplash.com/photo-1555396273-367ea4eb4db5?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');">
        <div class="flex items-center justify-center h-full bg-black bg-opacity-50">
            <div class="text-center">
                <h1 class="text-white text-5xl font-bold mb-4">{{ t('home.hero.title') }}</h1>
                <p class="text-white text-xl mb-8">{{ t('home.hero.subtitle') }}</p>
                <Link :href="route('reserva')" class="bg-yellow-500 text-gray-900 font-bold py-3 px-6 rounded-lg hover:bg-yellow-400 transition duration-300">
                    {{ t('home.hero.cta_reserve') }}
                </Link>
            </div>
        </div>
    </header>
</template>
