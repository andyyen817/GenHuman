<?php
/**
 * 數據庫配置文件
 * 適用於Zeabur MySQL環境
 * 遵循開發規則：安全的數據庫連接配置
 */

// 錯誤報告設置
error_reporting(E_ALL);
ini_set('display_errors', 0); // 生產環境不顯示錯誤
ini_set('log_errors', 1);

// 數據庫配置
class DatabaseConfig {
    // Zeabur MySQL 環境變量
    private static $host;
    private static $port;
    private static $database;
    private static $username;
    private static $password;
    
    public static function init() {
        // 檢測環境：通過檢查Zeabur特有的環境變量來判斷是否為生產環境
        $isZeaburProduction = isset($_ENV['ZEABUR']) || isset($_SERVER['ZEABUR']) || 
                             isset($_ENV['ZEABUR_ENVIRONMENT']) || isset($_SERVER['ZEABUR_ENVIRONMENT']) ||
                             (isset($_ENV['MYSQL_HOST']) && strpos($_ENV['MYSQL_HOST'], 'zeabur.internal') !== false) ||
                             (isset($_SERVER['MYSQL_HOST']) && strpos($_SERVER['MYSQL_HOST'], 'zeabur.internal') !== false);
        
        if ($isZeaburProduction) {
            // 生產環境：使用Zeabur自動注入的環境變量
            self::$host = $_ENV['MYSQL_HOST'] ?? $_SERVER['MYSQL_HOST'] ?? 'mysql.zeabur.internal';
            self::$port = $_ENV['MYSQL_PORT'] ?? $_SERVER['MYSQL_PORT'] ?? '3306';
            self::$database = $_ENV['MYSQL_DATABASE'] ?? $_SERVER['MYSQL_DATABASE'] ?? 'zeabur';
            self::$username = $_ENV['MYSQL_USERNAME'] ?? $_SERVER['MYSQL_USERNAME'] ?? 'root';
            self::$password = $_ENV['MYSQL_PASSWORD'] ?? $_SERVER['MYSQL_PASSWORD'] ?? '';
            
            error_log('DatabaseConfig: Using Zeabur production environment - Host: ' . self::$host);
        } else {
            // 本地開發環境：設置默認值，然後從local.env文件讀取配置
            self::$host = 'localhost';
            self::$port = '3306';
            self::$database = 'genhuman';
            self::$username = 'root';
            self::$password = '';
            
            error_log('DatabaseConfig: Using local development environment');
        }
        
        // 如果是本地環境且local.env文件存在，從文件讀取配置
        if (!$isZeaburProduction && file_exists(__DIR__ . '/local.env')) {
            $envContent = file_get_contents(__DIR__ . '/local.env');
            $envLines = explode("\n", $envContent);
            
            foreach ($envLines as $line) {
                $line = trim($line);
                if (empty($line) || strpos($line, '#') === 0) continue;
                
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value, '"\' ');
                    
                    // 處理環境變量引用
                    if (strpos($value, '${') !== false) {
                        $envVar = str_replace(['${', '}'], '', $value);
                        $value = $_ENV[$envVar] ?? $_SERVER[$envVar] ?? '';
                    }
                    
                    switch ($key) {
                        case 'MYSQL_HOST':
                            if (!empty($value)) self::$host = $value;
                            break;
                        case 'MYSQL_PORT':
                            if (!empty($value)) self::$port = $value;
                            break;
                        case 'MYSQL_DATABASE':
                            if (!empty($value)) self::$database = $value;
                            break;
                        case 'MYSQL_USERNAME':
                            if (!empty($value)) self::$username = $value;
                            break;
                        case 'MYSQL_PASSWORD':
                            if (!empty($value)) self::$password = $value;
                            break;
                    }
                }
            }
        }
        
        // 記錄配置信息（不包含密碼）
        error_log('Database config - Host: ' . self::$host . ', Port: ' . self::$port . ', Database: ' . self::$database . ', Username: ' . self::$username);
    }
    
    public static function getHost() { return self::$host; }
    public static function getPort() { return self::$port; }
    public static function getDatabaseName() { return self::$database; }
    public static function getUsername() { return self::$username; }
    public static function getPassword() { return self::$password; }
}

// 初始化配置
DatabaseConfig::init();

// 全局數據庫連接函數
if (!function_exists('getDatabase')) {
function getDatabase() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $host = DatabaseConfig::getHost();
            $port = DatabaseConfig::getPort();
            $database = DatabaseConfig::getDatabaseName();
            $username = DatabaseConfig::getUsername();
            $password = DatabaseConfig::getPassword();
            
            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 30,
                PDO::ATTR_PERSISTENT => false
            ];
            
            $pdo = new PDO($dsn, $username, $password, $options);
            $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // 設置時區
            $pdo->exec("SET time_zone = '+08:00'");
            
            // 記錄連接成功
            error_log('MySQL database connection established successfully');
            
        } catch (PDOException $e) {
            error_log('MySQL connection failed: ' . $e->getMessage());
            
            // 生產環境不暴露具體錯誤信息
            if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'production') {
                throw new Exception('Database connection failed');
            } else {
                throw new Exception('Database connection failed: ' . $e->getMessage());
            }
        }
    }
    
    return $pdo;
}
}

// 測試數據庫連接
if (!function_exists('testDatabaseConnection')) {
function testDatabaseConnection() {
    try {
        $pdo = getDatabase();
        $stmt = $pdo->query('SELECT 1');
        return true;
    } catch (Exception $e) {
        error_log('Database test failed: ' . $e->getMessage());
        return false;
    }
}
}

// 創建必要的數據庫表
if (!function_exists('initializeTables')) {
function initializeTables() {
    try {
        $pdo = getDatabase();
        
        // 創建用戶表
        $userTableSql = "
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                credits INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_username (username),
                INDEX idx_email (email)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        // 創建項目表
        $projectTableSql = "
            CREATE TABLE IF NOT EXISTS projects (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                name VARCHAR(255) NOT NULL,
                description TEXT,
                project_data JSON,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_user_id (user_id),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        // 創建GenHuman任務表
        $taskTableSql = "
            CREATE TABLE IF NOT EXISTS genhuman_tasks (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                project_id INT,
                task_type ENUM('voice_clone', 'voice_synthesis', 'scene_clone', 'digital_human') NOT NULL,
                task_id VARCHAR(100),
                status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
                request_data JSON,
                response_data JSON,
                error_message TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
                INDEX idx_user_id (user_id),
                INDEX idx_task_type (task_type),
                INDEX idx_status (status),
                INDEX idx_task_id (task_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        // 創建文件上傳表
        $fileTableSql = "
            CREATE TABLE IF NOT EXISTS uploaded_files (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                filename VARCHAR(255) NOT NULL,
                original_name VARCHAR(255) NOT NULL,
                file_path VARCHAR(500) NOT NULL,
                file_type VARCHAR(100),
                file_size BIGINT,
                mime_type VARCHAR(100),
                upload_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_user_id (user_id),
                INDEX idx_file_type (file_type),
                INDEX idx_upload_time (upload_time)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        $pdo->exec($userTableSql);
        $pdo->exec($projectTableSql);
        $pdo->exec($taskTableSql);
        $pdo->exec($fileTableSql);
        
        error_log('Database tables initialized successfully');
        return true;
        
    } catch (Exception $e) {
        error_log('Failed to initialize tables: ' . $e->getMessage());
        return false;
    }
}
}

// 自動初始化表
initializeTables();

?>