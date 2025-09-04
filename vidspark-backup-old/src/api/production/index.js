/**
 * Vidspark生產環境API統一出口
 * 支持靈活的Token配置和多種認證方式
 * 
 * 創建時間：2025-08-29
 * 版本：v1.0
 * 遵循：@vidsparkmvp上線任務執行清單v3v0829.md 任務1.4
 */

import { VidsparkProductionConfig } from './productionConfig.js'
import { VidsparkDigitalHumanAPI } from './digitalHuman.js'
import { VidsparkVoiceAPI } from './voice.js'
import { VidsparkTaskAPI } from './task.js'
import { VidsparkProductionEightSteps } from './eightStepsProduction.js'

/**
 * Vidspark生產環境API主類
 * 提供統一的API調用接口和Token管理
 */
export class VidsparkProductionAPI {
  constructor(options = {}) {
    // 初始化配置
    this.config = new VidsparkProductionConfig(options)
    
    // 初始化各個API模塊
    this.digitalHuman = new VidsparkDigitalHumanAPI(this.config)
    this.voice = new VidsparkVoiceAPI(this.config)
    this.task = new VidsparkTaskAPI(this.config)
    this.eightSteps = new VidsparkProductionEightSteps(this.config)
    
    // Token狀態追蹤
    this.tokenStatus = {
      isValid: false,
      lastValidated: null,
      error: null
    }
  }
  
  /**
   * 設置API Token
   * 支持多種Token來源：環境變量、直接設置、配置文件
   */
  setToken(token, source = 'manual') {
    this.config.setToken(token, source)
    this.tokenStatus.isValid = false // 重置驗證狀態
    return this
  }
  
  /**
   * 驗證Token有效性
   * 使用最簡單的API端點進行驗證
   */
  async validateToken() {
    try {
      const result = await this.digitalHuman.testConnection()
      
      this.tokenStatus = {
        isValid: result.success,
        lastValidated: new Date().toISOString(),
        error: result.success ? null : result.error
      }
      
      return this.tokenStatus
    } catch (error) {
      this.tokenStatus = {
        isValid: false,
        lastValidated: new Date().toISOString(),
        error: error.message
      }
      
      return this.tokenStatus
    }
  }
  
  /**
   * 獲取Token狀態
   */
  getTokenStatus() {
    return this.tokenStatus
  }
  
  /**
   * 執行完整的八步法工作流程
   * 支持免費數字人和聲音克隆
   */
  async executeEightStepsWorkflow(taskType, params) {
    // 先驗證Token
    if (!this.tokenStatus.isValid) {
      await this.validateToken()
    }
    
    if (!this.tokenStatus.isValid) {
      throw new Error(`Token驗證失敗: ${this.tokenStatus.error}`)
    }
    
    // 執行八步法流程
    return await this.eightSteps.execute(taskType, params)
  }
  
  /**
   * 獲取API配置信息
   */
  getConfig() {
    return {
      apiBase: this.config.getApiBase(),
      hasToken: this.config.hasToken(),
      tokenSource: this.config.getTokenSource(),
      environment: this.config.getEnvironment(),
      zeaburIntegration: this.config.isZeaburEnabled()
    }
  }
}

/**
 * 創建默認的Vidspark生產環境API實例
 * 自動從環境變量讀取配置
 */
export function createVidsparkAPI(options = {}) {
  return new VidsparkProductionAPI({
    // 從Zeabur環境變量讀取配置
    apiBase: process.env.VIDSPARK_GENHUMAN_API_BASE || 'https://api.yidevs.com',
    token: process.env.VIDSPARK_GENHUMAN_PRODUCTION_TOKEN,
    zeaburDatabase: {
      host: process.env.VIDSPARK_DB_HOST || 'mysql.zeabur.internal',
      database: process.env.VIDSPARK_DB_DATABASE || 'genhuman_db',
      username: process.env.VIDSPARK_DB_USERNAME || 'root',
      password: process.env.VIDSPARK_DB_PASSWORD
    },
    eightSteps: {
      enabled: process.env.VIDSPARK_EIGHT_STEPS_ENABLED === 'true',
      maxRetries: parseInt(process.env.VIDSPARK_MAX_RETRY_ATTEMPTS) || 5,
      pollingInterval: parseInt(process.env.VIDSPARK_POLLING_INTERVAL) || 5000,
      timeoutMinutes: parseInt(process.env.VIDSPARK_TIMEOUT_MINUTES) || 10
    },
    ...options
  })
}

/**
 * Token管理工具函數
 */
export const TokenManager = {
  /**
   * 從不同來源獲取Token
   */
  getTokenFromSources() {
    const sources = [
      { name: 'environment', value: process.env.VIDSPARK_GENHUMAN_PRODUCTION_TOKEN },
      { name: 'localStorage', value: localStorage?.getItem('vidspark_api_token') },
      { name: 'config', value: window?.VIDSPARK_CONFIG?.apiToken }
    ]
    
    return sources.filter(source => source.value).map(source => ({
      ...source,
      masked: this.maskToken(source.value)
    }))
  },
  
  /**
   * 掩碼Token顯示
   */
  maskToken(token) {
    if (!token || token.length < 10) return '無效Token'
    return token.substring(0, 8) + '...' + token.substring(token.length - 4)
  },
  
  /**
   * 驗證Token格式
   */
  validateTokenFormat(token) {
    if (!token) return { valid: false, error: 'Token不能為空' }
    if (token.length < 20) return { valid: false, error: 'Token長度不足' }
    if (!/^[A-F0-9.]+$/i.test(token)) return { valid: false, error: 'Token格式無效' }
    
    return { valid: true }
  }
}

// 導出便捷的默認實例
export const vidsparkAPI = createVidsparkAPI()

console.log('📚 Vidspark生產環境API已加載', vidsparkAPI.getConfig())
