import type { SupportLocale } from '../index'

const LOCALE_KEY = 'vidspark_locale'

/**
 * 保存語言偏好到本地存儲
 */
export function saveLocalePreference(locale: SupportLocale): void {
  try {
    localStorage.setItem(LOCALE_KEY, locale)
  } catch (error) {
    console.warn('Failed to save locale preference:', error)
  }
}

/**
 * 從本地存儲獲取語言偏好
 */
export function getLocalePreference(): SupportLocale | null {
  try {
    const stored = localStorage.getItem(LOCALE_KEY)
    return stored as SupportLocale | null
  } catch (error) {
    console.warn('Failed to get locale preference:', error)
    return null
  }
}

/**
 * 清除語言偏好
 */
export function clearLocalePreference(): void {
  try {
    localStorage.removeItem(LOCALE_KEY)
  } catch (error) {
    console.warn('Failed to clear locale preference:', error)
  }
}

/**
 * 檢測瀏覽器語言
 */
export function detectBrowserLocale(): SupportLocale {
  const browserLang = navigator.language || navigator.languages?.[0] || 'en'
  
  // 簡化語言匹配
  if (browserLang.startsWith('zh')) {
    if (browserLang.includes('TW') || browserLang.includes('HK') || browserLang.includes('MO')) {
      return 'zh-TW'
    }
    return 'zh-CN'
  }
  
  return 'en' // 默認英文
}
