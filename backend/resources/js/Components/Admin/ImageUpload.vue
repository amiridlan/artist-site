<template>
  <div>
    <div v-if="previewUrl || currentUrl" class="mb-2">
      <img :src="previewUrl || currentUrl" class="h-32 w-32 object-cover rounded-lg border border-gray-200" />
    </div>
    <input
      type="file"
      accept="image/*"
      @change="onChange"
      class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer"
    />
    <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
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
