export type VideoType = 'all' | 'music-video' | 'performance' | 'behind-the-scenes' | 'interview' | 'variety'
export type VideoQuality = '1080p' | '720p' | '480p' | '360p'

export interface Video {
  id: string
  title: string
  slug: string
  description: string
  thumbnail: string
  videoUrl?: string // YouTube URL or video file URL
  youtubeId?: string // For YouTube embeds
  duration: number // in seconds
  type: VideoType
  releaseDate: string // ISO date format
  views: number
  likes?: number
  featured?: boolean
  members?: string[] // Member IDs featured in video
  tags?: string[]
  availableQualities?: VideoQuality[]
}

export interface VideoFilter {
  type: VideoType
  searchQuery?: string
  sortBy?: 'date' | 'views' | 'title' | 'duration'
  sortOrder?: 'asc' | 'desc'
}

export interface VideoStats {
  totalVideos: number
  totalViews: number
  byType: {
    [key in Exclude<VideoType, 'all'>]: number
  }
}