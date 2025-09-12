/**
 * 工作流執行引擎
 * 負責協調和執行整個設計工作流程
 */

import { VidsparkDesignWorkflow, type WorkflowConfig, type WorkflowStep, type TaskInput } from './VidsparkDesignWorkflow'
import { VidsparkDesignMasterAgent, type AgentResponse, type AgentState } from '../VidsparkDesignMasterAgent'

/**
 * 工作流執行狀態
 */
export enum WorkflowExecutionState {
  IDLE = 'idle',
  RUNNING = 'running',
  PAUSED = 'paused',
  COMPLETED = 'completed',
  FAILED = 'failed',
  CANCELLED = 'cancelled'
}

/**
 * 工作流執行結果
 */
export interface WorkflowExecutionResult {
  /** 執行ID */
  executionId: string
  /** 執行狀態 */
  state: WorkflowExecutionState
  /** 開始時間 */
  startTime: Date
  /** 結束時間 */
  endTime?: Date
  /** 執行持續時間（毫秒） */
  duration?: number
  /** 當前步驟索引 */
  currentStepIndex: number
  /** 已完成的步驟 */
  completedSteps: string[]
  /** 步驟執行結果 */
  stepResults: Record<string, any>
  /** 最終結果 */
  finalResult?: any
  /** 錯誤信息 */
  error?: string
  /** 執行日誌 */
  logs: WorkflowLog[]
}

/**
 * 工作流日誌
 */
export interface WorkflowLog {
  /** 時間戳 */
  timestamp: Date
  /** 日誌級別 */
  level: 'info' | 'warn' | 'error' | 'debug'
  /** 步驟名稱 */
  step?: string
  /** 日誌消息 */
  message: string
  /** 額外數據 */
  data?: any
}

/**
 * 工作流執行選項
 */
export interface WorkflowExecutionOptions {
  /** 是否自動重試失敗的步驟 */
  autoRetry?: boolean
  /** 最大重試次數 */
  maxRetries?: number
  /** 重試延遲（毫秒） */
  retryDelay?: number
  /** 是否並行執行可並行的步驟 */
  parallel?: boolean
  /** 執行超時時間（毫秒） */
  timeout?: number
  /** 是否保存中間結果 */
  saveIntermediateResults?: boolean
  /** 自定義步驟處理器 */
  customHandlers?: Record<string, (input: any) => Promise<any>>
}

/**
 * 工作流執行事件
 */
export interface WorkflowExecutionEvent {
  /** 事件類型 */
  type: 'started' | 'step-started' | 'step-completed' | 'step-failed' | 'paused' | 'resumed' | 'completed' | 'failed' | 'cancelled'
  /** 執行ID */
  executionId: string
  /** 步驟名稱（如果適用） */
  stepName?: string
  /** 事件數據 */
  data?: any
  /** 時間戳 */
  timestamp: Date
}

/**
 * 工作流執行引擎類
 */
export class WorkflowEngine {
  private workflow: VidsparkDesignWorkflow
  private agent: VidsparkDesignMasterAgent
  private executions: Map<string, WorkflowExecutionResult> = new Map()
  private eventListeners: Map<string, ((event: WorkflowExecutionEvent) => void)[]> = new Map()

  constructor(
    workflow: VidsparkDesignWorkflow,
    agent: VidsparkDesignMasterAgent
  ) {
    this.workflow = workflow
    this.agent = agent
  }

  /**
   * 執行工作流
   */
  async execute(
    input: TaskInput,
    options: WorkflowExecutionOptions = {}
  ): Promise<WorkflowExecutionResult> {
    const executionId = this.generateExecutionId()
    const execution: WorkflowExecutionResult = {
      executionId,
      state: WorkflowExecutionState.IDLE,
      startTime: new Date(),
      currentStepIndex: 0,
      completedSteps: [],
      stepResults: {},
      logs: []
    }

    this.executions.set(executionId, execution)

    try {
      // 開始執行
      execution.state = WorkflowExecutionState.RUNNING
      this.log(execution, 'info', '工作流開始執行', { input, options })
      this.emitEvent({
        type: 'started',
        executionId,
        data: { input, options },
        timestamp: new Date()
      })

      // 獲取工作流配置
      const config = this.workflow.getConfig()
      const steps = config.steps

      // 執行步驟
      let currentInput = input
      for (let i = 0; i < steps.length; i++) {
        const step = steps[i]
        execution.currentStepIndex = i

        try {
          // 檢查是否被取消
          if (execution.state === WorkflowExecutionState.CANCELLED) {
            throw new Error('工作流執行被取消')
          }

          // 檢查超時
          if (options.timeout) {
            const elapsed = Date.now() - execution.startTime.getTime()
            if (elapsed > options.timeout) {
              throw new Error(`工作流執行超時（${options.timeout}ms）`)
            }
          }

          this.log(execution, 'info', `開始執行步驟: ${step.name}`)
          this.emitEvent({
            type: 'step-started',
            executionId,
            stepName: step.name,
            timestamp: new Date()
          })

          // 執行步驟
          const stepResult = await this.executeStep(step, currentInput, options)
          
          // 保存步驟結果
          execution.stepResults[step.name] = stepResult
          execution.completedSteps.push(step.name)
          
          // 更新輸入為下一步驟
          if (stepResult && typeof stepResult === 'object') {
            currentInput = { ...currentInput, ...stepResult }
          }

          this.log(execution, 'info', `步驟執行完成: ${step.name}`, stepResult)
          this.emitEvent({
            type: 'step-completed',
            executionId,
            stepName: step.name,
            data: stepResult,
            timestamp: new Date()
          })

        } catch (stepError) {
          this.log(execution, 'error', `步驟執行失敗: ${step.name}`, stepError)
          
          // 處理重試
          if (options.autoRetry && (options.maxRetries || 3) > 0) {
            this.log(execution, 'info', `重試步驟: ${step.name}`)
            
            if (options.retryDelay) {
              await this.delay(options.retryDelay)
            }
            
            // 遞歸重試（減少重試次數）
            const retryOptions = {
              ...options,
              maxRetries: (options.maxRetries || 3) - 1
            }
            
            try {
              const retryResult = await this.executeStep(step, currentInput, retryOptions)
              execution.stepResults[step.name] = retryResult
              execution.completedSteps.push(step.name)
              
              if (retryResult && typeof retryResult === 'object') {
                currentInput = { ...currentInput, ...retryResult }
              }
              
              this.log(execution, 'info', `步驟重試成功: ${step.name}`, retryResult)
              continue
            } catch (retryError) {
              this.log(execution, 'error', `步驟重試失敗: ${step.name}`, retryError)
            }
          }

          // 發送步驟失敗事件
          this.emitEvent({
            type: 'step-failed',
            executionId,
            stepName: step.name,
            data: stepError,
            timestamp: new Date()
          })

          throw stepError
        }
      }

      // 執行完成
      execution.state = WorkflowExecutionState.COMPLETED
      execution.endTime = new Date()
      execution.duration = execution.endTime.getTime() - execution.startTime.getTime()
      execution.finalResult = currentInput

      this.log(execution, 'info', '工作流執行完成', execution.finalResult)
      this.emitEvent({
        type: 'completed',
        executionId,
        data: execution.finalResult,
        timestamp: new Date()
      })

    } catch (error) {
      // 執行失敗
      execution.state = WorkflowExecutionState.FAILED
      execution.endTime = new Date()
      execution.duration = execution.endTime.getTime() - execution.startTime.getTime()
      execution.error = error instanceof Error ? error.message : String(error)

      this.log(execution, 'error', '工作流執行失敗', error)
      this.emitEvent({
        type: 'failed',
        executionId,
        data: error,
        timestamp: new Date()
      })
    }

    return execution
  }

  /**
   * 執行單個步驟
   */
  private async executeStep(
    step: WorkflowStep,
    input: any,
    options: WorkflowExecutionOptions
  ): Promise<any> {
    // 檢查是否有自定義處理器
    if (options.customHandlers && options.customHandlers[step.name]) {
      return await options.customHandlers[step.name](input)
    }

    // 使用智能體執行步驟
    switch (step.name) {
      case 'analyze_requirements':
        return await this.workflow.analyzeRequirements(input)
      
      case 'create_design_plan':
        return await this.workflow.createDesignPlan(input)
      
      case 'generate_code':
        return await this.workflow.generateCode(input)
      
      case 'optimize_design':
        return await this.workflow.optimizeDesign(input)
      
      case 'validate_result':
        return await this.workflow.validateResult(input)
      
      default:
        // 使用智能體處理未知步驟
        const response = await this.agent.processTask({
          type: 'custom_step',
          data: {
            stepName: step.name,
            stepConfig: step,
            input
          }
        })
        
        if (response.success) {
          return response.data
        } else {
          throw new Error(response.error || `步驟執行失敗: ${step.name}`)
        }
    }
  }

  /**
   * 暫停工作流執行
   */
  async pause(executionId: string): Promise<boolean> {
    const execution = this.executions.get(executionId)
    if (!execution || execution.state !== WorkflowExecutionState.RUNNING) {
      return false
    }

    execution.state = WorkflowExecutionState.PAUSED
    this.log(execution, 'info', '工作流執行已暫停')
    this.emitEvent({
      type: 'paused',
      executionId,
      timestamp: new Date()
    })

    return true
  }

  /**
   * 恢復工作流執行
   */
  async resume(executionId: string): Promise<boolean> {
    const execution = this.executions.get(executionId)
    if (!execution || execution.state !== WorkflowExecutionState.PAUSED) {
      return false
    }

    execution.state = WorkflowExecutionState.RUNNING
    this.log(execution, 'info', '工作流執行已恢復')
    this.emitEvent({
      type: 'resumed',
      executionId,
      timestamp: new Date()
    })

    return true
  }

  /**
   * 取消工作流執行
   */
  async cancel(executionId: string): Promise<boolean> {
    const execution = this.executions.get(executionId)
    if (!execution || [
      WorkflowExecutionState.COMPLETED,
      WorkflowExecutionState.FAILED,
      WorkflowExecutionState.CANCELLED
    ].includes(execution.state)) {
      return false
    }

    execution.state = WorkflowExecutionState.CANCELLED
    execution.endTime = new Date()
    execution.duration = execution.endTime.getTime() - execution.startTime.getTime()
    
    this.log(execution, 'info', '工作流執行已取消')
    this.emitEvent({
      type: 'cancelled',
      executionId,
      timestamp: new Date()
    })

    return true
  }

  /**
   * 獲取執行結果
   */
  getExecution(executionId: string): WorkflowExecutionResult | undefined {
    return this.executions.get(executionId)
  }

  /**
   * 獲取所有執行記錄
   */
  getAllExecutions(): WorkflowExecutionResult[] {
    return Array.from(this.executions.values())
  }

  /**
   * 清理執行記錄
   */
  clearExecutions(olderThan?: Date): void {
    if (olderThan) {
      for (const [id, execution] of this.executions.entries()) {
        if (execution.startTime < olderThan) {
          this.executions.delete(id)
        }
      }
    } else {
      this.executions.clear()
    }
  }

  /**
   * 添加事件監聽器
   */
  addEventListener(
    eventType: string,
    listener: (event: WorkflowExecutionEvent) => void
  ): void {
    if (!this.eventListeners.has(eventType)) {
      this.eventListeners.set(eventType, [])
    }
    this.eventListeners.get(eventType)!.push(listener)
  }

  /**
   * 移除事件監聽器
   */
  removeEventListener(
    eventType: string,
    listener: (event: WorkflowExecutionEvent) => void
  ): void {
    const listeners = this.eventListeners.get(eventType)
    if (listeners) {
      const index = listeners.indexOf(listener)
      if (index > -1) {
        listeners.splice(index, 1)
      }
    }
  }

  /**
   * 發送事件
   */
  private emitEvent(event: WorkflowExecutionEvent): void {
    const listeners = this.eventListeners.get(event.type)
    if (listeners) {
      listeners.forEach(listener => {
        try {
          listener(event)
        } catch (error) {
          console.error('事件監聽器執行錯誤:', error)
        }
      })
    }

    // 發送到全局監聽器
    const globalListeners = this.eventListeners.get('*')
    if (globalListeners) {
      globalListeners.forEach(listener => {
        try {
          listener(event)
        } catch (error) {
          console.error('全局事件監聽器執行錯誤:', error)
        }
      })
    }
  }

  /**
   * 記錄日誌
   */
  private log(
    execution: WorkflowExecutionResult,
    level: WorkflowLog['level'],
    message: string,
    data?: any
  ): void {
    const log: WorkflowLog = {
      timestamp: new Date(),
      level,
      message,
      data
    }
    
    execution.logs.push(log)
    
    // 控制台輸出
    const logMessage = `[${log.timestamp.toISOString()}] [${level.toUpperCase()}] ${message}`
    switch (level) {
      case 'error':
        console.error(logMessage, data)
        break
      case 'warn':
        console.warn(logMessage, data)
        break
      case 'debug':
        console.debug(logMessage, data)
        break
      default:
        console.log(logMessage, data)
    }
  }

  /**
   * 生成執行ID
   */
  private generateExecutionId(): string {
    return `exec-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`
  }

  /**
   * 延遲函數
   */
  private delay(ms: number): Promise<void> {
    return new Promise(resolve => setTimeout(resolve, ms))
  }

  /**
   * 獲取執行統計信息
   */
  getExecutionStats(): {
    total: number
    completed: number
    failed: number
    running: number
    cancelled: number
    averageDuration: number
  } {
    const executions = Array.from(this.executions.values())
    const total = executions.length
    const completed = executions.filter(e => e.state === WorkflowExecutionState.COMPLETED).length
    const failed = executions.filter(e => e.state === WorkflowExecutionState.FAILED).length
    const running = executions.filter(e => e.state === WorkflowExecutionState.RUNNING).length
    const cancelled = executions.filter(e => e.state === WorkflowExecutionState.CANCELLED).length
    
    const completedExecutions = executions.filter(e => e.duration !== undefined)
    const averageDuration = completedExecutions.length > 0
      ? completedExecutions.reduce((sum, e) => sum + (e.duration || 0), 0) / completedExecutions.length
      : 0

    return {
      total,
      completed,
      failed,
      running,
      cancelled,
      averageDuration
    }
  }
}

/**
 * 工作流引擎工廠
 */
export class WorkflowEngineFactory {
  /**
   * 創建標準的Vidspark設計工作流引擎
   */
  static createVidsparkDesignEngine(): WorkflowEngine {
    const workflow = new VidsparkDesignWorkflow()
    const agent = new VidsparkDesignMasterAgent()
    
    return new WorkflowEngine(workflow, agent)
  }

  /**
   * 創建自定義工作流引擎
   */
  static createCustomEngine(
    workflow: VidsparkDesignWorkflow,
    agent: VidsparkDesignMasterAgent
  ): WorkflowEngine {
    return new WorkflowEngine(workflow, agent)
  }
}