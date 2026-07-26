<template>
  <div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-6">
    <div class="flex items-start gap-3">
      <svg class="w-6 h-6 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
      </svg>

      <div class="flex-1">
        <h3 class="font-semibold text-red-900 mb-2">Scheduling Conflicts Detected</h3>

        <div class="space-y-3">
          <div v-for="(conflict, index) in conflicts" :key="index" class="bg-white rounded-lg p-3 border border-red-100">
            <div class="flex items-start gap-2">
              <span class="px-2 py-0.5 bg-red-100 text-red-800 text-xs rounded font-medium uppercase">
                {{ formatConflictType(conflict.type) }}
              </span>
              <p class="text-sm text-red-800 flex-1">{{ conflict.message }}</p>
            </div>

            <div v-if="conflict.details" class="mt-2 text-xs text-red-700 pl-2 border-l-2 border-red-200">
              <div v-if="conflict.details.member_name">
                Member: <strong>{{ conflict.details.member_name }}</strong>
              </div>
              <div v-if="conflict.details.staff_name">
                Staff: <strong>{{ conflict.details.staff_name }}</strong>
              </div>
              <div v-if="conflict.details.resource_name">
                Resource: <strong>{{ conflict.details.resource_name }}</strong>
              </div>
              <div v-if="conflict.details.conflicting_event_title">
                Conflicts with: <strong>{{ conflict.details.conflicting_event_title }}</strong>
              </div>
            </div>
          </div>
        </div>

        <div v-if="canOverride" class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
          <p class="text-sm text-yellow-900 mb-3">
            <strong>Super Admin Override:</strong> You have permission to override these conflicts.
            The system will log this decision for audit purposes.
          </p>
          <button @click="$emit('override')" class="btn-primary bg-yellow-600 hover:bg-yellow-700 text-sm">
            Override Conflicts & Save Anyway
          </button>
        </div>

        <div v-else class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded-lg">
          <p class="text-sm text-gray-700">
            You do not have permission to override these conflicts.
            Please resolve the conflicts or contact a Super Admin.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

defineProps({
  conflicts: {
    type: Array,
    required: true
  }
})

defineEmits(['override'])

const page = usePage()

const canOverride = computed(() => {
  return page.props.auth?.can?.['override-conflicts'] || false
})

function formatConflictType(type) {
  const types = {
    'artist_double_booking': 'Double Booking',
    'artist_day_off_conflict': 'Day-Off Conflict',
    'staff_availability': 'Staff Conflict',
    'resource_conflict': 'Resource Conflict'
  }
  return types[type] || type
}
</script>
