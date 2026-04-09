<template>
  <select
    :value="modelValue"
    @change="$emit('update:modelValue', $event.target.value)"
    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent bg-white"
    :class="error ? 'border-red-400' : ''"
  >
    <option v-if="placeholder" value="">{{ placeholder }}</option>
    <option v-for="opt in options" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
  </select>
</template>

<script setup>
defineProps({
  modelValue: String,
  options: Array,   // [{ value, label }]
  placeholder: String,
  error: String,
})
defineEmits(['update:modelValue'])
</script>
