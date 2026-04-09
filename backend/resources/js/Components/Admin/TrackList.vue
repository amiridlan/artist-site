<template>
  <div class="space-y-2">
    <div v-for="(track, i) in tracks" :key="i" class="flex gap-2 items-center">
      <input
        v-model.number="tracks[i].number"
        type="number"
        placeholder="#"
        @input="emit"
        class="w-16 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500"
      />
      <input
        v-model="tracks[i].title"
        type="text"
        placeholder="Track title"
        @input="emit"
        class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500"
      />
      <input
        v-model="tracks[i].duration"
        type="text"
        placeholder="0:00"
        @input="emit"
        class="w-20 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500"
      />
      <button type="button" @click="remove(i)" class="px-2 text-red-400 hover:text-red-600">×</button>
    </div>
    <button type="button" @click="add" class="text-sm text-teal-600 hover:text-teal-800 font-medium">+ Add track</button>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props  = defineProps({ modelValue: Array })
const emits  = defineEmits(['update:modelValue'])

const tracks = ref([...(props.modelValue || [{ number: 1, title: '', duration: '' }])])

watch(() => props.modelValue, (v) => { if (v) tracks.value = [...v] })

const emit   = () => emits('update:modelValue', [...tracks.value])
const add    = () => { tracks.value.push({ number: tracks.value.length + 1, title: '', duration: '' }); emit() }
const remove = (i) => { tracks.value.splice(i, 1); emit() }
</script>
