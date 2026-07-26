<template>
  <AdminLayout title="Schedule Events">
    <template #actions>
      <div class="flex gap-3">
        <Link :href="route('admin.calendar.index')" class="btn-secondary text-sm">
          Calendar View
        </Link>
        <div class="relative" v-if="canCreateAny">
          <button @click="showCreateMenu = !showCreateMenu" class="btn-primary text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Event
          </button>

          <!-- Dropdown menu -->
          <div v-if="showCreateMenu" class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10">
            <Link v-for="(canCreate, type) in createPermissions" :key="type"
              v-if="canCreate"
              :href="route('admin.schedule-events.create', { type })"
              class="block px-4 py-2 text-sm hover:bg-gray-50 transition-colors"
              @click="showCreateMenu = false">
              {{ eventTypeLabels[type] }}
            </Link>
          </div>
        </div>
      </div>
    </template>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
      <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <select v-model="form.type" class="input text-sm">
          <option value="">All Types</option>
          <option v-for="(label, value) in eventTypeLabels" :key="value" :value="value">
            {{ label }}
          </option>
        </select>

        <select v-model="form.status" class="input text-sm">
          <option value="">All Status</option>
          <option value="draft">Draft</option>
          <option value="confirmed">Confirmed</option>
          <option value="cancelled">Cancelled</option>
        </select>

        <DateInput v-model="form.start_date" placeholder="Start Date" />
        <DateInput v-model="form.end_date" placeholder="End Date" />

        <button type="submit" class="btn-primary text-sm">Apply Filters</button>
      </form>
    </div>

    <!-- Events table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Members</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="event in events.data" :key="event.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="font-medium text-gray-900">{{ event.title }}</div>
              <div v-if="event.venue" class="text-sm text-gray-500">{{ event.venue }}</div>
            </td>
            <td class="px-6 py-4">
              <span class="text-sm text-gray-700">{{ eventTypeLabels[event.type] }}</span>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm text-gray-900">{{ formatDate(event.startDatetime) }}</div>
              <div class="text-xs text-gray-500">{{ formatTime(event.startDatetime) }}</div>
            </td>
            <td class="px-6 py-4">
              <span class="px-2 py-1 text-xs rounded-full" :class="statusClass(event.status)">
                {{ event.status }}
              </span>
            </td>
            <td class="px-6 py-4">
              <div class="flex flex-wrap gap-1">
                <span v-for="member in event.members?.slice(0, 3)" :key="member.id"
                  class="px-2 py-0.5 bg-teal-100 text-teal-700 text-xs rounded">
                  {{ member.nameEnglish }}
                </span>
                <span v-if="event.members?.length > 3" class="text-xs text-gray-500">
                  +{{ event.members.length - 3 }}
                </span>
              </div>
            </td>
            <td class="px-6 py-4 text-right space-x-2">
              <Link :href="route('admin.schedule-events.edit', event.id)" class="text-teal-600 hover:text-teal-800 text-sm">
                Edit
              </Link>
              <button @click="deleteEvent(event)" class="text-red-600 hover:text-red-800 text-sm">
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div v-if="events.data?.length" class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
        <div class="text-sm text-gray-500">
          Showing {{ events.from }} to {{ events.to }} of {{ events.total }} events
        </div>
        <div class="flex gap-2">
          <Link v-for="link in events.links" :key="link.label"
            :href="link.url"
            :class="[
              'px-3 py-1 text-sm rounded border',
              link.active ? 'bg-teal-600 text-white border-teal-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
            ]"
            v-html="link.label">
          </Link>
        </div>
      </div>

      <!-- Empty state -->
      <div v-else class="px-6 py-12 text-center text-gray-500">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <p class="mt-2">No events found</p>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DateInput from '@/Components/Admin/DateInput.vue'

const props = defineProps({
  events: Object,
  filters: Object
})

const showCreateMenu = ref(false)

const form = useForm({
  type: props.filters?.type || '',
  status: props.filters?.status || '',
  start_date: props.filters?.start_date || '',
  end_date: props.filters?.end_date || ''
})

const eventTypeLabels = {
  'artist_performance': 'Artist Performance',
  'artist_appearance': 'Artist Appearance',
  'content_filming': 'Content Filming',
  'practice_day': 'Practice Day',
  'day_off': 'Day Off',
  'staff_event': 'Staff Event',
  'social_media_post': 'Social Media Post'
}

// Mock permissions - in real app, get from page.props.auth.can
const createPermissions = computed(() => ({
  'artist_performance': true,
  'artist_appearance': true,
  'content_filming': true,
  'practice_day': true,
  'day_off': true,
  'staff_event': true,
  'social_media_post': true
}))

const canCreateAny = computed(() => Object.values(createPermissions.value).some(v => v))

function applyFilters() {
  form.get(route('admin.schedule-events.index'), {
    preserveState: true,
    preserveScroll: true
  })
}

function deleteEvent(event) {
  if (confirm(`Delete "${event.title}"?`)) {
    router.delete(route('admin.schedule-events.destroy', event.id))
  }
}

function statusClass(status) {
  const classes = {
    draft: 'bg-gray-100 text-gray-700',
    confirmed: 'bg-green-100 text-green-700',
    cancelled: 'bg-red-100 text-red-700'
  }
  return classes[status] || classes.draft
}

function formatDate(datetime) {
  return new Date(datetime).toLocaleDateString('en-MY', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

function formatTime(datetime) {
  return new Date(datetime).toLocaleTimeString('en-MY', {
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>
