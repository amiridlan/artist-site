<template>
  <div class="relative" ref="dropdownRef">
    <button
      @click="isOpen = !isOpen"
      class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium transition-all duration-200"
      :class="variant === 'light'
        ? 'bg-white/10 backdrop-blur-sm text-white hover:bg-white/20'
        : 'bg-jade-50 text-jade-700 hover:bg-jade-100'"
    >
      <span class="text-base leading-none">{{ currentFlag }}</span>
      <span>{{ currentLang.toUpperCase() }}</span>
      <svg class="w-3.5 h-3.5 transition-transform" :class="isOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
      </svg>
    </button>

    <transition
      enter-active-class="transition duration-150 ease-out"
      enter-from-class="opacity-0 scale-95 -translate-y-1"
      enter-to-class="opacity-100 scale-100 translate-y-0"
      leave-active-class="transition duration-100 ease-in"
      leave-from-class="opacity-100 scale-100 translate-y-0"
      leave-to-class="opacity-0 scale-95 -translate-y-1"
    >
      <div v-if="isOpen"
           class="absolute right-0 mt-2 w-44 bg-white rounded-xl shadow-lg border border-charcoal-100 overflow-hidden z-50">
        <button
          v-for="lang in languages"
          :key="lang.code"
          @click="selectLanguage(lang.code)"
          class="w-full flex items-center gap-3 px-4 py-2.5 text-sm transition-colors hover:bg-jade-50"
          :class="currentLang === lang.code ? 'bg-jade-50 text-jade-700 font-medium' : 'text-charcoal-700'"
        >
          <span class="text-lg leading-none">{{ lang.flag }}</span>
          <span class="flex-1 text-left">{{ lang.name }}</span>
          <span class="text-xs text-charcoal-400 font-mono">{{ lang.code.toUpperCase() }}</span>
        </button>
      </div>
    </transition>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useLanguageStore } from '@/stores/language'
import { storeToRefs } from 'pinia'
import type { Language } from '@/utils/constants'

defineProps<{
  variant?: 'light' | 'dark'
}>()

const languageStore = useLanguageStore()
const { currentLang } = storeToRefs(languageStore)
const { setLanguage } = languageStore

const isOpen = ref(false)
const dropdownRef = ref<HTMLElement | null>(null)

const languages = [
  { code: 'en' as Language, name: 'English', flag: '🇬🇧' },
  { code: 'ms' as Language, name: 'Bahasa Melayu', flag: '🇲🇾' },
  { code: 'ja' as Language, name: '日本語', flag: '🇯🇵' },
]

const currentFlag = computed(() => {
  return languages.find(l => l.code === currentLang.value)?.flag || '🇬🇧'
})

function selectLanguage(code: Language) {
  setLanguage(code)
  isOpen.value = false
}

function handleClickOutside(e: MouseEvent) {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target as Node)) {
    isOpen.value = false
  }
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onUnmounted(() => document.removeEventListener('click', handleClickOutside))
</script>
