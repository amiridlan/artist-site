<template>
  <article
    class="video-card group relative cursor-pointer bg-white rounded-xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300"
    @click="handleClick"
  >
    <!-- Thumbnail Container -->
    <div class="relative aspect-video overflow-hidden bg-dark-900">
      <!-- Thumbnail Image -->
      <img
        :src="video.thumbnail"
        :alt="video.title"
        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
        loading="lazy"
      />

      <!-- Overlay on Hover -->
      <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
        <!-- Play Button -->
        <div class="w-16 h-16 rounded-full bg-white/90 flex items-center justify-center transform scale-0 group-hover:scale-100 transition-transform duration-300">
          <svg class="w-8 h-8 text-primary-500 ml-1" fill="currentColor" viewBox="0 0 20 20">
            <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" />
          </svg>
        </div>
      </div>

      <!-- Duration Badge -->
      <div class="absolute bottom-2 right-2 px-2 py-1 bg-black/80 text-white rounded text-xs font-medium backdrop-blur-sm">
        {{ formattedDuration }}
      </div>

      <!-- Type Badge -->
      <div class="absolute top-2 left-2">
        <span :class="typeClasses" class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide backdrop-blur-sm">
          {{ typeLabel }}
        </span>
      </div>

      <!-- Featured Badge -->
      <div v-if="video.featured" class="absolute top-2 right-2">
        <span class="px-2 py-1 bg-accent-yellow text-dark-900 rounded-full text-xs font-bold uppercase flex items-center gap-1">
          <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
          </svg>
        </span>
      </div>
    </div>

    <!-- Content Section -->
    <div class="p-4">
      <!-- Title -->
      <h3 class="font-outfit font-bold text-base md:text-lg mb-2 line-clamp-2 group-hover:text-primary-500 transition-colors duration-300">
        {{ video.title }}
      </h3>

      <!-- Description -->
      <p class="text-dark-600 text-sm mb-3 line-clamp-2 leading-relaxed">
        {{ video.description }}
      </p>

      <!-- Meta Information -->
      <div class="flex items-center justify-between text-xs text-dark-500">
        <!-- Views & Likes -->
        <div class="flex items-center gap-3">
          <span class="flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            {{ formatViews(video.views) }}
          </span>
          <span v-if="video.likes" class="flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
            {{ formatViews(video.likes) }}
          </span>
        </div>

        <!-- Date -->
        <time :datetime="video.releaseDate">
          {{ formattedDate }}
        </time>
      </div>

      <!-- Tags (Optional) -->
      <div v-if="showTags && video.tags && video.tags.length > 0" class="mt-3 flex flex-wrap gap-1">
        <span
          v-for="tag in video.tags.slice(0, 3)"
          :key="tag"
          class="px-2 py-1 bg-dark-100 text-dark-600 rounded text-xs"
        >
          #{{ tag }}
        </span>
      </div>
    </div>

    <!-- Hover Indicator -->
    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-primary transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
  </article>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { format, formatDistanceToNow } from 'date-fns'
import type { Video, VideoType } from '@/types/video'
import { formatDuration, formatCompactNumber } from '@/utils/helpers'

// Props
interface Props {
  video: Video
  showTags?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  showTags: false,
})

// Emits
const emit = defineEmits<{
  click: [video: Video]
}>()

// Computed
const formattedDuration = computed(() => formatDuration(props.video.duration))

const formattedDate = computed(() => {
  const date = new Date(props.video.releaseDate)
  const daysAgo = Math.floor((Date.now() - date.getTime()) / (1000 * 60 * 60 * 24))

  if (daysAgo < 30) {
    return formatDistanceToNow(date, { addSuffix: true })
  }
  return format(date, 'MMM d, yyyy')
})

const typeLabel = computed(() => {
  const labels: Record<VideoType, string> = {
    'all': 'All',
    'music-video': 'MV',
    'performance': 'Performance',
    'behind-the-scenes': 'BTS',
    'interview': 'Interview',
    'variety': 'Variety'
  }
  return labels[props.video.type]
})

const typeClasses = computed(() => {
  const baseClasses = 'text-white'

  switch (props.video.type) {
    case 'music-video':
      return `${baseClasses} bg-primary-500/90`
    case 'performance':
      return `${baseClasses} bg-accent-purple/90`
    case 'behind-the-scenes':
      return `${baseClasses} bg-accent-pink/90`
    case 'interview':
      return `${baseClasses} bg-accent-blue/90`
    case 'variety':
      return `${baseClasses} bg-accent-yellow/90 text-dark-900`
    default:
      return `${baseClasses} bg-dark-600/90`
  }
})

// Methods
const formatViews = (views: number): string => {
  return formatCompactNumber(views)
}

const handleClick = () => {
  emit('click', props.video)
}
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
.video-card {
  transform: translateY(0);
}

.video-card:hover {
  transform: translateY(-4px);
}

/* Aspect ratio container */
.aspect-video {
  aspect-ratio: 16 / 9;
}
</style>
