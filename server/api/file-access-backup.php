<?php
/**
 * 备用文件访问处理器
 * 当静态文件访问失败时，从数据库读取文件内容
 * 解决Zeabur环境文件同步问题
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, HEAD, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../config/database.php';

/**
 * 发送错误响应
 */
function sendError($message, $code = 404) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'error' => true,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * 获取数据库连接
 */
function getDatabase() {
    try {
        DatabaseConfig::init();
        $config = DatabaseConfig::getConfig();
        
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]);
        
        return $pdo;
    } catch (Exception $e) {
        error_log('Database connection error: ' . $e->getMessage());
        throw new Exception('数据库连接失败');
    }
}

/**
 * 从URL路径解析文件信息
 */
function parseFileFromUrl($url) {
    // 解析URL格式：/vidspark/files/{type}/{filename}
    if (preg_match('/\/vidspark\/files\/(\w+)\/(.+)$/', $url, $matches)) {
        return [
            'type' => $matches[1],
            'filename' => $matches[2]
        ];
    }
    return null;
}

/**
 * 从数据库查找文件
 */
function findFileInDatabase($filename, $type) {
    try {
        $pdo = getDatabase();
        
        // 首先尝试从yc_upload表查找
        $sql = "SELECT * FROM yc_upload WHERE url LIKE ? AND type = ? ORDER BY create_time DESC LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['%' . $filename, $type === 'video' ? 1 : 2]);
        $result = $stmt->fetch();
        
        if ($result) {
            return [
                'source' => 'yc_upload',
                'data' => $result
            ];
        }
        
        // 如果没找到，尝试从uploaded_files表查找
        $sql = "SELECT * FROM uploaded_files WHERE filename = ? AND file_type = ? ORDER BY upload_time DESC LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$filename, $type]);
        $result = $stmt->fetch();
        
        if ($result) {
            return [
                'source' => 'uploaded_files',
                'data' => $result
            ];
        }
        
        return null;
        
    } catch (Exception $e) {
        error_log('Database query error: ' . $e->getMessage());
        return null;
    }
}

/**
 * 获取MIME类型
 */
function getMimeType($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $mimeTypes = [
        'mp4' => 'video/mp4',
        'mp3' => 'audio/mpeg',
        'wav' => 'audio/wav',
        'm4a' => 'audio/x-m4a',
        'aac' => 'audio/aac',
        'txt' => 'text/plain'
    ];
    return $mimeTypes[$ext] ?? 'application/octet-stream';
}

// 主处理逻辑
try {
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    $fileInfo = parseFileFromUrl($requestUri);
    
    if (!$fileInfo) {
        sendError('无效的文件路径格式');
    }
    
    $type = $fileInfo['type'];
    $filename = $fileInfo['filename'];
    
    error_log("[FileAccessBackup] 请求文件: {$type}/{$filename}");
    
    // 首先尝试静态文件访问
    $publicPath = realpath(__DIR__ . '/../public') ?: (__DIR__ . '/../public');
    $staticFilePath = $publicPath . '/vidspark/files/' . $type . '/' . $filename;
    
    if (file_exists($staticFilePath)) {
        error_log("[FileAccessBackup] 找到静态文件: {$staticFilePath}");
        
        $mimeType = getMimeType($filename);
        $fileSize = filesize($staticFilePath);
        
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . $fileSize);
        header('Accept-Ranges: bytes');
        header('Cache-Control: public, max-age=86400');
        
        if ($_SERVER['REQUEST_METHOD'] === 'HEAD') {
            exit;
        }
        
        readfile($staticFilePath);
        exit;
    }
    
    error_log("[FileAccessBackup] 静态文件不存在，尝试数据库查找");
    
    // 静态文件不存在，尝试从数据库读取
    $dbFile = findFileInDatabase($filename, $type);
    
    if (!$dbFile) {
        error_log("[FileAccessBackup] 数据库中也未找到文件");
        sendError('文件不存在: ' . $filename);
    }
    
    error_log("[FileAccessBackup] 从数据库找到文件，来源: {$dbFile['source']}");
    
    $data = $dbFile['data'];
    $fileContent = null;
    $mimeType = getMimeType($filename);
    
    if ($dbFile['source'] === 'uploaded_files') {
        // 从uploaded_files表读取二进制数据
        $fileContent = $data['file_data'];
        $mimeType = $data['mime_type'] ?? $mimeType;
    } else {
        // 从yc_upload表，可能需要其他处理
        sendError('暂不支持从yc_upload表读取文件内容');
    }
    
    if (!$fileContent) {
        sendError('文件内容为空');
    }
    
    // 发送文件内容
    header('Content-Type: ' . $mimeType);
    header('Content-Length: ' . strlen($fileContent));
    header('Accept-Ranges: bytes');
    header('Cache-Control: public, max-age=86400');
    
    if ($_SERVER['REQUEST_METHOD'] === 'HEAD') {
        exit;
    }
    
    echo $fileContent;
    
    // 更新访问统计
    if ($dbFile['source'] === 'uploaded_files') {
        try {
            $pdo = getDatabase();
            $sql = "UPDATE uploaded_files SET access_count = access_count + 1, last_access = NOW() WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$data['id']]);
        } catch (Exception $e) {
            error_log('Update access count error: ' . $e->getMessage());
        }
    }
    
} catch (Exception $e) {
    error_log('File access backup error: ' . $e->getMessage());
    sendError('文件访问错误: ' . $e->getMessage(), 500);
}
?>