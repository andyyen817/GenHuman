import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      redirect: '/dashboard'
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: () => import('../views/DashboardView.vue'),
    },
    {
      path: '/projects',
      name: 'projects',
      component: () => import('../views/ProjectsView.vue'),
    },
    {
      path: '/avatars',
      name: 'avatars',
      component: () => import('../views/AvatarsView.vue'),
    },
    {
      path: '/voices',
      name: 'voices',
      component: () => import('../views/VoicesView.vue'),
    },
    {
      path: '/templates',
      name: 'templates',
      component: () => import('../views/TemplatesView.vue'),
    },
    {
      path: '/media',
      name: 'media',
      component: () => import('../views/MediaView.vue'),
    },
    {
      path: '/settings',
      name: 'settings',
      component: () => import('../views/SettingsView.vue'),
    },
    {
      path: '/team',
      name: 'team',
      component: () => import('../views/TeamView.vue'),
    },
    {
      path: '/pricing',
      name: 'pricing',
      // 重定向到HTML頁面
      beforeEnter() {
        window.location.href = '/vidspark-v2/pricing.html'
        return false
      },
      component: () => Promise.resolve({ template: '<div></div>' })
    }
  ],
})

export default router
