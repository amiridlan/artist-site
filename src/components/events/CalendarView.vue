<template>
  <div class="calendar-view">
    <!-- Calendar Header - Days of Week -->
    <div class="calendar-header grid grid-cols-7 gap-2 mb-4">
      <div
        v-for="day in daysOfWeek"
        :key="day"
        class="text-center font-outfit font-semibold text-sm text-neutral-700 py-2"
      >
        {{ day }}
      </div>
    </div>

    <!-- Calendar Grid -->
    <div class="calendar-grid grid grid-cols-7 gap-2">
      <div
        v-for="(day, index) in calendarDays"
        :key="`${day.year}-${day.month}-${day.day}-${index}`"
        :class="[
          'calendar-day relative aspect-square rounded-lg border-2 p-2 transition-all duration-200 cursor-pointer',
          day.isCurrentMonth
            ? 'border-neutral-200 bg-white hover:border-primary-500 hover:shadow-md'
            : 'border-neutral-100 bg-neutral-50 opacity-40',
          day.isToday && 'border-primary-500 bg-primary-50 ring-2 ring-primary-300',
          day.events.length > 0 && day.isCurrentMonth && 'font-semibold'
        ]"
        @click="handleDayClick(day)"
      >
        <!-- Day Number -->
        <div
          :class="[
            'text-sm md:text-base',
            day.isToday ? 'text-primary-500 font-bold' : 'text-neutral-700',
            !day.isCurrentMonth && 'text-neutral-400'
          ]"
        >
          {{ day.day }}
        </div>

        <!-- Event Indicators -->
        <div v-if="day.events.length > 0 && day.isCurrentMonth" class="mt-1 space-y-1">
          <!-- Show up to 3 event dots/bars -->
          <div
            v-for="(event, eventIndex) in day.events.slice(0, 3)"
            :key="event.id"
            :class="[
              'text-xs truncate px-1 py-0.5 rounded',
              getEventColorClass(event.type)
            ]"
            :title="event.title"
          >
            <span class="hidden md:inline">{{ event.startTime }}</span>
            <span class="md:hidden">•</span>
          </div>

          <!-- More events indicator -->
          <div
            v-if="day.events.length > 3"
            class="text-xs text-primary-500 font-semibold px-1"
          >
            +{{ day.events.length - 3 }} more
          </div>
        </div>
      </div>
    </div>

    <!-- Legend -->
    <div class="mt-6 flex flex-wrap items-center gap-4 text-xs">
      <div class="flex items-center gap-2">
        <div class="w-3 h-3 rounded-full bg-primary-500"></div>
        <span class="text-neutral-600">Concert</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="w-3 h-3 rounded-full bg-primary-600"></div>
        <span class="text-neutral-600">Meet & Greet</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="w-3 h-3 rounded-full bg-primary-500"></div>
        <span class="text-neutral-600">Handshake</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="w-3 h-3 rounded-full bg-primary-700"></div>
        <span class="text-neutral-600">Online</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="w-3 h-3 rounded-full bg-primary-400"></div>
        <span class="text-neutral-600">Other</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { Event, EventType, CalendarDay } from '@/types/event'

// Props
interface Props {
  events: Event[]
  currentMonth: number // 0-11
  currentYear: number
}

const props = defineProps<Props>()

// Emits
const emit = defineEmits<{
  dayClick: [day: CalendarDay]
}>()

// Constants
const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']

// Computed
const calendarDays = computed((): CalendarDay[] => {
  const days: CalendarDay[] = []
  const today = new Date()
  today.setHours(0, 0, 0, 0)

  // First day of current month
  const firstDay = new Date(props.currentYear, props.currentMonth, 1)
  const firstDayOfWeek = firstDay.getDay() // 0-6 (Sunday-Saturday)

  // Last day of current month
  const lastDay = new Date(props.currentYear, props.currentMonth + 1, 0)
  const lastDate = lastDay.getDate()

  // Last day of previous month
  const prevMonthLastDay = new Date(props.currentYear, props.currentMonth, 0)
  const prevMonthLastDate = prevMonthLastDay.getDate()

  // Add days from previous month
  for (let i = firstDayOfWeek - 1; i >= 0; i--) {
    const day = prevMonthLastDate - i
    const date = new Date(props.currentYear, props.currentMonth - 1, day)
    days.push({
      date,
      day,
      month: props.currentMonth - 1,
      year: props.currentYear,
      isCurrentMonth: false,
      isToday: false,
      events: []
    })
  }

  // Add days from current month
  for (let day = 1; day <= lastDate; day++) {
    const date = new Date(props.currentYear, props.currentMonth, day)
    date.setHours(0, 0, 0, 0)
    const isToday = date.getTime() === today.getTime()

    // Find events for this day
    const dayEvents = props.events.filter(event => {
      const eventDate = new Date(event.startDate)
      eventDate.setHours(0, 0, 0, 0)
      return eventDate.getTime() === date.getTime()
    })

    days.push({
      date,
      day,
      month: props.currentMonth,
      year: props.currentYear,
      isCurrentMonth: true,
      isToday,
      events: dayEvents
    })
  }

  // Add days from next month to fill the grid (6 rows x 7 days = 42 cells)
  const remainingCells = 42 - days.length
  for (let day = 1; day <= remainingCells; day++) {
    const date = new Date(props.currentYear, props.currentMonth + 1, day)
    days.push({
      date,
      day,
      month: props.currentMonth + 1,
      year: props.currentYear,
      isCurrentMonth: false,
      isToday: false,
      events: []
    })
  }

  return days
})

// Methods
const getEventColorClass = (type: EventType): string => {
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
      return 'bg-neutral-300 text-white'
  }
}

const handleDayClick = (day: CalendarDay) => {
  if (day.isCurrentMonth) {
    emit('dayClick', day)
  }
}
</script>

<style scoped>
/* Calendar day hover effect */
.calendar-day:hover {
  transform: translateY(-2px);
}

/* Aspect ratio for calendar cells */
.aspect-square {
  aspect-ratio: 1 / 1;
}

/* Ensure calendar cells don't get too small on mobile */
.calendar-day {
  min-height: 80px;
}

@media (min-width: 768px) {
  .calendar-day {
    min-height: 100px;
  }
}

@media (min-width: 1024px) {
  .calendar-day {
    min-height: 120px;
  }
}
</style>
