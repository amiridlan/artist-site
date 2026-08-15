<template>
  <div class="pt-24 pb-16 min-h-screen bg-cream-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Page header -->
      <div ref="headerRef" class="mb-10">
        <h1 class="text-4xl md:text-5xl font-heading font-bold text-charcoal-800">{{ $t('releases.title') }}</h1>
        <p class="text-charcoal-500 mt-2">{{ $t('releases.subtitle') }}</p>
      </div>

      <!-- Filter -->
      <div class="flex items-center gap-2 mb-8 overflow-x-auto pb-2 scrollbar-hide">
        <button
          v-for="type in RELEASE_TYPES"
          :key="type.value"
          @click="selectedType = type.value"
          class="pill-toggle flex-shrink-0"
          :class="selectedType === type.value ? 'pill-toggle-active' : 'pill-toggle-inactive'"
        >
          {{ $t(`releaseTypes.${type.value === 'digital-single' ? 'digitalSingle' : type.value}`) }}
        </button>
      </div>

      <!-- Skeleton loading state -->
      <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" aria-hidden="true">
        <div v-for="i in 6" :key="i" class="bg-white rounded-xl shadow-card overflow-hidden animate-pulse">
          <div class="aspect-square bg-charcoal-100"></div>
          <div class="p-5">
            <div class="h-5 w-2/3 rounded bg-charcoal-100 mb-2"></div>
            <div class="h-3 w-1/3 rounded bg-charcoal-100"></div>
          </div>
        </div>
      </div>

      <!-- Releases grid -->
      <div v-else-if="!error" ref="gridRef" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div
          v-for="release in filteredReleases"
          :key="release.id"
          class="bg-white rounded-xl shadow-card overflow-hidden group hover:shadow-card-hover transition-all duration-300"
        >
          <!-- Cover art -->
          <div class="relative aspect-square bg-charcoal-800 overflow-hidden">
            <ReleaseCoverArt :cover-image="release.coverImage" :title="release.title" />
            <!-- Hover overlay: track list -->
            <div class="absolute inset-0 bg-charcoal-800/80 translate-y-full group-hover:translate-y-0 transition-transform duration-500 flex items-center justify-center">
              <div class="text-center px-6">
                <p class="text-white/50 text-xs uppercase tracking-wider mb-3">{{ $t('releases.trackList') }}</p>
                <ul class="text-sm text-white/80 space-y-1">
                  <li v-for="track in release.tracks" :key="track.number">
                    {{ track.number }}. {{ track.title }}
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Info -->
          <div class="p-5">
            <div class="flex items-start justify-between gap-2">
              <div>
                <h3 class="font-heading font-bold text-charcoal-800 text-lg">{{ release.title }}</h3>
                <p class="text-sm text-charcoal-400">{{ formatDate(release.releaseDate, 'yyyy.MM.dd') }}</p>
              </div>
              <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-jade-100 text-jade-700 flex-shrink-0">
                {{ release.type.toUpperCase() }}
              </span>
            </div>
            <p v-if="release.description" class="text-sm text-charcoal-500 mt-3 line-clamp-2">
              {{ release.description }}
            </p>
          </div>
        </div>
      </div>

      <!-- Error state -->
      <div v-if="!loading && error" role="alert" class="text-center py-20">
        <p class="text-charcoal-400 text-lg mb-4">{{ $t('common.loadError') }}</p>
        <button @click="fetchReleases" class="pill-toggle pill-toggle-active">{{ $t('common.retry') }}</button>
      </div>

      <!-- Empty state -->
      <div v-else-if="!loading && filteredReleases.length === 0" class="text-center py-20">
        <p class="text-charcoal-400 text-lg">{{ $t('releases.empty') }}</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { gsap } from 'gsap'
import { apiFetch } from '@/composables/useApi'
import { useLanguageStore } from '@/stores/language'
import { prefersReducedMotion } from '@/utils/motion'
import ReleaseCoverArt from '@/components/ui/ReleaseCoverArt.vue'
import { RELEASE_TYPES } from '@/utils/constants'
import { formatDate } from '@/utils/helpers'
import type { Release, ReleaseType } from '@/types/release'

const languageStore = useLanguageStore()
const releases = ref<Release[]>([])
const loading = ref(true)
const error = ref(false)
const selectedType = ref<ReleaseType>('all')
const headerRef = ref<HTMLElement | null>(null)
const gridRef = ref<HTMLElement | null>(null)

const filteredReleases = computed(() => {
  if (selectedType.value === 'all') return releases.value
  return releases.value.filter(r => r.type === selectedType.value)
})

async function fetchReleases() {
  loading.value = true
  error.value = false
  try {
    releases.value = await apiFetch<Release[]>('/releases')
  } catch {
    error.value = true
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await fetchReleases()

  if (headerRef.value && !prefersReducedMotion()) {
    gsap.from(headerRef.value.children, {
      y: 20, opacity: 0, duration: 0.6, stagger: 0.1, ease: 'power2.out',
    })
  }
})

watch(() => languageStore.currentLang, fetchReleases)
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
