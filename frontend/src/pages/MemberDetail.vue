<template>
  <div class="pt-24 pb-16 min-h-screen bg-cream-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <template v-if="member">
        <!-- Back link -->
        <router-link to="/members" class="inline-flex items-center gap-1 text-sm text-jade-600 hover:text-jade-700 mb-8 group">
          <span class="transition-transform group-hover:-translate-x-1">&larr;</span>
          {{ $t('members.backToMembers') }}
        </router-link>

        <div ref="profileRef" class="grid grid-cols-1 md:grid-cols-3 gap-10">
          <!-- Photo -->
          <div class="md:col-span-1">
            <div
              class="aspect-[3/4] rounded-2xl overflow-hidden shadow-lg"
              :style="{ borderColor: member.color || '#00B4A0', borderWidth: '3px', borderStyle: 'solid' }"
            >
              <div class="w-full h-full flex items-center justify-center"
                   :style="{ backgroundColor: (member.color || '#00B4A0') + '15' }">
                <span class="text-8xl font-heading font-bold" :style="{ color: member.color || '#00B4A0' }">
                  {{ member.name.english.charAt(0) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Info -->
          <div class="md:col-span-2">
            <div class="flex items-start gap-3 mb-1">
              <h1 class="text-3xl md:text-4xl font-heading font-bold text-charcoal-800">
                {{ member.name.english }}
              </h1>
              <span v-if="member.status !== 'active'"
                    class="mt-2 px-3 py-0.5 text-xs font-medium rounded-full"
                    :class="member.status === 'concluded' ? 'bg-charcoal-200 text-charcoal-600' : 'bg-gold-100 text-gold-700'">
                {{ member.status }}
              </span>
            </div>

            <p v-if="member.name.native" class="text-lg text-charcoal-500 font-jp mb-6">{{ member.name.native }}</p>

            <!-- Quick stats -->
            <div class="grid grid-cols-2 gap-4 mb-8">
              <div class="bg-white rounded-xl p-4 shadow-card">
                <p class="text-xs text-charcoal-400 uppercase tracking-wider mb-1">{{ $t('common.generation') }}</p>
                <p class="font-semibold text-charcoal-800">{{ member.generation }}</p>
              </div>
              <div v-if="member.birthdate" class="bg-white rounded-xl p-4 shadow-card">
                <p class="text-xs text-charcoal-400 uppercase tracking-wider mb-1">{{ $t('members.birthday') }}</p>
                <p class="font-semibold text-charcoal-800">{{ member.birthdate }}</p>
              </div>
              <div v-if="member.hometown" class="bg-white rounded-xl p-4 shadow-card">
                <p class="text-xs text-charcoal-400 uppercase tracking-wider mb-1">{{ $t('members.from') }}</p>
                <p class="font-semibold text-charcoal-800">{{ member.hometown }}</p>
              </div>
              <div v-if="member.height" class="bg-white rounded-xl p-4 shadow-card">
                <p class="text-xs text-charcoal-400 uppercase tracking-wider mb-1">{{ $t('members.height') }}</p>
                <p class="font-semibold text-charcoal-800">{{ member.height }}{{ $t('members.cm') }}</p>
              </div>
            </div>

            <!-- Bio -->
            <div v-if="member.bio" class="mb-8">
              <h2 class="text-lg font-heading font-semibold text-charcoal-800 mb-3">{{ $t('members.about') }}</h2>
              <p class="text-charcoal-600 leading-relaxed">{{ member.bio }}</p>
            </div>

            <!-- Hobbies -->
            <div v-if="member.hobbies?.length" class="mb-8">
              <h2 class="text-lg font-heading font-semibold text-charcoal-800 mb-3">{{ $t('members.hobbies') }}</h2>
              <div class="flex flex-wrap gap-2">
                <span v-for="hobby in member.hobbies" :key="hobby"
                      class="px-3 py-1 text-sm bg-jade-50 text-jade-700 rounded-full">
                  {{ hobby }}
                </span>
              </div>
            </div>

            <!-- Social links -->
            <div v-if="member.social" class="flex items-center gap-3">
              <a v-for="(url, platform) in activeSocials" :key="platform"
                 :href="url" target="_blank" rel="noopener noreferrer"
                 class="w-10 h-10 rounded-full bg-jade-100 text-jade-600 flex items-center justify-center hover:bg-jade-500 hover:text-white transition-all duration-200">
                <span class="text-xs font-semibold uppercase">{{ (platform as string).slice(0, 2) }}</span>
              </a>
            </div>
          </div>
        </div>
      </template>

      <!-- Loading state -->
      <div v-else-if="loading" class="text-center py-20">
        <div class="inline-block w-8 h-8 border-4 border-jade-200 border-t-jade-600 rounded-full animate-spin"></div>
      </div>

      <!-- Not found -->
      <div v-else class="text-center py-20">
        <h2 class="text-2xl font-heading font-bold text-charcoal-800 mb-2">{{ $t('members.notFound') }}</h2>
        <router-link to="/members" class="text-jade-600 hover:text-jade-700 font-medium">
          &larr; {{ $t('members.backToMembers') }}
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { gsap } from 'gsap'
import { apiFetch } from '@/composables/useApi'
import type { Member } from '@/types/member'

const route = useRoute()
const member = ref<Member | null>(null)
const loading = ref(true)
const profileRef = ref<HTMLElement | null>(null)

const activeSocials = computed(() => {
  if (!member.value?.social) return {}
  return Object.fromEntries(
    Object.entries(member.value.social).filter(([_, url]) => url)
  )
})

async function fetchMember() {
  loading.value = true
  const id = route.params.id as string
  try {
    member.value = await apiFetch<Member>(`/members/${id}`)
  } catch {
    member.value = null
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await fetchMember()

  if (profileRef.value) {
    gsap.from(profileRef.value.children, {
      y: 30, opacity: 0, duration: 0.7, stagger: 0.15, ease: 'power2.out',
    })
  }
})

watch(() => route.params.id, fetchMember)
</script>
