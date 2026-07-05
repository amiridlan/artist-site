<template>
  <div class="pt-24 pb-16 min-h-screen bg-cream-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Page header -->
      <div ref="headerRef" class="mb-10">
        <h1 class="text-4xl md:text-5xl font-heading font-bold text-charcoal-800">{{ $t('members.title') }}</h1>
        <p class="text-charcoal-500 mt-2">{{ $t('members.subtitle') }}</p>
      </div>

      <!-- Filters -->
      <div class="flex flex-wrap items-center gap-3 mb-8">
        <button
          v-for="gen in generationFilters"
          :key="gen.value"
          @click="selectedGeneration = gen.value"
          class="pill-toggle"
          :class="selectedGeneration === gen.value ? 'pill-toggle-active' : 'pill-toggle-inactive'"
        >
          {{ $t(gen.labelKey) }}
        </button>

        <div class="w-px h-6 bg-charcoal-200 mx-2 hidden sm:block"></div>

        <button
          v-for="status in statusFilters"
          :key="status.value"
          @click="selectedStatus = status.value"
          class="pill-toggle"
          :class="selectedStatus === status.value ? 'pill-toggle-active' : 'pill-toggle-inactive'"
        >
          {{ $t(status.labelKey) }}
        </button>
      </div>

      <!-- Members grid -->
      <div ref="gridRef" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-5">
        <router-link
          v-for="member in filteredMembers"
          :key="member.id"
          :to="`/members/${member.id}`"
          class="group text-center"
        >
          <div
            class="relative w-full aspect-[3/4] rounded-2xl overflow-hidden mb-3 shadow-card group-hover:shadow-card-hover transition-all duration-300 transform group-hover:-translate-y-1"
            :style="{ borderColor: member.color || '#00B4A0', borderWidth: '2px', borderStyle: 'solid' }"
          >
            <!-- Placeholder with member initial -->
            <div class="absolute inset-0 flex items-center justify-center"
                 :style="{ backgroundColor: (member.color || '#00B4A0') + '15' }">
              <span class="text-5xl font-heading font-bold" :style="{ color: member.color || '#00B4A0' }">
                {{ member.name.english.charAt(0) }}
              </span>
            </div>

            <!-- Hover overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-charcoal-800/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
              <div class="text-white text-left">
                <p v-if="member.name.native" class="text-xs text-white/70 font-jp">{{ member.name.native }}</p>
                <span class="text-xs px-2 py-0.5 rounded-full bg-white/20 mt-1 inline-block">
                  {{ member.generation }} {{ $t('common.gen') }}
                </span>
              </div>
            </div>

            <!-- Status badge -->
            <div v-if="member.status !== 'active'"
                 class="absolute top-2 right-2 px-2 py-0.5 text-xs font-medium rounded-full"
                 :class="member.status === 'concluded' ? 'bg-charcoal-800/70 text-white/80' : 'bg-gold-400/80 text-white'">
              {{ member.status }}
            </div>
          </div>

          <p class="font-medium text-charcoal-800 group-hover:text-jade-600 transition-colors">
            {{ member.name.english }}
          </p>
          <p class="text-xs text-charcoal-400">{{ member.generation }} {{ $t('common.generation') }}</p>
        </router-link>
      </div>

      <!-- Loading state -->
      <div v-if="loading" class="text-center py-20">
        <div class="inline-block w-8 h-8 border-4 border-jade-200 border-t-jade-600 rounded-full animate-spin"></div>
      </div>

      <!-- Empty state -->
      <div v-else-if="filteredMembers.length === 0" class="text-center py-20">
        <p class="text-charcoal-400 text-lg">{{ $t('members.empty') }}</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { gsap } from 'gsap'
import { apiFetch } from '@/composables/useApi'
import type { Member, MemberGeneration, MemberStatus } from '@/types/member'

const members = ref<Member[]>([])
const loading = ref(true)
const headerRef = ref<HTMLElement | null>(null)
const gridRef = ref<HTMLElement | null>(null)

const selectedGeneration = ref<MemberGeneration | 'all'>('all')
const selectedStatus = ref<MemberStatus | 'all'>('all')

const generationFilters = [
  { value: 'all' as const, labelKey: 'members.allGenerations' },
  { value: '1st' as const, labelKey: 'members.firstGen' },
  { value: '2nd' as const, labelKey: 'members.secondGen' },
]

const statusFilters = [
  { value: 'all' as const, labelKey: 'memberStatus.all' },
  { value: 'active' as const, labelKey: 'memberStatus.active' },
  { value: 'concluded' as const, labelKey: 'memberStatus.concluded' },
]

const filteredMembers = computed(() => {
  return members.value.filter(m => {
    if (selectedGeneration.value !== 'all' && m.generation !== selectedGeneration.value) return false
    if (selectedStatus.value !== 'all' && m.status !== selectedStatus.value) return false
    return true
  })
})

onMounted(async () => {
  try {
    members.value = await apiFetch<Member[]>('/members')
  } finally {
    loading.value = false
  }

  if (headerRef.value) {
    gsap.from(headerRef.value.children, {
      y: 20, opacity: 0, duration: 0.6, stagger: 0.1, ease: 'power2.out',
    })
  }
})
</script>
