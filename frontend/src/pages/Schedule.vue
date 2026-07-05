<template>
  <div class="pt-24 pb-16 min-h-screen bg-cream-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

      <!-- Page header -->
      <div ref="headerRef" class="mb-8">
        <h1 class="text-4xl md:text-5xl font-heading font-bold text-charcoal-800">{{ $t('schedule.title') }}</h1>
        <p class="text-charcoal-500 mt-2">{{ $t('schedule.subtitle') }}</p>
      </div>

      <!-- Filter pills -->
      <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-2 scrollbar-hide">
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

      <!-- Calendar -->
      <div class="bg-white rounded-2xl shadow-card overflow-hidden">

        <!-- Month navigation -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-charcoal-100">
          <button
            @click="prevMonth"
            class="w-9 h-9 flex items-center justify-center rounded-lg hover:bg-charcoal-50 text-charcoal-500 hover:text-charcoal-800 transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </button>
          <h2 class="font-heading font-bold text-charcoal-800 text-lg">{{ monthLabel }}</h2>
          <button
            @click="nextMonth"
            class="w-9 h-9 flex items-center justify-center rounded-lg hover:bg-charcoal-50 text-charcoal-500 hover:text-charcoal-800 transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </button>
        </div>

        <!-- Day-of-week headers -->
        <div class="grid grid-cols-7 border-b border-charcoal-100">
          <div
            v-for="day in weekDays"
            :key="day"
            class="py-2 text-center text-xs font-semibold text-charcoal-400 uppercase tracking-wider"
          >
            {{ day }}
          </div>
        </div>

        <!-- Calendar grid -->
        <div class="grid grid-cols-7">
          <div
            v-for="(cell, idx) in calendarCells"
            :key="idx"
            class="min-h-[80px] md:min-h-[100px] border-b border-r border-charcoal-50 p-1.5 transition-colors"
            :class="[
              cell.day ? 'cursor-default' : 'bg-charcoal-50/30',
              cell.isToday ? 'bg-jade-50/60' : '',
              cell.events.length > 0 ? 'cursor-pointer hover:bg-cream-100' : 'hover:bg-cream-50',
              (idx + 1) % 7 === 0 ? 'border-r-0' : '',
            ]"
            @click="cell.events.length > 0 && openModal(cell)"
          >
            <!-- Date number -->
            <div v-if="cell.day" class="flex items-center justify-between mb-1">
              <span
                class="w-6 h-6 flex items-center justify-center text-xs font-medium rounded-full"
                :class="cell.isToday ? 'bg-forest-500 text-white' : 'text-charcoal-600'"
              >
                {{ cell.day }}
              </span>
            </div>

            <!-- Event chips -->
            <div v-if="cell.day" class="space-y-0.5">
              <div
                v-for="event in cell.events.slice(0, 3)"
                :key="event.id"
                class="truncate text-[10px] font-medium px-1.5 py-0.5 rounded"
                :class="statusChipClass(event.status)"
              >
                {{ event.title }}
              </div>
              <div v-if="cell.events.length > 3" class="text-[10px] text-charcoal-400 px-1.5">
                +{{ cell.events.length - 3 }} {{ $t('schedule.more') }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading state -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block w-8 h-8 border-4 border-jade-200 border-t-jade-600 rounded-full animate-spin"></div>
      </div>

      <!-- Empty state -->
      <div v-else-if="eventsThisMonth.length === 0" class="text-center py-12">
        <p class="text-charcoal-400">{{ $t('schedule.empty') }}</p>
      </div>

    </div>

    <!-- ── Event modal ── -->
    <Transition name="modal-backdrop">
      <div
        v-if="selectedCell"
        class="fixed inset-0 z-50 flex items-center justify-center px-4 py-8"
        @click.self="closeModal"
      >
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="closeModal" />

        <!-- Panel -->
        <Transition name="modal-pop" appear>
          <div
            v-if="selectedCell"
            class="relative bg-cream-50 rounded-2xl shadow-2xl w-full max-w-lg max-h-[80vh] flex flex-col overflow-hidden"
          >
            <!-- Modal header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-charcoal-100 bg-white flex-shrink-0">
              <h3 class="font-heading font-bold text-charcoal-800">{{ selectedDateLabel }}</h3>
              <button
                @click="closeModal"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-charcoal-400 hover:text-charcoal-700 hover:bg-charcoal-50 transition-colors"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>

            <!-- Event list (scrollable) -->
            <div class="overflow-y-auto p-4 space-y-3">
              <div
                v-for="event in selectedCell.events"
                :key="event.id"
                class="bg-white rounded-xl p-5 shadow-card"
              >
                <div class="flex items-start justify-between gap-3 mb-3">
                  <div>
                    <span class="text-xs font-semibold uppercase tracking-wider" :class="statusTextClass(event.status)">
                      {{ $t(`schedule.${event.status}`) }}
                    </span>
                    <h4 class="font-heading font-bold text-charcoal-800 text-base mt-0.5">{{ event.title }}</h4>
                  </div>
                  <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-jade-50 text-jade-700 flex-shrink-0">
                    {{ getEventTypeTranslation(event.type) }}
                  </span>
                </div>

                <div class="flex flex-wrap items-center gap-3 text-sm text-charcoal-500 mb-3">
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
                  <span v-if="event.location" class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                    </svg>
                    {{ event.location }}
                  </span>
                </div>

                <p v-if="event.description" class="text-sm text-charcoal-500 leading-relaxed mb-3">
                  {{ event.description }}
                </p>

                <a
                  v-if="event.ticketUrl"
                  :href="event.ticketUrl"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="inline-block px-4 py-2 bg-jade-gradient text-white text-sm font-semibold rounded-full hover:shadow-jade-glow transition-all duration-300"
                >
                  Get Tickets →
                </a>
              </div>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { gsap } from 'gsap'
import { apiFetch } from '@/composables/useApi'
import { EVENT_TYPES } from '@/utils/constants'
import { formatDate } from '@/utils/helpers'
import type { Event, EventType } from '@/types/event'

const { t, locale } = useI18n()

const events       = ref<Event[]>([])
const loading      = ref(true)
const selectedType = ref<EventType>('all')
const headerRef    = ref<HTMLElement | null>(null)

// Calendar state
const today        = new Date()
const currentYear  = ref(today.getFullYear())
const currentMonth = ref(today.getMonth())

interface CalendarCell {
  day: number | null
  date: Date | null
  isToday: boolean
  events: Event[]
}

const selectedCell = ref<CalendarCell | null>(null)

// ── Modal helpers ──
function openModal(cell: CalendarCell) {
  selectedCell.value = cell
  document.body.style.overflow = 'hidden'
}
function closeModal() {
  selectedCell.value = null
  document.body.style.overflow = ''
}

// Close on Escape key
function onKeydown(e: KeyboardEvent) {
  if (e.key === 'Escape') closeModal()
}
onMounted(() => window.addEventListener('keydown', onKeydown))
onUnmounted(() => {
  window.removeEventListener('keydown', onKeydown)
  document.body.style.overflow = ''
})

const weekDays = computed(() => {
  if (locale.value === 'ms') return ['Ahd', 'Isn', 'Sel', 'Rab', 'Kha', 'Jum', 'Sab']
  if (locale.value === 'ja') return ['日', '月', '火', '水', '木', '金', '土']
  return ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
})

const monthLabel = computed(() => {
  const d = new Date(currentYear.value, currentMonth.value, 1)
  return d.toLocaleDateString(
    locale.value === 'ja' ? 'ja-JP' : locale.value === 'ms' ? 'ms-MY' : 'en-US',
    { month: 'long', year: 'numeric' }
  )
})

const selectedDateLabel = computed(() => {
  if (!selectedCell.value?.date) return ''
  return selectedCell.value.date.toLocaleDateString(
    locale.value === 'ja' ? 'ja-JP' : locale.value === 'ms' ? 'ms-MY' : 'en-US',
    { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }
  )
})

const filteredEvents = computed(() =>
  selectedType.value === 'all' ? events.value : events.value.filter(e => e.type === selectedType.value)
)

const eventsThisMonth = computed(() =>
  filteredEvents.value.filter(e => {
    const d = new Date(e.date)
    return d.getFullYear() === currentYear.value && d.getMonth() === currentMonth.value
  })
)

const calendarCells = computed((): CalendarCell[] => {
  const firstDay    = new Date(currentYear.value, currentMonth.value, 1).getDay()
  const daysInMonth = new Date(currentYear.value, currentMonth.value + 1, 0).getDate()
  const cells: CalendarCell[] = []

  for (let i = 0; i < firstDay; i++) {
    cells.push({ day: null, date: null, isToday: false, events: [] })
  }
  for (let d = 1; d <= daysInMonth; d++) {
    const date    = new Date(currentYear.value, currentMonth.value, d)
    const isToday = d === today.getDate() && currentMonth.value === today.getMonth() && currentYear.value === today.getFullYear()
    const dayEvents = filteredEvents.value.filter(e => {
      const ed = new Date(e.date)
      return ed.getFullYear() === currentYear.value && ed.getMonth() === currentMonth.value && ed.getDate() === d
    })
    cells.push({ day: d, date, isToday, events: dayEvents })
  }
  const remainder = cells.length % 7
  if (remainder !== 0) {
    for (let i = 0; i < 7 - remainder; i++) {
      cells.push({ day: null, date: null, isToday: false, events: [] })
    }
  }
  return cells
})

function prevMonth() {
  closeModal()
  if (currentMonth.value === 0) { currentMonth.value = 11; currentYear.value-- }
  else currentMonth.value--
}
function nextMonth() {
  closeModal()
  if (currentMonth.value === 11) { currentMonth.value = 0; currentYear.value++ }
  else currentMonth.value++
}

const eventTypeKeyMap: Record<string, string> = {
  all: 'all', concert: 'concert', 'meet-greet': 'meetGreet',
  handshake: 'handshake', online: 'online', other: 'other',
}
function getEventTypeTranslation(type: string): string {
  return t(`eventTypes.${eventTypeKeyMap[type] || type}`)
}

function statusChipClass(status: string): string {
  return ({
    upcoming:  'bg-jade-100 text-jade-700',
    ongoing:   'bg-gold-100 text-gold-700',
    completed: 'bg-charcoal-100 text-charcoal-500',
    cancelled: 'bg-sakura-100 text-sakura-700',
  } as Record<string, string>)[status] ?? 'bg-charcoal-100 text-charcoal-500'
}

function statusTextClass(status: string): string {
  return ({
    upcoming:  'text-jade-600',
    ongoing:   'text-gold-600',
    completed: 'text-charcoal-400',
    cancelled: 'text-sakura-600',
  } as Record<string, string>)[status] ?? 'text-charcoal-400'
}

function jumpToRelevantMonth() {
  const upcoming = events.value
    .filter(e => e.status === 'upcoming')
    .map(e => new Date(e.date))
    .sort((a, b) => a.getTime() - b.getTime())

  if (upcoming.length > 0 && upcoming[0].getMonth() !== today.getMonth()) {
    currentYear.value  = upcoming[0].getFullYear()
    currentMonth.value = upcoming[0].getMonth()
  }
}

onMounted(async () => {
  try {
    events.value = await apiFetch<Event[]>('/events')
    jumpToRelevantMonth()
  } finally {
    loading.value = false
  }

  if (headerRef.value) {
    gsap.from(headerRef.value.children, { y: 20, opacity: 0, duration: 0.6, stagger: 0.1, ease: 'power2.out' })
  }
})
</script>

<style scoped>
/* Backdrop fade */
.modal-backdrop-enter-active,
.modal-backdrop-leave-active {
  transition: opacity 0.25s ease;
}
.modal-backdrop-enter-from,
.modal-backdrop-leave-to {
  opacity: 0;
}

/* Panel pop-up */
.modal-pop-enter-active {
  transition: opacity 0.2s ease, transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.modal-pop-leave-active {
  transition: opacity 0.15s ease, transform 0.15s ease;
}
.modal-pop-enter-from {
  opacity: 0;
  transform: scale(0.92) translateY(16px);
}
.modal-pop-leave-to {
  opacity: 0;
  transform: scale(0.96) translateY(8px);
}
</style>
