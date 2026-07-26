<template>
  <AdminLayout title="Members">
    <template #actions>
      <Link :href="route('admin.members.create')" class="btn-primary">+ New Member</Link>
    </template>

    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Member</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Generation</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Order</th>
            <th class="px-6 py-3" />
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          <tr v-for="member in members" :key="member.id" class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
            <td class="px-6 py-3 flex items-center gap-3">
              <img v-if="member.photo_url" :src="member.photo_url" class="w-9 h-9 rounded-full object-cover flex-shrink-0" />
              <div v-else class="w-9 h-9 rounded-full bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center text-teal-700 dark:text-teal-300 font-semibold text-sm flex-shrink-0">
                {{ member.name_english.charAt(0) }}
              </div>
              <div>
                <p class="font-medium text-gray-800 dark:text-gray-100">{{ member.name_english }}</p>
                <p v-if="member.name_native" class="text-xs text-gray-400 dark:text-gray-500">{{ member.name_native }}</p>
              </div>
            </td>
            <td class="px-6 py-3">
              <span class="px-2 py-0.5 rounded-full text-xs font-medium capitalize" :class="generationClass(member.generation)">
                {{ member.generation }} Gen
              </span>
            </td>
            <td class="px-6 py-3">
              <span class="px-2 py-0.5 rounded-full text-xs font-medium capitalize" :class="statusClass(member.status)">
                {{ member.status }}
              </span>
            </td>
            <td class="px-6 py-3 text-gray-500 dark:text-gray-400">{{ member.sort_order }}</td>
            <td class="px-6 py-3 text-right">
              <Link :href="route('admin.members.edit', member.id)" class="text-teal-600 dark:text-teal-400 hover:text-teal-800 dark:hover:text-teal-300 text-sm font-medium mr-4">Edit</Link>
              <button @click="destroy(member)" class="text-red-400 dark:text-red-400 hover:text-red-600 dark:hover:text-red-300 text-sm font-medium">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-if="!members.length" class="px-6 py-10 text-center text-gray-400 dark:text-gray-500">No members yet.</p>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'

defineProps({ members: Array })

const generationClass = (gen) => gen === '1st'
  ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300'
  : 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'

const statusClass = (s) => ({
  active:    'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
  graduated: 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
  concluded: 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400',
}[s] ?? 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400')

const destroy = (member) => {
  if (!confirm(`Delete ${member.name_english}?`)) return
  router.delete(route('admin.members.destroy', member.id))
}
</script>

