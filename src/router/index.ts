import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'

// Define route order for directional navigation
const routeOrder: Record<string, number> = {
  '/': 0,
  '/news': 1,
  '/members': 2,
  '/releases': 3,
  '/videos': 4,
  '/schedule': 5,
  '/fanclub': 6,
  '/about': 7
}

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    name: 'Home',
    component: () => import('@/pages/Home.vue'),
    meta: {
      title: 'KLP48 - Malaysia\'s Premier Idol Group',
      description: 'Official website for KLP48 idol group'
    }
  },
  {
    path: '/news',
    name: 'News',
    component: () => import('@/pages/News.vue'),
    meta: {
      title: 'News - KLP48',
      description: 'Latest news and updates from KLP48'
    }
  },
  {
    path: '/news/:slug',
    name: 'NewsDetail',
    component: () => import('@/pages/NewsDetail.vue'),
    meta: {
      title: 'News Article - KLP48',
      description: 'Read the latest news from KLP48'
    }
  },
  {
    path: '/members',
    name: 'Members',
    component: () => import('@/pages/Members.vue'),
    meta: {
      title: 'Members - KLP48',
      description: 'Meet the KLP48 members'
    }
  },
  {
    path: '/members/:id',
    name: 'MemberDetail',
    component: () => import('@/pages/MemberDetail.vue'),
    meta: {
      title: 'Member Profile - KLP48',
      description: 'View KLP48 member profile'
    }
  },
  {
    path: '/videos',
    name: 'Videos',
    component: () => import('@/pages/Videos.vue'),
    meta: {
      title: 'Videos - KLP48',
      description: 'Watch KLP48 music videos and performances'
    }
  },
  {
    path: '/releases',
    name: 'Releases',
    component: () => import('@/pages/Releases.vue'),
    meta: {
      title: 'Releases - KLP48',
      description: 'KLP48 discography and releases'
    }
  },
  {
    path: '/schedule',
    name: 'Schedule',
    component: () => import('@/pages/Schedule.vue'),
    meta: {
      title: 'Schedule - KLP48',
      description: 'Upcoming events and schedule'
    }
  },
  {
    path: '/fanclub',
    name: 'FanClub',
    component: () => import('@/pages/FanClub.vue'),
    meta: {
      title: 'Fan Club - KLP48',
      description: 'Join the KLP48 official fan club'
    }
  },
  {
    path: '/about',
    name: 'About',
    component: () => import('@/pages/About.vue'),
    meta: {
      title: 'About - KLP48',
      description: 'Learn more about KLP48'
    }
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('@/pages/NotFound.vue'),
    meta: {
      title: '404 - Page Not Found',
      description: 'The page you are looking for does not exist'
    }
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else if (to.hash) {
      return {
        el: to.hash,
        behavior: 'smooth',
      }
    } else {
      return { top: 0, behavior: 'smooth' }
    }
  }
})

// Helper function to get base path (without params)
const getBasePath = (path: string): string => {
  // Remove detail page params (e.g., /news/slug -> /news, /members/123 -> /members)
  const match = path.match(/^\/[^\/]+/)
  return match ? match[0] : path
}

// Navigation guards for meta tags and transition direction
router.beforeEach((to, from, next) => {
  // Update document title
  const nearestWithTitle = to.matched.slice().reverse().find(r => r.meta && r.meta.title)
  if (nearestWithTitle) {
    document.title = nearestWithTitle.meta.title as string
  }

  // Update meta description
  const nearestWithMeta = to.matched.slice().reverse().find(r => r.meta && r.meta.description)
  if (nearestWithMeta) {
    const metaDescription = document.querySelector('meta[name="description"]')
    if (metaDescription) {
      metaDescription.setAttribute('content', nearestWithMeta.meta.description as string)
    }
  }

  // Determine transition direction based on route order
  const fromBasePath = getBasePath(from.path)
  const toBasePath = getBasePath(to.path)

  const fromIndex = routeOrder[fromBasePath] ?? -1
  const toIndex = routeOrder[toBasePath] ?? -1

  // Set transition direction
  if (fromIndex === -1 || toIndex === -1) {
    // If route not in order map, use fade
    to.meta.transition = 'fade'
  } else if (toIndex > fromIndex) {
    // Moving forward (left to right)
    to.meta.transition = 'slide-left'
  } else if (toIndex < fromIndex) {
    // Moving backward (right to left)
    to.meta.transition = 'slide-right'
  } else {
    // Same route (e.g., detail pages), use fade
    to.meta.transition = 'fade'
  }

  next()
})

export default router