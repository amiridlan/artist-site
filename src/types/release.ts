export type ReleaseType = 'all' | 'single' | 'album' | 'ep' | 'digital-single'
export type ReleaseFormat = 'cd' | 'digital' | 'vinyl' | 'cassette'

export interface StreamingLink {
  platform: 'spotify' | 'apple-music' | 'youtube-music' | 'amazon-music'
  url: string
}

export interface ReleaseEdition {
  name: string // Regular, Limited, Theater, etc.
  price: number // in MYR
  items: string[] // What's included
  inStock: boolean
}

export interface Release {
  id: string
  title: string
  slug: string
  type: ReleaseType
  coverImage: string
  description: string
  releaseDate: string // ISO date format
  preOrderDate?: string // ISO date format
  tracklist: {
    number: number
    title: string
    duration: number // in seconds
    featured?: boolean // Is this the title track?
  }[]
  editions?: ReleaseEdition[]
  streamingLinks?: StreamingLink[]
  formats: ReleaseFormat[]
  featured?: boolean
  centerMember?: string // Member ID for center position
  tags?: string[]
  sales?: number
}

export interface ReleaseFilter {
  type: ReleaseType
  searchQuery?: string
  sortBy?: 'date' | 'title' | 'sales'
  sortOrder?: 'asc' | 'desc'
  showUpcoming?: boolean
}

export interface ReleaseStats {
  totalReleases: number
  totalSales: number
  byType: {
    [key in Exclude<ReleaseType, 'all'>]: number
  }
}