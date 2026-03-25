import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'

const routeOrder: Record<string, number> = {
  '/': 0,
  '/news': 1,
  '/members': 2,
  '/releases': 3,
  '/videos': 4,
  '/about': 5,
  '/schedule': 6,
  '/fanclub': 7,
}

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    name: 'Home',
    component: () => import('@/pages/Home.vue'),
    meta: { title: 'KLP48 - Malaysia\'s Official AKB48 Sister Group' },
  },
  {
    path: '/news',
    name: 'News',
    component: () => import('@/pages/News.vue'),
    meta: { title: 'News - KLP48' },
  },
  {
    path: '/news/:slug',
    name: 'NewsDetail',
    component: () => import('@/pages/NewsDetail.vue'),
    meta: { title: 'News - KLP48' },
  },
  {
    path: '/members',
    name: 'Members',
    component: () => import('@/pages/Members.vue'),
    meta: { title: 'Members - KLP48' },
  },
  {
    path: '/members/:id',
    name: 'MemberDetail',
    component: () => import('@/pages/MemberDetail.vue'),
    meta: { title: 'Member Profile - KLP48' },
  },
  {
    path: '/releases',
    name: 'Releases',
    component: () => import('@/pages/Releases.vue'),
    meta: { title: 'Discography - KLP48' },
  },
  {
    path: '/videos',
    name: 'Videos',
    component: () => import('@/pages/Videos.vue'),
    meta: { title: 'Movie - KLP48' },
  },
  {
    path: '/about',
    name: 'About',
    component: () => import('@/pages/About.vue'),
    meta: { title: 'About - KLP48' },
  },
  {
    path: '/schedule',
    name: 'Schedule',
    component: () => import('@/pages/Schedule.vue'),
    meta: { title: 'Schedule - KLP48' },
  },
  {
    path: '/fanclub',
    name: 'FanClub',
    component: () => import('@/pages/FanClub.vue'),
    meta: { title: 'Fan Club - KLP48' },
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('@/pages/NotFound.vue'),
    meta: { title: '404 - Page Not Found' },
  },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
  scrollBehavior(_, __, savedPosition) {
    if (savedPosition) return savedPosition
    return { top: 0, behavior: 'smooth' }
  },
})

const getBasePath = (path: string): string => {
  const match = path.match(/^\/[^/]+/)
  return match ? match[0] : path
}

router.beforeEach((to, from, next) => {
  const title = to.meta?.title as string | undefined
  if (title) document.title = title

  const fromBase = getBasePath(from.path)
  const toBase = getBasePath(to.path)
  const fromIdx = routeOrder[fromBase] ?? -1
  const toIdx = routeOrder[toBase] ?? -1

  if (fromIdx === -1 || toIdx === -1) to.meta.transition = 'fade'
  else if (toIdx > fromIdx) to.meta.transition = 'slide-left'
  else if (toIdx < fromIdx) to.meta.transition = 'slide-right'
  else to.meta.transition = 'fade'

  next()
})

export default router
