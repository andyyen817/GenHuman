<?php
/**
 * 文件列表端点
 * 显示已上传文件的详细信息
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    require_once __DIR__ . '/../config/database.php';
    
    // 使用内建的数据库连接函数
    $pdo = getDatabase();
    
    $result = [
        'success' => true,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    // 获取uploaded_files表中的文件
    $stmt = $pdo->query("
        SELECT 
            id,
            filename,
            original_filename,
            file_type,
            file_size,
            mime_type,
            upload_time,
            access_count,
            last_access,
            CASE 
                WHEN file_data IS NOT NULL THEN 'YES'
                ELSE 'NO'
            END as has_data,
            LENGTH(file_data) as data_size
        FROM uploaded_files 
        ORDER BY upload_time DESC 
        LIMIT 20
    ");
    
    $uploadedFiles = $stmt->fetchAll();
    
    // 为每个文件生成访问URL
    foreach ($uploadedFiles as &$file) {
        $file['file_url'] = "https://genhuman-digital-human.zeabur.app/vidspark/files/{$file['file_type']}/{$file['filename']}";
        $file['file_size_formatted'] = formatBytes($file['file_size']);
        if ($file['data_size']) {
            $file['data_size_formatted'] = formatBytes($file['data_size']);
        }
    }
    
    $result['uploaded_files'] = $uploadedFiles;
    $result['uploaded_files_count'] = count($uploadedFiles);
    
    // 获取yc_upload表中的文件
    try {
        $stmt = $pdo->query("
            SELECT 
                id,
                url,
                type,
                size,
                create_time,
                CASE type
                    WHEN 1 THEN 'video'
                    WHEN 2 THEN 'audio'
                    ELSE 'unknown'
                END as type_name
            FROM yc_upload 
            ORDER BY create_time DESC 
            LIMIT 20
        ");
        
        $ycUploadFiles = $stmt->fetchAll();
        
        foreach ($ycUploadFiles as &$file) {
            $file['size_formatted'] = formatBytes($file['size']);
        }
        
        $result['yc_upload_files'] = $ycUploadFiles;
        $result['yc_upload_files_count'] = count($ycUploadFiles);
        
    } catch (Exception $e) {
        $result['yc_upload_error'] = $e->getMessage();
    }
    
    // 统计信息
    $stats = [];
    
    // uploaded_files统计
    $stmt = $pdo->query("
        SELECT 
            file_type,
            COUNT(*) as count,
            SUM(file_size) as total_size,
            AVG(file_size) as avg_size,
            MAX(upload_time) as latest_upload
        FROM uploaded_files 
        GROUP BY file_type
    ");
    $stats['uploaded_files_by_type'] = $stmt->fetchAll();
    
    // 格式化统计数据
    foreach ($stats['uploaded_files_by_type'] as &$stat) {
        $stat['total_size_formatted'] = formatBytes($stat['total_size']);
        $stat['avg_size_formatted'] = formatBytes($stat['avg_size']);
    }
    
    $result['statistics'] = $stats;
    
    // 检查静态文件目录
    $publicPath = realpath(__DIR__ . '/../public') ?: (__DIR__ . '/../public');
    $vidspark_files_path = $publicPath . '/vidspark/files';
    
    $staticFiles = [];
    if (is_dir($vidspark_files_path)) {
        $types = ['video', 'audio'];
        foreach ($types as $type) {
            $typePath = $vidspark_files_path . '/' . $type;
            if (is_dir($typePath)) {
                $files = scandir($typePath);
                $files = array_filter($files, function($f) { return $f !== '.' && $f !== '..'; });
                
                foreach ($files as $file) {
                    $filePath = $typePath . '/' . $file;
                    if (is_file($filePath)) {
                        $staticFiles[] = [
                            'type' => $type,
                            'filename' => $file,
                            'size' => filesize($filePath),
                            'size_formatted' => formatBytes(filesize($filePath)),
                            'modified' => date('Y-m-d H:i:s', filemtime($filePath)),
                            'url' => "https://genhuman-digital-human.zeabur.app/vidspark/files/{$type}/{$file}"
                        ];
                    }
                }
            }
        }
    }
    
    $result['static_files'] = $staticFiles;
    $result['static_files_count'] = count($staticFiles);
    $result['static_files_path'] = $vidspark_files_path;
    
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'PDO错误',
        'message' => $e->getMessage(),
        'code' => $e->getCode()
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => '一般错误',
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

/**
 * 格式化字节大小
 */
function formatBytes($size, $precision = 2) {
    if ($size == 0) return '0 B';
    
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $base = log($size, 1024);
    
    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $units[floor($base)];
}
?>