<template>
  <button
    @click="$emit('click')"
    class="mobile-menu-toggle p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200"
    :aria-label="isOpen ? 'Close navigation menu' : 'Open navigation menu'"
    :aria-expanded="isOpen"
    type="button"
  >
    <!-- Hamburger Icon -->
    <svg
      class="w-6 h-6 transition-transform duration-300"
      :class="{ 'rotate-90': isOpen }"
      fill="none"
      stroke="currentColor"
      viewBox="0 0 24 24"
      aria-hidden="true"
    >
      <path
        v-if="!isOpen"
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M4 6h16M4 12h16M4 18h16"
      />
      <path
        v-else
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M6 18L18 6M6 6l12 12"
      />
    </svg>
    <span class="sr-only">{{ isOpen ? 'Close' : 'Open' }} menu</span>
  </button>
</template>

<script setup>
defineProps({
  isOpen: Boolean
})

defineEmits(['click'])
</script>

<style scoped>
.mobile-menu-toggle:focus-visible {
  outline: none;
  box-shadow: 0 0 0 2px #14b8a6;
}
</style>
