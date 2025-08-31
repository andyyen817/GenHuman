# Vidspark 分類化超時策略設計

## 🎯 **設計目標**
- **用戶體驗優化**：不同場景使用合適的超時時間
- **資源效率**：避免過長等待造成資源浪費
- **錯誤處理**：快速定位和反饋問題
- **可擴展性**：支持未來新的文件類型和API

## 📊 **業務場景分析**

### **文件上傳分類**
| 文件類型 | 大小範圍 | 用途 | 建議超時 | 用戶期望 |
|---------|---------|------|---------|----------|
| 音頻文件 | 1-50MB | 聲音克隆 | 60秒 | 快速 |
| 圖片文件 | 1-20MB | 頭像、素材 | 30秒 | 即時 |
| 短視頻 | 10-200MB | 數字人素材 | 120秒 | 可接受 |
| 長視頻 | 200MB-1GB | 剪輯處理 | 300秒 | 需耐心 |
| 4K視頻 | 1GB-5GB | 高質量剪輯 | 600秒 | 專業需求 |

### **API調用分類**
| API類型 | 處理複雜度 | 預期響應時間 | 建議超時 | 失敗影響 |
|---------|------------|--------------|---------|----------|
| 聲音克隆 | 中等 | 30-60秒 | 90秒 | 中等 |
| 語音合成 | 低 | 10-30秒 | 45秒 | 低 |
| 免費數字人 | 高 | 60-300秒 | 360秒 | 高 |
| 付費數字人 | 中等 | 30-120秒 | 180秒 | 高 |
| 視頻剪輯 | 很高 | 300-1800秒 | 2100秒 | 很高 |

## 🏗️ **技術實現架構**

### **1. 配置驅動的超時系統**
```php
class VidsparkTimeoutConfig {
    const TIMEOUT_CONFIG = [
        // 文件上傳超時
        'upload' => [
            'audio' => ['timeout' => 60, 'description' => '音頻文件上傳'],
            'image' => ['timeout' => 30, 'description' => '圖片文件上傳'],
            'video_small' => ['timeout' => 120, 'description' => '小視頻上傳(≤200MB)'],
            'video_large' => ['timeout' => 300, 'description' => '大視頻上傳(≤1GB)'],
            'video_4k' => ['timeout' => 600, 'description' => '4K視頻上傳(≤5GB)']
        ],
        
        // API調用超時
        'api' => [
            'voice_clone' => ['timeout' => 90, 'description' => '聲音克隆'],
            'voice_synthesis' => ['timeout' => 45, 'description' => '語音合成'],
            'scene_free' => ['timeout' => 360, 'description' => '免費數字人場景'],
            'scene_paid' => ['timeout' => 180, 'description' => '付費數字人場景'],
            'video_synthesis' => ['timeout' => 180, 'description' => '數字人合成'],
            'video_editing' => ['timeout' => 2100, 'description' => '視頻剪輯處理']
        ]
    ];
}
```

### **2. 動態超時選擇邏輯**
```php
class VidsparkTimeoutManager {
    public static function getUploadTimeout($fileType, $fileSize) {
        // 根據文件大小動態選擇
        if ($fileType === 'video') {
            if ($fileSize <= 200 * 1024 * 1024) return 120;      // ≤200MB
            if ($fileSize <= 1024 * 1024 * 1024) return 300;     // ≤1GB
            return 600;                                           // >1GB
        }
        
        return self::TIMEOUT_CONFIG['upload'][$fileType]['timeout'] ?? 60;
    }
    
    public static function getApiTimeout($apiType, $isPaidUser = false) {
        // 付費用戶使用優化的超時時間
        $baseTimeout = self::TIMEOUT_CONFIG['api'][$apiType]['timeout'] ?? 60;
        return $isPaidUser ? $baseTimeout * 0.8 : $baseTimeout;
    }
}
```

### **3. 用戶友好的進度反饋**
```javascript
class VidsparkProgressTracker {
    constructor(operationType, estimatedTime) {
        this.operationType = operationType;
        this.estimatedTime = estimatedTime;
        this.startTime = Date.now();
    }
    
    getProgressInfo() {
        const elapsed = Date.now() - this.startTime;
        const progress = Math.min(elapsed / this.estimatedTime, 0.95);
        
        return {
            progress: Math.round(progress * 100),
            elapsed: Math.round(elapsed / 1000),
            estimated: Math.round(this.estimatedTime / 1000),
            timeLeft: Math.max(0, Math.round((this.estimatedTime - elapsed) / 1000))
        };
    }
}
```

## 📱 **用戶體驗設計**

### **進度提示策略**
| 時間段 | 提示內容 | 用戶操作 |
|-------|----------|----------|
| 0-10秒 | "正在處理..." | 等待 |
| 10-30秒 | "處理中，請稍候..." | 可切換標籤 |
| 30-60秒 | "正在努力處理，預計還需X秒" | 顯示進度條 |
| 60-120秒 | "處理中...您可以先做其他事情" | 允許多任務 |
| >120秒 | "大文件處理需要更多時間，預計還需X分鐘" | 後台處理選項 |

### **錯誤處理策略**
| 超時類型 | 錯誤信息 | 用戶建議 |
|----------|----------|----------|
| 上傳超時 | "文件上傳時間較長，建議使用更小的文件" | 提供壓縮工具 |
| API超時 | "服務器處理繁忙，請稍後重試" | 重試按鈕 |
| 網絡超時 | "網絡連接不穩定，請檢查網絡" | 網絡診斷 |

## ⚙️ **實施計劃**

### **Phase 1：基礎超時分類 (1週)**
1. **實現VidsparkTimeoutConfig類**
2. **更新現有API調用使用分類超時**
3. **添加文件大小檢測邏輯**

### **Phase 2：用戶體驗優化 (1週)**
1. **實現進度追蹤系統**
2. **添加用戶友好的錯誤信息**
3. **實現重試機制**

### **Phase 3：高級功能 (1週)**
1. **後台處理選項**
2. **任務狀態持久化**
3. **用戶通知系統**

## 🔧 **配置參數建議**

### **當前立即實施**
```bash
# 基礎超時配置
VIDSPARK_UPLOAD_TIMEOUT_AUDIO=60
VIDSPARK_UPLOAD_TIMEOUT_IMAGE=30
VIDSPARK_UPLOAD_TIMEOUT_VIDEO_SMALL=120
VIDSPARK_UPLOAD_TIMEOUT_VIDEO_LARGE=300

# API超時配置
VIDSPARK_API_TIMEOUT_VOICE_CLONE=90
VIDSPARK_API_TIMEOUT_VOICE_SYNTHESIS=45
VIDSPARK_API_TIMEOUT_SCENE_FREE=360
VIDSPARK_API_TIMEOUT_SCENE_PAID=180
VIDSPARK_API_TIMEOUT_VIDEO_SYNTHESIS=180
```

### **後續優化方向**
1. **機器學習預測**：根據歷史數據預測處理時間
2. **負載均衡**：根據服務器負載動態調整超時
3. **用戶偏好**：記住用戶的處理偏好
4. **A/B測試**：測試不同超時設置的用戶滿意度

## 📊 **監控指標**

### **關鍵指標**
- **超時率**：各類操作的超時百分比
- **用戶滿意度**：完成vs放棄的比率
- **平均處理時間**：實際處理時間vs預估時間
- **重試率**：用戶重試操作的頻率

### **報警閾值**
- **超時率 > 10%**：需要優化處理邏輯
- **平均處理時間偏差 > 50%**：需要重新校准預估
- **用戶放棄率 > 20%**：需要改善用戶體驗

---

**創建時間**：2025-08-31
**適用範圍**：Vidspark全平台
**優先級**：高
**預計實施時間**：3週
**維護者**：Vidspark技術團隊
