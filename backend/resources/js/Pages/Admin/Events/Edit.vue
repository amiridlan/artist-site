<template>
  <AdminLayout :title="`Edit — ${event.title}`">
    <form @submit.prevent="submit" class="space-y-6 max-w-4xl">
      <Section title="Event Info">
        <div class="grid grid-cols-2 gap-4">
          <FormField label="Slug" required :error="form.errors.slug" class="col-span-2">
            <TextInput v-model="form.slug" :error="form.errors.slug" />
          </FormField>
          <FormField label="Type" required :error="form.errors.type">
            <SelectInput v-model="form.type" :options="typeOptions" :error="form.errors.type" />
          </FormField>
          <FormField label="Status" required :error="form.errors.status">
            <SelectInput v-model="form.status" :options="statusOptions" :error="form.errors.status" />
          </FormField>
          <FormField label="Date" required :error="form.errors.date">
            <DateInput v-model="form.date" :error="form.errors.date" />
          </FormField>
          <FormField label="End Date" :error="form.errors.end_date">
            <DateInput v-model="form.end_date" :error="form.errors.end_date" />
          </FormField>
        </div>
      </Section>

      <Section title="Location">
        <FormField label="Ticket URL" :error="form.errors.ticket_url">
          <TextInput v-model="form.ticket_url" type="url" :error="form.errors.ticket_url" />
        </FormField>
      </Section>

      <Section title="Media">
        <FormField label="Image" :error="form.errors.image">
          <ImageUpload :current-url="imageUrl" @change="f => form.image = f" :error="form.errors.image" />
        </FormField>
      </Section>

      <TranslatableFieldGroup :form="form" :fields="transFields" title="Content" />

      <div class="flex gap-3">
        <button type="submit" :disabled="form.processing" class="btn-primary">
          {{ form.processing ? 'Saving…' : 'Save Changes' }}
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
import DateInput from '@/Components/Admin/DateInput.vue'
import SelectInput from '@/Components/Admin/SelectInput.vue'
import ImageUpload from '@/Components/Admin/ImageUpload.vue'
import TranslatableFieldGroup from '@/Components/Admin/TranslatableFieldGroup.vue'
import Section from '@/Components/Admin/SectionCard.vue'

const props = defineProps({ event: Object, translations: Object, imageUrl: String })

const form = useForm({
  title:      props.event.title,
  slug:       props.event.slug,
  type:       props.event.type,
  status:     props.event.status,
  date:       props.event.date,
  end_date:   props.event.end_date   ?? '',
  venue:      props.event.venue      ?? '',
  location:   props.event.location   ?? '',
  ticket_url: props.event.ticket_url ?? '',
  description: props.event.description ?? '',
  image:      null,
  ...props.translations,
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
  { key: 'title',       label: 'Title',       type: 'text',     required: true },
  { key: 'description', label: 'Description', type: 'textarea' },
  { key: 'venue',       label: 'Venue',       type: 'text'     },
  { key: 'location',    label: 'Location',    type: 'text'     },
]

const submit = () => form.transform(data => ({ ...data, _method: 'PUT' })).post(route('admin.events.update', props.event.id), { forceFormData: true })
</script>

