// 動態加載語言包
export async function loadLocaleMessages() {
  const messages: Record<string, any> = {}
  
  // 加載所有語言文件
  const locales = ['zh-TW', 'zh-CN', 'en']
  
  for (const locale of locales) {
    try {
      const module = await import(`./locales/${locale}.json`)
      messages[locale] = module.default || module
    } catch (error) {
      console.warn(`Failed to load locale ${locale}:`, error)
      // 提供默認語言包
      messages[locale] = {
        app: {
          title: 'Vidspark Admin',
          welcome: 'Welcome to Vidspark Admin Dashboard'
        }
      }
    }
  }
  
  return messages
}

// 語言切換工具函數
export function setLocale(locale: string) {
  localStorage.setItem('vidspark_admin_locale', locale)
}

export function getLocale(): string {
  return localStorage.getItem('vidspark_admin_locale') || 'zh-TW'
}
