<template>
  <div class="default-layout min-h-screen flex flex-col bg-surface-100">
    <!-- Header -->
    <Header />

    <!-- Mobile Menu -->
    <MobileMenu />

    <!-- Main Content -->
    <main class="flex-grow">
      <slot />
    </main>

    <!-- Footer -->
    <Footer />
  </div>
</template>

<script setup lang="ts">
import Header from '@/components/layout/Header.vue'
import Footer from '@/components/layout/Footer.vue'
import MobileMenu from '@/components/layout/MobileMenu.vue'
</script>

<style scoped>
/* Layout styles if needed */
</style>
