import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory('/vidspark-admin/'),
  routes: [
    {
      path: '/',
      name: 'AdminDashboard',
      component: () => import('../views/AdminDashboard.vue')
    },
    {
      path: '/users',
      name: 'UserManagement',
      component: () => import('../views/UserManagement.vue')
    },
    {
      path: '/i18n',
      name: 'I18nManagement',
      component: () => import('../views/I18nManagement.vue')
    },
    {
      path: '/settings',
      name: 'AdminSettings',
      component: () => import('../views/AdminSettings.vue')
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'AdminNotFound',
      component: () => import('../views/AdminNotFound.vue')
    }
  ]
})

export default router
