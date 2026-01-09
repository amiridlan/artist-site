<template>
  <DefaultLayout>
    <div class="releases-page min-h-screen pt-24 pb-16">
      <div class="container mx-auto px-4 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8 md:mb-12">
          <h1 class="text-4xl md:text-5xl lg:text-6xl font-outfit font-bold mb-4">
            <span class="text-gradient">Releases</span>
          </h1>
          <p class="text-lg md:text-xl text-dark-600 max-w-2xl">
            Explore KLP48's discography featuring singles, albums, and EPs. {{ totalReleases }} releases available.
          </p>
        </div>

        <!-- Featured Release -->
        <div v-if="featuredReleases.length > 0" class="mb-12">
          <div class="relative rounded-2xl overflow-hidden shadow-2xl bg-gradient-to-br from-primary-500 to-accent-purple p-8 md:p-12">
            <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
              <!-- Cover Image -->
              <div class="relative">
                <img
                  :src="featuredReleases[0].coverImage"
                  :alt="featuredReleases[0].title"
                  class="w-full max-w-md mx-auto rounded-xl shadow-2xl"
                />
                <div v-if="isUpcoming(featuredReleases[0])" class="absolute top-4 right-4">
                  <span class="px-4 py-2 bg-accent-yellow text-dark-900 rounded-full text-sm font-bold uppercase animate-pulse">
                    Coming Soon
                  </span>
                </div>
              </div>

              <!-- Info -->
              <div class="text-white">
                <div class="inline-block px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-sm font-bold uppercase mb-4">
                  Featured Release
                </div>
                <h2 class="text-3xl md:text-4xl font-outfit font-bold mb-4">
                  {{ featuredReleases[0].title }}
                </h2>
                <p class="text-xl mb-4">
                  {{ isUpcoming(featuredReleases[0]) 
                    ? getCountdown(featuredReleases[0]) 
                    : formatDate(featuredReleases[0].releaseDate) }}
                </p>
                <p class="text-white/90 mb-6 leading-relaxed">
                  {{ featuredReleases[0].description }}
                </p>

                <!-- Streaming Links -->
                <div v-if="featuredReleases[0].streamingLinks" class="flex flex-wrap gap-3">
                  <a
                    v-for="link in featuredReleases[0].streamingLinks"
                    :key="link.platform"
                    :href="link.url"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="px-6 py-3 bg-white text-dark-900 rounded-full font-medium hover:scale-105 transition-transform flex items-center gap-2"
                  >
                    <span>{{ getPlatformName(link.platform) }}</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                  </a>
                </div>
              </div>
            </div>

            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-accent-yellow/20 rounded-full blur-3xl"></div>
          </div>
        </div>

        <!-- Search Bar -->
        <div class="mb-6">
          <div class="relative max-w-2xl">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search releases by title or description..."
              class="w-full pl-12 pr-4 py-3 md:py-4 rounded-lg border-2 border-dark-200 focus:border-primary-500 focus:outline-none text-dark-800 placeholder-dark-400 transition-colors"
            />
            <svg
              class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-dark-400"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <button
              v-if="searchQuery"
              @click="searchQuery = ''"
              class="absolute right-4 top-1/2 transform -translate-y-1/2 text-dark-400 hover:text-dark-600"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Type Filter & Options -->
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-8">
          <ReleaseTypeFilter
            v-model:active-type="selectedType"
            :count="filteredReleases.length"
          />

          <div class="flex items-center gap-4">
            <!-- Show Upcoming Toggle -->
            <label class="flex items-center gap-2 cursor-pointer">
              <input
                v-model="showUpcoming"
                type="checkbox"
                class="w-5 h-5 rounded text-primary-500 focus:ring-2 focus:ring-primary-500"
              />
              <span class="text-sm font-medium text-dark-700">Show Upcoming</span>
            </label>

            <!-- Sort Dropdown -->
            <select
              v-model="sortBy"
              class="px-4 py-3 rounded-lg border-2 border-dark-200 focus:border-primary-500 focus:outline-none text-dark-800 appearance-none cursor-pointer transition-colors bg-white"
            >
              <option value="date">Sort by Date (Newest)</option>
              <option value="title">Sort by Title (A-Z)</option>
              <option value="sales">Sort by Sales</option>
            </select>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="isLoading" class="release-grid mb-12">
          <div v-for="n in 6" :key="`skeleton-${n}`" class="bg-white rounded-xl overflow-hidden shadow-md animate-pulse">
            <div class="aspect-square bg-dark-200"></div>
            <div class="p-5 space-y-3">
              <div class="h-6 bg-dark-200 rounded w-3/4"></div>
              <div class="h-4 bg-dark-200 rounded w-1/2"></div>
              <div class="h-4 bg-dark-200 rounded w-full"></div>
            </div>
          </div>
        </div>

        <!-- Release Grid -->
        <div v-else-if="paginatedReleases.length > 0" class="release-grid mb-12">
          <ReleaseCard
            v-for="release in paginatedReleases"
            :key="release.id"
            :release="release"
            @click="handleReleaseClick"
          />
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-20">
          <svg class="w-24 h-24 mx-auto mb-6 text-dark-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
          </svg>
          <h3 class="text-2xl font-outfit font-bold mb-2">No releases found</h3>
          <p class="text-dark-600 mb-6">Try adjusting your filters or search query.</p>
          <button
            @click="resetFilters"
            class="px-6 py-3 bg-gradient-primary text-white rounded-full font-outfit font-semibold uppercase text-sm tracking-wide hover:scale-105 transition-all"
          >
            Show All Releases
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
                ? 'bg-dark-200 text-dark-400 cursor-not-allowed'
                : 'bg-white text-dark-700 hover:bg-primary-500 hover:text-white'
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
                ? 'bg-gradient-primary text-white'
                : 'bg-white text-dark-700 hover:bg-primary-500 hover:text-white'
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
                ? 'bg-dark-200 text-dark-400 cursor-not-allowed'
                : 'bg-white text-dark-700 hover:bg-primary-500 hover:text-white'
            ]"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </DefaultLayout>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import { format, formatDistanceToNowStrict } from 'date-fns'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
// import ReleaseCard from '@/components/releases/ReleaseCard.vue'
import ReleaseTypeFilter from '@/components/releases/ReleaseTypeFilter.vue'
import type { Release, ReleaseType } from '@/types/release'
import releasesData from '@/data/releases.json'

// State
const allReleases = ref<Release[]>([])
const isLoading = ref(true)
const selectedType = ref<ReleaseType>('all')
const searchQuery = ref('')
const sortBy = ref<'date' | 'title' | 'sales'>('date')
const showUpcoming = ref(true)
const currentPage = ref(1)
const releasesPerPage = 6

// Computed
const totalReleases = computed(() => allReleases.value.length)

const featuredReleases = computed(() => {
  return allReleases.value.filter(release => release.featured)
})

const filteredReleases = computed(() => {
  let result = [...allReleases.value].filter(release => !release.featured)

  // Apply upcoming filter
  if (!showUpcoming.value) {
    result = result.filter(release => new Date(release.releaseDate) <= new Date())
  }

  // Apply type filter
  if (selectedType.value !== 'all') {
    result = result.filter(release => release.type === selectedType.value)
  }

  // Apply search
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(release =>
      release.title.toLowerCase().includes(query) ||
      release.description.toLowerCase().includes(query)
    )
  }

  // Apply sorting
  result.sort((a, b) => {
    switch (sortBy.value) {
      case 'date':
        return new Date(b.releaseDate).getTime() - new Date(a.releaseDate).getTime()
      case 'title':
        return a.title.localeCompare(b.title)
      case 'sales':
        return (b.sales || 0) - (a.sales || 0)
      default:
        return 0
    }
  })

  return result
})

const totalPages = computed(() => {
  return Math.ceil(filteredReleases.value.length / releasesPerPage)
})

const paginatedReleases = computed(() => {
  const start = (currentPage.value - 1) * releasesPerPage
  const end = start + releasesPerPage
  return filteredReleases.value.slice(start, end)
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
const loadReleases = () => {
  isLoading.value = true
  setTimeout(() => {
    allReleases.value = releasesData as Release[]
    isLoading.value = false
  }, 500)
}

const isUpcoming = (release: Release): boolean => {
  return new Date(release.releaseDate) > new Date()
}

const getCountdown = (release: Release): string => {
  const releaseDate = new Date(release.releaseDate)
  return `Releases ${formatDistanceToNowStrict(releaseDate, { addSuffix: true })}`
}

const formatDate = (dateString: string): string => {
  return format(new Date(dateString), 'MMMM d, yyyy')
}

const getPlatformName = (platform: string): string => {
  const names: Record<string, string> = {
    'spotify': 'Spotify',
    'apple-music': 'Apple Music',
    'youtube-music': 'YouTube Music',
    'amazon-music': 'Amazon Music'
  }
  return names[platform] || platform
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
  showUpcoming.value = true
  currentPage.value = 1
}

const handleReleaseClick = (release: Release) => {
  // Future: Navigate to release detail page
  console.log('Release clicked:', release.title)
  
  // For now, if has streaming links, open first one
  if (release.streamingLinks && release.streamingLinks.length > 0) {
    window.open(release.streamingLinks[0].url, '_blank')
  }
}

// Watchers
watch([selectedType, searchQuery, sortBy, showUpcoming], () => {
  currentPage.value = 1
})

// Lifecycle
onMounted(() => {
  loadReleases()
})
</script>

<style scoped>
.release-grid {
  display: grid;
  grid-template-columns: repeat(1, 1fr);
  gap: 1.5rem;
}

@media (min-width: 640px) {
  .release-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 1024px) {
  .release-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (min-width: 1280px) {
  .release-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
  }
}
</style>