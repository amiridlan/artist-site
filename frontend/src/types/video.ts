export type VideoType = 'all' | 'music-video' | 'performance' | 'dance-practice' | 'behind-the-scenes'

export interface Video {
  id: string
  title: string
  type: Exclude<VideoType, 'all'>
  youtubeId?: string
  thumbnail?: string
  date: string
  duration?: string
  description?: string
  venue?: string
}

export interface VideoFilter {
  type?: VideoType
}
