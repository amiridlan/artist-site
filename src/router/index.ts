import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    name: 'Home',
    component: () => import('@/pages/Home.vue'),
    meta: {
      title: 'KLP48 - Malaysia\'s Premier Idol Group',
      description: 'Official website for KLP48 idol group',
      transition: 'fade'
    }
  },
  {
    path: '/news',
    name: 'News',
    component: () => import('@/pages/News.vue'),
    meta: {
      title: 'News - KLP48',
      description: 'Latest news and updates from KLP48',
      transition: 'slide-left'
    }
  },
  {
    path: '/news/:slug',
    name: 'NewsDetail',
    component: () => import('@/pages/NewsDetail.vue'),
    meta: {
      title: 'News Article - KLP48',
      description: 'Read the latest news from KLP48',
      transition: 'slide-left'
    }
  },
  {
    path: '/members',
    name: 'Members',
    component: () => import('@/pages/Members.vue'),
    meta: {
      title: 'Members - KLP48',
      description: 'Meet the KLP48 members',
      transition: 'zoom'
    }
  },
  {
    path: '/members/:id',
    name: 'MemberDetail',
    component: () => import('@/pages/MemberDetail.vue'),
    meta: {
      title: 'Member Profile - KLP48',
      description: 'View KLP48 member profile',
      transition: 'zoom'
    }
  },
  {
    path: '/videos',
    name: 'Videos',
    component: () => import('@/pages/Videos.vue'),
    meta: {
      title: 'Videos - KLP48',
      description: 'Watch KLP48 music videos and performances',
      transition: 'fade'
    }
  },
  {
    path: '/releases',
    name: 'Releases',
    component: () => import('@/pages/Releases.vue'),
    meta: {
      title: 'Releases - KLP48',
      description: 'KLP48 discography and releases',
      transition: 'fade'
    }
  },
  {
    path: '/schedule',
    name: 'Schedule',
    component: () => import('@/pages/Schedule.vue'),
    meta: {
      title: 'Schedule - KLP48',
      description: 'Upcoming events and schedule',
      transition: 'slide-right'
    }
  },
  {
    path: '/fanclub',
    name: 'FanClub',
    component: () => import('@/pages/FanClub.vue'),
    meta: {
      title: 'Fan Club - KLP48',
      description: 'Join the KLP48 official fan club',
      transition: 'zoom'
    }
  },
  {
    path: '/about',
    name: 'About',
    component: () => import('@/pages/About.vue'),
    meta: {
      title: 'About - KLP48',
      description: 'Learn more about KLP48',
      transition: 'fade'
    }
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('@/pages/NotFound.vue'),
    meta: {
      title: '404 - Page Not Found',
      description: 'The page you are looking for does not exist',
      transition: 'fade'
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

// Navigation guards for meta tags
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
  
  next()
})

export default router