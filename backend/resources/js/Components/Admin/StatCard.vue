<template>
  <a :href="href" class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-5 hover:border-teal-300 dark:hover:border-teal-600 transition-colors block">
    <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ value }}</p>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ label }}</p>
  </a>
</template>

<script setup>
defineProps({
  label: String,
  value: Number,
  href: String,
  color: String
})
</script>
