<template>
  <AdminLayout title="Add Fanclub Member">
    <form @submit.prevent="submit" class="space-y-6 max-w-2xl">
      <Section title="Member Info">
        <div class="grid grid-cols-2 gap-4">
          <FormField label="Name" required :error="form.errors.name" class="col-span-2">
            <TextInput v-model="form.name" :error="form.errors.name" />
          </FormField>
          <FormField label="Email" required :error="form.errors.email">
            <TextInput v-model="form.email" type="email" :error="form.errors.email" />
          </FormField>
          <FormField label="Phone" :error="form.errors.phone">
            <TextInput v-model="form.phone" :error="form.errors.phone" />
          </FormField>
          <FormField label="Tier" required :error="form.errors.tier">
            <SelectInput v-model="form.tier" :options="tierOptions" placeholder="Select…" :error="form.errors.tier" />
          </FormField>
          <FormField label="Status" required :error="form.errors.status">
            <SelectInput v-model="form.status" :options="statusOptions" :error="form.errors.status" />
          </FormField>
          <FormField label="Joined Date" required :error="form.errors.joined_at">
            <TextInput v-model="form.joined_at" type="date" :error="form.errors.joined_at" />
          </FormField>
          <FormField label="Expires Date" :error="form.errors.expires_at">
            <TextInput v-model="form.expires_at" type="date" :error="form.errors.expires_at" />
          </FormField>
        </div>
      </Section>

      <Section title="Benefits & Notes">
        <FormField label="Benefits" :error="form.errors.benefits">
          <TagsInput v-model="form.benefits" placeholder="Add benefit and press Enter…" :error="form.errors.benefits" />
        </FormField>
        <FormField label="Notes" class="mt-4" :error="form.errors.notes">
          <TextareaInput v-model="form.notes" :rows="3" :error="form.errors.notes" />
        </FormField>
      </Section>

      <div class="flex gap-3">
        <button type="submit" :disabled="form.processing" class="btn-primary">
          {{ form.processing ? 'Saving…' : 'Add Member' }}
        </button>
        <Link :href="route('admin.fanclub.index')" class="btn-secondary">Cancel</Link>
      </div>
    </form>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import FormField from '@/Components/Admin/FormField.vue'
import TextInput from '@/Components/Admin/TextInput.vue'
import SelectInput from '@/Components/Admin/SelectInput.vue'
import TextareaInput from '@/Components/Admin/TextareaInput.vue'
import TagsInput from '@/Components/Admin/TagsInput.vue'
import Section from '@/Components/Admin/SectionCard.vue'

const form = useForm({
  name: '', email: '', phone: '',
  tier: '', status: 'active',
  joined_at: '', expires_at: '',
  benefits: [], notes: '',
})

const tierOptions = [
  { value: 'basic', label: 'Basic' },
  { value: 'gold',  label: 'Gold'  },
]
const statusOptions = [
  { value: 'active',    label: 'Active'    },
  { value: 'expired',   label: 'Expired'   },
  { value: 'cancelled', label: 'Cancelled' },
]

const submit = () => form.post(route('admin.fanclub.store'))
</script>
