<template>
  <div>
    <!-- Hero Section -->
    <section ref="heroRef" class="relative h-screen flex items-center justify-center overflow-hidden">
      <!-- Video background placeholder (jade gradient fallback) -->
      <div class="absolute inset-0 bg-jade-gradient">
        <div class="absolute inset-0 bg-hero-overlay"></div>
      </div>

      <!-- Sakura particles (CSS-based) -->
      <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div
          v-for="i in 12"
          :key="i"
          class="sakura-petal absolute"
          :style="{
            left: `${Math.random() * 100}%`,
            animationDelay: `${Math.random() * 10}s`,
            animationDuration: `${8 + Math.random() * 6}s`,
            fontSize: `${10 + Math.random() * 14}px`,
            opacity: 0.4 + Math.random() * 0.4,
          }"
        >
          &#x1F338;
        </div>
      </div>

      <!-- Noren arch frame -->
      <div class="absolute inset-x-0 bottom-0 h-32 bg-cream-50" style="clip-path: ellipse(55% 100% at 50% 100%)"></div>

      <!-- Hero content -->
      <div ref="heroContentRef" class="relative z-10 text-center px-6">
        <h1 class="text-6xl md:text-8xl font-heading font-black text-white mb-4 tracking-tight">
          KLP48
        </h1>
        <p class="text-lg md:text-xl text-white/80 font-body mb-2">
          {{ $t('home.heroSubtitle') }}
        </p>
        <p class="text-sm text-white/50 font-jp">
          {{ $t('home.heroSubtitleJp') }}
        </p>

        <!-- CTA buttons -->
        <div class="flex items-center justify-center gap-4 mt-8">
          <router-link
            to="/members"
            class="px-8 py-3 bg-white text-jade-700 font-semibold rounded-full hover:bg-jade-50 transition-all duration-300 shadow-lg hover:shadow-jade-glow"
          >
            {{ $t('home.meetMembers') }}
          </router-link>
          <router-link
            to="/news"
            class="px-8 py-3 border-2 border-white/50 text-white font-semibold rounded-full hover:bg-white/10 transition-all duration-300"
          >
            {{ $t('home.latestNews') }}
          </router-link>
        </div>
      </div>
    </section>

    <!-- Announcement Ticker -->
    <section v-if="featuredNews.length" class="border-y-2 border-songket py-3 bg-cream-50">
      <div class="overflow-hidden">
        <div class="flex animate-ticker whitespace-nowrap">
          <template v-for="_ in 2" :key="_">
            <span
              v-for="article in featuredNews"
              :key="article.id + _"
              class="mx-8 text-sm text-charcoal-700 flex-shrink-0 cursor-pointer hover:text-jade-600 transition-colors"
            >
              <span class="text-gold-500 font-semibold mr-2">{{ formatDate(article.date, 'MMM d') }}</span>
              {{ article.title }}
            </span>
          </template>
        </div>
      </div>
    </section>

    <!-- News Preview Section -->
    <section ref="newsSectionRef" class="py-20 bg-cream-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-10">
          <div>
            <h2 class="text-3xl md:text-4xl font-heading font-bold text-charcoal-800">{{ $t('home.newsHeading') }}</h2>
            <p class="text-charcoal-500 mt-2">{{ $t('home.newsSubtitle') }}</p>
          </div>
          <router-link to="/news" class="text-jade-600 hover:text-jade-700 font-medium flex items-center gap-1 group">
            {{ $t('common.seeMore') }}
            <span class="inline-block transition-transform group-hover:translate-x-1">&rarr;</span>
          </router-link>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <router-link
            v-for="article in latestNews"
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
            <p class="text-sm text-charcoal-500 line-clamp-2 mb-3">{{ article.excerpt }}</p>
            <time class="text-xs text-charcoal-400">{{ formatDate(article.date) }}</time>
          </router-link>
        </div>
      </div>
    </section>

    <!-- Divider -->
    <div class="divider-batik max-w-7xl mx-auto"></div>

    <!-- Members Preview Section -->
    <section ref="membersSectionRef" class="py-20 bg-cream-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-10">
          <div>
            <h2 class="text-3xl md:text-4xl font-heading font-bold text-charcoal-800">{{ $t('home.membersHeading') }}</h2>
            <p class="text-charcoal-500 mt-2">{{ $t('home.membersSubtitle') }}</p>
          </div>
          <router-link to="/members" class="text-jade-600 hover:text-jade-700 font-medium flex items-center gap-1 group">
            {{ $t('common.viewAll') }}
            <span class="inline-block transition-transform group-hover:translate-x-1">&rarr;</span>
          </router-link>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
          <router-link
            v-for="member in activeMembers.slice(0, 6)"
            :key="member.id"
            :to="`/members/${member.id}`"
            class="group text-center"
          >
            <div
              class="relative w-full aspect-square rounded-2xl overflow-hidden mb-3 bg-cream-200 shadow-card group-hover:shadow-card-hover transition-all duration-300"
              :style="{ borderColor: member.color, borderWidth: '2px', borderStyle: 'solid' }"
            >
              <!-- Placeholder avatar -->
              <div class="absolute inset-0 flex items-center justify-center"
                   :style="{ backgroundColor: member.color + '20' }">
                <span class="text-4xl font-heading font-bold" :style="{ color: member.color }">
                  {{ member.name.english.charAt(0) }}
                </span>
              </div>
            </div>
            <p class="font-medium text-charcoal-800 text-sm group-hover:text-jade-600 transition-colors">
              {{ member.name.english }}
            </p>
            <p class="text-xs text-charcoal-400">{{ member.generation }} {{ $t('common.gen') }}</p>
          </router-link>
        </div>
      </div>
    </section>

    <!-- Release Preview Section -->
    <section ref="releaseSectionRef" class="py-20 bg-charcoal-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-10">
          <div>
            <h2 class="text-3xl md:text-4xl font-heading font-bold text-white">{{ $t('home.discographyHeading') }}</h2>
            <p class="text-white/50 mt-2">{{ $t('home.discographySubtitle') }}</p>
          </div>
          <router-link to="/releases" class="text-jade-400 hover:text-jade-300 font-medium flex items-center gap-1 group">
            {{ $t('common.viewAll') }}
            <span class="inline-block transition-transform group-hover:translate-x-1">&rarr;</span>
          </router-link>
        </div>

        <div class="flex gap-6 overflow-x-auto pb-4 scrollbar-hide">
          <router-link
            v-for="release in releases"
            :key="release.id"
            to="/releases"
            class="flex-shrink-0 w-56 group"
          >
            <div class="w-56 h-56 rounded-xl overflow-hidden mb-3 bg-charcoal-700 shadow-lg group-hover:shadow-jade-glow transition-all duration-300">
              <img
                v-if="release.coverImage"
                :src="release.coverImage"
                :alt="release.title"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center bg-jade-gradient opacity-60 group-hover:opacity-80 transition-opacity">
                <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z"/>
                </svg>
              </div>
            </div>
            <h3 class="font-heading font-semibold text-white group-hover:text-jade-400 transition-colors">
              {{ release.title }}
            </h3>
            <p class="text-sm text-white/40">{{ release.type.toUpperCase() }} &middot; {{ formatDate(release.releaseDate, 'yyyy.MM.dd') }}</p>
          </router-link>
        </div>
      </div>
    </section>

    <!-- Sponsors Section -->
    <section ref="sponsorsSectionRef" class="py-20 bg-cream-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl md:text-4xl font-heading font-bold text-charcoal-800">{{ $t('home.sponsorsHeading') }}</h2>
          <p class="text-charcoal-500 mt-2">{{ $t('home.sponsorsSubtitle') }}</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-6">
          <div
            v-for="sponsor in SPONSORS"
            :key="sponsor"
            class="flex flex-col items-center gap-3 group"
          >
            <!-- Logo placeholder — replace src with actual logo path when ready -->
            <div class="w-full aspect-video rounded-xl bg-white border border-charcoal-100 shadow-card flex items-center justify-center group-hover:shadow-card-hover group-hover:border-forest-500/30 transition-all duration-300">
              <svg class="w-8 h-8 text-charcoal-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
            </div>
            <span class="text-xs font-medium text-charcoal-500 text-center group-hover:text-charcoal-800 transition-colors">{{ sponsor }}</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Sister Groups Section -->
    <section ref="sisterGroupsSectionRef" class="py-20 bg-charcoal-900">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
          <p class="text-white/40">{{ $t('footer.sisterGroups') }}</p>
        </div>
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-4">
          <a
            v-for="group in SISTER_GROUPS"
            :key="group.name"
            :href="group.url || undefined"
            :target="group.url ? '_blank' : undefined"
            rel="noopener noreferrer"
            class="flex flex-col items-center gap-2 group"
            :class="!group.url ? 'pointer-events-none' : ''"
          >
            <div class="w-full aspect-video rounded-lg bg-white/5 border border-white/10 flex items-center justify-center group-hover:border-jade-500/40 group-hover:bg-white/10 transition-all duration-200">
              <span class="text-xs font-heading font-bold text-white/30 group-hover:text-jade-400 transition-colors text-center leading-tight px-1">
                {{ group.name }}
              </span>
            </div>
          </a>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import { apiFetch } from '@/composables/useApi'
import { formatDate, getCategoryColor } from '@/utils/helpers'
import { SPONSORS, SISTER_GROUPS } from '@/utils/constants'
import type { NewsArticle } from '@/types/news'
import type { Member } from '@/types/member'
import type { Release } from '@/types/release'

const news = ref<NewsArticle[]>([])
const members = ref<Member[]>([])
const releases = ref<Release[]>([])

const heroContentRef = ref<HTMLElement | null>(null)
const newsSectionRef = ref<HTMLElement | null>(null)
const membersSectionRef = ref<HTMLElement | null>(null)
const releaseSectionRef = ref<HTMLElement | null>(null)
const sisterGroupsSectionRef = ref<HTMLElement | null>(null)
const sponsorsSectionRef = ref<HTMLElement | null>(null)

const latestNews = computed(() => news.value.slice(0, 3))
const featuredNews = computed(() => news.value.filter(n => n.featured))
const activeMembers = computed(() => members.value.filter(m => m.status === 'active'))

onMounted(async () => {
  // Fetch data from API
  const [newsRes, membersRes, releasesRes] = await Promise.all([
    apiFetch<NewsArticle[]>('/news'),
    apiFetch<Member[]>('/members'),
    apiFetch<Release[]>('/releases'),
  ])
  news.value = newsRes
  members.value = membersRes
  releases.value = releasesRes

  // Hero content animation
  if (heroContentRef.value) {
    gsap.from(heroContentRef.value.children, {
      y: 40,
      opacity: 0,
      duration: 1,
      stagger: 0.15,
      ease: 'power3.out',
      delay: 0.3,
    })
  }

  // Section scroll reveals
  const sections = [newsSectionRef, membersSectionRef, releaseSectionRef, sisterGroupsSectionRef, sponsorsSectionRef]
  sections.forEach(sectionRef => {
    if (!sectionRef.value) return
    gsap.from(sectionRef.value.children, {
      y: 30,
      opacity: 0,
      duration: 0.8,
      stagger: 0.1,
      ease: 'power2.out',
      scrollTrigger: {
        trigger: sectionRef.value,
        start: 'top 80%',
      },
    })
  })
})
</script>

<style scoped>
.sakura-petal {
  animation: sakuraFall linear infinite;
  will-change: transform;
}

@keyframes sakuraFall {
  0% {
    transform: translateY(-10vh) rotate(0deg) translateX(0);
    opacity: 0;
  }
  10% {
    opacity: 1;
  }
  90% {
    opacity: 0.6;
  }
  100% {
    transform: translateY(110vh) rotate(720deg) translateX(100px);
    opacity: 0;
  }
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
