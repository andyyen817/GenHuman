<?php
/**
 * 数据库连接测试端点
 * 用于验证数据库连接和文件存储状态
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
    
    // 获取数据库配置信息
    $host = DatabaseConfig::getHost();
    $port = DatabaseConfig::getPort();
    $database = DatabaseConfig::getDatabaseName();
    $username = DatabaseConfig::getUsername();
    
    $result = [
        'success' => true,
        'message' => 'MySQL数据库连接成功',
        'database_info' => [
            'host' => $host,
            'port' => $port,
            'database' => $database,
            'username' => $username
        ],
        'server_info' => $pdo->getAttribute(PDO::ATTR_SERVER_VERSION),
        'connection_status' => $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS)
    ];
    
    // 检查表是否存在
    $tables = [];
    $stmt = $pdo->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }
    $result['tables'] = $tables;
    
    // 检查uploaded_files表结构
    if (in_array('uploaded_files', $tables)) {
        $stmt = $pdo->query("DESCRIBE uploaded_files");
        $result['uploaded_files_structure'] = $stmt->fetchAll();
        
        // 统计文件数量
        $stmt = $pdo->query("SELECT file_type, COUNT(*) as count FROM uploaded_files GROUP BY file_type");
        $result['file_counts'] = $stmt->fetchAll();
        
        // 获取最近的文件
        $stmt = $pdo->query("SELECT filename, file_type, file_size, upload_time FROM uploaded_files ORDER BY upload_time DESC LIMIT 5");
        $result['recent_files'] = $stmt->fetchAll();
    }
    
    // 检查用户表
    if (in_array('users', $tables)) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $result['users_count'] = $stmt->fetch()['count'];
    }
    
    // 检查项目表
    if (in_array('projects', $tables)) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM projects");
        $result['projects_count'] = $stmt->fetch()['count'];
    }
    
    // 检查任务表
    if (in_array('genhuman_tasks', $tables)) {
        $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM genhuman_tasks GROUP BY status");
        $result['task_counts'] = $stmt->fetchAll();
    }
    
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
?>