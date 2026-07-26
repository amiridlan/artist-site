<template>
  <AdminLayout :title="`Edit ${event.title}`">
    <template #actions>
      <Link :href="route('admin.schedule-events.index')" class="btn-secondary text-sm">
        Back to List
      </Link>
    </template>

    <ConflictWarning v-if="conflicts.length" :conflicts="conflicts" @override="handleOverride" />

    <!-- Existing Conflict Logs -->
    <div v-if="event.conflictLogs?.length" class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-4 mb-6">
      <h3 class="font-semibold text-yellow-900 dark:text-yellow-300 mb-2">Previous Conflicts</h3>
      <div class="space-y-2">
        <div v-for="log in event.conflictLogs" :key="log.id" class="text-sm">
          <span class="font-medium text-yellow-900 dark:text-yellow-300">{{ log.conflictType }}:</span>
          <span class="text-yellow-800 dark:text-yellow-400"> {{ log.details.message || 'Conflict detected' }}</span>
          <span v-if="log.resolution === 'overridden'" class="ml-2 px-2 py-0.5 bg-yellow-200 dark:bg-yellow-800 text-yellow-900 dark:text-yellow-200 text-xs rounded">
            Overridden
          </span>
        </div>
      </div>
    </div>

    <form @submit.prevent="submit" class="max-w-5xl">
      <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6 space-y-6">
        <!-- Basic Info -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title *</label>
          <input v-model="form.title" type="text" class="input" required>
          <p v-if="form.errors.title" class="text-red-500 dark:text-red-400 text-sm mt-1">{{ form.errors.title }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
          <textarea v-model="form.description" class="input" rows="3"></textarea>
        </div>

        <!-- Date & Time - Separated -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Start Date & Time -->
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date *</label>
              <DateInput
                v-model="form.start_date"
                placeholder="Select start date"
                :error="form.errors.start_date || form.errors.start_datetime"
                required
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Time *</label>
              <TimeInput
                v-model="form.start_time"
                placeholder="Select start time"
                :error="form.errors.start_time || form.errors.start_datetime"
                required
              />
            </div>
          </div>

          <!-- End Date & Time -->
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date *</label>
              <DateInput
                v-model="form.end_date"
                :minDate="form.start_date"
                placeholder="Select end date"
                :error="form.errors.end_date || form.errors.end_datetime"
                helper-text="Must be same day or after start date"
                required
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Time *</label>
              <TimeInput
                v-model="form.end_time"
                placeholder="Select end time"
                :error="form.errors.end_time || form.errors.end_datetime"
                required
              />
            </div>
          </div>
        </div>

        <!-- Timezone Indicator -->
        <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 -mt-2">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>{{ timezoneDisplay }}</span>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Venue</label>
          <input v-model="form.venue" type="text" class="input">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status *</label>
          <select v-model="form.status" class="input" required>
            <option value="draft">Draft</option>
            <option value="confirmed">Confirmed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>

        <!-- Members -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Members</label>
          <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
            <label v-for="member in members" :key="member.id" class="flex items-center gap-2 p-2 border border-gray-200 dark:border-gray-700 rounded hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer">
              <input type="checkbox" :value="member.id" v-model="form.member_ids" class="rounded text-teal-600 dark:text-teal-500">
              <span class="text-sm text-gray-700 dark:text-gray-300">{{ member.name_english }}</span>
            </label>
          </div>
        </div>

        <!-- Staff -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Staff</label>
          <div class="grid grid-cols-2 gap-2">
            <label v-for="s in staff" :key="s.id" class="flex items-center gap-2 p-2 border border-gray-200 dark:border-gray-700 rounded hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer">
              <input type="checkbox" :value="s.id" v-model="form.staff_ids" class="rounded text-teal-600 dark:text-teal-500">
              <span class="text-sm text-gray-700 dark:text-gray-300">{{ s.name }} <span class="text-xs text-gray-500 dark:text-gray-400">({{ s.email }})</span></span>
            </label>
          </div>
        </div>

        <!-- Resources -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Resources</label>
          <div class="grid grid-cols-2 gap-2">
            <label v-for="resource in resources" :key="resource.id" class="flex items-center gap-2 p-2 border border-gray-200 dark:border-gray-700 rounded hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer">
              <input type="checkbox" :value="resource.id" v-model="form.resource_ids" class="rounded text-teal-600 dark:text-teal-500">
              <span class="text-sm text-gray-700 dark:text-gray-300">{{ resource.name }} <span class="text-xs text-gray-500 dark:text-gray-400">({{ resource.type }})</span></span>
            </label>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-3 pt-4">
          <button type="submit" :disabled="form.processing" class="btn-primary">
            {{ form.processing ? 'Saving...' : 'Save Changes' }}
          </button>
          <Link :href="route('admin.schedule-events.index')" class="btn-secondary">
            Cancel
          </Link>
        </div>
      </div>
    </form>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DateInput from '@/Components/Admin/DateInput.vue'
import TimeInput from '@/Components/Admin/TimeInput.vue'
import ConflictWarning from '@/Components/Admin/ConflictWarning.vue'

const props = defineProps({
  event: Object,
  members: Array,
  staff: Array,
  resources: Array
})

const page = usePage()
const conflicts = ref(page.props.flash?.conflicts || [])

// Timezone display
const timezoneDisplay = computed(() => {
  const offset = -(new Date().getTimezoneOffset())
  const hours = Math.floor(Math.abs(offset) / 60)
  const minutes = Math.abs(offset) % 60
  const sign = offset >= 0 ? '+' : '-'
  const utcOffset = `UTC${sign}${hours}${minutes ? ':' + String(minutes).padStart(2, '0') : ''}`

  try {
    const timezoneName = Intl.DateTimeFormat().resolvedOptions().timeZone
    return `${timezoneName} (${utcOffset})`
  } catch (e) {
    return utcOffset
  }
})

// Split existing datetime into date and time
const splitDateTime = (datetime) => {
  if (!datetime) return { date: '', time: '' }
  const formatted = datetime.substring(0, 16) // "YYYY-MM-DDTHH:mm"
  const [date, time] = formatted.split('T')
  return { date, time }
}

const startDT = splitDateTime(props.event.startDatetime)
const endDT = splitDateTime(props.event.endDatetime)

const form = useForm({
  title: props.event.title,
  description: props.event.description,
  start_date: startDT.date,
  start_time: startDT.time,
  end_date: endDT.date,
  end_time: endDT.time,
  start_datetime: '', // Combined for backend
  end_datetime: '', // Combined for backend
  venue: props.event.venue,
  status: props.event.status,
  member_ids: props.event.members?.map(m => m.id) || [],
  staff_ids: props.event.staff?.map(s => s.id) || [],
  resource_ids: props.event.resources?.map(r => r.id) || [],
  override_conflicts: false
})

// Watch for date/time changes and combine them
watch([() => form.start_date, () => form.start_time], () => {
  if (form.start_date && form.start_time) {
    form.start_datetime = `${form.start_date}T${form.start_time}`
  }
})

watch([() => form.end_date, () => form.end_time], () => {
  if (form.end_date && form.end_time) {
    form.end_datetime = `${form.end_date}T${form.end_time}`
  }
})

function submit() {
  // Ensure datetime fields are populated
  if (form.start_date && form.start_time) {
    form.start_datetime = `${form.start_date}T${form.start_time}`
  }
  if (form.end_date && form.end_time) {
    form.end_datetime = `${form.end_date}T${form.end_time}`
  }

  form.put(route('admin.schedule-events.update', props.event.id), {
    preserveScroll: true,
    onSuccess: () => {
      conflicts.value = []
    },
    onError: (errors) => {
      if (page.props.flash?.conflicts) {
        conflicts.value = page.props.flash.conflicts
      }
    }
  })
}

function handleOverride() {
  form.override_conflicts = true
  submit()
}
</script>
