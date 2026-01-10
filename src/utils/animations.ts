import gsap from 'gsap'
import { prefersReducedMotion } from './helpers'

/**
 * Animation configuration defaults
 */
export const ANIMATION_DEFAULTS = {
  duration: 0.6,
  ease: 'power2.out',
  stagger: 0.1
}

/**
 * Fade in animation
 */
export const fadeIn = (
  element: string | HTMLElement,
  options: gsap.TweenVars = {}
): gsap.core.Tween | null => {
  if (prefersReducedMotion()) return null

  return gsap.from(element, {
    opacity: 0,
    duration: ANIMATION_DEFAULTS.duration,
    ease: ANIMATION_DEFAULTS.ease,
    ...options
  })
}

/**
 * Fade out animation
 */
export const fadeOut = (
  element: string | HTMLElement,
  options: gsap.TweenVars = {}
): gsap.core.Tween | null => {
  if (prefersReducedMotion()) return null

  return gsap.to(element, {
    opacity: 0,
    duration: ANIMATION_DEFAULTS.duration,
    ease: ANIMATION_DEFAULTS.ease,
    ...options
  })
}

/**
 * Slide in from direction
 */
export const slideIn = (
  element: string | HTMLElement,
  direction: 'left' | 'right' | 'up' | 'down' = 'up',
  options: gsap.TweenVars = {}
): gsap.core.Tween | null => {
  if (prefersReducedMotion()) return null

  const directionMap = {
    left: { x: -100 },
    right: { x: 100 },
    up: { y: 100 },
    down: { y: -100 }
  }

  return gsap.from(element, {
    ...directionMap[direction],
    opacity: 0,
    duration: ANIMATION_DEFAULTS.duration,
    ease: ANIMATION_DEFAULTS.ease,
    ...options
  })
}

/**
 * Scale in animation
 */
export const scaleIn = (
  element: string | HTMLElement,
  options: gsap.TweenVars = {}
): gsap.core.Tween | null => {
  if (prefersReducedMotion()) return null

  return gsap.from(element, {
    scale: 0.8,
    opacity: 0,
    duration: ANIMATION_DEFAULTS.duration,
    ease: ANIMATION_DEFAULTS.ease,
    ...options
  })
}

/**
 * Stagger animation for multiple elements
 */
export const staggerIn = (
  elements: string,
  options: gsap.TweenVars = {}
): gsap.core.Tween | null => {
  if (prefersReducedMotion()) return null

  return gsap.from(elements, {
    opacity: 0,
    y: 50,
    duration: ANIMATION_DEFAULTS.duration,
    ease: ANIMATION_DEFAULTS.ease,
    stagger: ANIMATION_DEFAULTS.stagger,
    ...options
  })
}

/**
 * Rotate in animation
 */
export const rotateIn = (
  element: string | HTMLElement,
  options: gsap.TweenVars = {}
): gsap.core.Tween | null => {
  if (prefersReducedMotion()) return null

  return gsap.from(element, {
    rotation: -180,
    opacity: 0,
    duration: ANIMATION_DEFAULTS.duration,
    ease: ANIMATION_DEFAULTS.ease,
    ...options
  })
}

/**
 * Bounce in animation
 */
export const bounceIn = (
  element: string | HTMLElement,
  options: gsap.TweenVars = {}
): gsap.core.Tween | null => {
  if (prefersReducedMotion()) return null

  return gsap.from(element, {
    scale: 0,
    opacity: 0,
    duration: ANIMATION_DEFAULTS.duration,
    ease: 'back.out(1.7)',
    ...options
  })
}

/**
 * Float animation (continuous)
 */
export const float = (
  element: string | HTMLElement,
  options: gsap.TweenVars = {}
): gsap.core.Tween | null => {
  if (prefersReducedMotion()) return null

  return gsap.to(element, {
    y: -20,
    duration: 2,
    ease: 'power1.inOut',
    repeat: -1,
    yoyo: true,
    ...options
  })
}

/**
 * Pulse animation (continuous)
 */
export const pulse = (
  element: string | HTMLElement,
  options: gsap.TweenVars = {}
): gsap.core.Tween | null => {
  if (prefersReducedMotion()) return null

  return gsap.to(element, {
    scale: 1.05,
    duration: 1,
    ease: 'power1.inOut',
    repeat: -1,
    yoyo: true,
    ...options
  })
}

/**
 * Shimmer effect (loading animation)
 */
export const shimmer = (
  element: string | HTMLElement,
  options: gsap.TweenVars = {}
): gsap.core.Tween | null => {
  if (prefersReducedMotion()) return null

  return gsap.to(element, {
    backgroundPosition: '200% center',
    duration: 1.5,
    ease: 'none',
    repeat: -1,
    ...options
  })
}

/**
 * Timeline for complex animations
 */
export const createTimeline = (options: gsap.TimelineVars = {}): gsap.core.Timeline | null => {
  if (prefersReducedMotion()) return null

  return gsap.timeline(options)
}

/**
 * Kill all GSAP animations
 */
export const killAllAnimations = (): void => {
  gsap.killTweensOf('*')
}

/**
 * Set GSAP default options
 */
export const setAnimationDefaults = (defaults: gsap.TweenVars): void => {
  gsap.defaults(defaults)
}

/**
 * Quick animation utility
 */
export const animate = (
  element: string | HTMLElement,
  to: gsap.TweenVars,
  from?: gsap.TweenVars
): gsap.core.Tween | null => {
  if (prefersReducedMotion()) return null

  if (from) {
    return gsap.fromTo(element, from, to)
  }
  return gsap.to(element, to)
}
