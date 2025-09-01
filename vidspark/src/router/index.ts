import { createRouter, createWebHistory } from 'vue-router';
import type { RouteRecordRaw } from 'vue-router';

// 路由配置
const routes: RouteRecordRaw[] = [
  {
    path: '/',
    name: 'Landing',
    component: () => import('@/views/LandingView.vue'),
    meta: {
      title: 'Vidspark - AI影片創作平台',
      requiresAuth: false
    }
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('@/views/RegisterView.vue'),
    meta: {
      title: '註冊 - Vidspark',
      requiresAuth: false
    }
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/LoginView.vue'),
    meta: {
      title: '登入 - Vidspark',
      requiresAuth: false
    }
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import('@/views/DashboardView.vue'),
    meta: {
      title: '主控台 - Vidspark',
      requiresAuth: true
    }
  },
  {
    path: '/voice-clone',
    name: 'VoiceClone',
    component: () => import('@/views/VoiceClone.vue'),
    meta: {
      title: '聲音克隆 - Vidspark',
      requiresAuth: true
    }
  },
  {
    path: '/create-video',
    name: 'CreateVideo',
    component: () => import('@/views/CreateVideo.vue'),
    meta: {
      title: '影片創作 - Vidspark',
      requiresAuth: true
    }
  },
  {
    path: '/my-videos',
    name: 'MyVideos',
    component: () => import('@/views/MyVideos.vue'),
    meta: {
      title: '我的影片 - Vidspark',
      requiresAuth: true
    }
  },
  {
    path: '/settings',
    name: 'Settings',
    component: () => import('@/views/Settings.vue'),
    meta: {
      title: '設置 - Vidspark',
      requiresAuth: true
    }
  },
  {
    path: '/templates',
    name: 'Templates',
    component: () => import('@/views/Templates.vue'),
    meta: {
      title: '模板庫 - Vidspark',
      requiresAuth: true
    }
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('@/views/NotFound.vue')
  }
];

// 創建路由實例
const router = createRouter({
  history: createWebHistory('/vidspark/'),
  routes
});

// 路由守衛
router.beforeEach((to, from, next) => {
  // 檢查認證
  const token = localStorage.getItem('vidspark_user_token');
  const requiresAuth = to.meta.requiresAuth;

  if (requiresAuth && !token) {
    // 需要認證但未登入，跳轉到登入頁
    next('/login');
  } else if (!requiresAuth && token && (to.name === 'Login' || to.name === 'Register')) {
    // 已登入但訪問登入/註冊頁，跳轉到控制台
    next('/dashboard');
  } else {
    next();
  }
  
  // 設置頁面標題
  if (to.meta.title) {
    document.title = to.meta.title as string;
  }
});

export default router;