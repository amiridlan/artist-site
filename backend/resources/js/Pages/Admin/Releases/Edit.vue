<template>
  <AdminLayout :title="`Edit — ${release.title}`">
    <form @submit.prevent="submit" class="space-y-6 max-w-4xl">
      <Section title="Release Info">
        <div class="grid grid-cols-2 gap-4">
          <FormField label="Title" required :error="form.errors.title" class="col-span-2">
            <TextInput v-model="form.title" :error="form.errors.title" />
          </FormField>
          <FormField label="Slug" required :error="form.errors.slug" class="col-span-2">
            <TextInput v-model="form.slug" :error="form.errors.slug" />
          </FormField>
          <FormField label="Type" required :error="form.errors.type">
            <SelectInput v-model="form.type" :options="typeOptions" :error="form.errors.type" />
          </FormField>
          <FormField label="Release Date" required :error="form.errors.release_date">
            <TextInput v-model="form.release_date" type="date" :error="form.errors.release_date" />
          </FormField>
        </div>
      </Section>

      <Section title="Media">
        <FormField label="Cover Image" :error="form.errors.cover_image">
          <ImageUpload :current-url="coverUrl" @change="f => form.cover_image = f" :error="form.errors.cover_image" />
        </FormField>
        <FormField label="Description" class="mt-4" :error="form.errors.description">
          <TextareaInput v-model="form.description" :rows="3" :error="form.errors.description" />
        </FormField>
      </Section>

      <Section title="Tracks">
        <TrackList v-model="form.tracks" />
        <p v-if="form.errors.tracks" class="mt-1 text-sm text-red-600">{{ form.errors.tracks }}</p>
      </Section>

      <Section title="Streaming Links">
        <KeyValueInput v-model="form.streaming_links" />
      </Section>

      <TranslationsSection :form="form" :fields="transFields" />

      <div class="flex gap-3">
        <button type="submit" :disabled="form.processing" class="btn-primary">
          {{ form.processing ? 'Saving…' : 'Save Changes' }}
        </button>
        <Link :href="route('admin.releases.index')" class="btn-secondary">Cancel</Link>
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
import TrackList from '@/Components/Admin/TrackList.vue'
import KeyValueInput from '@/Components/Admin/KeyValueInput.vue'
import TranslationsSection from '@/Components/Admin/TranslationsSection.vue'
import Section from '@/Components/Admin/SectionCard.vue'

const props = defineProps({ release: Object, translations: Object, coverUrl: String })

const form = useForm({
  title:           props.release.title,
  slug:            props.release.slug,
  type:            props.release.type,
  release_date:    props.release.release_date,
  description:     props.release.description     ?? '',
  cover_image:     null,
  tracks:          props.release.tracks          ?? [{ number: 1, title: '', duration: '' }],
  streaming_links: props.release.streaming_links ?? {},
  ...props.translations,
})

const typeOptions = [
  { value: 'single',         label: 'Single'         },
  { value: 'album',          label: 'Album'           },
  { value: 'ep',             label: 'EP'              },
  { value: 'digital-single', label: 'Digital Single'  },
]
const transFields = [
  { key: 'title',       label: 'Title',       type: 'text'     },
  { key: 'description', label: 'Description', type: 'textarea' },
]

const submit = () => form.transform(data => ({ ...data, _method: 'PUT' })).post(route('admin.releases.update', props.release.id), { forceFormData: true })
</script>

