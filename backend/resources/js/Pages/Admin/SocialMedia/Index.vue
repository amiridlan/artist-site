<template>
  <AdminLayout title="Social Media Analytics">
    <template #actions>
      <div class="flex items-center gap-3">
        <span v-if="lastFetched" class="text-xs text-gray-400">
          Last synced: {{ formatDateTime(lastFetched) }}
        </span>
        <button
          @click="syncNow"
          :disabled="syncing"
          class="flex items-center gap-1.5 px-3 py-1.5 text-sm bg-teal-600 text-white rounded-lg hover:bg-teal-700 disabled:opacity-50 transition-colors"
        >
          <svg class="w-4 h-4" :class="{ 'animate-spin': syncing }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          {{ syncing ? 'Syncing…' : 'Sync Now' }}
        </button>
      </div>
    </template>

    <!-- Platform cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 mb-8">
      <div
        v-for="p in platforms"
        :key="p.id"
        class="bg-white rounded-xl border border-gray-200 p-5 flex flex-col gap-3"
      >
        <!-- Header -->
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" :style="{ background: platformColor(p.platform) }">
              <component :is="platformIcon(p.platform)" class="w-4 h-4 text-white" />
            </div>
            <div>
              <p class="text-sm font-semibold text-gray-800">{{ p.display_name }}</p>
              <p class="text-xs text-gray-400">{{ p.handle }}</p>
            </div>
          </div>
          <span
            class="text-xs px-1.5 py-0.5 rounded-full font-medium"
            :class="p.delta.followers >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
          >
            {{ p.delta.followers >= 0 ? '↑' : '↓' }} {{ Math.abs(p.delta.followers_pct) }}%
          </span>
        </div>

        <!-- Main metric -->
        <div>
          <p class="text-2xl font-bold text-gray-900">{{ formatNumber(p.current?.followers) }}</p>
          <p class="text-xs text-gray-400 mt-0.5">{{ followerLabel(p.platform) }}</p>
        </div>

        <!-- Sub-metrics -->
        <div class="flex gap-3 text-xs text-gray-500">
          <span v-if="p.current?.views">
            <span class="font-medium text-gray-700">{{ formatNumber(p.current.views) }}</span> views
          </span>
          <span v-if="p.current?.posts">
            <span class="font-medium text-gray-700">{{ formatNumber(p.current.posts) }}</span> {{ p.platform === 'twitter' ? 'tweets' : 'posts' }}
          </span>
          <span v-if="p.current?.likes && !p.current?.views && !p.current?.posts">
            <span class="font-medium text-gray-700">{{ formatNumber(p.current.likes) }}</span> likes
          </span>
        </div>

        <!-- Sparkline -->
        <div class="h-14">
          <Line :data="sparklineData(p)" :options="sparklineOptions" class="h-14" />
        </div>

        <!-- Delta + details button -->
        <div class="flex items-center justify-between">
          <p class="text-xs" :class="p.delta.followers >= 0 ? 'text-green-600' : 'text-red-500'">
            {{ p.delta.followers >= 0 ? '+' : '' }}{{ formatNumber(p.delta.followers) }} in last 30 days
          </p>
          <Link
            :href="route('admin.social-media.show', p.platform)"
            class="text-xs font-medium text-teal-600 hover:text-teal-800 transition-colors"
          >
            View Details →
          </Link>
        </div>
      </div>
    </div>

    <!-- Combined trend chart -->
    <div class="bg-white rounded-xl border border-gray-200 mb-8">
      <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div>
          <h3 class="font-semibold text-gray-800">Follower Growth — Last 30 Days</h3>
          <p class="text-xs text-gray-400 mt-0.5">All platforms combined view</p>
        </div>
        <div class="flex flex-wrap gap-3">
          <div v-for="p in platforms" :key="p.id" class="flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full inline-block" :style="{ background: platformColor(p.platform) }"></span>
            <span class="text-xs text-gray-500">{{ p.display_name }}</span>
          </div>
        </div>
      </div>
      <div class="px-6 py-4">
        <Line :data="combinedChartData" :options="combinedChartOptions" class="max-h-80" />
      </div>
    </div>

    <!-- Platform details table -->
    <div class="bg-white rounded-xl border border-gray-200">
      <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-800">Platform Overview</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-100">
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Platform</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Followers</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">30d Change</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Views / Likes</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Posts</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Last Synced</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="p in platforms" :key="p.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4">
                <div class="flex items-center gap-2.5">
                  <div class="w-7 h-7 rounded-md flex items-center justify-center flex-shrink-0" :style="{ background: platformColor(p.platform) }">
                    <component :is="platformIcon(p.platform)" class="w-3.5 h-3.5 text-white" />
                  </div>
                  <div>
                    <p class="font-medium text-gray-800">{{ p.display_name }}</p>
                    <p class="text-xs text-gray-400">{{ p.handle }}</p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 text-right font-semibold text-gray-800">{{ formatNumber(p.current?.followers) }}</td>
              <td class="px-6 py-4 text-right">
                <span
                  class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium"
                  :class="p.delta.followers >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-500'"
                >
                  {{ p.delta.followers >= 0 ? '↑' : '↓' }}
                  {{ formatNumber(Math.abs(p.delta.followers)) }}
                  ({{ Math.abs(p.delta.followers_pct) }}%)
                </span>
              </td>
              <td class="px-6 py-4 text-right text-gray-600">
                {{ formatNumber(p.current?.views ?? p.current?.likes) || '—' }}
              </td>
              <td class="px-6 py-4 text-right text-gray-600">{{ formatNumber(p.current?.posts) || '—' }}</td>
              <td class="px-6 py-4 text-right text-gray-400 text-xs">{{ formatDateTime(p.current?.fetched_at) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { computed, ref, h } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Tooltip,
  Filler,
} from 'chart.js'

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Tooltip, Filler)

const props = defineProps({
  platforms:   Array,
  lastFetched: String,
})

const syncing = ref(false)

const syncNow = () => {
  syncing.value = true
  router.post(route('admin.social-media.sync'), {}, {
    onFinish: () => { syncing.value = false },
  })
}

// ── Helpers ──────────────────────────────────────────────────────────────────

const COLORS = {
  youtube:   '#FF0000',
  instagram: '#E1306C',
  tiktok:    '#010101',
  facebook:  '#1877F2',
  twitter:   '#1DA1F2',
}

const platformColor = (platform) => COLORS[platform] ?? '#6b7280'

const followerLabel = (platform) => platform === 'youtube' ? 'Subscribers' : 'Followers'

const formatNumber = (n) => {
  if (n == null) return '—'
  if (n >= 1_000_000) return (n / 1_000_000).toFixed(1).replace(/\.0$/, '') + 'M'
  if (n >= 1_000)     return (n / 1_000).toFixed(1).replace(/\.0$/, '') + 'K'
  return String(n)
}

const formatDateTime = (dt) => {
  if (!dt) return '—'
  return new Date(dt).toLocaleString('en-GB', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' })
}

// ── Sparkline per card ────────────────────────────────────────────────────────

const sparklineData = (p) => ({
  labels: p.chart.map((c) => c.date),
  datasets: [{
    data:            p.chart.map((c) => c.followers),
    borderColor:     platformColor(p.platform),
    backgroundColor: platformColor(p.platform) + '18',
    pointRadius:     0,
    borderWidth:     1.5,
    tension:         0.4,
    fill:            true,
  }],
})

const sparklineOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false }, tooltip: { enabled: false } },
  scales: {
    x: { display: false },
    y: { display: false },
  },
  animation: false,
}

// ── Combined trend chart ──────────────────────────────────────────────────────

// Use labels from the first platform (all share the same date range)
const sharedLabels = computed(() => props.platforms?.[0]?.chart.map((c) => c.date) ?? [])

const combinedChartData = computed(() => ({
  labels: sharedLabels.value,
  datasets: (props.platforms ?? []).map((p) => ({
    label:           p.display_name,
    data:            p.chart.map((c) => c.followers),
    borderColor:     platformColor(p.platform),
    backgroundColor: 'transparent',
    pointRadius:     0,
    pointHoverRadius: 4,
    borderWidth:     2,
    tension:         0.3,
    fill:            false,
  })),
}))

const combinedChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  interaction: { mode: 'index', intersect: false },
  plugins: {
    legend: { display: false },
    tooltip: {
      callbacks: {
        label: (item) => ` ${item.dataset.label}: ${formatNumber(item.parsed.y)}`,
      },
    },
  },
  scales: {
    x: { grid: { display: false }, ticks: { color: '#9ca3af', font: { size: 11 } } },
    y: {
      grid:  { color: '#f3f4f6' },
      ticks: { color: '#9ca3af', font: { size: 11 }, callback: (v) => formatNumber(v) },
    },
  },
}

// ── SVG icon components ───────────────────────────────────────────────────────

const YoutubeIcon = {
  render: () => h('svg', { viewBox: '0 0 24 24', fill: 'currentColor' }, [
    h('path', { d: 'M23.5 6.2a3 3 0 00-2.1-2.1C19.5 3.6 12 3.6 12 3.6s-7.5 0-9.4.5A3 3 0 00.5 6.2C0 8.1 0 12 0 12s0 3.9.5 5.8a3 3 0 002.1 2.1c1.9.5 9.4.5 9.4.5s7.5 0 9.4-.5a3 3 0 002.1-2.1c.5-1.9.5-5.8.5-5.8s0-3.9-.5-5.8zM9.7 15.5V8.5l6.3 3.5-6.3 3.5z' }),
  ]),
}
const InstagramIcon = {
  render: () => h('svg', { viewBox: '0 0 24 24', fill: 'currentColor' }, [
    h('path', { d: 'M12 2.2c3.2 0 3.6 0 4.9.1 3.3.1 4.8 1.7 4.9 4.9.1 1.3.1 1.6.1 4.8 0 3.2 0 3.6-.1 4.8-.1 3.2-1.7 4.8-4.9 4.9-1.3.1-1.6.1-4.9.1-3.2 0-3.6 0-4.8-.1-3.3-.1-4.8-1.7-4.9-4.9C2.2 15.6 2.2 15.2 2.2 12c0-3.2 0-3.6.1-4.8C2.4 3.9 3.9 2.4 7.2 2.3 8.4 2.2 8.8 2.2 12 2.2zm0-2.2C8.7 0 8.3 0 7.1.1 2.7.3.3 2.7.1 7.1 0 8.3 0 8.7 0 12c0 3.3 0 3.7.1 4.9.2 4.4 2.6 6.8 7 7C8.3 24 8.7 24 12 24c3.3 0 3.7 0 4.9-.1 4.4-.2 6.8-2.6 7-7 .1-1.2.1-1.6.1-4.9 0-3.3 0-3.7-.1-4.9-.2-4.4-2.6-6.8-7-7C15.7 0 15.3 0 12 0zm0 5.8a6.2 6.2 0 100 12.4A6.2 6.2 0 0012 5.8zm0 10.2a4 4 0 110-8 4 4 0 010 8zm6.4-11.8a1.44 1.44 0 100 2.88 1.44 1.44 0 000-2.88z' }),
  ]),
}
const TikTokIcon = {
  render: () => h('svg', { viewBox: '0 0 24 24', fill: 'currentColor' }, [
    h('path', { d: 'M19.6 3.3a4.5 4.5 0 01-2.8-2.5A4.6 4.6 0 0116.6 0h-3.3l-.1 16.5a2.7 2.7 0 01-2.7 2.5 2.7 2.7 0 01-2.7-2.7 2.7 2.7 0 012.7-2.7c.3 0 .5 0 .8.1V10.3a6 6 0 00-.8-.1 6 6 0 00-6 6 6 6 0 006 6 6 6 0 006-6V8.2a7.8 7.8 0 004.6 1.5V6.4a4.6 4.6 0 01-2-.9 4.5 4.5 0 01-.5-.2z' }),
  ]),
}
const FacebookIcon = {
  render: () => h('svg', { viewBox: '0 0 24 24', fill: 'currentColor' }, [
    h('path', { d: 'M24 12.1C24 5.4 18.6 0 12 0S0 5.4 0 12.1C0 18.1 4.4 23 10.1 24v-8.4H7.1v-3.5h3V9.4c0-3 1.8-4.7 4.5-4.7 1.3 0 2.7.2 2.7.2v3h-1.5c-1.5 0-2 .9-2 1.9v2.3h3.4l-.5 3.5h-2.8V24C19.6 23 24 18.1 24 12.1z' }),
  ]),
}
const TwitterIcon = {
  render: () => h('svg', { viewBox: '0 0 24 24', fill: 'currentColor' }, [
    h('path', { d: 'M18.9 1h3.7l-8 9.2L24 23h-7.4l-5.8-7.5L4.6 23H.9l8.6-9.8L0 1h7.6l5.2 6.9L18.9 1zm-1.3 19.8h2L6.5 3H4.4l13.2 17.8z' }),
  ]),
}

const ICONS = { youtube: YoutubeIcon, instagram: InstagramIcon, tiktok: TikTokIcon, facebook: FacebookIcon, twitter: TwitterIcon }
const platformIcon = (platform) => ICONS[platform] ?? 'span'
</script>
