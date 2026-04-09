<template>
  <AdminLayout :title="`Edit — ${member.name_english}`">
    <form @submit.prevent="submit" class="space-y-6 max-w-4xl" enctype="multipart/form-data">
      <!-- Basic Info -->
      <Section title="Basic Info">
        <div class="grid grid-cols-2 gap-4">
          <FormField label="Name (English)" required :error="form.errors.name_english">
            <TextInput v-model="form.name_english" :error="form.errors.name_english" />
          </FormField>
          <FormField label="Name (Native)" :error="form.errors.name_native">
            <TextInput v-model="form.name_native" :error="form.errors.name_native" />
          </FormField>
          <FormField label="Nickname" :error="form.errors.nickname">
            <TextInput v-model="form.nickname" :error="form.errors.nickname" />
          </FormField>
          <FormField label="Slug" required :error="form.errors.slug">
            <TextInput v-model="form.slug" :error="form.errors.slug" />
          </FormField>
          <FormField label="Generation" required :error="form.errors.generation">
            <SelectInput v-model="form.generation" :options="genOptions" :error="form.errors.generation" />
          </FormField>
          <FormField label="Status" required :error="form.errors.status">
            <SelectInput v-model="form.status" :options="statusOptions" :error="form.errors.status" />
          </FormField>
        </div>
      </Section>

      <!-- Profile Details -->
      <Section title="Profile Details">
        <div class="grid grid-cols-2 gap-4">
          <FormField label="Birthdate" :error="form.errors.birthdate">
            <TextInput v-model="form.birthdate" :error="form.errors.birthdate" />
          </FormField>
          <FormField label="Age" :error="form.errors.age">
            <TextInput v-model="form.age" type="number" :error="form.errors.age" />
          </FormField>
          <FormField label="Height (cm)" :error="form.errors.height">
            <TextInput v-model="form.height" type="number" :error="form.errors.height" />
          </FormField>
          <FormField label="Blood Type" :error="form.errors.blood_type">
            <TextInput v-model="form.blood_type" :error="form.errors.blood_type" />
          </FormField>
          <FormField label="Hometown" :error="form.errors.hometown">
            <TextInput v-model="form.hometown" :error="form.errors.hometown" />
          </FormField>
          <FormField label="Member Color" :error="form.errors.color">
            <div class="flex gap-2 items-center">
              <input type="color" v-model="form.color" class="h-10 w-16 rounded border border-gray-300 cursor-pointer" />
              <TextInput v-model="form.color" :error="form.errors.color" />
            </div>
          </FormField>
          <FormField label="Sort Order" :error="form.errors.sort_order">
            <TextInput v-model="form.sort_order" type="number" :error="form.errors.sort_order" />
          </FormField>
          <FormField label="Join Date" :error="form.errors.join_date">
            <TextInput v-model="form.join_date" type="date" :error="form.errors.join_date" />
          </FormField>
        </div>
      </Section>

      <!-- Media -->
      <Section title="Media">
        <div class="grid grid-cols-2 gap-4">
          <FormField label="Profile Photo" :error="form.errors.photo">
            <ImageUpload :current-url="photoUrl" @change="f => form.photo = f" :error="form.errors.photo" />
          </FormField>
          <FormField label="Cover Image" :error="form.errors.cover_image">
            <ImageUpload :current-url="coverUrl" @change="f => form.cover_image = f" :error="form.errors.cover_image" />
          </FormField>
        </div>
      </Section>

      <!-- Bio & Hobbies -->
      <Section title="Bio & Hobbies">
        <FormField label="Bio" :error="form.errors.bio">
          <TextareaInput v-model="form.bio" :rows="5" :error="form.errors.bio" />
        </FormField>
        <FormField label="Hobbies" class="mt-4" :error="form.errors.hobbies">
          <TagsInput v-model="form.hobbies" />
        </FormField>
      </Section>

      <!-- Social Links -->
      <Section title="Social Links">
        <KeyValueInput v-model="form.social" />
      </Section>

      <!-- Translations -->
      <TranslationsSection :form="form" :fields="transFields" />

      <div class="flex gap-3">
        <button type="submit" :disabled="form.processing" class="btn-primary">
          {{ form.processing ? 'Saving…' : 'Save Changes' }}
        </button>
        <Link :href="route('admin.members.index')" class="btn-secondary">Cancel</Link>
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
import TagsInput from '@/Components/Admin/TagsInput.vue'
import KeyValueInput from '@/Components/Admin/KeyValueInput.vue'
import TranslationsSection from '@/Components/Admin/TranslationsSection.vue'
import Section from '@/Components/Admin/SectionCard.vue'

const props = defineProps({ member: Object, translations: Object, photoUrl: String, coverUrl: String })

const form = useForm({
  name_english: props.member.name_english,
  name_native:  props.member.name_native  ?? '',
  nickname:     props.member.nickname     ?? '',
  slug:         props.member.slug,
  generation:   props.member.generation,
  status:       props.member.status,
  birthdate:    props.member.birthdate    ?? '',
  age:          props.member.age          ?? '',
  height:       props.member.height       ?? '',
  blood_type:   props.member.blood_type   ?? '',
  hometown:     props.member.hometown     ?? '',
  color:        props.member.color        ?? '#000000',
  sort_order:   props.member.sort_order   ?? 0,
  join_date:    props.member.join_date    ?? '',
  bio:          props.member.bio          ?? '',
  hobbies:      props.member.hobbies      ?? [],
  social:       props.member.social       ?? {},
  photo:        null,
  cover_image:  null,
  ...props.translations,
})

const genOptions    = [{ value: '1st', label: '1st Generation' }, { value: '2nd', label: '2nd Generation' }]
const statusOptions = [{ value: 'active', label: 'Active' }, { value: 'graduated', label: 'Graduated' }, { value: 'concluded', label: 'Concluded' }]
const transFields   = [{ key: 'bio', label: 'Bio', type: 'textarea' }, { key: 'hometown', label: 'Hometown', type: 'text' }]

const submit = () => form.transform(data => ({ ...data, _method: 'PUT' })).post(route('admin.members.update', props.member.id), { forceFormData: true })
</script>

