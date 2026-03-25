export type MemberStatus = 'active' | 'graduated' | 'concluded'
export type MemberGeneration = '1st' | '2nd'

export interface Member {
  id: string
  name: {
    english: string
    native?: string
  }
  nickname?: string
  photo: string
  coverImage?: string
  generation: MemberGeneration
  birthdate?: string
  age?: number
  height?: number
  bloodType?: string
  hometown?: string
  hobbies?: string[]
  bio?: string
  joinDate?: string
  status: MemberStatus
  color?: string
  social?: {
    twitter?: string
    instagram?: string
    tiktok?: string
  }
}

export interface MemberFilter {
  generation?: MemberGeneration | 'all'
  status?: MemberStatus | 'all'
  searchQuery?: string
}
