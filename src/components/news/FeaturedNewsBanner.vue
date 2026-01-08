<template>
  <section class="featured-news-banner relative rounded-2xl overflow-hidden shadow-2xl mb-12">
    <!-- Background Image with Parallax -->
    <div class="absolute inset-0">
      <img
        :src="featuredArticle.image"
        :alt="featuredArticle.title"
        class="w-full h-full object-cover"
      />
      <!-- Gradient Overlay -->
      <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/60 to-transparent"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 container mx-auto px-6 md:px-8 py-12 md:py-16 lg:py-20">
      <div class="max-w-2xl">
        <!-- Featured Badge -->
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-accent-yellow text-dark-900 rounded-full font-bold uppercase text-sm mb-4 md:mb-6 animate-pulse">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
          </svg>
          Featured Story
        </div>

        <!-- Category -->
        <div class="mb-3">
          <span
            :class="categoryClasses"
            class="inline-block px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide"
          >
            {{ getCategoryLabel(featuredArticle.category) }}
          </span>
        </div>

        <!-- Title -->
        <h2 class="font-outfit font-bold text-3xl md:text-4xl lg:text-5xl text-white mb-4 md:mb-6 leading-tight">
          {{ featuredArticle.title }}
        </h2>

        <!-- Excerpt -->
        <p class="text-white/90 text-base md:text-lg mb-6 md:mb-8 leading-relaxed line-clamp-3">
          {{ featuredArticle.excerpt }}
        </p>

        <!-- Meta & CTA -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-6">
          <!-- Meta Info -->
          <div class="flex items-center gap-4 text-white/80 text-sm">
            <time :datetime="featuredArticle.publishedAt" class="flex items-center gap-1">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              {{ formattedDate }}
            </time>
            <span v-if="featuredArticle.readTime" class="flex items-center gap-1">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              {{ featuredArticle.readTime }} min read
            </span>
          </div>

          <!-- Read More Button -->
          <button
            @click="navigateToArticle"
            class="btn-featured group px-6 py-3 bg-gradient-primary text-white rounded-full font-outfit font-semibold uppercase text-sm tracking-wide hover:scale-105 transition-all duration-300 hover:shadow-neon flex items-center gap-2"
          >
            Read Full Story
            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Decorative Element -->
    <div class="absolute bottom-0 right-0 w-64 h-64 bg-gradient-primary opacity-20 blur-3xl rounded-full transform translate-x-1/2 translate-y-1/2"></div>
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { format, formatDistanceToNow } from 'date-fns'
import type { NewsArticle, NewsCategory } from '@/types/news'
import { NEWS_CATEGORIES } from '@/utils/constants'

// Props
interface Props {
  featuredArticle: NewsArticle
}

const props = defineProps<Props>()

// Router
const router = useRouter()

// Computed
const formattedDate = computed(() => {
  const date = new Date(props.featuredArticle.publishedAt)
  const daysAgo = Math.floor((Date.now() - date.getTime()) / (1000 * 60 * 60 * 24))

  if (daysAgo < 7) {
    return formatDistanceToNow(date, { addSuffix: true })
  }
  return format(date, 'MMM d, yyyy')
})

const categoryClasses = computed(() => {
  const baseClasses = 'text-white'

  switch (props.featuredArticle.category) {
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

const navigateToArticle = () => {
  router.push(`/news/${props.featuredArticle.slug}`)
}
</script>

<style scoped>
.featured-news-banner {
  min-height: 400px;
}

@media (min-width: 768px) {
  .featured-news-banner {
    min-height: 500px;
  }
}

@media (min-width: 1024px) {
  .featured-news-banner {
    min-height: 600px;
  }
}

/* Line clamp */
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Button hover effect */
.btn-featured {
  position: relative;
  overflow: hidden;
}

.btn-featured::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #b026ff 0%, #00ff9f 100%);
  transition: left 0.3s ease;
  z-index: -1;
}

.btn-featured:hover::before {
  left: 0;
}
</style>
