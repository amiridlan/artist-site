<template>
  <div class="schedule-page-wrapper">
    <div class="schedule-page min-h-screen pt-24 pb-16">
      <div class="container mx-auto px-4 lg:px-8">
      <!-- Page Header -->
      <div ref="headerRef" class="mb-8 md:mb-12">
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-avant-garde font-bold mb-4">
          <span class="text-gradient">Schedule</span>
        </h1>
        <p class="text-lg md:text-xl text-neutral-600 max-w-2xl">
          Check out upcoming events, concerts, and meet & greet sessions. {{ totalEvents }} events available.
        </p>
      </div>

      <!-- View Toggle & Month Navigation -->
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-8">
        <!-- View Toggle -->
        <div class="flex items-center gap-2 bg-surface-200 rounded-lg p-1 shadow-md">
          <button
            @click="viewMode = 'list'"
            :class="[
              'px-4 py-2 rounded-md font-outfit font-semibold text-sm transition-all',
              viewMode === 'list'
                ? 'bg-primary-500 text-white shadow-md'
                : 'text-neutral-600 hover:bg-neutral-100'
            ]"
          >
            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
            </svg>
            List
          </button>
          <button
            @click="viewMode = 'calendar'"
            :class="[
              'px-4 py-2 rounded-md font-outfit font-semibold text-sm transition-all',
              viewMode === 'calendar'
                ? 'bg-primary-500 text-white shadow-md'
                : 'text-neutral-600 hover:bg-neutral-100'
            ]"
          >
            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Calendar
          </button>
        </div>

        <!-- Month Navigation (Calendar View Only) -->
        <div v-if="viewMode === 'calendar'" class="flex items-center gap-4">
          <button
            @click="previousMonth"
            class="p-2 rounded-full hover:bg-neutral-100 transition-colors"
            aria-label="Previous month"
          >
            <svg class="w-6 h-6 text-neutral-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          <div class="text-center min-w-[180px]">
            <h2 class="font-avant-garde font-bold text-xl text-neutral-900">
              {{ currentMonthName }} {{ currentYear }}
            </h2>
          </div>
          <button
            @click="nextMonth"
            class="p-2 rounded-full hover:bg-neutral-100 transition-colors"
            aria-label="Next month"
          >
            <svg class="w-6 h-6 text-neutral-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>
          <button
            @click="goToToday"
            class="px-4 py-2 bg-surface-200 text-neutral-700 border-2 border-neutral-200 rounded-lg font-semibold text-sm hover:border-primary-500 transition-colors"
          >
            Today
          </button>
        </div>
      </div>

      <!-- Search Bar (List View Only) -->
      <div v-if="viewMode === 'list'" class="mb-6">
        <div class="relative max-w-2xl">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search events by title, location, or description..."
            class="w-full pl-12 pr-4 py-3 md:py-4 rounded-lg border-2 border-neutral-200 focus:border-primary-500 focus:outline-none text-neutral-800 placeholder-neutral-400 transition-colors bg-surface-200"
          />
          <svg
            class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-neutral-400"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <button
            v-if="searchQuery"
            @click="searchQuery = ''"
            class="absolute right-4 top-1/2 transform -translate-y-1/2 text-neutral-400 hover:text-neutral-600"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Event Type Filter -->
      <EventTypeFilter
        v-model:active-type="selectedType"
        :count="filteredEvents.length"
      />

      <!-- Calendar View -->
      <div v-if="viewMode === 'calendar'">
        <CalendarView
          :events="filteredEvents"
          :current-month="currentMonth"
          :current-year="currentYear"
          @day-click="handleDayClick"
        />

        <!-- Selected Day Events -->
        <div v-if="selectedDayEvents.length > 0" class="mt-8">
          <h3 class="text-2xl font-avant-garde font-bold mb-6">
            Events on {{ selectedDayName }}
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <EventCard
              v-for="event in selectedDayEvents"
              :key="event.id"
              :event="event"
              @click="handleEventClick"
              @ticket-click="handleTicketClick"
            />
          </div>
        </div>
      </div>

      <!-- List View -->
      <div v-else>
        <!-- Loading State -->
        <div v-if="isLoading" class="event-grid mb-12">
          <div v-for="n in 6" :key="`skeleton-${n}`" class="bg-surface-200 rounded-xl overflow-hidden shadow-md animate-pulse">
            <div class="h-48 bg-neutral-200"></div>
            <div class="p-5 space-y-3">
              <div class="h-5 bg-neutral-200 rounded w-3/4"></div>
              <div class="h-4 bg-neutral-200 rounded w-full"></div>
              <div class="h-4 bg-neutral-200 rounded w-2/3"></div>
            </div>
          </div>
        </div>

        <!-- Event Grid -->
        <div v-else-if="paginatedEvents.length > 0" class="event-grid mb-12">
          <EventCard
            v-for="event in paginatedEvents"
            :key="event.id"
            :event="event"
            @click="handleEventClick"
            @ticket-click="handleTicketClick"
          />
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-20">
          <svg class="w-24 h-24 mx-auto mb-6 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <h3 class="text-2xl font-outfit font-bold mb-2">No events found</h3>
          <p class="text-neutral-600 mb-6">Try adjusting your filters or search query.</p>
          <button
            @click="resetFilters"
            class="px-6 py-3 bg-primary-500 text-white rounded-full font-outfit font-semibold uppercase text-sm tracking-wide hover:scale-105 transition-all"
          >
            Show All Events
          </button>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="flex justify-center items-center gap-2">
          <button
            @click="goToPage(currentPage - 1)"
            :disabled="currentPage === 1"
            :class="[
              'px-4 py-2 rounded-lg font-medium transition-all',
              currentPage === 1
                ? 'bg-neutral-200 text-neutral-400 cursor-not-allowed'
                : 'bg-white text-neutral-700 hover:bg-primary-500 hover:text-white'
            ]"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </button>

          <button
            v-for="page in visiblePages"
            :key="page"
            @click="goToPage(page)"
            :class="[
              'px-4 py-2 rounded-lg font-medium transition-all',
              currentPage === page
                ? 'bg-primary-500 text-white'
                : 'bg-white text-neutral-700 hover:bg-primary-500 hover:text-white'
            ]"
          >
            {{ page }}
          </button>

          <button
            @click="goToPage(currentPage + 1)"
            :disabled="currentPage === totalPages"
            :class="[
              'px-4 py-2 rounded-lg font-medium transition-all',
              currentPage === totalPages
                ? 'bg-neutral-200 text-neutral-400 cursor-not-allowed'
                : 'bg-white text-neutral-700 hover:bg-primary-500 hover:text-white'
            ]"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Event Detail Modal (Simple implementation for now) -->
    <teleport to="body">
    <transition name="fade">
      <div
        v-if="selectedEvent"
        class="fixed inset-0 bg-black/90 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        @click.self="closeEventModal"
      >
        <div class="relative w-full max-w-4xl bg-white rounded-2xl overflow-hidden shadow-2xl max-h-[90vh] overflow-y-auto">
          <!-- Close Button -->
          <button
            @click="closeEventModal"
            class="absolute top-4 right-4 z-10 w-10 h-10 rounded-full bg-black/50 hover:bg-black/70 text-white flex items-center justify-center transition-colors"
            aria-label="Close"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <!-- Event Cover Image -->
          <div v-if="selectedEvent.coverImage || selectedEvent.image" class="relative h-64 md:h-80 overflow-hidden">
            <img
              :src="selectedEvent.coverImage || selectedEvent.image"
              :alt="selectedEvent.title"
              class="w-full h-full object-cover"
            />
            <div class="absolute inset-0 bg-black/50"></div>
          </div>

          <!-- Event Content -->
          <div class="p-6 md:p-8">
            <!-- Title -->
            <h2 class="font-outfit font-bold text-3xl md:text-4xl mb-4">
              {{ selectedEvent.title }}
            </h2>

            <!-- Meta Info -->
            <div class="flex flex-wrap gap-4 mb-6">
              <span :class="['px-3 py-1 rounded-full text-xs font-semibold uppercase', getEventTypeClass(selectedEvent.type)]">
                {{ getEventTypeLabel(selectedEvent.type) }}
              </span>
              <span :class="['px-3 py-1 rounded-full text-xs font-semibold uppercase', getEventStatusClass(selectedEvent.status)]">
                {{ getEventStatusLabel(selectedEvent.status) }}
              </span>
            </div>

            <!-- Description -->
            <p class="text-neutral-700 text-lg leading-relaxed mb-6">
              {{ selectedEvent.description }}
            </p>

            <!-- Event Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
              <!-- Date & Time -->
              <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-primary-500 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <div>
                  <div class="font-semibold text-neutral-800">Date & Time</div>
                  <div class="text-neutral-600">{{ formatEventDate(selectedEvent) }}</div>
                  <div class="text-neutral-600">{{ selectedEvent.startTime }} {{ selectedEvent.endTime ? `- ${selectedEvent.endTime}` : '' }}</div>
                </div>
              </div>

              <!-- Location -->
              <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-primary-500 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <div>
                  <div class="font-semibold text-neutral-800">Location</div>
                  <div class="text-neutral-600">{{ selectedEvent.location.venue }}</div>
                  <div class="text-neutral-600 text-sm">{{ selectedEvent.location.city }}, {{ selectedEvent.location.country }}</div>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-wrap gap-4">
              <button
                v-if="selectedEvent.status === 'upcoming' && selectedEvent.ticket.available && !selectedEvent.ticket.soldOut && selectedEvent.ticket.ticketUrl"
                @click="openTicketUrl(selectedEvent.ticket.ticketUrl)"
                class="px-6 py-3 bg-primary-500 text-white rounded-full font-outfit font-semibold hover:scale-105 transition-transform"
              >
                Get Tickets
              </button>
              <button
                @click="exportToCalendar(selectedEvent)"
                class="px-6 py-3 bg-white text-neutral-700 border-2 border-neutral-300 rounded-full font-outfit font-semibold hover:border-primary-500 transition-colors flex items-center gap-2"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export to Calendar
              </button>
            </div>
          </div>
        </div>
        </div>
      </transition>
    </teleport>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import { format } from 'date-fns'
import EventCard from '@/components/events/EventCard.vue'
import EventTypeFilter from '@/components/events/EventTypeFilter.vue'
import CalendarView from '@/components/events/CalendarView.vue'
import type { Event, EventType, EventStatus, CalendarDay } from '@/types/event'
import eventsData from '@/data/events.json'
import { downloadICalendar } from '@/utils/helpers'

// State
const allEvents = ref<Event[]>([])
const isLoading = ref(true)
const viewMode = ref<'list' | 'calendar'>('list')
const selectedType = ref<EventType>('all')
const searchQuery = ref('')
const currentPage = ref(1)
const eventsPerPage = 6

// Calendar state
const currentMonth = ref(new Date().getMonth())
const currentYear = ref(new Date().getFullYear())
const selectedDay = ref<CalendarDay | null>(null)

// Modal state
const selectedEvent = ref<Event | null>(null)

// Computed
const totalEvents = computed(() => allEvents.value.length)

const filteredEvents = computed(() => {
  let result = [...allEvents.value]

  // Filter by type
  if (selectedType.value !== 'all') {
    result = result.filter(event => event.type === selectedType.value)
  }

  // Filter by search query (list view only)
  if (viewMode.value === 'list' && searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(event =>
      event.title.toLowerCase().includes(query) ||
      event.description.toLowerCase().includes(query) ||
      event.location.venue.toLowerCase().includes(query) ||
      event.location.city.toLowerCase().includes(query)
    )
  }

  // Sort by date (ascending for upcoming, descending for past)
  result.sort((a, b) => {
    const dateA = new Date(a.startDate).getTime()
    const dateB = new Date(b.startDate).getTime()
    return dateA - dateB
  })

  return result
})

const paginatedEvents = computed(() => {
  const start = (currentPage.value - 1) * eventsPerPage
  const end = start + eventsPerPage
  return filteredEvents.value.slice(start, end)
})

const totalPages = computed(() => {
  return Math.ceil(filteredEvents.value.length / eventsPerPage)
})

const visiblePages = computed(() => {
  const pages: number[] = []
  const maxVisible = 5
  let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2))
  let end = Math.min(totalPages.value, start + maxVisible - 1)

  if (end - start < maxVisible - 1) {
    start = Math.max(1, end - maxVisible + 1)
  }

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }

  return pages
})

const currentMonthName = computed(() => {
  const monthNames = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
  ]
  return monthNames[currentMonth.value]
})

const selectedDayEvents = computed(() => {
  if (!selectedDay.value || selectedDay.value.events.length === 0) {
    return []
  }
  return selectedDay.value.events
})

const selectedDayName = computed(() => {
  if (!selectedDay.value) return ''
  return format(selectedDay.value.date, 'EEEE, MMMM d, yyyy')
})

// Methods
const goToPage = (page: number) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const resetFilters = () => {
  selectedType.value = 'all'
  searchQuery.value = ''
  currentPage.value = 1
}

const previousMonth = () => {
  if (currentMonth.value === 0) {
    currentMonth.value = 11
    currentYear.value--
  } else {
    currentMonth.value--
  }
}

const nextMonth = () => {
  if (currentMonth.value === 11) {
    currentMonth.value = 0
    currentYear.value++
  } else {
    currentMonth.value++
  }
}

const goToToday = () => {
  const today = new Date()
  currentMonth.value = today.getMonth()
  currentYear.value = today.getFullYear()
}

const handleDayClick = (day: CalendarDay) => {
  selectedDay.value = day
}

const handleEventClick = (event: Event) => {
  selectedEvent.value = event
}

const handleTicketClick = (event: Event) => {
  if (event.ticket.ticketUrl) {
    window.open(event.ticket.ticketUrl, '_blank', 'noopener,noreferrer')
  }
}

const closeEventModal = () => {
  selectedEvent.value = null
}

const formatEventDate = (event: Event): string => {
  return format(new Date(event.startDate), 'EEEE, MMMM d, yyyy')
}

const getEventTypeLabel = (type: EventType): string => {
  const labels: Record<EventType, string> = {
    'all': 'All',
    'concert': 'Concert',
    'meet-greet': 'Meet & Greet',
    'handshake': 'Handshake',
    'online': 'Online',
    'other': 'Event'
  }
  return labels[type]
}

const getEventTypeClass = (type: EventType): string => {
  switch (type) {
    case 'concert':
      return 'bg-primary-500 text-white'
    case 'meet-greet':
      return 'bg-primary-600 text-white'
    case 'handshake':
      return 'bg-primary-500 text-white'
    case 'online':
      return 'bg-primary-700 text-white'
    case 'other':
      return 'bg-primary-400 text-neutral-900'
    default:
      return 'bg-neutral-600 text-white'
  }
}

const getEventStatusLabel = (status: EventStatus): string => {
  const labels: Record<EventStatus, string> = {
    'upcoming': 'Upcoming',
    'ongoing': 'Now',
    'completed': 'Past',
    'cancelled': 'Cancelled'
  }
  return labels[status]
}

const getEventStatusClass = (status: EventStatus): string => {
  switch (status) {
    case 'upcoming':
      return 'bg-accent-green/90 text-white'
    case 'ongoing':
      return 'bg-red-500 text-white'
    case 'completed':
      return 'bg-neutral-600 text-white'
    case 'cancelled':
      return 'bg-red-600 text-white'
    default:
      return 'bg-neutral-600 text-white'
  }
}

const openTicketUrl = (url: string) => {
  window.open(url, '_blank', 'noopener,noreferrer')
}

const exportToCalendar = (event: Event) => {
  const locationString = event.location.city === 'Online'
    ? event.location.onlineUrl || 'Online Event'
    : `${event.location.venue}, ${event.location.city}, ${event.location.country}`

  downloadICalendar({
    title: event.title,
    description: event.description,
    location: locationString,
    startDate: event.startDate,
    endDate: event.endDate,
    startTime: event.startTime,
    endTime: event.endTime,
    url: event.ticket.ticketUrl
  })
}

// Watch for filter/search changes
watch([selectedType, searchQuery], () => {
  currentPage.value = 1
})

// Lifecycle
onMounted(async () => {
  // Simulate loading
  setTimeout(() => {
    allEvents.value = eventsData as Event[]
    isLoading.value = false
  }, 500)
})
</script>

<style scoped>
/* Event grid */
.event-grid {
  display: grid;
  grid-template-columns: repeat(1, 1fr);
  gap: 1.5rem;
}

@media (min-width: 768px) {
  .event-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 1024px) {
  .event-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

/* Accent green color */
.text-accent-green,
.bg-accent-green {
  color: #00ff9f;
}

.bg-accent-green {
  background-color: #00ff9f;
}

/* Fade transition */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
