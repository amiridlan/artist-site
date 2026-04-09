<template>
  <AdminLayout title="Events">
    <template #actions>
      <Link :href="route('admin.events.create')" class="btn-primary">+ New Event</Link>
    </template>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Title</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Venue</th>
            <th class="px-6 py-3" />
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr v-for="event in events" :key="event.id" class="hover:bg-gray-50">
            <td class="px-6 py-3 font-medium text-gray-800 max-w-xs truncate">{{ event.title }}</td>
            <td class="px-6 py-3">
              <span class="px-2 py-0.5 rounded-full text-xs font-medium capitalize bg-orange-100 text-orange-700">{{ event.type }}</span>
            </td>
            <td class="px-6 py-3">
              <span class="px-2 py-0.5 rounded-full text-xs font-medium capitalize" :class="statusClass(event.status)">{{ event.status }}</span>
            </td>
            <td class="px-6 py-3 text-gray-500">{{ formatDate(event.date) }}</td>
            <td class="px-6 py-3 text-gray-500">{{ event.venue || '—' }}</td>
            <td class="px-6 py-3 text-right">
              <Link :href="route('admin.events.edit', event.id)" class="text-teal-600 hover:text-teal-800 text-sm font-medium mr-4">Edit</Link>
              <button @click="destroy(event)" class="text-red-400 hover:text-red-600 text-sm font-medium">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-if="!events.length" class="px-6 py-10 text-center text-gray-400">No events yet.</p>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'

defineProps({ events: Array })

const formatDate = (d) => d ? new Date(d).toLocaleDateString('en-GB') : '—'

const statusClass = (s) => ({
  upcoming:  'bg-green-100 text-green-700',
  ongoing:   'bg-yellow-100 text-yellow-700',
  completed: 'bg-gray-100 text-gray-600',
  cancelled: 'bg-red-100 text-red-700',
}[s] || 'bg-gray-100 text-gray-600')

const destroy = (e) => {
  if (!confirm(`Delete "${e.title}"?`)) return
  router.delete(route('admin.events.destroy', e.id))
}
</script>

