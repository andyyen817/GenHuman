/**
 * Vidspark頁面設計大師智能體核心實現
 * 與Trae AI平台集成的主要接口
 */

import { VidsparkWorkflowExecutor, TaskInput, AnalysisResult, DesignPlan, CodeGenerationResult } from './workflows/VidsparkDesignWorkflow'
import { VidsparkDesignMaster } from './VidsparkDesignMaster.json'

/**
 * 智能體響應接口
 */
export interface AgentResponse {
  success: boolean
  message: string
  data?: any
  error?: string
  metadata?: {
    execution_time: number
    step_count: number
    current_step: string
  }
}

/**
 * 智能體狀態枚舉
 */
export enum AgentStatus {
  IDLE = 'idle',
  PROCESSING = 'processing',
  COMPLETED = 'completed',
  ERROR = 'error'
}

/**
 * 智能體事件接口
 */
export interface AgentEvent {
  type: 'step_start' | 'step_complete' | 'step_error' | 'workflow_complete' | 'workflow_error'
  step?: string
  data?: any
  timestamp: number
}

/**
 * Vidspark設計大師智能體類
 */
export class VidsparkDesignMasterAgent {
  private workflowExecutor: VidsparkWorkflowExecutor
  private status: AgentStatus = AgentStatus.IDLE
  private currentTask: TaskInput | null = null
  private eventListeners: Map<string, Function[]> = new Map()
  private executionStartTime: number = 0

  constructor() {
    this.workflowExecutor = new VidsparkWorkflowExecutor()
    this.initializeEventListeners()
  }

  /**
   * 初始化事件監聽器
   */
  private initializeEventListeners(): void {
    // 可以在這裡添加默認的事件處理邏輯
  }

  /**
   * 添加事件監聽器
   */
  addEventListener(eventType: string, callback: Function): void {
    if (!this.eventListeners.has(eventType)) {
      this.eventListeners.set(eventType, [])
    }
    this.eventListeners.get(eventType)!.push(callback)
  }

  /**
   * 移除事件監聽器
   */
  removeEventListener(eventType: string, callback: Function): void {
    const listeners = this.eventListeners.get(eventType)
    if (listeners) {
      const index = listeners.indexOf(callback)
      if (index > -1) {
        listeners.splice(index, 1)
      }
    }
  }

  /**
   * 觸發事件
   */
  private emitEvent(event: AgentEvent): void {
    const listeners = this.eventListeners.get(event.type)
    if (listeners) {
      listeners.forEach(callback => {
        try {
          callback(event)
        } catch (error) {
          console.error('事件監聽器執行錯誤:', error)
        }
      })
    }
  }

  /**
   * 處理設計任務的主要入口點
   */
  async processDesignTask(input: TaskInput): Promise<AgentResponse> {
    try {
      this.status = AgentStatus.PROCESSING
      this.currentTask = input
      this.executionStartTime = Date.now()

      // 驗證輸入
      const validationResult = this.validateInput(input)
      if (!validationResult.success) {
        return validationResult
      }

      // 觸發工作流開始事件
      this.emitEvent({
        type: 'step_start',
        step: 'workflow_start',
        data: input,
        timestamp: Date.now()
      })

      // 執行工作流
      const result = await this.workflowExecutor.execute(input)

      this.status = AgentStatus.COMPLETED

      // 觸發工作流完成事件
      this.emitEvent({
        type: 'workflow_complete',
        data: result,
        timestamp: Date.now()
      })

      return {
        success: true,
        message: '設計任務完成',
        data: result,
        metadata: {
          execution_time: Date.now() - this.executionStartTime,
          step_count: this.workflowExecutor.getCurrentStep(),
          current_step: 'completed'
        }
      }
    } catch (error) {
      this.status = AgentStatus.ERROR
      
      // 觸發錯誤事件
      this.emitEvent({
        type: 'workflow_error',
        data: error,
        timestamp: Date.now()
      })

      return {
        success: false,
        message: '設計任務執行失敗',
        error: error instanceof Error ? error.message : String(error),
        metadata: {
          execution_time: Date.now() - this.executionStartTime,
          step_count: this.workflowExecutor.getCurrentStep(),
          current_step: 'error'
        }
      }
    }
  }

  /**
   * 驗證輸入數據
   */
  private validateInput(input: TaskInput): AgentResponse {
    const errors: string[] = []

    if (!input.page_name || input.page_name.trim().length === 0) {
      errors.push('頁面名稱不能為空')
    }

    if (!input.description || input.description.trim().length === 0) {
      errors.push('頁面描述不能為空')
    }

    if (!input.target_users || input.target_users.trim().length === 0) {
      errors.push('目標用戶不能為空')
    }

    if (!input.requirements || input.requirements.length === 0) {
      errors.push('功能需求不能為空')
    }

    if (input.page_name && input.page_name.length > 100) {
      errors.push('頁面名稱不能超過100個字符')
    }

    if (input.description && input.description.length > 1000) {
      errors.push('頁面描述不能超過1000個字符')
    }

    if (errors.length > 0) {
      return {
        success: false,
        message: '輸入驗證失敗',
        error: errors.join('; ')
      }
    }

    return {
      success: true,
      message: '輸入驗證通過'
    }
  }

  /**
   * 獲取智能體狀態
   */
  getStatus(): AgentStatus {
    return this.status
  }

  /**
   * 獲取當前任務
   */
  getCurrentTask(): TaskInput | null {
    return this.currentTask
  }

  /**
   * 獲取工作流進度
   */
  getProgress(): {
    current_step: number
    total_steps: number
    percentage: number
    step_name?: string
  } {
    const currentStep = this.workflowExecutor.getCurrentStep()
    const totalSteps = 5 // 工作流總步驟數
    
    return {
      current_step: currentStep,
      total_steps: totalSteps,
      percentage: Math.round((currentStep / totalSteps) * 100),
      step_name: this.getStepName(currentStep)
    }
  }

  /**
   * 獲取步驟名稱
   */
  private getStepName(stepIndex: number): string {
    const stepNames = [
      '需求分析',
      '設計規劃',
      '代碼生成',
      '優化調整',
      '驗證測試'
    ]
    return stepNames[stepIndex] || '未知步驟'
  }

  /**
   * 重置智能體狀態
   */
  reset(): void {
    this.status = AgentStatus.IDLE
    this.currentTask = null
    this.workflowExecutor.reset()
    this.executionStartTime = 0
  }

  /**
   * 獲取智能體信息
   */
  getAgentInfo(): any {
    return {
      name: 'Vidspark頁面設計大師',
      version: '1.0.0',
      description: '專業的頁面設計和開發智能體',
      capabilities: [
        '需求分析',
        '設計規劃',
        '代碼生成',
        '性能優化',
        '質量驗證'
      ],
      supported_page_types: ['靜態頁面', '控制台頁面', '混合頁面'],
      supported_tech_stacks: ['HTML/CSS/JS', 'Vue.js', '混合技術棧'],
      design_styles: ['靜態頁面風格', '控制台淺色主題', '控制台深色主題']
    }
  }

  /**
   * 處理聊天消息（Trae AI集成）
   */
  async handleChatMessage(message: string, context?: any): Promise<AgentResponse> {
    try {
      // 解析用戶消息，提取設計需求
      const taskInput = this.parseUserMessage(message, context)
      
      if (!taskInput) {
        return {
          success: false,
          message: '無法理解您的需求，請提供更詳細的頁面設計要求',
          error: '消息解析失敗'
        }
      }

      // 執行設計任務
      return await this.processDesignTask(taskInput)
    } catch (error) {
      return {
        success: false,
        message: '處理消息時發生錯誤',
        error: error instanceof Error ? error.message : String(error)
      }
    }
  }

  /**
   * 解析用戶消息，提取設計需求
   */
  private parseUserMessage(message: string, context?: any): TaskInput | null {
    try {
      // 簡單的消息解析邏輯
      // 在實際實現中，這裡可以使用更複雜的NLP技術
      
      const lines = message.split('\n').filter(line => line.trim())
      
      let pageName = ''
      let description = ''
      let targetUsers = ''
      const requirements: string[] = []
      
      for (const line of lines) {
        const trimmedLine = line.trim()
        
        if (trimmedLine.includes('頁面名稱') || trimmedLine.includes('頁面:')) {
          pageName = trimmedLine.split(':')[1]?.trim() || ''
        } else if (trimmedLine.includes('描述') || trimmedLine.includes('說明')) {
          description = trimmedLine.split(':')[1]?.trim() || ''
        } else if (trimmedLine.includes('用戶') || trimmedLine.includes('使用者')) {
          targetUsers = trimmedLine.split(':')[1]?.trim() || ''
        } else if (trimmedLine.includes('需求') || trimmedLine.includes('功能')) {
          const requirement = trimmedLine.split(':')[1]?.trim()
          if (requirement) {
            requirements.push(requirement)
          }
        } else if (trimmedLine.startsWith('-') || trimmedLine.startsWith('•')) {
          requirements.push(trimmedLine.substring(1).trim())
        }
      }

      // 如果沒有明確的結構，嘗試從整個消息中提取信息
      if (!pageName && !description) {
        // 假設第一行是頁面名稱或描述
        if (lines.length > 0) {
          const firstLine = lines[0].trim()
          if (firstLine.length < 50) {
            pageName = firstLine
          } else {
            description = firstLine
            pageName = '用戶自定義頁面'
          }
        }
        
        // 其餘行作為需求
        for (let i = 1; i < lines.length; i++) {
          const line = lines[i].trim()
          if (line) {
            requirements.push(line)
          }
        }
      }

      // 設置默認值
      if (!pageName) pageName = '用戶自定義頁面'
      if (!description) description = message.substring(0, 200)
      if (!targetUsers) targetUsers = '一般用戶'
      if (requirements.length === 0) requirements.push('基本頁面功能')

      return {
        page_name: pageName,
        description: description,
        target_users: targetUsers,
        requirements: requirements,
        constraints: context?.constraints || [],
        preferences: context?.preferences || {}
      }
    } catch (error) {
      console.error('解析用戶消息失敗:', error)
      return null
    }
  }

  /**
   * 生成設計建議
   */
  async generateDesignSuggestions(input: Partial<TaskInput>): Promise<AgentResponse> {
    try {
      const suggestions = {
        page_types: this.suggestPageTypes(input.description || ''),
        tech_stacks: this.suggestTechStacks(input.description || ''),
        design_styles: this.suggestDesignStyles(input.description || ''),
        components: this.suggestComponents(input.requirements || []),
        best_practices: this.getBestPractices()
      }

      return {
        success: true,
        message: '設計建議生成成功',
        data: suggestions
      }
    } catch (error) {
      return {
        success: false,
        message: '生成設計建議失敗',
        error: error instanceof Error ? error.message : String(error)
      }
    }
  }

  /**
   * 建議頁面類型
   */
  private suggestPageTypes(description: string): any[] {
    const suggestions = []
    const desc = description.toLowerCase()

    if (desc.includes('儀表板') || desc.includes('管理') || desc.includes('控制台')) {
      suggestions.push({
        type: 'console',
        name: '控制台頁面',
        description: '適合數據展示、管理操作的頁面',
        confidence: 0.9
      })
    }

    if (desc.includes('登陸') || desc.includes('註冊') || desc.includes('首頁')) {
      suggestions.push({
        type: 'static',
        name: '靜態頁面',
        description: '適合展示型、表單型的頁面',
        confidence: 0.8
      })
    }

    if (suggestions.length === 0) {
      suggestions.push({
        type: 'hybrid',
        name: '混合頁面',
        description: '結合靜態和動態元素的頁面',
        confidence: 0.6
      })
    }

    return suggestions
  }

  /**
   * 建議技術棧
   */
  private suggestTechStacks(description: string): any[] {
    return [
      {
        name: 'Vue.js',
        description: '現代化的前端框架，適合複雜交互',
        pros: ['組件化開發', '響應式數據', '豐富生態'],
        cons: ['學習曲線', '構建複雜度'],
        recommended: true
      },
      {
        name: 'HTML/CSS/JS',
        description: '原生技術棧，適合簡單頁面',
        pros: ['簡單直接', '加載快速', '兼容性好'],
        cons: ['開發效率低', '維護困難'],
        recommended: false
      }
    ]
  }

  /**
   * 建議設計風格
   */
  private suggestDesignStyles(description: string): any[] {
    return [
      {
        name: '現代簡約',
        description: '簡潔明了的設計風格',
        features: ['大量留白', '簡潔色彩', '清晰層次'],
        suitable_for: ['商業頁面', '產品展示']
      },
      {
        name: '控制台風格',
        description: '專業的管理界面風格',
        features: ['數據導向', '功能豐富', '高效操作'],
        suitable_for: ['管理系統', '數據分析']
      }
    ]
  }

  /**
   * 建議組件
   */
  private suggestComponents(requirements: string[]): any[] {
    const components = []
    const reqText = requirements.join(' ').toLowerCase()

    if (reqText.includes('按鈕') || reqText.includes('提交')) {
      components.push({ name: 'VButton', description: '按鈕組件' })
    }

    if (reqText.includes('表單') || reqText.includes('輸入')) {
      components.push({ name: 'VForm', description: '表單組件' })
    }

    if (reqText.includes('表格') || reqText.includes('列表')) {
      components.push({ name: 'VTable', description: '表格組件' })
    }

    return components
  }

  /**
   * 獲取最佳實踐
   */
  private getBestPractices(): string[] {
    return [
      '遵循響應式設計原則',
      '確保良好的用戶體驗',
      '保持代碼的可維護性',
      '實施性能優化策略',
      '遵循無障礙設計標準',
      '使用語義化的HTML結構',
      '實現適當的錯誤處理',
      '確保跨瀏覽器兼容性'
    ]
  }
}

// 導出智能體實例
export const vidsparkDesignMasterAgent = new VidsparkDesignMasterAgent()

// 導出類型定義
export type { AgentResponse, AgentEvent, TaskInput, AnalysisResult, DesignPlan, CodeGenerationResult }
export { AgentStatus }