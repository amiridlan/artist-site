<template>
  <div class="pt-24 pb-16 min-h-screen bg-cream-50">
    <!-- Hero banner -->
    <section ref="bannerRef" class="relative h-72 md:h-96 bg-jade-gradient overflow-hidden mb-16">
      <div class="absolute inset-0 bg-hero-overlay"></div>
      <div class="relative h-full flex items-center justify-center text-center px-6">
        <div>
          <h1 class="text-4xl md:text-6xl font-heading font-black text-white mb-3">{{ $t('about.title') }}</h1>
          <p class="text-white/70 text-lg">{{ $t('about.subtitle') }}</p>
        </div>
      </div>
      <!-- Noren arch bottom -->
      <div class="absolute inset-x-0 bottom-0 h-20 bg-cream-50" style="clip-path: ellipse(55% 100% at 50% 100%)"></div>
    </section>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Story -->
      <section ref="storyRef" class="mb-16">
        <h2 class="text-2xl font-heading font-bold text-charcoal-800 mb-6">{{ $t('about.ourStory') }}</h2>
        <div class="prose prose-lg max-w-none text-charcoal-600 leading-relaxed space-y-4">
          <p>{{ $t('about.storyP1') }}</p>
          <p>{{ $t('about.storyP2') }}</p>
          <p>{{ $t('about.storyP3') }}</p>
        </div>
      </section>

      <!-- Divider -->
      <div class="divider-batik"></div>

      <!-- Milestones -->
      <section ref="milestonesRef" class="mb-16">
        <h2 class="text-2xl font-heading font-bold text-charcoal-800 mb-8">{{ $t('about.milestones') }}</h2>

        <div class="space-y-6">
          <div v-for="milestone in milestones" :key="milestone.date"
               class="flex gap-6 items-start">
            <div class="flex-shrink-0 w-24 text-right">
              <p class="text-sm font-semibold text-jade-600">{{ milestone.date }}</p>
            </div>
            <div class="w-3 h-3 rounded-full bg-jade-500 mt-1.5 flex-shrink-0"></div>
            <div>
              <h3 class="font-heading font-semibold text-charcoal-800">{{ milestone.title }}</h3>
              <p class="text-sm text-charcoal-500 mt-1">{{ milestone.description }}</p>
            </div>
          </div>
        </div>
      </section>

      <!-- 48 Group Connection -->
      <section ref="connectionRef" class="mb-16">
        <h2 class="text-2xl font-heading font-bold text-charcoal-800 mb-6">{{ $t('about.groupFamily') }}</h2>
        <p class="text-charcoal-600 leading-relaxed mb-6">
          {{ $t('about.groupFamilyDesc') }}
        </p>

        <div class="flex flex-wrap gap-3">
          <span v-for="group in sisterGroups" :key="group"
                class="px-4 py-2 bg-white rounded-full shadow-card text-sm font-medium text-charcoal-700 hover:shadow-card-hover hover:text-jade-600 transition-all duration-200">
            {{ group }}
          </span>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

const { t } = useI18n()

const bannerRef = ref<HTMLElement | null>(null)
const storyRef = ref<HTMLElement | null>(null)
const milestonesRef = ref<HTMLElement | null>(null)
const connectionRef = ref<HTMLElement | null>(null)

const milestones = computed(() => [
  { date: '2024.08.16', title: t('about.milestone1Title'), description: t('about.milestone1Desc') },
  { date: '2024.09', title: t('about.milestone2Title'), description: t('about.milestone2Desc') },
  { date: '2025.05.30', title: t('about.milestone3Title'), description: t('about.milestone3Desc') },
  { date: '2025.06.13', title: t('about.milestone4Title'), description: t('about.milestone4Desc') },
  { date: '2025.08.16', title: t('about.milestone5Title'), description: t('about.milestone5Desc') },
  { date: '2025.09.12', title: t('about.milestone6Title'), description: t('about.milestone6Desc') },
])

const sisterGroups = [
  'AKB48', 'SKE48', 'NMB48', 'HKT48', 'NGT48', 'STU48',
  'JKT48', 'BNK48', 'MNL48', 'AKB48 Team SH', 'TPE48', 'CGM48', 'KLP48',
]

onMounted(() => {
  const sections = [storyRef, milestonesRef, connectionRef]
  sections.forEach(sectionRef => {
    if (!sectionRef.value) return
    gsap.from(sectionRef.value.children, {
      y: 25,
      opacity: 0,
      duration: 0.7,
      stagger: 0.1,
      ease: 'power2.out',
      scrollTrigger: {
        trigger: sectionRef.value,
        start: 'top 85%',
      },
    })
  })
})
</script>
