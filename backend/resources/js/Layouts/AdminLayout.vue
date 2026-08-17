<template>
  <!-- Skip to main content link for accessibility -->
  <a
    href="#main-content"
    class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-teal-600 focus:text-white focus:rounded-lg focus:shadow-lg"
  >
    Skip to main content
  </a>

  <div class="min-h-screen flex bg-gray-50 dark:bg-slate-900 transition-colors duration-300">
    <!-- Mobile Backdrop -->
    <Transition
      enter-active-class="transition-opacity duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="mobileMenuOpen"
        @click="closeMobileMenu"
        class="fixed inset-0 bg-black/50 lg:hidden z-40 backdrop-blur-sm"
        aria-hidden="true"
      />
    </Transition>

    <!-- Sidebar -->
    <Transition
      enter-active-class="transition-transform duration-300 ease-out"
      enter-from-class="-translate-x-full"
      enter-to-class="translate-x-0"
      leave-active-class="transition-transform duration-300 ease-in"
      leave-from-class="translate-x-0"
      leave-to-class="-translate-x-full"
    >
      <aside
        v-show="mobileMenuOpen || !isMobile"
        ref="sidebarRef"
        class="fixed lg:sticky left-0 top-0 z-50 h-screen w-64 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 flex flex-col flex-shrink-0 transition-colors duration-300"
        role="navigation"
        aria-label="Main navigation"
      >
        <!-- Logo Section (Fixed) -->
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
          <div>
            <h1 class="text-xl font-bold text-teal-500 dark:text-teal-400 tracking-tight">KLP48</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Admin Panel</p>
          </div>
          <!-- Mobile close button -->
          <button
            v-if="mobileMenuOpen"
            @click="closeMobileMenu"
            class="lg:hidden p-1.5 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
            aria-label="Close navigation menu"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Navigation (Scrollable) -->
        <nav class="flex-1 min-h-0 overflow-y-auto overscroll-contain px-3 py-3 space-y-3 scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-700 scrollbar-track-transparent">
          <!-- Dashboard -->
          <div class="space-y-0.5">
            <NavItem :href="route('admin.dashboard')" :active="isActive('admin.dashboard')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
              Dashboard
            </NavItem>
          </div>

          <!-- Content Section -->
          <div class="space-y-0.5">
            <div class="px-3 py-1.5 rounded-md bg-gray-50 dark:bg-gray-800 border-l-2 border-teal-500">
              <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Content</p>
            </div>

            <NavItem :href="route('admin.members.index')" :active="isActive('admin.members')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              Members
            </NavItem>

            <NavItem :href="route('admin.news.index')" :active="isActive('admin.news')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
              News
            </NavItem>

            <NavItem :href="route('admin.releases.index')" :active="isActive('admin.releases')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z"/></svg>
              Releases
            </NavItem>

            <NavItem :href="route('admin.videos.index')" :active="isActive('admin.videos')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
              Videos
            </NavItem>

            <NavItem :href="route('admin.events.index')" :active="isActive('admin.events')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              Events
            </NavItem>
          </div>

          <!-- Schedule Management Section -->
          <div class="space-y-0.5">
            <div class="px-3 py-1.5 rounded-md bg-gray-50 dark:bg-gray-800 border-l-2 border-teal-500">
              <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Schedule Management</p>
            </div>

            <NavItem :href="route('admin.calendar.index')" :active="isActive('admin.calendar')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              Calendar
            </NavItem>

            <NavItem v-if="canManageKanban" :href="route('admin.kanban.index')" :active="isActive('admin.kanban')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
              Kanban Board
            </NavItem>

            <NavItem v-if="canManageResources" :href="route('admin.resources.index')" :active="isActive('admin.resources')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
              Resources
            </NavItem>
          </div>

          <!-- Compliance Section -->
          <div v-if="canViewContracts" class="space-y-0.5">
            <div class="px-3 py-1.5 rounded-md bg-gray-50 dark:bg-gray-800 border-l-2 border-teal-500">
              <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Compliance</p>
            </div>

            <NavItem :href="route('admin.contracts.index')" :active="isActive('admin.contracts')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              Contracts
            </NavItem>
          </div>

          <!-- Analytics Section -->
          <div class="space-y-0.5">
            <div class="px-3 py-1.5 rounded-md bg-gray-50 dark:bg-gray-800 border-l-2 border-teal-500">
              <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Analytics</p>
            </div>

            <NavItem :href="route('admin.social-media.index')" :active="isActive('admin.social-media')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
              Social Media
            </NavItem>

            <NavItem v-if="canViewReports" :href="route('admin.reports.index')" :active="isActive('admin.reports')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17V9m4 8V5m4 12v-4M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
              Reports
            </NavItem>
          </div>

          <!-- Community Section -->
          <div class="space-y-0.5">
            <div class="px-3 py-1.5 rounded-md bg-gray-50 dark:bg-gray-800 border-l-2 border-teal-500">
              <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Community</p>
            </div>

            <NavItem :href="route('admin.fanclub.index')" :active="isActive('admin.fanclub')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
              Fanclub
            </NavItem>
          </div>
        </nav>

        <!-- User Profile + Dropdown Menu (Fixed at bottom) -->
        <div ref="userMenuRef" class="relative px-4 py-3 border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 flex-shrink-0">
          <!-- User Menu Trigger -->
          <button
            @click="toggleUserMenu"
            @keydown.escape="closeUserMenu"
            class="user-menu-trigger w-full flex items-center gap-3 px-2 py-2 rounded-lg text-left transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-gray-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-500"
            :aria-expanded="userMenuOpen"
            aria-haspopup="true"
            aria-label="User menu"
          >
            <!-- Avatar -->
            <div class="w-9 h-9 rounded-full bg-teal-600 dark:bg-teal-500 flex items-center justify-center text-sm font-semibold text-white flex-shrink-0">
              {{ auth.user?.name?.charAt(0) }}
            </div>

            <!-- User Info -->
            <div class="min-w-0 flex-1">
              <p class="text-sm font-medium truncate text-gray-900 dark:text-gray-100">{{ auth.user?.name }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ auth.user?.email }}</p>
            </div>

            <!-- Chevron Icon -->
            <svg
              class="w-4 h-4 text-gray-400 dark:text-gray-500 transition-transform duration-200 flex-shrink-0"
              :class="{ 'rotate-180': userMenuOpen }"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              aria-hidden="true"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>

          <!-- Dropdown Menu -->
          <Transition
            enter-active-class="transition-all duration-200 ease-out"
            enter-from-class="opacity-0 translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition-all duration-150 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-2"
          >
            <div
              v-if="userMenuOpen"
              class="absolute bottom-full left-4 right-4 mb-2 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50"
              role="menu"
              aria-orientation="vertical"
              @keydown.escape="closeUserMenu"
            >
              <!-- Dark/Light Mode Toggle -->
              <button
                @click="toggleDarkMode"
                class="menu-item w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors focus-visible:outline-none focus-visible:bg-gray-50 dark:focus-visible:bg-gray-700"
                role="menuitem"
                :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
              >
                <!-- Sun Icon (Light Mode) -->
                <svg
                  v-if="!isDark"
                  class="w-5 h-5 text-amber-500"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                  aria-hidden="true"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.5"
                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
                  />
                </svg>

                <!-- Moon Icon (Dark Mode) -->
                <svg
                  v-else
                  class="w-5 h-5 text-indigo-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                  aria-hidden="true"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.5"
                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
                  />
                </svg>

                <span>{{ isDark ? 'Light Mode' : 'Dark Mode' }}</span>
              </button>

              <!-- Divider -->
              <div class="h-px bg-gray-200 dark:bg-gray-700 my-1" role="separator"></div>

              <!-- Log Out -->
              <button
                @click="handleLogout"
                class="menu-item w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors focus-visible:outline-none focus-visible:bg-red-50 dark:focus-visible:bg-red-900/20"
                role="menuitem"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Log Out
              </button>
            </div>
          </Transition>
        </div>
      </aside>
    </Transition>

    <!-- Main content -->
    <div class="flex-1 flex flex-col min-w-0">
      <!-- Top bar -->
      <header class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 px-4 lg:px-8 py-4 flex items-center justify-between transition-colors duration-300">
        <!-- Mobile Menu Toggle -->
        <div class="flex items-center gap-4">
          <MobileMenuToggle
            :is-open="mobileMenuOpen"
            @click="toggleMobileMenu"
            class="lg:hidden"
          />
          <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ title }}</h2>
        </div>
        <slot name="actions" />
      </header>

      <!-- Flash messages -->
      <div v-if="flash.success || flash.error" class="px-4 lg:px-8 pt-4">
        <div v-if="flash.success" class="bg-teal-50 dark:bg-teal-900/20 border border-teal-200 dark:border-teal-800 text-teal-800 dark:text-teal-300 px-4 py-3 rounded-lg text-sm">
          {{ flash.success }}
        </div>
        <div v-if="flash.error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 px-4 py-3 rounded-lg text-sm">
          {{ flash.error }}
        </div>
      </div>

      <!-- Main content (scrollable independently) -->
      <main id="main-content" class="flex-1 px-4 lg:px-8 py-6 overflow-y-auto">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { usePage, router } from '@inertiajs/vue3'
import { computed, ref, onMounted, onUnmounted, watch, nextTick } from 'vue'
import NavItem from '@/Components/Admin/NavItem.vue'
import MobileMenuToggle from '@/Components/Admin/MobileMenuToggle.vue'
import { useDarkMode } from '@/composables/useDarkMode'

defineProps({ title: String })

const page = usePage()
const auth  = computed(() => page.props.auth)
const flash = computed(() => page.props.flash)

// Dark mode
const { isDark, toggleDarkMode } = useDarkMode()

// Mobile menu state
const mobileMenuOpen = ref(false)
const isMobile = ref(false)
const sidebarRef = ref(null)

// User menu state
const userMenuOpen = ref(false)
const userMenuRef = ref(null)

// Check if mobile
const checkMobile = () => {
  isMobile.value = window.innerWidth < 1024
  if (!isMobile.value) {
    mobileMenuOpen.value = false
  }
}

const toggleMobileMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value
}

const closeMobileMenu = () => {
  mobileMenuOpen.value = false
}

// User menu controls
const toggleUserMenu = () => {
  userMenuOpen.value = !userMenuOpen.value
}

const closeUserMenu = () => {
  userMenuOpen.value = false
}

const handleLogout = () => {
  closeUserMenu()
  logout()
}

// Permission checks for navigation
const canManageKanban = computed(() => auth.value.can?.['manage-kanban'] || false)
const canManageResources = computed(() => auth.value.can?.['manage-resources'] || false)
const canViewContracts = computed(() => auth.value.can?.['view-contracts'] || false)
const canViewReports = computed(() => auth.value.can?.['view-reports'] || false)

const isActive = (name) => page.url.startsWith('/' + name.replace('admin.', 'admin/').replace(/\./g, '/'))

const logout = () => router.post(route('admin.logout'))

// Focus management for mobile menu
watch(mobileMenuOpen, (isOpen) => {
  if (isOpen) {
    // Prevent body scroll when menu is open
    document.body.style.overflow = 'hidden'

    // Focus first navigation item
    nextTick(() => {
      const firstFocusable = sidebarRef.value?.querySelector('a, button')
      firstFocusable?.focus()
    })
  } else {
    document.body.style.overflow = ''
  }
})

// Handle escape key to close mobile menu
const handleEscape = (e) => {
  if (e.key === 'Escape' && mobileMenuOpen.value) {
    closeMobileMenu()
  }
}

// Click outside to close user menu
const handleClickOutside = (e) => {
  if (userMenuOpen.value && userMenuRef.value && !userMenuRef.value.contains(e.target)) {
    closeUserMenu()
  }
}

onMounted(() => {
  checkMobile()
  window.addEventListener('resize', checkMobile)
  window.addEventListener('keydown', handleEscape)
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  window.removeEventListener('resize', checkMobile)
  window.removeEventListener('keydown', handleEscape)
  document.removeEventListener('click', handleClickOutside)
  document.body.style.overflow = ''
})
</script>

<style>
/* Custom scrollbar styles */
.scrollbar-thin {
  scrollbar-width: thin;
}

/* Scroll isolation - prevent sidebar scroll from affecting main content */
nav.overscroll-contain {
  overscroll-behavior: contain;
}

.scrollbar-thumb-gray-300 {
  scrollbar-color: rgb(209 213 219) transparent;
}

.dark .scrollbar-thumb-gray-700 {
  scrollbar-color: rgb(55 65 81) transparent;
}

.scrollbar-track-transparent {
  scrollbar-color: transparent transparent;
}

/* Webkit scrollbar styles */
.scrollbar-thin::-webkit-scrollbar {
  width: 6px;
}

.scrollbar-thin::-webkit-scrollbar-track {
  background: transparent;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
  background-color: rgba(209, 213, 219, 0.8);
  border-radius: 3px;
  transition: background-color 0.2s ease;
}

.dark .scrollbar-thin::-webkit-scrollbar-thumb {
  background-color: rgba(55, 65, 81, 0.8);
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
  background-color: rgb(156 163 175);
}

.dark .scrollbar-thin::-webkit-scrollbar-thumb:hover {
  background-color: rgb(75 85 99);
}

/* Scrollbar visibility enhancement on nav hover */
nav.scrollbar-thin:hover::-webkit-scrollbar-thumb {
  background-color: rgb(156 163 175);
}

.dark nav.scrollbar-thin:hover::-webkit-scrollbar-thumb {
  background-color: rgb(75 85 99);
}

/* Prevent transitions on initial load */
html:not(.dark-mode-ready) * {
  transition: none !important;
}

/* Screen reader only utility */
.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border-width: 0;
}

.focus\:not-sr-only:focus {
  position: static;
  width: auto;
  height: auto;
  padding: inherit;
  margin: inherit;
  overflow: visible;
  clip: auto;
  white-space: normal;
}

/* User menu trigger focus styles */
.user-menu-trigger:focus-visible {
  outline: none;
  box-shadow: 0 0 0 2px #14b8a6;
}

/* Menu item keyboard navigation */
.menu-item:focus {
  outline: none;
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}
</style>
