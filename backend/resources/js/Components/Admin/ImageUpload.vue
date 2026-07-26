<template>
  <div>
    <div v-if="previewUrl || currentUrl" class="mb-2">
      <img :src="previewUrl || currentUrl" class="h-32 w-32 object-cover rounded-lg border border-gray-200 dark:border-gray-700" />
    </div>
    <input
      type="file"
      accept="image/*"
      @change="onChange"
      class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-teal-50 dark:file:bg-teal-900/30 file:text-teal-700 dark:file:text-teal-300 hover:file:bg-teal-100 dark:hover:file:bg-teal-900/50 cursor-pointer transition-colors"
    />
    <p v-if="error" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({ currentUrl: String, error: String })
const emit  = defineEmits(['change'])

const previewUrl = ref(null)

const onChange = (e) => {
  const file = e.target.files[0]
  if (!file) return
  previewUrl.value = URL.createObjectURL(file)
  emit('change', file)
}
</script>
