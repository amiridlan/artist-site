<template>
  <AdminLayout :title="member.name_english">
    <template #actions>
      <div class="flex items-center gap-3">
        <Link :href="route('admin.members.edit', member.id)" class="btn-primary">Edit</Link>
        <Link :href="route('admin.members.index')" class="btn-secondary">Back to List</Link>
      </div>
    </template>

    <div class="space-y-6 max-w-4xl">
      <!-- Header -->
      <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6 flex items-center gap-4">
        <img v-if="photoUrl" :src="photoUrl" class="w-16 h-16 rounded-full object-cover flex-shrink-0" />
        <div v-else class="w-16 h-16 rounded-full bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center text-teal-700 dark:text-teal-300 font-semibold text-xl flex-shrink-0">
          {{ member.name_english.charAt(0) }}
        </div>
        <div>
          <div class="flex items-center gap-2">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ member.name_english }}</h2>
            <span v-if="member.name_native" class="text-sm text-gray-400 dark:text-gray-500">{{ member.name_native }}</span>
          </div>
          <div class="flex items-center gap-2 mt-1">
            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
              {{ member.generation }} Gen
            </span>
            <span class="px-2 py-0.5 rounded-full text-xs font-medium capitalize" :class="statusClass(member.status)">
              {{ member.status }}
            </span>
          </div>
        </div>
      </div>

      <!-- Basic Info -->
      <Section title="Basic Info">
        <div class="grid grid-cols-2 gap-4">
          <Field label="Name (English)" :value="member.name_english" />
          <Field label="Name (Native)" :value="member.name_native" />
          <Field label="Nickname" :value="member.nickname" />
          <Field label="Slug" :value="member.slug" />
          <Field label="Generation" :value="member.generation" />
          <Field label="Status" :value="member.status" class="capitalize" />
        </div>
      </Section>

      <!-- Profile Details -->
      <Section title="Profile Details">
        <div class="grid grid-cols-2 gap-4">
          <Field label="Birthdate" :value="member.birthdate" />
          <Field label="Age" :value="member.age" />
          <Field label="Height (cm)" :value="member.height" />
          <Field label="Blood Type" :value="member.blood_type" />
          <Field label="Sort Order" :value="member.sort_order" />
          <Field label="Join Date" :value="member.join_date" />
          <div>
            <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">Member Color</p>
            <div class="flex items-center gap-2">
              <span class="w-5 h-5 rounded border border-gray-200 dark:border-gray-700 flex-shrink-0" :style="{ backgroundColor: member.color || '#000000' }" />
              <span class="text-sm text-gray-800 dark:text-gray-100">{{ member.color || '—' }}</span>
            </div>
          </div>
        </div>
      </Section>

      <!-- Media -->
      <Section title="Media">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">Profile Photo</p>
            <img v-if="photoUrl" :src="photoUrl" class="w-32 h-32 rounded-lg object-cover" />
            <p v-else class="text-sm text-gray-400 dark:text-gray-500">—</p>
          </div>
          <div>
            <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">Cover Image</p>
            <img v-if="coverUrl" :src="coverUrl" class="w-full h-32 rounded-lg object-cover" />
            <p v-else class="text-sm text-gray-400 dark:text-gray-500">—</p>
          </div>
        </div>
      </Section>

      <!-- Hobbies -->
      <Section title="Hobbies">
        <div v-if="member.hobbies?.length" class="flex flex-wrap gap-2">
          <span v-for="hobby in member.hobbies" :key="hobby" class="px-2.5 py-1 rounded-full text-xs font-medium bg-teal-50 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300">
            {{ hobby }}
          </span>
        </div>
        <p v-else class="text-sm text-gray-400 dark:text-gray-500">—</p>
      </Section>

      <!-- Social Links -->
      <Section title="Social Links">
        <div v-if="socialEntries.length" class="space-y-1.5">
          <div v-for="[platform, url] in socialEntries" :key="platform" class="flex items-center gap-2 text-sm">
            <span class="capitalize text-gray-500 dark:text-gray-400 w-24 flex-shrink-0">{{ platform }}</span>
            <a :href="url" target="_blank" rel="noopener noreferrer" class="text-teal-600 dark:text-teal-400 hover:underline truncate">{{ url }}</a>
          </div>
        </div>
        <p v-else class="text-sm text-gray-400 dark:text-gray-500">—</p>
      </Section>

      <!-- Bio & Hometown -->
      <Section title="Bio & Hometown">
        <div v-for="field in transFields" :key="field.key" class="py-3 first:pt-0 border-b border-gray-100 dark:border-gray-800 last:border-b-0 last:pb-0">
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500 mb-2">{{ field.label }}</p>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <Field label="English" :value="member[field.key]" />
            <Field label="Malay" :value="translations[`trans_ms_${field.key}`]" />
            <Field label="Japanese" :value="translations[`trans_ja_${field.key}`]" />
          </div>
        </div>
      </Section>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, h } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link } from '@inertiajs/vue3'
import Section from '@/Components/Admin/SectionCard.vue'

const props = defineProps({
  member: Object,
  translations: Object,
  photoUrl: String,
  coverUrl: String,
})

const transFields = [
  { key: 'bio', label: 'Bio' },
  { key: 'hometown', label: 'Hometown' },
]

const socialEntries = computed(() => Object.entries(props.member.social || {}).filter(([, url]) => url))

const statusClass = (s) => ({
  active:    'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
  graduated: 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
  concluded: 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400',
}[s] ?? 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400')

// Small read-only label/value pair, defined locally since it's only used on this page.
const Field = (props) => h('div', [
  h('p', { class: 'text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1' }, props.label),
  h('p', { class: ['text-sm text-gray-800 dark:text-gray-100', props.class] }, props.value || props.value === 0 ? String(props.value) : '—'),
])
Field.props = ['label', 'value', 'class']
</script>
