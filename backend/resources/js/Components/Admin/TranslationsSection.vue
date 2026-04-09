<template>
  <div class="bg-white rounded-xl border border-gray-200 p-6">
    <button type="button" @click="open = !open" class="flex items-center justify-between w-full">
      <div>
        <h3 class="text-sm font-semibold text-gray-800">Translations</h3>
        <p class="text-xs text-gray-500 mt-0.5">Optional translations for Malay and Japanese</p>
      </div>
      <svg class="w-5 h-5 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
      </svg>
    </button>

    <div v-if="open" class="mt-4">
      <!-- Locale tabs -->
      <div class="flex gap-1 border-b border-gray-200 mb-4">
        <button
          v-for="locale in locales"
          :key="locale.code"
          type="button"
          @click="activeLocale = locale.code"
          class="px-4 py-2 text-sm font-medium border-b-2 transition-colors -mb-px"
          :class="activeLocale === locale.code
            ? 'border-teal-600 text-teal-700'
            : 'border-transparent text-gray-500 hover:text-gray-700'"
        >
          {{ locale.label }}
        </button>
      </div>

      <div v-for="locale in locales" :key="locale.code" v-show="activeLocale === locale.code" class="space-y-4">
        <div v-for="field in fields" :key="field.key">
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ field.label }} <span class="text-gray-400 font-normal">({{ locale.label }})</span>
          </label>
          <textarea
            v-if="field.type === 'textarea'"
            v-model="form[`trans_${locale.code}_${field.key}`]"
            :rows="3"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 resize-y"
          />
          <input
            v-else
            type="text"
            v-model="form[`trans_${locale.code}_${field.key}`]"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

defineProps({
  form:   Object,
  fields: Array, // [{ key: 'title', label: 'Title', type: 'text'|'textarea' }]
})

const open        = ref(false)
const activeLocale = ref('ms')

const locales = [
  { code: 'ms', label: 'Malay (MY)' },
  { code: 'ja', label: 'Japanese (JP)' },
]
</script>
