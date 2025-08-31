# Zeabur大文件上傳環境變量配置清單

## 🎯 **目標**
支持500MB+大視頻文件上傳和處理，解決未來視頻剪輯需求

## 📋 **Zeabur環境變量設置清單**

### **PHP上傳限制配置**
```bash
# 文件上傳大小限制
PHP_UPLOAD_MAX_FILESIZE=1000M
PHP_POST_MAX_SIZE=1100M
PHP_MAX_FILE_UPLOADS=20

# 內存和執行時間限制
PHP_MEMORY_LIMIT=2048M
PHP_MAX_EXECUTION_TIME=1800
PHP_MAX_INPUT_TIME=1800
PHP_MAX_INPUT_VARS=10000

# 臨時文件配置
PHP_UPLOAD_TMP_DIR=/tmp
PHP_AUTO_PREPEND_FILE=
PHP_AUTO_APPEND_FILE=
```

### **Webman框架配置**
```bash
# Webman特殊配置
WEBMAN_MAX_PACKAGE_SIZE=1073741824
WEBMAN_MAX_REQUEST_SIZE=1100000000
WEBMAN_WORKER_COUNT=4
WEBMAN_TASK_WORKER_COUNT=2

# 容器資源配置
CONTAINER_MEMORY_LIMIT=4096Mi
CONTAINER_CPU_LIMIT=2000m
```

### **存儲和緩存配置**
```bash
# 存儲路徑配置
VIDSPARK_STORAGE_PATH=/var/www/html/server/public/vidspark/storage
VIDSPARK_TEMP_PATH=/tmp/vidspark_upload
VIDSPARK_CHUNK_SIZE=10485760

# 緩存配置
REDIS_ENABLED=true
REDIS_HOST=redis.zeabur.internal
REDIS_PORT=6379
REDIS_PASSWORD=
```

## 🔧 **Docker配置優化**

### **Dockerfile增強配置**
```dockerfile
# 在現有Dockerfile中添加
ENV PHP_UPLOAD_MAX_FILESIZE=1000M
ENV PHP_POST_MAX_SIZE=1100M
ENV PHP_MEMORY_LIMIT=2048M
ENV PHP_MAX_EXECUTION_TIME=1800

# 創建必要目錄
RUN mkdir -p /tmp/vidspark_upload && \
    chmod 777 /tmp/vidspark_upload
```

## 📊 **配置驗證命令**

### **檢查PHP配置**
```php
// 添加到診斷頁面
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "memory_limit: " . ini_get('memory_limit') . "\n";
echo "max_execution_time: " . ini_get('max_execution_time') . "\n";
```

### **Zeabur部署後驗證URL**
```
✅ 配置檢查: https://genhuman-digital-human.zeabur.app/vidspark-upload/video-diagnosis
✅ 大文件測試: https://genhuman-digital-human.zeabur.app/vidspark-video-upload-debug
✅ 系統診斷: https://genhuman-digital-human.zeabur.app/vidspark-path-debug
```

## 🚨 **重要部署順序**

### **步驟1: 設置Zeabur環境變量**
在Zeabur控制台 → 環境變量 → 添加上述所有配置

### **步驟2: 重新部署容器**
```bash
# Git推送觸發自動部署
cd D:\genhuman\genhuman
git add .
git commit -m "[性能優化] 支持500MB+大文件上傳配置"
git push origin main
```

### **步驟3: 驗證配置生效**
1. 檢查診斷頁面顯示新的限制值
2. 測試上傳100MB+文件
3. 驗證存儲目錄自動創建

## 📈 **性能預期**

### **文件大小支持**
- ✅ **小文件 (1-50MB)**: 秒級上傳
- ✅ **中等文件 (50-200MB)**: 30秒內上傳
- ✅ **大文件 (200-500MB)**: 2-5分鐘上傳
- ✅ **超大文件 (500MB-1GB)**: 5-10分鐘上傳

### **系統資源使用**
- **內存使用**: 最大2GB
- **CPU使用**: 中等負載
- **磁盤I/O**: 高負載期間
- **網絡帶寬**: 根據文件大小動態調整

## 🔍 **故障排除**

### **常見問題**
1. **413 Payload Too Large**: 檢查`post_max_size`設置
2. **500 Internal Server Error**: 檢查`memory_limit`和`max_execution_time`
3. **上傳中斷**: 檢查網絡穩定性和`max_input_time`
4. **存儲空間不足**: 監控磁盤使用情況

### **緊急回滾方案**
如果大文件配置導致系統問題，可以快速回滾到保守配置：
```bash
PHP_UPLOAD_MAX_FILESIZE=50M
PHP_POST_MAX_SIZE=100M
PHP_MEMORY_LIMIT=512M
```

---

**創建時間**: 2025-08-31
**適用環境**: Zeabur生產環境
**維護者**: Vidspark開發團隊
**版本**: v1.0
