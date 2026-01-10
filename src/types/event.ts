export type EventType = 'all' | 'concert' | 'meet-greet' | 'handshake' | 'online' | 'other'
export type EventStatus = 'upcoming' | 'ongoing' | 'completed' | 'cancelled'

export interface EventLocation {
  venue: string
  city: string
  state?: string
  country: string
  address?: string
  coordinates?: {
    lat: number
    lng: number
  }
  onlineUrl?: string // For online events
}

export interface EventTicket {
  available: boolean
  price?: {
    min: number
    max: number
    currency: string
  }
  ticketUrl?: string
  soldOut?: boolean
  freeEvent?: boolean
}

export interface Event {
  id: string
  title: string
  slug: string
  description: string
  type: EventType
  status: EventStatus
  startDate: string // ISO date format
  endDate?: string // ISO date format (for multi-day events)
  startTime: string // HH:mm format
  endTime?: string // HH:mm format
  timeZone: string // e.g., "Asia/Kuala_Lumpur"
  location: EventLocation
  ticket: EventTicket
  image?: string
  coverImage?: string
  capacity?: number
  attendees?: number
  featured?: boolean
  members?: string[] // Member IDs participating
  tags?: string[]
  organizer?: string
  ageRestriction?: string // e.g., "All Ages", "18+", etc.
  notes?: string
}

export interface EventFilter {
  type: EventType
  status?: EventStatus
  month?: number // 0-11 (January is 0)
  year?: number
  searchQuery?: string
  sortBy?: 'date' | 'title' | 'type'
  sortOrder?: 'asc' | 'desc'
}

export interface EventStats {
  totalEvents: number
  upcomingEvents: number
  byType: {
    [key in Exclude<EventType, 'all'>]: number
  }
  byMonth: {
    [key: string]: number // "YYYY-MM": count
  }
}

export interface CalendarDay {
  date: Date
  day: number
  month: number
  year: number
  isCurrentMonth: boolean
  isToday: boolean
  events: Event[]
}

export interface CalendarMonth {
  month: number // 0-11
  year: number
  days: CalendarDay[]
  monthName: string
}
