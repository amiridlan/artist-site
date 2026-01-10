<template>
  <article
    class="member-card group relative cursor-pointer"
    @click="navigateToMember"
    :style="{ '--member-color': member.color || '#00ff9f' }"
  >
    <!-- Card Inner Container with 3D Transform -->
    <div class="card-inner">
      <!-- Front of Card -->
      <div class="card-front">
        <!-- Photo Container -->
        <div class="relative overflow-hidden rounded-xl aspect-[3/4]">
          <img
            :src="member.photo"
            :alt="member.name.english"
            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
            loading="lazy"
          />

          <!-- Gradient Overlay -->
          <div class="absolute inset-0 bg-black/50 opacity-60 group-hover:opacity-80 transition-opacity duration-300"></div>

          <!-- Status Badge -->
          <div class="absolute top-3 left-3">
            <span
              :class="statusClasses"
              class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide backdrop-blur-sm"
            >
              {{ statusLabel }}
            </span>
          </div>

          <!-- Featured Badge -->
          <div v-if="member.featured" class="absolute top-3 right-3">
            <span class="px-2 py-1 bg-primary-400 text-neutral-900 rounded-full text-xs font-bold uppercase flex items-center gap-1">
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
            </span>
          </div>

          <!-- Position Badge -->
          <div v-if="member.position !== 'Regular'" class="absolute top-12 left-3">
            <span
              class="px-2 py-1 rounded-full text-xs font-bold uppercase"
              :style="{ backgroundColor: member.color || '#00ff9f', color: '#000' }"
            >
              {{ member.position }}
            </span>
          </div>

          <!-- Bottom Info -->
          <div class="absolute bottom-0 left-0 right-0 p-4">
            <!-- Name -->
            <h3 class="font-outfit font-bold text-xl text-white mb-1 drop-shadow-lg">
              {{ member.name.english }}
            </h3>

            <!-- Native Name -->
            <p v-if="member.name.native" class="text-white/90 text-sm mb-2 drop-shadow">
              {{ member.name.native }}
            </p>

            <!-- Team & Generation -->
            <div class="flex items-center gap-2 text-white/80 text-xs mb-2">
              <span class="flex items-center gap-1 px-2 py-1 bg-white/20 backdrop-blur-sm rounded">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                </svg>
                {{ member.team }}
              </span>
              <span class="flex items-center gap-1 px-2 py-1 bg-white/20 backdrop-blur-sm rounded">
                {{ member.generation }} Gen
              </span>
            </div>

            <!-- Stats (on hover) -->
            <div class="flex items-center gap-3 text-white/70 text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-300">
              <span v-if="member.stats?.centerPositions" class="flex items-center gap-1">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                {{ member.stats.centerPositions }}
              </span>
              <span v-if="member.stats?.performances" class="flex items-center gap-1">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                </svg>
                {{ member.stats.performances }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Accent Color Border (on hover) -->
    <div
      class="absolute inset-0 rounded-xl pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity duration-300"
      :style="{
        boxShadow: `0 0 0 2px ${member.color || '#00ff9f'}, 0 0 20px ${member.color || '#00ff9f'}40`
      }"
    ></div>
  </article>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import type { Member } from '@/types/member'

// Props
interface Props {
  member: Member
}

const props = defineProps<Props>()

// Router
const router = useRouter()

// Computed
const statusLabel = computed(() => {
  switch (props.member.status) {
    case 'active':
      return 'Active'
    case 'graduated':
      return 'Graduated'
    case 'on-hiatus':
      return 'On Hiatus'
    default:
      return props.member.status
  }
})

const statusClasses = computed(() => {
  const baseClasses = 'text-white'

  switch (props.member.status) {
    case 'active':
      return `${baseClasses} bg-green-500/90`
    case 'graduated':
      return `${baseClasses} bg-blue-500/90`
    case 'on-hiatus':
      return `${baseClasses} bg-orange-500/90`
    default:
      return `${baseClasses} bg-neutral-600/90`
  }
})

// Methods
const navigateToMember = () => {
  router.push(`/members/${props.member.id}`)
}
</script>

<style scoped>
/* Card Container */
.member-card {
  perspective: 1000px;
  transform-style: preserve-3d;
}

/* Card Inner with 3D Transform */
.card-inner {
  position: relative;
  width: 100%;
  transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
  transform-style: preserve-3d;
}

.member-card:hover .card-inner {
  transform: translateY(-8px) rotateX(2deg);
}

/* Card Front */
.card-front {
  backface-visibility: hidden;
  transform: translateZ(0);
}

/* 3D Shadow Effect */
.member-card::before {
  content: '';
  position: absolute;
  bottom: -10px;
  left: 10%;
  right: 10%;
  height: 20px;
  /* removed gradient */
  opacity: 0;
  transition: opacity 0.3s ease, transform 0.3s ease;
  transform: translateY(0);
  z-index: -1;
}

.member-card:hover::before {
  opacity: 1;
  transform: translateY(5px);
}

/* Shine Effect */
.card-front::after {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
/* removed gradient */
/* removed gradient */
/* removed gradient */
/* removed gradient */
/* removed gradient */
/* removed gradient */
  transform: translateX(-100%) translateY(-100%) rotate(45deg);
  transition: transform 0.6s;
}

.member-card:hover .card-front::after {
  transform: translateX(100%) translateY(100%) rotate(45deg);
}

/* Reduced Motion Support */
@media (prefers-reduced-motion: reduce) {
  .member-card:hover .card-inner {
    transform: translateY(-4px);
  }

  .card-inner,
  .card-front::after,
  .member-card::before {
    transition-duration: 0.01ms !important;
  }
}
</style>
