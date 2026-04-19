<template>
  <AdminLayout title="Members">
    <template #actions>
      <Link :href="route('admin.members.create')" class="btn-primary">+ New Member</Link>
    </template>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Member</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Generation</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Order</th>
            <th class="px-6 py-3" />
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr v-for="member in members" :key="member.id" class="hover:bg-gray-50">
            <td class="px-6 py-3 flex items-center gap-3">
              <img v-if="member.photo" :src="`/storage/${member.photo}`" class="w-9 h-9 rounded-full object-cover flex-shrink-0" />
              <div v-else class="w-9 h-9 rounded-full bg-teal-100 flex items-center justify-center text-teal-700 font-semibold text-sm flex-shrink-0">
                {{ member.name_english.charAt(0) }}
              </div>
              <div>
                <p class="font-medium text-gray-800">{{ member.name_english }}</p>
                <p v-if="member.name_native" class="text-xs text-gray-400">{{ member.name_native }}</p>
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
            <td class="px-6 py-3 text-gray-500">{{ member.sort_order }}</td>
            <td class="px-6 py-3 text-right">
              <Link :href="route('admin.members.edit', member.id)" class="text-teal-600 hover:text-teal-800 text-sm font-medium mr-4">Edit</Link>
              <button @click="destroy(member)" class="text-red-400 hover:text-red-600 text-sm font-medium">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-if="!members.length" class="px-6 py-10 text-center text-gray-400">No members yet.</p>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'

defineProps({ members: Array })

const generationClass = (gen) => gen === '1st'
  ? 'bg-green-100 text-green-700'
  : 'bg-blue-100 text-blue-700'

const statusClass = (s) => ({
  active:    'bg-green-100 text-green-700',
  graduated: 'bg-yellow-100 text-yellow-700',
  concluded: 'bg-gray-100 text-gray-600',
}[s] ?? 'bg-gray-100 text-gray-600')

const destroy = (member) => {
  if (!confirm(`Delete ${member.name_english}?`)) return
  router.delete(route('admin.members.destroy', member.id))
}
</script>

