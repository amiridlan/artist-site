<template>
  <article
    class="release-card group relative cursor-pointer bg-white rounded-xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300"
    @click="handleClick"
  >
    <!-- Cover Image Container -->
    <div class="relative aspect-square overflow-hidden bg-neutral-900">
      <!-- Cover Image -->
      <img
        :src="release.coverImage"
        :alt="release.title"
        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
        loading="lazy"
      />

      <!-- Overlay on Hover -->
      <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
        <!-- Play Button (for releases with streaming links) -->
        <div v-if="release.streamingLinks && release.streamingLinks.length > 0" class="w-16 h-16 rounded-full bg-white/90 flex items-center justify-center transform scale-0 group-hover:scale-100 transition-transform duration-300">
          <svg class="w-8 h-8 text-primary-500 ml-1" fill="currentColor" viewBox="0 0 20 20">
            <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" />
          </svg>
        </div>

        <!-- "View Details" for releases without streaming links -->
        <div v-else class="text-white text-sm font-semibold px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full">
          View Details
        </div>
      </div>

      <!-- Type Badge -->
      <div class="absolute top-2 left-2">
        <span :class="typeClasses" class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide backdrop-blur-sm">
          {{ typeLabel }}
        </span>
      </div>

    </div>

    <!-- Content Section -->
    <div class="p-5">
      <!-- Title -->
      <h3 class="font-outfit font-bold text-lg mb-2 line-clamp-2 group-hover:text-primary-500 transition-colors duration-300">
        {{ release.title }}
      </h3>

      <!-- Release Date or Countdown -->
      <div class="text-sm mb-3">
        <div v-if="isUpcoming" class="text-primary-500 font-semibold">
          {{ countdown }}
        </div>
        <time v-else :datetime="release.releaseDate" class="text-neutral-600">
          {{ formattedDate }}
        </time>
      </div>

      <!-- Description -->
      <p class="text-neutral-600 text-sm mb-4 line-clamp-2 leading-relaxed">
        {{ release.description }}
      </p>

      <!-- Format Icons -->
      <div class="flex items-center gap-2 mb-3">
        <span
          v-for="format in release.formats"
          :key="format"
          :class="formatClasses(format)"
          class="px-2 py-1 rounded text-xs font-medium uppercase"
        >
          {{ formatLabel(format) }}
        </span>
      </div>

      <!-- Meta Information -->
      <div class="flex items-center justify-between text-xs text-neutral-500 pt-3 border-t border-neutral-200">
        <!-- Track Count & Duration -->
        <div class="flex items-center gap-2">
          <span class="flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
            </svg>
            {{ release.tracklist.length }} tracks
          </span>
          <span>{{ totalDuration }}</span>
        </div>

        <!-- Sales (if available and not upcoming) -->
        <span v-if="release.sales && !isUpcoming" class="font-semibold text-primary-500">
          {{ formatSales(release.sales) }} sold
        </span>
      </div>
    </div>

    <!-- Hover Indicator -->
    <div class="absolute bottom-0 left-0 right-0 h-1 bg-primary-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
  </article>
</template>

<script setup lang="ts">
import { computed, ref, onMounted, onUnmounted } from 'vue'
import { format, formatDistanceToNowStrict } from 'date-fns'
import type { Release, ReleaseType, ReleaseFormat } from '@/types/release'
import { formatDuration, formatCompactNumber } from '@/utils/helpers'

// Props
interface Props {
  release: Release
}

const props = defineProps<Props>()

// Emits
const emit = defineEmits<{
  click: [release: Release]
}>()

// State
const countdown = ref('')
let countdownInterval: ReturnType<typeof setInterval> | null = null

// Computed
const isUpcoming = computed(() => {
  return new Date(props.release.releaseDate) > new Date()
})

const formattedDate = computed(() => {
  return format(new Date(props.release.releaseDate), 'MMM d, yyyy')
})

const totalDuration = computed(() => {
  const totalSeconds = props.release.tracklist.reduce((sum, track) => sum + track.duration, 0)
  return formatDuration(totalSeconds)
})

const typeLabel = computed(() => {
  const labels: Record<ReleaseType, string> = {
    'all': 'All',
    'single': 'Single',
    'album': 'Album',
    'ep': 'EP',
    'digital-single': 'Digital'
  }
  return labels[props.release.type]
})

const typeClasses = computed(() => {
  const baseClasses = 'text-white'

  switch (props.release.type) {
    case 'single':
      return `${baseClasses} bg-primary-700`
    case 'album':
      return `${baseClasses} bg-primary-600/90`
    case 'ep':
      return `${baseClasses} bg-primary-500/90`
    case 'digital-single':
      return `${baseClasses} bg-primary-700/90`
    default:
      return `${baseClasses} bg-neutral-600/90`
  }
})

// Methods
const updateCountdown = () => {
  if (!isUpcoming.value) return

  const now = new Date()
  const releaseDate = new Date(props.release.releaseDate)
  const diff = releaseDate.getTime() - now.getTime()

  if (diff <= 0) {
    countdown.value = 'Available Now!'
    if (countdownInterval) {
      clearInterval(countdownInterval)
    }
    return
  }

  const days = Math.floor(diff / (1000 * 60 * 60 * 24))
  const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
  const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))

  if (days > 0) {
    countdown.value = `${days}d ${hours}h ${minutes}m`
  } else if (hours > 0) {
    countdown.value = `${hours}h ${minutes}m`
  } else {
    countdown.value = `${minutes}m`
  }
}

const formatLabel = (format: ReleaseFormat): string => {
  const labels: Record<ReleaseFormat, string> = {
    'cd': 'CD',
    'digital': 'Digital',
    'vinyl': 'Vinyl',
    'cassette': 'Cassette'
  }
  return labels[format]
}

const formatClasses = (format: ReleaseFormat): string => {
  switch (format) {
    case 'cd':
      return 'bg-primary-700/10 text-accent-blue'
    case 'digital':
      return 'bg-primary-500/10 text-primary-500'
    case 'vinyl':
      return 'bg-primary-600/10 text-accent-purple'
    case 'cassette':
      return 'bg-primary-500/10 text-accent-pink'
    default:
      return 'bg-neutral-100 text-neutral-600'
  }
}

const formatSales = (sales: number): string => {
  return formatCompactNumber(sales)
}

const handleClick = () => {
  emit('click', props.release)
}

// Lifecycle
onMounted(() => {
  if (isUpcoming.value) {
    updateCountdown()
    countdownInterval = setInterval(updateCountdown, 60000) // Update every minute
  }
})

onUnmounted(() => {
  if (countdownInterval) {
    clearInterval(countdownInterval)
  }
})
</script>

<style scoped>
/* Line clamp utilities */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Card hover animation */
.release-card {
  transform: translateY(0);
}

.release-card:hover {
  transform: translateY(-4px);
}

/* Aspect ratio container */
.aspect-square {
  aspect-ratio: 1 / 1;
}
</style>
