/**
 * 主題切換工具
 * 支持靜態頁面和控制台風格的主題管理
 */

import { colors } from '../config/design-system';

export type ThemeMode = 'light' | 'dark';
export type PageType = 'static' | 'console';

export interface ThemeConfig {
  mode: ThemeMode;
  pageType: PageType;
  colors: any;
}

/**
 * 獲取當前主題配置
 */
export function getCurrentTheme(): ThemeMode {
  if (typeof window !== 'undefined') {
    const stored = localStorage.getItem('vidspark-theme');
    if (stored && (stored === 'light' || stored === 'dark')) {
      return stored;
    }
    
    // 檢查系統偏好
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
      return 'dark';
    }
  }
  
  return 'light';
}

/**
 * 設置主題
 */
export function setTheme(mode: ThemeMode): void {
  if (typeof window !== 'undefined') {
    localStorage.setItem('vidspark-theme', mode);
    document.documentElement.setAttribute('data-theme', mode);
    
    // 觸發主題變更事件
    window.dispatchEvent(new CustomEvent('theme-changed', { detail: { mode } }));
  }
}

/**
 * 切換主題
 */
export function toggleTheme(): ThemeMode {
  const current = getCurrentTheme();
  const newMode = current === 'light' ? 'dark' : 'light';
  setTheme(newMode);
  return newMode;
}

/**
 * 根據頁面類型和主題模式獲取顏色配置
 */
export function getThemeColors(pageType: PageType, mode: ThemeMode = getCurrentTheme()): any {
  if (pageType === 'static') {
    return {
      ...colors.static,
      ...colors.status
    };
  }
  
  // 控制台風格
  return {
    ...colors.console[mode],
    ...colors.console.status
  };
}

/**
 * 判斷頁面類型
 */
export function getPageType(pathname: string): PageType {
  const staticPages = ['login', 'register', 'landing', 'pricing', 'about', 'company'];
  const isStaticPage = staticPages.some(page => pathname.toLowerCase().includes(page));
  return isStaticPage ? 'static' : 'console';
}

/**
 * 獲取完整的主題配置
 */
export function getThemeConfig(pathname?: string): ThemeConfig {
  const mode = getCurrentTheme();
  const pageType = pathname ? getPageType(pathname) : 'console';
  const themeColors = getThemeColors(pageType, mode);
  
  return {
    mode,
    pageType,
    colors: themeColors
  };
}

/**
 * 初始化主題
 */
export function initTheme(): void {
  if (typeof window !== 'undefined') {
    const theme = getCurrentTheme();
    setTheme(theme);
    
    // 監聽系統主題變化
    if (window.matchMedia) {
      const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
      mediaQuery.addEventListener('change', (e) => {
        if (!localStorage.getItem('vidspark-theme')) {
          setTheme(e.matches ? 'dark' : 'light');
        }
      });
    }
  }
}

/**
 * CSS 變量生成器
 */
export function generateCSSVariables(config: ThemeConfig): Record<string, string> {
  const variables: Record<string, string> = {};
  
  if (config.pageType === 'static') {
    variables['--color-primary'] = config.colors.primary;
    variables['--color-primary-dark'] = config.colors.primaryDark;
    variables['--color-primary-light'] = config.colors.primaryLight;
    variables['--color-secondary-purple'] = config.colors.secondary.purple;
    variables['--color-secondary-green'] = config.colors.secondary.green;
    variables['--color-secondary-orange'] = config.colors.secondary.orange;
  } else {
    variables['--color-background'] = config.colors.background;
    variables['--color-card'] = config.colors.card;
    variables['--color-border'] = config.colors.border;
    variables['--color-text-primary'] = config.colors.text.primary;
    variables['--color-text-secondary'] = config.colors.text.secondary;
    variables['--color-accent'] = config.colors.accent;
  }
  
  // 狀態色（通用）
  variables['--color-success'] = config.colors.success || config.colors.status?.success;
  variables['--color-warning'] = config.colors.warning || config.colors.status?.warning;
  variables['--color-error'] = config.colors.error || config.colors.status?.error;
  variables['--color-info'] = config.colors.info || config.colors.status?.info;
  
  return variables;
}

/**
 * 應用 CSS 變量到文檔
 */
export function applyCSSVariables(variables: Record<string, string>): void {
  if (typeof document !== 'undefined') {
    const root = document.documentElement;
    Object.entries(variables).forEach(([key, value]) => {
      root.style.setProperty(key, value);
    });
  }
}

/**
 * 完整的主題應用函數
 */
export function applyTheme(pathname?: string): void {
  const config = getThemeConfig(pathname);
  const variables = generateCSSVariables(config);
  applyCSSVariables(variables);
}