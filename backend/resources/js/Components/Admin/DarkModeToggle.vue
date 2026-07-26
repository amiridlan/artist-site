<template>
  <button
    @click="toggleDarkMode"
    class="dark-mode-toggle flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 w-full text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-gray-50"
    :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
    type="button"
  >
    <!-- Sun Icon (Light Mode) -->
    <svg
      v-if="!isDark"
      class="w-5 h-5 transition-transform duration-300 rotate-0"
      fill="none"
      stroke="currentColor"
      viewBox="0 0 24 24"
      aria-hidden="true"
    >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="1.5"
        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
      />
    </svg>

    <!-- Moon Icon (Dark Mode) -->
    <svg
      v-else
      class="w-5 h-5 transition-transform duration-300 -rotate-90"
      fill="none"
      stroke="currentColor"
      viewBox="0 0 24 24"
      aria-hidden="true"
    >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="1.5"
        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
      />
    </svg>

    <span class="lg:inline">{{ isDark ? 'Dark Mode' : 'Light Mode' }}</span>

    <!-- Screen reader announcement -->
    <span role="status" aria-live="polite" class="sr-only">
      {{ isDark ? 'Dark mode enabled' : 'Light mode enabled' }}
    </span>
  </button>
</template>

<script setup>
defineProps({
  isDark: Boolean,
  toggleDarkMode: Function
})
</script>

<style scoped>
.dark-mode-toggle:focus-visible {
  outline: none;
  box-shadow: 0 0 0 2px #14b8a6, 0 0 0 4px #ffffff;
}

.dark .dark-mode-toggle:focus-visible {
  box-shadow: 0 0 0 2px #14b8a6, 0 0 0 4px #111827;
}
</style>
