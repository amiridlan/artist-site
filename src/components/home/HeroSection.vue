<template>
  <section
    ref="heroRef"
    class="relative min-h-screen flex items-center justify-center overflow-hidden"
  >
    <!-- Video Background -->
    <div
      class="absolute inset-0 z-0"
      :style="{ transform: `translateY(${parallaxOffset}px)` }"
    >
      <!-- Video element -->
      <video
        v-if="videoSrc"
        ref="videoRef"
        class="absolute inset-0 w-full h-full object-cover"
        autoplay
        loop
        muted
        playsinline
        :poster="fallbackImage"
        @loadeddata="onVideoLoaded"
      >
        <source :src="videoSrc" type="video/mp4" />
        Your browser does not support the video tag.
      </video>

      <!-- Fallback Image -->
      <img
        v-else
        :src="fallbackImage"
        alt="KLP48 Hero Background"
        class="absolute inset-0 w-full h-full object-cover"
      />

      <!-- Overlay -->
      <div class="absolute inset-0 bg-black/50"></div>
    </div>

    <!-- Hero Content -->
    <div class="relative z-10 container mx-auto px-4 text-center">
      <!-- Animated Tagline -->
      <h1 class="hero-tagline mb-8 md:mb-12">
        <span
          v-for="(word, index) in taglineWords"
          :key="index"
          :style="{ animationDelay: `${index * 150}ms` }"
          class="inline-block opacity-0 animate-slide-up mx-1 md:mx-2"
        >
          {{ word }}
        </span>
      </h1>

      <!-- Subtitle -->
      <p
        class="text-lg md:text-xl lg:text-2xl text-white/90 mb-10 md:mb-14 max-w-3xl mx-auto animate-fade-in"
        style="animation-delay: 800ms"
      >
        {{ subtitle }}
      </p>

      <!-- CTA Buttons -->
      <div
        class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-fade-in"
        style="animation-delay: 1000ms"
      >
        <router-link
          to="/fanclub"
          class="btn-primary px-8 py-4 rounded-full font-outfit font-semibold text-lg uppercase tracking-wide transition-all duration-300 hover:scale-105 hover:shadow-neon"
        >
          Join Fanclub
        </router-link>
        <router-link
          to="/videos"
          class="btn-secondary px-8 py-4 rounded-full font-outfit font-semibold text-lg uppercase tracking-wide transition-all duration-300 hover:scale-105"
        >
          Watch Latest MV
        </router-link>
      </div>
    </div>

    <!-- Scroll Indicator -->
    <div
      class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-10 animate-bounce-slow"
    >
      <button
        @click="scrollToContent"
        class="flex flex-col items-center text-white/80 hover:text-white transition-colors"
        aria-label="Scroll to content"
      >
        <span class="text-sm uppercase tracking-widest mb-2 font-medium">Scroll</span>
        <svg
          class="w-6 h-6"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M19 14l-7 7m0 0l-7-7m7 7V3"
          />
        </svg>
      </button>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'

// Props
interface Props {
  videoSrc?: string
  fallbackImage?: string
  tagline?: string
  subtitle?: string
}

const props = withDefaults(defineProps<Props>(), {
  videoSrc: '', // TODO: Add video URL when available
  fallbackImage: 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?w=1920&h=1080&fit=crop',
  tagline: 'Malaysia\'s Premier Idol Group',
  subtitle: 'Experience the energy, passion, and talent of KLP48',
})

// Refs
const heroRef = ref<HTMLElement | null>(null)
const videoRef = ref<HTMLVideoElement | null>(null)
const parallaxOffset = ref(0)
const isVideoLoaded = ref(false)

// Computed
const taglineWords = computed(() => props.tagline.split(' '))

// Methods
const handleScroll = () => {
  if (heroRef.value) {
    const scrollY = window.scrollY
    const heroHeight = heroRef.value.offsetHeight

    // Only apply parallax within hero section
    if (scrollY < heroHeight) {
      parallaxOffset.value = scrollY * 0.5
    }
  }
}

const scrollToContent = () => {
  const heroHeight = heroRef.value?.offsetHeight || window.innerHeight
  window.scrollTo({
    top: heroHeight,
    behavior: 'smooth',
  })
}

const onVideoLoaded = () => {
  isVideoLoaded.value = true
  console.log('Hero video loaded successfully')
}

// Lifecycle
onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true })

  // Ensure video plays on mobile
  if (videoRef.value) {
    videoRef.value.play().catch((error) => {
      console.warn('Video autoplay failed:', error)
    })
  }
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
.hero-tagline {
  font-family: 'ITC Avant Garde Gothic Std', 'Avant Garde', 'Century Gothic', sans-serif;
  @apply font-bold text-4xl sm:text-5xl md:text-6xl lg:text-7xl xl:text-8xl leading-tight;
  color: #288800;
}

/* Primary Button */
.btn-primary {
  background: #288800;
  color: white;
  transition: background-color 0.3s ease;
}

.btn-primary:hover {
  background: #237700;
}

/* Secondary Button */
.btn-secondary {
  background: transparent;
  color: white;
  border: 2px solid #288800;
  transition: background-color 0.3s ease, border-color 0.3s ease;
}

.btn-secondary:hover {
  background: #288800;
  border-color: #288800;
}

/* Animation overrides to ensure visibility */
@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.animate-slide-up {
  animation: slideUp 0.8s ease-out forwards;
}

.animate-fade-in {
  animation: fadeIn 1s ease-out forwards;
  opacity: 0;
}
</style>
