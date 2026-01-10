<template>
  <section class="featured-member relative rounded-2xl overflow-hidden shadow-2xl mb-12">
    <!-- Background with Parallax -->
    <div class="absolute inset-0">
      <img
        :src="member.coverImage || member.photo"
        :alt="member.name.english"
        class="w-full h-full object-cover"
      />
      <!-- Gradient Overlay -->
      <div class="absolute inset-0 bg-black/80"></div>
      <!-- Color Accent Overlay -->
      <div
        class="absolute inset-0 opacity-20"
        :style="{ backgroundColor: member.color }"
      ></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 container mx-auto px-6 md:px-8 py-12 md:py-16">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
        <!-- Left: Member Info -->
        <div>
          <!-- Featured Badge -->
          <div class="inline-flex items-center gap-2 px-4 py-2 bg-primary-400 text-neutral-900 rounded-full font-bold uppercase text-sm mb-4 animate-pulse">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
            Featured Member
          </div>

          <!-- Position Badge -->
          <div class="mb-3">
            <span
              class="inline-block px-4 py-2 rounded-full text-sm font-bold uppercase"
              :style="{ backgroundColor: member.color || '#00ff9f', color: '#000' }"
            >
              {{ member.position }}
            </span>
          </div>

          <!-- Name -->
          <h2 class="font-outfit font-bold text-4xl md:text-5xl lg:text-6xl text-white mb-2 leading-tight">
            {{ member.name.english }}
          </h2>

          <!-- Native Name -->
          <p v-if="member.name.native" class="text-2xl md:text-3xl text-white/90 mb-6">
            {{ member.name.native }}
          </p>

          <!-- Team & Generation -->
          <div class="flex flex-wrap items-center gap-3 mb-6">
            <span class="flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-sm rounded-lg text-white">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
              </svg>
              {{ member.team }}
            </span>
            <span class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-lg text-white">
              {{ member.generation }} Generation
            </span>
            <span class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-lg text-white">
              {{ member.hometown }}
            </span>
          </div>

          <!-- Bio -->
          <p class="text-white/90 text-lg mb-8 leading-relaxed">
            {{ member.bio }}
          </p>

          <!-- Stats -->
          <div v-if="member.stats" class="grid grid-cols-3 gap-4 mb-8">
            <div v-if="member.stats.centerPositions" class="text-center">
              <div class="text-3xl font-bold text-white mb-1">{{ member.stats.centerPositions }}</div>
              <div class="text-white/70 text-sm uppercase tracking-wide">Center</div>
            </div>
            <div v-if="member.stats.performances" class="text-center">
              <div class="text-3xl font-bold text-white mb-1">{{ member.stats.performances }}</div>
              <div class="text-white/70 text-sm uppercase tracking-wide">Shows</div>
            </div>
            <div v-if="member.stats.fanMeetings" class="text-center">
              <div class="text-3xl font-bold text-white mb-1">{{ member.stats.fanMeetings }}</div>
              <div class="text-white/70 text-sm uppercase tracking-wide">Events</div>
            </div>
          </div>

          <!-- CTA Button -->
          <button
            @click="navigateToMember"
            class="btn-featured group px-8 py-4 bg-primary-500 text-white rounded-full font-outfit font-semibold uppercase text-sm tracking-wide hover:scale-105 transition-all duration-300 hover:shadow-neon flex items-center gap-2"
          >
            View Full Profile
            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
          </button>
        </div>

        <!-- Right: Member Photo (Desktop Only) -->
        <div class="hidden lg:block">
          <div class="relative">
            <!-- Photo -->
            <img
              :src="member.photo"
              :alt="member.name.english"
              class="w-full max-w-md mx-auto rounded-2xl shadow-2xl"
              :style="{ boxShadow: `0 20px 60px ${member.color}60` }"
            />
            <!-- Decorative Element -->
            <div
              class="absolute -bottom-10 -right-10 w-64 h-64 rounded-full blur-3xl opacity-30"
              :style="{ backgroundColor: member.color || '#00ff9f' }"
            ></div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'
import type { Member } from '@/types/member'

// Props
interface Props {
  member: Member
}

const props = defineProps<Props>()

// Router
const router = useRouter()

// Methods
const navigateToMember = () => {
  router.push(`/members/${props.member.id}`)
}
</script>

<style scoped>
.featured-member {
  min-height: 400px;
}

@media (min-width: 768px) {
  .featured-member {
    min-height: 500px;
  }
}

@media (min-width: 1024px) {
  .featured-member {
    min-height: 600px;
  }
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
  background: #288820;
  transition: left 0.3s ease;
  z-index: -1;
}

.btn-featured:hover::before {
  left: 0;
}
</style>
