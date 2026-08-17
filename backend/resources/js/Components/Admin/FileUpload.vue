<template>
  <div>
    <input
      type="file"
      accept=".pdf,.jpg,.jpeg,.png"
      @change="onChange"
      class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-teal-50 dark:file:bg-teal-900/30 file:text-teal-700 dark:file:text-teal-300 hover:file:bg-teal-100 dark:hover:file:bg-teal-900/50 cursor-pointer transition-colors"
    />
    <p v-if="fileName" class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ fileName }}</p>
    <p v-if="error" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref } from 'vue'

defineProps({ error: String })
const emit = defineEmits(['change'])

const fileName = ref(null)

const onChange = (e) => {
  const file = e.target.files[0]
  if (!file) return
  fileName.value = file.name
  emit('change', file)
}
</script>
