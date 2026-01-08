<template>
  <DefaultLayout>
    <div class="news-page min-h-screen pt-24 pb-16">
      <div class="container mx-auto px-4 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8 md:mb-12">
          <h1 class="text-4xl md:text-5xl lg:text-6xl font-outfit font-bold mb-4">
            <span class="text-gradient">Latest News</span>
          </h1>
          <p class="text-lg md:text-xl text-dark-600 max-w-2xl">
            Stay updated with the latest news, announcements, and exclusive content from KLP48.
          </p>
        </div>

        <!-- Featured News Banner -->
        <FeaturedNewsBanner
          v-if="featuredArticles.length > 0"
          :featured-article="featuredArticles[0]"
        />

        <!-- Category Filter -->
        <CategoryFilter
          v-model:active-category="selectedCategory"
          :count="filteredArticles.length"
        />

        <!-- Loading Skeletons -->
        <div v-if="isLoading" class="news-grid mb-12">
          <NewsCardSkeleton v-for="n in 6" :key="`skeleton-${n}`" />
        </div>

        <!-- News Grid -->
        <div v-else-if="paginatedArticles.length > 0" class="news-grid mb-12">
          <NewsCard
            v-for="article in paginatedArticles"
            :key="article.id"
            :article="article"
            :show-tags="true"
          />
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-20">
          <svg class="w-24 h-24 mx-auto mb-6 text-dark-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <h3 class="text-2xl font-outfit font-bold mb-2">No articles found</h3>
          <p class="text-dark-600 mb-6">Try selecting a different category.</p>
          <button
            @click="selectedCategory = 'all'"
            class="px-6 py-3 bg-gradient-primary text-white rounded-full font-outfit font-semibold uppercase text-sm tracking-wide hover:scale-105 transition-all"
          >
            Show All News
          </button>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="flex justify-center items-center gap-2">
          <!-- Previous Button -->
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

          <!-- Page Numbers -->
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

          <!-- Next Button -->
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
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import FeaturedNewsBanner from '@/components/news/FeaturedNewsBanner.vue'
import CategoryFilter from '@/components/news/CategoryFilter.vue'
import NewsCard from '@/components/news/NewsCard.vue'
import NewsCardSkeleton from '@/components/common/NewsCardSkeleton.vue'
import type { NewsArticle, NewsCategory } from '@/types/news'
import newsData from '@/data/news.json'

// State
const allArticles = ref<NewsArticle[]>([])
const isLoading = ref(true)
const selectedCategory = ref<NewsCategory>('all')
const currentPage = ref(1)
const articlesPerPage = 9

// Computed
const featuredArticles = computed(() => {
  return allArticles.value.filter(article => article.featured)
})

const filteredArticles = computed(() => {
  if (selectedCategory.value === 'all') {
    return allArticles.value.filter(article => !article.featured)
  }
  return allArticles.value.filter(
    article => article.category === selectedCategory.value && !article.featured
  )
})

const totalPages = computed(() => {
  return Math.ceil(filteredArticles.value.length / articlesPerPage)
})

const paginatedArticles = computed(() => {
  const start = (currentPage.value - 1) * articlesPerPage
  const end = start + articlesPerPage
  return filteredArticles.value.slice(start, end)
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
const loadArticles = () => {
  isLoading.value = true
  // Simulate loading delay
  setTimeout(() => {
    allArticles.value = newsData as NewsArticle[]
    isLoading.value = false
  }, 500)
}

const goToPage = (page: number) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    // Scroll to top of news grid
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

// Watchers
watch(selectedCategory, () => {
  currentPage.value = 1
})

// Lifecycle
onMounted(() => {
  loadArticles()
})
</script>

<style scoped>
/* Masonry-style grid using CSS Grid */
.news-grid {
  display: grid;
  grid-template-columns: repeat(1, 1fr);
  gap: 1.5rem;
}

@media (min-width: 640px) {
  .news-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 1024px) {
  .news-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

/* For true masonry effect, we could use CSS Masonry Layout (experimental)
   or a library like Masonry.js, but grid works well for now */
</style>