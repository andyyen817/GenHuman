/**
 * 智能體安全驗證器
 * 確保遵循genhuman開發規則和安全防護機制
 */

/**
 * 安全驗證結果
 */
export interface SecurityValidationResult {
  /** 是否通過驗證 */
  valid: boolean
  /** 安全級別 */
  level: 'safe' | 'warning' | 'danger'
  /** 錯誤信息 */
  errors: string[]
  /** 警告信息 */
  warnings: string[]
  /** 建議修復方案 */
  suggestions: string[]
  /** 檢測到的風險類型 */
  riskTypes: string[]
}

/**
 * 代碼安全規則
 */
export interface CodeSecurityRule {
  /** 規則名稱 */
  name: string
  /** 規則描述 */
  description: string
  /** 規則類型 */
  type: 'forbidden' | 'required' | 'pattern' | 'structure'
  /** 嚴重程度 */
  severity: 'low' | 'medium' | 'high' | 'critical'
  /** 檢測模式 */
  pattern?: RegExp
  /** 檢測函數 */
  validator?: (code: string) => boolean
  /** 修復建議 */
  suggestion: string
}

/**
 * 安全驗證器類
 */
export class SecurityValidator {
  private static readonly FORBIDDEN_PATTERNS: CodeSecurityRule[] = [
    {
      name: 'no-eval',
      description: '禁止使用eval函數',
      type: 'forbidden',
      severity: 'critical',
      pattern: /\beval\s*\(/gi,
      suggestion: '使用JSON.parse()或其他安全的解析方法替代eval()'
    },
    {
      name: 'no-function-constructor',
      description: '禁止使用Function構造函數',
      type: 'forbidden',
      severity: 'critical',
      pattern: /new\s+Function\s*\(/gi,
      suggestion: '使用普通函數聲明或箭頭函數替代Function構造函數'
    },
    {
      name: 'no-script-injection',
      description: '禁止腳本注入',
      type: 'forbidden',
      severity: 'critical',
      pattern: /<script[^>]*>.*?<\/script>/gis,
      suggestion: '避免在模板中直接插入script標籤，使用Vue的安全綁定方式'
    },
    {
      name: 'no-innerHTML',
      description: '禁止使用innerHTML',
      type: 'forbidden',
      severity: 'high',
      pattern: /\.innerHTML\s*=/gi,
      suggestion: '使用textContent或Vue的v-html指令（需要確保內容安全）'
    },
    {
      name: 'no-outerHTML',
      description: '禁止使用outerHTML',
      type: 'forbidden',
      severity: 'high',
      pattern: /\.outerHTML\s*=/gi,
      suggestion: '使用Vue的模板語法或安全的DOM操作方法'
    },
    {
      name: 'no-document-write',
      description: '禁止使用document.write',
      type: 'forbidden',
      severity: 'high',
      pattern: /document\.write\s*\(/gi,
      suggestion: '使用現代DOM操作方法或Vue的響應式數據綁定'
    },
    {
      name: 'no-unsafe-protocols',
      description: '禁止不安全的協議',
      type: 'forbidden',
      severity: 'medium',
      pattern: /(?:javascript|data|vbscript):/gi,
      suggestion: '使用https://或相對路徑，避免javascript:等不安全協議'
    },
    {
      name: 'no-console-log-production',
      description: '生產環境禁止console.log',
      type: 'forbidden',
      severity: 'low',
      pattern: /console\.log\s*\(/gi,
      suggestion: '使用適當的日誌庫或在生產環境中移除調試代碼'
    }
  ]

  private static readonly REQUIRED_PATTERNS: CodeSecurityRule[] = [
    {
      name: 'vue-template-security',
      description: 'Vue模板必須使用安全的綁定方式',
      type: 'required',
      severity: 'medium',
      validator: (code: string) => {
        // 檢查是否正確使用Vue的安全綁定
        const hasUnsafeBinding = /v-html\s*=\s*["'][^"']*\{\{.*?\}\}[^"']*["']/gi.test(code)
        return !hasUnsafeBinding
      },
      suggestion: '確保v-html指令不直接綁定用戶輸入，使用v-text或插值語法處理純文本'
    },
    {
      name: 'input-validation',
      description: '表單輸入必須包含驗證',
      type: 'required',
      severity: 'medium',
      validator: (code: string) => {
        // 檢查表單是否包含驗證邏輯
        const hasInput = /<input|<textarea|<select/gi.test(code)
        if (!hasInput) return true
        
        const hasValidation = /(?:required|pattern|minlength|maxlength|min|max|validate)/gi.test(code)
        return hasValidation
      },
      suggestion: '為表單輸入添加適當的驗證規則（required、pattern等）'
    }
  ]

  private static readonly GENHUMAN_RULES: CodeSecurityRule[] = [
    {
      name: 'proper-imports',
      description: '必須使用正確的導入語句',
      type: 'structure',
      severity: 'medium',
      validator: (code: string) => {
        // 檢查是否使用了正確的ES6導入語法
        const hasOldImports = /require\s*\(/gi.test(code)
        const hasNewImports = /import\s+.*?from/gi.test(code)
        return !hasOldImports || hasNewImports
      },
      suggestion: '使用ES6的import語法替代require()'
    },
    {
      name: 'typescript-types',
      description: 'TypeScript文件必須包含類型定義',
      type: 'required',
      severity: 'low',
      validator: (code: string) => {
        // 檢查TypeScript文件是否包含類型定義
        if (!code.includes('.ts') && !code.includes('lang="ts"')) return true
        
        const hasTypes = /:\s*\w+|interface\s+\w+|type\s+\w+/gi.test(code)
        return hasTypes
      },
      suggestion: '為TypeScript代碼添加適當的類型定義'
    },
    {
      name: 'vue-composition-api',
      description: '推薦使用Vue 3 Composition API',
      type: 'pattern',
      severity: 'low',
      validator: (code: string) => {
        // 檢查是否使用了Composition API
        const hasOptionsAPI = /export\s+default\s*\{[^}]*(?:data|methods|computed|watch)\s*\(/gi.test(code)
        const hasCompositionAPI = /setup\s*\(|ref\s*\(|reactive\s*\(|computed\s*\(/gi.test(code)
        
        return !hasOptionsAPI || hasCompositionAPI
      },
      suggestion: '考慮使用Vue 3的Composition API以獲得更好的類型支持和代碼組織'
    },
    {
      name: 'responsive-design',
      description: '必須包含響應式設計',
      type: 'required',
      severity: 'medium',
      validator: (code: string) => {
        // 檢查是否包含響應式設計相關的類或樣式
        const hasResponsive = /(?:sm:|md:|lg:|xl:|2xl:|@media|flex|grid|responsive)/gi.test(code)
        return hasResponsive
      },
      suggestion: '添加響應式設計類或媒體查詢以支持不同屏幕尺寸'
    },
    {
      name: 'accessibility',
      description: '必須包含無障礙設計',
      type: 'required',
      severity: 'medium',
      validator: (code: string) => {
        // 檢查是否包含無障礙相關屬性
        const hasA11y = /(?:aria-|alt=|role=|tabindex=|for=|id=)/gi.test(code)
        return hasA11y
      },
      suggestion: '添加適當的ARIA屬性、alt文本和其他無障礙功能'
    }
  ]

  /**
   * 驗證代碼安全性
   */
  static validateCode(code: string, filename?: string): SecurityValidationResult {
    const result: SecurityValidationResult = {
      valid: true,
      level: 'safe',
      errors: [],
      warnings: [],
      suggestions: [],
      riskTypes: []
    }

    // 檢查禁止的模式
    for (const rule of this.FORBIDDEN_PATTERNS) {
      if (this.checkRule(code, rule)) {
        if (rule.severity === 'critical' || rule.severity === 'high') {
          result.errors.push(`${rule.name}: ${rule.description}`)
          result.suggestions.push(rule.suggestion)
          result.riskTypes.push(rule.name)
          result.valid = false
          result.level = 'danger'
        } else {
          result.warnings.push(`${rule.name}: ${rule.description}`)
          result.suggestions.push(rule.suggestion)
          if (result.level === 'safe') result.level = 'warning'
        }
      }
    }

    // 檢查必需的模式
    for (const rule of this.REQUIRED_PATTERNS) {
      if (!this.checkRule(code, rule)) {
        if (rule.severity === 'high' || rule.severity === 'critical') {
          result.errors.push(`${rule.name}: ${rule.description}`)
          result.suggestions.push(rule.suggestion)
          result.valid = false
          if (result.level !== 'danger') result.level = 'warning'
        } else {
          result.warnings.push(`${rule.name}: ${rule.description}`)
          result.suggestions.push(rule.suggestion)
          if (result.level === 'safe') result.level = 'warning'
        }
      }
    }

    // 檢查genhuman規則
    for (const rule of this.GENHUMAN_RULES) {
      if (!this.checkRule(code, rule)) {
        result.warnings.push(`${rule.name}: ${rule.description}`)
        result.suggestions.push(rule.suggestion)
        if (result.level === 'safe') result.level = 'warning'
      }
    }

    // 文件特定檢查
    if (filename) {
      const fileValidation = this.validateFile(code, filename)
      result.errors.push(...fileValidation.errors)
      result.warnings.push(...fileValidation.warnings)
      result.suggestions.push(...fileValidation.suggestions)
      
      if (fileValidation.errors.length > 0) {
        result.valid = false
        result.level = 'danger'
      }
    }

    return result
  }

  /**
   * 檢查單個規則
   */
  private static checkRule(code: string, rule: CodeSecurityRule): boolean {
    if (rule.pattern) {
      return rule.type === 'forbidden' 
        ? rule.pattern.test(code)
        : !rule.pattern.test(code)
    }
    
    if (rule.validator) {
      return rule.type === 'forbidden'
        ? !rule.validator(code)
        : rule.validator(code)
    }
    
    return false
  }

  /**
   * 驗證文件特定規則
   */
  private static validateFile(code: string, filename: string): {
    errors: string[]
    warnings: string[]
    suggestions: string[]
  } {
    const errors: string[] = []
    const warnings: string[] = []
    const suggestions: string[] = []

    // Vue文件檢查
    if (filename.endsWith('.vue')) {
      // 檢查是否包含template
      if (!/<template[^>]*>/gi.test(code)) {
        errors.push('Vue文件必須包含template標籤')
        suggestions.push('添加<template>標籤包裹組件模板')
      }

      // 檢查是否包含script
      if (!/<script[^>]*>/gi.test(code)) {
        warnings.push('Vue文件建議包含script標籤')
        suggestions.push('添加<script>標籤定義組件邏輯')
      }

      // 檢查是否使用scoped樣式
      if (/<style[^>]*>/gi.test(code) && !/<style[^>]*scoped[^>]*>/gi.test(code)) {
        warnings.push('建議使用scoped樣式避免樣式污染')
        suggestions.push('在<style>標籤中添加scoped屬性')
      }
    }

    // TypeScript文件檢查
    if (filename.endsWith('.ts') || filename.endsWith('.tsx')) {
      // 檢查是否有any類型
      if (/:\s*any\b/gi.test(code)) {
        warnings.push('避免使用any類型，使用具體的類型定義')
        suggestions.push('為變量和函數參數定義具體的類型')
      }
    }

    // CSS/SCSS文件檢查
    if (filename.endsWith('.css') || filename.endsWith('.scss')) {
      // 檢查是否使用了不安全的CSS
      if (/expression\s*\(/gi.test(code)) {
        errors.push('禁止使用CSS expression，存在安全風險')
        suggestions.push('使用標準CSS屬性替代expression')
      }
    }

    return { errors, warnings, suggestions }
  }

  /**
   * 驗證輸入數據
   */
  static validateInput(input: any): SecurityValidationResult {
    const result: SecurityValidationResult = {
      valid: true,
      level: 'safe',
      errors: [],
      warnings: [],
      suggestions: [],
      riskTypes: []
    }

    // 檢查輸入類型
    if (typeof input !== 'object' || input === null) {
      result.errors.push('輸入必須是有效的對象')
      result.valid = false
      result.level = 'danger'
      return result
    }

    // 檢查輸入大小
    const inputString = JSON.stringify(input)
    if (inputString.length > 100000) { // 100KB
      result.errors.push('輸入數據過大，可能存在安全風險')
      result.valid = false
      result.level = 'danger'
    }

    // 檢查危險字符
    const dangerousPatterns = [
      /<script[^>]*>/gi,
      /javascript:/gi,
      /on\w+\s*=/gi,
      /eval\s*\(/gi,
      /Function\s*\(/gi
    ]

    for (const pattern of dangerousPatterns) {
      if (pattern.test(inputString)) {
        result.errors.push('輸入包含潛在的惡意代碼')
        result.riskTypes.push('malicious-input')
        result.valid = false
        result.level = 'danger'
        break
      }
    }

    return result
  }

  /**
   * 清理和轉義輸入
   */
  static sanitizeInput(input: string): string {
    return input
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#x27;')
      .replace(/\//g, '&#x2F;')
  }

  /**
   * 驗證文件路徑
   */
  static validateFilePath(path: string): boolean {
    // 檢查路徑遍歷攻擊
    if (path.includes('..') || path.includes('~')) {
      return false
    }

    // 檢查絕對路徑
    if (path.startsWith('/') || /^[a-zA-Z]:/.test(path)) {
      return false
    }

    // 檢查特殊字符
    if (/[<>:"|?*]/.test(path)) {
      return false
    }

    return true
  }

  /**
   * 生成安全報告
   */
  static generateSecurityReport(validationResults: SecurityValidationResult[]): {
    summary: {
      total: number
      safe: number
      warning: number
      danger: number
    }
    details: SecurityValidationResult[]
    recommendations: string[]
  } {
    const summary = {
      total: validationResults.length,
      safe: validationResults.filter(r => r.level === 'safe').length,
      warning: validationResults.filter(r => r.level === 'warning').length,
      danger: validationResults.filter(r => r.level === 'danger').length
    }

    const recommendations: string[] = []
    
    // 收集所有建議
    for (const result of validationResults) {
      recommendations.push(...result.suggestions)
    }

    // 去重
    const uniqueRecommendations = [...new Set(recommendations)]

    return {
      summary,
      details: validationResults,
      recommendations: uniqueRecommendations
    }
  }
}

/**
 * 安全中間件
 */
export class SecurityMiddleware {
  /**
   * 請求前驗證
   */
  static async beforeRequest(request: any): Promise<void> {
    // 驗證請求數據
    const validation = SecurityValidator.validateInput(request)
    if (!validation.valid) {
      throw new Error(`安全驗證失敗: ${validation.errors.join(', ')}`)
    }

    // 記錄安全日誌
    if (validation.warnings.length > 0) {
      console.warn('安全警告:', validation.warnings)
    }
  }

  /**
   * 響應後驗證
   */
  static async afterResponse(response: any): Promise<any> {
    // 如果響應包含代碼，進行安全檢查
    if (response.code) {
      const validation = SecurityValidator.validateCode(response.code)
      if (!validation.valid) {
        throw new Error(`生成的代碼存在安全問題: ${validation.errors.join(', ')}`)
      }
      
      // 添加安全警告到響應中
      if (validation.warnings.length > 0) {
        response.securityWarnings = validation.warnings
        response.securitySuggestions = validation.suggestions
      }
    }

    return response
  }
}

/**
 * 安全配置
 */
export const SECURITY_CONFIG = {
  // 最大輸入長度
  MAX_INPUT_LENGTH: 50000,
  
  // 最大文件大小
  MAX_FILE_SIZE: 1024 * 1024, // 1MB
  
  // 允許的文件類型
  ALLOWED_FILE_TYPES: ['.vue', '.ts', '.js', '.css', '.scss', '.json', '.md'],
  
  // 禁止的關鍵詞
  FORBIDDEN_KEYWORDS: [
    'eval',
    'Function',
    'script',
    'iframe',
    'document.write',
    'innerHTML',
    'outerHTML'
  ],
  
  // 安全級別
  SECURITY_LEVELS: {
    SAFE: 'safe',
    WARNING: 'warning',
    DANGER: 'danger'
  } as const
}