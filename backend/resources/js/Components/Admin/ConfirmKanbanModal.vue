<template>
  <teleport to="body">
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click="$emit('close')">
      <div class="bg-white dark:bg-gray-900 rounded-xl p-6 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto" @click.stop>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Confirm to Schedule</h3>

        <div class="bg-teal-50 dark:bg-teal-900/20 border border-teal-200 dark:border-teal-800 rounded-lg p-3 mb-6">
          <p class="text-sm text-teal-900 dark:text-teal-300">
            <strong>{{ card.title }}</strong> will be confirmed and a schedule event will be created.
          </p>
        </div>

        <ConflictWarning v-if="conflicts.length" :conflicts="conflicts" @override="handleOverride" />

        <form @submit.prevent="submit" class="space-y-4">
          <!-- Date & Time - Separated -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Start Date & Time -->
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date *</label>
                <DateInput
                  v-model="form.start_date"
                  :minDate="new Date()"
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
                  :minDate="form.start_date || new Date()"
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
            <input v-model="form.venue" type="text" class="input" placeholder="Enter venue location">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Resources (Optional)</label>
            <div class="grid grid-cols-2 gap-2 max-h-32 overflow-y-auto p-2 border border-gray-200 dark:border-gray-700 rounded">
              <label v-for="resource in availableResources" :key="resource.id"
                class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                <input type="checkbox" :value="resource.id" v-model="form.resource_ids" class="rounded text-teal-600 dark:text-teal-500">
                {{ resource.name }}
              </label>
            </div>
          </div>

          <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
            <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Card Details</h4>
            <dl class="text-sm space-y-1">
              <div class="flex">
                <dt class="text-gray-500 dark:text-gray-400 w-24">Type:</dt>
                <dd class="text-gray-900 dark:text-gray-100">{{ formatType(card.type) }}</dd>
              </div>
              <div class="flex" v-if="card.members?.length">
                <dt class="text-gray-500 dark:text-gray-400 w-24">Members:</dt>
                <dd class="text-gray-900 dark:text-gray-100">{{ card.members.map(m => m.nameEnglish).join(', ') }}</dd>
              </div>
              <div class="flex" v-if="card.description">
                <dt class="text-gray-500 dark:text-gray-400 w-24">Description:</dt>
                <dd class="text-gray-900 dark:text-gray-100">{{ card.description }}</dd>
              </div>
            </dl>
          </div>

          <!-- Actions -->
          <div class="flex gap-3 pt-4">
            <button type="submit" :disabled="form.processing" class="btn-primary flex-1">
              {{ form.processing ? 'Confirming...' : 'Confirm & Create Event' }}
            </button>
            <button type="button" @click="$emit('close')" class="btn-secondary flex-1">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </teleport>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import DateInput from './DateInput.vue'
import TimeInput from './TimeInput.vue'
import ConflictWarning from './ConflictWarning.vue'

const props = defineProps({
  card: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['close', 'confirmed'])

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

// Mock resources - in production, fetch from backend
const availableResources = ref([
  { id: 1, name: 'KL Live Performance Hall' },
  { id: 2, name: 'Studio A Recording Room' },
  { id: 4, name: 'Sony A7S III Camera' }
])

const form = useForm({
  start_date: '',
  start_time: '',
  end_date: '',
  end_time: '',
  start_datetime: '', // Combined for backend
  end_datetime: '', // Combined for backend
  venue: '',
  resource_ids: [],
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

  form.post(route('admin.kanban.confirm', props.card.id), {
    preserveScroll: true,
    onSuccess: () => {
      conflicts.value = []
      emit('confirmed')
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

function formatType(type) {
  const types = {
    'artist_performance': 'Artist Performance',
    'artist_appearance': 'Artist Appearance',
    'content_filming': 'Content Filming',
    'practice_day': 'Practice Day',
    'social_media_post': 'Social Media Post'
  }
  return types[type] || type
}
</script>
