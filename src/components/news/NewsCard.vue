<template>
  <article
    class="news-card group relative bg-white rounded-xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 cursor-pointer"
    @click="navigateToArticle"
  >
    <!-- Image Container with Overlay -->
    <div class="relative h-48 md:h-56 overflow-hidden">
      <img
        :src="article.image"
        :alt="article.title"
        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
        loading="lazy"
      />

      <!-- Gradient Overlay -->
      <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

      <!-- Category Badge -->
      <div class="absolute top-4 left-4">
        <span
          :class="categoryClasses"
          class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide backdrop-blur-sm"
        >
          {{ getCategoryLabel(article.category) }}
        </span>
      </div>

      <!-- Featured Badge -->
      <div v-if="article.featured" class="absolute top-4 right-4">
        <span class="px-3 py-1 bg-accent-yellow text-dark-900 rounded-full text-xs font-bold uppercase tracking-wide flex items-center gap-1">
          <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
          </svg>
          Featured
        </span>
      </div>

      <!-- Read Time -->
      <div v-if="article.readTime" class="absolute bottom-4 right-4">
        <span class="px-2 py-1 bg-black/50 text-white rounded text-xs backdrop-blur-sm flex items-center gap-1">
          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          {{ article.readTime }} min
        </span>
      </div>
    </div>

    <!-- Content Section -->
    <div class="p-5 md:p-6">
      <!-- Title -->
      <h3 class="font-outfit font-bold text-lg md:text-xl mb-3 line-clamp-2 group-hover:text-primary-500 transition-colors duration-300">
        {{ article.title }}
      </h3>

      <!-- Excerpt -->
      <p class="text-dark-600 text-sm md:text-base mb-4 line-clamp-3 leading-relaxed">
        {{ article.excerpt }}
      </p>

      <!-- Meta Information -->
      <div class="flex items-center justify-between text-sm text-dark-500">
        <!-- Date -->
        <time :datetime="article.publishedAt" class="flex items-center gap-1">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          {{ formattedDate }}
        </time>

        <!-- Views -->
        <div v-if="article.views" class="flex items-center gap-1">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
          </svg>
          {{ formatViews(article.views) }}
        </div>
      </div>

      <!-- Tags (Optional) -->
      <div v-if="showTags && article.tags && article.tags.length > 0" class="mt-4 flex flex-wrap gap-2">
        <span
          v-for="tag in article.tags.slice(0, 3)"
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
import { useRouter } from 'vue-router'
import { format, formatDistanceToNow } from 'date-fns'
import type { NewsArticle, NewsCategory } from '@/types/news'
import { NEWS_CATEGORIES } from '@/utils/constants'

// Props
interface Props {
  article: NewsArticle
  showTags?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  showTags: false,
})

// Router
const router = useRouter()

// Computed
const formattedDate = computed(() => {
  const date = new Date(props.article.publishedAt)
  const daysAgo = Math.floor((Date.now() - date.getTime()) / (1000 * 60 * 60 * 24))

  if (daysAgo < 7) {
    return formatDistanceToNow(date, { addSuffix: true })
  }
  return format(date, 'MMM d, yyyy')
})

const categoryClasses = computed(() => {
  const baseClasses = 'text-white font-semibold'

  switch (props.article.category) {
    case 'news':
      return `${baseClasses} bg-primary-500/90`
    case 'fanclub':
      return `${baseClasses} bg-accent-purple/90`
    case 'store':
      return `${baseClasses} bg-accent-pink/90`
    case 'event':
      return `${baseClasses} bg-accent-blue/90`
    case 'release':
      return `${baseClasses} bg-gradient-primary`
    default:
      return `${baseClasses} bg-dark-600/90`
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

const navigateToArticle = () => {
  router.push(`/news/${props.article.slug}`)
}
</script>

<style scoped>
/* Line clamp utilities (fallback for older browsers) */
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

/* Card hover animation */
.news-card {
  transform: translateY(0);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.news-card:hover {
  transform: translateY(-4px);
}

/* Ensure smooth animations */
@media (prefers-reduced-motion: reduce) {
  .news-card,
  .news-card img,
  .news-card * {
    transition-duration: 0.01ms !important;
  }
}
</style>
