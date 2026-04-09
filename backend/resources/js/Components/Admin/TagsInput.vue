<template>
  <div>
    <div class="flex flex-wrap gap-2 mb-2">
      <span
        v-for="(tag, i) in tags"
        :key="i"
        class="inline-flex items-center gap-1 px-2 py-1 bg-teal-100 text-teal-800 text-sm rounded-full"
      >
        {{ tag }}
        <button type="button" @click="remove(i)" class="text-teal-600 hover:text-teal-900">×</button>
      </span>
    </div>
    <div class="flex gap-2">
      <input
        v-model="input"
        type="text"
        placeholder="Add tag and press Enter"
        @keydown.enter.prevent="add"
        @keydown.comma.prevent="add"
        class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500"
      />
      <button type="button" @click="add" class="px-3 py-2 bg-teal-600 text-white text-sm rounded-lg hover:bg-teal-700">Add</button>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({ modelValue: Array })
const emit  = defineEmits(['update:modelValue'])

const tags  = ref([...(props.modelValue || [])])
const input = ref('')

watch(() => props.modelValue, (v) => { tags.value = [...(v || [])] })

const add = () => {
  const val = input.value.trim().replace(/,$/, '')
  if (val && !tags.value.includes(val)) {
    tags.value.push(val)
    emit('update:modelValue', [...tags.value])
  }
  input.value = ''
}

const remove = (i) => {
  tags.value.splice(i, 1)
  emit('update:modelValue', [...tags.value])
}
</script>
