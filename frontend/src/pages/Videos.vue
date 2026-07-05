<template>
  <div class="pt-24 pb-16 min-h-screen bg-cream-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Page header -->
      <div ref="headerRef" class="mb-10">
        <h1 class="text-4xl md:text-5xl font-heading font-bold text-charcoal-800">{{ $t('videos.title') }}</h1>
        <p class="text-charcoal-500 mt-2">{{ $t('videos.subtitle') }}</p>
      </div>

      <!-- Filter -->
      <div class="flex items-center gap-2 mb-8 overflow-x-auto pb-2 scrollbar-hide">
        <button
          v-for="type in VIDEO_TYPES"
          :key="type.value"
          @click="selectedType = type.value"
          class="pill-toggle flex-shrink-0"
          :class="selectedType === type.value ? 'pill-toggle-active' : 'pill-toggle-inactive'"
        >
          {{ getVideoTypeTranslation(type.value) }}
        </button>
      </div>

      <!-- Video grid -->
      <div ref="gridRef" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="video in filteredVideos"
          :key="video.id"
          class="bg-white rounded-xl shadow-card overflow-hidden group hover:shadow-card-hover transition-all duration-300 cursor-pointer"
          @click="openVideo(video)"
        >
          <!-- Thumbnail placeholder -->
          <div class="relative aspect-video bg-charcoal-800 overflow-hidden">
            <div class="absolute inset-0 flex items-center justify-center bg-jade-gradient opacity-40">
              <div class="w-16 h-16 rounded-full bg-jade-500/80 flex items-center justify-center shadow-jade-glow group-hover:scale-110 transition-transform duration-300">
                <svg class="w-7 h-7 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M8 5v14l11-7z"/>
                </svg>
              </div>
            </div>
            <!-- Type badge -->
            <span class="absolute top-3 left-3 px-2 py-0.5 text-xs font-medium rounded bg-charcoal-800/70 text-white/80">
              {{ getVideoTypeLabel(video.type) }}
            </span>
          </div>

          <!-- Info -->
          <div class="p-4">
            <h3 class="font-heading font-semibold text-charcoal-800 group-hover:text-jade-600 transition-colors line-clamp-2">
              {{ video.title }}
            </h3>
            <div class="flex items-center gap-2 mt-2 text-sm text-charcoal-400">
              <time>{{ formatDate(video.date) }}</time>
              <span v-if="video.venue" class="flex items-center gap-1">
                &middot; {{ video.venue }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading state -->
      <div v-if="loading" class="text-center py-20">
        <div class="inline-block w-8 h-8 border-4 border-jade-200 border-t-jade-600 rounded-full animate-spin"></div>
      </div>

      <!-- Empty state -->
      <div v-else-if="filteredVideos.length === 0" class="text-center py-20">
        <p class="text-charcoal-400 text-lg">{{ $t('videos.empty') }}</p>
      </div>
    </div>

    <!-- Video modal -->
    <Teleport to="body">
      <transition name="fade">
        <div v-if="activeVideo" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-charcoal-900/90 backdrop-blur-sm" @click.self="activeVideo = null">
          <div class="relative w-full max-w-4xl bg-charcoal-800 rounded-xl overflow-hidden shadow-2xl">
            <button @click="activeVideo = null" class="absolute top-4 right-4 z-10 w-10 h-10 rounded-full bg-charcoal-700 text-white flex items-center justify-center hover:bg-charcoal-600 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
            <div class="aspect-video bg-charcoal-900 flex items-center justify-center">
              <p class="text-white/50 text-center px-8">
                <span class="block text-lg font-heading font-semibold text-white mb-2">{{ activeVideo.title }}</span>
                <span class="text-sm">{{ $t('videos.playerPlaceholder') }}</span>
              </p>
            </div>
          </div>
        </div>
      </transition>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { gsap } from 'gsap'
import { apiFetch } from '@/composables/useApi'
import { VIDEO_TYPES } from '@/utils/constants'
import { formatDate } from '@/utils/helpers'
import type { Video, VideoType } from '@/types/video'

const { t } = useI18n()

const videos = ref<Video[]>([])
const loading = ref(true)
const selectedType = ref<VideoType>('all')
const activeVideo = ref<Video | null>(null)
const headerRef = ref<HTMLElement | null>(null)
const gridRef = ref<HTMLElement | null>(null)

const filteredVideos = computed(() => {
  if (selectedType.value === 'all') return videos.value
  return videos.value.filter(v => v.type === selectedType.value)
})

const videoTypeKeyMap: Record<string, string> = {
  'all': 'all',
  'music-video': 'musicVideo',
  'performance': 'performance',
  'dance-practice': 'dancePractice',
  'behind-the-scenes': 'behindTheScenes',
}

function getVideoTypeTranslation(type: string): string {
  return t(`videoTypes.${videoTypeKeyMap[type] || type}`)
}

function getVideoTypeLabel(type: string): string {
  return getVideoTypeTranslation(type)
}

function openVideo(video: Video) {
  activeVideo.value = video
}

onMounted(async () => {
  try {
    videos.value = await apiFetch<Video[]>('/videos')
  } finally {
    loading.value = false
  }

  if (headerRef.value) {
    gsap.from(headerRef.value.children, {
      y: 20, opacity: 0, duration: 0.6, stagger: 0.1, ease: 'power2.out',
    })
  }
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
