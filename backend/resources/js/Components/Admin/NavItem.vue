<template>
  <Link
    :href="href"
    class="nav-item flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 relative"
    :class="[
      active
        ? 'bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-300 border-l-3 border-teal-500 -ml-[3px] pl-[9px]'
        : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-gray-50 hover:scale-[1.01] origin-left'
    ]"
    :aria-current="active ? 'page' : undefined"
  >
    <slot />
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
defineProps({
  href: String,
  active: Boolean
})
</script>

<style scoped>
/* Custom border width for active state */
.border-l-3 {
  border-left-width: 3px;
}

/* Focus visible styles using plain CSS */
.nav-item:focus-visible {
  outline: none;
  box-shadow: 0 0 0 2px #14b8a6, 0 0 0 4px #ffffff;
}

.dark .nav-item:focus-visible {
  box-shadow: 0 0 0 2px #14b8a6, 0 0 0 4px #111827;
}
</style>
