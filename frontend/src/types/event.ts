export type EventType = 'all' | 'concert' | 'meet-greet' | 'handshake' | 'online' | 'other'
export type EventStatus = 'upcoming' | 'ongoing' | 'completed' | 'cancelled'

export interface Event {
  id: string
  title: string
  type: Exclude<EventType, 'all'>
  status: EventStatus
  date: string
  endDate?: string
  venue?: string
  location?: string
  description?: string
  ticketUrl?: string
  image?: string
}

export interface EventFilter {
  type?: EventType
  status?: EventStatus | 'all'
}
