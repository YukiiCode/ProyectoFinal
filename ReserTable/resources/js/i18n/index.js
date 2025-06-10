import { createI18n } from 'vue-i18n'
import { getInitialLanguage } from '@/utils/i18nUtils'

// Importar traducciones
import es from './locales/es.json'
import en from './locales/en.json'

// Debug: verificar que las traducciones se cargan
console.log('Traducciones cargadas:', { es: !!es, en: !!en });
console.log('Claves en es:', Object.keys(es));
console.log('Claves en en:', Object.keys(en));

// Configuración de i18n
const i18n = createI18n({
  locale: getInitialLanguage(),
  fallbackLocale: 'en', // idioma de respaldo
  messages: {
    es,
    en
  },
  // Habilitar legacy para compatibilidad
  legacy: false,
  globalInjection: true
})

console.log('i18n inicializado con idioma:', i18n.global.locale.value);

export default i18n