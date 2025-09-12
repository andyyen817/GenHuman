/**
 * Vidspark頁面設計大師智能體配置
 * 定義智能體的基本信息、能力和配置參數
 */

import type { VidsparkDesignSystem } from '../../design-system/VidsparkDesignSystem'

/**
 * 智能體基本信息
 */
export interface AgentInfo {
  /** 智能體名稱 */
  name: string
  /** 智能體版本 */
  version: string
  /** 智能體描述 */
  description: string
  /** 智能體作者 */
  author: string
  /** 創建時間 */
  createdAt: Date
  /** 更新時間 */
  updatedAt: Date
  /** 智能體標籤 */
  tags: string[]
  /** 智能體圖標 */
  icon?: string
  /** 智能體封面 */
  cover?: string
}

/**
 * 智能體能力配置
 */
export interface AgentCapabilities {
  /** 支持的頁面類型 */
  supportedPageTypes: string[]
  /** 支持的技術棧 */
  supportedTechStacks: string[]
  /** 支持的設計風格 */
  supportedDesignStyles: string[]
  /** 支持的語言 */
  supportedLanguages: string[]
  /** 最大處理複雜度 */
  maxComplexity: number
  /** 並發處理能力 */
  concurrentTasks: number
  /** 是否支持實時預覽 */
  realTimePreview: boolean
  /** 是否支持代碼生成 */
  codeGeneration: boolean
  /** 是否支持設計系統 */
  designSystem: boolean
  /** 是否支持響應式設計 */
  responsiveDesign: boolean
  /** 是否支持無障礙設計 */
  accessibility: boolean
  /** 是否支持SEO優化 */
  seoOptimization: boolean
  /** 是否支持性能優化 */
  performanceOptimization: boolean
}

/**
 * 智能體模型配置
 */
export interface AgentModelConfig {
  /** 主要模型名稱 */
  primaryModel: string
  /** 備用模型列表 */
  fallbackModels: string[]
  /** 模型參數 */
  modelParams: {
    /** 溫度參數 */
    temperature: number
    /** 最大token數 */
    maxTokens: number
    /** top_p參數 */
    topP: number
    /** 頻率懲罰 */
    frequencyPenalty: number
    /** 存在懲罰 */
    presencePenalty: number
  }
  /** 提示詞配置 */
  promptConfig: {
    /** 系統提示詞 */
    systemPrompt: string
    /** 用戶提示詞模板 */
    userPromptTemplate: string
    /** 上下文窗口大小 */
    contextWindow: number
    /** 是否保留對話歷史 */
    keepHistory: boolean
    /** 歷史記錄數量 */
    historyLimit: number
  }
}

/**
 * 智能體安全配置
 */
export interface AgentSecurityConfig {
  /** 輸入驗證規則 */
  inputValidation: {
    /** 最大輸入長度 */
    maxInputLength: number
    /** 禁止的關鍵詞 */
    forbiddenKeywords: string[]
    /** 允許的文件類型 */
    allowedFileTypes: string[]
    /** 最大文件大小 */
    maxFileSize: number
  }
  /** 輸出過濾規則 */
  outputFiltering: {
    /** 是否過濾敏感內容 */
    filterSensitiveContent: boolean
    /** 是否驗證代碼安全性 */
    validateCodeSecurity: boolean
    /** 是否檢查惡意代碼 */
    checkMaliciousCode: boolean
  }
  /** 訪問控制 */
  accessControl: {
    /** 是否需要認證 */
    requireAuth: boolean
    /** 允許的用戶角色 */
    allowedRoles: string[]
    /** 速率限制 */
    rateLimit: {
      /** 每分鐘請求數 */
      requestsPerMinute: number
      /** 每小時請求數 */
      requestsPerHour: number
      /** 每天請求數 */
      requestsPerDay: number
    }
  }
}

/**
 * 智能體性能配置
 */
export interface AgentPerformanceConfig {
  /** 響應超時時間（毫秒） */
  responseTimeout: number
  /** 最大重試次數 */
  maxRetries: number
  /** 重試延遲（毫秒） */
  retryDelay: number
  /** 緩存配置 */
  cache: {
    /** 是否啟用緩存 */
    enabled: boolean
    /** 緩存過期時間（秒） */
    ttl: number
    /** 最大緩存條目數 */
    maxEntries: number
  }
  /** 並發控制 */
  concurrency: {
    /** 最大並發任務數 */
    maxConcurrentTasks: number
    /** 任務隊列大小 */
    queueSize: number
    /** 任務優先級 */
    taskPriority: 'fifo' | 'lifo' | 'priority'
  }
}

/**
 * 智能體監控配置
 */
export interface AgentMonitoringConfig {
  /** 是否啟用監控 */
  enabled: boolean
  /** 監控指標 */
  metrics: {
    /** 響應時間 */
    responseTime: boolean
    /** 成功率 */
    successRate: boolean
    /** 錯誤率 */
    errorRate: boolean
    /** 資源使用率 */
    resourceUsage: boolean
    /** 用戶滿意度 */
    userSatisfaction: boolean
  }
  /** 日誌配置 */
  logging: {
    /** 日誌級別 */
    level: 'debug' | 'info' | 'warn' | 'error'
    /** 是否記錄請求 */
    logRequests: boolean
    /** 是否記錄響應 */
    logResponses: boolean
    /** 是否記錄錯誤 */
    logErrors: boolean
    /** 日誌保留天數 */
    retentionDays: number
  }
  /** 告警配置 */
  alerts: {
    /** 錯誤率閾值 */
    errorRateThreshold: number
    /** 響應時間閾值 */
    responseTimeThreshold: number
    /** 告警通知方式 */
    notificationMethods: ('email' | 'webhook' | 'sms')[]
  }
}

/**
 * 完整的智能體配置
 */
export interface VidsparkAgentConfig {
  /** 基本信息 */
  info: AgentInfo
  /** 能力配置 */
  capabilities: AgentCapabilities
  /** 模型配置 */
  model: AgentModelConfig
  /** 安全配置 */
  security: AgentSecurityConfig
  /** 性能配置 */
  performance: AgentPerformanceConfig
  /** 監控配置 */
  monitoring: AgentMonitoringConfig
  /** 設計系統配置 */
  designSystem: VidsparkDesignSystem
  /** 自定義配置 */
  custom: Record<string, any>
}

/**
 * 默認智能體配置
 */
export const DEFAULT_AGENT_CONFIG: VidsparkAgentConfig = {
  info: {
    name: 'Vidspark頁面設計大師',
    version: '1.0.0',
    description: '專業的頁面設計和代碼生成智能體，能夠理解用戶需求並生成高質量的Vue.js頁面代碼',
    author: 'Genhuman Team',
    createdAt: new Date(),
    updatedAt: new Date(),
    tags: ['設計', '前端', 'Vue.js', '代碼生成', 'UI/UX'],
    icon: '🎨',
    cover: '/assets/vidspark-cover.jpg'
  },
  
  capabilities: {
    supportedPageTypes: [
      'landing-page',
      'dashboard',
      'form-page',
      'list-page',
      'detail-page',
      'profile-page',
      'settings-page',
      'auth-page',
      'error-page',
      'blog-page'
    ],
    supportedTechStacks: [
      'vue3',
      'typescript',
      'tailwindcss',
      'vite',
      'pinia',
      'vue-router',
      'element-plus',
      'ant-design-vue'
    ],
    supportedDesignStyles: [
      'modern',
      'minimalist',
      'corporate',
      'creative',
      'elegant',
      'playful',
      'professional',
      'artistic'
    ],
    supportedLanguages: ['zh-CN', 'zh-TW', 'en-US', 'ja-JP'],
    maxComplexity: 10,
    concurrentTasks: 5,
    realTimePreview: true,
    codeGeneration: true,
    designSystem: true,
    responsiveDesign: true,
    accessibility: true,
    seoOptimization: true,
    performanceOptimization: true
  },
  
  model: {
    primaryModel: 'claude-3-sonnet',
    fallbackModels: ['gpt-4', 'claude-3-haiku'],
    modelParams: {
      temperature: 0.7,
      maxTokens: 4096,
      topP: 0.9,
      frequencyPenalty: 0.1,
      presencePenalty: 0.1
    },
    promptConfig: {
      systemPrompt: '你是Vidspark頁面設計大師，專門負責理解用戶需求並生成高質量的Vue.js頁面代碼。',
      userPromptTemplate: '用戶需求：{requirement}\n\n請分析需求並生成相應的設計方案和代碼。',
      contextWindow: 8192,
      keepHistory: true,
      historyLimit: 10
    }
  },
  
  security: {
    inputValidation: {
      maxInputLength: 10000,
      forbiddenKeywords: [
        'eval',
        'Function',
        'script',
        'iframe',
        'document.write',
        'innerHTML',
        'outerHTML'
      ],
      allowedFileTypes: ['.vue', '.ts', '.js', '.css', '.scss', '.json', '.md'],
      maxFileSize: 1024 * 1024 // 1MB
    },
    outputFiltering: {
      filterSensitiveContent: true,
      validateCodeSecurity: true,
      checkMaliciousCode: true
    },
    accessControl: {
      requireAuth: false,
      allowedRoles: ['user', 'developer', 'designer', 'admin'],
      rateLimit: {
        requestsPerMinute: 30,
        requestsPerHour: 500,
        requestsPerDay: 2000
      }
    }
  },
  
  performance: {
    responseTimeout: 30000, // 30秒
    maxRetries: 3,
    retryDelay: 1000, // 1秒
    cache: {
      enabled: true,
      ttl: 3600, // 1小時
      maxEntries: 1000
    },
    concurrency: {
      maxConcurrentTasks: 5,
      queueSize: 100,
      taskPriority: 'priority'
    }
  },
  
  monitoring: {
    enabled: true,
    metrics: {
      responseTime: true,
      successRate: true,
      errorRate: true,
      resourceUsage: true,
      userSatisfaction: true
    },
    logging: {
      level: 'info',
      logRequests: true,
      logResponses: false,
      logErrors: true,
      retentionDays: 30
    },
    alerts: {
      errorRateThreshold: 0.05, // 5%
      responseTimeThreshold: 10000, // 10秒
      notificationMethods: ['webhook']
    }
  },
  
  designSystem: {
    colors: {
      primary: {
        50: '#f0f9ff',
        100: '#e0f2fe',
        200: '#bae6fd',
        300: '#7dd3fc',
        400: '#38bdf8',
        500: '#0ea5e9',
        600: '#0284c7',
        700: '#0369a1',
        800: '#075985',
        900: '#0c4a6e'
      },
      secondary: {
        50: '#f8fafc',
        100: '#f1f5f9',
        200: '#e2e8f0',
        300: '#cbd5e1',
        400: '#94a3b8',
        500: '#64748b',
        600: '#475569',
        700: '#334155',
        800: '#1e293b',
        900: '#0f172a'
      },
      success: {
        50: '#f0fdf4',
        500: '#22c55e',
        900: '#14532d'
      },
      warning: {
        50: '#fffbeb',
        500: '#f59e0b',
        900: '#78350f'
      },
      error: {
        50: '#fef2f2',
        500: '#ef4444',
        900: '#7f1d1d'
      }
    },
    typography: {
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
        mono: ['JetBrains Mono', 'monospace']
      },
      fontSize: {
        xs: '0.75rem',
        sm: '0.875rem',
        base: '1rem',
        lg: '1.125rem',
        xl: '1.25rem',
        '2xl': '1.5rem',
        '3xl': '1.875rem',
        '4xl': '2.25rem'
      }
    },
    spacing: {
      xs: '0.25rem',
      sm: '0.5rem',
      md: '1rem',
      lg: '1.5rem',
      xl: '2rem',
      '2xl': '3rem'
    },
    borderRadius: {
      none: '0',
      sm: '0.25rem',
      md: '0.375rem',
      lg: '0.5rem',
      xl: '0.75rem',
      full: '9999px'
    },
    shadows: {
      sm: '0 1px 2px 0 rgb(0 0 0 / 0.05)',
      md: '0 4px 6px -1px rgb(0 0 0 / 0.1)',
      lg: '0 10px 15px -3px rgb(0 0 0 / 0.1)',
      xl: '0 20px 25px -5px rgb(0 0 0 / 0.1)'
    },
    breakpoints: {
      sm: '640px',
      md: '768px',
      lg: '1024px',
      xl: '1280px',
      '2xl': '1536px'
    },
    animation: {
      duration: {
        fast: '150ms',
        normal: '300ms',
        slow: '500ms'
      },
      easing: {
        ease: 'ease',
        linear: 'linear',
        'ease-in': 'ease-in',
        'ease-out': 'ease-out',
        'ease-in-out': 'ease-in-out'
      }
    }
  } as VidsparkDesignSystem,
  
  custom: {
    // 可以添加項目特定的配置
    projectName: 'Vidspark',
    environment: 'development',
    features: {
      darkMode: true,
      i18n: true,
      analytics: false
    }
  }
}

/**
 * 配置驗證器
 */
export class AgentConfigValidator {
  /**
   * 驗證配置的完整性和正確性
   */
  static validate(config: Partial<VidsparkAgentConfig>): {
    valid: boolean
    errors: string[]
    warnings: string[]
  } {
    const errors: string[] = []
    const warnings: string[] = []

    // 驗證基本信息
    if (!config.info?.name) {
      errors.push('智能體名稱不能為空')
    }
    if (!config.info?.version) {
      errors.push('智能體版本不能為空')
    }

    // 驗證模型配置
    if (!config.model?.primaryModel) {
      errors.push('主要模型不能為空')
    }
    if (config.model?.modelParams?.temperature && 
        (config.model.modelParams.temperature < 0 || config.model.modelParams.temperature > 2)) {
      errors.push('溫度參數必須在0-2之間')
    }

    // 驗證安全配置
    if (config.security?.inputValidation?.maxInputLength && 
        config.security.inputValidation.maxInputLength < 100) {
      warnings.push('最大輸入長度可能過小')
    }

    // 驗證性能配置
    if (config.performance?.responseTimeout && 
        config.performance.responseTimeout < 5000) {
      warnings.push('響應超時時間可能過短')
    }

    return {
      valid: errors.length === 0,
      errors,
      warnings
    }
  }

  /**
   * 合併配置
   */
  static merge(
    baseConfig: VidsparkAgentConfig,
    overrideConfig: Partial<VidsparkAgentConfig>
  ): VidsparkAgentConfig {
    return {
      ...baseConfig,
      ...overrideConfig,
      info: { ...baseConfig.info, ...overrideConfig.info },
      capabilities: { ...baseConfig.capabilities, ...overrideConfig.capabilities },
      model: {
        ...baseConfig.model,
        ...overrideConfig.model,
        modelParams: { ...baseConfig.model.modelParams, ...overrideConfig.model?.modelParams },
        promptConfig: { ...baseConfig.model.promptConfig, ...overrideConfig.model?.promptConfig }
      },
      security: {
        ...baseConfig.security,
        ...overrideConfig.security,
        inputValidation: { ...baseConfig.security.inputValidation, ...overrideConfig.security?.inputValidation },
        outputFiltering: { ...baseConfig.security.outputFiltering, ...overrideConfig.security?.outputFiltering },
        accessControl: {
          ...baseConfig.security.accessControl,
          ...overrideConfig.security?.accessControl,
          rateLimit: { ...baseConfig.security.accessControl.rateLimit, ...overrideConfig.security?.accessControl?.rateLimit }
        }
      },
      performance: {
        ...baseConfig.performance,
        ...overrideConfig.performance,
        cache: { ...baseConfig.performance.cache, ...overrideConfig.performance?.cache },
        concurrency: { ...baseConfig.performance.concurrency, ...overrideConfig.performance?.concurrency }
      },
      monitoring: {
        ...baseConfig.monitoring,
        ...overrideConfig.monitoring,
        metrics: { ...baseConfig.monitoring.metrics, ...overrideConfig.monitoring?.metrics },
        logging: { ...baseConfig.monitoring.logging, ...overrideConfig.monitoring?.logging },
        alerts: { ...baseConfig.monitoring.alerts, ...overrideConfig.monitoring?.alerts }
      },
      designSystem: { ...baseConfig.designSystem, ...overrideConfig.designSystem },
      custom: { ...baseConfig.custom, ...overrideConfig.custom }
    }
  }
}

/**
 * 配置管理器
 */
export class AgentConfigManager {
  private config: VidsparkAgentConfig
  private listeners: ((config: VidsparkAgentConfig) => void)[] = []

  constructor(initialConfig: Partial<VidsparkAgentConfig> = {}) {
    this.config = AgentConfigValidator.merge(DEFAULT_AGENT_CONFIG, initialConfig)
  }

  /**
   * 獲取當前配置
   */
  getConfig(): VidsparkAgentConfig {
    return { ...this.config }
  }

  /**
   * 更新配置
   */
  updateConfig(updates: Partial<VidsparkAgentConfig>): void {
    const newConfig = AgentConfigValidator.merge(this.config, updates)
    const validation = AgentConfigValidator.validate(newConfig)
    
    if (!validation.valid) {
      throw new Error(`配置驗證失敗: ${validation.errors.join(', ')}`)
    }
    
    if (validation.warnings.length > 0) {
      console.warn('配置警告:', validation.warnings.join(', '))
    }
    
    this.config = newConfig
    this.config.info.updatedAt = new Date()
    
    // 通知監聽器
    this.listeners.forEach(listener => listener(this.config))
  }

  /**
   * 添加配置變更監聽器
   */
  addListener(listener: (config: VidsparkAgentConfig) => void): void {
    this.listeners.push(listener)
  }

  /**
   * 移除配置變更監聽器
   */
  removeListener(listener: (config: VidsparkAgentConfig) => void): void {
    const index = this.listeners.indexOf(listener)
    if (index > -1) {
      this.listeners.splice(index, 1)
    }
  }

  /**
   * 重置為默認配置
   */
  reset(): void {
    this.config = { ...DEFAULT_AGENT_CONFIG }
    this.listeners.forEach(listener => listener(this.config))
  }

  /**
   * 導出配置為JSON
   */
  exportConfig(): string {
    return JSON.stringify(this.config, null, 2)
  }

  /**
   * 從JSON導入配置
   */
  importConfig(jsonConfig: string): void {
    try {
      const config = JSON.parse(jsonConfig)
      this.updateConfig(config)
    } catch (error) {
      throw new Error(`配置導入失敗: ${error instanceof Error ? error.message : '未知錯誤'}`)
    }
  }
}