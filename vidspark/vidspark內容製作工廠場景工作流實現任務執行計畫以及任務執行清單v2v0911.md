# Vidspark 內容製作工廠場景工作流實現任務執行計畫 v2.0

## 📋 **檔案目的**
基於 v1.0 版本優化，專注實現**快速製作數字人工作流**，深度整合 GenHuman API 調用邏輯，採用模組化內容製作工廠模式，支援快速複製、修改和擴展。

## 🎯 **核心理念：數字人製作工廠模式**

### **🏭 工廠架構概念**
- **Vidspark = 數字人製作工廠**
- **快速數字人工作流 = 核心產線**
- **GenHuman API = 生產引擎**
- **頁面組件 = 產線設備**
- **共用組件 = 標準化零件**
- **配置文件 = 產線藍圖**

### **🔄 快速數字人工作流標準化流程**
```
場景選擇 → AI編劇 → AI導演1(聲音) → AI導演2(數字人) → 成品輸出
    ↓         ↓          ↓              ↓            ↓
  卡片場景   文案生成   聲音克隆合成    數字人合成    結果展示
```

## 🏗️ **快速數字人工作流架構設計**

### **📁 目錄結構設計**
```
src/
├── workflows/                    # 場景工作流目錄
│   ├── shared/                   # 共用組件和工具
│   │   ├── components/           # 通用組件
│   │   │   ├── WorkflowBase.vue
│   │   │   ├── WorkflowProgress.vue
│   │   │   ├── WorkflowControls.vue
│   │   │   ├── ScenarioSelector.vue
│   │   │   ├── AIScriptwriter.vue
│   │   │   ├── VoiceClonePanel.vue
│   │   │   ├── DigitalHumanPanel.vue
│   │   │   └── ResultDisplay.vue
│   │   ├── composables/          # 通用邏輯
│   │   │   ├── useWorkflow.ts
│   │   │   ├── useGenHumanAPI.ts
│   │   │   ├── useVoiceClone.ts
│   │   │   ├── useDigitalHuman.ts
│   │   │   └── useProjectManager.ts
│   │   ├── types/                # 通用類型定義
│   │   │   ├── workflow.ts
│   │   │   ├── genhuman-api.ts
│   │   │   └── ai-prompts.ts
│   │   └── utils/                # 通用工具函數
│   │       ├── genhumanClient.ts
│   │       ├── promptGenerator.ts
│   │       └── workflowValidator.ts
│   │
│   ├── quick-digital-human/      # 快速製作數字人工作流
│   │   ├── config/
│   │   │   ├── workflow.config.ts
│   │   │   ├── prompts.config.ts
│   │   │   └── genhuman.config.ts
│   │   ├── components/
│   │   │   ├── QuickScenarioView.vue      # 場景選擇頁面
│   │   │   ├── QuickScriptwriterView.vue  # AI編劇頁面
│   │   │   ├── QuickVoiceDirectorView.vue # AI導演1:聲音克隆
│   │   │   ├── QuickHumanDirectorView.vue # AI導演2:數字人合成
│   │   │   └── QuickResultView.vue        # 成品展示頁面
│   │   ├── composables/
│   │   │   ├── useQuickWorkflow.ts
│   │   │   ├── useQuickVoiceFlow.ts
│   │   │   └── useQuickHumanFlow.ts
│   │   └── router.ts
│   │
│   └── future-workflows/         # 未來工作流預留
│       ├── from-scratch/
│       ├── story-adaptation/
│       └── template-based/
│
├── api/                          # API 封裝層
│   ├── genhuman/
│   │   ├── voice.ts              # 聲音克隆 API
│   │   ├── digital-human.ts      # 數字人合成 API
│   │   ├── scene.ts              # 場景管理 API
│   │   └── task.ts               # 任務狀態查詢 API
│   └── config.ts
│
├── views/                        # 原有頁面（逐步遷移）
└── router/                       # 主路由配置
    ├── index.ts
    └── workflows.ts              # 工作流路由聚合
```

## 🚀 **快速數字人工作流詳細設計**

### **步驟1：場景選擇頁面 (QuickScenarioView)**

#### **功能需求**
1. **卡片場景選擇**
   - 輸入項目名稱
   - 三種文案準備狀態：
     - A. 用戶已準備好文案：直接輸入文案
     - B. 用戶未準備文案：選擇時長 → 輸入想法 → AI生成文案
     - C. 用戶有聲音檔案：直接跳轉到步驟3（AI導演2）

#### **頁面設計要求**
- 調用 **vidspark前端頁面設計大師智能體** 設計
- iOS風格 + HeyGen控制台風格
- 響應式設計，支援多語言
- 卡片式佈局，清晰的視覺層次

#### **技術實現**
```typescript
// QuickScenarioView.vue 核心邏輯
interface ScenarioData {
  projectName: string
  contentType: 'ready' | 'generate' | 'audio-file'
  content?: string
  duration?: number
  audioFile?: File
  ideas?: string
}
```

### **步驟2：AI編劇頁面 (QuickScriptwriterView)**

#### **功能需求**
1. 顯示步驟1的文案內容
2. 支援文案編輯和修改
3. 返回上一步功能
4. AI文案生成和優化

#### **頁面設計要求**
- 調用 **vidspark前端頁面設計大師智能體** 設計
- 文案編輯器：支援實時預覽
- 字數統計和時長估算
- AI建議和優化提示

### **步驟3：AI導演1-聲音克隆 (QuickVoiceDirectorView)**

#### **功能需求**
1. **聲音選擇**
   - 上傳聲音檔案（mp3, m4a, wav）
   - 選擇用戶資料庫中的聲音（預留功能）
2. **文案確認和修改**
3. **GenHuman API 調用流程**
   ```
   上傳聲音檔案 → 獲取voice_id → 文案+voice_id → 生成聲音合成URL
   ```
4. **試聽和下載功能**
5. **重新選擇聲音功能**

#### **GenHuman API 整合**
```typescript
// useQuickVoiceFlow.ts
export function useQuickVoiceFlow() {
  const cloneVoice = async (audioFile: File, name: string) => {
    // 1. 上傳聲音檔案到 GenHuman
    const voiceResult = await genhumanAPI.cloneVoice({
      audio_file: audioFile,
      name: name,
      description: `Vidspark快速數字人-${name}`
    })
    return voiceResult.voice_id
  }
  
  const generateAudio = async (text: string, voiceId: string) => {
    // 2. 使用voice_id生成語音
    const audioResult = await genhumanAPI.textToSpeech({
      text: text,
      voice_id: voiceId,
      callback_url: getCallbackUrl()
    })
    return audioResult.audio_url
  }
}
```

### **步驟4：AI導演2-數字人合成 (QuickHumanDirectorView)**

#### **功能需求**
1. **聲音來源處理**
   - 來自步驟3的聲音合成URL
   - 或用戶直接上傳的聲音檔案
2. **數字人形象選擇**
3. **GenHuman API 調用流程**
   ```
   數字人形象mp4 → 獲取scene_id → 聲音URL+scene_id → 生成數字人mp4
   ```
4. **結果處理**
   - 存儲到 Zeabur MySQL 資料庫
   - 頁面預覽和下載
   - 添加到「我的項目」

#### **GenHuman API 整合**
```typescript
// useQuickHumanFlow.ts
export function useQuickHumanFlow() {
  const createScene = async (avatarVideo: File, sceneName: string) => {
    // 1. 上傳數字人形象獲取scene_id
    const sceneResult = await genhumanAPI.createScene({
      video_file: avatarVideo,
      scene_name: sceneName,
      callback_url: getCallbackUrl()
    })
    return sceneResult.scene_id
  }
  
  const generateDigitalHuman = async (audioUrl: string, sceneId: string) => {
    // 2. 合成數字人視頻
    const result = await genhumanAPI.generateVideo({
      audio_url: audioUrl,
      scene_id: sceneId,
      callback_url: getCallbackUrl()
    })
    return result.video_url
  }
}
```

### **步驟5：成品展示 (QuickResultView)**

#### **功能需求**
1. 數字人視頻預覽
2. 下載功能
3. 項目信息展示
4. 分享功能
5. 重新製作選項

## 🔧 **GenHuman API 封裝層設計**

### **API 配置管理**
```typescript
// api/genhuman/config.ts
export const genhumanConfig = {
  baseURL: 'https://api.yidevs.com',
  timeout: 300000, // 5分鐘超時
  endpoints: {
    voiceClone: '/app/human/human/Voice/created',
    textToSpeech: '/app/human/human/Index/created',
    createScene: '/app/human/human/Scene/created',
    generateVideo: '/app/human/human/Musetalk/created',
    taskStatus: '/app/human/human/Musetalk/task'
  },
  callbackBaseUrl: 'https://genhuman-digital-human.zeabur.app'
}
```

### **統一錯誤處理**
```typescript
// utils/genhumanClient.ts
export class GenHumanClient {
  async callAPI(endpoint: string, data: any, options = {}) {
    try {
      const response = await fetch(endpoint, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${this.token}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data),
        timeout: genhumanConfig.timeout
      })
      
      if (!response.ok) {
        throw new Error(`API調用失敗: ${response.status}`)
      }
      
      const result = await response.json()
      
      if (result.code !== 200) {
        throw new Error(result.msg || 'API返回錯誤')
      }
      
      return result.data
    } catch (error) {
      console.error(`GenHuman API錯誤:`, error)
      throw error
    }
  }
}
```

## 🚀 **實現任務執行清單 v2.0**

### **階段一：基礎架構和API整合（2-3天）**

#### **任務 1.1：GenHuman API 封裝層**
- [ ] 創建 `api/genhuman/` 目錄結構
- [ ] 實現 `GenHumanClient` 核心類
- [ ] 封裝聲音克隆 API (`voice.ts`)
- [ ] 封裝數字人合成 API (`digital-human.ts`)
- [ ] 封裝場景管理 API (`scene.ts`)
- [ ] 封裝任務狀態查詢 API (`task.ts`)
- [ ] 實現統一錯誤處理和重試機制
- [ ] 添加詳細的控制台日誌記錄

#### **任務 1.2：工作流基礎架構**
- [ ] 創建 `workflows/shared/` 共用組件目錄
- [ ] 實現 `WorkflowBase.vue` 基礎組件
- [ ] 實現 `WorkflowProgress.vue` 進度組件
- [ ] 實現 `useWorkflow.ts` 通用邏輯
- [ ] 實現 `useGenHumanAPI.ts` API調用邏輯
- [ ] 設計 TypeScript 類型定義

#### **任務 1.3：數據庫整合**
- [ ] 設計數字人項目數據表結構
- [ ] 實現項目數據存儲 API
- [ ] 實現文件上傳和URL管理
- [ ] 確保外部URL可訪問性
- [ ] 實現用戶聲音檔案管理（預留）

### **階段二：快速數字人工作流實現（3-4天）**

#### **任務 2.1：場景選擇頁面**
- [ ] **調用 vidspark前端頁面設計大師智能體** 設計頁面
- [ ] 實現 `QuickScenarioView.vue` 組件
- [ ] 實現三種文案準備模式
- [ ] 實現項目名稱輸入和驗證
- [ ] 實現聲音檔案上傳功能
- [ ] 實現跳轉邏輯（直接到步驟3）
- [ ] 添加響應式設計和多語言支援

#### **任務 2.2：AI編劇頁面**
- [ ] **調用 vidspark前端頁面設計大師智能體** 設計頁面
- [ ] 實現 `QuickScriptwriterView.vue` 組件
- [ ] 實現文案編輯器功能
- [ ] 實現AI文案生成功能
- [ ] 實現字數統計和時長估算
- [ ] 實現返回上一步功能
- [ ] 添加文案優化建議

#### **任務 2.3：AI導演1-聲音克隆頁面**
- [ ] **調用 vidspark前端頁面設計大師智能體** 設計頁面
- [ ] 實現 `QuickVoiceDirectorView.vue` 組件
- [ ] 實現聲音檔案上傳功能
- [ ] 整合 GenHuman 聲音克隆 API
- [ ] 實現聲音合成功能
- [ ] 實現試聽和下載功能
- [ ] 實現重新選擇聲音功能
- [ ] 添加進度顯示和錯誤處理

#### **任務 2.4：AI導演2-數字人合成頁面**
- [ ] **調用 vidspark前端頁面設計大師智能體** 設計頁面
- [ ] 實現 `QuickHumanDirectorView.vue` 組件
- [ ] 實現數字人形象選擇功能
- [ ] 整合 GenHuman 場景創建 API
- [ ] 整合 GenHuman 數字人合成 API
- [ ] 實現任務狀態查詢和進度顯示
- [ ] 實現結果存儲到資料庫
- [ ] 添加錯誤處理和重試機制

#### **任務 2.5：成品展示頁面**
- [ ] **調用 vidspark前端頁面設計大師智能體** 設計頁面
- [ ] 實現 `QuickResultView.vue` 組件
- [ ] 實現視頻預覽功能
- [ ] 實現下載功能
- [ ] 實現項目信息展示
- [ ] 實現分享功能
- [ ] 實現重新製作選項
- [ ] 整合到「我的項目」列表

### **階段三：工作流整合和測試（2-3天）**

#### **任務 3.1：工作流路由配置**
- [ ] 配置 `quick-digital-human/router.ts`
- [ ] 整合到主路由系統
- [ ] 實現工作流導航邏輯
- [ ] 實現數據在步驟間傳遞
- [ ] 實現工作流狀態管理

#### **任務 3.2：完整流程測試**
- [ ] 測試場景A：用戶已準備文案流程
- [ ] 測試場景B：AI生成文案流程
- [ ] 測試場景C：直接上傳聲音檔案流程
- [ ] 測試 GenHuman API 調用穩定性
- [ ] 測試錯誤處理和重試機制
- [ ] 測試數據存儲和檢索

#### **任務 3.3：性能優化和錯誤處理**
- [ ] 實現組件懶加載
- [ ] 優化 API 調用性能
- [ ] 實現詳細的錯誤日誌
- [ ] 添加用戶友好的錯誤提示
- [ ] 實現網絡異常處理
- [ ] 優化大文件上傳體驗

### **階段四：生產環境部署和監控（1-2天）**

#### **任務 4.1：生產環境配置**
- [ ] 配置 Zeabur 部署環境
- [ ] 設置 MySQL 資料庫連接
- [ ] 配置文件存儲和CDN
- [ ] 設置環境變數和安全配置
- [ ] 實現健康檢查端點

#### **任務 4.2：監控和日誌**
- [ ] 實現 API 調用監控
- [ ] 設置錯誤報警機制
- [ ] 實現用戶行為分析
- [ ] 設置性能監控指標
- [ ] 實現日誌聚合和分析

#### **任務 4.3：GitHub 同步和 CI/CD**
- [ ] 設置 GitHub 自動同步
- [ ] 配置 CI/CD 流水線
- [ ] 實現自動化測試
- [ ] 設置代碼質量檢查
- [ ] 實現自動部署到 Zeabur

## ⚠️ **重點風險控制**

### **🔴 GenHuman API 相關風險**

#### **1. API 調用穩定性**
**風險描述：**
- GenHuman API 可能出現超時或失敗
- 大文件上傳可能導致網絡異常
- API 限流可能影響用戶體驗

**解決方案：**
- 實現指數退避重試機制
- 設置合理的超時時間（5分鐘）
- 實現任務狀態輪詢機制
- 添加用戶友好的進度提示

#### **2. 文件URL外部可訪問性**
**風險描述：**
- 上傳的文件可能無法被 GenHuman API 訪問
- callback_url 配置錯誤導致回調失敗

**解決方案：**
- 實現文件URL外部可訪問性驗證
- 設置正確的 callback_url 配置
- 實現文件存儲路由檢查機制

### **🟡 技術實現風險**

#### **3. 數據流複雜性**
**風險描述：**
- 多步驟間數據傳遞可能出錯
- 用戶中途退出導致數據丟失

**解決方案：**
- 實現本地存儲備份機制
- 使用 Pinia 進行狀態管理
- 實現數據驗證和恢復機制

## 📊 **成功指標和驗收標準**

### **功能完整性指標**
- 快速數字人工作流完整實現：100%
- GenHuman API 整合成功率：> 95%
- 三種場景流程測試通過率：100%

### **技術質量指標**
- API 調用成功率：> 95%
- 頁面加載時間：< 3 秒
- 數字人生成成功率：> 90%
- 錯誤處理覆蓋率：100%

### **用戶體驗指標**
- 工作流完成率：> 85%
- 用戶操作流暢度：> 4.5/5
- 錯誤恢復時間：< 30 秒

## 🎯 **下一步行動計劃**

### **立即執行（本週）**
1. 創建 GenHuman API 封裝層
2. 實現工作流基礎架構
3. **調用 vidspark前端頁面設計大師智能體** 設計第一個頁面
4. 開始實現場景選擇頁面

### **短期目標（2週內）**
1. 完成快速數字人工作流所有頁面
2. 整合 GenHuman API 調用邏輯
3. 實現完整的數據流和狀態管理
4. 完成基礎測試和錯誤處理

### **中期目標（1個月內）**
1. 完成生產環境部署
2. 實現監控和日誌系統
3. 完成用戶測試和反饋收集
4. 優化性能和用戶體驗

---

**文檔版本：** v2.0  
**創建日期：** 2024-09-11  
**基於版本：** v1.0 (2024-09-08)  
**主要更新：** 專注快速數字人工作流，深度整合 GenHuman API  
**負責人：** Vidspark 開發團隊  
**審核狀態：** 待確認執行清單