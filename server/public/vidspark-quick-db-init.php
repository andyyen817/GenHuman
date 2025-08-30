<?php
/**
 * Vidspark快速數據庫初始化
 * 只創建必要的vidspark_production_files表
 */

// 設置JSON響應頭
header('Content-Type: application/json; charset=utf-8');

$response = [
    'success' => false,
    'message' => '',
    'results' => [],
    'timestamp' => date('Y-m-d H:i:s')
];

try {
    // 數據庫連接信息
    $host = 'mysql.zeabur.internal';
    $dbname = 'genhuman_db';
    $username = 'root';
    $password = 'fhlkzgNuRQL79C5eFb4036vX2T18YdAn';
    
    // 創建PDO連接
    $pdo = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
    
    $response['results'][] = "✅ 數據庫連接成功";
    
    // 只創建必要的表
    $createTableQuery = "
        CREATE TABLE IF NOT EXISTS vidspark_production_files (
            id BIGINT PRIMARY KEY AUTO_INCREMENT,
            user_id BIGINT DEFAULT 1 COMMENT '用戶ID（暫時默認為1）',
            
            -- 文件信息
            file_type ENUM('video', 'audio', 'image', 'avatar') NOT NULL COMMENT '文件類型',
            original_name VARCHAR(255) COMMENT '原始文件名',
            file_name VARCHAR(255) NOT NULL COMMENT '存儲文件名',
            file_path VARCHAR(500) NOT NULL COMMENT '文件路徑',
            file_url VARCHAR(500) NOT NULL COMMENT '訪問URL',
            file_size BIGINT COMMENT '文件大小(字節)',
            mime_type VARCHAR(100) COMMENT 'MIME類型',
            
            -- 上傳信息
            upload_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '上傳時間',
            user_ip VARCHAR(45) COMMENT '用戶IP',
            
            -- 索引
            INDEX idx_file_type (file_type),
            INDEX idx_upload_time (upload_time),
            INDEX idx_user_id (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Vidspark文件管理表'
    ";
    
    $pdo->exec($createTableQuery);
    $response['results'][] = "✅ 成功創建 vidspark_production_files 表";
    
    // 創建存儲目錄
    $storageDirectories = [
        '/var/www/html/server/public/vidspark/storage/video/2025/08',
        '/var/www/html/server/public/vidspark/storage/audio/2025/08',
        '/var/www/html/server/public/vidspark/storage/images/2025/08'
    ];
    
    foreach ($storageDirectories as $dir) {
        if (!is_dir($dir)) {
            if (mkdir($dir, 0755, true)) {
                $response['results'][] = "✅ 創建目錄: $dir";
            } else {
                $response['results'][] = "❌ 創建目錄失敗: $dir";
            }
        } else {
            $response['results'][] = "ℹ️ 目錄已存在: $dir";
        }
    }
    
    // 驗證表是否創建成功
    $tables = $pdo->query("SHOW TABLES LIKE 'vidspark_production_files'")->fetchAll(PDO::FETCH_COLUMN);
    if (!empty($tables)) {
        $response['results'][] = "✅ 驗證: vidspark_production_files 表已存在";
        
        // 測試插入一條記錄
        $testInsert = $pdo->prepare("
            INSERT INTO vidspark_production_files 
            (file_type, original_name, file_name, file_path, file_url, file_size, mime_type, user_ip) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $testResult = $testInsert->execute([
            'video',
            'test.mp4',
            'test_' . time() . '.mp4',
            'vidspark/storage/video/test.mp4',
            'https://genhuman-digital-human.zeabur.app/vidspark/storage/video/test.mp4',
            1024,
            'video/mp4',
            '127.0.0.1'
        ]);
        
        if ($testResult) {
            $response['results'][] = "✅ 測試插入成功，表格功能正常";
            
            // 刪除測試記錄
            $pdo->exec("DELETE FROM vidspark_production_files WHERE original_name = 'test.mp4'");
            $response['results'][] = "✅ 清理測試數據完成";
        }
    }
    
    $response['success'] = true;
    $response['message'] = 'Vidspark快速數據庫初始化完成';
    
} catch (PDOException $e) {
    $response['message'] = '數據庫錯誤: ' . $e->getMessage();
    $response['results'][] = "❌ PDO錯誤: " . $e->getMessage();
} catch (Exception $e) {
    $response['message'] = '執行錯誤: ' . $e->getMessage();
    $response['results'][] = "❌ 執行錯誤: " . $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
