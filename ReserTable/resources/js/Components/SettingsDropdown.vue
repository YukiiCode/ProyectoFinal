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
  <div class="relative">
    <!-- Botón principal del dropdown -->
    <button 
      @click="toggleDropdown"
      :class="[
        'flex items-center gap-2 px-3 py-2 text-sm rounded-lg font-medium transition-all duration-200',
        variant === 'admin' 
          ? 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 shadow-sm'
          : 'text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 border border-gray-200 dark:border-gray-600',
        { 
          'ring-2 ring-blue-500 ring-opacity-50': isOpen,
          'shadow-md': isOpen 
        }
      ]"
    >
      <!-- Icono de configuración con animación -->
      <i class="pi pi-cog transition-transform duration-200" :class="{ 'rotate-90': isOpen }"></i>
      <span class="hidden sm:inline">{{ t('common.settings') }}</span>
      <i class="pi pi-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': isOpen }"></i>
    </button>

    <!-- Contenido del dropdown -->
    <div 
      v-show="isOpen"
      class="absolute top-full right-0 mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-xl z-50 min-w-[240px] overflow-hidden"
      @click.stop
    >
      <!-- Sección de idioma -->
      <div class="p-4 border-b border-gray-100 dark:border-gray-700">
        <div class="flex items-center gap-2 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-3">
          <i class="pi pi-globe text-sm"></i>
          {{ t('admin.settings.language') }}
        </div>
        <div class="space-y-1">
          <div 
            v-for="language in languages" 
            :key="language.code"
            @click="changeLanguage(language)"
            class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 cursor-pointer rounded-md transition-all duration-150 group"
            :class="{ 
              'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-200 font-medium shadow-sm': language.code === currentLanguage.code,
              'hover:shadow-sm': language.code !== currentLanguage.code 
            }"
          >
            <span class="text-lg">{{ language.flag }}</span>
            <span class="flex-1 font-medium">{{ language.name }}</span>
            <i 
              v-if="language.code === currentLanguage.code" 
              class="pi pi-check text-blue-500 dark:text-blue-400 font-bold"
            ></i>
            <i 
              v-else
              class="pi pi-angle-right text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity duration-150"
            ></i>
          </div>
        </div>
      </div>

      <!-- Sección de modo oscuro -->
      <div class="p-4">
        <div class="flex items-center gap-2 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-3">
          <i class="pi pi-palette text-sm"></i>
          {{ t('admin.settings.appearance') }}
        </div>
        <div 
          @click="toggleDarkMode"
          class="flex items-center justify-between px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer rounded-md transition-all duration-150 group hover:shadow-sm"
        >
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
              <i 
                :class="isDarkMode ? 'pi pi-moon text-blue-500' : 'pi pi-sun text-yellow-500'"
                class="text-sm"
              ></i>
            </div>
            <span class="font-medium">{{ isDarkMode ? t('admin.settings.dark_mode') : t('admin.settings.light_mode') }}</span>
          </div>
          <!-- Toggle visual mejorado -->
          <div class="relative">
            <div 
              class="w-12 h-6 rounded-full transition-all duration-300 shadow-inner border-2"
              :class="isDarkMode 
                ? 'bg-blue-500 border-blue-400' 
                : 'bg-gray-200 border-gray-300'"
            >
              <div 
                class="w-4 h-4 bg-white rounded-full shadow-md transition-all duration-300 ease-out flex items-center justify-center"
                :class="isDarkMode ? 'translate-x-6' : 'translate-x-0'"
                style="margin-top: 2px; margin-left: 2px;"
              >
                <div 
                  :class="isDarkMode ? 'bg-blue-500' : 'bg-gray-400'"
                  class="w-2 h-2 rounded-full transition-colors duration-300"
                ></div>
              </div>
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
