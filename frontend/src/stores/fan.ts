import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

const API_BASE = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
const TOKEN_KEY = 'klp48-fan-token'

export interface FanUser {
  id: number
  name: string
  email: string
  phone: string | null
  tier: 'basic' | 'gold'
  status: 'active' | 'expired' | 'cancelled'
  benefits: string[]
  joined_at: string | null
  expires_at: string | null
}

export const useFanStore = defineStore('fan', () => {
  const token = ref<string | null>(localStorage.getItem(TOKEN_KEY))
  const user  = ref<FanUser | null>(null)
  const loading = ref(false)

  const isLoggedIn = computed(() => !!token.value)
  const isActive   = computed(() => {
    const s   = user.value?.status
    const exp = user.value?.expires_at
    if (!s) return false
    // Cancelled members keep access through the full expiry date (until 23:59:59).
    const notExpired = !exp || new Date(exp + 'T23:59:59') > new Date()
    return (s === 'active' || s === 'cancelled') && notExpired
  })

  /** Authenticated API call — throws { status, body } on non-2xx */
  async function api<T = unknown>(endpoint: string, options: RequestInit = {}): Promise<T> {
    const headers: Record<string, string> = {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      ...(options.headers as Record<string, string> ?? {}),
    }
    if (token.value) headers['Authorization'] = `Bearer ${token.value}`

    const res = await fetch(`${API_BASE}${endpoint}`, { ...options, headers })

    if (res.status === 401) {
      _clearSession()
      throw new Error('unauthenticated')
    }

    if (!res.ok) {
      const body = await res.json().catch(() => ({}))
      throw { status: res.status, body }
    }

    return res.json() as Promise<T>
  }

  async function login(email: string, password: string): Promise<void> {
    loading.value = true
    try {
      const data = await api<{ token: string; user: FanUser }>('/fan/login', {
        method: 'POST',
        body: JSON.stringify({ email, password }),
      })
      _saveSession(data.token, data.user)
    } finally {
      loading.value = false
    }
  }

  /**
   * Pre-registration: validate + create ToyyibPay bill.
   * Account is only created AFTER successful payment.
   * Returns the ToyyibPay bill URL to redirect the user to.
   */
  async function preRegister(payload: {
    name: string
    email: string
    phone?: string
    password: string
    password_confirmation: string
    tier: 'basic' | 'gold'
  }): Promise<{ billUrl: string; billCode: string }> {
    const res = await fetch(`${API_BASE}/fan/pre-register`, {
      method: 'POST',
      headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    })

    if (!res.ok) {
      const body = await res.json().catch(() => ({}))
      throw { status: res.status, body }
    }

    return res.json()
  }

  async function cancelMembership(): Promise<void> {
    const data = await api<{ user: FanUser }>('/fan/cancel', { method: 'POST' })
    user.value = data.user
  }

  async function logout(): Promise<void> {
    try {
      await api('/fan/logout', { method: 'POST' })
    } catch {
      // clear regardless
    }
    _clearSession()
  }

  async function fetchMe(): Promise<void> {
    if (!token.value) return
    try {
      const data = await api<{ user: FanUser }>('/fan/me')
      user.value = data.user
    } catch {
      _clearSession()
    }
  }

  function _saveSession(t: string, u: FanUser) {
    token.value = t
    user.value  = u
    localStorage.setItem(TOKEN_KEY, t)
  }

  function _clearSession() {
    token.value = null
    user.value  = null
    localStorage.removeItem(TOKEN_KEY)
  }

  return {
    token, user, loading,
    isLoggedIn, isActive,
    api, login, preRegister, cancelMembership, logout, fetchMe,
  }
})
