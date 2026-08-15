<template>
  <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6">
    <div class="flex items-start justify-between gap-4 mb-4 pb-3 border-b border-gray-100 dark:border-gray-800">
      <div>
        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ title }}</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
          English is required; {{ localeNames }} are optional and fall back to English when left blank.
        </p>
      </div>
      <label class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300 whitespace-nowrap cursor-pointer select-none">
        <input
          type="checkbox"
          v-model="showMissingOnly"
          class="rounded border-gray-300 dark:border-gray-700 text-teal-600 focus:ring-teal-500 dark:bg-gray-800"
        />
        Show only missing translations
      </label>
    </div>

    <p v-if="visibleFields.length === 0" class="text-sm text-gray-500 dark:text-gray-400 py-4 text-center">
      All fields are translated.
    </p>

    <fieldset
      v-for="field in visibleFields"
      :key="field.key"
      class="border-0 p-0 m-0 py-4 first:pt-0 border-b border-gray-100 dark:border-gray-800 last:border-b-0 last:pb-0"
    >
      <legend class="sr-only">{{ field.label }}</legend>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label
            :for="`${uid}-${field.key}-en`"
            class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1"
          >
            {{ field.label }}
            <span class="normal-case font-normal text-gray-400 dark:text-gray-500">· English</span>
            <span v-if="field.required" class="text-red-500 dark:text-red-400">*</span>
          </label>
          <TextareaInput
            v-if="field.type === 'textarea'"
            :id="`${uid}-${field.key}-en`"
            v-model="form[field.key]"
            :rows="3"
            :error="form.errors[field.key]"
          />
          <TextInput
            v-else
            :id="`${uid}-${field.key}-en`"
            v-model="form[field.key]"
            :error="form.errors[field.key]"
          />
          <p v-if="form.errors[field.key]" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors[field.key] }}</p>
        </div>

        <div v-for="locale in locales" :key="locale.code">
          <label
            :for="`${uid}-${field.key}-${locale.code}`"
            class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide mb-1"
            :class="isMissing(field, locale) ? 'text-amber-600 dark:text-amber-400' : 'text-gray-500 dark:text-gray-400'"
          >
            {{ field.label }}
            <span
              class="normal-case font-normal"
              :class="isMissing(field, locale) ? 'text-amber-500 dark:text-amber-400' : 'text-gray-400 dark:text-gray-500'"
            >
              · {{ locale.label }}
            </span>
            <span
              v-if="isMissing(field, locale)"
              class="ml-auto inline-flex items-center rounded-full bg-amber-50 dark:bg-amber-500/10 px-1.5 py-0.5 text-[10px] font-medium text-amber-700 dark:text-amber-400 normal-case tracking-normal"
            >
              Missing
            </span>
          </label>
          <TextareaInput
            v-if="field.type === 'textarea'"
            :id="`${uid}-${field.key}-${locale.code}`"
            v-model="form[transKey(field, locale)]"
            :rows="3"
            :class="isMissing(field, locale) ? 'border-dashed' : ''"
          />
          <TextInput
            v-else
            :id="`${uid}-${field.key}-${locale.code}`"
            v-model="form[transKey(field, locale)]"
            :class="isMissing(field, locale) ? 'border-dashed' : ''"
          />
        </div>
      </div>
    </fieldset>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import TextInput from '@/Components/Admin/TextInput.vue'
import TextareaInput from '@/Components/Admin/TextareaInput.vue'

const props = defineProps({
  form:    { type: Object, required: true },
  fields:  { type: Array,  required: true }, // [{ key, label, type: 'text'|'textarea', required? }]
  locales: { type: Array,  default: () => [{ code: 'ms', label: 'Malay' }, { code: 'ja', label: 'Japanese' }] },
  title:   { type: String, default: 'Content' },
})

const uid = Math.random().toString(36).slice(2, 9)
const showMissingOnly = ref(false)

const transKey  = (field, locale) => `trans_${locale.code}_${field.key}`
const isMissing = (field, locale) => !String(props.form[transKey(field, locale)] ?? '').trim()

const localeNames = computed(() => props.locales.map(l => l.label).join(' and '))

const visibleFields = computed(() => {
  if (!showMissingOnly.value) return props.fields
  return props.fields.filter(field => props.locales.some(locale => isMissing(field, locale)))
})
</script>
