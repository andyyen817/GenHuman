/**
 * Vidspark生產環境配置管理
 * 統一管理API Token、環境變量和配置選項
 * 
 * 創建時間：2025-08-29
 * 版本：v1.0
 * 遵循：@vidsparkmvp上線任務執行清單v3v0829.md 任務1.4
 */

export class VidsparkProductionConfig {
  constructor(options = {}) {
    this.config = {
      // API基礎配置
      apiBase: options.apiBase || 'https://api.yidevs.com',
      environment: options.environment || 'production',
      
      // Token配置
      token: null,
      tokenSource: 'none',
      
      // Zeabur數據庫配置
      zeaburDatabase: options.zeaburDatabase || {},
      
      // 八步法配置
      eightSteps: {
        enabled: true,
        maxRetries: 5,
        pollingInterval: 5000,
        timeoutMinutes: 10,
        ...options.eightSteps
      },
      
      // 存儲配置
      storage: {
        path: '/var/www/html/server/public/vidspark/storage',
        cdnDomain: 'https://genhuman-digital-human.zeabur.app',
        ...options.storage
      },
      
      // 回調配置
      callback: {
        baseUrl: 'https://genhuman-digital-human.zeabur.app/vidspark-admin/api/callback',
        webhookSecret: 'vidspark_webhook_secret_2025',
        ...options.callback
      }
    }
    
    // 自動加載Token
    this.autoLoadToken()
  }
  
  /**
   * 自動從多個來源加載Token
   * 優先級：環境變量 > localStorage > 配置文件
   */
  autoLoadToken() {
    const tokenSources = [
      {
        name: 'environment',
        getValue: () => process.env?.VIDSPARK_GENHUMAN_PRODUCTION_TOKEN,
        priority: 1
      },
      {
        name: 'localStorage',
        getValue: () => {
          if (typeof window !== 'undefined' && window.localStorage) {
            return localStorage.getItem('vidspark_api_token')
          }
          return null
        },
        priority: 2
      },
      {
        name: 'config',
        getValue: () => {
          if (typeof window !== 'undefined' && window.VIDSPARK_CONFIG) {
            return window.VIDSPARK_CONFIG.apiToken
          }
          return null
        },
        priority: 3
      }
    ]
    
    // 按優先級嘗試加載Token
    for (const source of tokenSources.sort((a, b) => a.priority - b.priority)) {
      const token = source.getValue()
      if (token && this.validateTokenFormat(token)) {
        this.setToken(token, source.name)
        console.log(`🔑 Vidspark Token已從${source.name}加載`)
        break
      }
    }
    
    if (!this.hasToken()) {
      console.warn('⚠️ 未找到有效的Vidspark API Token，請手動設置')
    }
  }
  
  /**
   * 設置Token
   */
  setToken(token, source = 'manual') {
    if (!this.validateTokenFormat(token)) {
      throw new Error('Token格式無效')
    }
    
    this.config.token = token
    this.config.tokenSource = source
    
    // 如果是手動設置，保存到localStorage
    if (source === 'manual' && typeof window !== 'undefined' && window.localStorage) {
      localStorage.setItem('vidspark_api_token', token)
    }
  }
  
  /**
   * 獲取Token
   */
  getToken() {
    return this.config.token
  }
  
  /**
   * 檢查是否有Token
   */
  hasToken() {
    return !!this.config.token
  }
  
  /**
   * 獲取Token來源
   */
  getTokenSource() {
    return this.config.tokenSource
  }
  
  /**
   * 驗證Token格式
   */
  validateTokenFormat(token) {
    if (!token || typeof token !== 'string') return false
    if (token.length < 20) return false
    
    // GenHuman Token格式：所有字母、數字和點號
    const tokenPattern = /^[A-Z0-9.]+$/i
    return tokenPattern.test(token)
  }
  
  /**
   * 獲取API基礎地址
   */
  getApiBase() {
    return this.config.apiBase
  }
  
  /**
   * 獲取環境類型
   */
  getEnvironment() {
    return this.config.environment
  }
  
  /**
   * 檢查是否啟用Zeabur集成
   */
  isZeaburEnabled() {
    return !!this.config.zeaburDatabase.host
  }
  
  /**
   * 獲取Zeabur數據庫配置
   */
  getZeaburConfig() {
    return this.config.zeaburDatabase
  }
  
  /**
   * 獲取八步法配置
   */
  getEightStepsConfig() {
    return this.config.eightSteps
  }
  
  /**
   * 獲取存儲配置
   */
  getStorageConfig() {
    return this.config.storage
  }
  
  /**
   * 獲取回調配置
   */
  getCallbackConfig() {
    return this.config.callback
  }
  
  /**
   * 生成API請求頭
   */
  getApiHeaders() {
    if (!this.hasToken()) {
      throw new Error('API Token未設置，無法生成請求頭')
    }
    
    return {
      'Authorization': `Bearer ${this.getToken()}`,
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'User-Agent': 'Vidspark-Production/1.0'
    }
  }
  
  /**
   * 創建完整的API URL
   */
  createApiUrl(endpoint) {
    const base = this.getApiBase().replace(/\/$/, '')
    const path = endpoint.replace(/^\//, '')
    return `${base}/${path}`
  }
  
  /**
   * 獲取配置摘要（用於調試）
   */
  getSummary() {
    return {
      apiBase: this.getApiBase(),
      environment: this.getEnvironment(),
      hasToken: this.hasToken(),
      tokenSource: this.getTokenSource(),
      tokenMask: this.hasToken() ? this.maskToken(this.getToken()) : 'none',
      zeaburEnabled: this.isZeaburEnabled(),
      eightStepsEnabled: this.config.eightSteps.enabled
    }
  }
  
  /**
   * 掩碼Token顯示
   */
  maskToken(token) {
    if (!token || token.length < 10) return 'invalid'
    return token.substring(0, 8) + '...' + token.substring(token.length - 4)
  }
  
  /**
   * 測試配置完整性
   */
  testConfiguration() {
    const issues = []
    
    if (!this.hasToken()) {
      issues.push('API Token未設置')
    }
    
    if (!this.getApiBase()) {
      issues.push('API基礎地址未設置')
    }
    
    if (this.isZeaburEnabled()) {
      const zeaburConfig = this.getZeaburConfig()
      if (!zeaburConfig.host) issues.push('Zeabur數據庫主機未設置')
      if (!zeaburConfig.database) issues.push('Zeabur數據庫名稱未設置')
    }
    
    return {
      valid: issues.length === 0,
      issues: issues,
      summary: this.getSummary()
    }
  }
}

/**
 * Token管理助手類
 */
export class TokenManager {
  static getAvailableTokens() {
    const tokens = []
    
    // 環境變量Token
    if (process.env?.VIDSPARK_GENHUMAN_PRODUCTION_TOKEN) {
      tokens.push({
        source: 'environment',
        token: process.env.VIDSPARK_GENHUMAN_PRODUCTION_TOKEN,
        priority: 1
      })
    }
    
    // localStorage Token
    if (typeof window !== 'undefined' && window.localStorage) {
      const stored = localStorage.getItem('vidspark_api_token')
      if (stored) {
        tokens.push({
          source: 'localStorage',
          token: stored,
          priority: 2
        })
      }
    }
    
    return tokens.map(item => ({
      ...item,
      masked: this.maskToken(item.token),
      valid: this.validateFormat(item.token)
    }))
  }
  
  static maskToken(token) {
    if (!token || token.length < 10) return 'invalid'
    return token.substring(0, 8) + '...' + token.substring(token.length - 4)
  }
  
  static validateFormat(token) {
    if (!token || typeof token !== 'string') return false
    if (token.length < 20) return false
    return /^[A-F0-9.]+$/i.test(token)
  }
  
  static saveToken(token, persistent = true) {
    if (!this.validateFormat(token)) {
      throw new Error('Token格式無效')
    }
    
    if (persistent && typeof window !== 'undefined' && window.localStorage) {
      localStorage.setItem('vidspark_api_token', token)
    }
    
    return true
  }
  
  static clearToken() {
    if (typeof window !== 'undefined' && window.localStorage) {
      localStorage.removeItem('vidspark_api_token')
    }
  }
}

export default VidsparkProductionConfig
