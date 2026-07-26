<template>
  <AdminLayout title="Fanclub Members">
    <template #actions>
      <Link :href="route('admin.fanclub.create')" class="btn-primary">+ Add Member</Link>
    </template>

    <!-- Stats bar -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-4">
        <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ stats.total }}</p>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Total Members</p>
      </div>
      <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-4">
        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ stats.active }}</p>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Active</p>
      </div>
      <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-4">
        <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ stats.basic }}</p>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Basic (Active)</p>
      </div>
      <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-4">
        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ stats.gold }}</p>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Gold (Active)</p>
      </div>
    </div>

    <!-- Filters -->
    <div class="flex gap-2 mb-4">
      <button
        v-for="f in filters"
        :key="f.value"
        @click="activeFilter = f.value"
        class="px-3 py-1.5 text-sm rounded-lg border transition-colors"
        :class="activeFilter === f.value
          ? 'bg-teal-600 dark:bg-teal-500 text-white border-teal-600 dark:border-teal-500'
          : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-300 dark:border-gray-700 hover:border-teal-400 dark:hover:border-teal-600'"
      >
        {{ f.label }}
      </button>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tier</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Joined</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Expires</th>
            <th class="px-6 py-3" />
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          <tr v-for="member in filteredMembers" :key="member.id" class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
            <td class="px-6 py-3 font-medium text-gray-800 dark:text-gray-100">{{ member.name }}</td>
            <td class="px-6 py-3 text-gray-500 dark:text-gray-400">{{ member.email }}</td>
            <td class="px-6 py-3">
              <span
                class="px-2 py-0.5 rounded-full text-xs font-semibold uppercase"
                :class="member.tier === 'gold'
                  ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300'
                  : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400'"
              >{{ member.tier }}</span>
            </td>
            <td class="px-6 py-3">
              <span
                class="px-2 py-0.5 rounded-full text-xs font-medium capitalize"
                :class="statusClass(member.status)"
              >{{ member.status }}</span>
            </td>
            <td class="px-6 py-3 text-gray-500 dark:text-gray-400">{{ formatDate(member.joined_at) }}</td>
            <td class="px-6 py-3 text-gray-500 dark:text-gray-400">{{ member.expires_at ? formatDate(member.expires_at) : '—' }}</td>
            <td class="px-6 py-3 text-right whitespace-nowrap">
              <Link :href="route('admin.fanclub.edit', member.id)" class="text-teal-600 dark:text-teal-400 hover:text-teal-800 dark:hover:text-teal-300 text-sm font-medium mr-4">Edit</Link>
              <button @click="destroy(member)" class="text-red-400 hover:text-red-600 dark:hover:text-red-300 text-sm font-medium">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-if="!filteredMembers.length" class="px-6 py-10 text-center text-gray-400 dark:text-gray-500">No members found.</p>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({ members: Array, stats: Object })

const activeFilter = ref('all')
const filters = [
  { value: 'all',       label: 'All'       },
  { value: 'active',    label: 'Active'    },
  { value: 'basic',     label: 'Basic'     },
  { value: 'gold',      label: 'Gold'      },
  { value: 'expired',   label: 'Expired'   },
  { value: 'cancelled', label: 'Cancelled' },
]

const filteredMembers = computed(() => {
  if (activeFilter.value === 'all')       return props.members
  if (activeFilter.value === 'basic')     return props.members.filter(m => m.tier === 'basic')
  if (activeFilter.value === 'gold')      return props.members.filter(m => m.tier === 'gold')
  return props.members.filter(m => m.status === activeFilter.value)
})

const formatDate  = (d) => d ? new Date(d).toLocaleDateString('en-GB') : '—'
const statusClass = (s) => ({
  active:    'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
  expired:   'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
  cancelled: 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
}[s] || 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400')

const destroy = (member) => {
  if (!confirm(`Remove ${member.name} from fanclub?`)) return
  router.delete(route('admin.fanclub.destroy', member.id))
}
</script>
