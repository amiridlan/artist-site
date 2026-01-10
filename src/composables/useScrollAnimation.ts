import { onMounted, onUnmounted, type Ref } from 'vue'
import gsap from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import { prefersReducedMotion } from '@/utils/helpers'

// Register GSAP plugins
gsap.registerPlugin(ScrollTrigger)

export interface ScrollAnimationOptions {
  trigger?: string | HTMLElement
  start?: string
  end?: string
  scrub?: boolean | number
  markers?: boolean
  toggleActions?: string
  once?: boolean
}

/**
 * Composable for scroll-triggered animations using GSAP ScrollTrigger
 */
export const useScrollAnimation = (
  target: Ref<HTMLElement | null> | string,
  animation: gsap.TweenVars,
  options: ScrollAnimationOptions = {}
) => {
  let scrollTriggerInstance: ScrollTrigger | null = null

  const setupAnimation = () => {
    // Skip animations if user prefers reduced motion
    if (prefersReducedMotion()) {
      return
    }

    const element = typeof target === 'string' ? target : target.value
    if (!element) return

    const defaultOptions: ScrollAnimationOptions = {
      start: 'top 80%',
      end: 'top 20%',
      toggleActions: 'play none none reverse',
      once: false,
      ...options
    }

    // Create the animation with ScrollTrigger
    const tween = gsap.from(element, {
      ...animation,
      scrollTrigger: {
        trigger: options.trigger || element,
        start: defaultOptions.start,
        end: defaultOptions.end,
        scrub: defaultOptions.scrub,
        markers: defaultOptions.markers,
        toggleActions: defaultOptions.toggleActions,
        once: defaultOptions.once,
      }
    })

    scrollTriggerInstance = tween.scrollTrigger || null
  }

  onMounted(() => {
    setupAnimation()
  })

  onUnmounted(() => {
    if (scrollTriggerInstance) {
      scrollTriggerInstance.kill()
    }
  })

  return {
    refresh: () => ScrollTrigger.refresh(),
    kill: () => scrollTriggerInstance?.kill()
  }
}

/**
 * Composable for fade-in animation on scroll
 */
export const useFadeIn = (
  target: Ref<HTMLElement | null> | string,
  options: ScrollAnimationOptions = {}
) => {
  return useScrollAnimation(
    target,
    {
      opacity: 0,
      y: 50,
      duration: 0.8,
      ease: 'power2.out'
    },
    options
  )
}

/**
 * Composable for slide-in animation on scroll
 */
export const useSlideIn = (
  target: Ref<HTMLElement | null> | string,
  direction: 'left' | 'right' | 'up' | 'down' = 'up',
  options: ScrollAnimationOptions = {}
) => {
  const directionMap = {
    left: { x: -100, y: 0 },
    right: { x: 100, y: 0 },
    up: { x: 0, y: 100 },
    down: { x: 0, y: -100 }
  }

  return useScrollAnimation(
    target,
    {
      opacity: 0,
      ...directionMap[direction],
      duration: 0.8,
      ease: 'power2.out'
    },
    options
  )
}

/**
 * Composable for stagger animation on scroll
 */
export const useStaggerAnimation = (
  target: string,
  animation: gsap.TweenVars = {},
  options: ScrollAnimationOptions = {}
) => {
  let scrollTriggerInstance: ScrollTrigger | null = null

  const setupAnimation = () => {
    if (prefersReducedMotion()) return

    const defaultAnimation = {
      opacity: 0,
      y: 50,
      duration: 0.6,
      ease: 'power2.out',
      stagger: 0.1,
      ...animation
    }

    const tween = gsap.from(target, {
      ...defaultAnimation,
      scrollTrigger: {
        trigger: target,
        start: options.start || 'top 80%',
        toggleActions: options.toggleActions || 'play none none reverse',
        once: options.once || false,
        markers: options.markers
      }
    })

    scrollTriggerInstance = tween.scrollTrigger || null
  }

  onMounted(() => {
    setupAnimation()
  })

  onUnmounted(() => {
    if (scrollTriggerInstance) {
      scrollTriggerInstance.kill()
    }
  })

  return {
    refresh: () => ScrollTrigger.refresh()
  }
}

/**
 * Composable for parallax effect
 */
export const useParallax = (
  target: Ref<HTMLElement | null> | string,
  speed: number = 0.5,
  options: ScrollAnimationOptions = {}
) => {
  return useScrollAnimation(
    target,
    {
      y: 0,
      ease: 'none'
    },
    {
      ...options,
      scrub: true,
      start: 'top bottom',
      end: 'bottom top',
    }
  )
}

/**
 * Composable for scale animation on scroll
 */
export const useScaleIn = (
  target: Ref<HTMLElement | null> | string,
  options: ScrollAnimationOptions = {}
) => {
  return useScrollAnimation(
    target,
    {
      opacity: 0,
      scale: 0.8,
      duration: 0.8,
      ease: 'power2.out'
    },
    options
  )
}
