<template>
  <!-- Flying logo: sits above everything during the transition -->
  <Teleport to="body">
    <div
      v-if="isFlying"
      class="pointer-events-none overflow-hidden"
      :style="flyingStyle"
    >
      <img src="/images/klp.png" alt="" class="w-full h-full object-fill" />
    </div>
  </Teleport>

  <!-- Loading overlay -->
  <Transition name="loading-exit">
    <div
      v-if="!isDone"
      class="fixed inset-0 z-[200] bg-charcoal-900 flex flex-col items-center justify-center gap-10"
    >
      <!-- Logo with drawn-in reveal -->
      <div class="relative flex items-center justify-center">
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

        <img
          ref="logoRef"
          src="/images/klp.png"
          alt="KLP48"
          class="h-44 w-auto relative"
          :style="{
            clipPath: logoClip,
            transition: 'clip-path 0.8s cubic-bezier(0.4, 0, 0.2, 1)',
            opacity: isFlying ? 0 : 1,
          }"
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

const logoH = 176
const logoW = Math.round(logoH * 0.73)
const perimeter = 2 * ((logoW + 12) + (logoH + 12))

const progress = ref(0)
const isDone = ref(false)
const isFlying = ref(false)
const borderOffset = ref(perimeter)
const logoClip = ref('inset(0 0 100% 0)')
const logoRef = ref<HTMLImageElement | null>(null)
const flyingStyle = ref<Record<string, string>>({})

const DURATION = 2200

function flyLogoToHeader() {
  const fromEl = logoRef.value
  const toEl = document.querySelector('header img[alt="KLP48"]') as HTMLImageElement | null

  if (!fromEl || !toEl) {
    // Fallback: just fade out
    isDone.value = true
    setTimeout(() => emit('done'), 1400)
    return
  }

  const from = fromEl.getBoundingClientRect()
  const to = toEl.getBoundingClientRect()

  // Place flying clone exactly over the loading screen logo (no transition yet)
  flyingStyle.value = {
    position: 'fixed',
    top: `${from.top}px`,
    left: `${from.left}px`,
    width: `${from.width}px`,
    height: `${from.height}px`,
    zIndex: '300',
    transition: 'none',
  }
  isFlying.value = true

  // Hide real header logo while the clone is flying
  toEl.style.opacity = '0'

  // Two rAFs: ensure first paint of flying clone at start position before animating
  requestAnimationFrame(() => {
    requestAnimationFrame(() => {
      // Animate clone to header position
      flyingStyle.value = {
        position: 'fixed',
        top: `${to.top}px`,
        left: `${to.left}px`,
        width: `${to.width}px`,
        height: `${to.height}px`,
        zIndex: '300',
        transition: 'top 0.75s cubic-bezier(0.4, 0, 0.2, 1), left 0.75s cubic-bezier(0.4, 0, 0.2, 1), width 0.75s cubic-bezier(0.4, 0, 0.2, 1), height 0.75s cubic-bezier(0.4, 0, 0.2, 1)',
      }

      // Fade out the overlay at the same time
      isDone.value = true

      // Once clone arrives, swap in the real header logo
      setTimeout(() => {
        toEl.style.transition = 'opacity 0.2s ease'
        toEl.style.opacity = '1'
        isFlying.value = false
      }, 750)

      // Unmount after overlay fade finishes
      setTimeout(() => emit('done'), 1400)
    })
  })
}

onMounted(() => {
  setTimeout(() => { borderOffset.value = 0 }, 150)
  setTimeout(() => { logoClip.value = 'inset(0 0 0% 0)' }, 950)

  const startTime = performance.now()

  const animate = (now: number) => {
    const elapsed = now - startTime
    progress.value = Math.min(100, (elapsed / DURATION) * 100)

    if (progress.value < 100) {
      requestAnimationFrame(animate)
    } else {
      setTimeout(flyLogoToHeader, 400)
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