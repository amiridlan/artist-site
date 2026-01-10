import { format, formatDistanceToNow, parseISO } from 'date-fns'

/**
 * Format date to readable string
 */
export const formatDate = (date: string | Date, formatStr: string = 'MMM dd, yyyy'): string => {
  try {
    const dateObj = typeof date === 'string' ? parseISO(date) : date
    return format(dateObj, formatStr)
  } catch (error) {
    console.error('Error formatting date:', error)
    return ''
  }
}

/**
 * Format date to relative time (e.g., "2 days ago")
 */
export const formatRelativeTime = (date: string | Date): string => {
  try {
    const dateObj = typeof date === 'string' ? parseISO(date) : date
    return formatDistanceToNow(dateObj, { addSuffix: true })
  } catch (error) {
    console.error('Error formatting relative time:', error)
    return ''
  }
}

/**
 * Format duration in seconds to MM:SS or HH:MM:SS
 */
export const formatDuration = (seconds: number): string => {
  const hours = Math.floor(seconds / 3600)
  const minutes = Math.floor((seconds % 3600) / 60)
  const secs = Math.floor(seconds % 60)

  if (hours > 0) {
    return `${hours}:${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`
  }
  return `${minutes}:${String(secs).padStart(2, '0')}`
}

/**
 * Truncate text to specified length with ellipsis
 */
export const truncateText = (text: string, maxLength: number): string => {
  if (text.length <= maxLength) return text
  return text.slice(0, maxLength).trim() + '...'
}

/**
 * Generate slug from title
 */
export const slugify = (text: string): string => {
  return text
    .toLowerCase()
    .replace(/[^\w\s-]/g, '')
    .replace(/\s+/g, '-')
    .replace(/--+/g, '-')
    .trim()
}

/**
 * Format number with commas (e.g., 1000 -> 1,000)
 */
export const formatNumber = (num: number): string => {
  return num.toLocaleString()
}

/**
 * Format number to compact notation (e.g., 1000 -> 1K)
 */
export const formatCompactNumber = (num: number): string => {
  if (num < 1000) return num.toString()
  if (num < 1000000) return `${(num / 1000).toFixed(1)}K`
  return `${(num / 1000000).toFixed(1)}M`
}

/**
 * Debounce function
 */
export const debounce = <T extends (...args: any[]) => any>(
  func: T,
  wait: number
): ((...args: Parameters<T>) => void) => {
  let timeout: ReturnType<typeof setTimeout> | null = null

  return (...args: Parameters<T>) => {
    if (timeout) clearTimeout(timeout)
    timeout = setTimeout(() => func(...args), wait)
  }
}

/**
 * Throttle function
 */
export const throttle = <T extends (...args: any[]) => any>(
  func: T,
  limit: number
): ((...args: Parameters<T>) => void) => {
  let inThrottle: boolean = false

  return (...args: Parameters<T>) => {
    if (!inThrottle) {
      func(...args)
      inThrottle = true
      setTimeout(() => (inThrottle = false), limit)
    }
  }
}

/**
 * Check if element is in viewport
 */
export const isInViewport = (element: HTMLElement): boolean => {
  const rect = element.getBoundingClientRect()
  return (
    rect.top >= 0 &&
    rect.left >= 0 &&
    rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
    rect.right <= (window.innerWidth || document.documentElement.clientWidth)
  )
}

/**
 * Smooth scroll to element
 */
export const scrollToElement = (
  elementId: string,
  offset: number = 0,
  behavior: ScrollBehavior = 'smooth'
): void => {
  const element = document.getElementById(elementId)
  if (!element) return

  const elementPosition = element.getBoundingClientRect().top + window.pageYOffset
  const offsetPosition = elementPosition - offset

  window.scrollTo({
    top: offsetPosition,
    behavior,
  })
}

/**
 * Copy text to clipboard
 */
export const copyToClipboard = async (text: string): Promise<boolean> => {
  try {
    await navigator.clipboard.writeText(text)
    return true
  } catch (error) {
    console.error('Failed to copy to clipboard:', error)
    return false
  }
}

/**
 * Get image URL with CDN support
 */
export const getImageUrl = (path: string): string => {
  const cdnUrl = import.meta.env.VITE_CDN_URL
  if (cdnUrl && !path.startsWith('http')) {
    return `${cdnUrl}${path.startsWith('/') ? '' : '/'}${path}`
  }
  return path
}

/**
 * Lazy load image
 */
export const lazyLoadImage = (img: HTMLImageElement, src: string): void => {
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          img.src = src
          img.classList.add('loaded')
          observer.unobserve(img)
        }
      })
    },
    { rootMargin: '50px' }
  )

  observer.observe(img)
}

/**
 * Check if user prefers reduced motion
 */
export const prefersReducedMotion = (): boolean => {
  return window.matchMedia('(prefers-reduced-motion: reduce)').matches
}

/**
 * Generate random ID
 */
export const generateId = (): string => {
  return `${Date.now()}-${Math.random().toString(36).substr(2, 9)}`
}

/**
 * Format date for iCalendar format (YYYYMMDDTHHMMSSZ)
 */
const formatICalDate = (date: Date): string => {
  const year = date.getUTCFullYear()
  const month = String(date.getUTCMonth() + 1).padStart(2, '0')
  const day = String(date.getUTCDate()).padStart(2, '0')
  const hours = String(date.getUTCHours()).padStart(2, '0')
  const minutes = String(date.getUTCMinutes()).padStart(2, '0')
  const seconds = String(date.getUTCSeconds()).padStart(2, '0')
  return `${year}${month}${day}T${hours}${minutes}${seconds}Z`
}

/**
 * Generate .ics file content for calendar export
 */
export const generateICalendar = (event: {
  title: string
  description?: string
  location?: string
  startDate: string
  endDate?: string
  startTime?: string
  endTime?: string
  url?: string
}): string => {
  const now = new Date()
  const startDate = new Date(event.startDate)

  // Parse time if provided
  if (event.startTime) {
    const [hours, minutes] = event.startTime.split(':')
    startDate.setHours(parseInt(hours), parseInt(minutes))
  }

  let endDate = event.endDate ? new Date(event.endDate) : new Date(startDate)
  if (event.endTime) {
    const [hours, minutes] = event.endTime.split(':')
    endDate.setHours(parseInt(hours), parseInt(minutes))
  } else if (!event.endDate) {
    // Default to 2 hours if no end time specified
    endDate.setHours(endDate.getHours() + 2)
  }

  const icsContent = [
    'BEGIN:VCALENDAR',
    'VERSION:2.0',
    'PRODID:-//KLP48//Event Calendar//EN',
    'CALSCALE:GREGORIAN',
    'METHOD:PUBLISH',
    'BEGIN:VEVENT',
    `UID:${generateId()}@klp48.com`,
    `DTSTAMP:${formatICalDate(now)}`,
    `DTSTART:${formatICalDate(startDate)}`,
    `DTEND:${formatICalDate(endDate)}`,
    `SUMMARY:${event.title}`,
    event.description ? `DESCRIPTION:${event.description.replace(/\n/g, '\\n')}` : '',
    event.location ? `LOCATION:${event.location}` : '',
    event.url ? `URL:${event.url}` : '',
    'STATUS:CONFIRMED',
    'END:VEVENT',
    'END:VCALENDAR'
  ]
    .filter(line => line !== '') // Remove empty lines
    .join('\r\n')

  return icsContent
}

/**
 * Download .ics file
 */
export const downloadICalendar = (
  event: {
    title: string
    description?: string
    location?: string
    startDate: string
    endDate?: string
    startTime?: string
    endTime?: string
    url?: string
  },
  filename?: string
): void => {
  const icsContent = generateICalendar(event)
  const blob = new Blob([icsContent], { type: 'text/calendar;charset=utf-8' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = filename || `${slugify(event.title)}.ics`
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  URL.revokeObjectURL(link.href)
}