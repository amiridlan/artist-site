<template>
  <AdminLayout :title="`Edit — ${article.title}`">
    <form @submit.prevent="submit" class="space-y-6 max-w-4xl">
      <Section title="Article Info">
        <div class="grid grid-cols-2 gap-4">
          <FormField label="Title" required :error="form.errors.title" class="col-span-2">
            <TextInput v-model="form.title" :error="form.errors.title" />
          </FormField>
          <FormField label="Slug" required :error="form.errors.slug" class="col-span-2">
            <TextInput v-model="form.slug" :error="form.errors.slug" />
          </FormField>
          <FormField label="Category" required :error="form.errors.category">
            <SelectInput v-model="form.category" :options="categoryOptions" :error="form.errors.category" />
          </FormField>
          <FormField label="Date" required :error="form.errors.date">
            <TextInput v-model="form.date" type="date" :error="form.errors.date" />
          </FormField>
          <FormField label="Featured">
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" v-model="form.featured" class="rounded border-gray-300 text-teal-600 focus:ring-teal-500" />
              <span class="text-sm text-gray-700">Mark as featured</span>
            </label>
          </FormField>
          <FormField label="Published">
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" v-model="form.published" class="rounded border-gray-300 text-teal-600 focus:ring-teal-500" />
              <span class="text-sm text-gray-700">Published</span>
            </label>
          </FormField>
        </div>
      </Section>

      <Section title="Content">
        <FormField label="Excerpt" required :error="form.errors.excerpt">
          <TextareaInput v-model="form.excerpt" :rows="3" :error="form.errors.excerpt" />
        </FormField>
        <FormField label="Full Content" class="mt-4" :error="form.errors.content">
          <TextareaInput v-model="form.content" :rows="10" :error="form.errors.content" />
        </FormField>
      </Section>

      <Section title="Media">
        <FormField label="Image" :error="form.errors.image">
          <ImageUpload :current-url="imageUrl" @change="f => form.image = f" :error="form.errors.image" />
        </FormField>
      </Section>

      <TranslationsSection :form="form" :fields="transFields" />

      <div class="flex gap-3">
        <button type="submit" :disabled="form.processing" class="btn-primary">
          {{ form.processing ? 'Saving…' : 'Save Changes' }}
        </button>
        <Link :href="route('admin.news.index')" class="btn-secondary">Cancel</Link>
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

const props = defineProps({ article: Object, translations: Object, imageUrl: String })

const form = useForm({
  title:     props.article.title,
  slug:      props.article.slug,
  category:  props.article.category,
  date:      props.article.date,
  featured:  props.article.featured,
  published: props.article.published,
  excerpt:   props.article.excerpt   ?? '',
  content:   props.article.content   ?? '',
  image:     null,
  ...props.translations,
})

const categoryOptions = [
  { value: 'news',    label: 'News'    },
  { value: 'fanclub', label: 'Fanclub' },
  { value: 'store',   label: 'Store'   },
  { value: 'event',   label: 'Event'   },
  { value: 'release', label: 'Release' },
]
const transFields = [
  { key: 'title',   label: 'Title',   type: 'text'     },
  { key: 'excerpt', label: 'Excerpt', type: 'textarea' },
  { key: 'content', label: 'Content', type: 'textarea' },
]

const submit = () => form.transform(data => ({ ...data, _method: 'PUT' })).post(route('admin.news.update', props.article.id), { forceFormData: true })
</script>

