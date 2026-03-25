export const LANGUAGES = {
  en: 'English',
  ms: 'Bahasa Melayu',
  ja: '日本語',
} as const

export type Language = keyof typeof LANGUAGES

export const SOCIAL_LINKS = {
  twitter: 'https://x.com/KLP48official',
  facebook: 'https://www.facebook.com/KLP48official',
  instagram: 'https://www.instagram.com/klp48official',
  tiktok: 'https://www.tiktok.com/@klp48official',
  line: '',
} as const

export const SUPPORT_EMAIL = 'support_klp48@twopointzero.world'

export const NEWS_CATEGORIES = [
  { value: 'all', label: 'All' },
  { value: 'news', label: 'News' },
  { value: 'fanclub', label: 'Fan Club' },
  { value: 'store', label: 'Store' },
  { value: 'event', label: 'Event' },
  { value: 'release', label: 'Release' },
] as const

export const VIDEO_TYPES = [
  { value: 'all', label: 'All' },
  { value: 'music-video', label: 'Music Video' },
  { value: 'performance', label: 'Performance' },
  { value: 'dance-practice', label: 'Dance Practice' },
  { value: 'behind-the-scenes', label: 'Behind the Scenes' },
] as const

export const RELEASE_TYPES = [
  { value: 'all', label: 'All' },
  { value: 'single', label: 'Single' },
  { value: 'album', label: 'Album' },
  { value: 'ep', label: 'EP' },
  { value: 'digital-single', label: 'Digital Single' },
] as const

export const EVENT_TYPES = [
  { value: 'all', label: 'All' },
  { value: 'concert', label: 'Concert' },
  { value: 'meet-greet', label: 'Meet & Greet' },
  { value: 'handshake', label: 'Handshake Event' },
  { value: 'online', label: 'Online Event' },
  { value: 'other', label: 'Other' },
] as const

export const SISTER_GROUPS = [
  { name: 'AKB48', url: 'https://www.akb48.co.jp/' },
  { name: 'SKE48', url: 'https://www.ske48.co.jp/' },
  { name: 'NMB48', url: 'https://www.nmb48.com/' },
  { name: 'HKT48', url: 'https://www.hkt48.jp/' },
  { name: 'NGT48', url: 'https://ngt48.jp/' },
  { name: 'STU48', url: 'https://www.stu48.com/' },
  { name: 'JKT48', url: 'https://www.jkt48.com/' },
  { name: 'BNK48', url: 'https://www.bnk48.com/' },
  { name: 'MNL48', url: 'https://www.mnl48.com/' },
  { name: 'AKB48 Team SH', url: '' },
  { name: 'TPE48', url: '' },
  { name: 'CGM48', url: 'https://www.cgm48.com/' },
] as const

export const SPONSORS = [
  'Karaoke Manekineko',
  'MTOWN',
  'KAMI Seitai',
  'COCOROCA',
  'GOOD LIFE',
  'mirAI',
] as const

export const NAV_ITEMS = [
  { path: '/', label: 'Home' },
  { path: '/news', label: 'News' },
  { path: '/members', label: 'Member' },
  { path: '/releases', label: 'Release' },
  { path: '/videos', label: 'Movie' },
  { path: '/about', label: 'About' },
  { path: '/schedule', label: 'Schedule' },
  { path: '/fanclub', label: 'Fanclub' },
] as const

export const API_CONFIG = {
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  timeout: parseInt(import.meta.env.VITE_API_TIMEOUT || '30000'),
} as const
