<template>
  <DefaultLayout>
    <article v-if="member" class="member-detail-page pt-20 pb-16">
      <!-- Hero Section with Cover Image -->
      <div class="relative h-[60vh] md:h-[70vh] mb-12 overflow-hidden">
        <img
          :src="member.coverImage || member.photo"
          :alt="member.name.english"
          class="w-full h-full object-cover"
        />
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent"></div>

        <!-- Member Info Overlay -->
        <div class="absolute bottom-0 left-0 right-0 p-6 md:p-12">
          <div class="container mx-auto max-w-6xl">
            <div class="flex flex-col md:flex-row items-start md:items-end gap-6">
              <!-- Profile Photo -->
              <div class="relative">
                <img
                  :src="member.photo"
                  :alt="member.name.english"
                  class="w-40 h-40 md:w-48 md:h-48 rounded-2xl object-cover shadow-2xl border-4 border-white"
                  :style="{ boxShadow: `0 10px 40px ${member.color}80` }"
                />
                <!-- Status Badge -->
                <span
                  :class="statusClasses"
                  class="absolute top-3 right-3 px-3 py-1 rounded-full text-xs font-bold uppercase"
                >
                  {{ statusLabel }}
                </span>
              </div>

              <!-- Name & Details -->
              <div class="flex-1">
                <div class="flex flex-wrap items-center gap-3 mb-3">
                  <span
                    class="px-4 py-2 rounded-full text-sm font-bold uppercase"
                    :style="{ backgroundColor: member.color || '#00ff9f', color: '#000' }"
                  >
                    {{ member.position }}
                  </span>
                  <span v-if="member.featured" class="px-3 py-1 bg-accent-yellow text-dark-900 rounded-full text-xs font-bold uppercase">
                    Featured
                  </span>
                </div>

                <h1 class="font-outfit font-bold text-4xl md:text-5xl lg:text-6xl text-white mb-2">
                  {{ member.name.english }}
                </h1>

                <p v-if="member.name.native" class="text-2xl md:text-3xl text-white/90 mb-4">
                  {{ member.name.native }}
                </p>

                <div class="flex flex-wrap items-center gap-4 text-white/80">
                  <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                    </svg>
                    {{ member.team }}
                  </span>
                  <span>{{ member.generation }} Generation</span>
                  <span>{{ member.hometown }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="container mx-auto px-4 max-w-6xl">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Left Column: Profile Info -->
          <div class="lg:col-span-1 space-y-6">
            <!-- Basic Info Card -->
            <div class="bg-white rounded-xl p-6 shadow-md">
              <h2 class="font-outfit font-bold text-2xl mb-4">Profile</h2>
              <dl class="space-y-3">
                <div v-if="member.nickname">
                  <dt class="text-sm text-dark-500 uppercase tracking-wide">Nickname</dt>
                  <dd class="text-dark-800 font-medium">{{ member.nickname }}</dd>
                </div>
                <div>
                  <dt class="text-sm text-dark-500 uppercase tracking-wide">Birthday</dt>
                  <dd class="text-dark-800 font-medium">{{ formattedBirthday }} ({{ member.age }} years old)</dd>
                </div>
                <div v-if="member.height">
                  <dt class="text-sm text-dark-500 uppercase tracking-wide">Height</dt>
                  <dd class="text-dark-800 font-medium">{{ member.height }} cm</dd>
                </div>
                <div v-if="member.bloodType">
                  <dt class="text-sm text-dark-500 uppercase tracking-wide">Blood Type</dt>
                  <dd class="text-dark-800 font-medium">{{ member.bloodType }}</dd>
                </div>
                <div>
                  <dt class="text-sm text-dark-500 uppercase tracking-wide">Join Date</dt>
                  <dd class="text-dark-800 font-medium">{{ formattedJoinDate }}</dd>
                </div>
              </dl>
            </div>

            <!-- Stats Card -->
            <div v-if="member.stats" class="bg-white rounded-xl p-6 shadow-md">
              <h2 class="font-outfit font-bold text-2xl mb-4">Stats</h2>
              <div class="grid grid-cols-2 gap-4">
                <div v-if="member.stats.centerPositions" class="text-center p-4 bg-primary-50 rounded-lg">
                  <div class="text-3xl font-bold text-primary-500 mb-1">{{ member.stats.centerPositions }}</div>
                  <div class="text-xs text-dark-600 uppercase tracking-wide">Center</div>
                </div>
                <div v-if="member.stats.performances" class="text-center p-4 bg-primary-50 rounded-lg">
                  <div class="text-3xl font-bold text-primary-500 mb-1">{{ member.stats.performances }}</div>
                  <div class="text-xs text-dark-600 uppercase tracking-wide">Shows</div>
                </div>
                <div v-if="member.stats.fanMeetings" class="text-center p-4 bg-primary-50 rounded-lg col-span-2">
                  <div class="text-3xl font-bold text-primary-500 mb-1">{{ member.stats.fanMeetings }}</div>
                  <div class="text-xs text-dark-600 uppercase tracking-wide">Fan Meetings</div>
                </div>
              </div>
            </div>

            <!-- Social Links -->
            <div v-if="member.social" class="bg-white rounded-xl p-6 shadow-md">
              <h2 class="font-outfit font-bold text-2xl mb-4">Follow</h2>
              <div class="flex flex-wrap gap-3">
                <a
                  v-if="member.social.twitter"
                  :href="member.social.twitter"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-[#1DA1F2] text-white rounded-lg hover:bg-[#1a8cd8] transition-colors"
                >
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                  </svg>
                  <span class="text-sm font-medium">Twitter</span>
                </a>
                <a
                  v-if="member.social.instagram"
                  :href="member.social.instagram"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-br from-purple-600 to-pink-500 text-white rounded-lg hover:from-purple-700 hover:to-pink-600 transition-colors"
                >
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                  </svg>
                  <span class="text-sm font-medium">Instagram</span>
                </a>
                <a
                  v-if="member.social.tiktok"
                  :href="member.social.tiktok"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-black text-white rounded-lg hover:bg-gray-900 transition-colors"
                >
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/>
                  </svg>
                  <span class="text-sm font-medium">TikTok</span>
                </a>
                <a
                  v-if="member.social.youtube"
                  :href="member.social.youtube"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-[#FF0000] text-white rounded-lg hover:bg-[#cc0000] transition-colors"
                >
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                  </svg>
                  <span class="text-sm font-medium">YouTube</span>
                </a>
              </div>
            </div>
          </div>

          <!-- Right Column: Bio & Details -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Bio -->
            <div class="bg-white rounded-xl p-6 md:p-8 shadow-md">
              <h2 class="font-outfit font-bold text-3xl mb-4">About</h2>
              <p class="text-dark-700 text-lg leading-relaxed mb-6">
                {{ member.bio }}
              </p>

              <!-- Hobbies -->
              <div v-if="member.hobbies && member.hobbies.length > 0" class="mb-6">
                <h3 class="font-outfit font-semibold text-xl mb-3">Hobbies</h3>
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="hobby in member.hobbies"
                    :key="hobby"
                    class="px-4 py-2 bg-primary-50 text-primary-600 rounded-full text-sm font-medium"
                  >
                    {{ hobby }}
                  </span>
                </div>
              </div>

              <!-- Specialties -->
              <div v-if="member.specialties && member.specialties.length > 0">
                <h3 class="font-outfit font-semibold text-xl mb-3">Specialties</h3>
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="specialty in member.specialties"
                    :key="specialty"
                    class="px-4 py-2 rounded-full text-sm font-medium"
                    :style="{ backgroundColor: `${member.color}20`, color: member.color }"
                  >
                    {{ specialty }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Back Button -->
            <div class="text-center">
              <router-link
                to="/members"
                class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-primary text-white rounded-full font-outfit font-semibold uppercase text-sm tracking-wide hover:scale-105 transition-all duration-300 hover:shadow-neon"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                </svg>
                Back to All Members
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </article>

    <!-- Loading State -->
    <div v-else-if="isLoading" class="min-h-screen pt-24 pb-16 flex items-center justify-center">
      <div class="text-center">
        <div class="inline-block w-16 h-16 border-4 border-primary-500 border-t-transparent rounded-full animate-spin mb-4"></div>
        <p class="text-xl text-dark-600">Loading member...</p>
      </div>
    </div>

    <!-- Not Found State -->
    <div v-else class="min-h-screen pt-24 pb-16 flex items-center justify-center">
      <div class="text-center">
        <h1 class="text-6xl font-outfit font-bold mb-4">404</h1>
        <p class="text-2xl text-dark-600 mb-8">Member not found</p>
        <router-link
          to="/members"
          class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-primary text-white rounded-full font-outfit font-semibold uppercase text-sm tracking-wide hover:scale-105 transition-all"
        >
          Back to Members
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
import type { Member } from '@/types/member'
import membersData from '@/data/members.json'

// Route
const route = useRoute()

// State
const member = ref<Member | null>(null)
const isLoading = ref(true)

// Computed
const formattedBirthday = computed(() => {
  if (!member.value) return ''
  return format(new Date(member.value.birthdate), 'MMMM d, yyyy')
})

const formattedJoinDate = computed(() => {
  if (!member.value) return ''
  return format(new Date(member.value.joinDate), 'MMMM d, yyyy')
})

const statusLabel = computed(() => {
  if (!member.value) return ''

  switch (member.value.status) {
    case 'active':
      return 'Active'
    case 'graduated':
      return 'Graduated'
    case 'on-hiatus':
      return 'On Hiatus'
    default:
      return member.value.status
  }
})

const statusClasses = computed(() => {
  if (!member.value) return ''

  const baseClasses = 'text-white'

  switch (member.value.status) {
    case 'active':
      return `${baseClasses} bg-green-500`
    case 'graduated':
      return `${baseClasses} bg-blue-500`
    case 'on-hiatus':
      return `${baseClasses} bg-orange-500`
    default:
      return `${baseClasses} bg-dark-600`
  }
})

// Methods
const loadMember = () => {
  const id = route.params.id as string
  const foundMember = (membersData as Member[]).find(m => m.id === id)

  if (foundMember) {
    member.value = foundMember
  }

  isLoading.value = false
}

// Lifecycle
onMounted(() => {
  loadMember()
})
</script>
