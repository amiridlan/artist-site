export type ReleaseType = 'all' | 'single' | 'album' | 'ep' | 'digital-single'

export interface ReleaseTrack {
  number: number
  title: string
  duration?: string
}

export interface Release {
  id: string
  title: string
  type: Exclude<ReleaseType, 'all'>
  releaseDate: string
  coverImage?: string
  tracks: ReleaseTrack[]
  description?: string
  streamingLinks?: {
    spotify?: string
    appleMusic?: string
    youtubeMusic?: string
  }
}

export interface ReleaseFilter {
  type?: ReleaseType
}
