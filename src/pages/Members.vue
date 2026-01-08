<template>
  <DefaultLayout>
    <div class="members-page min-h-screen pt-24 pb-16">
      <div class="container mx-auto px-4 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8 md:mb-12">
          <h1 class="text-4xl md:text-5xl lg:text-6xl font-outfit font-bold mb-4">
            <span class="text-gradient">Our Members</span>
          </h1>
          <p class="text-lg md:text-xl text-dark-600 max-w-2xl">
            Meet the talented individuals who make KLP48 Malaysia's premier idol group. {{ totalMembers }} members across {{ teams.length - 1 }} teams.
          </p>
        </div>

        <!-- Featured Member -->
        <FeaturedMember
          v-if="featuredMembers.length > 0"
          :member="featuredMembers[0]"
        />

        <!-- Search and Sort -->
        <MemberSearch
          v-model:search-query="filters.searchQuery"
          v-model:sort-by="filters.sortBy"
          v-model:sort-order="filters.sortOrder"
        />

        <!-- Filters -->
        <MemberFilters
          :filters="filters"
          :filtered-count="filteredMembers.length"
          @update:filters="updateFilters"
        />

        <!-- Loading Skeletons -->
        <div v-if="isLoading" class="member-grid mb-12">
          <MemberCardSkeleton v-for="n in 12" :key="`skeleton-${n}`" />
        </div>

        <!-- Member Grid -->
        <div v-else-if="paginatedMembers.length > 0" class="member-grid mb-12">
          <MemberCard
            v-for="member in paginatedMembers"
            :key="member.id"
            :member="member"
          />
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-20">
          <svg class="w-24 h-24 mx-auto mb-6 text-dark-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          <h3 class="text-2xl font-outfit font-bold mb-2">No members found</h3>
          <p class="text-dark-600 mb-6">Try adjusting your filters or search query.</p>
          <button
            @click="resetAllFilters"
            class="px-6 py-3 bg-gradient-primary text-white rounded-full font-outfit font-semibold uppercase text-sm tracking-wide hover:scale-105 transition-all"
          >
            Show All Members
          </button>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="flex justify-center items-center gap-2">
          <!-- Previous Button -->
          <button
            @click="goToPage(currentPage - 1)"
            :disabled="currentPage === 1"
            :class="[
              'px-4 py-2 rounded-lg font-medium transition-all',
              currentPage === 1
                ? 'bg-dark-200 text-dark-400 cursor-not-allowed'
                : 'bg-white text-dark-700 hover:bg-primary-500 hover:text-white'
            ]"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </button>

          <!-- Page Numbers -->
          <button
            v-for="page in visiblePages"
            :key="page"
            @click="goToPage(page)"
            :class="[
              'px-4 py-2 rounded-lg font-medium transition-all',
              currentPage === page
                ? 'bg-gradient-primary text-white'
                : 'bg-white text-dark-700 hover:bg-primary-500 hover:text-white'
            ]"
          >
            {{ page }}
          </button>

          <!-- Next Button -->
          <button
            @click="goToPage(currentPage + 1)"
            :disabled="currentPage === totalPages"
            :class="[
              'px-4 py-2 rounded-lg font-medium transition-all',
              currentPage === totalPages
                ? 'bg-dark-200 text-dark-400 cursor-not-allowed'
                : 'bg-white text-dark-700 hover:bg-primary-500 hover:text-white'
            ]"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </DefaultLayout>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import FeaturedMember from '@/components/members/FeaturedMember.vue'
import MemberFilters from '@/components/members/MemberFilters.vue'
import MemberSearch from '@/components/members/MemberSearch.vue'
import MemberCard from '@/components/members/MemberCard.vue'
import MemberCardSkeleton from '@/components/common/MemberCardSkeleton.vue'
import type { Member, MemberFilter } from '@/types/member'
import membersData from '@/data/members.json'

// State
const allMembers = ref<Member[]>([])
const isLoading = ref(true)
const currentPage = ref(1)
const membersPerPage = 12

const filters = ref<MemberFilter>({
  team: 'all',
  generation: 'all',
  status: 'all',
  searchQuery: '',
  sortBy: 'name',
  sortOrder: 'asc',
})

// Computed
const totalMembers = computed(() => allMembers.value.length)

const teams = computed(() => [
  { value: 'all', label: 'All' },
  { value: 'Team K', label: 'Team K' },
  { value: 'Team L', label: 'Team L' },
  { value: 'Team P', label: 'Team P' },
  { value: 'Trainee', label: 'Trainee' },
])

const featuredMembers = computed(() => {
  return allMembers.value.filter(member => member.featured && member.status === 'active')
})

const filteredMembers = computed(() => {
  let result = [...allMembers.value]

  // Apply team filter
  if (filters.value.team && filters.value.team !== 'all') {
    result = result.filter(member => member.team === filters.value.team)
  }

  // Apply generation filter
  if (filters.value.generation && filters.value.generation !== 'all') {
    result = result.filter(member => member.generation === filters.value.generation)
  }

  // Apply status filter
  if (filters.value.status && filters.value.status !== 'all') {
    result = result.filter(member => member.status === filters.value.status)
  }

  // Apply search query
  if (filters.value.searchQuery) {
    const query = filters.value.searchQuery.toLowerCase()
    result = result.filter(member =>
      member.name.english.toLowerCase().includes(query) ||
      (member.name.native && member.name.native.toLowerCase().includes(query)) ||
      (member.nickname && member.nickname.toLowerCase().includes(query))
    )
  }

  // Apply sorting
  result.sort((a, b) => {
    let comparison = 0

    switch (filters.value.sortBy) {
      case 'name':
        comparison = a.name.english.localeCompare(b.name.english)
        break
      case 'generation':
        comparison = a.generation.localeCompare(b.generation)
        break
      case 'birthday':
        comparison = new Date(a.birthdate).getTime() - new Date(b.birthdate).getTime()
        break
      case 'joinDate':
        comparison = new Date(a.joinDate).getTime() - new Date(b.joinDate).getTime()
        break
    }

    return filters.value.sortOrder === 'asc' ? comparison : -comparison
  })

  return result
})

const totalPages = computed(() => {
  return Math.ceil(filteredMembers.value.length / membersPerPage)
})

const paginatedMembers = computed(() => {
  const start = (currentPage.value - 1) * membersPerPage
  const end = start + membersPerPage
  return filteredMembers.value.slice(start, end)
})

const visiblePages = computed(() => {
  const pages: number[] = []
  const maxVisible = 5
  const total = totalPages.value

  if (total <= maxVisible) {
    for (let i = 1; i <= total; i++) {
      pages.push(i)
    }
  } else {
    const current = currentPage.value
    const half = Math.floor(maxVisible / 2)

    let start = Math.max(1, current - half)
    let end = Math.min(total, current + half)

    if (current <= half) {
      end = maxVisible
    } else if (current >= total - half) {
      start = total - maxVisible + 1
    }

    for (let i = start; i <= end; i++) {
      pages.push(i)
    }
  }

  return pages
})

// Methods
const loadMembers = () => {
  isLoading.value = true
  // Simulate loading delay
  setTimeout(() => {
    allMembers.value = membersData as Member[]
    isLoading.value = false
  }, 500)
}

const goToPage = (page: number) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    // Scroll to top of members grid
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const updateFilters = (newFilters: MemberFilter) => {
  filters.value = { ...filters.value, ...newFilters }
}

const resetAllFilters = () => {
  filters.value = {
    team: 'all',
    generation: 'all',
    status: 'all',
    searchQuery: '',
    sortBy: 'name',
    sortOrder: 'asc',
  }
}

// Watchers
watch(
  () => [filters.value.team, filters.value.generation, filters.value.status, filters.value.searchQuery],
  () => {
    currentPage.value = 1
  }
)

// Lifecycle
onMounted(() => {
  loadMembers()
})
</script>

<style scoped>
/* Member Grid using CSS Grid */
.member-grid {
  display: grid;
  grid-template-columns: repeat(1, 1fr);
  gap: 1.5rem;
}

@media (min-width: 640px) {
  .member-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 768px) {
  .member-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (min-width: 1024px) {
  .member-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

@media (min-width: 1280px) {
  .member-grid {
    grid-template-columns: repeat(4, 1fr);
    gap: 2rem;
  }
}
</style>
