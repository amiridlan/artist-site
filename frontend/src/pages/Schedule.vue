<template>
  <div class="pt-24 pb-16 min-h-screen bg-cream-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Page header -->
      <div ref="headerRef" class="mb-10">
        <h1 class="text-4xl md:text-5xl font-heading font-bold text-charcoal-800">{{ $t('schedule.title') }}</h1>
        <p class="text-charcoal-500 mt-2">{{ $t('schedule.subtitle') }}</p>
      </div>

      <!-- Filter -->
      <div class="flex items-center gap-2 mb-8 overflow-x-auto pb-2 scrollbar-hide">
        <button
          v-for="type in EVENT_TYPES"
          :key="type.value"
          @click="selectedType = type.value"
          class="pill-toggle flex-shrink-0"
          :class="selectedType === type.value ? 'pill-toggle-active' : 'pill-toggle-inactive'"
        >
          {{ getEventTypeTranslation(type.value) }}
        </button>
      </div>

      <!-- Timeline -->
      <div ref="timelineRef" class="relative">
        <!-- Timeline line -->
        <div class="absolute left-6 md:left-8 top-0 bottom-0 w-px bg-jade-200"></div>

        <div v-for="event in filteredEvents" :key="event.id" class="relative pl-16 md:pl-20 pb-10 group">
          <!-- Timeline dot -->
          <div
            class="absolute left-4 md:left-6 w-4 h-4 rounded-full border-2 border-white shadow-sm"
            :class="{
              'bg-jade-500 animate-pulse-jade': event.status === 'upcoming',
              'bg-gold-400': event.status === 'ongoing',
              'bg-charcoal-300': event.status === 'completed',
              'bg-sakura-400': event.status === 'cancelled',
            }"
          ></div>

          <!-- Event card -->
          <div class="bg-white rounded-xl p-6 shadow-card hover:shadow-card-hover transition-all duration-300">
            <div class="flex items-start justify-between gap-3 mb-3">
              <div>
                <span class="text-xs font-medium uppercase tracking-wider"
                      :class="{
                        'text-jade-600': event.status === 'upcoming',
                        'text-gold-600': event.status === 'ongoing',
                        'text-charcoal-400': event.status === 'completed',
                        'text-sakura-600': event.status === 'cancelled',
                      }">
                  {{ $t(`schedule.${event.status}`) }}
                </span>
                <h3 class="font-heading font-bold text-charcoal-800 text-lg mt-1">{{ event.title }}</h3>
              </div>
              <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-jade-50 text-jade-700 flex-shrink-0">
                {{ getEventTypeLabel(event.type) }}
              </span>
            </div>

            <div class="flex flex-wrap items-center gap-4 text-sm text-charcoal-500 mb-3">
              <span class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ formatDate(event.date) }}
              </span>
              <span v-if="event.venue" class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ event.venue }}
              </span>
            </div>

            <p v-if="event.description" class="text-sm text-charcoal-500 leading-relaxed">
              {{ event.description }}
            </p>
          </div>
        </div>
      </div>

      <div v-if="filteredEvents.length === 0" class="text-center py-20">
        <p class="text-charcoal-400 text-lg">{{ $t('schedule.empty') }}</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { gsap } from 'gsap'
import { apiFetch } from '@/composables/useApi'
import { EVENT_TYPES } from '@/utils/constants'
import { formatDate } from '@/utils/helpers'
import type { Event, EventType } from '@/types/event'

const { t } = useI18n()

const events = ref<Event[]>([])
const selectedType = ref<EventType>('all')
const headerRef = ref<HTMLElement | null>(null)
const timelineRef = ref<HTMLElement | null>(null)

const filteredEvents = computed(() => {
  if (selectedType.value === 'all') return events.value
  return events.value.filter(e => e.type === selectedType.value)
})

const eventTypeKeyMap: Record<string, string> = {
  'all': 'all',
  'concert': 'concert',
  'meet-greet': 'meetGreet',
  'handshake': 'handshake',
  'online': 'online',
  'other': 'other',
}

function getEventTypeTranslation(type: string): string {
  return t(`eventTypes.${eventTypeKeyMap[type] || type}`)
}

function getEventTypeLabel(type: string): string {
  return getEventTypeTranslation(type)
}

onMounted(async () => {
  events.value = await apiFetch<Event[]>('/events')

  if (headerRef.value) {
    gsap.from(headerRef.value.children, {
      y: 20, opacity: 0, duration: 0.6, stagger: 0.1, ease: 'power2.out',
    })
  }
  if (timelineRef.value) {
    gsap.from(timelineRef.value.children, {
      x: -20, opacity: 0, duration: 0.5, stagger: 0.1, ease: 'power2.out',
      scrollTrigger: { trigger: timelineRef.value, start: 'top 80%' },
    })
  }
})
</script>
