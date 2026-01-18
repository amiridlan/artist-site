<template>
  <div class="fanclub-page">
    <!-- Hero Section -->
    <section class="hero-section relative min-h-[70vh] flex items-center justify-center overflow-hidden">
      <!-- Background -->
      <div class="absolute inset-0 bg-[#288820]"></div>

      <!-- Content -->
      <div class="relative z-10 container mx-auto px-4 text-center text-white">
        <h1 class="text-5xl md:text-6xl lg:text-7xl font-avant-garde font-bold mb-6 animate-fade-in">
          Join the KLP48 Fan Club
        </h1>
        <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto opacity-90">
          Get exclusive access to behind-the-scenes content, priority event tickets, and connect with fans worldwide!
        </p>
        <button
          @click="scrollToTiers"
          class="px-8 py-4 bg-white text-primary-500 rounded-full font-outfit font-bold text-lg hover:scale-105 transition-transform shadow-2xl"
        >
          Explore Membership Tiers
        </button>
      </div>

      <!-- Decorative elements -->
      <div class="absolute bottom-0 left-0 right-0 h-32 "></div>
    </section>

    <!-- Benefits Overview -->
    <section ref="benefitsSection" class="py-16 md:py-24 bg-surface-100">
      <div class="container mx-auto px-4">
        <h2 class="text-3xl md:text-4xl font-avant-garde font-bold text-center mb-4">
          Fanclub Benefits
        </h2>
        <p class="text-lg text-neutral-900 text-center mb-12 max-w-2xl mx-auto">
          Become part of an exclusive community and enjoy amazing perks!
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          <div
            v-for="(benefit, index) in benefits"
            :key="index"
            class="benefit-card text-center p-6 rounded-xl bg-surface-200 border-2 border-neutral-200 hover:border-primary-500 transition-all hover:shadow-lg"
          >
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-primary-500 flex items-center justify-center">
              <component :is="benefit.icon" class="w-8 h-8 text-white" />
            </div>
            <h3 class="font-avant-garde font-bold text-lg mb-2">{{ benefit.title }}</h3>
            <p class="text-neutral-900 text-sm">{{ benefit.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Membership Tiers -->
    <section ref="tiersSection" class="py-16 md:py-24 bg-surface-200">
      <div class="container mx-auto px-4">
        <h2 class="text-3xl md:text-4xl font-avant-garde font-bold text-center mb-4">
          Choose Your Membership Tier
        </h2>
        <p class="text-lg text-neutral-900 text-center mb-12 max-w-2xl mx-auto">
          Select the perfect plan that fits your fan journey
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-7xl mx-auto">
          <div
            v-for="(tier, index) in tiers"
            :key="index"
            :class="[
              'tier-card relative rounded-2xl p-8 transition-all duration-300 hover:scale-105',
              tier.recommended
                ? 'bg-primary-500 text-white shadow-2xl ring-4 ring-primary-300 transform scale-105'
                : 'bg-surface-100 text-neutral-800 shadow-lg hover:shadow-xl'
            ]"
          >
            <!-- Recommended Badge -->
            <div
              v-if="tier.recommended"
              class="absolute -top-4 left-1/2 transform -translate-x-1/2 px-4 py-1 bg-primary-400 text-white rounded-full text-xs font-bold uppercase"
            >
              Most Popular
            </div>

            <!-- Tier Name -->
            <h3 class="font-avant-garde font-bold text-2xl mb-2">{{ tier.name }}</h3>

            <!-- Price -->
            <div class="mb-6">
              <span class="text-4xl font-bold">{{ tier.price === 0 ? 'FREE' : `MYR ${tier.price}` }}</span>
              <span v-if="tier.price > 0" :class="tier.recommended ? 'text-white/80' : 'text-neutral-500'">/month</span>
            </div>

            <!-- Features -->
            <ul class="space-y-3 mb-8">
              <li
                v-for="(feature, fIndex) in tier.features"
                :key="fIndex"
                class="flex items-start gap-2"
              >
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" :class="tier.recommended ? 'text-white' : 'text-primary-500'" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                <span :class="tier.recommended ? 'text-white/90' : 'text-neutral-700'" class="text-sm">{{ feature }}</span>
              </li>
            </ul>

            <!-- CTA Button -->
            <button
              @click="openRegistrationModal(tier)"
              :class="[
                'w-full py-3 rounded-full font-outfit font-semibold transition-all',
                tier.recommended
                  ? 'bg-white text-primary-500 hover:bg-neutral-50'
                  : 'bg-primary-500 text-white hover:scale-105'
              ]"
            >
              {{ tier.price === 0 ? 'Join Free' : 'Get Started' }}
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <section ref="faqSection" class="py-16 md:py-24 bg-surface-200">
      <div class="container mx-auto px-4 max-w-4xl">
        <h2 class="text-3xl md:text-4xl font-avant-garde font-bold text-center mb-12">
          Frequently Asked Questions
        </h2>

        <div class="space-y-4">
          <div
            v-for="(faq, index) in faqs"
            :key="index"
            class="faq-item bg-surface-100 rounded-xl overflow-hidden shadow-md"
          >
            <button
              @click="toggleFaq(index)"
              class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-neutral-100 transition-colors"
            >
              <span class="font-avant-garde font-semibold text-lg">{{ faq.question }}</span>
              <svg
                :class="['w-6 h-6 transition-transform', openFaqIndex === index && 'rotate-180']"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <transition name="accordion">
              <div v-if="openFaqIndex === index" class="px-6 pb-4">
                <p class="text-neutral-900 leading-relaxed">{{ faq.answer }}</p>
              </div>
            </transition>
          </div>
        </div>
      </div>
    </section>

    <!-- Registration Modal -->
    <teleport to="body">
      <transition name="fade">
        <div
          v-if="showRegistrationModal"
          class="fixed inset-0 bg-black/90 backdrop-blur-sm z-50 flex items-center justify-center p-4"
          @click.self="closeRegistrationModal"
        >
          <div class="relative w-full max-w-2xl bg-white rounded-2xl overflow-hidden shadow-2xl max-h-[90vh] overflow-y-auto">
            <!-- Close Button -->
            <button
              @click="closeRegistrationModal"
              class="absolute top-4 right-4 z-10 w-10 h-10 rounded-full bg-neutral-200 hover:bg-neutral-300 text-neutral-700 flex items-center justify-center transition-colors"
              aria-label="Close"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>

            <!-- Modal Content -->
            <div class="p-8">
              <h3 class="text-3xl font-outfit font-bold mb-2">Join {{ selectedTier?.name }}</h3>
              <p class="text-neutral-900 mb-6">Fill in your details to get started</p>

              <!-- Registration Form -->
              <form @submit.prevent="handleSubmit" class="space-y-6">
                <!-- Name -->
                <div>
                  <label class="block text-sm font-semibold text-neutral-900 mb-2">Full Name *</label>
                  <input
                    v-model="formData.name"
                    type="text"
                    required
                    class="w-full px-4 py-3 border-2 border-neutral-200 rounded-lg focus:border-primary-500 focus:outline-none transition-colors"
                    placeholder="Enter your full name"
                  />
                </div>

                <!-- Email -->
                <div>
                  <label class="block text-sm font-semibold text-neutral-900 mb-2">Email Address *</label>
                  <input
                    v-model="formData.email"
                    type="email"
                    required
                    class="w-full px-4 py-3 border-2 border-neutral-200 rounded-lg focus:border-primary-500 focus:outline-none transition-colors"
                    placeholder="your.email@example.com"
                  />
                </div>

                <!-- Phone -->
                <div>
                  <label class="block text-sm font-semibold text-neutral-900 mb-2">Phone Number</label>
                  <input
                    v-model="formData.phone"
                    type="tel"
                    class="w-full px-4 py-3 border-2 border-neutral-200 rounded-lg focus:border-primary-500 focus:outline-none transition-colors"
                    placeholder="+60 12-345 6789"
                  />
                </div>

                <!-- Country -->
                <div>
                  <label class="block text-sm font-semibold text-neutral-900 mb-2">Country *</label>
                  <select
                    v-model="formData.country"
                    required
                    class="w-full px-4 py-3 border-2 border-neutral-200 rounded-lg focus:border-primary-500 focus:outline-none transition-colors"
                  >
                    <option value="">Select your country</option>
                    <option value="MY">Malaysia</option>
                    <option value="SG">Singapore</option>
                    <option value="ID">Indonesia</option>
                    <option value="TH">Thailand</option>
                    <option value="PH">Philippines</option>
                    <option value="JP">Japan</option>
                    <option value="KR">South Korea</option>
                    <option value="OTHER">Other</option>
                  </select>
                </div>

                <!-- Terms -->
                <div class="flex items-start gap-2">
                  <input
                    v-model="formData.agreedToTerms"
                    type="checkbox"
                    required
                    class="mt-1 w-5 h-5 text-primary-500 border-2 border-neutral-300 rounded focus:ring-2 focus:ring-primary-500"
                  />
                  <label class="text-sm text-neutral-900">
                    I agree to the <a href="#" class="text-primary-500 hover:underline">Terms & Conditions</a> and <a href="#" class="text-primary-500 hover:underline">Privacy Policy</a>
                  </label>
                </div>

                <!-- Newsletter -->
                <div class="flex items-start gap-2">
                  <input
                    v-model="formData.newsletter"
                    type="checkbox"
                    class="mt-1 w-5 h-5 text-primary-500 border-2 border-neutral-300 rounded focus:ring-2 focus:ring-primary-500"
                  />
                  <label class="text-sm text-neutral-900">
                    Send me news, updates, and exclusive offers
                  </label>
                </div>

                <!-- Submit Button -->
                <button
                  type="submit"
                  :disabled="isSubmitting"
                  class="w-full py-4 bg-primary-500 text-white rounded-full font-outfit font-bold text-lg hover:scale-105 transition-transform disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  {{ isSubmitting ? 'Processing...' : `Join ${selectedTier?.name}` }}
                </button>

                <!-- Note -->
                <p class="text-xs text-center text-neutral-500">
                  This is a demo form. No actual registration will be processed.
                </p>
              </form>
            </div>
          </div>
        </div>
      </transition>
    </teleport>
  </div>
</template>

<script setup lang="ts">
import { ref, h } from 'vue'

// Icons as functional components
const TicketIcon = () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z' })
])

const VideoIcon = () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z' })
])

const UsersIcon = () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' })
])

const GiftIcon = () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
  h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7' })
])

// Data
const benefits = [
  {
    icon: TicketIcon,
    title: 'Priority Tickets',
    description: 'Get early access to event tickets before public sale'
  },
  {
    icon: VideoIcon,
    title: 'Exclusive Content',
    description: 'Behind-the-scenes videos, photos, and member vlogs'
  },
  {
    icon: UsersIcon,
    title: 'Fan Community',
    description: 'Connect with fans worldwide in our exclusive forum'
  },
  {
    icon: GiftIcon,
    title: 'Special Perks',
    description: 'Birthday gifts, surprise messages, and merchandise discounts'
  }
]

const tiers = [
  {
    name: 'Free',
    price: 0,
    recommended: false,
    features: [
      'Access to public news & updates',
      'Watch music videos',
      'Join community discussions',
      'Event calendar access',
      'Basic profile customization'
    ]
  },
  {
    name: 'Basic',
    price: 15,
    recommended: false,
    features: [
      'Everything in Free',
      'Monthly newsletter',
      'Exclusive wallpapers',
      'Early ticket notifications',
      '10% merchandise discount',
      'Digital member card'
    ]
  },
  {
    name: 'Premium',
    price: 30,
    recommended: true,
    features: [
      'Everything in Basic',
      'Priority ticket access',
      'Exclusive behind-the-scenes content',
      'Monthly video messages',
      '20% merchandise discount',
      'Birthday shoutout',
      'Members-only live streams'
    ]
  },
  {
    name: 'VIP',
    price: 60,
    recommended: false,
    features: [
      'Everything in Premium',
      'Guaranteed event tickets',
      'Exclusive meet & greet access',
      'Personalized birthday message',
      '30% merchandise discount',
      'Annual photo book',
      'VIP lounge access at events',
      'Backstage tour opportunity'
    ]
  }
]

const testimonials = [
  {
    name: 'Sarah L.',
    tier: 'Premium Member',
    quote: 'Being a premium member has been amazing! The exclusive content and priority tickets are worth every cent. I never miss a show now!'
  },
  {
    name: 'Ahmad R.',
    tier: 'VIP Member',
    quote: 'The VIP membership changed everything. Meeting the members backstage was a dream come true. The community is fantastic too!'
  },
  {
    name: 'Michelle T.',
    tier: 'Basic Member',
    quote: 'Even as a basic member, I feel connected to KLP48. The newsletter keeps me updated and the discounts are great for merch!'
  }
]

const faqs = [
  {
    question: 'How do I join the fan club?',
    answer: 'Simply choose your preferred membership tier above and click the "Get Started" or "Join Free" button. Fill in the registration form with your details, and you\'ll receive a confirmation email with your member credentials.'
  },
  {
    question: 'Can I upgrade my membership later?',
    answer: 'Yes! You can upgrade your membership at any time from your account dashboard. You\'ll be charged the difference on a pro-rated basis.'
  },
  {
    question: 'What payment methods do you accept?',
    answer: 'We accept major credit cards (Visa, Mastercard, American Express), online banking (FPX), and e-wallets (GrabPay, Touch \'n Go eWallet, Boost).'
  },
  {
    question: 'Is there a contract or commitment period?',
    answer: 'No, all memberships are month-to-month with no long-term commitment. You can cancel anytime from your account settings.'
  },
  {
    question: 'How do I get my member benefits?',
    answer: 'Once registered, you\'ll receive your digital member card via email. Use your account to access exclusive content, priority ticket sales, and special offers. Physical benefits like merchandise will be shipped to your registered address.'
  },
  {
    question: 'Can I share my membership with family/friends?',
    answer: 'Memberships are personal and non-transferable. Each member receives unique credentials tied to their email address. However, we do offer family packages - contact support for details!'
  }
]

// State
const tiersSection = ref<HTMLElement | null>(null)
const benefitsSection = ref<HTMLElement | null>(null)
const testimonialsSection = ref<HTMLElement | null>(null)
const faqSection = ref<HTMLElement | null>(null)
const showRegistrationModal = ref(false)
const selectedTier = ref<typeof tiers[0] | null>(null)
const openFaqIndex = ref<number | null>(null)
const isSubmitting = ref(false)

const formData = ref({
  name: '',
  email: '',
  phone: '',
  country: '',
  agreedToTerms: false,
  newsletter: false
})

// Methods
const scrollToTiers = () => {
  tiersSection.value?.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

const openRegistrationModal = (tier: typeof tiers[0]) => {
  selectedTier.value = tier
  showRegistrationModal.value = true
}

const closeRegistrationModal = () => {
  showRegistrationModal.value = false
  setTimeout(() => {
    selectedTier.value = null
    resetForm()
  }, 300)
}

const toggleFaq = (index: number) => {
  openFaqIndex.value = openFaqIndex.value === index ? null : index
}

const resetForm = () => {
  formData.value = {
    name: '',
    email: '',
    phone: '',
    country: '',
    agreedToTerms: false,
    newsletter: false
  }
}

const handleSubmit = async () => {
  isSubmitting.value = true

  // Simulate API call
  await new Promise(resolve => setTimeout(resolve, 1500))

  alert(`Thank you for joining ${selectedTier.value?.name}! This is a demo - no actual registration was processed.`)

  isSubmitting.value = false
  closeRegistrationModal()
}
</script>

<style scoped>
/* Hero animation */
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 1s ease-out;
}

/* Benefit card hover */
.benefit-card {
  transition: all 0.3s ease;
}

.benefit-card:hover {
  transform: translateY(-8px);
}

/* Tier card */
.tier-card {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Accordion transition */
.accordion-enter-active,
.accordion-leave-active {
  transition: all 0.3s ease;
  max-height: 300px;
  overflow: hidden;
}

.accordion-enter-from,
.accordion-leave-to {
  max-height: 0;
  opacity: 0;
}

/* Modal fade */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
