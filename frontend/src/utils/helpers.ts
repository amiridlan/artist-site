import { format, formatDistanceToNow, parseISO } from 'date-fns'

export function formatDate(dateString: string, pattern: string = 'MMM d, yyyy'): string {
  return format(parseISO(dateString), pattern)
}

export function formatRelativeTime(dateString: string): string {
  return formatDistanceToNow(parseISO(dateString), { addSuffix: true })
}

export function truncate(text: string, length: number = 100): string {
  if (text.length <= length) return text
  return text.slice(0, length).trimEnd() + '...'
}

export function slugify(text: string): string {
  return text
    .toLowerCase()
    .replace(/[^\w\s-]/g, '')
    .replace(/[\s_]+/g, '-')
    .replace(/^-+|-+$/g, '')
}

export function getEventStatusColor(status: string): string {
  switch (status) {
    case 'upcoming': return 'jade'
    case 'ongoing': return 'gold'
    case 'completed': return 'charcoal'
    case 'cancelled': return 'sakura'
    default: return 'charcoal'
  }
}

export function getCategoryColor(category: string): string {
  switch (category) {
    case 'news': return 'bg-jade-100 text-jade-700'
    case 'fanclub': return 'bg-sakura-100 text-sakura-700'
    case 'store': return 'bg-gold-100 text-gold-700'
    case 'event': return 'bg-jade-100 text-jade-700'
    case 'release': return 'bg-charcoal-100 text-charcoal-700'
    default: return 'bg-cream-200 text-charcoal-600'
  }
}
