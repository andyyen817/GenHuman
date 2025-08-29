<?php
/**
 * Vidspark存儲目錄初始化腳本
 * 用於創建必要的存儲目錄和測試文件
 */

header('Content-Type: application/json; charset=utf-8');

try {
    $basePath = __DIR__ . '/vidspark/storage';
    $audioPaths = [
        $basePath . '/audio/2025/08',
        $basePath . '/video/2025/08',
        $basePath . '/images/2025/08'
    ];
    
    $results = [];
    
    foreach ($audioPaths as $path) {
        if (!is_dir($path)) {
            if (mkdir($path, 0755, true)) {
                $results[] = "✅ 創建目錄成功: $path";
            } else {
                $results[] = "❌ 創建目錄失敗: $path";
            }
        } else {
            $results[] = "✅ 目錄已存在: $path";
        }
    }
    
    // 創建測試文件
    $testFile = $basePath . '/audio/2025/08/test.txt';
    if (file_put_contents($testFile, 'Vidspark storage test file - ' . date('Y-m-d H:i:s'))) {
        $results[] = "✅ 測試文件創建成功: $testFile";
    } else {
        $results[] = "❌ 測試文件創建失敗: $testFile";
    }
    
    // 檢查權限
    $permissions = [];
    foreach ($audioPaths as $path) {
        if (is_dir($path)) {
            $perms = fileperms($path);
            $permissions[] = "$path: " . sprintf('%o', $perms & 0777);
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Vidspark存儲目錄初始化完成',
        'results' => $results,
        'permissions' => $permissions,
        'base_path' => $basePath,
        'time' => date('Y-m-d H:i:s')
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'time' => date('Y-m-d H:i:s')
    ], JSON_UNESCAPED_UNICODE);
}
?>
