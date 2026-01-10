<template>
  <div class="news-card-skeleton bg-white rounded-xl overflow-hidden shadow-md">
    <!-- Image Skeleton -->
    <div class="skeleton-shimmer h-48 md:h-56 bg-neutral-200"></div>

    <!-- Content Skeleton -->
    <div class="p-5 md:p-6">
      <!-- Title Skeleton -->
      <div class="skeleton-shimmer h-6 bg-neutral-200 rounded mb-3 w-4/5"></div>
      <div class="skeleton-shimmer h-6 bg-neutral-200 rounded mb-4 w-3/5"></div>

      <!-- Excerpt Skeleton -->
      <div class="skeleton-shimmer h-4 bg-neutral-200 rounded mb-2 w-full"></div>
      <div class="skeleton-shimmer h-4 bg-neutral-200 rounded mb-2 w-full"></div>
      <div class="skeleton-shimmer h-4 bg-neutral-200 rounded mb-4 w-2/3"></div>

      <!-- Meta Skeleton -->
      <div class="flex items-center justify-between">
        <div class="skeleton-shimmer h-4 bg-neutral-200 rounded w-24"></div>
        <div class="skeleton-shimmer h-4 bg-neutral-200 rounded w-16"></div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Simple skeleton component - no logic needed
</script>

<style scoped>
/* Shimmer animation */
.skeleton-shimmer {
  animation: shimmer 1.5s infinite;
  background: linear-gradient(
    90deg,
    #e0e0e0 0%,
    #f0f0f0 50%,
    #e0e0e0 100%
  );
  background-size: 200% 100%;
}

@keyframes shimmer {
  0% {
    background-position: -200% 0;
  }
  100% {
    background-position: 200% 0;
  }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .skeleton-shimmer {
    background: linear-gradient(
      90deg,
      #2a2a2a 0%,
      #3a3a3a 50%,
      #2a2a2a 100%
    );
    background-size: 200% 100%;
  }
}
</style>
