<template>
  <div class="bg-white rounded-xl border border-gray-200 p-6">
    <h3 class="text-sm font-semibold text-gray-800 mb-4 pb-3 border-b border-gray-100">{{ title }}</h3>
    <slot />
  </div>
</template>

<script setup>
defineProps({ title: String })
</script>
