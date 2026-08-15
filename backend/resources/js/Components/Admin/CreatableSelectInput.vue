<template>
  <div>
    <select
      ref="selectRef"
      :value="modelValue"
      @change="onSelectChange"
      class="w-full border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-teal-500 dark:focus:ring-teal-400 focus:border-transparent transition-colors"
      :class="error ? 'border-red-400 dark:border-red-500' : ''"
    >
      <option v-if="placeholder" value="">{{ placeholder }}</option>
      <option v-for="opt in allOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
      <option :value="ADD_NEW_VALUE">{{ addLabel }}</option>
    </select>

    <div v-if="isAdding" class="mt-2 flex items-center gap-2">
      <input
        ref="inputRef"
        v-model="newValue"
        type="text"
        :placeholder="newValueHint"
        aria-label="New generation value"
        class="flex-1 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-1.5 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-500 dark:focus:ring-teal-400 focus:border-transparent transition-colors"
        @keydown.enter.prevent="confirmAdd"
        @keydown.escape.prevent="cancelAdd"
      />
      <button
        type="button"
        @click="confirmAdd"
        class="text-xs font-medium text-teal-600 dark:text-teal-400 hover:text-teal-800 dark:hover:text-teal-300 flex-shrink-0"
      >
        Save
      </button>
      <button
        type="button"
        @click="cancelAdd"
        class="text-xs font-medium text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 flex-shrink-0"
      >
        Cancel
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue'

const props = defineProps({
  modelValue:   String,
  options:      { type: Array, default: () => [] }, // [{ value, label }]
  placeholder:  String,
  error:        String,
  addLabel:     { type: String, default: '+ Add new…' },
  newValueHint: { type: String, default: 'e.g. 3rd, 4th' },
})
const emit = defineEmits(['update:modelValue', 'optionCreated'])

const ADD_NEW_VALUE = '__add_new__'

const selectRef = ref(null)
const inputRef  = ref(null)
const isAdding  = ref(false)
const newValue  = ref('')
const createdOptions = ref([])

const allOptions = computed(() => {
  const merged = [...props.options]
  for (const created of createdOptions.value) {
    if (!merged.some(o => o.value.toLowerCase() === created.value.toLowerCase())) {
      merged.push(created)
    }
  }
  return merged
})

function onSelectChange(event) {
  const val = event.target.value
  if (val === ADD_NEW_VALUE) {
    isAdding.value = true
    newValue.value = ''
    // Revert the visible select back to the current value until the new one is confirmed
    event.target.value = props.modelValue ?? ''
    nextTick(() => inputRef.value?.focus())
    return
  }
  emit('update:modelValue', val)
}

function confirmAdd() {
  const trimmed = newValue.value.trim()
  if (!trimmed) return

  const existing = allOptions.value.find(o => o.value.toLowerCase() === trimmed.toLowerCase())
  if (existing) {
    emit('update:modelValue', existing.value)
  } else {
    createdOptions.value.push({ value: trimmed, label: trimmed })
    emit('update:modelValue', trimmed)
    emit('optionCreated', trimmed)
  }

  isAdding.value = false
  newValue.value = ''
  nextTick(() => selectRef.value?.focus())
}

function cancelAdd() {
  isAdding.value = false
  newValue.value = ''
  nextTick(() => selectRef.value?.focus())
}
</script>
