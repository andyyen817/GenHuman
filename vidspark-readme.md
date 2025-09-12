# Vidspark 項目說明文檔

## 📋 **項目基本信息**

- **項目名稱**：Vidspark MVP
- **創建日期**：2025年8月29日 上午11:58
- **項目類型**：AI數字人影片生成平台
- **技術架構**：Vue.js + PHP(Webman) + MySQL + GenHuman API
- **部署平台**：Zeabur
- **項目狀態**：MVP開發階段

## 🎯 **項目目標**

Vidspark是基於GenHuman API的獨立數字人影片生成平台，採用**真實生產環境開發策略**，實現：

- **核心功能**：免費數字人影片生成 + 免費聲音克隆體驗
- **用戶體驗**：完整的註冊-創作-管理流程
- **技術特色**：Vidspark API請求八步法，確保異步請求穩定性
- **多語言支持**：繁體中文、簡體中文、英文
- **快速上線**：預計2-3週內完成MVP版本

## 🏗️ **項目架構**

### **同項目多應用架構**
```
D:\genhuman\genhuman\                    # 項目根目錄
├── server/                              # 後端PHP代碼
│   ├── app/                            # 應用核心代碼
│   │   ├── admin/                      # GenHuman管理後台
│   │   ├── api/                        # GenHuman API接口
│   │   └── vidspark/                   # 🆕 Vidspark專用API
│   ├── config/                         # 配置文件
│   │   └── route.php                   # 路由配置（含Vidspark路由）
│   ├── public/                         # 靜態文件目錄
│   │   ├── admin/                     # GenHuman管理後台構建產物
│   │   ├── h5/                        # GenHuman H5構建產物
│   │   ├── vidspark/                  # 🆕 Vidspark用戶端構建產物
│   │   └── vidspark-admin/            # 🆕 Vidspark管理後台構建產物
│   └── start.php                      # 啟動文件
├── admin/                              # GenHuman管理後台前端
├── vidspark/                           # 🆕 Vidspark用戶端前端
│   ├── src/                           # Vue源代碼
│   │   ├── i18n/                     # 獨立多語言系統
│   │   ├── api/                      # GenHuman API封裝
│   │   ├── stores/                   # Pinia狀態管理
│   │   └── views/                    # 頁面組件
│   └── vite.config.ts                # 構建到/vidspark/
└── vidspark-admin/                     # 🆕 Vidspark管理後台前端
    ├── src/                           # Vue源代碼
    └── vite.config.ts                # 構建到/vidspark-admin/
```

### **URL訪問結構**
- Vidspark用戶端：`https://genhuman-digital-human.zeabur.app/vidspark/`
- Vidspark管理後台：`https://genhuman-digital-human.zeabur.app/vidspark-admin/`
- GenHuman系統：保持原有路徑不變

## ⚙️ **環境配置說明**

### **🔧 項目基礎環境配置**

#### **應用基本配置**
```bash
# 應用環境
APP_ENV=production
APP_DEBUG=false
APP_URL=https://genhuman-digital-human.zeabur.app

# PHP運行環境
PHP_VERSION=8.1
COMPOSER_ALLOW_SUPERUSER=1

# Web服務配置
PORT=8080
WEBMAN_LISTEN=0.0.0.0:8787

# 緩存和會話
CACHE_DRIVER=file
SESSION_DRIVER=file

# 文件上傳配置（統一命名規範）- 2025-08-31更新
# 🆕 新增：大文件上傳支持（支持500MB+視頻剪輯）
PHP_UPLOAD_MAX_FILESIZE=1000M          # 🔄 修改：從50M→1000M
PHP_POST_MAX_SIZE=1100M                # 🔄 修改：從100M→1100M  
PHP_MEMORY_LIMIT=2048M                 # 🔄 修改：從256M→2048M
PHP_MAX_EXECUTION_TIME=1800            # 🔄 修改：從300→1800秒
PHP_MAX_INPUT_TIME=1800                # 🔄 修改：從300→1800秒
PHP_MAX_FILE_UPLOADS=20                # ✅ 保持不變
PHP_MAX_INPUT_VARS=10000               # 🆕 新增：大表單支持

# 日誌配置
LOG_CHANNEL=stack
LOG_LEVEL=error

# 應用密鑰
APP_KEY=base64:your-secret-key-here

# 時區設置
APP_TIMEZONE=Asia/Taipei

# CORS配置
CORS_ALLOWED_ORIGINS=*
CORS_ALLOWED_METHODS=GET,POST,PUT,DELETE,OPTIONS
CORS_ALLOWED_HEADERS=Content-Type,Authorization,X-Requested-With
```

#### **GenHuman API配置**
```bash
# GenHuman API基礎配置
API_BASE_URL=https://api.yidevs.com
API_TOKEN=08D7EE7F91D258F27B44DDF59CDDDEDE.1E95F76130BA23D3...
API_DEBUG=false

# 執行配置
MAX_EXECUTION_TIME=300
MEMORY_LIMIT=512M
OPCACHE_ENABLE=1
OPCACHE_MEMORY_CONSUMPTION=256

# HTTP配置
HTTPS_ONLY=true
POST_MAX_SIZE=100M
UPLOAD_MAX_FILESIZE=100M
SESSION_SECURE=true
WEB_PORT=${WEB_PORT}
```

### **🗄️ MySQL數據庫環境配置**

#### **Zeabur MySQL生產配置**
```bash
# 數據庫連接配置
DB_CONNECTION=mysql
DB_HOST=mysql.zeabur.internal
DB_PORT=3306
DB_DATABASE=genhuman_db
DB_USERNAME=root
DB_PASSWORD=fhlkzgNuRQL79C5eFb4036vX2T18YdAn

# 數據庫連接池配置
DB_POOL_MAX_CONNECTIONS=5
DB_POOL_MIN_CONNECTIONS=1
DB_POOL_WAIT_TIMEOUT=3
DB_POOL_IDLE_TIMEOUT=300

# MySQL Root密碼
MYSQL_ROOT_PASSWORD=${PASSWORD}
PASSWORD=fhlkzgNuRQL79C5eFb4036vX2T18YdAn
```

### **🆕 Vidspark專用環境配置**

#### **🚨 Zeabur立即執行配置（2025-08-31）**
**解決文件上傳問題，支持大文件處理**
```bash
# 🔥 立即在Zeabur添加/修改這6個環境變量
PHP_UPLOAD_MAX_FILESIZE=1000M
PHP_POST_MAX_SIZE=1100M  
PHP_MEMORY_LIMIT=2048M
PHP_MAX_EXECUTION_TIME=1800
PHP_MAX_INPUT_TIME=1800
PHP_MAX_INPUT_VARS=10000
```

#### **核心啟用配置**
```bash
VIDSPARK_ENABLED=true
VIDSPARK_ENVIRONMENT=production
VIDSPARK_EIGHT_STEPS_ENABLED=true
```

#### **八步法性能配置**
```bash
VIDSPARK_MAX_RETRY_ATTEMPTS=5
VIDSPARK_POLLING_INTERVAL=5000
VIDSPARK_TIMEOUT_MINUTES=10
```

#### **存儲和CDN配置**
```bash
VIDSPARK_STORAGE_PATH=/var/www/html/server/public/vidspark/storage
VIDSPARK_CDN_DOMAIN=https://genhuman-digital-human.zeabur.app
```

#### **API和回調配置**
```bash
VIDSPARK_GENHUMAN_API_BASE=https://api.yidevs.com
VIDSPARK_GENHUMAN_PRODUCTION_TOKEN=08D7EE7F91D258F27B44DDF59CDDDEDE.1E95F76130BA23D3
VIDSPARK_CALLBACK_BASE_URL=https://genhuman-digital-human.zeabur.app/vidspark-admin/api/callback
VIDSPARK_WEBHOOK_SECRET=vidspark_webhook_secret_2025
```

#### **Vidspark數據庫配置**
```bash
VIDSPARK_DB_HOST=mysql.zeabur.internal
VIDSPARK_DB_DATABASE=genhuman_db
VIDSPARK_DB_USERNAME=root
VIDSPARK_DB_PASSWORD=fhlkzgNuRQL79C5eFb4036vX2T18YdAn
```

#### **用戶額度配置**
```bash
VIDSPARK_DEFAULT_QUOTA_DAILY=3
VIDSPARK_DEFAULT_VOICE_QUOTA=1
```

#### **安全和會話配置**
```bash
VIDSPARK_JWT_SECRET=vidspark_jwt_secret_2025_secure
VIDSPARK_SESSION_TIMEOUT=86400
```

## 🗄️ **數據庫結構**

### **Vidspark專用表**
```sql
-- 在現有genhuman_db中的Vidspark表：

1. vidspark_production_users          # Vidspark用戶表
2. vidspark_production_tasks          # Vidspark任務表
3. vidspark_production_quotas         # Vidspark用戶額度表
4. vidspark_production_files          # Vidspark文件管理表
5. vidspark_production_api_logs       # Vidspark API調用日誌表
```

### **數據庫初始化**
```bash
# 初始化腳本地址
https://genhuman-digital-human.zeabur.app/vidspark-database-init.php

# 測試用戶
用戶名: test@vidspark.com
密碼: test123456
```

## 🔄 **Vidspark API請求八步法**

### **核心流程**
```
步驟1: Vidspark前端發送真實創作請求
步驟2: Vidspark後端調用GenHuman生產API  
步驟3: 立即返回"處理中"狀態
步驟4: GenHuman API處理內容生成
步驟5: 獲取GenHuman API結果
步驟6: 存儲結果到Vidspark數據庫
步驟7: 保存文件到本地存儲
步驟8: 前端獲取最終結果
```

### **技術特點**
- **真實環境**：所有API、數據庫、文件存儲直接使用生產環境
- **異步處理**：避免前端長時間等待
- **狀態追蹤**：完整的任務狀態管理
- **錯誤恢復**：多重保障機制（5次重試）
- **零模擬數據**：所有測試和開發都使用真實數據

## 📁 **核心文檔**

### **技術文檔**
- `vidsparkapi請求八步法v2v0829.md` - API請求八步法詳細說明
- `vidsparkmvp上線任務執行清單v3v0829.md` - MVP上線完整計劃
- `genhuman開發規則.md` - 開發規範和原則
- `genhuman开发錯誤.md` - 錯誤記錄和預防措施

### **設計文檔**
- `design/prototypes/index.html` - UI/UX設計原型
- `design/specs/Design_Spec.md` - 設計規範文檔
- `design/Flowchart.md` - 流程圖文檔

## 🚀 **部署說明**

### **Zeabur部署配置**
```bash
# 部署地址
生產環境: https://genhuman-digital-human.zeabur.app

# 構建配置
前端構建: npm run build
後端啟動: php start.php start

# 健康檢查
主站: https://genhuman-digital-human.zeabur.app/
Vidspark: https://genhuman-digital-human.zeabur.app/vidspark/
管理後台: https://genhuman-digital-human.zeabur.app/vidspark-admin/
```

### **開發流程**
1. **本地開發**：在 `D:\genhuman\genhuman` 目錄下開發
2. **Git提交**：按照超小步修改原則提交
3. **自動部署**：推送到GitHub自動觸發Zeabur部署
4. **生產驗證**：直接在生產環境驗證功能

## 🛠️ **開發規範**

### **核心原則**
- **超小步修改原則**：每次只修改1-2個文件，立即驗證
- **零依賴策略**：不引入新的Composer依賴
- **簡化邏輯原則**：避免複雜的認證和數據庫操作
- **漸進式驗證**：每30分鐘為一個開發週期，立即測試
- **避免終端卡頓**：優先直接修改文件，避免頻繁使用終端指令

### **Git提交規範**
```bash
# 功能開發
git commit -m "[Vidspark] 具體功能描述"

# 錯誤修復
git commit -m "[Vidspark修復] 具體錯誤描述和解決方案"

# 配置更新
git commit -m "[Vidspark配置] 配置更新說明"
```

## 📊 **MVP上線目標**

### **Phase 1：API測試與驗證（第1週）**
- GenHuman生產API核心功能測試
- API封裝層開發
- 生產環境管理後台實現

### **Phase 2：前端核心功能開發（第2週）**
- 真實用戶認證系統
- 生產環境免費數字人功能
- 生產環境聲音克隆功能

### **Phase 3：用戶體驗優化（第3週）**
- 生產環境主控台和項目管理
- 設置和用戶中心
- 系統整合和優化

### **Phase 4：部署和上線（第3週末）**
- Zeabur生產環境最終配置
- 用戶驗收測試
- 性能和可用性驗證

## 📞 **技術支持**

### **問題排查順序**
1. **檢查環境變量**：確認所有20個Vidspark環境變量已設置
2. **驗證數據庫連接**：使用數據庫初始化腳本檢查
3. **檢查API連接**：驗證GenHuman API Token有效性
4. **查看錯誤日誌**：檢查Zeabur部署日誌
5. **參考錯誤文檔**：查看 `genhuman开发錯誤.md`

### **常用檢查命令**
```bash
# 本地開發目錄
cd D:\genhuman\genhuman

# Git操作
git add .
git commit -m "[Vidspark] 修改說明"
git push origin main

# 健康檢查URL
curl https://genhuman-digital-human.zeabur.app/vidspark-database-init.php
```

## 📋 **更新日誌**

### **v1.0 - 2025年8月29日**
- ✅ 創建Vidspark項目架構
- ✅ 配置20個專用環境變量
- ✅ 設計Vidspark API請求八步法
- ✅ 建立數據庫表結構
- ✅ 完成UI/UX設計原型
- ✅ 制定MVP上線計劃

### **v1.1 - 2025年9月1日**
- ✅ **數字人克隆成功突破**：解決所有基礎問題，實現完整數字人克隆流程
- ✅ **新增簡單上傳系統**：`/vidspark-simple-upload/audio` 和 `/vidspark-simple-upload/video`
- ✅ **統一文件存儲路由**：`/vidspark/files/{type}/{filename}` 支持外部API訪問
- ✅ **修復Webman語法錯誤**：統一使用正確的Response格式
- ✅ **建立六步成功修復法**：系統性問題解決方法論
- ✅ **完善錯誤預防機制**：24個錯誤案例記錄和預防措施
- ✅ **大文件上傳解決方案**：使用Base64編碼繞過Zeabur 2M限制
- ✅ **音頻視頻URL格式統一**：解決舊系統URL格式不匹配問題

### **v1.2 - 2025年9月1日（最新）**
- ✅ **完整數字人生成流程**：四步法全流程打通（聲音克隆→數字人克隆→視頻生成→音頻合成）
- ✅ **音頻上傳統一化**：優先使用新系統，Base64備用機制
- ✅ **URL格式標準化**：統一使用 `/vidspark/files/{type}/{filename}` 格式
- ✅ **GenHuman API穩定對接**：包含callback_url和video_url外部可訪問性驗證
- ✅ **錯誤預防機制強化**：建立Webman vs Laravel語法檢查機制
- 🔄 **準備前端模組化開發**：場景模組化架構設計完成

### **🔧 技術架構更新（v1.1）**

#### **新增API端點**
```bash
# 簡單上傳系統（推薦使用）
POST /vidspark-simple-upload/video    # 視頻上傳
POST /vidspark-simple-upload/audio    # 音頻上傳
GET  /vidspark-simple-upload/test     # 系統測試

# 文件訪問（支持外部API）
GET  /vidspark/files/video/{filename} # 視頻文件訪問
GET  /vidspark/files/audio/{filename} # 音頻文件訪問

# 回調端點（關鍵）
ANY  /vidspark-admin/api/callback     # GenHuman API回調
```

#### **存儲結構更新**
```
public/vidspark/files/
├── video/                    # 視頻文件直接存儲
│   ├── video_20250901123456_abc123.mp4
│   └── ...
└── audio/                    # 音頻文件直接存儲
    ├── audio_20250901123456_def456.mp3
    └── ...
```

#### **🚨 強制開發規範更新**

**M. 六步成功修復法（必須遵循）**：
1. **URL外部可訪問性驗證** - 設置URL後立即測試外部訪問
2. **三次修改規則** - 超過3次修改無效立即重新設計
3. **路由定義同步檢查** - 新URL引用必須同步檢查路由定義
4. **框架語法對照表** - 避免重複性語法錯誤
5. **命名規範統一性** - 新開發必須遵循現有命名邏輯
6. **開發文檔同步機制** - 開發完成 = 代碼完成 + 文檔更新

**N. Webman框架特殊語法（重要提醒）**：
```php
// ❌ 錯誤（Laravel語法）
return response()->json([...], 200);

// ✅ 正確（Webman語法）
return new Response(200, [
    'Content-Type' => 'application/json; charset=utf-8'
], json_encode([...], JSON_UNESCAPED_UNICODE));
```

#### **📋 關鍵技術經驗總結（v1.2）**

**🔥 大文件上傳突破（Zeabur限制繞過）**：
- **問題**：Zeabur默認2M上傳限制無法處理視頻文件
- **解決方案**：Base64編碼 + PHP配置優化
- **關鍵技術**：
  ```php
  PHP_UPLOAD_MAX_FILESIZE=1000M
  PHP_POST_MAX_SIZE=1100M
  PHP_MEMORY_LIMIT=2048M
  ```
- **應用場景**：音頻/視頻文件上傳，支持500MB+大文件

**🔧 URL格式統一化突破**：
- **問題**：舊系統URL格式 `/vidspark/storage/...` 與新路由不匹配
- **解決方案**：統一使用 `/vidspark/files/{type}/{filename}` 格式
- **關鍵修復**：前端優先使用新上傳系統，Base64備用機制

**🚨 Webman語法錯誤預防機制**：
- **核心問題**：重複使用Laravel語法 `response()->json()` 而非Webman `new Response()`
- **強制預防**：每次寫Response前暫停5秒確認語法
- **檢查機制**：代碼中強制添加 `// 🚨 Webman語法：new Response()` 註釋

**🔄 模組化架構設計原則**：
- **獨立控制器**：每個場景獨立的Controller和路由
- **故障隔離**：一個模組出問題不影響其他模組
- **簡化邏輯**：避免複雜的跨模組依賴

#### **📋 文檔同步機制（新增）**

**強制同步規則**：
1. **每次技術變更必須同步文檔** - 新API、新路由、新配置
2. **版本號管理** - 重大變更提升小版本號
3. **變更記錄** - 詳細記錄每次變更的技術要點
4. **檢查清單** - 開發完成必須檢查文檔是否更新

**同步檢查清單**：
- [ ] 新API端點是否記錄
- [ ] 存儲結構變更是否更新
- [ ] 環境變量變更是否同步
- [ ] 技術架構圖是否修正
- [ ] 版本號是否正確提升
- [ ] 關鍵技術經驗是否總結

---

**📅 創建日期**：2025年8月29日 上午11:58  
**📅 最後更新**：2025年9月1日  
**📊 版本**：v1.1  
**👨‍💻 維護者**：Vidspark技術團隊  
**🎯 項目狀態**：MVP開發階段 - 核心功能已突破  
**📝 下次更新**：每週五或重大變更時更新
