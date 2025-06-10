<script setup>
import { ref, onMounted, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { usePage, router } from '@inertiajs/vue3';
import { 
  getInitialLanguage, 
  getInitialDarkMode, 
  applyDarkMode, 
  SUPPORTED_LANGUAGES 
} from '@/utils/i18nUtils';

// Props para personalizar el estilo según el contexto
const props = defineProps({
  variant: {
    type: String,
    default: 'public', // 'public' or 'admin'
    validator: (value) => ['public', 'admin'].includes(value)
  }
});

const { locale, t } = useI18n();
const isOpen = ref(false);
const page = usePage();

// Configuración de idiomas
const languages = SUPPORTED_LANGUAGES;
const currentLanguage = ref(languages[0]);

// Estado del modo oscuro
const isDarkMode = ref(false);

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
  
  // Guardar la preferencia en localStorage
  localStorage.setItem('preferred-language', language.code);
  // Si el usuario está logueado, actualizar en el servidor
  if (page.props.auth?.user) {
    router.patch(route('admin.settings.language'), {
      language: language.code
    }, {
      preserveState: true,
      preserveScroll: true
    });
  }
};

const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value;
  
  // Aplicar el cambio inmediatamente en el DOM
  applyDarkMode(isDarkMode.value);
  
  // Guardar en localStorage
  localStorage.setItem('darkMode', isDarkMode.value ? 'true' : 'false');
  
  // Si el usuario está logueado, actualizar en el servidor
  if (page.props.auth?.user) {
    router.patch(route('admin.settings.dark-mode'), {
      dark_mode: isDarkMode.value
    }, {
      preserveState: true,
      preserveScroll: true,
      onError: () => {
        // Si hay error, revertir el cambio
        isDarkMode.value = !isDarkMode.value;
        applyDarkMode(isDarkMode.value);
      }
    });
  }
};

const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
};

const closeDropdown = () => {
  isOpen.value = false;
};

// Inicialización al montar el componente
onMounted(() => {
  const userSettings = page.props.auth?.user?.settings;
  const initialLang = getInitialLanguage(userSettings);
  const initialDarkMode = getInitialDarkMode(userSettings);
  
  // Configurar idioma
  if (locale.value !== initialLang) {
    console.log('Actualizando idioma de', locale.value, 'a', initialLang);
    locale.value = initialLang;
  }
  updateCurrentLanguage(initialLang);
  
  // Configurar modo oscuro
  isDarkMode.value = initialDarkMode;
  applyDarkMode(initialDarkMode);
});

// Observar cambios en las configuraciones del usuario
watch(() => page.props.auth?.user?.settings, (newSettings) => {
  if (newSettings) {
    if (newSettings.language && newSettings.language !== locale.value) {
      console.log('Configuración de idioma del usuario cambió:', newSettings.language);
      locale.value = newSettings.language;
      updateCurrentLanguage(newSettings.language);
    }
    
    if (newSettings.dark_mode !== undefined && newSettings.dark_mode !== isDarkMode.value) {
      console.log('Configuración de modo oscuro del usuario cambió:', newSettings.dark_mode);
      isDarkMode.value = newSettings.dark_mode;
      applyDarkMode(newSettings.dark_mode);
    }
  }
}, { deep: true, immediate: false });
</script>

<template>
  <div class="relative">    <!-- Botón principal del dropdown -->
    <button 
      @click="toggleDropdown"
      :class="[
        'flex items-center gap-2 px-3 py-2 text-sm rounded-md transition-colors',
        variant === 'admin' 
          ? 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-600'
          : 'bg-white/10 hover:bg-white/20 border border-white/20 text-white',
        { 'ring-2': isOpen, 'ring-white/50': isOpen && variant === 'public', 'ring-blue-500': isOpen && variant === 'admin' }
      ]"
    >
      <i class="pi pi-cog"></i>
      <span class="hidden sm:inline">{{ t('common.settings') }}</span>
      <i class="pi pi-chevron-down text-xs transition-transform" :class="{ 'rotate-180': isOpen }"></i>
    </button>

    <!-- Contenido del dropdown -->
    <div 
      v-show="isOpen"
      class="absolute top-full right-0 mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg z-50 min-w-[220px]"
      @click.stop
    >
      <!-- Sección de idioma -->
      <div class="p-3 border-b border-gray-200 dark:border-gray-700">
        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">
          {{ t('admin.settings.language') }}
        </div>
        <div class="space-y-1">
          <div 
            v-for="language in languages" 
            :key="language.code"
            @click="changeLanguage(language)"
            class="flex items-center gap-2 px-2 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer rounded transition-colors"
            :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300': language.code === currentLanguage.code }"
          >
            <span class="text-lg">{{ language.flag }}</span>
            <span class="flex-1">{{ language.name }}</span>
            <i v-if="language.code === currentLanguage.code" class="pi pi-check text-blue-500"></i>
          </div>
        </div>
      </div>

      <!-- Sección de modo oscuro -->
      <div class="p-3">
        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">
          {{ t('admin.settings.appearance') }}
        </div>
        <div 
          @click="toggleDarkMode"
          class="flex items-center justify-between px-2 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer rounded transition-colors"
        >
          <div class="flex items-center gap-2">
            <i :class="isDarkMode ? 'pi pi-moon' : 'pi pi-sun'"></i>
            <span>{{ isDarkMode ? t('admin.settings.dark_mode') : t('admin.settings.light_mode') }}</span>
          </div>
          <!-- Toggle visual -->
          <div class="relative">
            <div 
              class="w-10 h-5 rounded-full transition-colors"
              :class="isDarkMode ? 'bg-blue-500' : 'bg-gray-300'"
            >
              <div 
                class="w-4 h-4 bg-white rounded-full shadow-sm transition-transform duration-200 ease-in-out"
                :class="isDarkMode ? 'translate-x-5' : 'translate-x-0.5'"
                style="margin-top: 2px;"
              ></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Backdrop para cerrar el dropdown -->
    <div 
      v-if="isOpen" 
      @click="closeDropdown"
      class="fixed inset-0 z-40"
    ></div>
  </div>
</template>
