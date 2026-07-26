<template>
  <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6">
    <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-4 pb-3 border-b border-gray-100 dark:border-gray-800">{{ title }}</h3>
    <slot />
  </div>
</template>

<script setup>
defineProps({ title: String })
</script>
