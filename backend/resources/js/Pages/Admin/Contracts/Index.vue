<template>
  <AdminLayout title="Contracts">
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-4 mb-6">
      <form @submit.prevent="applyFilters" class="flex gap-3">
        <select v-model="form.status" class="input text-sm">
          <option value="">All Statuses</option>
          <option value="active">Active</option>
          <option value="expired">Expired</option>
          <option value="terminated">Terminated</option>
        </select>
        <select v-model="form.member_id" class="input text-sm">
          <option value="">All Members</option>
          <option v-for="member in members" :key="member.id" :value="member.id">{{ member.name_english }}</option>
        </select>
        <button type="submit" class="btn-primary text-sm">Apply</button>
      </form>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Member</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Start</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">End</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          <tr
            v-for="contract in contracts.data"
            :key="contract.id"
            class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
            :class="isExpiringSoon(contract) ? 'bg-amber-50 dark:bg-amber-900/10' : ''"
          >
            <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">{{ contract.member?.name_english }}</td>
            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ contract.start_date }}</td>
            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
              {{ contract.end_date }}
              <span v-if="isExpiringSoon(contract)" class="ml-1 text-xs text-amber-600 dark:text-amber-400">(expiring soon)</span>
            </td>
            <td class="px-6 py-4">
              <span class="px-2 py-1 text-xs rounded-full capitalize" :class="statusClass(contract.status)">{{ contract.status }}</span>
            </td>
            <td class="px-6 py-4 text-right space-x-2">
              <Link :href="route('admin.contracts.edit', contract.id)" class="text-teal-600 dark:text-teal-400 hover:text-teal-800 dark:hover:text-teal-300 text-sm">Edit</Link>
              <button @click="deleteContract(contract)" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 text-sm">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="contracts.data?.length" class="px-6 py-4 border-t border-gray-200 dark:border-gray-800 flex items-center justify-between">
        <div class="text-sm text-gray-500 dark:text-gray-400">
          Showing {{ contracts.from }} to {{ contracts.to }} of {{ contracts.total }} contracts
        </div>
        <div class="flex gap-2">
          <Link
            v-for="link in contracts.links"
            :key="link.label"
            :href="link.url || '#'"
            :class="[
              'px-3 py-1 text-sm rounded border',
              link.active ? 'bg-teal-600 text-white border-teal-600' : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800'
            ]"
            v-html="link.label"
          />
        </div>
      </div>

      <div v-else class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
        <p>No contracts found</p>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ contracts: Object, filters: Object, members: Array, renewalLookaheadDays: Number })

const form = useForm({ status: props.filters?.status || '', member_id: props.filters?.member_id || '' })

function applyFilters() {
  form.get(route('admin.contracts.index'), { preserveState: true, preserveScroll: true })
}

function deleteContract(contract) {
  if (confirm(`Delete this contract for ${contract.member?.name_english}?`)) {
    router.delete(route('admin.contracts.destroy', contract.id), { preserveScroll: true })
  }
}

function isExpiringSoon(contract) {
  if (contract.status !== 'active') return false
  const daysLeft = (new Date(contract.end_date) - new Date()) / 86400000
  return daysLeft >= 0 && daysLeft <= (props.renewalLookaheadDays || 60)
}

function statusClass(status) {
  return {
    active: 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
    expired: 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400',
    terminated: 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
  }[status] ?? 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400'
}
</script>
