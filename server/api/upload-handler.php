<?php
/**
 * 文件上傳處理器
 * 支持音頻和視頻文件上傳到MySQL數據庫
 * 使用Base64編碼繞過Zeabur上傳限制
 * 生成可公開訪問的URL
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../config/database.php';

// 錯誤處理函數
function sendError($message, $code = 400) {
    http_response_code($code);
    echo json_encode([
        'success' => false,
        'message' => $message,
        'code' => $code
    ]);
    exit;
}

// 成功響應函數
function sendSuccess($data, $message = 'success') {
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// 生成唯一文件ID
function generateFileId() {
    return uniqid() . '_' . time() . '_' . mt_rand(1000, 9999);
}

// 獲取文件MIME類型
function getMimeType($filename) {
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    $mimeTypes = [
        // 音頻格式
        'mp3' => 'audio/mpeg',
        'm4a' => 'audio/mp4',
        'wav' => 'audio/wav',
        'aac' => 'audio/aac',
        'ogg' => 'audio/ogg',
        'flac' => 'audio/flac',
        
        // 視頻格式
        'mp4' => 'video/mp4',
        'avi' => 'video/x-msvideo',
        'mov' => 'video/quicktime',
        'wmv' => 'video/x-ms-wmv',
        'flv' => 'video/x-flv',
        'webm' => 'video/webm',
        'mkv' => 'video/x-matroska'
    ];
    
    return $mimeTypes[$extension] ?? 'application/octet-stream';
}

// 驗證文件類型
function validateFileType($filename, $type) {
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    $allowedTypes = [
        'audio' => ['mp3', 'm4a', 'wav', 'aac', 'ogg', 'flac'],
        'video' => ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv']
    ];
    
    if (!isset($allowedTypes[$type])) {
        return false;
    }
    
    return in_array($extension, $allowedTypes[$type]);
}

// 處理文件上傳
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST method allowed');
}

// 獲取上傳數據
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    sendError('Invalid JSON data');
}

// 驗證必需字段
if (empty($input['file_data']) || empty($input['filename']) || empty($input['type'])) {
    sendError('Missing required fields: file_data, filename, type');
}

$fileData = $input['file_data'];
$filename = $input['filename'];
$type = $input['type']; // 'audio' or 'video'
$description = $input['description'] ?? '';

// 驗證文件類型
if (!validateFileType($filename, $type)) {
    sendError('Invalid file type for ' . $type);
}

// 處理Base64數據
if (strpos($fileData, 'data:') === 0) {
    // 移除data URL前綴
    $fileData = substr($fileData, strpos($fileData, ',') + 1);
}

// 解碼Base64數據
$binaryData = base64_decode($fileData);
if ($binaryData === false) {
    sendError('Invalid Base64 data');
}

// 檢查文件大小（限制50MB）
if (strlen($binaryData) > 50 * 1024 * 1024) {
    sendError('File size too large (max 50MB)');
}

// 生成文件信息
$fileId = generateFileId();
$mimeType = getMimeType($filename);
$fileSize = strlen($binaryData);
$uploadTime = date('Y-m-d H:i:s');

try {
    // 連接數據庫
    $pdo = getDatabase();
    
    // 創建文件表（如果不存在）
    $createTableSql = "
        CREATE TABLE IF NOT EXISTS uploaded_files (
            id VARCHAR(100) PRIMARY KEY,
            filename VARCHAR(255) NOT NULL,
            original_filename VARCHAR(255) NOT NULL,
            file_type ENUM('audio', 'video') NOT NULL,
            mime_type VARCHAR(100) NOT NULL,
            file_size INT NOT NULL,
            file_data LONGBLOB NOT NULL,
            description TEXT,
            upload_time DATETIME NOT NULL,
            access_count INT DEFAULT 0,
            last_access DATETIME NULL,
            INDEX idx_type (file_type),
            INDEX idx_upload_time (upload_time)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($createTableSql);
    
    // 插入文件數據
    $insertSql = "
        INSERT INTO uploaded_files 
        (id, filename, original_filename, file_type, mime_type, file_size, file_data, description, upload_time)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";
    
    $stmt = $pdo->prepare($insertSql);
    $result = $stmt->execute([
        $fileId,
        $fileId . '.' . pathinfo($filename, PATHINFO_EXTENSION),
        $filename,
        $type,
        $mimeType,
        $fileSize,
        $binaryData,
        $description,
        $uploadTime
    ]);
    
    if (!$result) {
        sendError('Failed to save file to database', 500);
    }
    
    // 生成訪問URL
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $baseUrl = $protocol . '://' . $host;
    
    $fileUrl = $baseUrl . '/api/file-access.php?id=' . urlencode($fileId);
    
    // 記錄上傳日誌
    $logData = [
        'timestamp' => $uploadTime,
        'file_id' => $fileId,
        'filename' => $filename,
        'type' => $type,
        'size' => $fileSize,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ];
    
    $logFile = __DIR__ . '/../logs/file-upload.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    file_put_contents($logFile, json_encode($logData) . "\n", FILE_APPEND | LOCK_EX);
    
    // 返回成功響應
    sendSuccess([
        'file_id' => $fileId,
        'filename' => $filename,
        'file_url' => $fileUrl,
        'file_type' => $type,
        'mime_type' => $mimeType,
        'file_size' => $fileSize,
        'upload_time' => $uploadTime
    ], 'File uploaded successfully');
    
} catch (Exception $e) {
    error_log('File upload error: ' . $e->getMessage());
    sendError('Database error: ' . $e->getMessage(), 500);
}
?>