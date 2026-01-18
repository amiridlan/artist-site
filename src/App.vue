<template>
  <div id="app" class="min-h-screen flex flex-col bg-surface-100">
    <!-- Header (static, no animation) -->
    <Header />

    <!-- Mobile Menu (static, no animation) -->
    <MobileMenu />

    <!-- Main Content (animated) -->
    <main class="flex-grow">
      <router-view v-slot="{ Component, route }">
        <transition
          :name="route.meta.transition as string || 'fade'"
          mode="out-in"
        >
          <component :is="Component" :key="route.path" />
        </transition>
      </router-view>
    </main>

    <!-- Footer (static, no animation) -->
    <Footer />
  </div>
</template>

<script setup lang="ts">
import Header from '@/components/layout/Header.vue'
import Footer from '@/components/layout/Footer.vue'
import MobileMenu from '@/components/layout/MobileMenu.vue'
</script>

<style>
/* Page transition styles */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Slide Left - Moving Forward (News → Members, Schedule → FanClub) */
.slide-left-enter-active,
.slide-left-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-left-enter-from {
  opacity: 0;
  transform: translateX(50px);
}

.slide-left-leave-to {
  opacity: 0;
  transform: translateX(-50px);
}

/* Slide Right - Moving Backward (Members → News, FanClub → Schedule) */
.slide-right-enter-active,
.slide-right-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-right-enter-from {
  opacity: 0;
  transform: translateX(-50px);
}

.slide-right-leave-to {
  opacity: 0;
  transform: translateX(50px);
}

.zoom-enter-active,
.zoom-leave-active {
  transition: all 0.3s ease;
}

.zoom-enter-from {
  opacity: 0;
  transform: scale(0.9);
}

.zoom-leave-to {
  opacity: 0;
  transform: scale(1.1);
}
</style>