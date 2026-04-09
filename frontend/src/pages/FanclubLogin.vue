<template>
  <div class="pt-24 pb-16 min-h-screen bg-cream-50 flex items-center justify-center px-4">
    <div class="w-full max-w-md">
      <!-- Logo / heading -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-jade-500 text-white text-2xl mb-4">
          ♡
        </div>
        <h1 class="text-2xl font-heading font-bold text-charcoal-800">Fanclub Login</h1>
        <p class="text-charcoal-500 mt-1 text-sm">Sign in to access your member portal.</p>
      </div>

      <!-- Card -->
      <div class="bg-white rounded-2xl shadow-card p-8">
        <form @submit.prevent="submit" class="space-y-5">
          <!-- Error banner -->
          <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
            {{ error }}
          </div>

          <div>
            <label class="block text-sm font-medium text-charcoal-700 mb-1.5">Email</label>
            <input
              v-model="form.email"
              type="email"
              required
              autocomplete="email"
              class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:border-jade-500 focus:ring-1 focus:ring-jade-500 transition-colors"
              placeholder="your@email.com"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-charcoal-700 mb-1.5">Password</label>
            <input
              v-model="form.password"
              type="password"
              required
              autocomplete="current-password"
              class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:border-jade-500 focus:ring-1 focus:ring-jade-500 transition-colors"
              placeholder="••••••••"
            />
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full py-3 bg-jade-gradient text-white font-semibold rounded-full hover:shadow-jade-glow transition-all duration-300 disabled:opacity-60 disabled:cursor-not-allowed"
          >
            {{ loading ? 'Signing in…' : 'Sign In' }}
          </button>
        </form>

        <p class="text-center text-sm text-charcoal-500 mt-6">
          Not a member yet?
          <RouterLink to="/fanclub/register" class="text-jade-600 font-medium hover:text-jade-700">
            Register here
          </RouterLink>
        </p>
      </div>

      <p class="text-center text-xs text-charcoal-400 mt-4">
        <RouterLink to="/fanclub" class="hover:text-jade-600">← Back to Fanclub</RouterLink>
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useFanStore } from '@/stores/fan'

const router = useRouter()
const route  = useRoute()
const fan    = useFanStore()

const form    = ref({ email: '', password: '' })
const error   = ref<string | null>(null)
const loading = ref(false)

async function submit() {
  error.value   = null
  loading.value = true
  try {
    await fan.login(form.value.email, form.value.password)
    const redirect = (route.query.redirect as string) || '/fanclub/portal'
    router.push(redirect)
  } catch (e: unknown) {
    const err = e as { body?: { errors?: Record<string, string[]>; message?: string } }
    if (err?.body?.errors?.email) {
      error.value = err.body.errors.email[0]
    } else if (err?.body?.message) {
      error.value = err.body.message
    } else {
      error.value = 'Login failed. Please check your email and password.'
    }
  } finally {
    loading.value = false
  }
}
</script>
