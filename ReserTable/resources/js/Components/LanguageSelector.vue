<script setup>
import { ref, onMounted, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { usePage } from '@inertiajs/vue3';

const { locale } = useI18n();
const isOpen = ref(false);
const page = usePage();

const languages = [
  { code: 'es', name: 'Español', flag: '🇪🇸' },
  { code: 'en', name: 'English', flag: '🇺🇸' }
];

const currentLanguage = ref(languages[0]); // Valor por defecto

const updateCurrentLanguage = (langCode) => {
  const language = languages.find(lang => lang.code === langCode);
  if (language) {
    currentLanguage.value = language;
  }
};

const changeLanguage = (language) => {
  console.log('Cambiando idioma a:', language.code);
  locale.value = language.code;
  updateCurrentLanguage(language.code);
  isOpen.value = false;
  
  // Guardar la preferencia en localStorage
  localStorage.setItem('preferred-language', language.code);
  
  // Si el usuario está logueado, también actualizar las configuraciones del servidor
  if (page.props.auth?.user) {
    console.log('Usuario logueado, actualizando configuración en servidor');
    
    // Hacer llamada al servidor para actualizar el idioma
    fetch(route('admin.settings.language'), {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        language: language.code
      })
    })
    .then(response => response.json())
    .then(data => {
      console.log('Idioma actualizado en servidor:', data);
    })
    .catch(error => {
      console.error('Error al actualizar idioma en servidor:', error);
    });
  }
};

const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
};

// Función para obtener el idioma inicial basado en prioridades
const getInitialLanguage = () => {
  let selectedLang = 'es'; // Fallback por defecto
  
  // 1. Si el usuario está logueado, usar sus configuraciones
  if (page.props.auth?.user?.settings?.language) {
    selectedLang = page.props.auth.user.settings.language;
    console.log('Idioma desde configuraciones del usuario:', selectedLang);
  }
  // 2. Si no está logueado, verificar localStorage
  else {
    const savedLanguage = localStorage.getItem('preferred-language');
    if (savedLanguage && (savedLanguage === 'es' || savedLanguage === 'en')) {
      selectedLang = savedLanguage;
      console.log('Idioma desde localStorage:', savedLanguage);
    }
    // 3. Si no hay preferencias guardadas, usar idioma del navegador
    else {
      const browserLang = navigator.language || navigator.userLanguage;
      selectedLang = browserLang.startsWith('es') ? 'es' : 'en';
      console.log('Idioma desde navegador:', browserLang, '->', selectedLang);
    }
  }
  
  return selectedLang;
};

// Inicialización al montar el componente
onMounted(() => {
  const initialLang = getInitialLanguage();
  
  // Actualizar el idioma en vue-i18n si es diferente al actual
  if (locale.value !== initialLang) {
    console.log('Actualizando idioma de', locale.value, 'a', initialLang);
    locale.value = initialLang;
  }
  
  // Actualizar el estado del componente
  updateCurrentLanguage(initialLang);
});

// Observar cambios en las props del usuario para actualizar cuando se loguee/desloguee
watch(() => page.props.auth?.user?.settings?.language, (newLang) => {
  if (newLang && newLang !== locale.value) {
    console.log('Configuración de usuario cambió, actualizando idioma a:', newLang);
    locale.value = newLang;
    updateCurrentLanguage(newLang);
  }
}, { immediate: false });
</script>

<template>
  <div class="relative">
    <button 
      @click="toggleDropdown"
      class="flex items-center gap-2 px-3 py-2 text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
      :class="{ 'ring-2 ring-blue-500': isOpen }"
    >
      <span class="text-lg">{{ currentLanguage.flag }}</span>
      <span class="hidden sm:inline text-gray-700 dark:text-gray-300">{{ currentLanguage.name }}</span>
      <i class="pi pi-chevron-down text-xs text-gray-500 transition-transform" :class="{ 'rotate-180': isOpen }"></i>
    </button>

    <div 
      v-show="isOpen"
      class="absolute top-full right-0 mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg z-50 min-w-[120px]"
      @click.stop
    >
      <div 
        v-for="language in languages" 
        :key="language.code"
        @click="changeLanguage(language)"
        class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors"
        :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300': language.code === currentLanguage.code }"
      >
        <span class="text-lg">{{ language.flag }}</span>
        <span>{{ language.name }}</span>
        <i v-if="language.code === currentLanguage.code" class="pi pi-check text-blue-500 ml-auto"></i>
      </div>
    </div>

    <!-- Backdrop para cerrar el dropdown -->
    <div 
      v-if="isOpen" 
      @click="isOpen = false"
      class="fixed inset-0 z-40"
    ></div>
  </div>
</template>