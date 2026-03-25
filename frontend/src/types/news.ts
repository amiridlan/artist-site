export type NewsCategory = 'all' | 'news' | 'fanclub' | 'store' | 'event' | 'release'

export interface NewsArticle {
  id: string
  slug: string
  title: string
  excerpt: string
  content?: string
  category: Exclude<NewsCategory, 'all'>
  date: string
  image?: string
  featured?: boolean
}

export interface NewsFilter {
  category?: NewsCategory
  searchQuery?: string
}
