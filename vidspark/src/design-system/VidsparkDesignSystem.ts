/**
 * Vidspark設計系統核心配置
 * 定義統一的設計規範、配色方案和組件樣式
 */

/**
 * 配色方案接口
 */
export interface ColorScheme {
  name: string
  description: string
  colors: {
    primary: string
    secondary: string
    accent: string
    background: string
    surface: string
    text: {
      primary: string
      secondary: string
      disabled: string
    }
    border: string
    shadow: string
    success: string
    warning: string
    error: string
    info: string
  }
}

/**
 * 字體系統接口
 */
export interface Typography {
  fontFamily: {
    sans: string
    serif: string
    mono: string
  }
  fontSize: {
    xs: string
    sm: string
    base: string
    lg: string
    xl: string
    '2xl': string
    '3xl': string
    '4xl': string
    '5xl': string
  }
  fontWeight: {
    light: number
    normal: number
    medium: number
    semibold: number
    bold: number
  }
  lineHeight: {
    tight: number
    normal: number
    relaxed: number
    loose: number
  }
  letterSpacing: {
    tight: string
    normal: string
    wide: string
  }
}

/**
 * 間距系統接口
 */
export interface Spacing {
  baseUnit: number
  scale: {
    0: string
    1: string
    2: string
    3: string
    4: string
    5: string
    6: string
    8: string
    10: string
    12: string
    16: string
    20: string
    24: string
    32: string
    40: string
    48: string
    56: string
    64: string
  }
}

/**
 * 斷點系統接口
 */
export interface Breakpoints {
  xs: string
  sm: string
  md: string
  lg: string
  xl: string
  '2xl': string
}

/**
 * 陰影系統接口
 */
export interface Shadows {
  none: string
  sm: string
  base: string
  md: string
  lg: string
  xl: string
  '2xl': string
  inner: string
}

/**
 * 邊框半徑接口
 */
export interface BorderRadius {
  none: string
  sm: string
  base: string
  md: string
  lg: string
  xl: string
  '2xl': string
  '3xl': string
  full: string
}

/**
 * 動畫配置接口
 */
export interface Animations {
  duration: {
    fast: string
    normal: string
    slow: string
  }
  easing: {
    linear: string
    ease: string
    easeIn: string
    easeOut: string
    easeInOut: string
  }
  keyframes: {
    fadeIn: string
    fadeOut: string
    slideIn: string
    slideOut: string
    bounce: string
    pulse: string
  }
}

/**
 * 靜態頁面配色方案
 */
export const staticPagesColorScheme: ColorScheme = {
  name: 'Static Pages',
  description: '適用於靜態頁面的清新配色方案',
  colors: {
    primary: '#2563eb',
    secondary: '#8b5cf6',
    accent: '#10b981',
    background: '#ffffff',
    surface: '#f8fafc',
    text: {
      primary: '#1e293b',
      secondary: '#64748b',
      disabled: '#94a3b8'
    },
    border: '#e2e8f0',
    shadow: 'rgba(0, 0, 0, 0.1)',
    success: '#10b981',
    warning: '#f59e0b',
    error: '#ef4444',
    info: '#3b82f6'
  }
}

/**
 * 控制台淺色主題配色方案
 */
export const consoleLightColorScheme: ColorScheme = {
  name: 'Console Light',
  description: '控制台淺色主題，專業且易讀',
  colors: {
    primary: '#2563eb',
    secondary: '#64748b',
    accent: '#3b82f6',
    background: '#ffffff',
    surface: '#f1f5f9',
    text: {
      primary: '#1e293b',
      secondary: '#475569',
      disabled: '#94a3b8'
    },
    border: '#cbd5e1',
    shadow: 'rgba(0, 0, 0, 0.08)',
    success: '#059669',
    warning: '#d97706',
    error: '#dc2626',
    info: '#2563eb'
  }
}

/**
 * 控制台深色主題配色方案
 */
export const consoleDarkColorScheme: ColorScheme = {
  name: 'Console Dark',
  description: '控制台深色主題，減少眼部疲勞',
  colors: {
    primary: '#3b82f6',
    secondary: '#94a3b8',
    accent: '#60a5fa',
    background: '#0f172a',
    surface: '#1e293b',
    text: {
      primary: '#f1f5f9',
      secondary: '#cbd5e1',
      disabled: '#64748b'
    },
    border: '#334155',
    shadow: 'rgba(0, 0, 0, 0.3)',
    success: '#10b981',
    warning: '#f59e0b',
    error: '#f87171',
    info: '#60a5fa'
  }
}

/**
 * 字體系統配置
 */
export const typography: Typography = {
  fontFamily: {
    sans: 'Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
    serif: 'Georgia, Cambria, "Times New Roman", Times, serif',
    mono: 'Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace'
  },
  fontSize: {
    xs: '0.75rem',    // 12px
    sm: '0.875rem',   // 14px
    base: '1rem',     // 16px
    lg: '1.125rem',   // 18px
    xl: '1.25rem',    // 20px
    '2xl': '1.5rem',  // 24px
    '3xl': '1.875rem', // 30px
    '4xl': '2.25rem', // 36px
    '5xl': '3rem'     // 48px
  },
  fontWeight: {
    light: 300,
    normal: 400,
    medium: 500,
    semibold: 600,
    bold: 700
  },
  lineHeight: {
    tight: 1.25,
    normal: 1.5,
    relaxed: 1.75,
    loose: 2
  },
  letterSpacing: {
    tight: '-0.025em',
    normal: '0',
    wide: '0.025em'
  }
}

/**
 * 間距系統配置
 */
export const spacing: Spacing = {
  baseUnit: 4, // 4px
  scale: {
    0: '0',
    1: '0.25rem',  // 4px
    2: '0.5rem',   // 8px
    3: '0.75rem',  // 12px
    4: '1rem',     // 16px
    5: '1.25rem',  // 20px
    6: '1.5rem',   // 24px
    8: '2rem',     // 32px
    10: '2.5rem',  // 40px
    12: '3rem',    // 48px
    16: '4rem',    // 64px
    20: '5rem',    // 80px
    24: '6rem',    // 96px
    32: '8rem',    // 128px
    40: '10rem',   // 160px
    48: '12rem',   // 192px
    56: '14rem',   // 224px
    64: '16rem'    // 256px
  }
}

/**
 * 響應式斷點配置
 */
export const breakpoints: Breakpoints = {
  xs: '475px',
  sm: '640px',
  md: '768px',
  lg: '1024px',
  xl: '1280px',
  '2xl': '1536px'
}

/**
 * 陰影系統配置
 */
export const shadows: Shadows = {
  none: 'none',
  sm: '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
  base: '0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06)',
  md: '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
  lg: '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
  xl: '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
  '2xl': '0 25px 50px -12px rgba(0, 0, 0, 0.25)',
  inner: 'inset 0 2px 4px 0 rgba(0, 0, 0, 0.06)'
}

/**
 * 邊框半徑配置
 */
export const borderRadius: BorderRadius = {
  none: '0',
  sm: '0.125rem',   // 2px
  base: '0.25rem',  // 4px
  md: '0.375rem',   // 6px
  lg: '0.5rem',     // 8px
  xl: '0.75rem',    // 12px
  '2xl': '1rem',    // 16px
  '3xl': '1.5rem',  // 24px
  full: '9999px'
}

/**
 * 動畫配置
 */
export const animations: Animations = {
  duration: {
    fast: '150ms',
    normal: '300ms',
    slow: '500ms'
  },
  easing: {
    linear: 'linear',
    ease: 'ease',
    easeIn: 'ease-in',
    easeOut: 'ease-out',
    easeInOut: 'ease-in-out'
  },
  keyframes: {
    fadeIn: `
      @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
      }
    `,
    fadeOut: `
      @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
      }
    `,
    slideIn: `
      @keyframes slideIn {
        from { transform: translateX(-100%); }
        to { transform: translateX(0); }
      }
    `,
    slideOut: `
      @keyframes slideOut {
        from { transform: translateX(0); }
        to { transform: translateX(-100%); }
      }
    `,
    bounce: `
      @keyframes bounce {
        0%, 20%, 53%, 80%, 100% {
          transform: translate3d(0, 0, 0);
        }
        40%, 43% {
          transform: translate3d(0, -30px, 0);
        }
        70% {
          transform: translate3d(0, -15px, 0);
        }
        90% {
          transform: translate3d(0, -4px, 0);
        }
      }
    `,
    pulse: `
      @keyframes pulse {
        0% {
          transform: scale(1);
        }
        50% {
          transform: scale(1.05);
        }
        100% {
          transform: scale(1);
        }
      }
    `
  }
}

/**
 * 設計系統主配置
 */
export interface VidsparkDesignSystem {
  colorSchemes: {
    staticPages: ColorScheme
    consoleLight: ColorScheme
    consoleDark: ColorScheme
  }
  typography: Typography
  spacing: Spacing
  breakpoints: Breakpoints
  shadows: Shadows
  borderRadius: BorderRadius
  animations: Animations
}

/**
 * 完整的設計系統配置
 */
export const vidsparkDesignSystem: VidsparkDesignSystem = {
  colorSchemes: {
    staticPages: staticPagesColorScheme,
    consoleLight: consoleLightColorScheme,
    consoleDark: consoleDarkColorScheme
  },
  typography,
  spacing,
  breakpoints,
  shadows,
  borderRadius,
  animations
}

/**
 * 主題管理器
 */
export class ThemeManager {
  private currentTheme: string = 'consoleLight'
  private themes: Map<string, ColorScheme> = new Map()
  private listeners: Set<(theme: ColorScheme) => void> = new Set()

  constructor() {
    this.themes.set('staticPages', staticPagesColorScheme)
    this.themes.set('consoleLight', consoleLightColorScheme)
    this.themes.set('consoleDark', consoleDarkColorScheme)
    
    // 自動檢測系統主題偏好
    this.detectSystemTheme()
  }

  /**
   * 檢測系統主題偏好
   */
  private detectSystemTheme(): void {
    if (typeof window !== 'undefined' && window.matchMedia) {
      const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)')
      
      // 設置初始主題
      this.currentTheme = darkModeQuery.matches ? 'consoleDark' : 'consoleLight'
      
      // 監聽系統主題變化
      darkModeQuery.addEventListener('change', (e) => {
        this.setTheme(e.matches ? 'consoleDark' : 'consoleLight')
      })
    }
  }

  /**
   * 設置主題
   */
  setTheme(themeName: string): void {
    if (this.themes.has(themeName)) {
      this.currentTheme = themeName
      const theme = this.themes.get(themeName)!
      
      // 應用CSS變量
      this.applyCSSVariables(theme)
      
      // 通知監聽器
      this.listeners.forEach(listener => listener(theme))
      
      // 保存到本地存儲
      if (typeof localStorage !== 'undefined') {
        localStorage.setItem('vidspark-theme', themeName)
      }
    }
  }

  /**
   * 獲取當前主題
   */
  getCurrentTheme(): ColorScheme {
    return this.themes.get(this.currentTheme)!
  }

  /**
   * 獲取當前主題名稱
   */
  getCurrentThemeName(): string {
    return this.currentTheme
  }

  /**
   * 獲取所有可用主題
   */
  getAvailableThemes(): { name: string; scheme: ColorScheme }[] {
    return Array.from(this.themes.entries()).map(([name, scheme]) => ({
      name,
      scheme
    }))
  }

  /**
   * 切換主題（在淺色和深色之間）
   */
  toggleTheme(): void {
    const isDark = this.currentTheme === 'consoleDark'
    this.setTheme(isDark ? 'consoleLight' : 'consoleDark')
  }

  /**
   * 添加主題變化監聽器
   */
  addThemeListener(listener: (theme: ColorScheme) => void): void {
    this.listeners.add(listener)
  }

  /**
   * 移除主題變化監聽器
   */
  removeThemeListener(listener: (theme: ColorScheme) => void): void {
    this.listeners.delete(listener)
  }

  /**
   * 應用CSS變量
   */
  private applyCSSVariables(theme: ColorScheme): void {
    if (typeof document !== 'undefined') {
      const root = document.documentElement
      
      // 應用顏色變量
      root.style.setProperty('--color-primary', theme.colors.primary)
      root.style.setProperty('--color-secondary', theme.colors.secondary)
      root.style.setProperty('--color-accent', theme.colors.accent)
      root.style.setProperty('--color-background', theme.colors.background)
      root.style.setProperty('--color-surface', theme.colors.surface)
      root.style.setProperty('--color-text-primary', theme.colors.text.primary)
      root.style.setProperty('--color-text-secondary', theme.colors.text.secondary)
      root.style.setProperty('--color-text-disabled', theme.colors.text.disabled)
      root.style.setProperty('--color-border', theme.colors.border)
      root.style.setProperty('--color-shadow', theme.colors.shadow)
      root.style.setProperty('--color-success', theme.colors.success)
      root.style.setProperty('--color-warning', theme.colors.warning)
      root.style.setProperty('--color-error', theme.colors.error)
      root.style.setProperty('--color-info', theme.colors.info)
      
      // 應用字體變量
      root.style.setProperty('--font-family-sans', typography.fontFamily.sans)
      root.style.setProperty('--font-family-serif', typography.fontFamily.serif)
      root.style.setProperty('--font-family-mono', typography.fontFamily.mono)
      
      // 應用間距變量
      Object.entries(spacing.scale).forEach(([key, value]) => {
        root.style.setProperty(`--spacing-${key}`, value)
      })
      
      // 應用陰影變量
      Object.entries(shadows).forEach(([key, value]) => {
        root.style.setProperty(`--shadow-${key}`, value)
      })
      
      // 應用邊框半徑變量
      Object.entries(borderRadius).forEach(([key, value]) => {
        root.style.setProperty(`--radius-${key}`, value)
      })
    }
  }

  /**
   * 從本地存儲恢復主題
   */
  restoreTheme(): void {
    if (typeof localStorage !== 'undefined') {
      const savedTheme = localStorage.getItem('vidspark-theme')
      if (savedTheme && this.themes.has(savedTheme)) {
        this.setTheme(savedTheme)
      }
    }
  }

  /**
   * 註冊自定義主題
   */
  registerTheme(name: string, scheme: ColorScheme): void {
    this.themes.set(name, scheme)
  }

  /**
   * 移除主題
   */
  removeTheme(name: string): void {
    if (name !== 'consoleLight' && name !== 'consoleDark' && name !== 'staticPages') {
      this.themes.delete(name)
      
      // 如果移除的是當前主題，切換到默認主題
      if (this.currentTheme === name) {
        this.setTheme('consoleLight')
      }
    }
  }
}

// 導出默認主題管理器實例
export const themeManager = new ThemeManager()

/**
 * CSS變量生成器
 */
export function generateCSSVariables(theme: ColorScheme): string {
  return `
    :root {
      /* 顏色變量 */
      --color-primary: ${theme.colors.primary};
      --color-secondary: ${theme.colors.secondary};
      --color-accent: ${theme.colors.accent};
      --color-background: ${theme.colors.background};
      --color-surface: ${theme.colors.surface};
      --color-text-primary: ${theme.colors.text.primary};
      --color-text-secondary: ${theme.colors.text.secondary};
      --color-text-disabled: ${theme.colors.text.disabled};
      --color-border: ${theme.colors.border};
      --color-shadow: ${theme.colors.shadow};
      --color-success: ${theme.colors.success};
      --color-warning: ${theme.colors.warning};
      --color-error: ${theme.colors.error};
      --color-info: ${theme.colors.info};
      
      /* 字體變量 */
      --font-family-sans: ${typography.fontFamily.sans};
      --font-family-serif: ${typography.fontFamily.serif};
      --font-family-mono: ${typography.fontFamily.mono};
      
      /* 間距變量 */
      ${Object.entries(spacing.scale).map(([key, value]) => 
        `--spacing-${key}: ${value};`
      ).join('\n      ')}
      
      /* 陰影變量 */
      ${Object.entries(shadows).map(([key, value]) => 
        `--shadow-${key}: ${value};`
      ).join('\n      ')}
      
      /* 邊框半徑變量 */
      ${Object.entries(borderRadius).map(([key, value]) => 
        `--radius-${key}: ${value};`
      ).join('\n      ')}
      
      /* 動畫變量 */
      --duration-fast: ${animations.duration.fast};
      --duration-normal: ${animations.duration.normal};
      --duration-slow: ${animations.duration.slow};
      --easing-linear: ${animations.easing.linear};
      --easing-ease: ${animations.easing.ease};
      --easing-ease-in: ${animations.easing.easeIn};
      --easing-ease-out: ${animations.easing.easeOut};
      --easing-ease-in-out: ${animations.easing.easeInOut};
    }
    
    /* 動畫關鍵幀 */
    ${Object.values(animations.keyframes).join('\n    ')}
  `
}

/**
 * 響應式媒體查詢生成器
 */
export function generateMediaQueries(): string {
  return `
    /* 響應式斷點 */
    @media (min-width: ${breakpoints.xs}) {
      .xs\\:block { display: block; }
      .xs\\:hidden { display: none; }
    }
    
    @media (min-width: ${breakpoints.sm}) {
      .sm\\:block { display: block; }
      .sm\\:hidden { display: none; }
    }
    
    @media (min-width: ${breakpoints.md}) {
      .md\\:block { display: block; }
      .md\\:hidden { display: none; }
    }
    
    @media (min-width: ${breakpoints.lg}) {
      .lg\\:block { display: block; }
      .lg\\:hidden { display: none; }
    }
    
    @media (min-width: ${breakpoints.xl}) {
      .xl\\:block { display: block; }
      .xl\\:hidden { display: none; }
    }
    
    @media (min-width: ${breakpoints['2xl']}) {
      .\\32 xl\\:block { display: block; }
      .\\32 xl\\:hidden { display: none; }
    }
  `
}