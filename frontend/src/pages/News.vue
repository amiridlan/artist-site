<template>
  <div class="pt-24 pb-16 min-h-screen bg-cream-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Page header -->
      <div ref="headerRef" class="mb-10">
        <h1 class="text-4xl md:text-5xl font-heading font-bold text-charcoal-800">{{ $t('news.title') }}</h1>
        <p class="text-charcoal-500 mt-2">{{ $t('news.subtitle') }}</p>
      </div>

      <!-- Category filter -->
      <div class="flex items-center gap-2 mb-8 overflow-x-auto pb-2 scrollbar-hide">
        <button
          v-for="cat in NEWS_CATEGORIES"
          :key="cat.value"
          @click="selectedCategory = cat.value"
          class="pill-toggle flex-shrink-0"
          :class="selectedCategory === cat.value ? 'pill-toggle-active' : 'pill-toggle-inactive'"
        >
          {{ $t(`newsCategories.${cat.value}`) }}
        </button>
      </div>

      <!-- Skeleton loading state -->
      <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" aria-hidden="true">
        <div v-for="i in 6" :key="i" class="card-jade p-6 animate-pulse">
          <div class="h-5 w-16 rounded-full bg-charcoal-100 mb-3"></div>
          <div class="h-4 w-5/6 rounded bg-charcoal-100 mb-2"></div>
          <div class="h-4 w-2/3 rounded bg-charcoal-100 mb-3"></div>
          <div class="h-3 w-1/3 rounded bg-charcoal-100"></div>
        </div>
      </div>

      <!-- News grid -->
      <div v-else-if="!error" ref="gridRef" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <router-link
          v-for="article in filteredNews"
          :key="article.id"
          :to="`/news/${article.slug}`"
          class="card-jade batik-overlay p-6 group"
        >
          <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full mb-3"
                :class="getCategoryColor(article.category)">
            {{ article.category }}
          </span>
          <h3 class="font-heading font-semibold text-charcoal-800 mb-2 group-hover:text-jade-600 transition-colors line-clamp-2">
            {{ article.title }}
          </h3>
          <p class="text-sm text-charcoal-500 line-clamp-3 mb-3">{{ article.excerpt }}</p>
          <time class="text-xs text-charcoal-400">{{ formatDate(article.date) }}</time>
        </router-link>
      </div>

      <!-- Error state -->
      <div v-if="!loading && error" role="alert" class="text-center py-20">
        <p class="text-charcoal-400 text-lg mb-4">{{ $t('common.loadError') }}</p>
        <button @click="fetchNews" class="pill-toggle pill-toggle-active">{{ $t('common.retry') }}</button>
      </div>

      <!-- Empty state -->
      <div v-else-if="!loading && filteredNews.length === 0" class="text-center py-20">
        <p class="text-charcoal-400 text-lg">{{ $t('news.empty') }}</p>
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
import { NEWS_CATEGORIES } from '@/utils/constants'
import { formatDate, getCategoryColor } from '@/utils/helpers'
import type { NewsArticle, NewsCategory } from '@/types/news'

const languageStore = useLanguageStore()
const news = ref<NewsArticle[]>([])
const loading = ref(true)
const error = ref(false)
const selectedCategory = ref<NewsCategory>('all')
const headerRef = ref<HTMLElement | null>(null)
const gridRef = ref<HTMLElement | null>(null)

const filteredNews = computed(() => {
  if (selectedCategory.value === 'all') return news.value
  return news.value.filter(a => a.category === selectedCategory.value)
})

async function fetchNews() {
  loading.value = true
  error.value = false
  try {
    news.value = await apiFetch<NewsArticle[]>('/news')
  } catch {
    error.value = true
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await fetchNews()

  if (headerRef.value && !prefersReducedMotion()) {
    gsap.from(headerRef.value.children, {
      y: 20, opacity: 0, duration: 0.6, stagger: 0.1, ease: 'power2.out',
    })
  }
})

watch(() => languageStore.currentLang, fetchNews)
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
