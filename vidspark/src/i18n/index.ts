import { createI18n } from 'vue-i18n'
import type { App } from 'vue'

// 語言包
import zhTW from './locales/zh-TW.json'
import zhCN from './locales/zh-CN.json'
import en from './locales/en.json'

// 支持的語言
export const SUPPORTED_LANGUAGES = [
  { code: 'zh-TW', name: '繁體中文', flag: '🇹🇼' },
  { code: 'zh-CN', name: '简体中文', flag: '🇨🇳' },
  { code: 'en', name: 'English', flag: '🇺🇸' }
] as const

export type SupportedLanguage = typeof SUPPORTED_LANGUAGES[number]['code']

// 創建 i18n 實例
const i18n = createI18n({
  legacy: false,
  locale: 'zh-TW', // 默認語言
  fallbackLocale: 'en',
  messages: {
    'zh-TW': zhTW,
    'zh-CN': zhCN,
    'en': en
  }
})

// 設置語言
export function setLanguage(language: SupportedLanguage) {
  i18n.global.locale.value = language
  document.documentElement.lang = language
  localStorage.setItem('vidspark_language', language)
}

// 獲取當前語言
export function getCurrentLanguage(): SupportedLanguage {
  const stored = localStorage.getItem('vidspark_language') as SupportedLanguage
  if (stored && SUPPORTED_LANGUAGES.find(lang => lang.code === stored)) {
    return stored
  }
  return 'zh-TW' // 默認繁體中文
}

// 設置 i18n 插件
export function setupI18n(app: App) {
  app.use(i18n)
  // 初始化語言設置
  const currentLang = getCurrentLanguage()
  setLanguage(currentLang)
}

export default i18n
