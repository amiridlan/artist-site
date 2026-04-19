<template>
  <div class="min-h-screen flex bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-900 text-white flex flex-col flex-shrink-0">
      <div class="px-6 py-5 border-b border-gray-700">
        <h1 class="text-xl font-bold text-teal-400">KLP48</h1>
        <p class="text-xs text-gray-400 mt-0.5">Admin Panel</p>
      </div>

      <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
        <NavItem :href="route('admin.dashboard')" :active="isActive('admin.dashboard')">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
          Dashboard
        </NavItem>

        <div class="pt-3 pb-1">
          <p class="text-xs text-gray-500 uppercase tracking-wider px-3">Content</p>
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

        <div class="pt-3 pb-1">
          <p class="text-xs text-gray-500 uppercase tracking-wider px-3">Analytics</p>
        </div>

        <NavItem :href="route('admin.social-media.index')" :active="isActive('admin.social-media')">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
          Social Media
        </NavItem>

        <div class="pt-3 pb-1">
          <p class="text-xs text-gray-500 uppercase tracking-wider px-3">Community</p>
        </div>

        <NavItem :href="route('admin.fanclub.index')" :active="isActive('admin.fanclub')">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
          Fanclub
        </NavItem>
      </nav>

      <!-- User + logout -->
      <div class="px-4 py-4 border-t border-gray-700">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-8 h-8 rounded-full bg-teal-600 flex items-center justify-center text-sm font-semibold">
            {{ auth.user?.name?.charAt(0) }}
          </div>
          <div class="min-w-0">
            <p class="text-sm font-medium truncate">{{ auth.user?.name }}</p>
            <p class="text-xs text-gray-400 truncate">{{ auth.user?.email }}</p>
          </div>
        </div>
        <form @submit.prevent="logout">
          <button type="submit" class="w-full text-left text-sm text-gray-400 hover:text-white transition-colors flex items-center gap-2 px-2 py-1.5 rounded hover:bg-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            Sign out
          </button>
        </form>
      </div>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col min-w-0">
      <!-- Top bar -->
      <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-800">{{ title }}</h2>
        <slot name="actions" />
      </header>

      <!-- Flash messages -->
      <div v-if="flash.success || flash.error" class="px-8 pt-4">
        <div v-if="flash.success" class="bg-teal-50 border border-teal-200 text-teal-800 px-4 py-3 rounded-lg text-sm">
          {{ flash.success }}
        </div>
        <div v-if="flash.error" class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
          {{ flash.error }}
        </div>
      </div>

      <main class="flex-1 px-8 py-6">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { usePage, router } from '@inertiajs/vue3'
import { computed } from 'vue'
import NavItem from '@/Components/Admin/NavItem.vue'

defineProps({ title: String })

const page = usePage()
const auth  = computed(() => page.props.auth)
const flash = computed(() => page.props.flash)

const isActive = (name) => page.url.startsWith('/' + name.replace('admin.', 'admin/').replace(/\./g, '/'))

const logout = () => router.post(route('admin.logout'))
</script>
