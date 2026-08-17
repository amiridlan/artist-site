<template>
  <AdminLayout title="Reports">
    <!-- Schedule Load per Member -->
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 mb-8">
      <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
        <h3 class="font-semibold text-gray-800 dark:text-gray-100">Schedule Load per Member</h3>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Confirmed events per active member, trailing 90 days</p>
      </div>
      <div class="px-6 py-4">
        <Bar :data="scheduleLoadData" :options="barOptions" class="max-h-72" />
      </div>
    </div>

    <!-- Conflict Frequency -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
      <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
          <h3 class="font-semibold text-gray-800 dark:text-gray-100">Conflict Frequency</h3>
          <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Conflicts logged over the trailing 12 months</p>
        </div>
        <div class="px-6 py-4">
          <Line :data="conflictTrendData" :options="lineOptions" class="max-h-64" />
        </div>
        <p class="px-6 pb-4 text-xs text-gray-400 dark:text-gray-500">
          Reflects conflicts logged at kanban confirm-time only, not every conflict flagged during scheduling.
        </p>
      </div>
      <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
          <h3 class="font-semibold text-gray-800 dark:text-gray-100">By Type</h3>
        </div>
        <div class="px-6 py-4">
          <Doughnut :data="conflictTypeData" :options="doughnutOptions" class="max-h-64" />
        </div>
      </div>
    </div>

    <!-- Fanclub Revenue Trends -->
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
      <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <div>
          <h3 class="font-semibold text-gray-800 dark:text-gray-100">Fanclub Revenue Trends</h3>
          <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Paid subscriptions by tier (RM)</p>
        </div>
        <div class="flex gap-1">
          <button
            v-for="r in ranges"
            :key="r.value"
            @click="activeRange = r.value"
            class="px-2.5 py-1 text-xs rounded-md border transition-colors"
            :class="activeRange === r.value
              ? 'bg-teal-600 dark:bg-teal-500 text-white border-teal-600 dark:border-teal-500'
              : 'bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 border-gray-300 dark:border-gray-700 hover:border-teal-400 dark:hover:border-teal-600'"
          >{{ r.label }}</button>
        </div>
      </div>
      <div class="px-6 py-4">
        <Bar :data="revenueData" :options="barOptions" class="max-h-72" />
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { computed, ref } from 'vue'
import { Bar, Line, Doughnut } from 'vue-chartjs'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  PointElement,
  LineElement,
  ArcElement,
  Tooltip,
  Legend,
  Filler,
} from 'chart.js'

ChartJS.register(CategoryScale, LinearScale, BarElement, PointElement, LineElement, ArcElement, Tooltip, Legend, Filler)

const isDarkMode = ref(document.documentElement.classList.contains('dark'))
const observer = new MutationObserver((mutations) => {
  mutations.forEach((mutation) => {
    if (mutation.attributeName === 'class') {
      isDarkMode.value = document.documentElement.classList.contains('dark')
    }
  })
})
observer.observe(document.documentElement, { attributes: true })

const props = defineProps({
  scheduleLoad: Array,
  conflictTrend: Array,
  conflictsByType: Object,
  revenueTrend: Array,
})

const tickColor = computed(() => (isDarkMode.value ? '#9ca3af' : '#6b7280'))
const gridColor = computed(() => (isDarkMode.value ? '#374151' : '#f3f4f6'))
const tooltipStyle = computed(() => ({
  backgroundColor: isDarkMode.value ? '#1f2937' : '#ffffff',
  titleColor: isDarkMode.value ? '#f3f4f6' : '#111827',
  bodyColor: isDarkMode.value ? '#d1d5db' : '#374151',
  borderColor: isDarkMode.value ? '#374151' : '#e5e7eb',
  borderWidth: 1,
}))

const formatMonthLabel = (ym) => {
  const [y, m] = ym.split('-')
  return new Date(Number(y), Number(m) - 1).toLocaleDateString('en-GB', { month: 'short', year: '2-digit' })
}

// Schedule load per member
const scheduleLoadData = computed(() => ({
  labels: props.scheduleLoad.map(d => d.member),
  datasets: [{
    label: 'Confirmed Events',
    data: props.scheduleLoad.map(d => d.count),
    backgroundColor: '#0d9488',
    borderRadius: 4,
  }],
}))

// Conflict trend
const conflictTrendData = computed(() => ({
  labels: props.conflictTrend.map(d => formatMonthLabel(d.month)),
  datasets: [{
    label: 'Conflicts Logged',
    data: props.conflictTrend.map(d => d.count),
    borderColor: '#f59e0b',
    backgroundColor: 'rgba(245,158,11,0.1)',
    pointBackgroundColor: '#f59e0b',
    pointRadius: 3,
    tension: 0.3,
    fill: true,
  }],
}))

const CONFLICT_TYPE_LABELS = {
  artist_double_booking: 'Double Booking',
  artist_day_off_conflict: 'Day-Off Conflict',
  staff_availability: 'Staff Availability',
  resource_conflict: 'Resource Conflict',
}
const conflictTypeData = computed(() => {
  const entries = Object.entries(props.conflictsByType || {})
  return {
    labels: entries.map(([type]) => CONFLICT_TYPE_LABELS[type] || type),
    datasets: [{
      data: entries.map(([, count]) => count),
      backgroundColor: ['#ef4444', '#10b981', '#6366f1', '#f97316'],
      borderWidth: 0,
    }],
  }
})

// Revenue trend
const ranges = [
  { value: 3, label: '3M' },
  { value: 6, label: '6M' },
  { value: 12, label: '1Y' },
  { value: 24, label: '2Y' },
  { value: 36, label: '3Y' },
]
const activeRange = ref(12)
const visibleRevenue = computed(() => (props.revenueTrend ?? []).slice(-activeRange.value))

const revenueData = computed(() => ({
  labels: visibleRevenue.value.map(d => formatMonthLabel(d.month)),
  datasets: [
    { label: 'Basic', data: visibleRevenue.value.map(d => d.basic), backgroundColor: '#5eead4', borderRadius: 4 },
    { label: 'Gold', data: visibleRevenue.value.map(d => d.gold), backgroundColor: '#d97706', borderRadius: 4 },
  ],
}))

const barOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    tooltip: tooltipStyle.value,
    legend: { labels: { color: tickColor.value, boxWidth: 10, font: { size: 11 } } },
  },
  scales: {
    x: { grid: { display: false }, ticks: { color: tickColor.value, font: { size: 11 } } },
    y: { grid: { color: gridColor.value }, ticks: { color: tickColor.value, font: { size: 11 }, precision: 0 } },
  },
}))

const lineOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: { tooltip: tooltipStyle.value, legend: { display: false } },
  scales: {
    x: { grid: { display: false }, ticks: { color: tickColor.value, font: { size: 11 } } },
    y: { grid: { color: gridColor.value }, ticks: { color: tickColor.value, font: { size: 11 }, precision: 0 } },
  },
}))

const doughnutOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    tooltip: tooltipStyle.value,
    legend: { position: 'bottom', labels: { color: tickColor.value, boxWidth: 10, font: { size: 11 } } },
  },
}))
</script>
