/**
 * Vidspark Vue 組件庫
 * 統一導出所有組件和類型定義
 */

// 基礎組件
export { default as VButton } from './base/VButton.vue'
export { default as VInput } from './base/VInput.vue'
export { default as VTable } from './base/VTable.vue'
export { default as VCard } from './base/VCard.vue'
export { default as VModal } from './base/VModal.vue'

// 組件類型定義
export type {
  VButtonProps,
  VButtonEmits
} from './base/VButton.vue'

export type {
  VInputProps,
  VInputEmits
} from './base/VInput.vue'

export type {
  VTableProps,
  VTableEmits,
  TableColumn
} from './base/VTable.vue'

export type {
  VCardProps
} from './base/VCard.vue'

export type {
  VModalProps,
  VModalEmits
} from './base/VModal.vue'

// 設計系統
export {
  VidsparkDesignSystem,
  ThemeManager,
  type DesignSystemConfig,
  type ColorScheme,
  type FontConfig,
  type SpacingConfig,
  type BreakpointConfig,
  type ShadowConfig,
  type RadiusConfig,
  type AnimationConfig
} from '../design-system/VidsparkDesignSystem'

// 智能體相關
export {
  VidsparkDesignMasterAgent,
  type AgentResponse,
  type AgentState,
  type AgentEvent
} from '../agents/VidsparkDesignMasterAgent'

export {
  VidsparkDesignWorkflow,
  type WorkflowConfig,
  type WorkflowStep,
  type TaskInput,
  type AnalysisResult,
  type DesignPlan,
  type CodeGenerationResult
} from '../agents/workflows/VidsparkDesignWorkflow'

/**
 * 組件庫版本信息
 */
export const VERSION = '1.0.0'

/**
 * 組件庫配置
 */
export interface VidsparkComponentsConfig {
  /** 主題配置 */
  theme?: {
    /** 默認主題 */
    default?: 'light' | 'dark'
    /** 是否啟用自動主題切換 */
    auto?: boolean
  }
  /** 組件默認配置 */
  components?: {
    /** 按鈕組件默認配置 */
    button?: Partial<VButtonProps>
    /** 輸入框組件默認配置 */
    input?: Partial<VInputProps>
    /** 表格組件默認配置 */
    table?: Partial<VTableProps>
    /** 卡片組件默認配置 */
    card?: Partial<VCardProps>
    /** 模態框組件默認配置 */
    modal?: Partial<VModalProps>
  }
  /** 國際化配置 */
  i18n?: {
    /** 默認語言 */
    locale?: 'zh-TW' | 'zh-CN' | 'en-US'
    /** 語言包 */
    messages?: Record<string, Record<string, string>>
  }
}

/**
 * 默認配置
 */
export const defaultConfig: VidsparkComponentsConfig = {
  theme: {
    default: 'light',
    auto: true
  },
  components: {
    button: {
      variant: 'primary',
      size: 'md'
    },
    input: {
      variant: 'default',
      size: 'md'
    },
    table: {
      size: 'md',
      striped: true,
      pagination: false
    },
    card: {
      variant: 'default',
      size: 'md',
      rounded: true
    },
    modal: {
      size: 'md',
      centered: true,
      maskClosable: true
    }
  },
  i18n: {
    locale: 'zh-TW',
    messages: {
      'zh-TW': {
        'button.loading': '載入中...',
        'input.placeholder': '請輸入...',
        'table.empty': '暫無數據',
        'table.loading': '載入中...',
        'modal.close': '關閉',
        'pagination.prev': '上一頁',
        'pagination.next': '下一頁'
      },
      'zh-CN': {
        'button.loading': '加载中...',
        'input.placeholder': '请输入...',
        'table.empty': '暂无数据',
        'table.loading': '加载中...',
        'modal.close': '关闭',
        'pagination.prev': '上一页',
        'pagination.next': '下一页'
      },
      'en-US': {
        'button.loading': 'Loading...',
        'input.placeholder': 'Please enter...',
        'table.empty': 'No data',
        'table.loading': 'Loading...',
        'modal.close': 'Close',
        'pagination.prev': 'Previous',
        'pagination.next': 'Next'
      }
    }
  }
}

/**
 * 組件庫安裝函數
 * 用於 Vue 應用程序中安裝組件庫
 */
export function install(app: any, config: VidsparkComponentsConfig = {}) {
  // 合併配置
  const finalConfig = {
    ...defaultConfig,
    ...config,
    theme: { ...defaultConfig.theme, ...config.theme },
    components: { ...defaultConfig.components, ...config.components },
    i18n: { ...defaultConfig.i18n, ...config.i18n }
  }

  // 提供全局配置
  app.provide('vidspark-config', finalConfig)

  // 註冊全局組件
  app.component('VButton', VButton)
  app.component('VInput', VInput)
  app.component('VTable', VTable)
  app.component('VCard', VCard)
  app.component('VModal', VModal)

  // 初始化主題管理器
  const themeManager = new ThemeManager()
  if (finalConfig.theme?.default) {
    themeManager.setTheme(finalConfig.theme.default)
  }
  if (finalConfig.theme?.auto) {
    themeManager.enableAutoTheme()
  }

  // 提供主題管理器
  app.provide('theme-manager', themeManager)

  return app
}

/**
 * 組件庫工具函數
 */
export const utils = {
  /**
   * 生成唯一ID
   */
  generateId: (prefix = 'v') => {
    return `${prefix}-${Math.random().toString(36).substr(2, 9)}`
  },

  /**
   * 格式化文件大小
   */
  formatFileSize: (bytes: number): string => {
    if (bytes === 0) return '0 B'
    const k = 1024
    const sizes = ['B', 'KB', 'MB', 'GB', 'TB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
  },

  /**
   * 格式化日期
   */
  formatDate: (date: Date | string | number, format = 'YYYY-MM-DD'): string => {
    const d = new Date(date)
    const year = d.getFullYear()
    const month = String(d.getMonth() + 1).padStart(2, '0')
    const day = String(d.getDate()).padStart(2, '0')
    const hour = String(d.getHours()).padStart(2, '0')
    const minute = String(d.getMinutes()).padStart(2, '0')
    const second = String(d.getSeconds()).padStart(2, '0')

    return format
      .replace('YYYY', year.toString())
      .replace('MM', month)
      .replace('DD', day)
      .replace('HH', hour)
      .replace('mm', minute)
      .replace('ss', second)
  },

  /**
   * 防抖函數
   */
  debounce: <T extends (...args: any[]) => any>(
    func: T,
    wait: number
  ): ((...args: Parameters<T>) => void) => {
    let timeout: NodeJS.Timeout
    return (...args: Parameters<T>) => {
      clearTimeout(timeout)
      timeout = setTimeout(() => func.apply(null, args), wait)
    }
  },

  /**
   * 節流函數
   */
  throttle: <T extends (...args: any[]) => any>(
    func: T,
    limit: number
  ): ((...args: Parameters<T>) => void) => {
    let inThrottle: boolean
    return (...args: Parameters<T>) => {
      if (!inThrottle) {
        func.apply(null, args)
        inThrottle = true
        setTimeout(() => (inThrottle = false), limit)
      }
    }
  },

  /**
   * 深拷貝
   */
  deepClone: <T>(obj: T): T => {
    if (obj === null || typeof obj !== 'object') return obj
    if (obj instanceof Date) return new Date(obj.getTime()) as any
    if (obj instanceof Array) return obj.map(item => utils.deepClone(item)) as any
    if (typeof obj === 'object') {
      const clonedObj = {} as any
      for (const key in obj) {
        if (obj.hasOwnProperty(key)) {
          clonedObj[key] = utils.deepClone(obj[key])
        }
      }
      return clonedObj
    }
    return obj
  },

  /**
   * 檢查是否為移動設備
   */
  isMobile: (): boolean => {
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
      navigator.userAgent
    )
  },

  /**
   * 檢查是否支持觸摸
   */
  isTouchDevice: (): boolean => {
    return 'ontouchstart' in window || navigator.maxTouchPoints > 0
  },

  /**
   * 獲取瀏覽器信息
   */
  getBrowserInfo: () => {
    const ua = navigator.userAgent
    const browsers = {
      chrome: /Chrome/.test(ua) && /Google Inc/.test(navigator.vendor),
      firefox: /Firefox/.test(ua),
      safari: /Safari/.test(ua) && /Apple Computer/.test(navigator.vendor),
      edge: /Edg/.test(ua),
      ie: /Trident/.test(ua)
    }
    
    for (const [name, test] of Object.entries(browsers)) {
      if (test) return name
    }
    return 'unknown'
  }
}

/**
 * 導出所有內容作為默認導出
 */
export default {
  install,
  VERSION,
  defaultConfig,
  utils,
  // 組件
  VButton,
  VInput,
  VTable,
  VCard,
  VModal,
  // 設計系統
  VidsparkDesignSystem,
  ThemeManager,
  // 智能體
  VidsparkDesignMasterAgent,
  VidsparkDesignWorkflow
}