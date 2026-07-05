import { ref, type Ref } from 'vue'

const API_BASE = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

// In-memory cache with TTL
interface CacheEntry<T> {
  data: T
  timestamp: number
}

const cache = new Map<string, CacheEntry<unknown>>()
const CACHE_TTL = 5 * 60 * 1000 // 5 minutes

function getCurrentLang(): string {
  return localStorage.getItem('klp48-lang') || 'en'
}

function buildUrl(endpoint: string, params?: Record<string, string>): string {
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
  return url.toString()
}

function getCacheKey(endpoint: string, params?: Record<string, string>): string {
  return buildUrl(endpoint, params)
}

function getFromCache<T>(key: string): T | null {
  const entry = cache.get(key) as CacheEntry<T> | undefined
  if (!entry) return null

  // Check if cache is still valid
  if (Date.now() - entry.timestamp > CACHE_TTL) {
    cache.delete(key)
    return null
  }

  return entry.data
}

function setCache<T>(key: string, data: T): void {
  cache.set(key, { data, timestamp: Date.now() })
}

// Clear cache when language changes
export function clearApiCache(): void {
  cache.clear()
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
    const cacheKey = getCacheKey(endpoint, params)

    // Return cached data immediately if available
    const cached = getFromCache<T>(cacheKey)
    if (cached) {
      data.value = cached
      // Still fetch in background to update cache (stale-while-revalidate)
      fetchFresh(cacheKey).catch(() => {})
      return
    }

    loading.value = true
    error.value = null

    try {
      const result = await fetchFresh<T>(cacheKey)
      data.value = result
    } catch (e) {
      error.value = e instanceof Error ? e.message : 'Failed to fetch'
    } finally {
      loading.value = false
    }
  }

  async function fetchFresh<R>(url: string): Promise<R> {
    const response = await window.fetch(url, {
      headers: { 'Accept': 'application/json' },
    })

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`)
    }

    const json = await response.json()
    const result = json.data ?? json
    setCache(url, result)
    return result
  }

  return { data, loading, error, fetch: fetchData }
}

export async function apiFetch<T>(endpoint: string, params?: Record<string, string>): Promise<T> {
  const cacheKey = getCacheKey(endpoint, params)

  // Return cached data if available and fresh
  const cached = getFromCache<T>(cacheKey)
  if (cached) {
    // Refresh cache in background
    fetchAndCache<T>(cacheKey).catch(() => {})
    return cached
  }

  return fetchAndCache<T>(cacheKey)
}

async function fetchAndCache<T>(url: string): Promise<T> {
  const response = await window.fetch(url, {
    headers: { 'Accept': 'application/json' },
  })

  if (!response.ok) {
    throw new Error(`HTTP ${response.status}`)
  }

  const json = await response.json()
  const result = json.data ?? json
  setCache(url, result)
  return result
}

// Prefetch data (call on hover or before navigation)
export function prefetch(endpoint: string, params?: Record<string, string>): void {
  const cacheKey = getCacheKey(endpoint, params)
  if (!getFromCache(cacheKey)) {
    fetchAndCache(cacheKey).catch(() => {})
  }
}
