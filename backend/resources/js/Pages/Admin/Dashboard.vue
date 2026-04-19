<template>
  <AdminLayout title="Dashboard">
    <!-- Stats grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
      <StatCard v-for="s in statCards" :key="s.label" :label="s.label" :value="s.value" :href="s.href" :color="s.color" />
    </div>

    <!-- Social media summary -->
    <div class="bg-white rounded-xl border border-gray-200 mb-8">
      <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div>
          <h3 class="font-semibold text-gray-800">Social Media</h3>
          <p class="text-xs text-gray-400 mt-0.5">Follower counts across all platforms</p>
        </div>
        <Link :href="route('admin.social-media.index')" class="text-sm text-teal-600 hover:text-teal-800 font-medium">
          View analytics →
        </Link>
      </div>
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 divide-x divide-y lg:divide-y-0 divide-gray-100">
        <div v-for="p in socialSummary" :key="p.platform" class="px-5 py-4">
          <div class="flex items-center gap-1.5 mb-1">
            <span class="w-2 h-2 rounded-full inline-block" :style="{ background: socialColor(p.platform) }"></span>
            <p class="text-xs text-gray-500">{{ p.display_name }}</p>
          </div>
          <p class="text-xl font-bold text-gray-800">{{ formatSocial(p.followers) }}</p>
          <p class="text-xs mt-0.5" :class="p.delta >= 0 ? 'text-green-600' : 'text-red-500'">
            {{ p.delta >= 0 ? '↑' : '↓' }} {{ formatSocial(Math.abs(p.delta)) }} / 30d
          </p>
        </div>
      </div>
    </div>

    <!-- Fanclub growth chart -->
    <div class="bg-white rounded-xl border border-gray-200 mb-8">
      <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div>
          <h3 class="font-semibold text-gray-800">Fanclub Growth</h3>
          <p class="text-xs text-gray-400 mt-0.5">Cumulative active signups over time</p>
        </div>
        <div class="flex items-center gap-3">
          <!-- Time range toggles -->
          <div class="flex gap-1">
            <button
              v-for="r in ranges"
              :key="r.value"
              @click="activeRange = r.value"
              class="px-2.5 py-1 text-xs rounded-md border transition-colors"
              :class="activeRange === r.value
                ? 'bg-teal-600 text-white border-teal-600'
                : 'bg-white text-gray-500 border-gray-300 hover:border-teal-400'"
            >{{ r.label }}</button>
          </div>
          <!-- Quick nav to Fanclub page -->
          <Link :href="route('admin.fanclub.index')" class="text-sm text-teal-600 hover:text-teal-800 font-medium">
            Manage →
          </Link>
        </div>
      </div>
      <div class="px-6 py-4">
        <Line :data="chartData" :options="chartOptions" class="max-h-72" />
      </div>
    </div>

    <!-- Upcoming events -->
    <div class="bg-white rounded-xl border border-gray-200">
      <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800">Upcoming Events</h3>
        <Link :href="route('admin.events.index')" class="text-sm text-teal-600 hover:text-teal-800">View all →</Link>
      </div>
      <div v-if="upcomingEvents.length" class="divide-y divide-gray-100">
        <div v-for="event in upcomingEvents" :key="event.id" class="px-6 py-3 flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-800">{{ event.title }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ event.venue }} · {{ formatDate(event.date) }}</p>
          </div>
          <span class="px-2 py-0.5 text-xs rounded-full bg-teal-100 text-teal-700 font-medium capitalize">{{ event.status }}</span>
        </div>
      </div>
      <p v-else class="px-6 py-8 text-center text-sm text-gray-400">No upcoming events.</p>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
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
  stats:         Object,
  upcomingEvents: Array,
  fanclubChart:  Array,
  socialSummary: Array,
})

const SOCIAL_COLORS = { youtube: '#FF0000', instagram: '#E1306C', tiktok: '#010101', facebook: '#1877F2', twitter: '#1DA1F2' }
const socialColor = (p) => SOCIAL_COLORS[p] ?? '#6b7280'
const formatSocial = (n) => {
  if (!n) return '0'
  if (n >= 1_000_000) return (n / 1_000_000).toFixed(1).replace(/\.0$/, '') + 'M'
  if (n >= 1_000)     return (n / 1_000).toFixed(1).replace(/\.0$/, '') + 'K'
  return String(n)
}

const statCards = computed(() => [
  { label: 'Members',  value: props.stats.members,  href: route('admin.members.index'),  color: 'teal'   },
  { label: 'News',     value: props.stats.news,      href: route('admin.news.index'),     color: 'blue'   },
  { label: 'Releases', value: props.stats.releases,  href: route('admin.releases.index'), color: 'purple' },
  { label: 'Videos',   value: props.stats.videos,    href: route('admin.videos.index'),   color: 'pink'   },
  { label: 'Events',   value: props.stats.events,    href: route('admin.events.index'),   color: 'orange' },
  { label: 'Fanclub',  value: props.stats.fanclub,   href: route('admin.fanclub.index'),  color: 'teal'   },
])

const formatDate = (d) => new Date(d).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })

// Chart range toggle
const ranges = [
  { value: 1,  label: '1M'  },
  { value: 3,  label: '3M'  },
  { value: 6,  label: '6M'  },
  { value: 12, label: '1Y'  },
  { value: 24, label: '2Y'  },
  { value: 36, label: '3Y'  },
]
const activeRange = ref(12)

const visibleData = computed(() => {
  const all = props.fanclubChart ?? []
  return all.slice(-activeRange.value)
})

const formatMonthLabel = (ym) => {
  const [y, m] = ym.split('-')
  return new Date(Number(y), Number(m) - 1).toLocaleDateString('en-GB', { month: 'short', year: '2-digit' })
}

const chartData = computed(() => ({
  labels: visibleData.value.map(d => formatMonthLabel(d.month)),
  datasets: [{
    label: 'Total Members',
    data: visibleData.value.map(d => d.total),
    borderColor: '#0d9488',
    backgroundColor: 'rgba(13,148,136,0.08)',
    pointBackgroundColor: '#0d9488',
    pointRadius: 4,
    pointHoverRadius: 6,
    tension: 0.3,
    fill: true,
  }],
}))

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  interaction: { mode: 'index', intersect: false },
  plugins: {
    tooltip: {
      callbacks: {
        title: (items) => items[0].label,
        label:  (item)  => ` ${item.parsed.y} members`,
      },
    },
    legend: { display: false },
  },
  scales: {
    x: { grid: { display: false }, ticks: { color: '#9ca3af', font: { size: 11 } } },
    y: { grid: { color: '#f3f4f6' }, ticks: { color: '#9ca3af', font: { size: 11 }, precision: 0 } },
  },
}
</script>

<script>
const StatCard = {
  props: { label: String, value: Number, href: String, color: String },
  template: `
    <a :href="href" class="bg-white rounded-xl border border-gray-200 p-5 hover:border-teal-300 transition-colors block">
      <p class="text-3xl font-bold text-gray-800">{{ value }}</p>
      <p class="text-sm text-gray-500 mt-1">{{ label }}</p>
    </a>
  `,
}
export default { components: { StatCard } }
</script>
