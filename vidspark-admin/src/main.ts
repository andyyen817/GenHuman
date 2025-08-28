import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createI18n } from 'vue-i18n'
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'

import App from './App.vue'
import router from './router'

// 默認語言包
const defaultMessages = {
  'zh-TW': {
    app: {
      title: 'Vidspark 管理後台',
      welcome: '歡迎使用 Vidspark 管理後台'
    },
    nav: {
      dashboard: '控制台',
      users: '用戶管理',
      i18n: '多語言管理',
      settings: '系統設置'
    },
    common: {
      save: '保存',
      cancel: '取消',
      edit: '編輯',
      delete: '刪除',
      search: '搜索',
      loading: '載入中...',
      success: '操作成功',
      error: '操作失敗'
    }
  },
  'zh-CN': {
    app: {
      title: 'Vidspark 管理后台',
      welcome: '欢迎使用 Vidspark 管理后台'
    },
    nav: {
      dashboard: '控制台',
      users: '用户管理',
      i18n: '多语言管理',
      settings: '系统设置'
    },
    common: {
      save: '保存',
      cancel: '取消',
      edit: '编辑',
      delete: '删除',
      search: '搜索',
      loading: '加载中...',
      success: '操作成功',
      error: '操作失败'
    }
  },
  'en': {
    app: {
      title: 'Vidspark Admin',
      welcome: 'Welcome to Vidspark Admin Dashboard'
    },
    nav: {
      dashboard: 'Dashboard',
      users: 'User Management',
      i18n: 'I18n Management',
      settings: 'Settings'
    },
    common: {
      save: 'Save',
      cancel: 'Cancel',
      edit: 'Edit',
      delete: 'Delete',
      search: 'Search',
      loading: 'Loading...',
      success: 'Success',
      error: 'Error'
    }
  }
}

// 創建Vue應用
const app = createApp(App)

// 創建Pinia狀態管理
const pinia = createPinia()

// 創建i18n實例（同步初始化）
const i18n = createI18n({
  legacy: false,
  locale: localStorage.getItem('vidspark_admin_locale') || 'zh-TW',
  fallbackLocale: 'en',
  messages: defaultMessages
})

// 安裝插件
app.use(pinia)
app.use(router)
app.use(i18n)
app.use(ElementPlus)

// 掛載應用
app.mount('#app')
