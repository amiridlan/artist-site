<template>
  <div class="member-card-skeleton">
    <!-- Card Container -->
    <div class="rounded-xl overflow-hidden bg-white shadow-md">
      <!-- Image Skeleton -->
      <div class="skeleton-shimmer aspect-[3/4] bg-dark-200"></div>

      <!-- Content Area (if needed for extended skeleton) -->
      <div class="p-4 space-y-2">
        <div class="skeleton-shimmer h-6 bg-dark-200 rounded w-3/4"></div>
        <div class="skeleton-shimmer h-4 bg-dark-200 rounded w-1/2"></div>
        <div class="flex gap-2 mt-3">
          <div class="skeleton-shimmer h-6 bg-dark-200 rounded w-16"></div>
          <div class="skeleton-shimmer h-6 bg-dark-200 rounded w-16"></div>
        </div>
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
