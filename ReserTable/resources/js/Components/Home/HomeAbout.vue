<script setup>
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>

<template>
    <section id="about" class="mb-16">
        <h2 class="text-3xl font-semibold mb-6 text-center text-red-700 dark:text-red-500">
            <i class="pi pi-heart mr-2"></i>
            {{ t('home.about.title') }}
        </h2>
        <div class="text-gray-700 dark:text-gray-300 space-y-4">
            <p class="text-lg leading-relaxed">
                {{ t('home.about.description_1') }}
            </p>
            <p class="text-lg leading-relaxed">
                {{ t('home.about.description_2') }}
            </p>
        </div>
    </section>
</template>
