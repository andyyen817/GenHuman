/**
 * Vue 主題管理組合式函數
 * 提供響應式的主題狀態管理
 */

import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import {
  ThemeMode,
  PageType,
  ThemeConfig,
  getCurrentTheme,
  setTheme,
  toggleTheme,
  getThemeConfig,
  applyTheme,
  initTheme
} from '../utils/theme';

// 全局主題狀態
const themeMode = ref<ThemeMode>(getCurrentTheme());
const isInitialized = ref(false);

export function useTheme() {
  const route = useRoute();
  
  // 計算當前頁面類型
  const pageType = computed<PageType>(() => {
    const pathname = route?.path || '';
    const staticPages = ['login', 'register', 'landing', 'pricing', 'about', 'company'];
    const isStaticPage = staticPages.some(page => pathname.toLowerCase().includes(page));
    return isStaticPage ? 'static' : 'console';
  });
  
  // 計算當前主題配置
  const themeConfig = computed<ThemeConfig>(() => {
    return getThemeConfig(route?.path);
  });
  
  // 計算是否為深色模式
  const isDark = computed(() => themeMode.value === 'dark');
  
  // 計算是否為靜態頁面
  const isStaticPage = computed(() => pageType.value === 'static');
  
  // 計算是否為控制台頁面
  const isConsolePage = computed(() => pageType.value === 'console');
  
  // 主題變更處理函數
  const handleThemeChange = (event: CustomEvent) => {
    themeMode.value = event.detail.mode;
  };
  
  // 切換主題
  const toggle = () => {
    const newMode = toggleTheme();
    themeMode.value = newMode;
    return newMode;
  };
  
  // 設置特定主題
  const setMode = (mode: ThemeMode) => {
    setTheme(mode);
    themeMode.value = mode;
  };
  
  // 應用主題到當前頁面
  const apply = () => {
    applyTheme(route?.path);
  };
  
  // 獲取主題相關的CSS類名
  const getThemeClasses = () => {
    const classes = [
      `theme-${themeMode.value}`,
      `page-${pageType.value}`
    ];
    
    if (isStaticPage.value) {
      classes.push('static-page');
    } else {
      classes.push('console-page');
    }
    
    return classes;
  };
  
  // 獲取主題相關的樣式對象
  const getThemeStyles = () => {
    const config = themeConfig.value;
    const styles: Record<string, string> = {};
    
    if (config.pageType === 'console') {
      styles.backgroundColor = config.colors.background;
      styles.color = config.colors.text.primary;
    }
    
    return styles;
  };
  
  // 監聽路由變化，重新應用主題
  watch(
    () => route?.path,
    () => {
      if (isInitialized.value) {
        apply();
      }
    },
    { immediate: false }
  );
  
  // 組件掛載時初始化
  onMounted(() => {
    if (!isInitialized.value) {
      initTheme();
      isInitialized.value = true;
    }
    
    apply();
    
    // 監聽主題變更事件
    if (typeof window !== 'undefined') {
      window.addEventListener('theme-changed', handleThemeChange as EventListener);
    }
  });
  
  // 組件卸載時清理
  onUnmounted(() => {
    if (typeof window !== 'undefined') {
      window.removeEventListener('theme-changed', handleThemeChange as EventListener);
    }
  });
  
  return {
    // 狀態
    themeMode: readonly(themeMode),
    pageType: readonly(pageType),
    themeConfig: readonly(themeConfig),
    isDark: readonly(isDark),
    isStaticPage: readonly(isStaticPage),
    isConsolePage: readonly(isConsolePage),
    isInitialized: readonly(isInitialized),
    
    // 方法
    toggle,
    setMode,
    apply,
    getThemeClasses,
    getThemeStyles
  };
}

/**
 * 簡化版主題切換組合式函數
 * 適用於只需要基本主題切換功能的組件
 */
export function useSimpleTheme() {
  const mode = ref<ThemeMode>(getCurrentTheme());
  const isDark = computed(() => mode.value === 'dark');
  
  const toggle = () => {
    const newMode = toggleTheme();
    mode.value = newMode;
    return newMode;
  };
  
  const setMode = (newMode: ThemeMode) => {
    setTheme(newMode);
    mode.value = newMode;
  };
  
  // 監聽主題變更事件
  const handleThemeChange = (event: CustomEvent) => {
    mode.value = event.detail.mode;
  };
  
  onMounted(() => {
    if (typeof window !== 'undefined') {
      window.addEventListener('theme-changed', handleThemeChange as EventListener);
    }
  });
  
  onUnmounted(() => {
    if (typeof window !== 'undefined') {
      window.removeEventListener('theme-changed', handleThemeChange as EventListener);
    }
  });
  
  return {
    mode: readonly(mode),
    isDark: readonly(isDark),
    toggle,
    setMode
  };
}