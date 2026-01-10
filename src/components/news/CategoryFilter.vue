<template>
  <div class="category-filter mb-8 md:mb-12">
    <!-- Desktop: Horizontal Tabs -->
    <div class="hidden md:flex items-center gap-3 overflow-x-auto scrollbar-hide pb-2">
      <button
        v-for="category in categories"
        :key="category.value"
        @click="selectCategory(category.value)"
        :class="[
          'category-tab px-6 py-3 rounded-full font-outfit font-semibold uppercase text-sm tracking-wide whitespace-nowrap transition-all duration-300',
          activeCategory === category.value
            ? 'bg-primary-500 text-white shadow-neon scale-105'
            : 'bg-white text-neutral-700 hover:bg-neutral-100 hover:scale-105'
        ]"
      >
        {{ category.label }}
        <span
          v-if="activeCategory === category.value && count !== undefined"
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
        aria-label="Filter by category"
        aria-haspopup="true"
        :aria-expanded="isDropdownOpen"
      >
        <span class="flex items-center gap-2">
          <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
          </svg>
          {{ activeCategoryLabel }}
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
            v-for="category in categories"
            :key="category.value"
            @click="selectCategory(category.value)"
            :class="[
              'w-full text-left px-5 py-3 text-sm transition-colors',
              activeCategory === category.value
                ? 'bg-primary-500/10 text-primary-500 font-semibold'
                : 'text-neutral-700 hover:bg-neutral-100'
            ]"
            role="menuitem"
          >
            {{ category.label }}
            <svg
              v-if="activeCategory === category.value"
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
import type { NewsCategory } from '@/types/news'
import { NEWS_CATEGORIES } from '@/utils/constants'

// Props
interface Props {
  activeCategory: NewsCategory
  count?: number
}

const props = defineProps<Props>()

// Emits
const emit = defineEmits<{
  'update:activeCategory': [category: NewsCategory]
}>()

// State
const isDropdownOpen = ref(false)
const dropdownRef = ref<HTMLElement | null>(null)

// Data
const categories = NEWS_CATEGORIES

// Computed
const activeCategoryLabel = computed(() => {
  const category = categories.find(c => c.value === props.activeCategory)
  return category?.label || 'All'
})

// Methods
const selectCategory = (category: NewsCategory) => {
  emit('update:activeCategory', category)
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

/* Category tab hover effect */
.category-tab {
  position: relative;
}

.category-tab::after {
  content: '';
  position: absolute;
  bottom: -2px;
  left: 50%;
  transform: translateX(-50%) scaleX(0);
  width: 80%;
  height: 2px;
  background: #288820;
  transition: transform 0.3s ease;
}

.category-tab:hover::after {
  transform: translateX(-50%) scaleX(1);
}
</style>
