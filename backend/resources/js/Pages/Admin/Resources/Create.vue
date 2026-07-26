<template>
  <AdminLayout title="Create Resource">
    <template #actions>
      <Link :href="route('admin.resources.index')" class="btn-secondary text-sm">
        Cancel
      </Link>
    </template>

    <form @submit.prevent="submit" class="max-w-2xl">
      <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6 space-y-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name *</label>
          <input v-model="form.name" type="text" class="input" required>
          <p v-if="form.errors.name" class="text-red-500 dark:text-red-400 text-sm mt-1">{{ form.errors.name }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type *</label>
          <select v-model="form.type" class="input" required>
            <option value="">Select type...</option>
            <option value="venue">Venue</option>
            <option value="equipment">Equipment</option>
            <option value="vehicle">Vehicle</option>
          </select>
          <p v-if="form.errors.type" class="text-red-500 dark:text-red-400 text-sm mt-1">{{ form.errors.type }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
          <textarea v-model="form.description" class="input" rows="4" placeholder="Describe this resource..."></textarea>
          <p v-if="form.errors.description" class="text-red-500 dark:text-red-400 text-sm mt-1">{{ form.errors.description }}</p>
        </div>

        <div>
          <label class="flex items-center gap-2">
            <input v-model="form.is_active" type="checkbox" class="rounded text-teal-600 dark:text-teal-500">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</span>
          </label>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Inactive resources won't appear in event creation forms</p>
        </div>

        <div class="flex gap-3 pt-4">
          <button type="submit" :disabled="form.processing" class="btn-primary">
            {{ form.processing ? 'Creating...' : 'Create Resource' }}
          </button>
          <Link :href="route('admin.resources.index')" class="btn-secondary">
            Cancel
          </Link>
        </div>
      </div>
    </form>
  </AdminLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const form = useForm({
  name: '',
  type: '',
  description: '',
  is_active: true
})

function submit() {
  form.post(route('admin.resources.store'))
}
</script>
