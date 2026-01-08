import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { LANGUAGES, APP_CONFIG } from '@/utils/constants'
import type { Language } from '@/utils/constants'

export const useLanguageStore = defineStore('language', () => {
  // State
  const currentLanguage = ref<Language>(
    (localStorage.getItem('klp48-language') as Language) || APP_CONFIG.defaultLang as Language
  )

  // Getters
  const languageName = computed(() => LANGUAGES[currentLanguage.value])
  
  const availableLanguages = computed(() => {
    return Object.entries(LANGUAGES).map(([code, name]) => ({
      code: code as Language,
      name,
    }))
  })

  // Actions
  const setLanguage = (lang: Language) => {
    if (LANGUAGES[lang]) {
      currentLanguage.value = lang
      localStorage.setItem('klp48-language', lang)
      
      // Update HTML lang attribute
      document.documentElement.lang = lang
      
      // TODO: Future - trigger API calls to fetch translated content
    }
  }

  // Initialize
  document.documentElement.lang = currentLanguage.value

  return {
    currentLanguage,
    languageName,
    availableLanguages,
    setLanguage,
  }
})