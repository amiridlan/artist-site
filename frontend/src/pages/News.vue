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

      <!-- News grid -->
      <div ref="gridRef" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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

      <!-- Loading state -->
      <div v-if="loading" class="text-center py-20">
        <div class="inline-block w-8 h-8 border-4 border-jade-200 border-t-jade-600 rounded-full animate-spin"></div>
      </div>

      <!-- Empty state -->
      <div v-else-if="filteredNews.length === 0" class="text-center py-20">
        <p class="text-charcoal-400 text-lg">{{ $t('news.empty') }}</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { gsap } from 'gsap'
import { apiFetch } from '@/composables/useApi'
import { NEWS_CATEGORIES } from '@/utils/constants'
import { formatDate, getCategoryColor } from '@/utils/helpers'
import type { NewsArticle, NewsCategory } from '@/types/news'

const news = ref<NewsArticle[]>([])
const loading = ref(true)
const selectedCategory = ref<NewsCategory>('all')
const headerRef = ref<HTMLElement | null>(null)
const gridRef = ref<HTMLElement | null>(null)

const filteredNews = computed(() => {
  if (selectedCategory.value === 'all') return news.value
  return news.value.filter(a => a.category === selectedCategory.value)
})

onMounted(async () => {
  try {
    news.value = await apiFetch<NewsArticle[]>('/news')
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
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
