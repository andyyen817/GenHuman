import { createRouter, createWebHashHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'

// 路由配置
const routes: RouteRecordRaw[] = [
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import('@/views/Dashboard.vue'),
    meta: {
      title: 'navigation.dashboard',
      requiresAuth: true
    }
  },
  {
    path: '/create',
    name: 'CreateVideo',
    component: () => import('@/views/CreateVideo.vue'),
    meta: {
      title: 'navigation.create_video',
      requiresAuth: true
    }
  },
  {
    path: '/videos',
    name: 'MyVideos',
    component: () => import('@/views/MyVideos.vue'),
    meta: {
      title: 'navigation.my_videos',
      requiresAuth: true
    }
  },
  {
    path: '/voice-clone',
    name: 'VoiceClone',
    component: () => import('@/views/VoiceClone.vue'),
    meta: {
      title: 'navigation.voice_clone',
      requiresAuth: true
    }
  },
  {
    path: '/templates',
    name: 'Templates',
    component: () => import('@/views/Templates.vue'),
    meta: {
      title: 'navigation.templates',
      requiresAuth: true
    }
  },
  {
    path: '/settings',
    name: 'Settings',
    component: () => import('@/views/Settings.vue'),
    meta: {
      title: 'navigation.settings',
      requiresAuth: true
    }
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/Login.vue'),
    meta: {
      title: 'auth.login',
      requiresAuth: false
    }
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('@/views/Register.vue'),
    meta: {
      title: 'auth.register',
      requiresAuth: false
    }
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('@/views/NotFound.vue')
  }
]

// 創建路由實例
const router = createRouter({
  history: createWebHashHistory('/vidspark/'),
  routes
})

// 路由守衛
router.beforeEach((to, from, next) => {
  // 檢查認證
  const token = localStorage.getItem('vidspark_token')
  const requiresAuth = to.meta.requiresAuth

  if (requiresAuth && !token) {
    // 需要認證但未登入，跳轉到登入頁
    next('/login')
  } else if (!requiresAuth && token && (to.name === 'Login' || to.name === 'Register')) {
    // 已登入但訪問登入/註冊頁，跳轉到控制台
    next('/dashboard')
  } else {
    next()
  }
})

export default router
