export type MemberStatus = 'active' | 'graduated' | 'on-hiatus'
export type MemberTeam = 'Team K' | 'Team L' | 'Team P' | 'Trainee'
export type MemberGeneration = '1st' | '2nd' | '3rd' | '4th' | '5th'
export type MemberPosition = 'Center' | 'Captain' | 'Vice Captain' | 'Regular'

export interface MemberSocialLinks {
  twitter?: string
  instagram?: string
  tiktok?: string
  youtube?: string
}

export interface Member {
  id: string
  name: {
    english: string
    native?: string // Name in local language (Chinese, Malay, Tamil, etc.)
  }
  nickname?: string
  photo: string
  coverImage?: string
  team: MemberTeam
  generation: MemberGeneration
  position: MemberPosition
  birthdate: string // ISO date format
  age: number
  height?: number // in cm
  bloodType?: string
  hometown: string
  hobbies?: string[]
  specialties?: string[]
  bio: string
  joinDate: string // ISO date format
  status: MemberStatus
  color?: string // Member's official color (hex)
  social?: MemberSocialLinks
  stats?: {
    centerPositions?: number // Number of times as center
    performances?: number
    fanMeetings?: number
  }
  featured?: boolean
}

export interface MemberFilter {
  team?: MemberTeam | 'all'
  generation?: MemberGeneration | 'all'
  status?: MemberStatus | 'all'
  searchQuery?: string
  sortBy?: 'name' | 'generation' | 'birthday' | 'joinDate'
  sortOrder?: 'asc' | 'desc'
}

export interface MemberStats {
  totalMembers: number
  activeMembers: number
  graduatedMembers: number
  byTeam: {
    [key in MemberTeam]: number
  }
  byGeneration: {
    [key in MemberGeneration]: number
  }
}
