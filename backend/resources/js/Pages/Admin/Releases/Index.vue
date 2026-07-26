<template>
  <AdminLayout title="Releases">
    <template #actions>
      <Link :href="route('admin.releases.create')" class="btn-primary">+ New Release</Link>
    </template>

    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Release</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
            <th class="px-6 py-3" />
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          <tr v-for="release in releases" :key="release.id" class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
            <td class="px-6 py-3 flex items-center gap-3">
              <img v-if="release.cover_image_url" :src="release.cover_image_url" class="w-10 h-10 rounded object-cover flex-shrink-0" />
              <div v-else class="w-10 h-10 rounded bg-gray-100 dark:bg-gray-800 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z"/></svg>
              </div>
              <span class="font-medium text-gray-800 dark:text-gray-100">{{ release.title }}</span>
            </td>
            <td class="px-6 py-3">
              <span class="px-2 py-0.5 rounded-full text-xs font-medium uppercase bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300">{{ release.type }}</span>
            </td>
            <td class="px-6 py-3 text-gray-500 dark:text-gray-400">{{ formatDate(release.release_date) }}</td>
            <td class="px-6 py-3 text-right">
              <Link :href="route('admin.releases.edit', release.id)" class="text-teal-600 dark:text-teal-400 hover:text-teal-800 dark:hover:text-teal-300 text-sm font-medium mr-4">Edit</Link>
              <button @click="destroy(release)" class="text-red-400 hover:text-red-600 dark:hover:text-red-300 text-sm font-medium">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-if="!releases.length" class="px-6 py-10 text-center text-gray-400 dark:text-gray-500">No releases yet.</p>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'

defineProps({ releases: Array })

const formatDate = (d) => {
  if (!d) return '—'
  const date = new Date(d)
  return date.toLocaleDateString('en-GB') // DD/MM/YYYY
}

const destroy = (r) => {
  if (!confirm(`Delete "${r.title}"?`)) return
  router.delete(route('admin.releases.destroy', r.id))
}
</script>

