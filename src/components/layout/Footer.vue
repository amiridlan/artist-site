<template>
  <footer class="bg-neutral-900 text-white/90 relative overflow-hidden">
    <!-- Background gradient overlay -->
    <div class="absolute inset-0 bg-primary-500/5"></div>

    <div class="relative z-10 container mx-auto px-4 py-12 md:py-16">
      <!-- Main Footer Content -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 md:gap-12 mb-12">
        <!-- Brand Section -->
        <div class="space-y-4">
          <h3 class="text-3xl font-outfit font-bold">
            <span class="text-gradient">KLP48</span>
          </h3>
          <p class="text-white/70 text-sm leading-relaxed">
            {{ appConfig.description }}
          </p>
          <!-- Social Media Links -->
          <div class="flex space-x-3 pt-2">
            <a
              v-if="socialLinks.twitter"
              :href="socialLinks.twitter"
              target="_blank"
              rel="noopener noreferrer"
              class="social-icon-btn"
              aria-label="Follow us on Twitter"
            >
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
              </svg>
            </a>
            <a
              v-if="socialLinks.tiktok"
              :href="socialLinks.tiktok"
              target="_blank"
              rel="noopener noreferrer"
              class="social-icon-btn"
              aria-label="Follow us on TikTok"
            >
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/>
              </svg>
            </a>
            <a
              v-if="socialLinks.instagram"
              :href="socialLinks.instagram"
              target="_blank"
              rel="noopener noreferrer"
              class="social-icon-btn"
              aria-label="Follow us on Instagram"
            >
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
              </svg>
            </a>
            <a
              v-if="socialLinks.youtube"
              :href="socialLinks.youtube"
              target="_blank"
              rel="noopener noreferrer"
              class="social-icon-btn"
              aria-label="Follow us on YouTube"
            >
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
              </svg>
            </a>
          </div>
        </div>

        <!-- Quick Links -->
        <div>
          <h4 class="font-outfit font-semibold text-lg mb-4 uppercase tracking-wide">
            Quick Links
          </h4>
          <ul class="space-y-2">
            <li v-for="link in quickLinks" :key="link.path">
              <router-link
                :to="link.path"
                class="text-white/70 hover:text-primary-500 transition-colors text-sm"
              >
                {{ link.label }}
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Information -->
        <div>
          <h4 class="font-outfit font-semibold text-lg mb-4 uppercase tracking-wide">
            Information
          </h4>
          <ul class="space-y-2">
            <li v-for="link in infoLinks" :key="link.label">
              <a
                :href="link.path"
                class="text-white/70 hover:text-primary-500 transition-colors text-sm"
                target="_blank"
                rel="noopener noreferrer"
              >
                {{ link.label }}
              </a>
            </li>
          </ul>
        </div>

        <!-- Newsletter (Optional) -->
        <div>
          <h4 class="font-outfit font-semibold text-lg mb-4 uppercase tracking-wide">
            Stay Updated
          </h4>
          <p class="text-white/70 text-sm mb-4">
            Subscribe to get the latest news and updates.
          </p>
          <form @submit.prevent="handleNewsletterSubmit" class="space-y-2">
            <input
              v-model="newsletterEmail"
              type="email"
              placeholder="Enter your email"
              class="w-full px-4 py-2 rounded-lg bg-white/10 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm"
              aria-label="Email for newsletter"
            />
            <button
              type="submit"
              class="w-full px-4 py-2 bg-primary-500 text-white rounded-lg font-medium text-sm hover:scale-105 transition-transform"
            >
              Subscribe
            </button>
          </form>
        </div>
      </div>

      <!-- Sponsor Logos (Optional - placeholder for now) -->
      <div v-if="sponsors.length > 0" class="border-t border-white/10 pt-8 mb-8">
        <h4 class="text-center font-outfit font-semibold text-lg mb-6 uppercase tracking-wide">
          Our Sponsors
        </h4>
        <div class="grid grid-cols-3 md:grid-cols-6 gap-6 items-center">
          <a
            v-for="sponsor in sponsors"
            :key="sponsor.name"
            :href="sponsor.url"
            target="_blank"
            rel="noopener noreferrer"
            class="sponsor-logo"
            :aria-label="`Visit ${sponsor.name} website`"
          >
            <img
              :src="sponsor.logo"
              :alt="sponsor.name"
              class="w-full h-auto"
            />
          </a>
        </div>
      </div>

      <!-- Copyright -->
      <div class="border-t border-white/10 pt-8 text-center">
        <p class="text-white/50 text-sm">
          &copy; {{ currentYear }} {{ appConfig.name }}. All rights reserved.
        </p>
        <p class="text-white/40 text-xs mt-2">
          Designed and developed with passion for fans worldwide.
        </p>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { APP_CONFIG, SOCIAL_LINKS } from '@/utils/constants'

// State
const newsletterEmail = ref('')

// Computed
const currentYear = computed(() => new Date().getFullYear())
const appConfig = computed(() => APP_CONFIG)
const socialLinks = computed(() => SOCIAL_LINKS)

// Quick links
const quickLinks = [
  { label: 'Home', path: '/' },
  { label: 'News', path: '/news' },
  { label: 'Members', path: '/members' },
  { label: 'Videos', path: '/videos' },
  { label: 'Schedule', path: '/schedule' },
  { label: 'Fan Club', path: '/fanclub' },
]

// Information links
const infoLinks = [
  { label: 'About Us', path: '/about' },
  { label: 'FAQ', path: '#' }, // TODO: Add FAQ page
  { label: 'Privacy Policy', path: '#' }, // TODO: Add Privacy Policy page
  { label: 'Terms of Service', path: '#' }, // TODO: Add Terms page
  { label: 'Contact', path: '#' }, // TODO: Add Contact page
]

// Sponsors (placeholder - can be populated later)
const sponsors = ref<Array<{ name: string; logo: string; url: string }>>([])

// Methods
const handleNewsletterSubmit = () => {
  if (newsletterEmail.value) {
    // TODO: Implement newsletter subscription (backend integration in future)
    console.log('Newsletter subscription:', newsletterEmail.value)
    alert('Thank you for subscribing! (Newsletter feature coming soon)')
    newsletterEmail.value = ''
  }
}
</script>

<style scoped>
/* Social icon buttons */
.social-icon-btn {
  @apply w-10 h-10 flex items-center justify-center rounded-full;
  @apply bg-white/10 hover:bg-primary-500;
  @apply transition-all duration-300;
  @apply hover:scale-110 hover:shadow-neon;
}

/* Sponsor logos */
.sponsor-logo {
  @apply grayscale opacity-60 hover:grayscale-0 hover:opacity-100;
  @apply transition-all duration-300;
  @apply hover:scale-110;
}
</style>
