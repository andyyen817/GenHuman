# Zeabur正確PHP環境變量設置指南

## 🚨 **問題分析**
當前設置的環境變量使用了錯誤的命名格式，PHP無法識別並應用這些設置。

## ✅ **正確的環境變量命名**

### **方案A：標準PHP-FPM環境變量**
```bash
# 刪除舊的環境變量，添加以下新變量：

# 上傳限制
PHP_INI_UPLOAD_MAX_FILESIZE=1000M
PHP_INI_POST_MAX_SIZE=1100M
PHP_INI_MEMORY_LIMIT=2048M

# 執行時間
PHP_INI_MAX_EXECUTION_TIME=1800
PHP_INI_MAX_INPUT_TIME=1800
PHP_INI_MAX_INPUT_VARS=10000

# 文件上傳
PHP_INI_FILE_UPLOADS=On
PHP_INI_MAX_FILE_UPLOADS=20
```

### **方案B：直接ini設置**
```bash
# 如果方案A不工作，使用這個：
PHP_INI_SCAN_DIR=/usr/local/etc/php/conf.d
PHP_INI_upload_max_filesize=1000M
PHP_INI_post_max_size=1100M
PHP_INI_memory_limit=2048M
PHP_INI_max_execution_time=1800
PHP_INI_max_input_time=1800
```

### **方案C：使用php.ini文件方式**
```bash
# 設置PHP_INI_SCAN_DIR指向我們的配置目錄
PHP_INI_SCAN_DIR=/usr/local/etc/php/conf.d:/var/www/html/server
```

## 📋 **立即執行步驟**

### **步驟1：在Zeabur中刪除舊變量**
刪除這些變量：
- PHP_UPLOAD_MAX_FILESIZE
- PHP_POST_MAX_SIZE  
- PHP_MEMORY_LIMIT
- PHP_MAX_EXECUTION_TIME
- PHP_MAX_INPUT_TIME
- PHP_MAX_INPUT_VARS

### **步驟2：添加新的環境變量**
使用方案A的命名格式添加6個新變量

### **步驟3：重新部署**
點擊"重新部署"按鈕

### **步驟4：驗證**
訪問：https://genhuman-digital-human.zeabur.app/zeabur-config-check

## 🔄 **如果方案A不工作**

### **備用方案：創建PHP配置文件**
我們會在代碼中創建一個PHP配置文件，通過.htaccess或php.ini覆蓋設置

## ⚠️ **重要提醒**
不同的Docker基礎鏡像可能使用不同的環境變量格式。我們提供了多個方案以確保兼容性。

---

**創建時間**：2025-08-31
**優先級**：緊急
**預計解決時間**：15分鐘
