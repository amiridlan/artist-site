<template>
  <LoadingScreen v-if="showLoading" @done="onLoadingDone" />
  <div v-if="isRouteLoading" class="route-progress-bar" aria-hidden="true"></div>
  <div
    id="klp48-app"
    class="min-h-screen flex flex-col bg-cream-50"
    :style="{ opacity: 1 }"
  >
    <Header />
    <MobileMenu />
    <main class="flex-grow">
      <router-view v-slot="{ Component, route }">
        <transition :name="(route.meta.transition as string) || 'fade'" mode="out-in">
          <component :is="Component" :key="route.path" />
        </transition>
      </router-view>
    </main>
    <Footer />
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import Header from '@/components/layout/Header.vue'
import Footer from '@/components/layout/Footer.vue'
import MobileMenu from '@/components/layout/MobileMenu.vue'
import LoadingScreen from '@/components/ui/LoadingScreen.vue'
import { isRouteLoading } from '@/composables/useRouteLoading'

const SPLASH_SESSION_KEY = 'klp48-splash-shown'
const showLoading = ref(!sessionStorage.getItem(SPLASH_SESSION_KEY))

function onLoadingDone() {
  showLoading.value = false
  sessionStorage.setItem(SPLASH_SESSION_KEY, '1')
}
</script>

<style>
.route-progress-bar {
  position: fixed;
  top: 0;
  left: 0;
  height: 3px;
  width: 30%;
  z-index: 999;
  background: linear-gradient(90deg, transparent, #00B4A0, transparent);
  animation: route-progress-slide 0.9s ease-in-out infinite;
}

@keyframes route-progress-slide {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(400%); }
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.slide-left-enter-active,
.slide-left-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.slide-left-enter-from {
  opacity: 0;
  transform: translateX(40px);
}
.slide-left-leave-to {
  opacity: 0;
  transform: translateX(-40px);
}

.slide-right-enter-active,
.slide-right-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.slide-right-enter-from {
  opacity: 0;
  transform: translateX(-40px);
}
.slide-right-leave-to {
  opacity: 0;
  transform: translateX(40px);
}

.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.45s cubic-bezier(0.4, 0, 0.2, 1);
}
.slide-up-enter-from {
  opacity: 0;
  transform: translateY(50px);
}
.slide-up-leave-to {
  opacity: 0;
  transform: translateY(-50px);
}
</style>
