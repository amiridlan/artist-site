<template>
  <div class="space-y-2">
    <div v-for="(pair, i) in pairs" :key="i" class="flex gap-2">
      <input
        v-model="pairs[i].key"
        type="text"
        placeholder="Key"
        @input="emit"
        class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500"
      />
      <input
        v-model="pairs[i].value"
        type="text"
        placeholder="Value"
        @input="emit"
        class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500"
      />
      <button type="button" @click="remove(i)" class="px-2 text-red-400 hover:text-red-600">×</button>
    </div>
    <button type="button" @click="add" class="text-sm text-teal-600 hover:text-teal-800 font-medium">+ Add row</button>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props  = defineProps({ modelValue: Object })
const emits  = defineEmits(['update:modelValue'])

const toArray = (obj) => Object.entries(obj || {}).map(([key, value]) => ({ key, value }))
const toObj   = (arr) => Object.fromEntries(arr.filter(p => p.key).map(p => [p.key, p.value]))

const pairs = ref(toArray(props.modelValue))

watch(() => props.modelValue, (v) => { pairs.value = toArray(v) })

const emit  = () => emits('update:modelValue', toObj(pairs.value))
const add   = () => { pairs.value.push({ key: '', value: '' }) }
const remove = (i) => { pairs.value.splice(i, 1); emit() }
</script>
