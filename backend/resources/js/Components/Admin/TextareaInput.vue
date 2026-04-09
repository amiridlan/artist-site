<template>
  <textarea
    :value="modelValue"
    @input="$emit('update:modelValue', $event.target.value)"
    :rows="rows"
    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent resize-y"
    :class="error ? 'border-red-400' : ''"
  />
</template>

<script setup>
defineProps({ modelValue: String, rows: { type: Number, default: 4 }, error: String })
defineEmits(['update:modelValue'])
</script>
