import { createI18n } from 'vue-i18n'
import en from './locales/en.json'
import ms from './locales/ms.json'
import ja from './locales/ja.json'

const savedLang = localStorage.getItem('klp48-lang') || 'en'

const i18n = createI18n({
  legacy: false,
  locale: savedLang,
  fallbackLocale: 'en',
  messages: { en, ms, ja },
})

export default i18n
