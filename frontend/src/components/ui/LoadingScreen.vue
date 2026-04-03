<template>
  <Transition name="loading-exit">
    <div
      v-if="!isDone"
      class="fixed inset-0 z-[200] bg-charcoal-900 flex flex-col items-center justify-center gap-10"
    >
      <!-- Logo with drawn-in reveal -->
      <div class="relative flex items-center justify-center">
        <!-- SVG border that draws around the logo -->
        <svg
          class="absolute"
          :width="logoW + 16"
          :height="logoH + 16"
          :viewBox="`0 0 ${logoW + 16} ${logoH + 16}`"
          fill="none"
        >
          <rect
            :x="2" :y="2"
            :width="logoW + 12" :height="logoH + 12"
            rx="4"
            stroke="#3a8000"
            stroke-width="2"
            :stroke-dasharray="perimeter"
            :stroke-dashoffset="borderOffset"
            style="transition: stroke-dashoffset 0.9s cubic-bezier(0.4, 0, 0.2, 1);"
          />
        </svg>

        <!-- Logo revealed via clip-path, delayed after border draws -->
        <img
          src="/images/klp.png"
          alt="KLP48"
          :style="{ clipPath: logoClip, transition: 'clip-path 0.8s cubic-bezier(0.4, 0, 0.2, 1)' }"
          class="h-44 w-auto relative"
        />
      </div>

      <!-- Progress bar -->
      <div class="flex flex-col items-center gap-3 w-56">
        <div class="w-full h-0.5 bg-white/10 rounded-full overflow-hidden">
          <div
            class="h-full bg-forest-500 rounded-full"
            :style="{ width: `${progress}%` }"
            style="transition: width 0.1s linear;"
          />
        </div>
        <span class="text-xs font-mono text-white/30 tabular-nums">{{ Math.round(progress) }}%</span>
      </div>
    </div>
  </Transition>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

const emit = defineEmits<{ done: [] }>()

// Logo dimensions: h-44 = 176px, logo is roughly portrait 0.73 ratio
const logoH = 176
const logoW = Math.round(logoH * 0.73)
const perimeter = 2 * ((logoW + 12) + (logoH + 12))

const progress = ref(0)
const isDone = ref(false)
const borderOffset = ref(perimeter)
const logoClip = ref('inset(0 0 100% 0)')

const DURATION = 2200 // total loading animation duration in ms

onMounted(() => {
  // Draw border
  setTimeout(() => { borderOffset.value = 0 }, 150)

  // Reveal logo after border finishes
  setTimeout(() => { logoClip.value = 'inset(0 0 0% 0)' }, 950)

  // Animate progress 0 → 100 over DURATION using rAF
  const startTime = performance.now()

  const animate = (now: number) => {
    const elapsed = now - startTime
    const raw = (elapsed / DURATION) * 100
    progress.value = Math.min(100, raw)

    if (progress.value < 100) {
      requestAnimationFrame(animate)
    } else {
      // Hold at 100%, then start fade-out, then destroy after transition completes
      setTimeout(() => {
        isDone.value = true             // triggers <Transition> leave (1400ms)
        setTimeout(() => emit('done'), 1400) // destroy component after fade finishes
      }, 400)
    }
  }

  requestAnimationFrame(animate)
})
</script>

<style scoped>
.loading-exit-leave-active {
  transition: opacity 1.4s ease;
}
.loading-exit-leave-to {
  opacity: 0;
}
</style>