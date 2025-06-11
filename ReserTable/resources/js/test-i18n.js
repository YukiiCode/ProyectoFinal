// Test simple para verificar i18n
import i18n from './i18n/index.js';

console.log('Configuración i18n:', {
  locale: i18n.global.locale.value,
  availableLocales: i18n.global.availableLocales,
  messages: Object.keys(i18n.global.messages)
});

// Probar algunas traducciones
const testKeys = [
  'common.welcome',
  'welcome.title',
  'admin.dashboard.title',
  'footer.company_name'
];

console.log('\n=== Probando traducciones ===');
testKeys.forEach(key => {
  try {
    const translation = i18n.global.t(key);
    console.log(`${key}: "${translation}"`);
  } catch (error) {
    console.error(`Error con "${key}":`, error.message);
  }
});

// Verificar si las traducciones están cargadas
console.log('\n=== Contenido de traducciones ===');
console.log('ES keys:', Object.keys(i18n.global.messages.es || {}));
console.log('EN keys:', Object.keys(i18n.global.messages.en || {}));

// Verificar estructura específica
if (i18n.global.messages.es) {
  console.log('\nEstructura ES:');
  console.log('- common:', !!i18n.global.messages.es.common);
  console.log('- welcome:', !!i18n.global.messages.es.welcome);
  console.log('- admin:', !!i18n.global.messages.es.admin);
  console.log('- footer:', !!i18n.global.messages.es.footer);
}
