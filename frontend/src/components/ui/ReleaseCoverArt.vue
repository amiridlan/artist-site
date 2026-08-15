<template>
  <div class="relative w-full h-full">
    <!-- Fallback disc icon, always present behind the cover art -->
    <div class="absolute inset-0 bg-jade-gradient flex items-center justify-center opacity-60 group-hover:opacity-80 transition-opacity">
      <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z"/>
      </svg>
    </div>

    <!-- Cover art fades in over the fallback once loaded -->
    <img
      v-if="coverImage"
      :src="coverImage"
      :alt="title"
      loading="lazy"
      class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300"
      :class="loaded ? 'opacity-100' : 'opacity-0'"
      @load="loaded = true"
    />
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

defineProps<{
  coverImage?: string
  title: string
}>()

const loaded = ref(false)
</script>
