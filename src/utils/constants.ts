// Language options
export const LANGUAGES = {
  en: 'English',
  ja: '日本語',
  'zh-CN': '中文(简体)',
  ms: 'Bahasa Melayu',
} as const

export type Language = keyof typeof LANGUAGES

// Social media links
export const SOCIAL_LINKS = {
  twitter: import.meta.env.VITE_TWITTER_URL || 'https://x.com/KLP48official',
  tiktok: import.meta.env.VITE_TIKTOK_URL || 'https://x.gd/EutXI',
  instagram: import.meta.env.VITE_INSTAGRAM_URL || '',
  youtube: import.meta.env.VITE_YOUTUBE_URL || '',
}

// News categories
export const NEWS_CATEGORIES = [
  { value: 'all', label: 'All' },
  { value: 'news', label: 'News' },
  { value: 'fanclub', label: 'Fan Club' },
  { value: 'store', label: 'Store' },
  { value: 'event', label: 'Event' },
  { value: 'release', label: 'Release' },
] as const

// Video types
export const VIDEO_TYPES = [
  { value: 'all', label: 'All' },
  { value: 'music-video', label: 'Music Video' },
  { value: 'performance', label: 'Performance' },
  { value: 'behind-the-scenes', label: 'Behind the Scenes' },
  { value: 'interview', label: 'Interview' },
  { value: 'variety', label: 'Variety' },
] as const

// Release types
export const RELEASE_TYPES = [
  { value: 'all', label: 'All' },
  { value: 'single', label: 'Single' },
  { value: 'album', label: 'Album' },
  { value: 'ep', label: 'EP' },
  { value: 'digital-single', label: 'Digital Single' },
] as const

// Event types
export const EVENT_TYPES = [
  { value: 'all', label: 'All' },
  { value: 'concert', label: 'Concert' },
  { value: 'meet-greet', label: 'Meet & Greet' },
  { value: 'handshake', label: 'Handshake Event' },
  { value: 'online', label: 'Online Event' },
  { value: 'other', label: 'Other' },
] as const

// Streaming platforms
export const STREAMING_PLATFORMS = {
  spotify: {
    name: 'Spotify',
    icon: 'spotify',
    color: '#1DB954',
  },
  'apple-music': {
    name: 'Apple Music',
    icon: 'apple',
    color: '#FA243C',
  },
  'youtube-music': {
    name: 'YouTube Music',
    icon: 'youtube',
    color: '#FF0000',
  },
  'amazon-music': {
    name: 'Amazon Music',
    icon: 'amazon',
    color: '#00A8E1',
  },
} as const

// Animation durations (in milliseconds)
export const ANIMATION_DURATION = {
  fast: 150,
  normal: 300,
  slow: 500,
} as const

// Breakpoints (matching Tailwind defaults)
export const BREAKPOINTS = {
  sm: 640,
  md: 768,
  lg: 1024,
  xl: 1280,
  '2xl': 1536,
} as const

// API Configuration
export const API_CONFIG = {
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  timeout: parseInt(import.meta.env.VITE_API_TIMEOUT || '30000'),
} as const

// App Configuration
export const APP_CONFIG = {
  name: import.meta.env.VITE_APP_NAME || 'KLP48',
  description: import.meta.env.VITE_APP_DESCRIPTION || "Malaysia's Premier Idol Group",
  defaultLang: import.meta.env.VITE_DEFAULT_LANG || 'en',
  cdnUrl: import.meta.env.VITE_CDN_URL || '',
} as const