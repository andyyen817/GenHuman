import type { SupportLocale } from '../index'

/**
 * 動態載入語言包
 * 目前為簡化版本，未來可擴展為按需載入
 */
export async function loadLocaleMessages(locale: SupportLocale): Promise<Record<string, any> | null> {
  try {
    let messages: Record<string, any>
    
    switch (locale) {
      case 'en':
        messages = await import('../locales/en.json')
        break
      case 'zh-TW':
        messages = await import('../locales/zh-TW.json')
        break
      case 'zh-CN':
        messages = await import('../locales/zh-CN.json')
        break
      default:
        console.warn(`Unsupported locale: ${locale}`)
        return null
    }
    
    // 返回默認導出或整個模塊
    return messages.default || messages
  } catch (error) {
    console.error(`Failed to load locale messages for ${locale}:`, error)
    return null
  }
}

/**
 * 獲取語言顯示名稱
 */
export function getLanguageDisplayName(locale: SupportLocale): string {
  const displayNames: Record<SupportLocale, string> = {
    'en': 'English',
    'zh-TW': '繁體中文',
    'zh-CN': '简体中文'
  }
  
  return displayNames[locale] || locale
}

/**
 * 獲取語言標誌
 */
export function getLanguageFlag(locale: SupportLocale): string {
  const flags: Record<SupportLocale, string> = {
    'en': '🇺🇸',
    'zh-TW': '🇹🇼',
    'zh-CN': '🇨🇳'
  }
  
  return flags[locale] || '🌐'
}
