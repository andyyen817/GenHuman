/**
 * Vidspark頁面設計大師智能體測試
 * 驗證智能體的核心功能
 */

import { describe, it, expect, beforeEach } from 'vitest'
import {
  createVidspark,
  VidsparkDesignMasterAgent,
  WorkflowEngine,
  SecurityValidator,
  api
} from '../src/index'

describe('Vidspark頁面設計大師智能體', () => {
  let vidspark: ReturnType<typeof createVidspark>
  let agent: VidsparkDesignMasterAgent
  let workflow: WorkflowEngine

  beforeEach(() => {
    vidspark = createVidspark()
    agent = vidspark.getCurrentAgent()!
    workflow = vidspark.getCurrentWorkflow()!
  })

  describe('智能體初始化', () => {
    it('應該成功創建Vidspark實例', () => {
      expect(vidspark).toBeDefined()
      expect(agent).toBeDefined()
      expect(workflow).toBeDefined()
    })

    it('應該有正確的智能體配置', () => {
      const state = agent.getState()
      expect(state.config.info.name).toBe('Vidspark頁面設計大師')
      expect(state.config.capabilities.codeGeneration).toBe(true)
      expect(state.config.capabilities.designAnalysis).toBe(true)
    })

    it('應該支持多種能力', () => {
      const state = agent.getState()
      const capabilities = state.config.capabilities
      
      expect(capabilities.codeGeneration).toBe(true)
      expect(capabilities.designAnalysis).toBe(true)
      expect(capabilities.requirementAnalysis).toBe(true)
      expect(capabilities.performanceOptimization).toBe(true)
      expect(capabilities.accessibilityCheck).toBe(true)
      expect(capabilities.securityValidation).toBe(true)
    })
  })

  describe('需求分析功能', () => {
    it('應該能夠分析簡單的頁面需求', async () => {
      const requirement = '創建一個登錄頁面，包含用戶名和密碼輸入框'
      
      const response = await agent.processTask({
        type: 'design_request',
        data: { requirement }
      })

      expect(response.success).toBe(true)
      expect(response.data).toBeDefined()
      expect(response.data.analysis).toBeDefined()
      expect(response.data.analysis.functionalRequirements).toContain('登錄')
    })

    it('應該能夠識別複雜的業務需求', async () => {
      const requirement = '設計一個電商產品列表頁面，支持篩選、排序和分頁功能'
      
      const response = await agent.processTask({
        type: 'design_request',
        data: { requirement }
      })

      expect(response.success).toBe(true)
      expect(response.data.analysis.functionalRequirements).toEqual(
        expect.arrayContaining(['產品展示', '篩選', '排序', '分頁'])
      )
    })
  })

  describe('代碼生成功能', () => {
    it('應該生成符合Vue 3規範的組件代碼', async () => {
      const requirement = '創建一個簡單的按鈕組件'
      
      const response = await agent.processTask({
        type: 'design_request',
        data: { requirement }
      })

      expect(response.success).toBe(true)
      expect(response.data.code).toBeDefined()
      expect(response.data.code).toContain('<template>')
      expect(response.data.code).toContain('<script setup lang="ts">')
      expect(response.data.code).toContain('</template>')
    })

    it('應該包含TypeScript類型定義', async () => {
      const requirement = '創建一個帶有props的輸入框組件'
      
      const response = await agent.processTask({
        type: 'design_request',
        data: { requirement }
      })

      expect(response.data.code).toContain('interface')
      expect(response.data.code).toContain('Props')
      expect(response.data.code).toContain('defineProps')
    })

    it('應該包含響應式樣式', async () => {
      const requirement = '創建一個響應式的卡片組件'
      
      const response = await agent.processTask({
        type: 'design_request',
        data: { requirement }
      })

      expect(response.data.code).toMatch(/class="[^"]*(?:sm:|md:|lg:)/)
    })
  })

  describe('安全驗證功能', () => {
    it('應該檢測不安全的代碼模式', () => {
      const unsafeCode = `
        <template>
          <div v-html="userInput"></div>
        </template>
      `
      
      const result = SecurityValidator.validateCode(unsafeCode, 'test.vue')
      expect(result.valid).toBe(false)
      expect(result.errors.length).toBeGreaterThan(0)
      expect(result.errors[0].message).toContain('v-html')
    })

    it('應該通過安全的代碼', () => {
      const safeCode = `
        <template>
          <div>{{ userInput }}</div>
        </template>
        <script setup lang="ts">
        const userInput = ref('')
        </script>
      `
      
      const result = SecurityValidator.validateCode(safeCode, 'test.vue')
      expect(result.valid).toBe(true)
      expect(result.errors.length).toBe(0)
    })

    it('應該檢測危險的JavaScript模式', () => {
      const dangerousCode = `
        const result = eval(userInput)
        document.write('<script>alert("xss")</script>')
      `
      
      const result = SecurityValidator.validateCode(dangerousCode, 'test.js')
      expect(result.valid).toBe(false)
      expect(result.errors.some(e => e.message.includes('eval'))).toBe(true)
    })
  })

  describe('工作流執行', () => {
    it('應該能夠執行完整的設計工作流', async () => {
      const requirement = '創建一個用戶註冊表單'
      
      const result = await workflow.execute(
        {
          type: 'design_request',
          requirement,
          timestamp: new Date()
        },
        {
          workflowName: 'complete_design_workflow'
        }
      )

      expect(result.success).toBe(true)
      expect(result.steps.length).toBeGreaterThan(0)
      expect(result.artifacts).toBeDefined()
      expect(result.artifacts.code).toBeDefined()
    })

    it('應該能夠執行快速組件生成工作流', async () => {
      const requirement = '創建一個簡單的標題組件'
      
      const result = await workflow.execute(
        {
          type: 'design_request',
          requirement,
          timestamp: new Date()
        },
        {
          workflowName: 'quick_component_generation'
        }
      )

      expect(result.success).toBe(true)
      expect(result.executionTime).toBeLessThan(20000) // 應該在20秒內完成
    })
  })

  describe('主題管理', () => {
    it('應該支持主題切換', () => {
      const themeManager = vidspark.getThemeManager()
      
      themeManager.applyTheme('dark')
      expect(themeManager.getCurrentTheme()).toBe('dark')
      
      themeManager.applyTheme('light')
      expect(themeManager.getCurrentTheme()).toBe('light')
    })

    it('應該支持自定義主題', () => {
      const themeManager = vidspark.getThemeManager()
      
      const customTheme = {
        colors: {
          primary: '#ff6b6b',
          secondary: '#4ecdc4'
        }
      }
      
      themeManager.setTheme('custom', customTheme)
      themeManager.applyTheme('custom')
      
      expect(themeManager.getCurrentTheme()).toBe('custom')
      expect(themeManager.getTheme('custom')).toEqual(customTheme)
    })
  })

  describe('API便捷函數', () => {
    it('api.createPage應該能快速創建頁面', async () => {
      const requirement = '創建一個關於我們頁面'
      
      const response = await api.createPage(requirement)
      
      expect(response.success).toBe(true)
      expect(response.data.code).toBeDefined()
    })

    it('api.validateCode應該能驗證代碼安全性', () => {
      const code = '<template><div>Hello World</div></template>'
      
      const result = api.validateCode(code, 'test.vue')
      
      expect(result.valid).toBe(true)
    })

    it('api.getStatus應該返回系統狀態', () => {
      const status = api.getStatus()
      
      expect(status.agents).toBeDefined()
      expect(status.theme).toBeDefined()
      expect(status.version).toBeDefined()
    })
  })

  describe('錯誤處理', () => {
    it('應該優雅處理無效的需求', async () => {
      const invalidRequirement = ''
      
      const response = await agent.processTask({
        type: 'design_request',
        data: { requirement: invalidRequirement }
      })

      expect(response.success).toBe(false)
      expect(response.error).toBeDefined()
      expect(response.error.message).toContain('需求不能為空')
    })

    it('應該處理工作流執行錯誤', async () => {
      const result = await workflow.execute(
        {
          type: 'invalid_request',
          requirement: 'test',
          timestamp: new Date()
        } as any
      )

      expect(result.success).toBe(false)
      expect(result.error).toBeDefined()
    })
  })

  describe('性能測試', () => {
    it('代碼生成應該在合理時間內完成', async () => {
      const startTime = Date.now()
      
      await agent.processTask({
        type: 'design_request',
        data: { requirement: '創建一個簡單的頁面' }
      })
      
      const executionTime = Date.now() - startTime
      expect(executionTime).toBeLessThan(10000) // 應該在10秒內完成
    })

    it('應該支持並發任務處理', async () => {
      const tasks = Array.from({ length: 3 }, (_, i) => 
        agent.processTask({
          type: 'design_request',
          data: { requirement: `創建組件${i + 1}` }
        })
      )
      
      const results = await Promise.all(tasks)
      
      results.forEach(result => {
        expect(result.success).toBe(true)
      })
    })
  })
})

// 集成測試
describe('Vidspark集成測試', () => {
  it('應該能夠完成端到端的設計流程', async () => {
    const vidspark = createVidspark()
    
    // 1. 分析需求
    const requirement = '創建一個用戶管理頁面，包含用戶列表、添加用戶和編輯用戶功能'
    
    // 2. 執行工作流
    const result = await vidspark.executeDesignWorkflow(requirement, {
      workflowName: 'complete_design_workflow',
      includeTests: true,
      generateDocs: true
    })
    
    // 3. 驗證結果
    expect(result.success).toBe(true)
    expect(result.artifacts.code).toBeDefined()
    expect(result.artifacts.tests).toBeDefined()
    expect(result.artifacts.documentation).toBeDefined()
    
    // 4. 驗證代碼安全性
    const securityResult = vidspark.validateCodeSecurity(result.artifacts.code)
    expect(securityResult.valid).toBe(true)
    
    // 5. 檢查生成的代碼質量
    expect(result.artifacts.code).toContain('用戶列表')
    expect(result.artifacts.code).toContain('添加用戶')
    expect(result.artifacts.code).toContain('編輯用戶')
  })
})