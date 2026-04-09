<template>
  <input
    v-bind="$attrs"
    :value="modelValue"
    @input="$emit('update:modelValue', $event.target.value)"
    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
    :class="error ? 'border-red-400' : ''"
  />
</template>

<script setup>
defineProps({ modelValue: [String, Number], error: String })
defineEmits(['update:modelValue'])
</script>
