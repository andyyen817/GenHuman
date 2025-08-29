<?php

namespace app\controller;

use support\Request;
use support\Response;
use PDO;
use PDOException;
use Exception;

/**
 * Vidspark控制器
 * 處理Vidspark相關功能
 */
class VidsparkController
{
    /**
     * 數據庫初始化
     */
    public function databaseInit(Request $request): Response
    {
        $html = $this->generateDatabaseInitHtml();
        
        return new Response(200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Cache-Control' => 'no-cache, no-store, must-revalidate'
        ], $html);
    }
    
    /**
     * 生成數據庫初始化HTML內容
     */
    private function generateDatabaseInitHtml(): string
    {
        ob_start();
        
        echo "<!DOCTYPE html>
<html lang='zh-TW'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Vidspark數據庫初始化</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; background-color: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #27AE60; background: #D4EDDA; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { color: #E74C3C; background: #F8D7DA; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info { color: #2980B9; background: #D1ECF1; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .step { margin: 15px 0; padding: 15px; border-left: 4px solid #3498DB; background: #f8f9fa; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { background: #3498DB; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #2980B9; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🚀 Vidspark數據庫初始化腳本</h1>
        <p><strong>目的</strong>：在現有genhuman_db中創建Vidspark專用表</p>
        <p><strong>版本</strong>：v1.1 (控制器版本) | <strong>日期</strong>：2025-08-29</p>
        <hr>";

        try {
            // 數據庫連接信息
            $host = 'mysql.zeabur.internal';
            $dbname = 'genhuman_db';
            $username = 'root';
            $password = 'fhlkzgNuRQL79C5eFb4036vX2T18YdAn';
            
            echo "<div class='step'>
                    <h3>📋 步驟1：連接到Zeabur MySQL數據庫</h3>
                    <p>正在連接到：{$host}/{$dbname}</p>
                  </div>";
            
            // 創建PDO連接
            $pdo = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8mb4", $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]);
            
            echo "<div class='success'>✅ 數據庫連接成功！</div>";
            
            // 檢查現有表
            echo "<div class='step'>
                    <h3>📋 步驟2：檢查現有數據庫表</h3>
                  </div>";
            
            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            echo "<div class='info'>現有表數量：" . count($tables) . " 個</div>";
            
            // 檢查是否已存在Vidspark表
            $vidsparkTables = array_filter($tables, function($table) {
                return strpos($table, 'vidspark_') === 0;
            });
            
            if (!empty($vidsparkTables)) {
                echo "<div class='error'>⚠️ 發現已存在的Vidspark表：" . implode(', ', $vidsparkTables) . "</div>";
                echo "<div class='info'>將跳過已存在的表，只創建缺失的表</div>";
            }
            
            // 定義要創建的表
            $createTableQueries = $this->getCreateTableQueries();
            
            // 執行表創建
            echo "<div class='step'>
                    <h3>📋 步驟3：創建Vidspark專用表</h3>
                  </div>";
            
            $created_tables = [];
            $skipped_tables = [];
            
            foreach ($createTableQueries as $tableName => $query) {
                try {
                    $pdo->exec($query);
                    $created_tables[] = $tableName;
                    echo "<div class='success'>✅ 成功創建表：{$tableName}</div>";
                } catch (PDOException $e) {
                    if (strpos($e->getMessage(), 'already exists') !== false) {
                        $skipped_tables[] = $tableName;
                        echo "<div class='info'>ℹ️ 表已存在，跳過：{$tableName}</div>";
                    } else {
                        echo "<div class='error'>❌ 創建表失敗：{$tableName}<br>錯誤：{$e->getMessage()}</div>";
                    }
                }
            }
            
            // 驗證表創建結果
            echo "<div class='step'>
                    <h3>📋 步驟4：驗證表創建結果</h3>
                  </div>";
            
            $newTables = $pdo->query("SHOW TABLES LIKE 'vidspark_%'")->fetchAll(PDO::FETCH_COLUMN);
            
            echo "<table>
                    <thead>
                        <tr><th>表名</th><th>狀態</th><th>記錄數</th><th>描述</th></tr>
                    </thead>
                    <tbody>";
            
            foreach ($newTables as $table) {
                $count = $pdo->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
                $status = in_array($table, $created_tables) ? '新創建' : '已存在';
                $descriptions = [
                    'vidspark_production_users' => 'Vidspark用戶表',
                    'vidspark_production_tasks' => 'Vidspark任務表',
                    'vidspark_production_quotas' => 'Vidspark用戶額度表',
                    'vidspark_production_files' => 'Vidspark文件管理表',
                    'vidspark_production_api_logs' => 'Vidspark API調用日誌表'
                ];
                $description = $descriptions[$table] ?? '未知';
                
                echo "<tr>
                        <td>{$table}</td>
                        <td>" . ($status === '新創建' ? '<span style="color: green;">✅ 新創建</span>' : '<span style="color: blue;">ℹ️ 已存在</span>') . "</td>
                        <td>{$count}</td>
                        <td>{$description}</td>
                      </tr>";
            }
            
            echo "</tbody></table>";
            
            // 創建測試用戶（可選）
            echo "<div class='step'>
                    <h3>📋 步驟5：創建測試用戶（可選）</h3>
                  </div>";
            
            if (isset($_GET['create_test_user']) && $_GET['create_test_user'] === 'yes') {
                try {
                    // 檢查測試用戶是否已存在
                    $existing = $pdo->query("SELECT id FROM vidspark_production_users WHERE email = 'test@vidspark.com'")->fetch();
                    
                    if (!$existing) {
                        // 創建測試用戶
                        $pdo->exec("
                            INSERT INTO vidspark_production_users (email, password_hash, username, subscription_type, email_verified) 
                            VALUES ('test@vidspark.com', '" . password_hash('test123456', PASSWORD_DEFAULT) . "', 'Vidspark測試用戶', 'free', 1)
                        ");
                        
                        $testUserId = $pdo->lastInsertId();
                        
                        // 創建測試用戶額度
                        $pdo->exec("
                            INSERT INTO vidspark_production_quotas (user_id, quota_reset_date) 
                            VALUES ({$testUserId}, CURDATE())
                        ");
                        
                        echo "<div class='success'>✅ 成功創建測試用戶：test@vidspark.com (密碼：test123456)</div>";
                    } else {
                        echo "<div class='info'>ℹ️ 測試用戶已存在：test@vidspark.com</div>";
                    }
                } catch (PDOException $e) {
                    echo "<div class='error'>❌ 創建測試用戶失敗：{$e->getMessage()}</div>";
                }
            } else {
                echo "<p><a href='?create_test_user=yes' class='btn'>創建測試用戶</a></p>";
            }
            
            // 總結報告
            echo "<div class='step'>
                    <h3>🎉 初始化完成總結</h3>
                    <div class='success'>
                        <h4>✅ 成功完成Vidspark數據庫初始化！</h4>
                        <ul>
                            <li><strong>數據庫</strong>：genhuman_db</li>
                            <li><strong>新創建表</strong>：" . count($created_tables) . " 個</li>
                            <li><strong>跳過表</strong>：" . count($skipped_tables) . " 個</li>
                            <li><strong>總Vidspark表</strong>：" . count($newTables) . " 個</li>
                        </ul>
                    </div>
                  </div>";
            
            echo "<div class='info'>
                    <h4>🔄 接下來的步驟：</h4>
                    <ol>
                        <li>✅ 數據庫表已準備完成</li>
                        <li>⏭️ 請確認Zeabur環境變量已設置</li>
                        <li>⏭️ 可以開始Phase 1：GenHuman生產API測試</li>
                        <li>⏭️ 建議先測試用戶註冊和登入功能</li>
                    </ol>
                  </div>";

        } catch (PDOException $e) {
            echo "<div class='error'>
                    <h4>❌ 數據庫連接失敗</h4>
                    <p><strong>錯誤信息</strong>：{$e->getMessage()}</p>
                    <p><strong>解決方案</strong>：</p>
                    <ul>
                        <li>檢查Zeabur MySQL服務是否正常運行</li>
                        <li>確認數據庫連接信息是否正確</li>
                        <li>檢查網絡連接是否正常</li>
                    </ul>
                  </div>";
        } catch (Exception $e) {
            echo "<div class='error'>
                    <h4>❌ 執行失敗</h4>
                    <p><strong>錯誤信息</strong>：{$e->getMessage()}</p>
                  </div>";
        }

        echo "
                <hr>
                <p style='text-align: center; color: #666; margin-top: 30px;'>
                    <strong>Vidspark數據庫初始化腳本 v1.1 (控制器版本)</strong><br>
                    創建日期：2025年8月29日 | 
                    <a href='https://genhuman-digital-human.zeabur.app/' target='_blank'>返回主站</a>
                </p>
            </div>
        </body>
        </html>";

        return ob_get_clean();
    }
    
    /**
     * 獲取創建表的SQL語句
     */
    private function getCreateTableQueries(): array
    {
        return [
            'vidspark_production_users' => "
                CREATE TABLE IF NOT EXISTS vidspark_production_users (
                    id BIGINT PRIMARY KEY AUTO_INCREMENT,
                    email VARCHAR(255) UNIQUE NOT NULL COMMENT '用戶郵箱',
                    password_hash VARCHAR(255) NOT NULL COMMENT '密碼哈希',
                    username VARCHAR(100) COMMENT '用戶名稱',
                    avatar_url VARCHAR(500) COMMENT '頭像URL',
                    subscription_type ENUM('free', 'pro', 'enterprise') DEFAULT 'free' COMMENT '訂閱類型',
                    email_verified TINYINT(1) DEFAULT 0 COMMENT '郵箱是否驗證',
                    language_preference VARCHAR(10) DEFAULT 'zh-TW' COMMENT '語言偏好',
                    last_login_at TIMESTAMP NULL COMMENT '最後登入時間',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
                    
                    INDEX idx_email (email),
                    INDEX idx_subscription (subscription_type),
                    INDEX idx_email_verified (email_verified)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Vidspark用戶表'
            ",
            
            'vidspark_production_tasks' => "
                CREATE TABLE IF NOT EXISTS vidspark_production_tasks (
                    id BIGINT PRIMARY KEY AUTO_INCREMENT,
                    user_id BIGINT NOT NULL COMMENT '用戶ID',
                    task_type ENUM('free_avatar', 'voice_clone', 'paid_avatar') NOT NULL COMMENT '任務類型',
                    gendhuman_task_id VARCHAR(100) COMMENT 'GenHuman任務ID',
                    
                    -- 輸入參數
                    input_params JSON COMMENT '輸入參數',
                    
                    -- 任務狀態
                    status ENUM('pending', 'processing', 'api_completed', 'completed', 'error') DEFAULT 'pending' COMMENT '任務狀態',
                    progress INT DEFAULT 0 COMMENT '進度百分比',
                    
                    -- API結果
                    api_result JSON COMMENT 'API返回結果',
                    
                    -- 本地化結果
                    local_video_url VARCHAR(500) COMMENT '本地影片URL',
                    local_thumbnail_url VARCHAR(500) COMMENT '本地縮略圖URL',
                    video_duration INT COMMENT '影片時長(秒)',
                    file_size BIGINT COMMENT '文件大小(字節)',
                    
                    -- 生產環境標記
                    production_mode TINYINT(1) DEFAULT 1 COMMENT '生產模式',
                    zeabur_storage_path VARCHAR(500) COMMENT 'Zeabur存儲路徑',
                    
                    -- 錯誤信息
                    error_message TEXT COMMENT '錯誤信息',
                    retry_count INT DEFAULT 0 COMMENT '重試次數',
                    
                    -- 時間戳
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
                    completed_at TIMESTAMP NULL COMMENT '完成時間',
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
                    
                    INDEX idx_user_status (user_id, status),
                    INDEX idx_gendhuman_task (gendhuman_task_id),
                    INDEX idx_task_type (task_type),
                    INDEX idx_production_mode (production_mode),
                    INDEX idx_created_at (created_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Vidspark生產任務表'
            ",
            
            'vidspark_production_quotas' => "
                CREATE TABLE IF NOT EXISTS vidspark_production_quotas (
                    id BIGINT PRIMARY KEY AUTO_INCREMENT,
                    user_id BIGINT NOT NULL UNIQUE COMMENT '用戶ID',
                    
                    -- 免費額度配置
                    free_avatar_daily INT DEFAULT 3 COMMENT '每日免費數字人次數',
                    free_avatar_used_today INT DEFAULT 0 COMMENT '今日已使用免費數字人次數',
                    voice_clone_total INT DEFAULT 1 COMMENT '聲音克隆總次數',
                    voice_clone_used INT DEFAULT 0 COMMENT '已使用聲音克隆次數',
                    quota_reset_date DATE COMMENT '額度重置日期',
                    
                    -- 生產環境統計
                    total_production_videos INT DEFAULT 0 COMMENT '總生產影片數',
                    total_production_voices INT DEFAULT 0 COMMENT '總生產聲音數',
                    total_storage_used BIGINT DEFAULT 0 COMMENT '總存儲使用量(字節)',
                    last_production_activity TIMESTAMP NULL COMMENT '最後生產活動時間',
                    
                    -- 時間戳
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
                    
                    INDEX idx_user_id (user_id),
                    INDEX idx_quota_reset_date (quota_reset_date)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Vidspark用戶額度表'
            ",
            
            'vidspark_production_files' => "
                CREATE TABLE IF NOT EXISTS vidspark_production_files (
                    id BIGINT PRIMARY KEY AUTO_INCREMENT,
                    task_id BIGINT NOT NULL COMMENT '任務ID',
                    user_id BIGINT NOT NULL COMMENT '用戶ID',
                    
                    -- 文件信息
                    file_type ENUM('video', 'thumbnail', 'audio', 'voice_sample') NOT NULL COMMENT '文件類型',
                    original_url VARCHAR(500) COMMENT '原始GenHuman URL',
                    zeabur_local_path VARCHAR(500) COMMENT 'Zeabur本地路徑',
                    public_access_url VARCHAR(500) COMMENT '公開訪問URL',
                    file_size BIGINT COMMENT '文件大小(字節)',
                    mime_type VARCHAR(100) COMMENT 'MIME類型',
                    
                    -- 存儲狀態
                    storage_status ENUM('downloading', 'stored', 'failed') DEFAULT 'stored' COMMENT '存儲狀態',
                    
                    -- 時間戳
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
                    
                    INDEX idx_task_id (task_id),
                    INDEX idx_user_id (user_id),
                    INDEX idx_file_type (file_type),
                    INDEX idx_storage_status (storage_status)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Vidspark生產文件表'
            ",
            
            'vidspark_production_api_logs' => "
                CREATE TABLE IF NOT EXISTS vidspark_production_api_logs (
                    id BIGINT PRIMARY KEY AUTO_INCREMENT,
                    task_id BIGINT COMMENT '關聯任務ID',
                    
                    -- API調用信息
                    endpoint VARCHAR(200) NOT NULL COMMENT 'API端點',
                    method VARCHAR(10) DEFAULT 'POST' COMMENT 'HTTP方法',
                    status ENUM('success', 'error', 'timeout') NOT NULL COMMENT '調用狀態',
                    attempt INT DEFAULT 1 COMMENT '嘗試次數',
                    response_time INT COMMENT '響應時間(毫秒)',
                    
                    -- 錯誤信息
                    error_message TEXT COMMENT '錯誤信息',
                    http_code INT COMMENT 'HTTP狀態碼',
                    
                    -- 時間戳
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
                    
                    INDEX idx_task_id (task_id),
                    INDEX idx_endpoint (endpoint),
                    INDEX idx_status (status),
                    INDEX idx_created_at (created_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Vidspark生產API調用日誌表'
            "
        ];
    }
}
