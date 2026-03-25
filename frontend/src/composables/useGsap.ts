import { onMounted, onUnmounted, ref, type Ref } from 'vue'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

export function useScrollReveal(
  elementRef: Ref<HTMLElement | null>,
  options: {
    y?: number
    x?: number
    opacity?: number
    duration?: number
    delay?: number
    stagger?: number
    ease?: string
    once?: boolean
  } = {}
) {
  const {
    y = 40,
    x = 0,
    opacity = 0,
    duration = 0.8,
    delay = 0,
    ease = 'power2.out',
    once = true,
  } = options

  onMounted(() => {
    if (!elementRef.value) return

    gsap.from(elementRef.value, {
      y,
      x,
      opacity,
      duration,
      delay,
      ease,
      scrollTrigger: {
        trigger: elementRef.value,
        start: 'top 85%',
        toggleActions: once ? 'play none none none' : 'play none none reverse',
      },
    })
  })

  onUnmounted(() => {
    ScrollTrigger.getAll().forEach(st => {
      if (st.trigger === elementRef.value) st.kill()
    })
  })
}

export function useStaggerReveal(
  containerRef: Ref<HTMLElement | null>,
  childSelector: string,
  options: {
    y?: number
    opacity?: number
    duration?: number
    stagger?: number
    ease?: string
  } = {}
) {
  const {
    y = 30,
    opacity = 0,
    duration = 0.6,
    stagger = 0.1,
    ease = 'power2.out',
  } = options

  onMounted(() => {
    if (!containerRef.value) return

    const children = containerRef.value.querySelectorAll(childSelector)
    if (!children.length) return

    gsap.from(children, {
      y,
      opacity,
      duration,
      stagger,
      ease,
      scrollTrigger: {
        trigger: containerRef.value,
        start: 'top 85%',
      },
    })
  })

  onUnmounted(() => {
    ScrollTrigger.getAll().forEach(st => {
      if (st.trigger === containerRef.value) st.kill()
    })
  })
}

export function useParallax(
  elementRef: Ref<HTMLElement | null>,
  speed: number = 0.3
) {
  onMounted(() => {
    if (!elementRef.value) return

    gsap.to(elementRef.value, {
      yPercent: -speed * 100,
      ease: 'none',
      scrollTrigger: {
        trigger: elementRef.value,
        start: 'top bottom',
        end: 'bottom top',
        scrub: true,
      },
    })
  })

  onUnmounted(() => {
    ScrollTrigger.getAll().forEach(st => {
      if (st.trigger === elementRef.value) st.kill()
    })
  })
}

export function useMemberCardTilt(cardRef: Ref<HTMLElement | null>) {
  const isHovering = ref(false)

  const handleMouseMove = (e: MouseEvent) => {
    if (!cardRef.value) return
    const rect = cardRef.value.getBoundingClientRect()
    const x = (e.clientX - rect.left) / rect.width - 0.5
    const y = (e.clientY - rect.top) / rect.height - 0.5

    gsap.to(cardRef.value, {
      rotateY: x * 10,
      rotateX: -y * 10,
      duration: 0.3,
      ease: 'power2.out',
      transformPerspective: 800,
    })
  }

  const handleMouseLeave = () => {
    if (!cardRef.value) return
    isHovering.value = false
    gsap.to(cardRef.value, {
      rotateY: 0,
      rotateX: 0,
      duration: 0.5,
      ease: 'power2.out',
    })
  }

  const handleMouseEnter = () => {
    isHovering.value = true
  }

  onMounted(() => {
    if (!cardRef.value) return
    cardRef.value.addEventListener('mousemove', handleMouseMove)
    cardRef.value.addEventListener('mouseleave', handleMouseLeave)
    cardRef.value.addEventListener('mouseenter', handleMouseEnter)
  })

  onUnmounted(() => {
    if (!cardRef.value) return
    cardRef.value.removeEventListener('mousemove', handleMouseMove)
    cardRef.value.removeEventListener('mouseleave', handleMouseLeave)
    cardRef.value.removeEventListener('mouseenter', handleMouseEnter)
  })

  return { isHovering }
}

export function useTextReveal(elementRef: Ref<HTMLElement | null>) {
  onMounted(() => {
    if (!elementRef.value) return

    gsap.from(elementRef.value, {
      clipPath: 'inset(0 100% 0 0)',
      duration: 1,
      ease: 'power4.inOut',
      scrollTrigger: {
        trigger: elementRef.value,
        start: 'top 85%',
      },
    })
  })
}
