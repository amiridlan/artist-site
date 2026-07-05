import { defineStore } from 'pinia'
import { ref, watch } from 'vue'
import i18n from '@/i18n'
import { LANGUAGES, type Language } from '@/utils/constants'
import { clearApiCache } from '@/composables/useApi'

export const useLanguageStore = defineStore('language', () => {
  const currentLang = ref<Language>(
    (localStorage.getItem('klp48-lang') as Language) || 'en'
  )

  const availableLanguages = Object.entries(LANGUAGES).map(([code, name]) => ({
    code: code as Language,
    name,
  }))

  function setLanguage(lang: Language) {
    currentLang.value = lang
    localStorage.setItem('klp48-lang', lang)
    document.documentElement.lang = lang
    i18n.global.locale.value = lang
    clearApiCache() // Clear cached API responses for new language
  }

  watch(currentLang, (newLang) => {
    document.documentElement.lang = newLang
    i18n.global.locale.value = newLang
  }, { immediate: true })

  return {
    currentLang,
    availableLanguages,
    setLanguage,
  }
})
