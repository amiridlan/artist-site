import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'
import { useFanStore } from '@/stores/fan'

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
    component: () => import('@/pages/Fanclub.vue'),
    meta: { title: 'Fan Club - KLP48' },
  },
  // Fanclub auth & portal routes
  {
    path: '/fanclub/login',
    name: 'FanclubLogin',
    component: () => import('@/pages/FanclubLogin.vue'),
    meta: { title: 'Fan Club Login - KLP48', fanGuest: true },
  },
  {
    path: '/fanclub/register',
    name: 'FanclubRegister',
    component: () => import('@/pages/FanclubRegister.vue'),
    meta: { title: 'Join Fan Club - KLP48' },
  },
  {
    path: '/fanclub/subscribe',
    name: 'FanclubSubscribe',
    component: () => import('@/pages/FanclubSubscribe.vue'),
    // Requires login — only existing members can renew
    meta: { title: 'Renew Membership - KLP48 Fan Club', requiresFan: true },
  },
  {
    path: '/fanclub/portal',
    name: 'FanclubPortal',
    component: () => import('@/pages/FanclubPortal.vue'),
    meta: { title: 'My Portal - KLP48 Fan Club', requiresFan: true, requiresActive: true },
  },
  {
    path: '/fanclub/payment/return',
    name: 'FanclubPaymentReturn',
    component: () => import('@/pages/FanclubPaymentReturn.vue'),
    meta: { title: 'Payment - KLP48 Fan Club' },
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

  const fan = useFanStore()

  // Fan-only routes: must be logged in
  if (to.meta.requiresFan && !fan.isLoggedIn) {
    return next({ path: '/fanclub/login', query: { redirect: to.fullPath } })
  }

  // Active-member-only routes: must be active subscriber
  if (to.meta.requiresActive && !fan.isActive) {
    return next('/fanclub/subscribe')
  }

  // Guest-only fanclub pages: redirect logged-in active members to portal
  if (to.meta.fanGuest && fan.isLoggedIn && fan.isActive) {
    return next('/fanclub/portal')
  }

  // Page transition logic
  const fromBase = getBasePath(from.path)
  const toBase   = getBasePath(to.path)
  const fromIdx  = routeOrder[fromBase] ?? -1
  const toIdx    = routeOrder[toBase]   ?? -1

  const slideUpRoutes = new Set(['/', '/about'])
  if (slideUpRoutes.has(to.path) || slideUpRoutes.has(from.path)) to.meta.transition = 'slide-up'
  else if (fromIdx === -1 || toIdx === -1) to.meta.transition = 'fade'
  else if (toIdx > fromIdx) to.meta.transition = 'slide-left'
  else if (toIdx < fromIdx) to.meta.transition = 'slide-right'
  else to.meta.transition = 'fade'

  next()
})

export default router
