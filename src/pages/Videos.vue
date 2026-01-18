<template>
  <div class="videos-page-wrapper">
    <div class="videos-page min-h-screen pt-24 pb-16">
      <div class="container mx-auto px-4 lg:px-8">
      <!-- Page Header -->
      <div class="mb-8 md:mb-12">
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-outfit font-bold mb-4">
          <span class="text-gradient">Videos</span>
        </h1>
        <p class="text-lg md:text-xl text-neutral-600 max-w-2xl">
          Watch KLP48's music videos, performances, behind-the-scenes content, and more. {{ totalVideos }} videos available.
        </p>
      </div>

      <!-- Featured Videos Banner -->
      <div v-if="featuredVideos.length > 0" class="mb-12">
        <h2 class="text-2xl font-outfit font-bold mb-6">Featured Videos</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <VideoCard
            v-for="video in featuredVideos.slice(0, 2)"
            :key="video.id"
            :video="video"
            :show-tags="true"
            @click="openVideoPlayer"
          />
        </div>
      </div>

      <!-- Search Bar -->
      <div class="mb-6">
        <div class="relative max-w-2xl">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search videos by title, description, or tags..."
            class="w-full pl-12 pr-4 py-3 md:py-4 rounded-lg border-2 border-neutral-200 focus:border-primary-500 focus:outline-none text-neutral-800 placeholder-dark-400 transition-colors"
          />
          <svg
            class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-neutral-400"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <button
            v-if="searchQuery"
            @click="searchQuery = ''"
            class="absolute right-4 top-1/2 transform -translate-y-1/2 text-neutral-400 hover:text-neutral-600"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Type Filter & Sort -->
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-8">
        <VideoTypeFilter
          v-model:active-type="selectedType"
          :count="filteredVideos.length"
        />

        <!-- Sort Dropdown -->
        <div class="w-full md:w-64">
          <select
            v-model="sortBy"
            class="w-full px-4 py-3 rounded-lg border-2 border-neutral-200 focus:border-primary-500 focus:outline-none text-neutral-800 appearance-none cursor-pointer transition-colors bg-white"
          >
            <option value="date">Sort by Date (Newest)</option>
            <option value="views">Sort by Views</option>
            <option value="title">Sort by Title (A-Z)</option>
            <option value="duration">Sort by Duration</option>
          </select>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="video-grid mb-12">
        <div v-for="n in 8" :key="`skeleton-${n}`" class="bg-white rounded-xl overflow-hidden shadow-md animate-pulse">
          <div class="aspect-video bg-neutral-200"></div>
          <div class="p-4 space-y-3">
            <div class="h-5 bg-neutral-200 rounded w-3/4"></div>
            <div class="h-4 bg-neutral-200 rounded w-full"></div>
            <div class="h-4 bg-neutral-200 rounded w-2/3"></div>
          </div>
        </div>
      </div>

      <!-- Video Grid -->
      <div v-else-if="paginatedVideos.length > 0" class="video-grid mb-12">
        <VideoCard
          v-for="video in paginatedVideos"
          :key="video.id"
          :video="video"
          :show-tags="true"
          @click="openVideoPlayer"
        />
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-20">
        <svg class="w-24 h-24 mx-auto mb-6 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
        </svg>
        <h3 class="text-2xl font-outfit font-bold mb-2">No videos found</h3>
        <p class="text-neutral-600 mb-6">Try adjusting your filters or search query.</p>
        <button
          @click="resetFilters"
          class="px-6 py-3 bg-primary-500 text-white rounded-full font-outfit font-semibold uppercase text-sm tracking-wide hover:scale-105 transition-all"
        >
          Show All Videos
        </button>
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="flex justify-center items-center gap-2">
        <button
          @click="goToPage(currentPage - 1)"
          :disabled="currentPage === 1"
          :class="[
            'px-4 py-2 rounded-lg font-medium transition-all',
            currentPage === 1
              ? 'bg-neutral-200 text-neutral-400 cursor-not-allowed'
              : 'bg-white text-neutral-700 hover:bg-primary-500 hover:text-white'
          ]"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>

        <button
          v-for="page in visiblePages"
          :key="page"
          @click="goToPage(page)"
          :class="[
            'px-4 py-2 rounded-lg font-medium transition-all',
            currentPage === page
              ? 'bg-primary-500 text-white'
              : 'bg-white text-neutral-700 hover:bg-primary-500 hover:text-white'
          ]"
        >
          {{ page }}
        </button>

        <button
          @click="goToPage(currentPage + 1)"
          :disabled="currentPage === totalPages"
          :class="[
            'px-4 py-2 rounded-lg font-medium transition-all',
            currentPage === totalPages
              ? 'bg-neutral-200 text-neutral-400 cursor-not-allowed'
              : 'bg-white text-neutral-700 hover:bg-primary-500 hover:text-white'
          ]"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
        </div>
      </div>
    </div>

    <!-- Video Player Modal -->
    <VideoPlayerModal
      :is-open="isPlayerOpen"
      :video="selectedVideo"
      @close="closeVideoPlayer"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import VideoCard from '@/components/videos/VideoCard.vue'
import VideoTypeFilter from '@/components/videos/VideoTypeFilter.vue'
import VideoPlayerModal from '@/components/videos/VideoPlayerModal.vue'
import type { Video, VideoType } from '@/types/video'
import videosData from '@/data/videos.json'

// State
const allVideos = ref<Video[]>([])
const isLoading = ref(true)
const selectedType = ref<VideoType>('all')
const searchQuery = ref('')
const sortBy = ref<'date' | 'views' | 'title' | 'duration'>('date')
const currentPage = ref(1)
const videosPerPage = 8

const isPlayerOpen = ref(false)
const selectedVideo = ref<Video | null>(null)

// Computed
const totalVideos = computed(() => allVideos.value.length)

const featuredVideos = computed(() => {
  return allVideos.value.filter(video => video.featured)
})

const filteredVideos = computed(() => {
  let result = [...allVideos.value].filter(video => !video.featured)

  // Apply type filter
  if (selectedType.value !== 'all') {
    result = result.filter(video => video.type === selectedType.value)
  }

  // Apply search
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(video =>
      video.title.toLowerCase().includes(query) ||
      video.description.toLowerCase().includes(query) ||
      (video.tags && video.tags.some(tag => tag.toLowerCase().includes(query)))
    )
  }

  // Apply sorting
  result.sort((a, b) => {
    switch (sortBy.value) {
      case 'date':
        return new Date(b.releaseDate).getTime() - new Date(a.releaseDate).getTime()
      case 'views':
        return b.views - a.views
      case 'title':
        return a.title.localeCompare(b.title)
      case 'duration':
        return b.duration - a.duration
      default:
        return 0
    }
  })

  return result
})

const totalPages = computed(() => {
  return Math.ceil(filteredVideos.value.length / videosPerPage)
})

const paginatedVideos = computed(() => {
  const start = (currentPage.value - 1) * videosPerPage
  const end = start + videosPerPage
  return filteredVideos.value.slice(start, end)
})

const visiblePages = computed(() => {
  const pages: number[] = []
  const maxVisible = 5
  const total = totalPages.value

  if (total <= maxVisible) {
    for (let i = 1; i <= total; i++) {
      pages.push(i)
    }
  } else {
    const current = currentPage.value
    const half = Math.floor(maxVisible / 2)

    let start = Math.max(1, current - half)
    let end = Math.min(total, current + half)

    if (current <= half) {
      end = maxVisible
    } else if (current >= total - half) {
      start = total - maxVisible + 1
    }

    for (let i = start; i <= end; i++) {
      pages.push(i)
    }
  }

  return pages
})

// Methods
const loadVideos = () => {
  isLoading.value = true
  setTimeout(() => {
    allVideos.value = videosData as Video[]
    isLoading.value = false
  }, 500)
}

const goToPage = (page: number) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const resetFilters = () => {
  selectedType.value = 'all'
  searchQuery.value = ''
  sortBy.value = 'date'
  currentPage.value = 1
}

const openVideoPlayer = (video: Video) => {
  selectedVideo.value = video
  isPlayerOpen.value = true
}

const closeVideoPlayer = () => {
  isPlayerOpen.value = false
  selectedVideo.value = null
}

// Watchers
watch([selectedType, searchQuery, sortBy], () => {
  currentPage.value = 1
})

// Lifecycle
onMounted(() => {
  loadVideos()
})
</script>

<style scoped>
.video-grid {
  display: grid;
  grid-template-columns: repeat(1, 1fr);
  gap: 1.5rem;
}

@media (min-width: 640px) {
  .video-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 1024px) {
  .video-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (min-width: 1280px) {
  .video-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}
</style>