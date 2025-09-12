/**
 * 通用工具函數
 * 提供各種輔助功能，支持組件和業務邏輯
 */

/**
 * 生成唯一ID
 * @param prefix 前綴
 * @returns 唯一ID字符串
 */
export function generateId(prefix: string = 'id'): string {
  const timestamp = Date.now().toString(36)
  const randomStr = Math.random().toString(36).substring(2, 8)
  return `${prefix}-${timestamp}-${randomStr}`
}

/**
 * 防抖函數
 * @param func 要防抖的函數
 * @param wait 等待時間（毫秒）
 * @param immediate 是否立即執行
 * @returns 防抖後的函數
 */
export function debounce<T extends (...args: any[]) => any>(
  func: T,
  wait: number,
  immediate: boolean = false
): (...args: Parameters<T>) => void {
  let timeout: NodeJS.Timeout | null = null
  
  return function executedFunction(...args: Parameters<T>) {
    const later = () => {
      timeout = null
      if (!immediate) func(...args)
    }
    
    const callNow = immediate && !timeout
    
    if (timeout) clearTimeout(timeout)
    timeout = setTimeout(later, wait)
    
    if (callNow) func(...args)
  }
}

/**
 * 節流函數
 * @param func 要節流的函數
 * @param limit 時間限制（毫秒）
 * @returns 節流後的函數
 */
export function throttle<T extends (...args: any[]) => any>(
  func: T,
  limit: number
): (...args: Parameters<T>) => void {
  let inThrottle: boolean = false
  
  return function executedFunction(...args: Parameters<T>) {
    if (!inThrottle) {
      func(...args)
      inThrottle = true
      setTimeout(() => inThrottle = false, limit)
    }
  }
}

/**
 * 深拷貝對象
 * @param obj 要拷貝的對象
 * @returns 深拷貝後的對象
 */
export function deepClone<T>(obj: T): T {
  if (obj === null || typeof obj !== 'object') {
    return obj
  }
  
  if (obj instanceof Date) {
    return new Date(obj.getTime()) as T
  }
  
  if (obj instanceof Array) {
    return obj.map(item => deepClone(item)) as T
  }
  
  if (typeof obj === 'object') {
    const clonedObj = {} as T
    for (const key in obj) {
      if (obj.hasOwnProperty(key)) {
        clonedObj[key] = deepClone(obj[key])
      }
    }
    return clonedObj
  }
  
  return obj
}

/**
 * 格式化文件大小
 * @param bytes 字節數
 * @param decimals 小數位數
 * @returns 格式化後的文件大小字符串
 */
export function formatFileSize(bytes: number, decimals: number = 2): string {
  if (bytes === 0) return '0 Bytes'
  
  const k = 1024
  const dm = decimals < 0 ? 0 : decimals
  const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']
  
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  
  return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i]
}

/**
 * 格式化時間
 * @param date 日期對象或時間戳
 * @param format 格式字符串
 * @returns 格式化後的時間字符串
 */
export function formatDate(date: Date | number | string, format: string = 'YYYY-MM-DD HH:mm:ss'): string {
  const d = new Date(date)
  
  if (isNaN(d.getTime())) {
    return 'Invalid Date'
  }
  
  const year = d.getFullYear()
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const day = String(d.getDate()).padStart(2, '0')
  const hours = String(d.getHours()).padStart(2, '0')
  const minutes = String(d.getMinutes()).padStart(2, '0')
  const seconds = String(d.getSeconds()).padStart(2, '0')
  
  return format
    .replace('YYYY', year.toString())
    .replace('MM', month)
    .replace('DD', day)
    .replace('HH', hours)
    .replace('mm', minutes)
    .replace('ss', seconds)
}

/**
 * 相對時間格式化
 * @param date 日期對象或時間戳
 * @returns 相對時間字符串
 */
export function formatRelativeTime(date: Date | number | string): string {
  const d = new Date(date)
  const now = new Date()
  const diff = now.getTime() - d.getTime()
  
  const seconds = Math.floor(diff / 1000)
  const minutes = Math.floor(seconds / 60)
  const hours = Math.floor(minutes / 60)
  const days = Math.floor(hours / 24)
  const months = Math.floor(days / 30)
  const years = Math.floor(days / 365)
  
  if (years > 0) return `${years}年前`
  if (months > 0) return `${months}個月前`
  if (days > 0) return `${days}天前`
  if (hours > 0) return `${hours}小時前`
  if (minutes > 0) return `${minutes}分鐘前`
  return '剛剛'
}

/**
 * 驗證郵箱格式
 * @param email 郵箱地址
 * @returns 是否為有效郵箱
 */
export function isValidEmail(email: string): boolean {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

/**
 * 驗證手機號格式（中國大陸）
 * @param phone 手機號
 * @returns 是否為有效手機號
 */
export function isValidPhone(phone: string): boolean {
  const phoneRegex = /^1[3-9]\d{9}$/
  return phoneRegex.test(phone)
}

/**
 * 驗證URL格式
 * @param url URL地址
 * @returns 是否為有效URL
 */
export function isValidUrl(url: string): boolean {
  try {
    new URL(url)
    return true
  } catch {
    return false
  }
}

/**
 * 獲取文件擴展名
 * @param filename 文件名
 * @returns 文件擴展名
 */
export function getFileExtension(filename: string): string {
  return filename.slice((filename.lastIndexOf('.') - 1 >>> 0) + 2)
}

/**
 * 獲取文件名（不含擴展名）
 * @param filename 文件名
 * @returns 不含擴展名的文件名
 */
export function getFileNameWithoutExtension(filename: string): string {
  return filename.replace(/\.[^/.]+$/, '')
}

/**
 * 首字母大寫
 * @param str 字符串
 * @returns 首字母大寫的字符串
 */
export function capitalize(str: string): string {
  return str.charAt(0).toUpperCase() + str.slice(1)
}

/**
 * 駝峰命名轉換
 * @param str 字符串
 * @returns 駝峰命名的字符串
 */
export function toCamelCase(str: string): string {
  return str.replace(/-([a-z])/g, (g) => g[1].toUpperCase())
}

/**
 * 短橫線命名轉換
 * @param str 字符串
 * @returns 短橫線命名的字符串
 */
export function toKebabCase(str: string): string {
  return str.replace(/([a-z0-9]|(?=[A-Z]))([A-Z])/g, '$1-$2').toLowerCase()
}

/**
 * 截斷文本
 * @param text 文本
 * @param length 最大長度
 * @param suffix 後綴
 * @returns 截斷後的文本
 */
export function truncateText(text: string, length: number, suffix: string = '...'): string {
  if (text.length <= length) return text
  return text.substring(0, length) + suffix
}

/**
 * 隨機字符串生成
 * @param length 長度
 * @param chars 字符集
 * @returns 隨機字符串
 */
export function randomString(length: number = 8, chars: string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'): string {
  let result = ''
  for (let i = 0; i < length; i++) {
    result += chars.charAt(Math.floor(Math.random() * chars.length))
  }
  return result
}

/**
 * 數組去重
 * @param array 數組
 * @param key 對象數組的去重鍵
 * @returns 去重後的數組
 */
export function uniqueArray<T>(array: T[], key?: keyof T): T[] {
  if (!key) {
    return [...new Set(array)]
  }
  
  const seen = new Set()
  return array.filter(item => {
    const value = item[key]
    if (seen.has(value)) {
      return false
    }
    seen.add(value)
    return true
  })
}

/**
 * 數組分組
 * @param array 數組
 * @param key 分組鍵
 * @returns 分組後的對象
 */
export function groupBy<T>(array: T[], key: keyof T): Record<string, T[]> {
  return array.reduce((groups, item) => {
    const group = String(item[key])
    groups[group] = groups[group] || []
    groups[group].push(item)
    return groups
  }, {} as Record<string, T[]>)
}

/**
 * 數組排序
 * @param array 數組
 * @param key 排序鍵
 * @param order 排序順序
 * @returns 排序後的數組
 */
export function sortBy<T>(array: T[], key: keyof T, order: 'asc' | 'desc' = 'asc'): T[] {
  return [...array].sort((a, b) => {
    const aVal = a[key]
    const bVal = b[key]
    
    if (aVal < bVal) return order === 'asc' ? -1 : 1
    if (aVal > bVal) return order === 'asc' ? 1 : -1
    return 0
  })
}

/**
 * 等待指定時間
 * @param ms 毫秒數
 * @returns Promise
 */
export function sleep(ms: number): Promise<void> {
  return new Promise(resolve => setTimeout(resolve, ms))
}

/**
 * 重試函數
 * @param fn 要重試的函數
 * @param retries 重試次數
 * @param delay 重試間隔
 * @returns Promise
 */
export async function retry<T>(
  fn: () => Promise<T>,
  retries: number = 3,
  delay: number = 1000
): Promise<T> {
  try {
    return await fn()
  } catch (error) {
    if (retries > 0) {
      await sleep(delay)
      return retry(fn, retries - 1, delay)
    }
    throw error
  }
}

/**
 * 檢查是否為移動設備
 * @returns 是否為移動設備
 */
export function isMobile(): boolean {
  return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
}

/**
 * 檢查是否為觸控設備
 * @returns 是否為觸控設備
 */
export function isTouchDevice(): boolean {
  return 'ontouchstart' in window || navigator.maxTouchPoints > 0
}

/**
 * 獲取設備類型
 * @returns 設備類型
 */
export function getDeviceType(): 'mobile' | 'tablet' | 'desktop' {
  const width = window.innerWidth
  
  if (width < 768) return 'mobile'
  if (width < 1024) return 'tablet'
  return 'desktop'
}

/**
 * 複製到剪貼板
 * @param text 要複製的文本
 * @returns Promise<boolean>
 */
export async function copyToClipboard(text: string): Promise<boolean> {
  try {
    if (navigator.clipboard && window.isSecureContext) {
      await navigator.clipboard.writeText(text)
      return true
    } else {
      // 降級方案
      const textArea = document.createElement('textarea')
      textArea.value = text
      textArea.style.position = 'fixed'
      textArea.style.left = '-999999px'
      textArea.style.top = '-999999px'
      document.body.appendChild(textArea)
      textArea.focus()
      textArea.select()
      const result = document.execCommand('copy')
      textArea.remove()
      return result
    }
  } catch (error) {
    console.error('複製失敗:', error)
    return false
  }
}

/**
 * 下載文件
 * @param url 文件URL
 * @param filename 文件名
 */
export function downloadFile(url: string, filename?: string): void {
  const link = document.createElement('a')
  link.href = url
  if (filename) {
    link.download = filename
  }
  link.target = '_blank'
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

/**
 * 獲取查詢參數
 * @param name 參數名
 * @returns 參數值
 */
export function getQueryParam(name: string): string | null {
  const urlParams = new URLSearchParams(window.location.search)
  return urlParams.get(name)
}

/**
 * 設置查詢參數
 * @param params 參數對象
 */
export function setQueryParams(params: Record<string, string>): void {
  const url = new URL(window.location.href)
  Object.entries(params).forEach(([key, value]) => {
    url.searchParams.set(key, value)
  })
  window.history.pushState({}, '', url.toString())
}

/**
 * 移除查詢參數
 * @param names 參數名數組
 */
export function removeQueryParams(names: string[]): void {
  const url = new URL(window.location.href)
  names.forEach(name => {
    url.searchParams.delete(name)
  })
  window.history.pushState({}, '', url.toString())
}