<template>
  <Link
    :href="href"
    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors"
    :class="active
      ? 'bg-teal-600 text-white'
      : 'text-gray-400 hover:text-white hover:bg-gray-800'"
  >
    <slot />
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
defineProps({ href: String, active: Boolean })
</script>
