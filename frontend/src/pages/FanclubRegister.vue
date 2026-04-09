<template>
  <div class="pt-24 pb-16 min-h-screen bg-cream-50 px-4">
    <div class="max-w-2xl mx-auto">

      <!-- Header -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-jade-500 text-white text-2xl mb-4">♡</div>
        <h1 class="text-2xl font-heading font-bold text-charcoal-800">Join the Fanclub</h1>
        <p class="text-charcoal-500 mt-1 text-sm">Fill in your details, choose a tier, and complete payment to activate your membership.</p>
      </div>

      <!-- Step indicator -->
      <div class="flex items-center justify-center gap-2 mb-8 text-sm">
        <span class="flex items-center gap-1.5 font-medium" :class="step === 1 ? 'text-jade-600' : 'text-charcoal-400'">
          <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold border-2"
                :class="step === 1 ? 'border-jade-500 bg-jade-500 text-white' : (step > 1 ? 'border-jade-500 bg-jade-500 text-white' : 'border-gray-300 text-gray-400')">
            {{ step > 1 ? '✓' : '1' }}
          </span>
          Your Details
        </span>
        <div class="w-10 h-px bg-gray-300" />
        <span class="flex items-center gap-1.5 font-medium" :class="step === 2 ? 'text-jade-600' : 'text-charcoal-400'">
          <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold border-2"
                :class="step === 2 ? 'border-jade-500 bg-jade-500 text-white' : 'border-gray-300 text-gray-400'">
            2
          </span>
          Choose Tier &amp; Pay
        </span>
      </div>

      <div class="bg-white rounded-2xl shadow-card p-8">

        <!-- ── Step 1: Personal details ── -->
        <form v-if="step === 1" @submit.prevent="nextStep" class="space-y-5">
          <div v-if="errors._general" class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
            {{ errors._general }}
          </div>

          <div>
            <label class="block text-sm font-medium text-charcoal-700 mb-1.5">Full Name</label>
            <input v-model="form.name" type="text" required autocomplete="name"
              class="w-full px-4 py-2.5 rounded-lg border text-sm focus:outline-none focus:border-jade-500 focus:ring-1 focus:ring-jade-500 transition-colors"
              :class="errors.name ? 'border-red-400' : 'border-gray-300'"
              placeholder="Your full name" />
            <p v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-charcoal-700 mb-1.5">Email</label>
            <input v-model="form.email" type="email" required autocomplete="email"
              class="w-full px-4 py-2.5 rounded-lg border text-sm focus:outline-none focus:border-jade-500 focus:ring-1 focus:ring-jade-500 transition-colors"
              :class="errors.email ? 'border-red-400' : 'border-gray-300'"
              placeholder="your@email.com" />
            <p v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-charcoal-700 mb-1.5">
              Phone <span class="text-charcoal-400 font-normal">(optional)</span>
            </label>
            <input v-model="form.phone" type="tel" autocomplete="tel"
              class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:border-jade-500 focus:ring-1 focus:ring-jade-500 transition-colors"
              placeholder="+60 12-345 6789" />
          </div>

          <div>
            <label class="block text-sm font-medium text-charcoal-700 mb-1.5">Password</label>
            <input v-model="form.password" type="password" required autocomplete="new-password"
              class="w-full px-4 py-2.5 rounded-lg border text-sm focus:outline-none focus:border-jade-500 focus:ring-1 focus:ring-jade-500 transition-colors"
              :class="errors.password ? 'border-red-400' : 'border-gray-300'"
              placeholder="At least 8 characters" />
            <p v-if="errors.password" class="text-red-500 text-xs mt-1">{{ errors.password }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-charcoal-700 mb-1.5">Confirm Password</label>
            <input v-model="form.password_confirmation" type="password" required autocomplete="new-password"
              class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:border-jade-500 focus:ring-1 focus:ring-jade-500 transition-colors"
              placeholder="Repeat your password" />
          </div>

          <button type="submit"
            class="w-full py-3 bg-jade-gradient text-white font-semibold rounded-full hover:shadow-jade-glow transition-all duration-300">
            Continue →
          </button>
        </form>

        <!-- ── Step 2: Tier selection + pay ── -->
        <div v-else class="space-y-6">
          <div v-if="errors._general" class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
            {{ errors._general }}
          </div>

          <p class="text-sm text-charcoal-600">Registering as <strong>{{ form.email }}</strong></p>

          <!-- Tier cards -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Basic -->
            <button @click="form.tier = 'basic'" type="button"
              class="text-left rounded-xl border-2 p-5 transition-all duration-200 focus:outline-none"
              :class="form.tier === 'basic' ? 'border-jade-500 bg-jade-50' : 'border-gray-200 hover:border-jade-300'">
              <div class="flex items-center justify-between mb-3">
                <span class="px-2.5 py-0.5 rounded-full bg-gray-100 text-charcoal-600 text-xs font-semibold uppercase">Basic</span>
                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                     :class="form.tier === 'basic' ? 'border-jade-500 bg-jade-500' : 'border-gray-300'">
                  <div v-if="form.tier === 'basic'" class="w-2 h-2 rounded-full bg-white" />
                </div>
              </div>
              <p class="text-2xl font-bold text-charcoal-800 mb-3">RM 30<span class="text-sm font-normal text-charcoal-500">/year</span></p>
              <ul class="space-y-1">
                <li v-for="b in basicBenefits" :key="b" class="flex items-center gap-1.5 text-xs text-charcoal-600">
                  <span class="text-jade-500">✓</span> {{ b }}
                </li>
              </ul>
            </button>

            <!-- Gold -->
            <button @click="form.tier = 'gold'" type="button"
              class="text-left rounded-xl border-2 p-5 transition-all duration-200 focus:outline-none relative"
              :class="form.tier === 'gold' ? 'border-amber-400 bg-amber-50' : 'border-gray-200 hover:border-amber-300'">
              <div class="absolute top-2 right-2">
                <span class="px-2 py-0.5 rounded-full bg-amber-400 text-white text-xs font-bold">BEST</span>
              </div>
              <div class="flex items-center justify-between mb-3">
                <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-700 text-xs font-semibold uppercase">Gold</span>
                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                     :class="form.tier === 'gold' ? 'border-amber-500 bg-amber-500' : 'border-gray-300'">
                  <div v-if="form.tier === 'gold'" class="w-2 h-2 rounded-full bg-white" />
                </div>
              </div>
              <p class="text-2xl font-bold text-charcoal-800 mb-3">RM 60<span class="text-sm font-normal text-charcoal-500">/year</span></p>
              <ul class="space-y-1">
                <li v-for="b in goldBenefits" :key="b" class="flex items-center gap-1.5 text-xs text-charcoal-600">
                  <span class="text-amber-500">✓</span> {{ b }}
                </li>
              </ul>
            </button>
          </div>

          <div class="flex gap-3">
            <button @click="step = 1; clearErrors()" type="button"
              class="px-6 py-3 border-2 border-gray-200 text-charcoal-600 font-semibold rounded-full hover:border-gray-300 transition-colors">
              ← Back
            </button>
            <button @click="submit" :disabled="!form.tier || loading"
              class="flex-1 py-3 bg-jade-gradient text-white font-semibold rounded-full hover:shadow-jade-glow transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
              {{ loading ? 'Preparing payment…' : `Register & Pay — RM ${form.tier === 'gold' ? '60' : '30'}` }}
            </button>
          </div>

          <p class="text-center text-xs text-charcoal-400">
            Secure payment via ToyyibPay · DuitNow QR · FPX · Credit/Debit Card<br/>
            Your account is created automatically after payment is confirmed.
          </p>
        </div>
      </div>

      <p class="text-center text-xs text-charcoal-400 mt-4">
        Already have an account?
        <RouterLink to="/fanclub/login" class="text-jade-600 hover:text-jade-700 font-medium">Sign in</RouterLink>
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useFanStore } from '@/stores/fan'

const fan  = useFanStore()
const step = ref(1)

const form = reactive({
  name: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
  tier: '' as 'basic' | 'gold' | '',
})

const errors  = reactive<Record<string, string>>({})
const loading = ref(false)

const basicBenefits = ['Monthly newsletter', 'Digital wallpapers', 'Members-only announcements', 'Fanclub ID card']
const goldBenefits  = [...basicBenefits, 'Priority ticketing', 'Merch discount (10%)', 'Discord Radio Talk', 'Birthday shoutout']

function clearErrors() {
  Object.keys(errors).forEach(k => delete errors[k])
}

function nextStep() {
  clearErrors()
  // Client-side validation before moving to step 2
  if (!form.name.trim())  { errors.name = 'Name is required.'; return }
  if (!form.email.trim()) { errors.email = 'Email is required.'; return }
  if (form.password.length < 8) { errors.password = 'Password must be at least 8 characters.'; return }
  if (form.password !== form.password_confirmation) { errors.password = 'Passwords do not match.'; return }
  step.value = 2
}

async function submit() {
  if (!form.tier) return
  clearErrors()
  loading.value = true
  try {
    const { billUrl } = await fan.preRegister({
      name:                  form.name,
      email:                 form.email,
      phone:                 form.phone || undefined,
      password:              form.password,
      password_confirmation: form.password_confirmation,
      tier:                  form.tier,
    })
    // Redirect to ToyyibPay — account will be created on payment confirmation
    window.location.href = billUrl
  } catch (e: unknown) {
    const err = e as { body?: { errors?: Record<string, string[]>; message?: string } }
    if (err?.body?.errors) {
      for (const [field, msgs] of Object.entries(err.body.errors)) {
        errors[field] = (msgs as string[])[0]
      }
      // If the error is on a step-1 field, go back to step 1
      if (errors.name || errors.email || errors.password) {
        step.value = 1
      } else {
        errors._general = Object.values(errors)[0]
      }
    } else {
      errors._general = err?.body?.message || 'Something went wrong. Please try again.'
    }
    loading.value = false
  }
}
</script>
