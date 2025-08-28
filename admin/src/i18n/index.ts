import { createI18n } from 'vue-i18n'
import type { App } from 'vue'

// 語言包導入
import en from './locales/en.json'
import zhTW from './locales/zh-TW.json'
import zhCN from './locales/zh-CN.json'

// 支援的語言
export const SUPPORT_LOCALES = ['en', 'zh-TW', 'zh-CN'] as const
export type SupportLocale = typeof SUPPORT_LOCALES[number]

// 默認語言
const DEFAULT_LOCALE: SupportLocale = 'en'

// 創建i18n實例
const i18n = createI18n({
  legacy: false, // 使用 Composition API
  locale: DEFAULT_LOCALE,
  fallbackLocale: 'en',
  messages: {
    en,
    'zh-TW': zhTW,
    'zh-CN': zhCN
  },
  globalInjection: true
})

// 安裝插件
export function setupI18n(app: App): void {
  app.use(i18n)
}

export default i18n
