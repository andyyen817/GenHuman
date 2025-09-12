import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('../views/SimpleDashboardView.vue'),
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      redirect: '/' // 重定向到新的简化首页
    },
    {
      path: '/old-dashboard',
      name: 'old-dashboard',
      component: () => import('../views/DashboardView.vue'),
    },
    {
      path: '/projects',
      name: 'projects',
      component: () => import('../views/ProjectsView.vue'),
    },
    // 🎯 AI创作工作流路由 (按照v6任务清单)
    {
      path: '/inspiration',
      name: 'inspiration',
      component: () => import('../views/InspirationView.vue'),
    },
    {
      path: '/scriptwriter',
      name: 'scriptwriter',
      component: () => import('../views/ScriptwriterView.vue'),
    },
    {
      path: '/voice-storyboard',
      name: 'voice-storyboard',
      component: () => import('../views/VoiceStoryboardView.vue'),
    },
    {
      path: '/image-storyboard',
      name: 'image-storyboard',
      component: () => import('../views/ImageStoryboardView.vue'),
    },
    {
      path: '/director',
      name: 'director', 
      component: () => import('../views/DirectorView.vue'),
    },
    {
      path: '/editor',
      name: 'editor',
      component: () => import('../views/EditorView.vue'),
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
    },
    {
      path: '/genhuman',
      name: 'genhuman',
      component: () => import('../views/GenHumanView.vue'),
    }
  ],
})

export default router
