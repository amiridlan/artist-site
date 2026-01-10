<template>
  <div class="member-search-and-sort flex flex-col md:flex-row gap-4 mb-6">
    <!-- Search Bar -->
    <div class="flex-1 relative">
      <div class="relative">
        <input
          type="text"
          :value="searchQuery"
          @input="handleSearchInput"
          placeholder="Search members by name..."
          class="w-full pl-12 pr-4 py-3 md:py-4 rounded-lg border-2 border-neutral-200 focus:border-primary-500 focus:outline-none text-neutral-800 placeholder-dark-400 transition-colors"
        />
        <svg
          class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-neutral-400"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <!-- Clear Button -->
        <button
          v-if="searchQuery"
          @click="clearSearch"
          class="absolute right-4 top-1/2 transform -translate-y-1/2 text-neutral-400 hover:text-neutral-600 transition-colors"
          aria-label="Clear search"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Sort Dropdown -->
    <div class="md:w-64">
      <div class="relative">
        <select
          :value="sortBy"
          @change="handleSortChange"
          class="w-full pl-12 pr-10 py-3 md:py-4 rounded-lg border-2 border-neutral-200 focus:border-primary-500 focus:outline-none text-neutral-800 appearance-none cursor-pointer transition-colors"
        >
          <option value="name">Sort by Name</option>
          <option value="generation">Sort by Generation</option>
          <option value="birthday">Sort by Birthday</option>
          <option value="joinDate">Sort by Join Date</option>
        </select>
        <svg
          class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-neutral-400 pointer-events-none"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
        </svg>
        <svg
          class="absolute right-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-neutral-400 pointer-events-none"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </div>
    </div>

    <!-- Sort Order Toggle -->
    <button
      @click="toggleSortOrder"
      class="md:w-14 px-4 md:px-0 py-3 md:py-4 rounded-lg border-2 border-neutral-200 hover:border-primary-500 hover:bg-primary-500/10 transition-all flex items-center justify-center gap-2 md:gap-0"
      :aria-label="sortOrder === 'asc' ? 'Sort ascending' : 'Sort descending'"
    >
      <svg
        v-if="sortOrder === 'asc'"
        class="w-5 h-5 text-neutral-600"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4" />
      </svg>
      <svg
        v-else
        class="w-5 h-5 text-neutral-600"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
      </svg>
      <span class="md:hidden text-sm font-medium">
        {{ sortOrder === 'asc' ? 'A-Z' : 'Z-A' }}
      </span>
    </button>
  </div>
</template>

<script setup lang="ts">
import type { MemberFilter } from '@/types/member'

// Props
interface Props {
  searchQuery?: string
  sortBy?: 'name' | 'generation' | 'birthday' | 'joinDate'
  sortOrder?: 'asc' | 'desc'
}

const props = withDefaults(defineProps<Props>(), {
  searchQuery: '',
  sortBy: 'name',
  sortOrder: 'asc',
})

// Emits
const emit = defineEmits<{
  'update:searchQuery': [value: string]
  'update:sortBy': [value: 'name' | 'generation' | 'birthday' | 'joinDate']
  'update:sortOrder': [value: 'asc' | 'desc']
}>()

// Methods
const handleSearchInput = (event: Event) => {
  const target = event.target as HTMLInputElement
  emit('update:searchQuery', target.value)
}

const clearSearch = () => {
  emit('update:searchQuery', '')
}

const handleSortChange = (event: Event) => {
  const target = event.target as HTMLSelectElement
  emit('update:sortBy', target.value as any)
}

const toggleSortOrder = () => {
  emit('update:sortOrder', props.sortOrder === 'asc' ? 'desc' : 'asc')
}
</script>
