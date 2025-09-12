# Vidspark 內容製作工廠場景工作流實現任務執行計畫 v1.0

## 📋 **檔案目的**
基於當前 Commit 0bb310e 版本，設計 Vidspark 為模組化內容製作工廠，每個場景工作流都是獨立產線，支援快速複製、修改和擴展，提升開發效率和代碼複用性。

## 🎯 **核心理念：內容製作工廠模式**

### **🏭 工廠架構概念**
- **Vidspark = 內容製作工廠**
- **場景工作流 = 獨立產線**
- **頁面組件 = 產線設備**
- **共用組件 = 標準化零件**
- **配置文件 = 產線藍圖**

### **🔄 工作流標準化流程**
```
選擇場景 → AI編劇 → AI導演 → AI剪輯 → 成品輸出
    ↓         ↓        ↓        ↓        ↓
  場景配置   劇本生成   分鏡設計   視頻合成   結果展示
```

## 🏗️ **模組化架構設計**

### **📁 目錄結構設計**
```
src/
├── workflows/                    # 場景工作流目錄
│   ├── shared/                   # 共用組件和工具
│   │   ├── components/           # 通用組件
│   │   │   ├── ScenarioSelector.vue
│   │   │   ├── AIScriptwriter.vue
│   │   │   ├── AIDirector.vue
│   │   │   ├── AIEditor.vue
│   │   │   └── ResultDisplay.vue
│   │   ├── composables/          # 通用邏輯
│   │   │   ├── useWorkflow.ts
│   │   │   ├── useAIGeneration.ts
│   │   │   └── useProjectManager.ts
│   │   ├── types/                # 通用類型定義
│   │   │   ├── workflow.ts
│   │   │   └── ai-prompts.ts
│   │   └── utils/                # 通用工具函數
│   │       ├── promptGenerator.ts
│   │       └── workflowValidator.ts
│   │
│   ├── quick-digital-human/      # 快速製作數字人工作流
│   │   ├── config/
│   │   │   ├── workflow.config.ts
│   │   │   └── prompts.config.ts
│   │   ├── components/
│   │   │   ├── QuickScenarioView.vue
│   │   │   ├── QuickScriptwriterView.vue
│   │   │   ├── QuickDirectorView.vue
│   │   │   └── QuickEditorView.vue
│   │   ├── composables/
│   │   │   └── useQuickWorkflow.ts
│   │   └── router.ts
│   │
│   ├── from-scratch/             # 從零開始工作流
│   │   ├── config/
│   │   ├── components/
│   │   ├── composables/
│   │   └── router.ts
│   │
│   ├── story-adaptation/         # 故事改編工作流
│   │   ├── config/
│   │   ├── components/
│   │   ├── composables/
│   │   └── router.ts
│   │
│   └── template-based/           # 模板化工作流
│       ├── config/
│       ├── components/
│       ├── composables/
│       └── router.ts
│
├── views/                        # 原有頁面（逐步遷移）
└── router/                       # 主路由配置
    ├── index.ts
    └── workflows.ts              # 工作流路由聚合
```

### **⚙️ 工作流配置系統**

#### **1. 工作流配置文件範例**
```typescript
// workflows/quick-digital-human/config/workflow.config.ts
export const quickDigitalHumanWorkflow = {
  id: 'quick-digital-human',
  name: '快速製作數字人',
  description: '快速生成數字人視頻的工作流',
  version: '1.0.0',
  
  // 工作流步驟定義
  steps: [
    {
      id: 'scenario',
      name: '選擇場景',
      component: 'QuickScenarioView',
      route: '/workflows/quick-digital-human/scenario',
      required: true,
      estimatedTime: '2分鐘'
    },
    {
      id: 'scriptwriter',
      name: 'AI編劇',
      component: 'QuickScriptwriterView', 
      route: '/workflows/quick-digital-human/scriptwriter',
      required: true,
      estimatedTime: '5分鐘'
    },
    {
      id: 'director',
      name: 'AI導演',
      component: 'QuickDirectorView',
      route: '/workflows/quick-digital-human/director', 
      required: true,
      estimatedTime: '8分鐘'
    },
    {
      id: 'editor',
      name: 'AI剪輯',
      component: 'QuickEditorView',
      route: '/workflows/quick-digital-human/editor',
      required: true,
      estimatedTime: '10分鐘'
    }
  ],
  
  // 共用組件配置
  sharedComponents: [
    'ScenarioSelector',
    'AIScriptwriter', 
    'ResultDisplay'
  ],
  
  // 自定義組件配置
  customComponents: [
    'QuickScenarioView',
    'QuickScriptwriterView'
  ],
  
  // 數據流配置
  dataFlow: {
    input: ['userPreferences', 'templateSelection'],
    output: ['finalVideo', 'projectData'],
    intermediate: ['script', 'storyboard', 'audioTrack']
  }
}
```

#### **2. AI提示詞配置文件**
```typescript
// workflows/quick-digital-human/config/prompts.config.ts
export const quickDigitalHumanPrompts = {
  scriptwriter: {
    system: `你是專業的數字人視頻編劇，專注於快速生成吸引人的短視頻劇本。
    特點：簡潔有力、情感豐富、適合數字人表演。`,
    
    userPrompt: (scenario: string, duration: number) => `
    場景：${scenario}
    時長：${duration}秒
    要求：生成適合數字人表演的劇本，包含對話和動作描述。
    `,
    
    examples: [
      {
        input: '產品介紹，30秒',
        output: '大家好，我是AI助手小薇。今天為您介紹一款革命性的產品...'
      }
    ]
  },
  
  director: {
    system: `你是專業的數字人視頻導演，負責將劇本轉換為詳細的分鏡腳本。`,
    
    userPrompt: (script: string) => `
    劇本：${script}
    要求：生成詳細的分鏡腳本，包含鏡頭角度、表情、手勢等。
    `,
    
    shotTypes: ['特寫', '中景', '全景'],
    expressions: ['微笑', '認真', '驚訝', '思考'],
    gestures: ['指向', '展示', '強調', '歡迎']
  }
}
```

### **🔧 共用組件系統**

#### **1. 工作流基礎組件**
```vue
<!-- workflows/shared/components/WorkflowBase.vue -->
<template>
  <div class="workflow-container">
    <!-- 工作流進度條 -->
    <WorkflowProgress 
      :steps="workflowConfig.steps"
      :currentStep="currentStep"
    />
    
    <!-- 工作流內容區域 -->
    <div class="workflow-content">
      <component 
        :is="currentComponent"
        v-bind="componentProps"
        @next="handleNext"
        @previous="handlePrevious"
        @save="handleSave"
      />
    </div>
    
    <!-- 工作流控制按鈕 -->
    <WorkflowControls
      :canPrevious="canPrevious"
      :canNext="canNext"
      :isLoading="isLoading"
      @previous="handlePrevious"
      @next="handleNext"
      @save="handleSave"
    />
  </div>
</template>

<script setup lang="ts">
import { useWorkflow } from '../composables/useWorkflow'
import type { WorkflowConfig } from '../types/workflow'

interface Props {
  workflowConfig: WorkflowConfig
}

const props = defineProps<Props>()
const {
  currentStep,
  currentComponent,
  componentProps,
  canPrevious,
  canNext,
  isLoading,
  handleNext,
  handlePrevious,
  handleSave
} = useWorkflow(props.workflowConfig)
</script>
```

#### **2. 通用邏輯 Composable**
```typescript
// workflows/shared/composables/useWorkflow.ts
import { ref, computed } from 'vue'
import type { WorkflowConfig, WorkflowStep } from '../types/workflow'

export function useWorkflow(config: WorkflowConfig) {
  const currentStepIndex = ref(0)
  const projectData = ref({})
  const isLoading = ref(false)
  
  const currentStep = computed(() => config.steps[currentStepIndex.value])
  const canPrevious = computed(() => currentStepIndex.value > 0)
  const canNext = computed(() => currentStepIndex.value < config.steps.length - 1)
  
  const currentComponent = computed(() => {
    return currentStep.value?.component
  })
  
  const componentProps = computed(() => {
    return {
      stepConfig: currentStep.value,
      projectData: projectData.value,
      workflowConfig: config
    }
  })
  
  const handleNext = async () => {
    if (!canNext.value) return
    
    isLoading.value = true
    try {
      // 驗證當前步驟
      await validateCurrentStep()
      currentStepIndex.value++
    } catch (error) {
      console.error('步驟驗證失敗:', error)
    } finally {
      isLoading.value = false
    }
  }
  
  const handlePrevious = () => {
    if (!canPrevious.value) return
    currentStepIndex.value--
  }
  
  const handleSave = async (data: any) => {
    projectData.value = { ...projectData.value, ...data }
    // 保存到本地存儲或服務器
    await saveProjectData(projectData.value)
  }
  
  const validateCurrentStep = async () => {
    // 實現步驟驗證邏輯
    return true
  }
  
  const saveProjectData = async (data: any) => {
    // 實現數據保存邏輯
    localStorage.setItem(`workflow_${config.id}`, JSON.stringify(data))
  }
  
  return {
    currentStep,
    currentComponent,
    componentProps,
    canPrevious,
    canNext,
    isLoading,
    handleNext,
    handlePrevious,
    handleSave
  }
}
```

## 🚀 **實現任務執行清單**

### **階段一：基礎架構搭建（1-2天）**

#### **任務 1.1：創建工作流目錄結構**
- [ ] 創建 `src/workflows/` 主目錄
- [ ] 創建 `shared/` 共用組件目錄
- [ ] 創建第一個工作流 `quick-digital-human/`
- [ ] 設置基礎配置文件結構

#### **任務 1.2：開發共用組件系統**
- [ ] 實現 `WorkflowBase.vue` 基礎組件
- [ ] 實現 `WorkflowProgress.vue` 進度組件
- [ ] 實現 `WorkflowControls.vue` 控制組件
- [ ] 實現 `useWorkflow.ts` 通用邏輯

#### **任務 1.3：設計類型定義**
- [ ] 定義 `WorkflowConfig` 接口
- [ ] 定義 `WorkflowStep` 接口
- [ ] 定義 `AIPromptConfig` 接口
- [ ] 設置 TypeScript 嚴格模式

### **階段二：快速數字人工作流實現（2-3天）**

#### **任務 2.1：遷移現有頁面**
- [ ] 將 `ScriptwriterView.vue` 遷移為 `QuickScriptwriterView.vue`
- [ ] 將 `DirectorView.vue` 遷移為 `QuickDirectorView.vue`
- [ ] 將 `EditorView.vue` 遷移為 `QuickEditorView.vue`
- [ ] 創建 `QuickScenarioView.vue` 場景選擇頁面

#### **任務 2.2：配置工作流**
- [ ] 編寫 `workflow.config.ts` 配置文件
- [ ] 編寫 `prompts.config.ts` AI提示詞配置
- [ ] 設置路由配置 `router.ts`
- [ ] 整合到主路由系統

#### **任務 2.3：測試和優化**
- [ ] 測試工作流完整流程
- [ ] 優化頁面間數據傳遞
- [ ] 修復兼容性問題
- [ ] 性能優化

### **階段三：工作流複製和擴展機制（1-2天）**

#### **任務 3.1：開發工作流生成器**
- [ ] 實現工作流模板複製功能
- [ ] 開發配置文件生成器
- [ ] 實現組件自動重命名
- [ ] 創建路由自動註冊機制

#### **任務 3.2：實現第二個工作流**
- [ ] 複製 `quick-digital-human` 為 `from-scratch`
- [ ] 修改配置文件和提示詞
- [ ] 調整組件邏輯
- [ ] 測試獨立性

#### **任務 3.3：開發管理界面**
- [ ] 創建工作流管理頁面
- [ ] 實現工作流列表展示
- [ ] 添加工作流啟用/禁用功能
- [ ] 實現工作流統計和分析

### **階段四：高級功能和優化（2-3天）**

#### **任務 4.1：組件複用系統**
- [ ] 實現跨工作流組件共享
- [ ] 開發組件版本管理
- [ ] 創建組件依賴檢查
- [ ] 實現組件熱更新

#### **任務 4.2：數據流優化**
- [ ] 實現工作流狀態管理
- [ ] 優化數據持久化
- [ ] 添加數據驗證機制
- [ ] 實現數據回滾功能

#### **任務 4.3：開發者工具**
- [ ] 創建工作流調試工具
- [ ] 實現配置文件驗證器
- [ ] 開發性能監控面板
- [ ] 添加錯誤追蹤系統

## ⚠️ **潛在問題和風險分析**

### **🔴 高風險問題**

#### **1. 代碼複雜度增加**
**問題描述：**
- 多層嵌套的目錄結構可能導致代碼難以維護
- 組件間依賴關係複雜化
- 新開發者學習成本增加

**解決方案：**
- 制定嚴格的代碼規範和文檔標準
- 實現自動化測試覆蓋
- 提供詳細的開發者指南
- 使用 TypeScript 強類型約束

#### **2. 性能影響**
**問題描述：**
- 動態組件加載可能影響首屏性能
- 多個工作流同時運行時內存佔用增加
- 路由層級過深影響導航性能

**解決方案：**
- 實現組件懶加載和代碼分割
- 使用 Pinia 進行狀態管理優化
- 實現工作流實例生命週期管理
- 添加性能監控和預警機制

#### **3. 數據一致性問題**
**問題描述：**
- 跨工作流數據共享可能導致狀態混亂
- 組件間數據傳遞複雜化
- 數據持久化策略不統一

**解決方案：**
- 實現統一的數據管理層
- 使用事件總線進行組件通信
- 制定數據命名空間規範
- 實現數據版本控制

### **🟡 中風險問題**

#### **4. 開發效率問題**
**問題描述：**
- 初期搭建架構時間成本較高
- 組件抽象過度可能降低開發速度
- 配置文件維護工作量增加

**解決方案：**
- 分階段實施，先實現核心功能
- 提供工作流快速生成工具
- 自動化配置文件生成
- 建立組件庫和最佳實踐

#### **5. 技術債務累積**
**問題描述：**
- 快速複製可能導致代碼質量下降
- 共用組件修改影響範圍擴大
- 版本管理複雜化

**解決方案：**
- 實施代碼審查機制
- 定期重構和優化
- 使用語義化版本控制
- 建立技術債務追蹤系統

### **🟢 低風險問題**

#### **6. 用戶體驗一致性**
**問題描述：**
- 不同工作流間界面風格可能不一致
- 用戶學習成本可能增加

**解決方案：**
- 建立統一的設計系統
- 實施 UI 組件標準化
- 提供用戶引導和幫助文檔

## 📊 **成功指標和驗收標準**

### **開發效率指標**
- 新工作流開發時間：從 2-3 天縮短到 4-6 小時
- 組件複用率：達到 70% 以上
- 代碼重複率：降低到 15% 以下

### **技術質量指標**
- 代碼覆蓋率：達到 80% 以上
- 性能指標：首屏加載時間 < 2 秒
- 錯誤率：< 1%

### **用戶體驗指標**
- 工作流完成率：> 85%
- 用戶滿意度：> 4.5/5
- 學習成本：新用戶 15 分鐘內掌握基本操作

## 🎯 **下一步行動計劃**

### **立即執行（本週）**
1. 創建基礎目錄結構
2. 實現 `WorkflowBase` 組件
3. 設計核心類型定義
4. 開始遷移第一個工作流

### **短期目標（2週內）**
1. 完成快速數字人工作流
2. 實現工作流複製機制
3. 創建第二個工作流驗證架構
4. 建立基礎文檔和規範

### **中期目標（1個月內）**
1. 完成 3-4 個主要工作流
2. 實現高級功能和優化
3. 建立完整的開發者工具鏈
4. 進行全面測試和性能優化

---

**文檔版本：** v1.0  
**創建日期：** 2024-09-08  
**最後更新：** 2024-09-08  
**負責人：** Vidspark 開發團隊  
**審核狀態：** 待審核