import { ref, reactive, watch } from 'vue'

export function useTranslations() {
  const initialTranslations = window.__INITIAL_TRANSLATIONS__
  const translations = ref(initialTranslations)


  const trans = function(key, replace = {}) {
    let translation = key.split('.').reduce((o, i) => o?.[i], translations.value);
    if (typeof translation === 'string') {
      Object.keys(replace).forEach(key => {
        translation = translation.replace(`:${key}`, replace[key]);
      });
    }
    return translation || key;
  }

  const saveLanguagePreference = (locale) => {
    localStorage.setItem('userLanguage', locale)
  }
  
  const getLanguagePreference = () => {
    return localStorage.getItem('userLanguage') || document.documentElement.lang || 'en'
  }

  const currentLocale = ref(getLanguagePreference())

  const setLocale = async function(newLocale) {
    try {
      const response = await fetch('/change-language', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ locale: newLocale })
      });
      const data = await response.json();
      translations.value = data.translations;
      currentLocale.value = data.locale;
      saveLanguagePreference(data.locale);
    } catch (error) {
      console.error('Failed to change language:', error);
    }
  }

  const getLocale = function() {
    return currentLocale.value;
  }

  const initTranslations = async function(initialTranslations, initialLocale) {
    const storedLocale = getLanguagePreference();
    console.log(storedLocale, initialLocale)
    if (storedLocale !== initialLocale) {
      await setLocale(storedLocale);
    } else {
      translations.value = initialTranslations;
      currentLocale.value = initialLocale;
    }
  }

  initTranslations(translations.value, currentLocale.value)

  return {
    trans,
    setLocale,
    getLocale,
    initTranslations,
    getLanguagePreference,
    currentLocale
  }
}