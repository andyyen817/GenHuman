/**
 * Vidspark數字人API封裝
 * 統一處理GenHuman數字人相關API調用
 * 
 * 創建時間：2025-08-29
 * 版本：v1.0
 * 遵循：@vidsparkmvp上線任務執行清單v3v0829.md 任務1.4
 */

export class VidsparkDigitalHumanAPI {
  constructor(config) {
    this.config = config
    this.apiEndpoints = {
      freeAvatar: '/app/human/human/Index/created',
      paidAvatar: '/app/human/human/Index/created',
      scene: '/app/human/human/Scene/created',
      taskStatus: '/app/human/human/Musetalk/task'
    }
  }
  
  /**
   * 測試API連接
   * 使用最簡單的請求來驗證Token和連接
   */
  async testConnection() {
    try {
      const result = await this.callApi(this.apiEndpoints.freeAvatar, {
        text: '測試連接',
        test_mode: true
      }, { timeout: 10000 })
      
      return {
        success: true,
        message: 'API連接測試成功',
        data: result
      }
    } catch (error) {
      return {
        success: false,
        message: 'API連接測試失敗',
        error: error.message,
        details: error.details || null
      }
    }
  }
  
  /**
   * 免費數字人生成
   * 對應GenHuman API的免費數字人端點
   */
  async createFreeAvatar(params) {
    const {
      text,
      avatar_id = 1,
      voice_id = 1,
      callback_url = null
    } = params
    
    // 參數驗證
    if (!text || text.trim().length === 0) {
      throw new Error('文本內容不能為空')
    }
    
    if (text.length > 500) {
      throw new Error('文本內容不能超過500字符')
    }
    
    const requestData = {
      text: text.trim(),
      avatar_id,
      voice_id,
      callback_url: callback_url || this.config.getCallbackConfig().baseUrl,
      production_mode: true,
      vidspark_request: true
    }
    
    try {
      const result = await this.callApi(this.apiEndpoints.freeAvatar, requestData)
      
      return {
        success: true,
        task_id: result.data?.task_id || result.task_id,
        message: result.msg || result.message || '免費數字人創建請求已提交',
        data: result,
        estimated_time: '2-5分鐘',
        api_endpoint: this.apiEndpoints.freeAvatar
      }
    } catch (error) {
      throw new Error(`免費數字人創建失敗: ${error.message}`)
    }
  }
  
  /**
   * 付費數字人生成
   * 支持更高級的功能和更快的處理速度
   */
  async createPaidAvatar(params) {
    const {
      text,
      avatar_id,
      voice_id,
      quality = 'high',
      callback_url = null
    } = params
    
    // 參數驗證
    if (!text || text.trim().length === 0) {
      throw new Error('文本內容不能為空')
    }
    
    const requestData = {
      text: text.trim(),
      avatar_id,
      voice_id,
      quality,
      callback_url: callback_url || this.config.getCallbackConfig().baseUrl,
      production_mode: true,
      vidspark_request: true,
      priority: 'high'
    }
    
    try {
      const result = await this.callApi(this.apiEndpoints.paidAvatar, requestData)
      
      return {
        success: true,
        task_id: result.data?.task_id || result.task_id,
        message: result.msg || result.message || '付費數字人創建請求已提交',
        data: result,
        estimated_time: '1-3分鐘',
        api_endpoint: this.apiEndpoints.paidAvatar
      }
    } catch (error) {
      throw new Error(`付費數字人創建失敗: ${error.message}`)
    }
  }
  
  /**
   * 查詢任務狀態
   * 用於輪詢檢查數字人生成進度
   */
  async getTaskStatus(taskId) {
    if (!taskId) {
      throw new Error('任務ID不能為空')
    }
    
    try {
      const result = await this.callApi(
        this.apiEndpoints.taskStatus,
        { task_id: taskId },
        { method: 'GET' }
      )
      
      return {
        success: true,
        task_id: taskId,
        status: result.data?.status || result.status || 'unknown',
        progress: result.data?.progress || result.progress || 0,
        result_url: result.data?.result_url || result.result_url,
        thumbnail_url: result.data?.thumbnail_url || result.thumbnail_url,
        message: result.msg || result.message,
        data: result,
        completed: this.isTaskCompleted(result)
      }
    } catch (error) {
      throw new Error(`任務狀態查詢失敗: ${error.message}`)
    }
  }
  
  /**
   * 判斷任務是否完成
   */
  isTaskCompleted(result) {
    const status = result.data?.status || result.status
    const completedStatuses = ['completed', 'success', 'finished', 'done']
    return completedStatuses.includes(status?.toLowerCase())
  }
  
  /**
   * 批量查詢任務狀態
   * 支持同時查詢多個任務的狀態
   */
  async batchGetTaskStatus(taskIds) {
    if (!Array.isArray(taskIds) || taskIds.length === 0) {
      throw new Error('任務ID列表不能為空')
    }
    
    const promises = taskIds.map(taskId => 
      this.getTaskStatus(taskId).catch(error => ({
        success: false,
        task_id: taskId,
        error: error.message
      }))
    )
    
    const results = await Promise.all(promises)
    
    return {
      success: true,
      total: taskIds.length,
      completed: results.filter(r => r.success && r.completed).length,
      failed: results.filter(r => !r.success).length,
      results: results
    }
  }
  
  /**
   * 核心API調用方法
   * 統一處理所有API請求，包括錯誤處理和重試機制
   */
  async callApi(endpoint, data, options = {}) {
    const {
      method = 'POST',
      timeout = 30000,
      retries = 3
    } = options
    
    if (!this.config.hasToken()) {
      throw new Error('API Token未設置，請先配置Token')
    }
    
    const url = this.config.createApiUrl(endpoint)
    const headers = this.config.getApiHeaders()
    
    const requestOptions = {
      method,
      headers,
      timeout
    }
    
    // 根據方法添加數據
    if (method === 'POST') {
      requestOptions.body = JSON.stringify(data)
    } else if (method === 'GET' && data) {
      const params = new URLSearchParams(data)
      url += '?' + params.toString()
    }
    
    let lastError = null
    
    // 重試機制
    for (let attempt = 1; attempt <= retries; attempt++) {
      try {
        console.log(`🔄 [嘗試 ${attempt}/${retries}] 調用API: ${endpoint}`)
        
        const response = await fetch(url, requestOptions)
        
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: ${response.statusText}`)
        }
        
        const result = await response.json()
        
        // 檢查API響應中的錯誤
        if (result.code && result.code !== 200) {
          const error = new Error(result.msg || result.message || 'API調用失敗')
          error.code = result.code
          error.details = result
          throw error
        }
        
        console.log(`✅ API調用成功: ${endpoint}`)
        return result
        
      } catch (error) {
        lastError = error
        console.warn(`❌ [嘗試 ${attempt}/${retries}] API調用失敗: ${error.message}`)
        
        // 如果是最後一次嘗試或者是Token錯誤，不再重試
        if (attempt === retries || error.code === 401) {
          break
        }
        
        // 等待後重試
        await this.sleep(1000 * attempt)
      }
    }
    
    throw lastError
  }
  
  /**
   * 輔助方法：延遲
   */
  sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms))
  }
  
  /**
   * 獲取可用的數字人列表
   * 用於前端選擇界面
   */
  getAvailableAvatars() {
    return [
      {
        id: 1,
        name: '免費數字人 1',
        type: 'free',
        preview_image: '/static/avatars/avatar1.jpg',
        description: '適合日常使用的數字人'
      },
      {
        id: 2,
        name: '免費數字人 2',
        type: 'free',
        preview_image: '/static/avatars/avatar2.jpg',
        description: '商務風格的數字人'
      },
      {
        id: 3,
        name: '高級數字人 1',
        type: 'paid',
        preview_image: '/static/avatars/avatar3.jpg',
        description: '高質量的專業數字人'
      }
    ]
  }
  
  /**
   * 獲取可用的聲音列表
   */
  getAvailableVoices() {
    return [
      {
        id: 1,
        name: '標準女聲',
        type: 'free',
        language: 'zh-TW',
        sample_url: '/static/voices/voice1.mp3'
      },
      {
        id: 2,
        name: '標準男聲',
        type: 'free',
        language: 'zh-TW',
        sample_url: '/static/voices/voice2.mp3'
      }
    ]
  }
}

export default VidsparkDigitalHumanAPI
