<template>
  <AdminLayout :title="platform.display_name + ' Analytics'">
    <template #actions>
      <Link :href="route('admin.social-media.index')" class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-800 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"/>
        </svg>
        All Platforms
      </Link>
    </template>

    <!-- Platform header -->
    <div class="flex items-center gap-4 mb-6">
      <div class="w-12 h-12 rounded-xl flex items-center justify-center" :style="{ background: color }">
        <component :is="icon" class="w-6 h-6 text-white" />
      </div>
      <div>
        <h2 class="text-xl font-bold text-gray-900">{{ platform.display_name }}</h2>
        <p class="text-sm text-gray-400">{{ platform.handle }}</p>
      </div>
      <div class="ml-auto text-right">
        <p class="text-xs text-gray-400">Last synced</p>
        <p class="text-xs font-medium text-gray-600">{{ formatDateTime(platform.current?.fetched_at) }}</p>
      </div>
    </div>

    <!-- Top stats cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
      <div v-for="card in statCards" :key="card.label" class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-xs text-gray-400 mb-1">{{ card.label }}</p>
        <p class="text-2xl font-bold text-gray-900">{{ card.value }}</p>
        <p v-if="card.sub" class="text-xs text-gray-400 mt-0.5">{{ card.sub }}</p>
      </div>
    </div>

    <!-- Trend + Geo row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
      <!-- Follower / subscriber trend (2/3 width) -->
      <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100">
          <h3 class="font-semibold text-gray-800">{{ followerLabel }} Growth — Last 90 Days</h3>
        </div>
        <div class="px-6 py-4">
          <Line :data="trendChartData" :options="lineOptions('', color)" class="max-h-56" />
        </div>
      </div>

      <!-- Top countries (1/3 width) -->
      <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100">
          <h3 class="font-semibold text-gray-800">Top Countries</h3>
        </div>
        <div class="px-6 py-4 space-y-2.5">
          <div v-for="geo in geoStats.slice(0, 8)" :key="geo.country_code" class="flex items-center gap-2">
            <span class="text-xs text-gray-500 w-24 truncate">{{ geo.country_name }}</span>
            <div class="flex-1 bg-gray-100 rounded-full h-1.5">
              <div class="h-1.5 rounded-full transition-all" :style="{ width: geo.percentage + '%', background: color }"></div>
            </div>
            <span class="text-xs font-medium text-gray-700 w-10 text-right">{{ geo.percentage }}%</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Platform-specific extras row -->

    <!-- YouTube: traffic sources + subscriber daily gain -->
    <template v-if="platform.platform === 'youtube'">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl border border-gray-200">
          <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Traffic Sources</h3>
            <p class="text-xs text-gray-400 mt-0.5">How viewers find KLP48 on YouTube</p>
          </div>
          <div class="px-6 py-4 flex items-center gap-6">
            <div class="w-40 h-40 flex-shrink-0">
              <Doughnut :data="trafficSourceChart" :options="doughnutOptions" />
            </div>
            <div class="space-y-2 flex-1">
              <div v-for="(src, i) in extras.traffic_sources" :key="src.label" class="flex items-center gap-2 text-xs">
                <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" :style="{ background: doughnutColors[i] }"></span>
                <span class="flex-1 text-gray-600">{{ src.label }}</span>
                <span class="font-semibold text-gray-800">{{ src.value }}%</span>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200">
          <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Daily Subscriber Gain — Last 30 Days</h3>
          </div>
          <div class="px-6 py-4">
            <Bar :data="subscriberGainChart" :options="barOptions(color)" class="max-h-48" />
          </div>
        </div>
      </div>
    </template>

    <!-- Instagram: reach trend + age/gender demographics -->
    <template v-if="platform.platform === 'instagram'">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl border border-gray-200">
          <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Reach — Last 30 Days</h3>
            <p class="text-xs text-gray-400 mt-0.5">Unique accounts reached per day</p>
          </div>
          <div class="px-6 py-4">
            <Line :data="reachTrendChart" :options="lineOptions('Reach', color)" class="max-h-48" />
          </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200">
          <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Audience Age & Gender</h3>
          </div>
          <div class="px-6 py-4">
            <Bar :data="demographicsChart" :options="demographicsOptions" class="max-h-48" />
          </div>
        </div>
      </div>
    </template>

    <!-- Facebook: reactions donut + demographics -->
    <template v-if="platform.platform === 'facebook'">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl border border-gray-200">
          <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Reactions Breakdown</h3>
            <p class="text-xs text-gray-400 mt-0.5">How people react to posts</p>
          </div>
          <div class="px-6 py-4 flex items-center gap-6">
            <div class="w-40 h-40 flex-shrink-0">
              <Doughnut :data="reactionsChart" :options="doughnutOptions" />
            </div>
            <div class="space-y-2 flex-1">
              <div v-for="(r, i) in extras.reactions" :key="r.label" class="flex items-center gap-2 text-xs">
                <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" :style="{ background: doughnutColors[i] }"></span>
                <span class="flex-1 text-gray-600">{{ r.label }}</span>
                <span class="font-semibold text-gray-800">{{ r.value }}%</span>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200">
          <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Audience Age & Gender</h3>
          </div>
          <div class="px-6 py-4">
            <Bar :data="demographicsChart" :options="demographicsOptions" class="max-h-48" />
          </div>
        </div>
      </div>
    </template>

    <!-- TikTok: video views trend + peak activity hours -->
    <template v-if="platform.platform === 'tiktok'">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl border border-gray-200">
          <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Video Views — Last 30 Days</h3>
          </div>
          <div class="px-6 py-4">
            <Line :data="viewsTrendChart" :options="lineOptions('Views', color)" class="max-h-48" />
          </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200">
          <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Peak Follower Activity (by Hour)</h3>
            <p class="text-xs text-gray-400 mt-0.5">Best time to post</p>
          </div>
          <div class="px-6 py-4">
            <Bar :data="activityHoursChart" :options="barOptions(color)" class="max-h-48" />
          </div>
        </div>
      </div>
    </template>

    <!-- Twitter: impressions trend -->
    <template v-if="platform.platform === 'twitter'">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl border border-gray-200">
          <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Daily Impressions — Last 30 Days</h3>
          </div>
          <div class="px-6 py-4">
            <Line :data="impressionsTrendChart" :options="lineOptions('Impressions', color)" class="max-h-48" />
          </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 flex flex-col justify-center px-6 py-4">
          <div class="grid grid-cols-2 gap-4">
            <div class="bg-gray-50 rounded-lg p-4 text-center">
              <p class="text-2xl font-bold text-gray-900">{{ formatNumber(extras.impressions_30d) }}</p>
              <p class="text-xs text-gray-400 mt-1">Total Impressions (30d)</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
              <p class="text-2xl font-bold text-gray-900">{{ formatNumber(extras.avg_impressions) }}</p>
              <p class="text-xs text-gray-400 mt-1">Avg. per Tweet</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
              <p class="text-2xl font-bold text-gray-900">{{ formatNumber(extras.profile_visits_30d) }}</p>
              <p class="text-xs text-gray-400 mt-1">Profile Visits (30d)</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
              <p class="text-2xl font-bold text-gray-900">{{ platform.current?.posts }}</p>
              <p class="text-xs text-gray-400 mt-1">Total Tweets</p>
            </div>
          </div>
        </div>
      </div>
    </template>

    <!-- Content table -->
    <div class="bg-white rounded-xl border border-gray-200">
      <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div>
          <h3 class="font-semibold text-gray-800">{{ contentLabel }} Performance</h3>
          <p class="text-xs text-gray-400 mt-0.5">Sorted by most recent</p>
        </div>
        <span class="text-xs text-gray-400">{{ contentItems.length }} {{ contentLabel.toLowerCase() }}</span>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ contentLabel }}</th>
              <th v-for="col in contentColumns" :key="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ col }}</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Published</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="item in contentItems" :key="item.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-3">
                <div class="flex items-center gap-3">
                  <!-- Thumbnail placeholder -->
                  <div class="w-10 h-7 rounded flex-shrink-0 flex items-center justify-center text-white text-xs font-bold" :style="{ background: color + 'cc' }">
                    <svg v-if="platform.platform === 'youtube'" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    <svg v-else-if="platform.platform === 'twitter'" class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M18.9 1h3.7l-8 9.2L24 23h-7.4l-5.8-7.5L4.6 23H.9l8.6-9.8L0 1h7.6l5.2 6.9L18.9 1z"/></svg>
                    <svg v-else class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M4 16l4-4-4-4M12 20h8"/></svg>
                  </div>
                  <p class="text-sm text-gray-700 line-clamp-1 max-w-xs">{{ item.title }}</p>
                </div>
              </td>
              <td v-for="col in contentColumns" :key="col" class="px-4 py-3 text-right text-gray-700 font-medium">
                {{ formatNumber(item.metrics[col.toLowerCase().replace(/ /g, '_')]) }}
              </td>
              <td class="px-6 py-3 text-right text-xs text-gray-400">{{ item.published_at }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { computed, h } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Line, Bar, Doughnut } from 'vue-chartjs'
import {
  Chart as ChartJS,
  CategoryScale, LinearScale,
  PointElement, LineElement,
  BarController, BarElement,
  DoughnutController, ArcElement,
  Tooltip, Filler, Legend,
} from 'chart.js'

ChartJS.register(
  CategoryScale, LinearScale,
  PointElement, LineElement,
  BarController, BarElement,
  DoughnutController, ArcElement,
  Tooltip, Filler, Legend,
)

const props = defineProps({
  platform:     Object,
  contentItems: Array,
  geoStats:     Array,
  extras:       Object,
})

// ── Platform colour & icon ─────────────────────────────────────────────────

const COLORS = { youtube: '#FF0000', instagram: '#E1306C', tiktok: '#010101', facebook: '#1877F2', twitter: '#1DA1F2' }
const color  = computed(() => COLORS[props.platform.platform] ?? '#6b7280')

const YoutubeIcon   = { render: () => h('svg', { viewBox: '0 0 24 24', fill: 'currentColor' }, [h('path', { d: 'M23.5 6.2a3 3 0 00-2.1-2.1C19.5 3.6 12 3.6 12 3.6s-7.5 0-9.4.5A3 3 0 00.5 6.2C0 8.1 0 12 0 12s0 3.9.5 5.8a3 3 0 002.1 2.1c1.9.5 9.4.5 9.4.5s7.5 0 9.4-.5a3 3 0 002.1-2.1c.5-1.9.5-5.8.5-5.8s0-3.9-.5-5.8zM9.7 15.5V8.5l6.3 3.5-6.3 3.5z' })]) }
const InstagramIcon = { render: () => h('svg', { viewBox: '0 0 24 24', fill: 'currentColor' }, [h('path', { d: 'M12 2.2c3.2 0 3.6 0 4.9.1 3.3.1 4.8 1.7 4.9 4.9.1 1.3.1 1.6.1 4.8 0 3.2 0 3.6-.1 4.8-.1 3.2-1.7 4.8-4.9 4.9-1.3.1-1.6.1-4.9.1-3.2 0-3.6 0-4.8-.1-3.3-.1-4.8-1.7-4.9-4.9C2.2 15.6 2.2 15.2 2.2 12c0-3.2 0-3.6.1-4.8C2.4 3.9 3.9 2.4 7.2 2.3 8.4 2.2 8.8 2.2 12 2.2zm0-2.2C8.7 0 8.3 0 7.1.1 2.7.3.3 2.7.1 7.1 0 8.3 0 8.7 0 12c0 3.3 0 3.7.1 4.9.2 4.4 2.6 6.8 7 7C8.3 24 8.7 24 12 24c3.3 0 3.7 0 4.9-.1 4.4-.2 6.8-2.6 7-7 .1-1.2.1-1.6.1-4.9 0-3.3 0-3.7-.1-4.9-.2-4.4-2.6-6.8-7-7C15.7 0 15.3 0 12 0zm0 5.8a6.2 6.2 0 100 12.4A6.2 6.2 0 0012 5.8zm0 10.2a4 4 0 110-8 4 4 0 010 8zm6.4-11.8a1.44 1.44 0 100 2.88 1.44 1.44 0 000-2.88z' })]) }
const TikTokIcon    = { render: () => h('svg', { viewBox: '0 0 24 24', fill: 'currentColor' }, [h('path', { d: 'M19.6 3.3a4.5 4.5 0 01-2.8-2.5A4.6 4.6 0 0116.6 0h-3.3l-.1 16.5a2.7 2.7 0 01-2.7 2.5 2.7 2.7 0 01-2.7-2.7 2.7 2.7 0 012.7-2.7c.3 0 .5 0 .8.1V10.3a6 6 0 00-.8-.1 6 6 0 00-6 6 6 6 0 006 6 6 6 0 006-6V8.2a7.8 7.8 0 004.6 1.5V6.4a4.6 4.6 0 01-2-.9 4.5 4.5 0 01-.5-.2z' })]) }
const FacebookIcon  = { render: () => h('svg', { viewBox: '0 0 24 24', fill: 'currentColor' }, [h('path', { d: 'M24 12.1C24 5.4 18.6 0 12 0S0 5.4 0 12.1C0 18.1 4.4 23 10.1 24v-8.4H7.1v-3.5h3V9.4c0-3 1.8-4.7 4.5-4.7 1.3 0 2.7.2 2.7.2v3h-1.5c-1.5 0-2 .9-2 1.9v2.3h3.4l-.5 3.5h-2.8V24C19.6 23 24 18.1 24 12.1z' })]) }
const TwitterIcon   = { render: () => h('svg', { viewBox: '0 0 24 24', fill: 'currentColor' }, [h('path', { d: 'M18.9 1h3.7l-8 9.2L24 23h-7.4l-5.8-7.5L4.6 23H.9l8.6-9.8L0 1h7.6l5.2 6.9L18.9 1z' })]) }
const ICONS = { youtube: YoutubeIcon, instagram: InstagramIcon, tiktok: TikTokIcon, facebook: FacebookIcon, twitter: TwitterIcon }
const icon  = computed(() => ICONS[props.platform.platform] ?? 'span')

// ── Labels ─────────────────────────────────────────────────────────────────

const followerLabel  = computed(() => props.platform.platform === 'youtube' ? 'Subscriber' : 'Follower')
const contentLabel   = computed(() => ({ youtube: 'Videos', instagram: 'Posts', tiktok: 'Videos', facebook: 'Posts', twitter: 'Tweets' }[props.platform.platform] ?? 'Content'))
const contentColumns = computed(() => ({
  youtube:   ['Views', 'Likes', 'Comments'],
  instagram: ['Likes', 'Comments', 'Reach'],
  tiktok:    ['Views', 'Likes', 'Shares'],
  facebook:  ['Reach', 'Likes', 'Shares'],
  twitter:   ['Impressions', 'Likes', 'Retweets'],
}[props.platform.platform] ?? []))

// ── Stat cards ──────────────────────────────────────────────────────────────

const statCards = computed(() => {
  const c = props.platform.current ?? {}
  const e = props.extras ?? {}
  const p = props.platform.platform
  const d = props.platform.delta

  const followerCard = {
    label: p === 'youtube' ? 'Subscribers' : 'Followers',
    value: formatNumber(c.followers),
    sub: `${d.followers >= 0 ? '+' : ''}${formatNumber(d.followers)} this month`,
  }

  const maps = {
    youtube: [
      followerCard,
      { label: 'Total Views',   value: formatNumber(c.views) },
      { label: 'Videos',        value: c.posts },
      { label: 'Watch Time',    value: formatNumber(e.watch_time_hours) + ' hrs', sub: 'all time' },
    ],
    instagram: [
      followerCard,
      { label: 'Total Posts',   value: c.posts },
      { label: 'Reach (30d)',   value: formatNumber(e.reach_30d) },
      { label: 'Impressions (30d)', value: formatNumber(e.impressions_30d) },
    ],
    tiktok: [
      followerCard,
      { label: 'Total Likes',   value: formatNumber(c.likes) },
      { label: 'Videos',        value: c.posts },
      { label: 'Avg Views',     value: formatNumber(e.avg_views), sub: 'per video' },
    ],
    facebook: [
      followerCard,
      { label: 'Total Posts',   value: c.posts },
      { label: 'Page Views (30d)', value: formatNumber(e.page_views_30d) },
      { label: 'Avg Reach',     value: formatNumber(Math.round((e.page_views_30d ?? 0) / 30)), sub: 'per day' },
    ],
    twitter: [
      followerCard,
      { label: 'Total Tweets',  value: c.posts },
      { label: 'Impressions (30d)', value: formatNumber(e.impressions_30d) },
      { label: 'Profile Visits (30d)', value: formatNumber(e.profile_visits_30d) },
    ],
  }
  return maps[p] ?? [followerCard]
})

// ── Chart helpers ──────────────────────────────────────────────────────────

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

const lineOptions = (label, borderColor) => ({
  responsive: true,
  maintainAspectRatio: false,
  interaction: { mode: 'index', intersect: false },
  plugins: {
    legend: { display: false },
    tooltip: { callbacks: { label: (i) => ` ${label || i.dataset.label}: ${formatNumber(i.parsed.y)}` } },
  },
  scales: {
    x: { grid: { display: false }, ticks: { color: '#9ca3af', font: { size: 10 }, maxTicksLimit: 10 } },
    y: { grid: { color: '#f3f4f6' }, ticks: { color: '#9ca3af', font: { size: 10 }, callback: (v) => formatNumber(v) } },
  },
})

const barOptions = (barColor) => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false }, tooltip: { callbacks: { label: (i) => ` ${formatNumber(i.parsed.y)}` } } },
  scales: {
    x: { grid: { display: false }, ticks: { color: '#9ca3af', font: { size: 10 }, maxTicksLimit: 10 } },
    y: { grid: { color: '#f3f4f6' }, ticks: { color: '#9ca3af', font: { size: 10 }, callback: (v) => formatNumber(v) } },
  },
})

const doughnutOptions = {
  responsive: true,
  maintainAspectRatio: true,
  cutout: '65%',
  plugins: { legend: { display: false }, tooltip: { callbacks: { label: (i) => ` ${i.label}: ${i.parsed}%` } } },
}

const demographicsOptions = {
  responsive: true,
  maintainAspectRatio: false,
  indexAxis: 'y',
  plugins: {
    legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 12 } },
    tooltip: { callbacks: { label: (i) => ` ${i.dataset.label}: ${i.parsed.x}%` } },
  },
  scales: {
    x: { stacked: false, grid: { display: false }, ticks: { color: '#9ca3af', font: { size: 10 }, callback: (v) => v + '%' } },
    y: { grid: { display: false }, ticks: { color: '#6b7280', font: { size: 10 } } },
  },
}

const doughnutColors = ['#0d9488', '#3b82f6', '#8b5cf6', '#f59e0b', '#ef4444', '#ec4899', '#10b981']

// ── Chart data ─────────────────────────────────────────────────────────────

const trendChartData = computed(() => ({
  labels: props.platform.trend.map(d => d.date),
  datasets: [{
    label: followerLabel.value,
    data: props.platform.trend.map(d => d.followers),
    borderColor: color.value,
    backgroundColor: color.value + '14',
    pointRadius: 0,
    borderWidth: 2,
    tension: 0.3,
    fill: true,
  }],
}))

const trafficSourceChart = computed(() => ({
  labels: (props.extras.traffic_sources ?? []).map(s => s.label),
  datasets: [{ data: (props.extras.traffic_sources ?? []).map(s => s.value), backgroundColor: doughnutColors, borderWidth: 0 }],
}))

const subscriberGainChart = computed(() => ({
  labels: (props.extras.subscriber_gain_30d ?? []).map(d => d.date),
  datasets: [{
    label: 'New Subscribers',
    data: (props.extras.subscriber_gain_30d ?? []).map(d => d.value),
    backgroundColor: color.value + 'cc',
    borderRadius: 3,
  }],
}))

const reachTrendChart = computed(() => ({
  labels: (props.extras.reach_trend ?? []).map(d => d.date),
  datasets: [{
    label: 'Reach',
    data: (props.extras.reach_trend ?? []).map(d => d.value),
    borderColor: color.value,
    backgroundColor: color.value + '14',
    pointRadius: 0,
    borderWidth: 2,
    tension: 0.3,
    fill: true,
  }],
}))

const demographicsChart = computed(() => {
  const demo = props.extras.demographics ?? []
  return {
    labels: demo.map(d => d.label),
    datasets: [
      { label: 'Female', data: demo.map(d => d.female), backgroundColor: '#ec4899cc', borderRadius: 3 },
      { label: 'Male',   data: demo.map(d => d.male),   backgroundColor: '#3b82f6cc', borderRadius: 3 },
    ],
  }
})

const reactionsChart = computed(() => ({
  labels: (props.extras.reactions ?? []).map(r => r.label),
  datasets: [{ data: (props.extras.reactions ?? []).map(r => r.value), backgroundColor: doughnutColors, borderWidth: 0 }],
}))

const viewsTrendChart = computed(() => ({
  labels: (props.extras.views_trend ?? []).map(d => d.date),
  datasets: [{
    label: 'Views',
    data: (props.extras.views_trend ?? []).map(d => d.value),
    borderColor: color.value,
    backgroundColor: color.value + '14',
    pointRadius: 0,
    borderWidth: 2,
    tension: 0.3,
    fill: true,
  }],
}))

const activityHoursChart = computed(() => ({
  labels: (props.extras.activity_hours ?? []).map(h => h.hour),
  datasets: [{
    label: 'Activity Index',
    data: (props.extras.activity_hours ?? []).map(h => h.value),
    backgroundColor: (props.extras.activity_hours ?? []).map(h => h.value >= 1.5 ? color.value : color.value + '55'),
    borderRadius: 3,
  }],
}))

const impressionsTrendChart = computed(() => ({
  labels: (props.extras.impressions_trend ?? []).map(d => d.date),
  datasets: [{
    label: 'Impressions',
    data: (props.extras.impressions_trend ?? []).map(d => d.value),
    borderColor: color.value,
    backgroundColor: color.value + '14',
    pointRadius: 0,
    borderWidth: 2,
    tension: 0.3,
    fill: true,
  }],
}))
</script>
