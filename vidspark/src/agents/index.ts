/**
 * Vidspark頁面設計大師智能體 - 主入口文件
 * 整合所有模塊並提供統一的接口
 */

// 核心智能體
export { VidsparkDesignMasterAgent } from './VidsparkDesignMasterAgent'
export type {
  AgentResponse,
  AgentState,
  AgentEvent,
  TaskInput as AgentTaskInput
} from './VidsparkDesignMasterAgent'

// 工作流系統
export { VidsparkDesignWorkflow } from './workflows/VidsparkDesignWorkflow'
export {
  WorkflowEngine,
  WorkflowEngineFactory,
  WorkflowExecutionState
} from './workflows/WorkflowEngine'
export type {
  WorkflowConfig,
  WorkflowStep,
  TaskInput,
  AnalysisResult,
  DesignPlan,
  CodeGenerationResult,
  WorkflowExecutionResult,
  WorkflowExecutionOptions,
  WorkflowExecutionEvent,
  WorkflowLog
} from './workflows/VidsparkDesignWorkflow'

// 配置管理
export {
  AgentConfigManager,
  AgentConfigValidator,
  DEFAULT_AGENT_CONFIG
} from './config/AgentConfig'
export type {
  VidsparkAgentConfig,
  AgentInfo,
  AgentCapabilities,
  AgentModelConfig,
  AgentSecurityConfig,
  AgentPerformanceConfig,
  AgentMonitoringConfig
} from './config/AgentConfig'

// 安全驗證
export {
  SecurityValidator,
  SecurityMiddleware,
  SECURITY_CONFIG
} from './security/SecurityValidator'
export type {
  SecurityValidationResult,
  CodeSecurityRule
} from './security/SecurityValidator'

// 提示詞模板（從文件讀取）
export const PROMPT_TEMPLATES = {
  SYSTEM_PROMPT: `你是Vidspark頁面設計大師，一個專業的前端設計和開發智能體。

你的核心能力包括：
1. 理解用戶的頁面設計需求
2. 分析設計要求並提供專業建議
3. 生成高質量的Vue.js代碼
4. 遵循現代前端開發最佳實踐
5. 確保代碼的安全性和可維護性

請始終遵循以下原則：
- 使用Vue 3 Composition API
- 採用TypeScript進行類型安全
- 遵循響應式設計原則
- 確保無障礙設計
- 優化性能和SEO
- 遵循安全編碼規範`,

  REQUIREMENT_ANALYSIS: `請分析以下用戶需求：

需求描述：{requirement}

請從以下維度進行分析：
1. 頁面類型和功能需求
2. 設計風格和視覺要求
3. 技術實現方案
4. 響應式和無障礙要求
5. 性能和SEO考慮

請提供詳細的分析結果和實現建議。`,

  DESIGN_PLANNING: `基於需求分析結果，請制定詳細的設計方案：

分析結果：{analysisResult}

請包含以下內容：
1. 頁面結構和佈局設計
2. 組件拆分和復用策略
3. 數據流和狀態管理
4. 樣式系統和主題設計
5. 交互設計和用戶體驗
6. 技術選型和架構決策`,

  CODE_GENERATION: `請根據設計方案生成Vue.js代碼：

設計方案：{designPlan}

代碼要求：
1. 使用Vue 3 + TypeScript
2. 採用Composition API
3. 遵循組件化開發
4. 包含完整的類型定義
5. 添加適當的註釋
6. 確保代碼安全性
7. 支持響應式設計
8. 包含無障礙功能`,

  OPTIMIZATION: `請優化以下代碼：

當前代碼：{currentCode}

優化目標：
1. 性能優化
2. 代碼質量提升
3. 安全性加強
4. 可維護性改善
5. 用戶體驗優化

請提供優化後的代碼和改進說明。`,

  VALIDATION: `請驗證以下實現結果：

實現結果：{result}

驗證項目：
1. 功能完整性
2. 代碼質量
3. 安全性檢查
4. 性能評估
5. 用戶體驗
6. 無障礙性
7. 響應式設計

請提供詳細的驗證報告和改進建議。`
}

/**
 * 智能體工廠類
 * 提供創建和配置智能體的便捷方法
 */
export class VidsparkAgentFactory {
  /**
   * 創建標準的Vidspark設計智能體
   */
  static createStandardAgent(config?: Partial<VidsparkAgentConfig>): VidsparkDesignMasterAgent {
    const configManager = new AgentConfigManager(config)
    const agent = new VidsparkDesignMasterAgent()
    
    // 應用配置
    agent.updateConfig(configManager.getConfig())
    
    return agent
  }

  /**
   * 創建帶工作流的智能體
   */
  static createAgentWithWorkflow(config?: Partial<VidsparkAgentConfig>): {
    agent: VidsparkDesignMasterAgent
    workflow: VidsparkDesignWorkflow
    engine: WorkflowEngine
  } {
    const agent = this.createStandardAgent(config)
    const workflow = new VidsparkDesignWorkflow()
    const engine = new WorkflowEngine(workflow, agent)
    
    return { agent, workflow, engine }
  }

  /**
   * 創建安全增強的智能體
   */
  static createSecureAgent(config?: Partial<VidsparkAgentConfig>): VidsparkDesignMasterAgent {
    const secureConfig = {
      ...config,
      security: {
        ...DEFAULT_AGENT_CONFIG.security,
        ...config?.security,
        inputValidation: {
          ...DEFAULT_AGENT_CONFIG.security.inputValidation,
          ...config?.security?.inputValidation,
          maxInputLength: Math.min(
            config?.security?.inputValidation?.maxInputLength || SECURITY_CONFIG.MAX_INPUT_LENGTH,
            SECURITY_CONFIG.MAX_INPUT_LENGTH
          )
        },
        outputFiltering: {
          ...DEFAULT_AGENT_CONFIG.security.outputFiltering,
          ...config?.security?.outputFiltering,
          filterSensitiveContent: true,
          validateCodeSecurity: true,
          checkMaliciousCode: true
        }
      }
    }
    
    return this.createStandardAgent(secureConfig)
  }

  /**
   * 創建高性能智能體
   */
  static createPerformanceAgent(config?: Partial<VidsparkAgentConfig>): VidsparkDesignMasterAgent {
    const performanceConfig = {
      ...config,
      performance: {
        ...DEFAULT_AGENT_CONFIG.performance,
        ...config?.performance,
        cache: {
          ...DEFAULT_AGENT_CONFIG.performance.cache,
          ...config?.performance?.cache,
          enabled: true,
          ttl: 7200, // 2小時
          maxEntries: 2000
        },
        concurrency: {
          ...DEFAULT_AGENT_CONFIG.performance.concurrency,
          ...config?.performance?.concurrency,
          maxConcurrentTasks: 10,
          queueSize: 200
        }
      }
    }
    
    return this.createStandardAgent(performanceConfig)
  }
}

/**
 * 智能體管理器
 * 管理多個智能體實例
 */
export class VidsparkAgentManager {
  private agents: Map<string, VidsparkDesignMasterAgent> = new Map()
  private workflows: Map<string, WorkflowEngine> = new Map()
  private configs: Map<string, AgentConfigManager> = new Map()

  /**
   * 註冊智能體
   */
  registerAgent(
    id: string,
    agent: VidsparkDesignMasterAgent,
    workflow?: WorkflowEngine,
    config?: AgentConfigManager
  ): void {
    this.agents.set(id, agent)
    
    if (workflow) {
      this.workflows.set(id, workflow)
    }
    
    if (config) {
      this.configs.set(id, config)
    }
  }

  /**
   * 獲取智能體
   */
  getAgent(id: string): VidsparkDesignMasterAgent | undefined {
    return this.agents.get(id)
  }

  /**
   * 獲取工作流引擎
   */
  getWorkflow(id: string): WorkflowEngine | undefined {
    return this.workflows.get(id)
  }

  /**
   * 獲取配置管理器
   */
  getConfig(id: string): AgentConfigManager | undefined {
    return this.configs.get(id)
  }

  /**
   * 移除智能體
   */
  removeAgent(id: string): boolean {
    const removed = this.agents.delete(id)
    this.workflows.delete(id)
    this.configs.delete(id)
    return removed
  }

  /**
   * 獲取所有智能體ID
   */
  getAgentIds(): string[] {
    return Array.from(this.agents.keys())
  }

  /**
   * 清理所有智能體
   */
  clear(): void {
    this.agents.clear()
    this.workflows.clear()
    this.configs.clear()
  }

  /**
   * 獲取智能體統計信息
   */
  getStats(): {
    totalAgents: number
    activeAgents: number
    totalWorkflows: number
    totalConfigs: number
  } {
    let activeAgents = 0
    
    for (const agent of this.agents.values()) {
      if (agent.getState() !== 'idle') {
        activeAgents++
      }
    }
    
    return {
      totalAgents: this.agents.size,
      activeAgents,
      totalWorkflows: this.workflows.size,
      totalConfigs: this.configs.size
    }
  }
}

/**
 * 全局智能體管理器實例
 */
export const globalAgentManager = new VidsparkAgentManager()

/**
 * 便捷函數：創建並註冊智能體
 */
export function createAndRegisterAgent(
  id: string,
  config?: Partial<VidsparkAgentConfig>,
  withWorkflow: boolean = true
): {
  agent: VidsparkDesignMasterAgent
  workflow?: WorkflowEngine
  configManager: AgentConfigManager
} {
  const configManager = new AgentConfigManager(config)
  
  if (withWorkflow) {
    const { agent, workflow } = VidsparkAgentFactory.createAgentWithWorkflow(config)
    globalAgentManager.registerAgent(id, agent, workflow, configManager)
    return { agent, workflow, configManager }
  } else {
    const agent = VidsparkAgentFactory.createStandardAgent(config)
    globalAgentManager.registerAgent(id, agent, undefined, configManager)
    return { agent, configManager }
  }
}

/**
 * 便捷函數：獲取或創建智能體
 */
export function getOrCreateAgent(
  id: string,
  config?: Partial<VidsparkAgentConfig>
): VidsparkDesignMasterAgent {
  let agent = globalAgentManager.getAgent(id)
  
  if (!agent) {
    const result = createAndRegisterAgent(id, config)
    agent = result.agent
  }
  
  return agent
}

/**
 * 智能體版本信息
 */
export const AGENT_VERSION = {
  version: '1.0.0',
  buildDate: new Date().toISOString(),
  features: [
    'Vue 3 + TypeScript支持',
    '智能需求分析',
    '自動代碼生成',
    '安全性驗證',
    '響應式設計',
    '無障礙支持',
    '性能優化',
    '工作流管理'
  ],
  compatibility: {
    vue: '^3.0.0',
    typescript: '^4.0.0',
    node: '^16.0.0'
  }
}

/**
 * 導出所有類型定義
 */
export type {
  VidsparkDesignSystem
} from '../design-system/VidsparkDesignSystem'

export type {
  VButtonProps,
  VInputProps,
  VTableProps,
  VCardProps,
  VModalProps
} from '../components'

// 默認導出主要的智能體類
export default VidsparkDesignMasterAgent