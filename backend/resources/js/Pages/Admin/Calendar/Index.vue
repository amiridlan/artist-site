<template>
  <AdminLayout title="Calendar">
    <template #actions>
      <Link :href="route('admin.schedule-events.index')" class="btn-secondary text-sm">
        List View
      </Link>
      <Link :href="route('admin.schedule-events.create')" class="btn-primary text-sm ml-2">
        + New Event
      </Link>
    </template>

    <div class="flex gap-6">
      <!-- Left Sidebar - Filters -->
      <div class="w-64 flex-shrink-0 space-y-4">
        <!-- Quick View Presets -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-4">
          <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">Quick Views</h3>
          <div class="space-y-1">
            <button
              v-for="preset in quickViewPresets"
              :key="preset.id"
              @click="applyPreset(preset.id)"
              class="w-full text-left px-3 py-2 text-sm rounded-lg transition-colors"
              :class="activePreset === preset.id
                ? 'bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300 font-medium'
                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'"
            >
              {{ preset.label }}
            </button>
          </div>
        </div>

        <!-- Event Type Toggles -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-4">
          <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Event Types</h3>
            <button @click="toggleAll" class="text-xs text-teal-600 dark:text-teal-400 hover:text-teal-800 dark:hover:text-teal-300">
              {{ allVisible ? 'Hide All' : 'Show All' }}
            </button>
          </div>
          <div class="space-y-2">
            <label
              v-for="(label, value) in eventTypes"
              :key="value"
              class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 px-2 py-1.5 rounded transition-colors"
            >
              <input
                type="checkbox"
                :value="value"
                v-model="visibleTypes"
                @change="savePreferences"
                class="rounded text-teal-600 dark:text-teal-500 focus:ring-teal-500 dark:focus:ring-teal-400 border-gray-300 dark:border-gray-600 dark:bg-gray-800"
              />
              <span class="w-3 h-3 rounded-full flex-shrink-0" :style="{ backgroundColor: getEventColor(value) }"></span>
              <span class="text-sm text-gray-700 dark:text-gray-300">{{ label }}</span>
            </label>
          </div>
        </div>

        <!-- Additional Filters -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-4">
          <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">Filters</h3>

          <div class="space-y-3">
            <!-- Status Filter -->
            <div>
              <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Status</label>
              <select v-model="filters.status" @change="applyFilters" class="input text-sm">
                <option value="">All Statuses</option>
                <option value="draft">Draft</option>
                <option value="confirmed">Confirmed</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>

            <!-- Member Filter -->
            <div v-if="members.length">
              <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Member</label>
              <select v-model="filters.member" @change="applyFilters" class="input text-sm">
                <option value="">All Members</option>
                <option v-for="member in members" :key="member.id" :value="member.id">
                  {{ member.name }}
                </option>
              </select>
            </div>

            <!-- My Events Only Toggle -->
            <label class="flex items-center gap-2 cursor-pointer">
              <input
                type="checkbox"
                v-model="filters.myEventsOnly"
                @change="applyFilters"
                class="rounded text-teal-600 dark:text-teal-500 focus:ring-teal-500 dark:focus:ring-teal-400 border-gray-300 dark:border-gray-600 dark:bg-gray-800"
              />
              <span class="text-sm text-gray-700 dark:text-gray-300">My Events Only</span>
            </label>

            <!-- Reset Filters -->
            <button
              @click="resetFilters"
              class="w-full text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 py-1.5 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
            >
              Reset Filters
            </button>
          </div>
        </div>
      </div>

      <!-- Main Calendar -->
      <div class="flex-1 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6">
        <FullCalendar ref="calendar" :options="calendarOptions" />
      </div>
    </div>

    <!-- Event Detail Modal -->
    <teleport to="body">
      <div v-if="selectedEvent" class="fixed inset-0 bg-black bg-opacity-50 dark:bg-black dark:bg-opacity-70 flex items-center justify-center z-50 backdrop-blur-sm" @click="selectedEvent = null">
        <div class="bg-white dark:bg-gray-900 rounded-xl p-6 max-w-md w-full mx-4 border border-gray-200 dark:border-gray-800 shadow-xl" @click.stop>
          <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">{{ selectedEvent.title }}</h3>

          <div class="space-y-3 text-sm">
            <div>
              <span class="text-gray-500 dark:text-gray-400">Type:</span>
              <span class="ml-2 font-medium text-gray-900 dark:text-gray-100">{{ eventTypes[selectedEvent.extendedProps.type] }}</span>
            </div>
            <div>
              <span class="text-gray-500 dark:text-gray-400">Status:</span>
              <span class="ml-2 capitalize font-medium text-gray-900 dark:text-gray-100">{{ selectedEvent.extendedProps.status }}</span>
            </div>
            <div>
              <span class="text-gray-500 dark:text-gray-400">Time:</span>
              <span class="ml-2 text-gray-900 dark:text-gray-100">{{ formatDateTime(selectedEvent.start) }} - {{ formatDateTime(selectedEvent.end) }}</span>
            </div>
            <div v-if="selectedEvent.extendedProps.venue">
              <span class="text-gray-500 dark:text-gray-400">Venue:</span>
              <span class="ml-2 text-gray-900 dark:text-gray-100">{{ selectedEvent.extendedProps.venue }}</span>
            </div>
            <div v-if="selectedEvent.extendedProps.description">
              <span class="text-gray-500 dark:text-gray-400">Description:</span>
              <p class="mt-1 text-gray-700 dark:text-gray-300">{{ selectedEvent.extendedProps.description }}</p>
            </div>
            <div v-if="selectedEvent.extendedProps.members?.length">
              <span class="text-gray-500 dark:text-gray-400">Members:</span>
              <div class="mt-1 flex flex-wrap gap-1">
                <span v-for="member in selectedEvent.extendedProps.members" :key="member"
                  class="px-2 py-1 bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300 text-xs rounded">
                  {{ member }}
                </span>
              </div>
            </div>
            <div v-if="selectedEvent.extendedProps.createdBy">
              <span class="text-gray-500 dark:text-gray-400">Created by:</span>
              <span class="ml-2 text-gray-900 dark:text-gray-100">{{ selectedEvent.extendedProps.createdBy }}</span>
            </div>
            <div v-if="selectedEvent.extendedProps.canEdit === false" class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded px-3 py-2">
              <p class="text-xs text-yellow-800 dark:text-yellow-300">
                🔒 You can only view this event. Only the creator can edit or delete it.
              </p>
            </div>
          </div>

          <div class="mt-6 flex gap-3">
            <Link
              v-if="selectedEvent.extendedProps.canEdit"
              :href="route('admin.schedule-events.edit', selectedEvent.id)"
              class="btn-primary text-sm flex-1"
            >
              Edit Event
            </Link>
            <button @click="selectedEvent = null" class="btn-secondary text-sm flex-1">
              Close
            </button>
          </div>
        </div>
      </div>
    </teleport>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import listPlugin from '@fullcalendar/list'

const props = defineProps({
  eventTypes: Object,
  members: Array
})

const page = usePage()
const calendar = ref(null)
const selectedEvent = ref(null)
const activePreset = ref('all')

// Load saved preferences from localStorage
const savedPrefs = JSON.parse(localStorage.getItem('calendarPreferences') || '{}')

const visibleTypes = ref(savedPrefs.visibleTypes || Object.keys(props.eventTypes))
const filters = ref({
  status: savedPrefs.status || '',
  member: savedPrefs.member || '',
  myEventsOnly: savedPrefs.myEventsOnly || false
})

// Quick View Presets
const quickViewPresets = [
  { id: 'all', label: 'All Events' },
  { id: 'my-events', label: 'My Events' },
  { id: 'marketing', label: 'Marketing Only' },
  { id: 'events', label: 'Events Only' },
  { id: 'artists', label: 'Artist Events' }
]

const allVisible = computed(() => visibleTypes.value.length === Object.keys(props.eventTypes).length)

const calendarOptions = {
  plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin, listPlugin],
  initialView: 'dayGridMonth',
  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
  },
  editable: false,
  selectable: true,
  selectMirror: true,
  dayMaxEvents: true,
  weekends: true,
  events: fetchEvents,
  eventClick: handleEventClick,
  height: 'auto'
}

async function fetchEvents(info) {
  const params = new URLSearchParams({
    start: info.startStr,
    end: info.endStr,
    types: visibleTypes.value.join(','),
    ...(filters.value.status && { status: filters.value.status }),
    ...(filters.value.member && { member: filters.value.member }),
    ...(filters.value.myEventsOnly && { my_events: '1' })
  })

  const response = await fetch(route('admin.calendar.events') + '?' + params)
  return await response.json()
}

function applyFilters() {
  savePreferences()
  calendar.value?.getApi().refetchEvents()
}

function applyPreset(presetId) {
  activePreset.value = presetId

  switch (presetId) {
    case 'all':
      visibleTypes.value = Object.keys(props.eventTypes)
      filters.value = { status: '', member: '', myEventsOnly: false }
      break
    case 'my-events':
      visibleTypes.value = Object.keys(props.eventTypes)
      filters.value = { status: '', member: '', myEventsOnly: true }
      break
    case 'marketing':
      visibleTypes.value = ['social_media_post', 'content_filming', 'practice_day']
      filters.value = { status: '', member: '', myEventsOnly: false }
      break
    case 'events':
      visibleTypes.value = ['artist_performance', 'artist_appearance', 'staff_event']
      filters.value = { status: '', member: '', myEventsOnly: false }
      break
    case 'artists':
      visibleTypes.value = ['artist_performance', 'artist_appearance', 'day_off', 'practice_day']
      filters.value = { status: '', member: '', myEventsOnly: false }
      break
  }

  applyFilters()
}

function resetFilters() {
  activePreset.value = 'all'
  visibleTypes.value = Object.keys(props.eventTypes)
  filters.value = { status: '', member: '', myEventsOnly: false }
  applyFilters()
}

function toggleAll() {
  if (allVisible.value) {
    visibleTypes.value = []
  } else {
    visibleTypes.value = Object.keys(props.eventTypes)
  }
  applyFilters()
}

function savePreferences() {
  localStorage.setItem('calendarPreferences', JSON.stringify({
    visibleTypes: visibleTypes.value,
    status: filters.value.status,
    member: filters.value.member,
    myEventsOnly: filters.value.myEventsOnly
  }))
}

function handleEventClick(clickInfo) {
  selectedEvent.value = clickInfo.event
}

function formatDateTime(date) {
  if (!date) return ''
  return new Date(date).toLocaleString('en-MY', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

function getEventColor(type) {
  const colors = {
    artist_performance: '#ef4444',
    artist_appearance: '#f97316',
    content_filming: '#8b5cf6',
    practice_day: '#3b82f6',
    day_off: '#10b981',
    staff_event: '#6366f1',
    social_media_post: '#ec4899'
  }
  return colors[type] || '#6b7280'
}
</script>

<style>
/* FullCalendar theming */
.fc {
  font-family: inherit;
}

.fc-theme-standard td,
.fc-theme-standard th {
  border-color: #e5e7eb;
}

.dark .fc-theme-standard td,
.dark .fc-theme-standard th {
  border-color: #374151;
}

.fc .fc-col-header-cell-cushion,
.fc .fc-daygrid-day-number {
  color: #374151;
}

.dark .fc .fc-col-header-cell-cushion,
.dark .fc .fc-daygrid-day-number {
  color: #d1d5db;
}

.fc .fc-button-primary {
  background-color: #14b8a6;
  border-color: #14b8a6;
}

.fc .fc-button-primary:hover {
  background-color: #0d9488;
  border-color: #0d9488;
}

.fc .fc-button-primary:not(:disabled):active,
.fc .fc-button-primary:not(:disabled).fc-button-active {
  background-color: #0f766e;
  border-color: #0f766e;
}

.dark .fc .fc-button-primary {
  background-color: #14b8a6;
  border-color: #14b8a6;
}

.dark .fc .fc-button-primary:hover {
  background-color: #5eead4;
  border-color: #5eead4;
  color: #111827;
}

.dark .fc .fc-button-primary:not(:disabled):active,
.dark .fc .fc-button-primary:not(:disabled).fc-button-active {
  background-color: #0d9488;
  border-color: #0d9488;
  color: #ffffff;
}

.fc .fc-daygrid-day.fc-day-today {
  background-color: rgba(20, 184, 166, 0.1);
}

.dark .fc .fc-daygrid-day.fc-day-today {
  background-color: rgba(20, 184, 166, 0.15);
}

.fc-event {
  cursor: pointer;
  border: none;
}

.fc .fc-toolbar-title {
  color: #111827;
}

.dark .fc .fc-toolbar-title {
  color: #f3f4f6;
}

/* List view dark mode */
.dark .fc-list-event:hover td {
  background-color: #1f2937;
}

.dark .fc-list-day-cushion {
  background-color: #1f2937;
  color: #f3f4f6;
}

.dark .fc-list-event-title,
.dark .fc-list-event-time {
  color: #d1d5db;
}
</style>
