/**
 * Vidspark頁面設計大師 - 主入口文件
 * 整合智能體、組件庫、設計系統等所有模塊
 */

// 智能體相關導出
export {
  VidsparkDesignMasterAgent,
  VidsparkDesignWorkflow,
  WorkflowEngine,
  WorkflowEngineFactory,
  AgentConfigManager,
  AgentConfigValidator,
  SecurityValidator,
  SecurityMiddleware,
  VidsparkAgentFactory,
  VidsparkAgentManager,
  globalAgentManager,
  createAndRegisterAgent,
  getOrCreateAgent,
  DEFAULT_AGENT_CONFIG,
  SECURITY_CONFIG,
  PROMPT_TEMPLATES,
  AGENT_VERSION
} from './agents'

// 智能體類型導出
export type {
  AgentResponse,
  AgentState,
  AgentEvent,
  AgentTaskInput,
  WorkflowConfig,
  WorkflowStep,
  TaskInput,
  AnalysisResult,
  DesignPlan,
  CodeGenerationResult,
  WorkflowExecutionResult,
  WorkflowExecutionOptions,
  WorkflowExecutionEvent,
  WorkflowLog,
  VidsparkAgentConfig,
  AgentInfo,
  AgentCapabilities,
  AgentModelConfig,
  AgentSecurityConfig,
  AgentPerformanceConfig,
  AgentMonitoringConfig,
  SecurityValidationResult,
  CodeSecurityRule
} from './agents'

// 設計系統導出
export {
  VidsparkDesignSystem,
  ThemeManager,
  VIDSPARK_DESIGN_SYSTEM
} from './design-system/VidsparkDesignSystem'

export type {
  ColorPalette,
  Typography,
  Spacing,
  Breakpoints,
  Shadows,
  BorderRadius,
  Animation,
  Theme,
  ThemeConfig
} from './design-system/VidsparkDesignSystem'

// 組件庫導出
export {
  VButton,
  VInput,
  VTable,
  VCard,
  VModal,
  COMPONENT_VERSION,
  COMPONENT_CONFIG,
  DEFAULT_COMPONENT_CONFIG,
  installComponents,
  createComponentInstance,
  validateProps
} from './components'

export type {
  VButtonProps,
  VInputProps,
  VTableProps,
  VTableColumn,
  VCardProps,
  VModalProps,
  ComponentConfig,
  ComponentInstance
} from './components'

/**
 * Vidspark核心類
 * 提供統一的API接口
 */
export class Vidspark {
  private agentManager: VidsparkAgentManager
  private themeManager: ThemeManager
  private currentAgent?: VidsparkDesignMasterAgent
  private currentWorkflow?: WorkflowEngine

  constructor(config?: Partial<VidsparkAgentConfig>) {
    this.agentManager = new VidsparkAgentManager()
    this.themeManager = new ThemeManager()
    
    // 創建默認智能體
    if (config !== false) {
      this.initializeDefaultAgent(config)
    }
  }

  /**
   * 初始化默認智能體
   */
  private initializeDefaultAgent(config?: Partial<VidsparkAgentConfig>): void {
    const { agent, workflow, configManager } = createAndRegisterAgent(
      'default',
      config,
      true
    )
    
    this.currentAgent = agent
    this.currentWorkflow = workflow
    
    // 設置主題
    if (config?.designSystem) {
      this.themeManager.setTheme('custom', config.designSystem)
      this.themeManager.applyTheme('custom')
    } else {
      this.themeManager.applyTheme('light')
    }
  }

  /**
   * 創建新的智能體
   */
  createAgent(
    id: string,
    config?: Partial<VidsparkAgentConfig>
  ): VidsparkDesignMasterAgent {
    const { agent } = createAndRegisterAgent(id, config, true)
    return agent
  }

  /**
   * 切換當前智能體
   */
  switchAgent(id: string): boolean {
    const agent = this.agentManager.getAgent(id)
    const workflow = this.agentManager.getWorkflow(id)
    
    if (agent) {
      this.currentAgent = agent
      this.currentWorkflow = workflow
      return true
    }
    
    return false
  }

  /**
   * 獲取當前智能體
   */
  getCurrentAgent(): VidsparkDesignMasterAgent | undefined {
    return this.currentAgent
  }

  /**
   * 獲取當前工作流
   */
  getCurrentWorkflow(): WorkflowEngine | undefined {
    return this.currentWorkflow
  }

  /**
   * 處理設計任務
   */
  async processDesignTask(requirement: string): Promise<AgentResponse> {
    if (!this.currentAgent) {
      throw new Error('沒有可用的智能體')
    }

    return await this.currentAgent.processTask({
      type: 'design_request',
      data: { requirement }
    })
  }

  /**
   * 執行完整的設計工作流
   */
  async executeDesignWorkflow(
    requirement: string,
    options?: WorkflowExecutionOptions
  ): Promise<WorkflowExecutionResult> {
    if (!this.currentWorkflow) {
      throw new Error('沒有可用的工作流引擎')
    }

    return await this.currentWorkflow.execute(
      {
        type: 'design_request',
        requirement,
        timestamp: new Date()
      },
      options
    )
  }

  /**
   * 切換主題
   */
  switchTheme(theme: 'light' | 'dark' | string): void {
    this.themeManager.applyTheme(theme)
  }

  /**
   * 獲取主題管理器
   */
  getThemeManager(): ThemeManager {
    return this.themeManager
  }

  /**
   * 獲取智能體管理器
   */
  getAgentManager(): VidsparkAgentManager {
    return this.agentManager
  }

  /**
   * 驗證代碼安全性
   */
  validateCodeSecurity(code: string, filename?: string): SecurityValidationResult {
    return SecurityValidator.validateCode(code, filename)
  }

  /**
   * 獲取系統狀態
   */
  getSystemStatus(): {
    agents: ReturnType<VidsparkAgentManager['getStats']>
    theme: {
      current: string
      available: string[]
    }
    version: typeof AGENT_VERSION
  } {
    return {
      agents: this.agentManager.getStats(),
      theme: {
        current: this.themeManager.getCurrentTheme(),
        available: this.themeManager.getAvailableThemes()
      },
      version: AGENT_VERSION
    }
  }

  /**
   * 清理資源
   */
  dispose(): void {
    this.agentManager.clear()
    this.currentAgent = undefined
    this.currentWorkflow = undefined
  }
}

/**
 * 創建Vidspark實例的工廠函數
 */
export function createVidspark(config?: Partial<VidsparkAgentConfig>): Vidspark {
  return new Vidspark(config)
}

/**
 * 創建用於Trae AI平台的智能體配置
 */
export function createTraeAgentConfig(): {
  name: string
  description: string
  version: string
  capabilities: string[]
  config: VidsparkAgentConfig
  prompts: typeof PROMPT_TEMPLATES
} {
  return {
    name: 'Vidspark頁面設計大師',
    description: '專業的Vue.js頁面設計和代碼生成智能體，能夠理解用戶需求並生成高質量的前端代碼',
    version: AGENT_VERSION.version,
    capabilities: [
      '需求分析和理解',
      'Vue.js代碼生成',
      '響應式設計',
      '無障礙設計',
      '性能優化',
      '安全性驗證',
      '設計系統應用',
      '工作流管理'
    ],
    config: DEFAULT_AGENT_CONFIG,
    prompts: PROMPT_TEMPLATES
  }
}

/**
 * 智能體註冊函數（用於Trae AI平台）
 */
export function registerVidsparkAgent(): {
  agent: VidsparkDesignMasterAgent
  config: VidsparkAgentConfig
  workflow: WorkflowEngine
} {
  const { agent, workflow, configManager } = createAndRegisterAgent(
    'vidspark-main',
    DEFAULT_AGENT_CONFIG,
    true
  )
  
  return {
    agent,
    config: configManager.getConfig(),
    workflow: workflow!
  }
}

/**
 * 版本信息
 */
export const VERSION = {
  ...AGENT_VERSION,
  components: COMPONENT_VERSION,
  designSystem: '1.0.0'
}

/**
 * 默認導出Vidspark類
 */
export default Vidspark

/**
 * 全局實例（可選使用）
 */
let globalVidspark: Vidspark | null = null

/**
 * 獲取全局Vidspark實例
 */
export function getGlobalVidspark(): Vidspark {
  if (!globalVidspark) {
    globalVidspark = new Vidspark()
  }
  return globalVidspark
}

/**
 * 設置全局Vidspark實例
 */
export function setGlobalVidspark(instance: Vidspark): void {
  globalVidspark = instance
}

/**
 * 清理全局實例
 */
export function clearGlobalVidspark(): void {
  if (globalVidspark) {
    globalVidspark.dispose()
    globalVidspark = null
  }
}

/**
 * 便捷的API函數
 */
export const api = {
  /**
   * 快速創建頁面
   */
  async createPage(requirement: string): Promise<AgentResponse> {
    const vidspark = getGlobalVidspark()
    return await vidspark.processDesignTask(requirement)
  },

  /**
   * 快速執行設計工作流
   */
  async designWorkflow(
    requirement: string,
    options?: WorkflowExecutionOptions
  ): Promise<WorkflowExecutionResult> {
    const vidspark = getGlobalVidspark()
    return await vidspark.executeDesignWorkflow(requirement, options)
  },

  /**
   * 驗證代碼
   */
  validateCode(code: string, filename?: string): SecurityValidationResult {
    return SecurityValidator.validateCode(code, filename)
  },

  /**
   * 切換主題
   */
  switchTheme(theme: 'light' | 'dark' | string): void {
    const vidspark = getGlobalVidspark()
    vidspark.switchTheme(theme)
  },

  /**
   * 獲取系統狀態
   */
  getStatus() {
    const vidspark = getGlobalVidspark()
    return vidspark.getSystemStatus()
  }
}

/**
 * 類型守衛函數
 */
export function isVidsparkAgent(obj: any): obj is VidsparkDesignMasterAgent {
  return obj && typeof obj.processTask === 'function' && typeof obj.getState === 'function'
}

export function isWorkflowEngine(obj: any): obj is WorkflowEngine {
  return obj && typeof obj.execute === 'function' && typeof obj.getExecution === 'function'
}

export function isSecurityValidationResult(obj: any): obj is SecurityValidationResult {
  return obj && typeof obj.valid === 'boolean' && Array.isArray(obj.errors)
}

/**
 * 初始化函數（可選調用）
 */
export function initialize(config?: Partial<VidsparkAgentConfig>): Vidspark {
  const vidspark = createVidspark(config)
  setGlobalVidspark(vidspark)
  
  // 應用主題到DOM
  if (typeof document !== 'undefined') {
    vidspark.getThemeManager().applyTheme('light')
  }
  
  return vidspark
}

/**
 * 模塊信息
 */
export const MODULE_INFO = {
  name: 'Vidspark頁面設計大師',
  description: '基於AI的Vue.js頁面設計和代碼生成解決方案',
  author: 'Genhuman Team',
  license: 'MIT',
  repository: 'https://github.com/genhuman/vidspark',
  keywords: [
    'vue',
    'typescript',
    'ai',
    'code-generation',
    'design-system',
    'frontend',
    'ui-components'
  ],
  version: VERSION.version,
  buildDate: VERSION.buildDate
}