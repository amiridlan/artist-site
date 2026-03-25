import { ref, type Ref } from 'vue'

const API_BASE = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

function getCurrentLang(): string {
  return localStorage.getItem('klp48-lang') || 'en'
}

interface UseApiReturn<T> {
  data: Ref<T | null>
  loading: Ref<boolean>
  error: Ref<string | null>
  fetch: () => Promise<void>
}

export function useApi<T>(endpoint: string, params?: Record<string, string>): UseApiReturn<T> {
  const data = ref<T | null>(null) as Ref<T | null>
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function fetchData() {
    loading.value = true
    error.value = null

    try {
      const url = new URL(`${API_BASE}${endpoint}`)
      const lang = getCurrentLang()
      if (lang !== 'en') {
        url.searchParams.set('lang', lang)
      }
      if (params) {
        Object.entries(params).forEach(([key, value]) => {
          if (value && value !== 'all') {
            url.searchParams.set(key, value)
          }
        })
      }

      const response = await window.fetch(url.toString(), {
        headers: { 'Accept': 'application/json' },
      })

      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`)
      }

      const json = await response.json()
      data.value = json.data ?? json
    } catch (e) {
      error.value = e instanceof Error ? e.message : 'Failed to fetch'
    } finally {
      loading.value = false
    }
  }

  return { data, loading, error, fetch: fetchData }
}

export async function apiFetch<T>(endpoint: string, params?: Record<string, string>): Promise<T> {
  const url = new URL(`${API_BASE}${endpoint}`)
  const lang = getCurrentLang()
  if (lang !== 'en') {
    url.searchParams.set('lang', lang)
  }
  if (params) {
    Object.entries(params).forEach(([key, value]) => {
      if (value && value !== 'all') {
        url.searchParams.set(key, value)
      }
    })
  }

  const response = await window.fetch(url.toString(), {
    headers: { 'Accept': 'application/json' },
  })

  if (!response.ok) {
    throw new Error(`HTTP ${response.status}`)
  }

  const json = await response.json()
  return json.data ?? json
}
