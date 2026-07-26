<template>
  <AdminLayout title="Events">
    <template #actions>
      <Link :href="route('admin.events.create')" class="btn-primary">+ New Event</Link>
    </template>

    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Title</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Venue</th>
            <th class="px-6 py-3" />
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          <tr v-for="event in events" :key="event.id" class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
            <td class="px-6 py-3 font-medium text-gray-800 dark:text-gray-100 max-w-xs truncate">{{ event.title }}</td>
            <td class="px-6 py-3">
              <span class="px-2 py-0.5 rounded-full text-xs font-medium capitalize bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300">{{ event.type }}</span>
            </td>
            <td class="px-6 py-3">
              <span class="px-2 py-0.5 rounded-full text-xs font-medium capitalize" :class="statusClass(event.status)">{{ event.status }}</span>
            </td>
            <td class="px-6 py-3 text-gray-500 dark:text-gray-400">{{ formatDate(event.date) }}</td>
            <td class="px-6 py-3 text-gray-500 dark:text-gray-400">{{ event.venue || '—' }}</td>
            <td class="px-6 py-3 text-right">
              <Link :href="route('admin.events.edit', event.id)" class="text-teal-600 dark:text-teal-400 hover:text-teal-800 dark:hover:text-teal-300 text-sm font-medium mr-4">Edit</Link>
              <button @click="destroy(event)" class="text-red-400 hover:text-red-600 dark:hover:text-red-300 text-sm font-medium">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-if="!events.length" class="px-6 py-10 text-center text-gray-400 dark:text-gray-500">No events yet.</p>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'

defineProps({ events: Array })

const formatDate = (d) => d ? new Date(d).toLocaleDateString('en-GB') : '—'

const statusClass = (s) => ({
  upcoming:  'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
  ongoing:   'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
  completed: 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400',
  cancelled: 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
}[s] || 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400')

const destroy = (e) => {
  if (!confirm(`Delete "${e.title}"?`)) return
  router.delete(route('admin.events.destroy', e.id))
}
</script>

