<template>
  <div class="pt-24 pb-16 min-h-screen bg-cream-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
      <template v-if="article">
        <!-- Back link -->
        <router-link to="/news" class="inline-flex items-center gap-1 text-sm text-jade-600 hover:text-jade-700 mb-8 group">
          <span class="transition-transform group-hover:-translate-x-1">&larr;</span>
          {{ $t('news.backToNews') }}
        </router-link>

        <!-- Article -->
        <article ref="articleRef">
          <span class="inline-block px-3 py-1 text-xs font-medium rounded-full mb-4"
                :class="getCategoryColor(article.category)">
            {{ article.category }}
          </span>
          <h1 class="text-3xl md:text-4xl font-heading font-bold text-charcoal-800 mb-4">
            {{ article.title }}
          </h1>
          <time class="text-sm text-charcoal-400 block mb-8">{{ formatDate(article.date, 'MMMM d, yyyy') }}</time>

          <div class="prose prose-lg max-w-none">
            <p class="text-charcoal-600 leading-relaxed">{{ article.excerpt }}</p>
            <p v-if="article.content" class="text-charcoal-600 leading-relaxed mt-4">{{ article.content }}</p>
          </div>
        </article>
      </template>

      <!-- Loading state -->
      <div v-else-if="loading" class="text-center py-20">
        <div class="inline-block w-8 h-8 border-4 border-jade-200 border-t-jade-600 rounded-full animate-spin"></div>
      </div>

      <!-- Not found -->
      <div v-else class="text-center py-20">
        <h2 class="text-2xl font-heading font-bold text-charcoal-800 mb-2">{{ $t('news.notFound') }}</h2>
        <p class="text-charcoal-500 mb-6">{{ $t('news.notFoundDesc') }}</p>
        <router-link to="/news" class="text-jade-600 hover:text-jade-700 font-medium">
          &larr; {{ $t('news.backToNews') }}
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { gsap } from 'gsap'
import { apiFetch } from '@/composables/useApi'
import { formatDate, getCategoryColor } from '@/utils/helpers'
import type { NewsArticle } from '@/types/news'

const route = useRoute()
const article = ref<NewsArticle | null>(null)
const loading = ref(true)
const articleRef = ref<HTMLElement | null>(null)

async function fetchArticle() {
  loading.value = true
  const slug = route.params.slug as string
  try {
    article.value = await apiFetch<NewsArticle>(`/news/${slug}`)
  } catch {
    article.value = null
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await fetchArticle()

  if (articleRef.value) {
    gsap.from(articleRef.value.children, {
      y: 20, opacity: 0, duration: 0.6, stagger: 0.1, ease: 'power2.out',
    })
  }
})

watch(() => route.params.slug, fetchArticle)
</script>
