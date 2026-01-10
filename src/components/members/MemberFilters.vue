<template>
  <div class="member-filters space-y-4 md:space-y-6 mb-8">
    <!-- Filter Tabs (Desktop) -->
    <div class="hidden md:flex flex-wrap items-center gap-3">

      <!-- Generation Filter -->
      <div class="flex items-center gap-2">
        <label class="text-sm font-semibold text-neutral-600 uppercase tracking-wide">Gen:</label>
        <div class="flex gap-2">
          <button
            v-for="gen in generations"
            :key="gen.value"
            @click="updateFilter('generation', gen.value)"
            :class="[
              'px-4 py-2 rounded-lg font-medium text-sm transition-all duration-300',
              filters.generation === gen.value
                ? 'bg-primary-500 text-white shadow-lg scale-105'
                : 'bg-white text-neutral-700 hover:bg-primary-500/10 hover:text-primary-500'
            ]"
          >
            {{ gen.label }}
          </button>
        </div>
      </div>

      <!-- Status Filter -->
      <div class="flex items-center gap-2">
        <label class="text-sm font-semibold text-neutral-600 uppercase tracking-wide">Status:</label>
        <div class="flex gap-2">
          <button
            v-for="status in statuses"
            :key="status.value"
            @click="updateFilter('status', status.value)"
            :class="[
              'px-4 py-2 rounded-lg font-medium text-sm transition-all duration-300',
              filters.status === status.value
                ? 'bg-primary-500 text-white shadow-lg scale-105'
                : 'bg-white text-neutral-700 hover:bg-primary-500/10 hover:text-primary-500'
            ]"
          >
            {{ status.label }}
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Filters -->
    <div class="md:hidden space-y-3">

      <!-- Generation Select -->
      <div>
        <label class="block text-sm font-semibold text-neutral-600 uppercase tracking-wide mb-2">Generation</label>
        <select
          :value="filters.generation"
          @change="updateFilter('generation', ($event.target as HTMLSelectElement).value)"
          class="w-full px-4 py-3 rounded-lg border-2 border-neutral-200 focus:border-primary-500 focus:outline-none"
        >
          <option v-for="gen in generations" :key="gen.value" :value="gen.value">
            {{ gen.label }}
          </option>
        </select>
      </div>

      <!-- Status Select -->
      <div>
        <label class="block text-sm font-semibold text-neutral-600 uppercase tracking-wide mb-2">Status</label>
        <select
          :value="filters.status"
          @change="updateFilter('status', ($event.target as HTMLSelectElement).value)"
          class="w-full px-4 py-3 rounded-lg border-2 border-neutral-200 focus:border-primary-500 focus:outline-none"
        >
          <option v-for="status in statuses" :key="status.value" :value="status.value">
            {{ status.label }}
          </option>
        </select>
      </div>
    </div>

    <!-- Active Filters Count & Reset -->
    <div v-if="activeFiltersCount > 0" class="flex items-center justify-between">
      <span class="text-sm text-neutral-600">
        {{ filteredCount }} member{{ filteredCount !== 1 ? 's' : '' }} found
      </span>
      <button
        @click="resetFilters"
        class="text-sm text-primary-500 hover:text-primary-600 font-medium flex items-center gap-1"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
        Clear Filters
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { MemberFilter, MemberGeneration, MemberStatus } from '@/types/member'

// Props
interface Props {
  filters: MemberFilter
  filteredCount: number
}

const props = defineProps<Props>()

// Emits
const emit = defineEmits<{
  'update:filters': [filters: MemberFilter]
}>()

// Filter Options
// const teams = [
//   { value: 'all', label: 'All' },
//   { value: 'Team K', label: 'Team K' },
//   { value: 'Team L', label: 'Team L' },
//   { value: 'Team P', label: 'Team P' },
//   { value: 'Trainee', label: 'Trainee' },
// ]

const generations = [
  { value: 'all', label: 'All' },
  { value: '1st', label: '1st' },
  { value: '2nd', label: '2nd' },
]

const statuses = [
  { value: 'all', label: 'All' },
  // { value: 'active', label: 'Active' },
  { value: 'graduated', label: 'Graduated' },
  { value: 'on-hiatus', label: 'On Hiatus' },
]

// Computed
const activeFiltersCount = computed(() => {
  let count = 0
  // if (props.filters.team && props.filters.team !== 'all') count++
  if (props.filters.generation && props.filters.generation !== 'all') count++
  if (props.filters.status && props.filters.status !== 'all') count++
  return count
})

// Methods
const updateFilter = (key: keyof MemberFilter, value: string) => {
  emit('update:filters', {
    ...props.filters,
    [key]: value,
  })
}

const resetFilters = () => {
  emit('update:filters', {
    // team: 'all',
    generation: 'all',
    status: 'all',
    searchQuery: props.filters.searchQuery,
    sortBy: props.filters.sortBy,
    sortOrder: props.filters.sortOrder,
  })
}
</script>
