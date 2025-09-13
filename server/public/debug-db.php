<?php
/**
 * 生產環境數據庫連接診斷腳本
 * 用於診斷Zeabur MySQL連接問題
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

try {
    $result = [
        'timestamp' => date('Y-m-d H:i:s'),
        'environment_check' => [],
        'database_test' => [],
        'file_system_check' => []
    ];
    
    // 檢查文件系統路徑
    $currentDir = __DIR__;
    $apiDir = __DIR__ . '/../api';
    $testDbFile = __DIR__ . '/../api/test-db.php';
    $configDir = __DIR__ . '/../config';
    $databaseFile = __DIR__ . '/../config/database.php';
    
    $result['file_system_check'] = [
        'current_dir' => $currentDir,
        'api_dir' => $apiDir,
        'api_dir_exists' => is_dir($apiDir),
        'test_db_file' => $testDbFile,
        'test_db_exists' => file_exists($testDbFile),
        'config_dir' => $configDir,
        'config_dir_exists' => is_dir($configDir),
        'database_file' => $databaseFile,
        'database_file_exists' => file_exists($databaseFile)
    ];
    
    if (is_dir($apiDir)) {
        $result['file_system_check']['api_files'] = array_diff(scandir($apiDir), ['.', '..']);
    }
    
    if (is_dir($configDir)) {
        $result['file_system_check']['config_files'] = array_diff(scandir($configDir), ['.', '..']);
    }
    
    // 檢查環境變量
    $envVars = ['MYSQL_HOST', 'MYSQL_PORT', 'MYSQL_DATABASE', 'MYSQL_USERNAME', 'MYSQL_PASSWORD', 'ZEABUR', 'ZEABUR_ENVIRONMENT'];
    foreach ($envVars as $var) {
        $result['environment_check'][$var] = [
            'env' => isset($_ENV[$var]) ? ($_ENV[$var] === '' ? 'empty' : 'set') : 'not_set',
            'server' => isset($_SERVER[$var]) ? ($_SERVER[$var] === '' ? 'empty' : 'set') : 'not_set',
            'value' => $var === 'MYSQL_PASSWORD' ? '***' : ($_ENV[$var] ?? $_SERVER[$var] ?? 'not_found')
        ];
    }
    
    // 檢測環境類型
    $isZeaburProduction = isset($_ENV['ZEABUR']) || isset($_SERVER['ZEABUR']) || 
                         isset($_ENV['ZEABUR_ENVIRONMENT']) || isset($_SERVER['ZEABUR_ENVIRONMENT']) ||
                         (isset($_ENV['MYSQL_HOST']) && strpos($_ENV['MYSQL_HOST'], 'zeabur.internal') !== false) ||
                         (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'zeabur.app') !== false) ||
                         (isset($_SERVER['MYSQL_HOST']) && strpos($_SERVER['MYSQL_HOST'], 'zeabur.internal') !== false);
    
    $result['environment_detection'] = [
        'is_zeabur_production' => $isZeaburProduction,
        'detection_reason' => []
    ];
    
    if (isset($_ENV['ZEABUR']) || isset($_SERVER['ZEABUR'])) {
        $result['environment_detection']['detection_reason'][] = 'ZEABUR variable found';
    }
    if (isset($_ENV['ZEABUR_ENVIRONMENT']) || isset($_SERVER['ZEABUR_ENVIRONMENT'])) {
        $result['environment_detection']['detection_reason'][] = 'ZEABUR_ENVIRONMENT variable found';
    }
    if ((isset($_ENV['MYSQL_HOST']) && strpos($_ENV['MYSQL_HOST'], 'zeabur.internal') !== false) ||
        (isset($_SERVER['MYSQL_HOST']) && strpos($_SERVER['MYSQL_HOST'], 'zeabur.internal') !== false)) {
        $result['environment_detection']['detection_reason'][] = 'zeabur.internal host detected';
    }
    
    // 獲取數據庫配置
    if ($isZeaburProduction) {
        $host = $_ENV['MYSQL_HOST'] ?? $_SERVER['MYSQL_HOST'] ?? 'mysql.zeabur.internal';
        $port = $_ENV['MYSQL_PORT'] ?? $_SERVER['MYSQL_PORT'] ?? '3306';
        $database = $_ENV['MYSQL_DATABASE'] ?? $_SERVER['MYSQL_DATABASE'] ?? 'zeabur';
        $username = $_ENV['MYSQL_USERNAME'] ?? $_SERVER['MYSQL_USERNAME'] ?? 'root';
        $password = $_ENV['MYSQL_PASSWORD'] ?? $_SERVER['MYSQL_PASSWORD'] ?? '';
        $result['config_source'] = 'zeabur_environment';
    } else {
        $host = 'localhost';
        $port = '3306';
        $database = 'genhuman';
        $username = 'root';
        $password = '';
        $result['config_source'] = 'local_defaults';
    }
    
    $result['database_config'] = [
        'host' => $host,
        'port' => $port,
        'database' => $database,
        'username' => $username,
        'password' => $password ? '***' : 'empty'
    ];
    
    // 測試數據庫連接
    try {
        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 10
        ]);
        
        $result['database_test'] = [
            'connection' => 'success',
            'server_version' => $pdo->getAttribute(PDO::ATTR_SERVER_VERSION),
            'connection_status' => $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS)
        ];
        
        // 測試簡單查詢
        $stmt = $pdo->query('SELECT 1 as test');
        $testResult = $stmt->fetch();
        $result['database_test']['query_test'] = $testResult['test'] === 1 ? 'success' : 'failed';
        
    } catch (PDOException $e) {
        $result['database_test'] = [
            'connection' => 'failed',
            'error' => $e->getMessage(),
            'error_code' => $e->getCode()
        ];
    } catch (Exception $e) {
        $result['database_test'] = [
            'connection' => 'failed',
            'error' => 'General error: ' . $e->getMessage(),
            'error_code' => $e->getCode()
        ];
    }
    
    $result['success'] = isset($result['database_test']['connection']) && $result['database_test']['connection'] === 'success';
    
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>