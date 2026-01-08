<template>
  <DefaultLayout>
    <!-- Hero Section -->
    <HeroSection />

    <!-- Future sections will be added in upcoming sprints -->
    <section class="py-20 bg-white">
      <div class="container mx-auto px-4 text-center">
        <h2 class="text-4xl font-outfit font-bold mb-4">More Content Coming Soon</h2>
        <p class="text-lg text-dark-600">
          News, members, releases, and more sections will be added in upcoming sprints.
        </p>
      </div>
    </section>
  </DefaultLayout>
</template>

<script setup lang="ts">
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import HeroSection from '@/components/home/HeroSection.vue'
</script>
