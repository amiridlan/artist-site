<template>
  <AdminLayout title="News">
    <template #actions>
      <Link :href="route('admin.news.create')" class="btn-primary">+ New Article</Link>
    </template>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Title</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Featured</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Published</th>
            <th class="px-6 py-3" />
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr v-for="article in articles" :key="article.id" class="hover:bg-gray-50">
            <td class="px-6 py-3 font-medium text-gray-800 max-w-xs truncate">{{ article.title }}</td>
            <td class="px-6 py-3">
              <span class="px-2 py-0.5 rounded-full text-xs font-medium capitalize bg-blue-100 text-blue-700">{{ article.category }}</span>
            </td>
            <td class="px-6 py-3 text-gray-500">{{ formatDate(article.date) }}</td>
            <td class="px-6 py-3">
              <span :class="article.featured ? 'text-teal-600' : 'text-gray-300'">{{ article.featured ? '★' : '☆' }}</span>
            </td>
            <td class="px-6 py-3">
              <span :class="article.published ? 'text-green-600' : 'text-gray-400'" class="text-xs font-medium">
                {{ article.published ? 'Published' : 'Draft' }}
              </span>
            </td>
            <td class="px-6 py-3 text-right">
              <Link :href="route('admin.news.edit', article.id)" class="text-teal-600 hover:text-teal-800 text-sm font-medium mr-4">Edit</Link>
              <button @click="destroy(article)" class="text-red-400 hover:text-red-600 text-sm font-medium">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-if="!articles.length" class="px-6 py-10 text-center text-gray-400">No articles yet.</p>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'

defineProps({ articles: Array })

const formatDate = (d) => d ? new Date(d).toLocaleDateString('en-GB') : '—'

const destroy = (a) => {
  if (!confirm(`Delete "${a.title}"?`)) return
  router.delete(route('admin.news.destroy', a.id))
}
</script>

