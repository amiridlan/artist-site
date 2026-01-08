export type NewsCategory = 'all' | 'news' | 'fanclub' | 'store' | 'event' | 'release'

export interface NewsArticle {
  id: string
  title: string
  slug: string
  excerpt: string
  content: string
  category: NewsCategory
  image: string
  thumbnail?: string
  author?: string
  publishedAt: string
  updatedAt?: string
  featured: boolean
  tags?: string[]
  readTime?: number // in minutes
  views?: number
}

export interface NewsFilter {
  category: NewsCategory
  searchQuery?: string
  sortBy?: 'date' | 'views' | 'title'
  sortOrder?: 'asc' | 'desc'
}
