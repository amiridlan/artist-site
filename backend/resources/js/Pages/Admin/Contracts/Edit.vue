<template>
  <AdminLayout :title="`Edit Contract — ${contract.member.name_english}`">
    <template #actions>
      <Link :href="route('admin.members.show', contract.member.id)" class="btn-secondary text-sm">Cancel</Link>
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
            {{ form.processing ? 'Saving...' : 'Save Contract' }}
          </button>
          <Link :href="route('admin.members.show', contract.member.id)" class="btn-secondary">Cancel</Link>
        </div>
      </div>
    </form>
  </AdminLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DateInput from '@/Components/Admin/DateInput.vue'

const props = defineProps({ contract: Object, signedContracts: Array })

const form = useForm({
  start_date: props.contract.start_date,
  end_date: props.contract.end_date,
  status: props.contract.status,
  document_id: props.contract.document_id,
  exclusivity_terms: props.contract.exclusivity_terms || '',
  notes: props.contract.notes || '',
})

function submit() {
  form.put(route('admin.contracts.update', props.contract.id))
}
</script>
