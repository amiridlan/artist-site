<template>
  <div class="home-page">
    <!-- Hero Section -->
    <HeroSection />

    <!-- Future sections will be added in upcoming sprints -->
    <section class="py-20 bg-surface-100">
      <div class="container mx-auto px-4 text-center">
        <h2 class="text-4xl font-outfit font-bold mb-4">More Content Coming Soon</h2>
        <p class="text-lg text-neutral-600">
          News, members, releases, and more sections will be added in upcoming sprints.
        </p>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import HeroSection from '@/components/home/HeroSection.vue'
</script>
