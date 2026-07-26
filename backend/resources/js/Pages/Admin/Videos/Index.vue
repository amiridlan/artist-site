<template>
  <AdminLayout title="Videos">
    <template #actions>
      <Link :href="route('admin.videos.create')" class="btn-primary">+ New Video</Link>
    </template>

    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Title</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">YouTube</th>
            <th class="px-6 py-3" />
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          <tr v-for="video in videos" :key="video.id" class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
            <td class="px-6 py-3 font-medium text-gray-800 dark:text-gray-100 max-w-xs truncate">{{ video.title }}</td>
            <td class="px-6 py-3">
              <span class="px-2 py-0.5 rounded-full text-xs font-medium capitalize bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-300">{{ video.type }}</span>
            </td>
            <td class="px-6 py-3 text-gray-500 dark:text-gray-400">{{ formatDate(video.date) }}</td>
            <td class="px-6 py-3 text-gray-500 dark:text-gray-400 font-mono text-xs">{{ video.youtube_id || '—' }}</td>
            <td class="px-6 py-3 text-right">
              <Link :href="route('admin.videos.edit', video.id)" class="text-teal-600 dark:text-teal-400 hover:text-teal-800 dark:hover:text-teal-300 text-sm font-medium mr-4">Edit</Link>
              <button @click="destroy(video)" class="text-red-400 hover:text-red-600 dark:hover:text-red-300 text-sm font-medium">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-if="!videos.length" class="px-6 py-10 text-center text-gray-400 dark:text-gray-500">No videos yet.</p>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'

defineProps({ videos: Array })

const formatDate = (d) => d ? new Date(d).toLocaleDateString('en-GB') : '—'

const destroy = (v) => {
  if (!confirm(`Delete "${v.title}"?`)) return
  router.delete(route('admin.videos.destroy', v.id))
}
</script>

