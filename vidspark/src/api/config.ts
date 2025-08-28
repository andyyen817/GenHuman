import axios from 'axios'
import type { AxiosInstance, AxiosRequestConfig, AxiosResponse } from 'axios'

// API基礎配置
export const API_BASE_URL = '/api/v1'

// 創建axios實例
export const apiClient: AxiosInstance = axios.create({
  baseURL: API_BASE_URL,
  timeout: 30000,
  headers: {
    'Content-Type': 'application/json'
  }
})

// 請求攔截器
apiClient.interceptors.request.use(
  (config: AxiosRequestConfig) => {
    // 添加認證token
    const token = localStorage.getItem('vidspark_token')
    if (token && config.headers) {
      config.headers.Authorization = `Bearer ${token}`
    }
    
    // 添加語言標頭
    const language = localStorage.getItem('vidspark_language') || 'zh-TW'
    if (config.headers) {
      config.headers['Accept-Language'] = language
    }
    
    console.log('API Request:', config.method?.toUpperCase(), config.url)
    return config
  },
  (error) => {
    console.error('Request Error:', error)
    return Promise.reject(error)
  }
)

// 響應攔截器
apiClient.interceptors.response.use(
  (response: AxiosResponse) => {
    console.log('API Response:', response.status, response.config.url)
    return response
  },
  (error) => {
    console.error('Response Error:', error.response?.status, error.response?.data)
    
    // 處理認證錯誤
    if (error.response?.status === 401) {
      // 清除token並跳轉到登入頁
      localStorage.removeItem('vidspark_token')
      window.location.href = '/vidspark/#/login'
    }
    
    return Promise.reject(error)
  }
)

// API響應類型
export interface ApiResponse<T = any> {
  code: number
  message: string
  data: T
}

// API錯誤類型
export interface ApiError {
  code: number
  message: string
  details?: any
}
