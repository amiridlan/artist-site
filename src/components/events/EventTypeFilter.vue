<template>
  <div class="event-type-filter mb-8 md:mb-12">
    <!-- Desktop: Horizontal Tabs -->
    <div class="hidden md:flex items-center gap-3 overflow-x-auto scrollbar-hide pb-2">
      <button
        v-for="type in eventTypes"
        :key="type.value"
        @click="selectType(type.value)"
        :class="[
          'type-tab px-6 py-3 rounded-full font-outfit font-semibold uppercase text-sm tracking-wide whitespace-nowrap transition-all duration-300',
          activeType === type.value
            ? 'bg-primary-500 text-white shadow-neon scale-105'
            : 'bg-white text-neutral-700 hover:bg-neutral-100 hover:scale-105'
        ]"
      >
        {{ type.label }}
        <span
          v-if="activeType === type.value && count !== undefined"
          class="ml-2 px-2 py-0.5 bg-white/20 rounded-full text-xs"
        >
          {{ count }}
        </span>
      </button>
    </div>

    <!-- Mobile: Dropdown -->
    <div class="md:hidden relative" ref="dropdownRef">
      <button
        @click="toggleDropdown"
        class="w-full flex items-center justify-between px-5 py-3 bg-white rounded-lg shadow-md font-outfit font-semibold text-neutral-700"
        aria-label="Filter by event type"
        aria-haspopup="true"
        :aria-expanded="isDropdownOpen"
      >
        <span class="flex items-center gap-2">
          <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          {{ activeTypeLabel }}
        </span>
        <svg
          class="w-5 h-5 transition-transform duration-300"
          :class="{ 'rotate-180': isDropdownOpen }"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>

      <!-- Dropdown Menu -->
      <transition name="dropdown">
        <div
          v-if="isDropdownOpen"
          class="absolute z-20 w-full mt-2 bg-white rounded-lg shadow-xl border border-neutral-200 py-2 animate-slide-down"
          role="menu"
        >
          <button
            v-for="type in eventTypes"
            :key="type.value"
            @click="selectType(type.value)"
            :class="[
              'w-full text-left px-5 py-3 text-sm transition-colors',
              activeType === type.value
                ? 'bg-primary-500/10 text-primary-500 font-semibold'
                : 'text-neutral-700 hover:bg-neutral-100'
            ]"
            role="menuitem"
          >
            {{ type.label }}
            <svg
              v-if="activeType === type.value"
              class="inline-block w-4 h-4 ml-2"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fill-rule="evenodd"
                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                clip-rule="evenodd"
              />
            </svg>
          </button>
        </div>
      </transition>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { onClickOutside } from '@vueuse/core'
import type { EventType } from '@/types/event'
import { EVENT_TYPES } from '@/utils/constants'

// Props
interface Props {
  activeType: EventType
  count?: number
}

const props = defineProps<Props>()

// Emits
const emit = defineEmits<{
  'update:activeType': [type: EventType]
}>()

// State
const isDropdownOpen = ref(false)
const dropdownRef = ref<HTMLElement | null>(null)

// Data
const eventTypes = EVENT_TYPES

// Computed
const activeTypeLabel = computed(() => {
  const type = eventTypes.find(t => t.value === props.activeType)
  return type?.label || 'All'
})

// Methods
const selectType = (type: EventType) => {
  emit('update:activeType', type)
  isDropdownOpen.value = false
}

const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value
}

// Close dropdown when clicking outside
onClickOutside(dropdownRef, () => {
  isDropdownOpen.value = false
})
</script>

<style scoped>
/* Dropdown transition */
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Type tab hover effect */
.type-tab {
  position: relative;
}

.type-tab::after {
  content: '';
  position: absolute;
  bottom: -2px;
  left: 50%;
  transform: translateX(-50%) scaleX(0);
  width: 80%;
  height: 2px;
  background: #288800;
  transition: transform 0.3s ease;
}

.type-tab:hover::after {
  transform: translateX(-50%) scaleX(1);
}

/* Hide scrollbar */
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

/* Shadow effect for active tab */
.shadow-neon {
  box-shadow: 0 0 20px rgba(0, 255, 159, 0.3), 0 0 40px rgba(176, 38, 255, 0.2);
}
</style>
