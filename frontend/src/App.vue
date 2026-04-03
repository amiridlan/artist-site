<template>
  <LoadingScreen v-if="showLoading" @done="onLoadingDone" />
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

const showLoading = ref(true)

function onLoadingDone() {
  showLoading.value = false
}
</script>

<style>
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
