// Utilidades para manejo de idiomas e internacionalización

/**
 * Detecta el idioma preferido del navegador
 * @returns {string} Código del idioma ('es' o 'en')
 */
export function getBrowserLanguage() {
  const lang = navigator.language || navigator.userLanguage;
  console.log('Idioma del navegador detectado:', lang);
  
  // Si es español o variante de español, usar español
  if (lang.startsWith('es')) {
    return 'es';
  }
  
  // Para cualquier otro idioma, usar inglés
  return 'en';
}

/**
 * Obtiene el idioma inicial basado en prioridades
 * @param {Object} userSettings - Configuraciones del usuario (si está logueado)
 * @returns {string} Código del idioma
 */
export function getInitialLanguage(userSettings = null) {
  let selectedLang = 'es'; // Fallback por defecto
  
  // 1. Si el usuario está logueado, usar sus configuraciones
  if (userSettings?.language) {
    selectedLang = userSettings.language;
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
      selectedLang = getBrowserLanguage();
      console.log('Usando idioma del navegador:', selectedLang);
    }
  }
  
  return selectedLang;
}

/**
 * Obtiene el estado inicial del modo oscuro
 * @param {Object} userSettings - Configuraciones del usuario (si está logueado)
 * @returns {boolean} true si debe activarse el modo oscuro
 */
export function getInitialDarkMode(userSettings = null) {
  // 1. Si el usuario está logueado, usar sus configuraciones
  if (userSettings?.dark_mode !== undefined) {
    return userSettings.dark_mode;
  }
  
  // 2. Si no está logueado, verificar localStorage
  const savedDarkMode = localStorage.getItem('darkMode');
  return savedDarkMode === 'true';
}

/**
 * Aplica el modo oscuro al DOM
 * @param {boolean} isActive - true para activar modo oscuro
 */
export function applyDarkMode(isActive) {
  if (isActive) {
    document.documentElement.classList.add('dark');
    document.body.classList.add('dark-mode');
  } else {
    document.documentElement.classList.remove('dark');
    document.body.classList.remove('dark-mode');
  }
}

/**
 * Lista de idiomas soportados
 */
export const SUPPORTED_LANGUAGES = [
  { code: 'es', name: 'Español', flag: '🇪🇸' },
  { code: 'en', name: 'English', flag: '🇺🇸' }
];

/**
 * Valida si un código de idioma es soportado
 * @param {string} langCode - Código del idioma
 * @returns {boolean}
 */
export function isValidLanguage(langCode) {
  return SUPPORTED_LANGUAGES.some(lang => lang.code === langCode);
}
