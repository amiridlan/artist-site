<template>
  <article
    class="event-card group relative cursor-pointer bg-white rounded-xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300"
    @click="handleClick"
  >
    <!-- Event Image -->
    <div class="relative h-48 overflow-hidden bg-neutral-900">
      <img
        v-if="event.image"
        :src="event.image"
        :alt="event.title"
        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
        loading="lazy"
      />
      <div v-else class="w-full h-full bg-primary-500 flex items-center justify-center">
        <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
      </div>

      <!-- Gradient Overlay -->
      <div class="absolute inset-0 bg-black/30"></div>

      <!-- Type Badge -->
      <div class="absolute top-3 left-3">
        <span :class="typeClasses" class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide backdrop-blur-sm">
          {{ typeLabel }}
        </span>
      </div>

      <!-- Status Badge -->
      <div class="absolute top-3 right-3">
        <span :class="statusClasses" class="px-3 py-1 rounded-full text-xs font-bold uppercase backdrop-blur-sm">
          {{ statusLabel }}
        </span>
      </div>

      <!-- Featured Badge -->
      <div v-if="event.featured" class="absolute bottom-3 left-3">
        <span class="px-2 py-1 bg-primary-400 text-neutral-900 rounded-full text-xs font-bold uppercase flex items-center gap-1">
          <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
          </svg>
          Featured
        </span>
      </div>
    </div>

    <!-- Content Section -->
    <div class="p-5">
      <!-- Date & Time -->
      <div class="flex items-center gap-2 text-primary-500 font-semibold text-sm mb-3">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <span>{{ formattedDate }}</span>
        <span class="text-neutral-400">•</span>
        <span>{{ event.startTime }}</span>
      </div>

      <!-- Title -->
      <h3 class="font-outfit font-bold text-lg mb-2 line-clamp-2 group-hover:text-primary-500 transition-colors duration-300">
        {{ event.title }}
      </h3>

      <!-- Description -->
      <p class="text-neutral-600 text-sm mb-4 line-clamp-2 leading-relaxed">
        {{ event.description }}
      </p>

      <!-- Location -->
      <div class="flex items-start gap-2 text-sm text-neutral-700 mb-4">
        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        <div class="line-clamp-2">
          <div class="font-medium">{{ event.location.venue }}</div>
          <div class="text-neutral-500 text-xs">{{ locationString }}</div>
        </div>
      </div>

      <!-- Ticket Info -->
      <div class="flex items-center justify-between pt-4 border-t border-neutral-200">
        <!-- Price or Free -->
        <div v-if="event.ticket.freeEvent" class="text-accent-green font-bold text-lg">
          FREE
        </div>
        <div v-else-if="event.ticket.soldOut" class="text-red-500 font-bold text-sm uppercase">
          Sold Out
        </div>
        <div v-else-if="event.ticket.price" class="text-neutral-800">
          <span class="text-sm text-neutral-500">From</span>
          <span class="font-bold text-lg ml-1">{{ event.ticket.price.currency }} {{ event.ticket.price.min }}</span>
        </div>
        <div v-else class="text-neutral-500 text-sm">
          TBA
        </div>

        <!-- CTA Button -->
        <button
          v-if="event.status === 'upcoming' && event.ticket.available && !event.ticket.soldOut"
          class="px-4 py-2 bg-primary-500 text-white rounded-full text-sm font-semibold hover:scale-105 transition-transform"
          @click.stop="handleTicketClick"
        >
          Get Tickets
        </button>
        <button
          v-else-if="event.status === 'completed'"
          class="px-4 py-2 bg-neutral-200 text-neutral-600 rounded-full text-sm font-medium cursor-default"
          @click.stop
        >
          Past Event
        </button>
      </div>

      <!-- Tags -->
      <div v-if="event.tags && event.tags.length > 0" class="mt-4 flex flex-wrap gap-1">
        <span
          v-for="tag in event.tags.slice(0, 3)"
          :key="tag"
          class="px-2 py-1 bg-neutral-100 text-neutral-600 rounded text-xs"
        >
          #{{ tag }}
        </span>
      </div>
    </div>

    <!-- Hover Indicator -->
    <div class="absolute bottom-0 left-0 right-0 h-1 bg-primary-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
  </article>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { format } from 'date-fns'
import type { Event, EventType, EventStatus } from '@/types/event'

// Props
interface Props {
  event: Event
}

const props = defineProps<Props>()

// Emits
const emit = defineEmits<{
  click: [event: Event]
  ticketClick: [event: Event]
}>()

// Computed
const formattedDate = computed(() => {
  return format(new Date(props.event.startDate), 'EEE, MMM d, yyyy')
})

const locationString = computed(() => {
  const loc = props.event.location
  if (loc.city === 'Online') {
    return 'Online Event'
  }
  return `${loc.city}, ${loc.country}`
})

const typeLabel = computed(() => {
  const labels: Record<EventType, string> = {
    'all': 'All',
    'concert': 'Concert',
    'meet-greet': 'Meet & Greet',
    'handshake': 'Handshake',
    'online': 'Online',
    'other': 'Event'
  }
  return labels[props.event.type]
})

const typeClasses = computed(() => {
  const baseClasses = 'text-white'

  switch (props.event.type) {
    case 'concert':
      return `${baseClasses} bg-primary-500/90`
    case 'meet-greet':
      return `${baseClasses} bg-primary-600/90`
    case 'handshake':
      return `${baseClasses} bg-primary-500/90`
    case 'online':
      return `${baseClasses} bg-primary-700/90`
    case 'other':
      return `${baseClasses} bg-primary-400/90 text-neutral-900`
    default:
      return `${baseClasses} bg-neutral-600/90`
  }
})

const statusLabel = computed(() => {
  const labels: Record<EventStatus, string> = {
    'upcoming': 'Upcoming',
    'ongoing': 'Now',
    'completed': 'Past',
    'cancelled': 'Cancelled'
  }
  return labels[props.event.status]
})

const statusClasses = computed(() => {
  switch (props.event.status) {
    case 'upcoming':
      return 'bg-accent-green/90 text-white'
    case 'ongoing':
      return 'bg-red-500/90 text-white animate-pulse'
    case 'completed':
      return 'bg-neutral-600/90 text-white'
    case 'cancelled':
      return 'bg-red-600/90 text-white'
    default:
      return 'bg-neutral-600/90 text-white'
  }
})

// Methods
const handleClick = () => {
  emit('click', props.event)
}

const handleTicketClick = () => {
  emit('ticketClick', props.event)
}
</script>

<style scoped>
/* Line clamp utilities */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Card hover animation */
.event-card {
  transform: translateY(0);
}

.event-card:hover {
  transform: translateY(-4px);
}

/* Accent green color */
.text-accent-green {
  color: #00ff9f;
}
</style>
