<template>
  <AdminLayout title="Resources">
    <template #actions>
      <Link :href="route('admin.resources.create')" class="btn-primary text-sm">
        + New Resource
      </Link>
    </template>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-4 mb-6">
      <form @submit.prevent="applyFilters" class="flex gap-3">
        <select v-model="form.type" class="input text-sm">
          <option value="">All Types</option>
          <option value="venue">Venue</option>
          <option value="equipment">Equipment</option>
          <option value="vehicle">Vehicle</option>
        </select>

        <select v-model="form.is_active" class="input text-sm">
          <option value="">All Status</option>
          <option value="1">Active</option>
          <option value="0">Inactive</option>
        </select>

        <button type="submit" class="btn-primary text-sm">Apply</button>
      </form>
    </div>

    <!-- Resources table -->
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Type</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Description</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          <tr v-for="resource in resources.data" :key="resource.id" class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
            <td class="px-6 py-4">
              <div class="font-medium text-gray-900 dark:text-gray-100">{{ resource.name }}</div>
            </td>
            <td class="px-6 py-4">
              <span class="px-2 py-1 text-xs rounded-full capitalize" :class="typeClass(resource.type)">
                {{ resource.type }}
              </span>
            </td>
            <td class="px-6 py-4">
              <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2">{{ resource.description || '-' }}</p>
            </td>
            <td class="px-6 py-4">
              <span class="px-2 py-1 text-xs rounded-full" :class="resource.isActive ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-400'">
                {{ resource.isActive ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td class="px-6 py-4 text-right space-x-2">
              <Link :href="route('admin.resources.edit', resource.id)" class="text-teal-600 dark:text-teal-400 hover:text-teal-800 dark:hover:text-teal-300 text-sm">
                Edit
              </Link>
              <button @click="deleteResource(resource)" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 text-sm">
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Empty state -->
      <div v-if="!resources.data?.length" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
        </svg>
        <p class="mt-2">No resources found</p>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  resources: Object,
  filters: Object
})

const form = useForm({
  type: props.filters?.type || '',
  is_active: props.filters?.is_active || ''
})

function applyFilters() {
  form.get(route('admin.resources.index'), {
    preserveState: true,
    preserveScroll: true
  })
}

function deleteResource(resource) {
  if (confirm(`Delete "${resource.name}"?`)) {
    router.delete(route('admin.resources.destroy', resource.id))
  }
}

function typeClass(type) {
  const classes = {
    venue: 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300',
    equipment: 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
    vehicle: 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300'
  }
  return classes[type] || 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-400'
}
</script>
