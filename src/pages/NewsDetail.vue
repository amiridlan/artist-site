<template>
  <DefaultLayout>
    <article v-if="article" class="news-detail-page pt-24 pb-16">
      <!-- Hero Image Section -->
      <div class="relative h-[50vh] md:h-[60vh] lg:h-[70vh] mb-12 overflow-hidden">
        <img
          :src="article.image"
          :alt="article.title"
          class="w-full h-full object-cover"
        />
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>

        <!-- Article Meta (Overlay on Image) -->
        <div class="absolute bottom-0 left-0 right-0 p-6 md:p-12">
          <div class="container mx-auto max-w-4xl">
            <!-- Category Badge -->
            <div class="mb-4">
              <span
                :class="categoryClasses"
                class="inline-block px-4 py-2 rounded-full text-sm font-semibold uppercase tracking-wide"
              >
                {{ getCategoryLabel(article.category) }}
              </span>
            </div>

            <!-- Title -->
            <h1 class="font-outfit font-bold text-3xl md:text-4xl lg:text-5xl xl:text-6xl text-white mb-6 leading-tight">
              {{ article.title }}
            </h1>

            <!-- Meta Info -->
            <div class="flex flex-wrap items-center gap-4 md:gap-6 text-white/90 text-sm md:text-base">
              <span v-if="article.author" class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                {{ article.author }}
              </span>
              <time :datetime="article.publishedAt" class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ formattedDate }}
              </time>
              <span v-if="article.readTime" class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ article.readTime }} min read
              </span>
              <span v-if="article.views" class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                {{ formatViews(article.views) }} views
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Article Content -->
      <div class="container mx-auto px-4 max-w-4xl">
        <!-- Excerpt -->
        <div class="mb-8 p-6 bg-primary-50 border-l-4 border-primary-500 rounded-r-lg">
          <p class="text-lg md:text-xl text-dark-700 font-medium italic">
            {{ article.excerpt }}
          </p>
        </div>

        <!-- Main Content -->
        <div
          class="prose prose-lg max-w-none mb-12"
          v-html="article.content"
        ></div>

        <!-- Tags -->
        <div v-if="article.tags && article.tags.length > 0" class="mb-12">
          <h3 class="text-lg font-outfit font-semibold mb-4">Tags</h3>
          <div class="flex flex-wrap gap-2">
            <span
              v-for="tag in article.tags"
              :key="tag"
              class="px-4 py-2 bg-dark-100 text-dark-700 rounded-full text-sm hover:bg-primary-500 hover:text-white transition-colors cursor-pointer"
            >
              #{{ tag }}
            </span>
          </div>
        </div>

        <!-- Share Buttons -->
        <div class="mb-12 p-6 bg-dark-50 rounded-xl">
          <h3 class="text-lg font-outfit font-semibold mb-4">Share this article</h3>
          <div class="flex flex-wrap gap-3">
            <button
              @click="shareOnTwitter"
              class="flex items-center gap-2 px-4 py-2 bg-[#1DA1F2] text-white rounded-lg hover:bg-[#1a8cd8] transition-colors"
            >
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
              </svg>
              Twitter
            </button>
            <button
              @click="shareOnFacebook"
              class="flex items-center gap-2 px-4 py-2 bg-[#1877F2] text-white rounded-lg hover:bg-[#1664d8] transition-colors"
            >
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
              Facebook
            </button>
            <button
              @click="copyLink"
              class="flex items-center gap-2 px-4 py-2 bg-dark-600 text-white rounded-lg hover:bg-dark-700 transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
              </svg>
              {{ linkCopied ? 'Copied!' : 'Copy Link' }}
            </button>
          </div>
        </div>

        <!-- Back to News Button -->
        <div class="text-center">
          <router-link
            to="/news"
            class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-primary text-white rounded-full font-outfit font-semibold uppercase text-sm tracking-wide hover:scale-105 transition-all duration-300 hover:shadow-neon"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            Back to News
          </router-link>
        </div>
      </div>
    </article>

    <!-- Loading State -->
    <div v-else-if="isLoading" class="min-h-screen pt-24 pb-16 flex items-center justify-center">
      <div class="text-center">
        <div class="inline-block w-16 h-16 border-4 border-primary-500 border-t-transparent rounded-full animate-spin mb-4"></div>
        <p class="text-xl text-dark-600">Loading article...</p>
      </div>
    </div>

    <!-- Not Found State -->
    <div v-else class="min-h-screen pt-24 pb-16 flex items-center justify-center">
      <div class="text-center">
        <h1 class="text-6xl font-outfit font-bold mb-4">404</h1>
        <p class="text-2xl text-dark-600 mb-8">Article not found</p>
        <router-link
          to="/news"
          class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-primary text-white rounded-full font-outfit font-semibold uppercase text-sm tracking-wide hover:scale-105 transition-all"
        >
          Back to News
        </router-link>
      </div>
    </div>
  </DefaultLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { format } from 'date-fns'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import type { NewsArticle, NewsCategory } from '@/types/news'
import { NEWS_CATEGORIES } from '@/utils/constants'
import newsData from '@/data/news.json'

// Route
const route = useRoute()

// State
const article = ref<NewsArticle | null>(null)
const isLoading = ref(true)
const linkCopied = ref(false)

// Computed
const formattedDate = computed(() => {
  if (!article.value) return ''
  return format(new Date(article.value.publishedAt), 'MMMM d, yyyy')
})

const categoryClasses = computed(() => {
  if (!article.value) return ''

  const baseClasses = 'text-white'

  switch (article.value.category) {
    case 'news':
      return `${baseClasses} bg-primary-500`
    case 'fanclub':
      return `${baseClasses} bg-accent-purple`
    case 'store':
      return `${baseClasses} bg-accent-pink`
    case 'event':
      return `${baseClasses} bg-accent-blue`
    case 'release':
      return `${baseClasses} bg-gradient-primary`
    default:
      return `${baseClasses} bg-dark-600`
  }
})

// Methods
const getCategoryLabel = (category: NewsCategory): string => {
  const cat = NEWS_CATEGORIES.find(c => c.value === category)
  return cat?.label || category.toUpperCase()
}

const formatViews = (views: number): string => {
  if (views >= 1000000) {
    return `${(views / 1000000).toFixed(1)}M`
  }
  if (views >= 1000) {
    return `${(views / 1000).toFixed(1)}K`
  }
  return views.toString()
}

const loadArticle = () => {
  const slug = route.params.slug as string
  const foundArticle = (newsData as NewsArticle[]).find(a => a.slug === slug)

  if (foundArticle) {
    article.value = foundArticle
  }

  isLoading.value = false
}

const shareOnTwitter = () => {
  const url = window.location.href
  const text = article.value?.title || ''
  window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(text)}`, '_blank')
}

const shareOnFacebook = () => {
  const url = window.location.href
  window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank')
}

const copyLink = async () => {
  try {
    await navigator.clipboard.writeText(window.location.href)
    linkCopied.value = true
    setTimeout(() => {
      linkCopied.value = false
    }, 2000)
  } catch (err) {
    console.error('Failed to copy link:', err)
  }
}

// Lifecycle
onMounted(() => {
  loadArticle()
})
</script>

<style>
/* Prose styles for article content */
.prose {
  @apply text-dark-800 leading-relaxed;
}

.prose p {
  @apply mb-6 text-lg;
}

.prose h2 {
  @apply text-3xl font-outfit font-bold mt-12 mb-6;
}

.prose h3 {
  @apply text-2xl font-outfit font-bold mt-8 mb-4;
}

.prose ul, .prose ol {
  @apply mb-6 ml-6;
}

.prose li {
  @apply mb-2;
}

.prose strong {
  @apply font-semibold text-dark-900;
}

.prose a {
  @apply text-primary-500 hover:text-primary-600 underline;
}
</style>
