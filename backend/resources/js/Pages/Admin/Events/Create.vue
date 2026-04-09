<template>
  <AdminLayout title="New Event">
    <form @submit.prevent="submit" class="space-y-6 max-w-4xl">
      <Section title="Event Info">
        <div class="grid grid-cols-2 gap-4">
          <FormField label="Title" required :error="form.errors.title" class="col-span-2">
            <TextInput v-model="form.title" @input="autoSlug" :error="form.errors.title" />
          </FormField>
          <FormField label="Slug" required :error="form.errors.slug" class="col-span-2">
            <TextInput v-model="form.slug" :error="form.errors.slug" />
          </FormField>
          <FormField label="Type" required :error="form.errors.type">
            <SelectInput v-model="form.type" :options="typeOptions" placeholder="Select…" :error="form.errors.type" />
          </FormField>
          <FormField label="Status" required :error="form.errors.status">
            <SelectInput v-model="form.status" :options="statusOptions" :error="form.errors.status" />
          </FormField>
          <FormField label="Date" required :error="form.errors.date">
            <TextInput v-model="form.date" type="date" :error="form.errors.date" />
          </FormField>
          <FormField label="End Date" :error="form.errors.end_date">
            <TextInput v-model="form.end_date" type="date" :error="form.errors.end_date" />
          </FormField>
        </div>
      </Section>

      <Section title="Location">
        <div class="grid grid-cols-2 gap-4">
          <FormField label="Venue" :error="form.errors.venue">
            <TextInput v-model="form.venue" :error="form.errors.venue" />
          </FormField>
          <FormField label="Location" :error="form.errors.location">
            <TextInput v-model="form.location" placeholder="City, Country" :error="form.errors.location" />
          </FormField>
          <FormField label="Ticket URL" :error="form.errors.ticket_url" class="col-span-2">
            <TextInput v-model="form.ticket_url" type="url" placeholder="https://…" :error="form.errors.ticket_url" />
          </FormField>
        </div>
      </Section>

      <Section title="Details">
        <FormField label="Description" :error="form.errors.description">
          <TextareaInput v-model="form.description" :rows="4" :error="form.errors.description" />
        </FormField>
        <FormField label="Image" class="mt-4" :error="form.errors.image">
          <ImageUpload @change="f => form.image = f" :error="form.errors.image" />
        </FormField>
      </Section>

      <TranslationsSection :form="form" :fields="transFields" />

      <div class="flex gap-3">
        <button type="submit" :disabled="form.processing" class="btn-primary">
          {{ form.processing ? 'Saving…' : 'Create Event' }}
        </button>
        <Link :href="route('admin.events.index')" class="btn-secondary">Cancel</Link>
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
import ImageUpload from '@/Components/Admin/ImageUpload.vue'
import TranslationsSection from '@/Components/Admin/TranslationsSection.vue'
import Section from '@/Components/Admin/SectionCard.vue'

const form = useForm({
  title: '', slug: '', type: '', status: 'upcoming',
  date: '', end_date: '',
  venue: '', location: '', ticket_url: '',
  description: '', image: null,
  trans_ms_title: '', trans_ms_description: '', trans_ms_venue: '', trans_ms_location: '',
  trans_ja_title: '', trans_ja_description: '', trans_ja_venue: '', trans_ja_location: '',
})

const typeOptions = [
  { value: 'concert',    label: 'Concert'      },
  { value: 'meet-greet', label: 'Meet & Greet' },
  { value: 'handshake',  label: 'Handshake'    },
  { value: 'online',     label: 'Online'        },
  { value: 'other',      label: 'Other'         },
]
const statusOptions = [
  { value: 'upcoming',  label: 'Upcoming'  },
  { value: 'ongoing',   label: 'Ongoing'   },
  { value: 'completed', label: 'Completed' },
  { value: 'cancelled', label: 'Cancelled' },
]
const transFields = [
  { key: 'title',       label: 'Title',       type: 'text'     },
  { key: 'description', label: 'Description', type: 'textarea' },
  { key: 'venue',       label: 'Venue',       type: 'text'     },
  { key: 'location',    label: 'Location',    type: 'text'     },
]

const autoSlug = () => {
  form.slug = form.title.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '')
}

const submit = () => form.post(route('admin.events.store'), { forceFormData: true })
</script>

