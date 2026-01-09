<template>
  <!-- Backdrop -->
  <Teleport to="body">
    <transition name="fade">
      <div
        v-if="isOpen"
        class="fixed inset-0 bg-black/90 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        @click.self="closeModal"
      >
        <!-- Modal Container -->
        <div class="relative w-full max-w-6xl bg-dark-900 rounded-2xl overflow-hidden shadow-2xl">
          <!-- Close Button -->
          <button
            @click="closeModal"
            class="absolute top-4 right-4 z-10 w-10 h-10 rounded-full bg-black/50 hover:bg-black/70 text-white flex items-center justify-center transition-colors"
            aria-label="Close video player"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <!-- Video Player -->
          <div class="aspect-video bg-black">
            <iframe
              v-if="video && video.youtubeId"
              :src="`https://www.youtube.com/embed/${video.youtubeId}?autoplay=1`"
              class="w-full h-full"
              frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
              allowfullscreen
            ></iframe>
            <div v-else class="w-full h-full flex items-center justify-center text-white">
              <p>Video not available</p>
            </div>
          </div>

          <!-- Video Info -->
          <div v-if="video" class="p-6 md:p-8">
            <!-- Title & Type -->
            <div class="flex items-start justify-between mb-4">
              <div class="flex-1">
                <h2 class="font-outfit font-bold text-2xl md:text-3xl text-white mb-2">
                  {{ video.title }}
                </h2>
                <span :class="typeClasses" class="inline-block px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide">
                  {{ typeLabel }}
                </span>
              </div>
            </div>

            <!-- Description -->
            <p class="text-white/80 text-base leading-relaxed mb-6">
              {{ video.description }}
            </p>

            <!-- Stats -->
            <div class="flex flex-wrap items-center gap-6 text-white/60 text-sm mb-6">
              <span class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                {{ formatViews(video.views) }} views
              </span>
              <span v-if="video.likes" class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                {{ formatViews(video.likes) }} likes
              </span>
              <span class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ formattedDate }}
              </span>
            </div>

            <!-- Tags -->
            <div v-if="video.tags && video.tags.length > 0" class="flex flex-wrap gap-2">
              <span
                v-for="tag in video.tags"
                :key="tag"
                class="px-3 py-1 bg-white/10 hover:bg-white/20 text-white/80 rounded-full text-xs transition-colors cursor-pointer"
              >
                #{{ tag }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup lang="ts">
import { computed, watch } from 'vue'
import { format } from 'date-fns'
import type { Video, VideoType } from '@/types/video'
import { formatCompactNumber } from '@/utils/helpers'

// Props
interface Props {
  isOpen: boolean
  video: Video | null
}

const props = defineProps<Props>()

// Emits
const emit = defineEmits<{
  close: []
}>()

// Computed
const formattedDate = computed(() => {
  if (!props.video) return ''
  return format(new Date(props.video.releaseDate), 'MMM d, yyyy')
})

const typeLabel = computed(() => {
  if (!props.video) return ''
  
  const labels: Record<VideoType, string> = {
    'all': 'All',
    'music-video': 'Music Video',
    'performance': 'Performance',
    'behind-the-scenes': 'Behind the Scenes',
    'interview': 'Interview',
    'variety': 'Variety Show'
  }
  return labels[props.video.type]
})

const typeClasses = computed(() => {
  if (!props.video) return ''
  
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

const closeModal = () => {
  emit('close')
}

// Watch for modal open/close to handle body scroll
watch(() => props.isOpen, (newValue) => {
  if (typeof document !== 'undefined') {
    if (newValue) {
      document.body.style.overflow = 'hidden'
    } else {
      document.body.style.overflow = ''
    }
  }
})

// Handle escape key
const handleEscape = (e: KeyboardEvent) => {
  if (e.key === 'Escape' && props.isOpen) {
    closeModal()
  }
}

// Add/remove escape key listener
watch(() => props.isOpen, (newValue) => {
  if (newValue) {
    window.addEventListener('keydown', handleEscape)
  } else {
    window.removeEventListener('keydown', handleEscape)
  }
})
</script>

<style scoped>
/* Fade transition */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Aspect ratio */
.aspect-video {
  aspect-ratio: 16 / 9;
}
</style>
