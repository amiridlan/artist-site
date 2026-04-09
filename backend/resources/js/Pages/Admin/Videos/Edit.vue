<template>
  <AdminLayout :title="`Edit — ${video.title}`">
    <form @submit.prevent="submit" class="space-y-6 max-w-4xl">
      <Section title="Video Info">
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
          <FormField label="Date" required :error="form.errors.date">
            <TextInput v-model="form.date" type="date" :error="form.errors.date" />
          </FormField>
          <FormField label="YouTube ID" :error="form.errors.youtube_id">
            <TextInput v-model="form.youtube_id" :error="form.errors.youtube_id" />
          </FormField>
          <FormField label="Duration" :error="form.errors.duration">
            <TextInput v-model="form.duration" :error="form.errors.duration" />
          </FormField>
          <FormField label="Venue" :error="form.errors.venue" class="col-span-2">
            <TextInput v-model="form.venue" :error="form.errors.venue" />
          </FormField>
        </div>
      </Section>

      <Section title="Media">
        <FormField label="Thumbnail" :error="form.errors.thumbnail">
          <ImageUpload :current-url="thumbnailUrl" @change="f => form.thumbnail = f" :error="form.errors.thumbnail" />
        </FormField>
        <FormField label="Description" class="mt-4" :error="form.errors.description">
          <TextareaInput v-model="form.description" :rows="3" :error="form.errors.description" />
        </FormField>
      </Section>

      <TranslationsSection :form="form" :fields="transFields" />

      <div class="flex gap-3">
        <button type="submit" :disabled="form.processing" class="btn-primary">
          {{ form.processing ? 'Saving…' : 'Save Changes' }}
        </button>
        <Link :href="route('admin.videos.index')" class="btn-secondary">Cancel</Link>
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

const props = defineProps({ video: Object, translations: Object, thumbnailUrl: String })

const form = useForm({
  title:       props.video.title,
  slug:        props.video.slug,
  type:        props.video.type,
  date:        props.video.date,
  youtube_id:  props.video.youtube_id  ?? '',
  duration:    props.video.duration    ?? '',
  venue:       props.video.venue       ?? '',
  description: props.video.description ?? '',
  thumbnail:   null,
  ...props.translations,
})

const typeOptions = [
  { value: 'music-video',       label: 'Music Video'       },
  { value: 'performance',       label: 'Performance'       },
  { value: 'dance-practice',    label: 'Dance Practice'    },
  { value: 'behind-the-scenes', label: 'Behind the Scenes' },
]
const transFields = [
  { key: 'title',       label: 'Title',       type: 'text'     },
  { key: 'description', label: 'Description', type: 'textarea' },
]

const submit = () => form.transform(data => ({ ...data, _method: 'PUT' })).post(route('admin.videos.update', props.video.id), { forceFormData: true })
</script>

