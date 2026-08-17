<template>
  <AdminLayout :title="`New Contract — ${member.name_english}`">
    <template #actions>
      <Link :href="route('admin.members.show', member.id)" class="btn-secondary text-sm">Cancel</Link>
    </template>

    <form @submit.prevent="submit" class="max-w-2xl">
      <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6 space-y-6">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date *</label>
            <DateInput v-model="form.start_date" placeholder="Select start date" :error="form.errors.start_date" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date *</label>
            <DateInput v-model="form.end_date" :minDate="form.start_date" placeholder="Select end date" :error="form.errors.end_date" required />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status *</label>
          <select v-model="form.status" class="input" required>
            <option value="active">Active</option>
            <option value="expired">Expired</option>
            <option value="terminated">Terminated</option>
          </select>
          <p v-if="form.errors.status" class="text-red-500 dark:text-red-400 text-sm mt-1">{{ form.errors.status }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Signed Contract Document</label>
          <select v-model="form.document_id" class="input">
            <option :value="null">None on file</option>
            <option v-for="doc in signedContracts" :key="doc.id" :value="doc.id">{{ doc.title }}</option>
          </select>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Upload the signed PDF from the member's Compliance Documents section first, then link it here.
          </p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Exclusivity Terms</label>
          <textarea v-model="form.exclusivity_terms" class="input" rows="4"></textarea>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
          <textarea v-model="form.notes" class="input" rows="3"></textarea>
        </div>

        <div class="flex gap-3 pt-4">
          <button type="submit" :disabled="form.processing" class="btn-primary">
            {{ form.processing ? 'Creating...' : 'Create Contract' }}
          </button>
          <Link :href="route('admin.members.show', member.id)" class="btn-secondary">Cancel</Link>
        </div>
      </div>
    </form>
  </AdminLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DateInput from '@/Components/Admin/DateInput.vue'

const props = defineProps({ member: Object, signedContracts: Array })

const form = useForm({
  start_date: null,
  end_date: null,
  status: 'active',
  document_id: null,
  exclusivity_terms: '',
  notes: '',
})

function submit() {
  form.post(route('admin.members.contracts.store', props.member.id))
}
</script>
