import { createRouter, createWebHistory } from 'vue-router'

// 路由守衛：檢查管理員登入狀態
const checkAuth = () => {
  const authData = localStorage.getItem('vidspark_admin_auth') || 
                   sessionStorage.getItem('vidspark_admin_auth')
  
  if (authData) {
    try {
      const { loginTime } = JSON.parse(authData)
      const timeDiff = Date.now() - new Date(loginTime).getTime()
      const hoursDiff = timeDiff / (1000 * 60 * 60)
      
      // 如果登入時間超過24小時，清除登入狀態
      if (hoursDiff > 24) {
        localStorage.removeItem('vidspark_admin_auth')
        sessionStorage.removeItem('vidspark_admin_auth')
        return false
      }
      return true
    } catch (error) {
      localStorage.removeItem('vidspark_admin_auth')
      sessionStorage.removeItem('vidspark_admin_auth')
      return false
    }
  }
  return false
}

const router = createRouter({
  history: createWebHistory('/vidspark-admin/'),
  routes: [
    {
      path: '/login',
      name: 'AdminLogin',
      component: () => import('../views/AdminLogin.vue'),
      meta: { hideFromAuth: true }
    },
    {
      path: '/',
      name: 'AdminDashboard',
      component: () => import('../views/AdminDashboard.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/users',
      name: 'UserManagement',
      component: () => import('../views/UserManagement.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/i18n',
      name: 'I18nManagement',
      component: () => import('../views/I18nManagement.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/settings',
      name: 'AdminSettings',
      component: () => import('../views/AdminSettings.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'AdminNotFound',
      component: () => import('../views/AdminNotFound.vue')
    }
  ]
})

// 全局路由守衛
router.beforeEach((to, from, next) => {
  const isAuthenticated = checkAuth()
  
  // 如果頁面需要認證但用戶未登入，跳轉到登入頁
  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login')
  }
  // 如果用戶已登入但訪問登入頁，跳轉到首頁
  else if (to.name === 'AdminLogin' && isAuthenticated) {
    next('/')
  }
  // 其他情況正常導航
  else {
    next()
  }
})

export default router
