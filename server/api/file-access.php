<?php
/**
 * 文件訪問端點
 * 提供上傳到數據庫的文件的公開訪問
 * 支持音頻和視頻文件的直接訪問
 */

require_once __DIR__ . '/../config/database.php';

// 錯誤處理函數
function sendError($message, $code = 404) {
    http_response_code($code);
    echo json_encode([
        'success' => false,
        'message' => $message,
        'code' => $code
    ]);
    exit;
}

// 獲取文件ID
$fileId = $_GET['id'] ?? '';

if (empty($fileId)) {
    sendError('File ID is required');
}

// 驗證文件ID格式
if (!preg_match('/^[a-zA-Z0-9_]+$/', $fileId)) {
    sendError('Invalid file ID format');
}

try {
    // 連接數據庫
    $pdo = getDatabase();
    
    // 查詢文件
    $sql = "
        SELECT id, filename, original_filename, file_type, mime_type, 
               file_size, file_data, upload_time, access_count
        FROM uploaded_files 
        WHERE id = ?
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$fileId]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$file) {
        sendError('File not found');
    }
    
    // 更新訪問計數和最後訪問時間
    $updateSql = "
        UPDATE uploaded_files 
        SET access_count = access_count + 1, last_access = NOW() 
        WHERE id = ?
    ";
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute([$fileId]);
    
    // 設置適當的HTTP頭
    header('Content-Type: ' . $file['mime_type']);
    header('Content-Length: ' . $file['file_size']);
    header('Content-Disposition: inline; filename="' . $file['original_filename'] . '"');
    header('Cache-Control: public, max-age=3600'); // 緩存1小時
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', strtotime($file['upload_time'])) . ' GMT');
    
    // 支持範圍請求（用於視頻流）
    $fileSize = $file['file_size'];
    $start = 0;
    $end = $fileSize - 1;
    
    if (isset($_SERVER['HTTP_RANGE'])) {
        $range = $_SERVER['HTTP_RANGE'];
        if (preg_match('/bytes=(\d+)-(\d*)/', $range, $matches)) {
            $start = intval($matches[1]);
            if (!empty($matches[2])) {
                $end = intval($matches[2]);
            }
        }
        
        header('HTTP/1.1 206 Partial Content');
        header('Accept-Ranges: bytes');
        header('Content-Range: bytes ' . $start . '-' . $end . '/' . $fileSize);
        header('Content-Length: ' . ($end - $start + 1));
        
        // 輸出部分內容
        echo substr($file['file_data'], $start, $end - $start + 1);
    } else {
        // 輸出完整文件
        echo $file['file_data'];
    }
    
    // 記錄訪問日誌
    $logData = [
        'timestamp' => date('Y-m-d H:i:s'),
        'file_id' => $fileId,
        'filename' => $file['original_filename'],
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        'range_request' => isset($_SERVER['HTTP_RANGE'])
    ];
    
    $logFile = __DIR__ . '/../logs/file-access.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    file_put_contents($logFile, json_encode($logData) . "\n", FILE_APPEND | LOCK_EX);
    
} catch (Exception $e) {
    error_log('File access error: ' . $e->getMessage());
    sendError('Database error', 500);
}
?>