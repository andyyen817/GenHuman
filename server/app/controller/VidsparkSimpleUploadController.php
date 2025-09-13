<?php

namespace app\controller;

use support\Request;
use support\Response;
use Exception;

/**
 * Vidspark簡單文件上傳控制器
 * 設計原則：絕對簡單、絕對可靠
 * 
 * 存儲結構：
 * /public/vidspark/files/
 * ├── video/           # 視頻文件直接存儲
 * └── audio/           # 音頻文件直接存儲
 * 
 * URL結構：
 * https://domain.com/vidspark/files/video/filename.mp4
 * 
 * 創建時間：2025-08-31
 * 版本：v1.0 - 歸零重寫版本
 */
class VidsparkSimpleUploadController
{
    /**
     * 上傳視頻文件 - 超簡單版本
     */
    public function uploadVideo(Request $request): Response
    {
        try {
            error_log('[SimpleUpload] ==================== 開始視頻上傳 ====================');
            error_log('[SimpleUpload] 當前時間: ' . date('Y-m-d H:i:s'));
            error_log('[SimpleUpload] 請求方法: ' . $request->method());
            error_log('[SimpleUpload] Content-Type: ' . $request->header('content-type'));
            
            // 調試：檢查所有上傳的文件
            $allFiles = $request->file();
            error_log('[SimpleUpload] 所有上傳文件: ' . print_r($allFiles, true));
            
            // 檢查是否有上傳的視頻文件
            $file = $request->file('video');
            error_log('[SimpleUpload] video文件對象: ' . ($file ? 'exists' : 'null'));
            if (!$file) {
                throw new Exception('沒有接收到視頻文件');
            }
            
            error_log('[SimpleUpload] 接收到文件，開始處理...');
            
            // 獲取文件信息（使用正確的Webman方法）
            $originalName = method_exists($file, 'getUploadName') ? $file->getUploadName() : 'unknown.mp4';
            $fileSize = method_exists($file, 'getSize') ? $file->getSize() : 0;
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            
            error_log('[SimpleUpload] 文件名: ' . $originalName);
            error_log('[SimpleUpload] 文件大小: ' . $fileSize . ' bytes');
            error_log('[SimpleUpload] 文件擴展名: ' . $extension);
            
            // 生成新文件名
            $newFilename = 'video_' . date('YmdHis') . '_' . uniqid() . '.' . $extension;
            
            // 存儲路徑（超簡單）
            $storageDir = dirname(__DIR__, 2) . '/public/vidspark/files/video';
            $fullPath = $storageDir . '/' . $newFilename;
            
            error_log('[SimpleUpload] 存儲目錄: ' . $storageDir);
            error_log('[SimpleUpload] 完整路徑: ' . $fullPath);
            
            // 確保目錄存在
            if (!is_dir($storageDir)) {
                if (!mkdir($storageDir, 0755, true)) {
                    throw new Exception('無法創建存儲目錄');
                }
                error_log('[SimpleUpload] 創建目錄成功: ' . $storageDir);
            }
            
            // 保存文件
            error_log('[SimpleUpload] 開始保存文件...');
            $saved = $file->move($fullPath);
            error_log('[SimpleUpload] 文件保存結果: ' . ($saved ? '成功' : '失敗'));
            
            if (!$saved) {
                throw new Exception('文件保存失敗');
            }
            
            // 最終驗證
            if (!file_exists($fullPath)) {
                throw new Exception('文件保存失敗：文件未出現在目標位置');
            }
            
            $actualSize = filesize($fullPath);
            error_log('[SimpleUpload] 文件實際大小: ' . $actualSize . ' bytes');
            
            if ($actualSize === 0) {
                throw new Exception('文件保存失敗：文件大小為0');
            }
            
            // 生成URL（與存儲路徑完全匹配）
            $fileUrl = 'https://genhuman-digital-human.zeabur.app/vidspark/files/video/' . $newFilename;
            
            error_log('[SimpleUpload] 生成的URL: ' . $fileUrl);
            error_log('[SimpleUpload] ==================== 上傳成功 ====================');
            
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => true,
                'message' => '視頻上傳成功',
                'data' => [
                    'file_url' => $fileUrl,
                    'original_name' => $originalName,
                    'file_size' => $this->formatFileSize($actualSize),
                    'upload_time' => date('Y-m-d H:i:s')
                ]
            ], JSON_UNESCAPED_UNICODE));
            
        } catch (Exception $e) {
            error_log('[SimpleUpload] 上傳失敗: ' . $e->getMessage());
            
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'upload_time' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE));
        }
    }
    
    /**
     * 上傳音頻文件 - 超簡單版本
     */
    public function uploadAudio(Request $request): Response
    {
        try {
            error_log('[SimpleUpload] ==================== 開始音頻上傳 ====================');
            error_log('[SimpleUpload] 當前時間: ' . date('Y-m-d H:i:s'));
            
            // 檢查文件
            $file = $request->file('audio');
            if (!$file) {
                throw new Exception('沒有接收到音頻文件');
            }
            
            error_log('[SimpleUpload] 接收到音頻文件，開始處理...');
            
            // 獲取文件信息（使用正確的Webman方法）
            $originalName = method_exists($file, 'getUploadName') ? $file->getUploadName() : 'unknown.mp3';
            $fileSize = method_exists($file, 'getSize') ? $file->getSize() : 0;
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            
            error_log('[SimpleUpload] 音頻文件名: ' . $originalName);
            error_log('[SimpleUpload] 音頻文件大小: ' . $fileSize . ' bytes');
            error_log('[SimpleUpload] 音頻文件擴展名: ' . $extension);
            
            // 驗證音頻格式
            $allowedExtensions = ['mp3', 'wav', 'm4a', 'aac'];
            if (!in_array(strtolower($extension), $allowedExtensions)) {
                throw new Exception('不支持的音頻格式，僅支持: ' . implode(', ', $allowedExtensions));
            }
            
            // 生成新文件名
            $newFilename = 'audio_' . date('YmdHis') . '_' . uniqid() . '.' . $extension;
            
            // 存儲路徑（超簡單）
            $storageDir = dirname(__DIR__, 2) . '/public/vidspark/files/audio';
            $fullPath = $storageDir . '/' . $newFilename;
            
            error_log('[SimpleUpload] 音頻存儲目錄: ' . $storageDir);
            error_log('[SimpleUpload] 音頻完整路徑: ' . $fullPath);
            
            // 確保目錄存在
            if (!is_dir($storageDir)) {
                if (!mkdir($storageDir, 0755, true)) {
                    throw new Exception('無法創建音頻存儲目錄');
                }
                error_log('[SimpleUpload] 創建音頻目錄成功: ' . $storageDir);
            }
            
            // 保存文件
            error_log('[SimpleUpload] 開始保存音頻文件...');
            $saved = $file->move($fullPath);
            error_log('[SimpleUpload] 音頻文件保存結果: ' . ($saved ? '成功' : '失敗'));
            
            if (!$saved) {
                throw new Exception('音頻文件保存失敗');
            }
            
            // 最終驗證
            if (!file_exists($fullPath)) {
                throw new Exception('音頻文件保存失敗：文件未出現在目標位置');
            }
            
            $actualSize = filesize($fullPath);
            error_log('[SimpleUpload] 音頻文件實際大小: ' . $actualSize . ' bytes');
            
            if ($actualSize === 0) {
                throw new Exception('音頻文件保存失敗：文件大小為0');
            }
            
            // 生成URL（與存儲路徑完全匹配）
            $fileUrl = 'https://genhuman-digital-human.zeabur.app/vidspark/files/audio/' . $newFilename;
            
            error_log('[SimpleUpload] 生成的音頻URL: ' . $fileUrl);
            error_log('[SimpleUpload] ==================== 音頻上傳成功 ====================');
            
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => true,
                'message' => '音頻上傳成功',
                'data' => [
                    'file_url' => $fileUrl,
                    'original_name' => $originalName,
                    'file_size' => $this->formatFileSize($actualSize),
                    'upload_time' => date('Y-m-d H:i:s')
                ]
            ], JSON_UNESCAPED_UNICODE));
            
        } catch (Exception $e) {
            error_log('[SimpleUpload] 音頻上傳失敗: ' . $e->getMessage());
            
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'upload_time' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE));
        }
    }
    
    /**
     * 測試端點
     */
    public function test(Request $request): Response
    {
        return new Response(200, [
            'Content-Type' => 'application/json; charset=utf-8'
        ], json_encode([
            'message' => 'Vidspark簡單上傳系統正常運行',
            'time' => date('Y-m-d H:i:s'),
            'system' => 'Simple Upload v1.0'
        ], JSON_UNESCAPED_UNICODE));
    }
    
    /**
     * 調試端點 - 檢查請求數據
     */
    public function debug(Request $request): Response
    {
        // 直接讀取 php://input
        $directInput = file_get_contents('php://input');
        
        // 觸發手動解析（如果需要的話）
        $allFiles = $request->file();
        
        $debug = [
            'method' => $request->method(),
            'content_type' => $request->header('content-type'),
            'post_data' => $_POST,
            'files_data' => $_FILES,
            'parsed_files' => array_keys($allFiles),
            'direct_input_length' => strlen($directInput),
            'request_raw_input_length' => strlen($request->getRawInput()),
            'direct_input_preview' => substr($directInput, 0, 200),
            'server_vars' => [
                'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
                'CONTENT_TYPE' => $_SERVER['CONTENT_TYPE'] ?? 'unknown',
                'CONTENT_LENGTH' => $_SERVER['CONTENT_LENGTH'] ?? 'unknown'
            ]
        ];
        
        return new Response(200, [
            'Content-Type' => 'application/json; charset=utf-8'
        ], json_encode($debug, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
    
    /**
     * 處理 Base64 編碼的文件上傳
     */
    public function uploadBase64(Request $request): Response
    {
        try {
            error_log('[Base64Upload] ==================== 開始 Base64 上傳 ====================');
            error_log('[Base64Upload] 當前時間: ' . date('Y-m-d H:i:s'));
            
            // 獲取 JSON 數據
            $jsonData = json_decode(file_get_contents('php://input'), true);
            
            if (!$jsonData) {
                throw new Exception('無效的 JSON 數據');
            }
            
            if (!isset($jsonData['data']) || !isset($jsonData['filename'])) {
                throw new Exception('缺少必要的文件數據');
            }
            
            $filename = $jsonData['filename'];
            $filesize = $jsonData['filesize'] ?? 0;
            $filetype = $jsonData['filetype'] ?? 'application/octet-stream';
            $base64Data = $jsonData['data'];
            
            error_log('[Base64Upload] 文件名: ' . $filename);
            error_log('[Base64Upload] 文件大小: ' . $this->formatFileSize($filesize));
            error_log('[Base64Upload] 文件類型: ' . $filetype);
            error_log('[Base64Upload] Base64 數據長度: ' . strlen($base64Data));
            
            // 解碼 Base64 數據
            $fileContent = base64_decode($base64Data);
            if ($fileContent === false) {
                throw new Exception('Base64 解碼失敗');
            }
            
            // 確定文件類型和存儲目錄
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'];
            $audioExtensions = ['mp3', 'wav', 'm4a', 'aac', 'ogg'];
            
            if (in_array($extension, $videoExtensions)) {
                $fileType = 'video';
                $dbType = 3; // 視頻類型
            } elseif (in_array($extension, $audioExtensions)) {
                $fileType = 'audio';
                $dbType = 2; // 音頻類型
            } else {
                $fileType = 'video'; // 默認為視頻
                $dbType = 3;
            }
            
            // 生成唯一文件名
            $uniqueName = $fileType . '_' . date('YmdHis') . '_' . uniqid() . '.' . $extension;
            
            // 設置存儲路徑（統一使用vidspark/files結構）
            $storageDir = dirname(__DIR__, 2) . '/public/vidspark/files/' . $fileType;
            if (!is_dir($storageDir)) {
                mkdir($storageDir, 0755, true);
            }
            
            $filePath = $storageDir . '/' . $uniqueName;
            
            // 保存文件到本地
            if (file_put_contents($filePath, $fileContent) === false) {
                throw new Exception('文件保存失敗');
            }
            
            error_log('[Base64Upload] 文件保存成功: ' . $filePath);
            
            // 生成完整的Zeabur URL
            $fileUrl = 'https://genhuman-digital-human.zeabur.app/vidspark/files/' . $fileType . '/' . $uniqueName;
            
            // 保存到數據庫（yc_upload表）
            try {
                $this->saveToDatabase([
                    'title' => $filename,
                    'url' => $fileUrl,
                    'size' => $this->formatFileSize(strlen($fileContent)),
                    'md5' => md5($fileContent),
                    'ext' => $extension,
                    'type' => $dbType,
                    'adapter' => 'local',
                    'mime_type' => $filetype,
                    'uid' => 0, // 暫時設為0，後續可根據用戶系統調整
                    'admin_uid' => 0,
                    'hidden' => 1,
                    'create_time' => date('Y-m-d H:i:s'),
                    'update_time' => date('Y-m-d H:i:s')
                ]);
                error_log('[Base64Upload] 數據庫記錄保存成功');
            } catch (Exception $dbError) {
                error_log('[Base64Upload] 數據庫保存失敗: ' . $dbError->getMessage());
                // 數據庫保存失敗不影響文件上傳成功
            }
            
            error_log('[Base64Upload] 生成的URL: ' . $fileUrl);
            error_log('[Base64Upload] ==================== Base64上傳成功 ====================');
            
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => true,
                'message' => '文件上傳成功',
                'data' => [
                    'file_url' => $fileUrl,
                    'original_name' => $filename,
                    'file_size' => $this->formatFileSize(strlen($fileContent)),
                    'upload_time' => date('Y-m-d H:i:s'),
                    'method' => 'base64',
                    'storage' => 'zeabur_mysql'
                ]
            ], JSON_UNESCAPED_UNICODE));
            
        } catch (Exception $e) {
            error_log('[Base64Upload] 錯誤: ' . $e->getMessage());
            
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'upload_time' => date('Y-m-d H:i:s'),
                'method' => 'base64'
            ], JSON_UNESCAPED_UNICODE));
        }
    }
    
    /**
     * 保存文件信息到數據庫
     */
    private function saveToDatabase($data): void
    {
        try {
            // 獲取數據庫配置
            $host = $_ENV['MYSQL_HOST'] ?? $_SERVER['MYSQL_HOST'] ?? 'localhost';
            $port = $_ENV['MYSQL_PORT'] ?? $_SERVER['MYSQL_PORT'] ?? '3306';
            $database = $_ENV['MYSQL_DATABASE'] ?? $_SERVER['MYSQL_DATABASE'] ?? 'genhuman';
            $username = $_ENV['MYSQL_USERNAME'] ?? $_SERVER['MYSQL_USERNAME'] ?? 'root';
            $password = $_ENV['MYSQL_PASSWORD'] ?? $_SERVER['MYSQL_PASSWORD'] ?? '';
            
            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
            $pdo = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]);
            
            $sql = "INSERT INTO yc_upload (title, url, size, md5, ext, type, adapter, mime_type, uid, admin_uid, hidden, create_time, update_time) 
                    VALUES (:title, :url, :size, :md5, :ext, :type, :adapter, :mime_type, :uid, :admin_uid, :hidden, :create_time, :update_time)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            
            error_log('[Database] 文件記錄已保存到yc_upload表，ID: ' . $pdo->lastInsertId());
            
        } catch (\PDOException $e) {
            error_log('[Database] PDO錯誤: ' . $e->getMessage());
            throw new Exception('數據庫連接或操作失敗: ' . $e->getMessage());
        }
    }
    
    /**
     * 數據庫連接測試端點
     */
    public function testDatabase(Request $request): Response
    {
        try {
            require_once __DIR__ . '/../../config/database.php';
            
            // 初始化數據庫配置
            DatabaseConfig::init();
            $config = DatabaseConfig::getConfig();
            
            // 創建數據庫連接
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";
            $pdo = new \PDO($dsn, $config['username'], $config['password'], [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]);
            
            $result = [
                'success' => true,
                'message' => '數據庫連接成功',
                'database_info' => [
                    'host' => $config['host'],
                    'port' => $config['port'],
                    'database' => $config['database'],
                    'username' => $config['username']
                ],
                'server_info' => $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION),
                'connection_status' => $pdo->getAttribute(\PDO::ATTR_CONNECTION_STATUS)
            ];
            
            // 檢查表是否存在
            $tables = [];
            $stmt = $pdo->query("SHOW TABLES");
            while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }
            $result['tables'] = $tables;
            
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8',
                'Access-Control-Allow-Origin' => '*'
            ], json_encode($result, JSON_UNESCAPED_UNICODE));
            
        } catch (\Exception $e) {
            return new Response(500, [
                'Content-Type' => 'application/json; charset=utf-8',
                'Access-Control-Allow-Origin' => '*'
            ], json_encode([
                'success' => false,
                'message' => '數據庫連接失敗',
                'error' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
        }
    }
    
    /**
     * 文件列表端點
     */
    public function listFiles(Request $request): Response
    {
        try {
            require_once __DIR__ . '/../../config/database.php';
            
            // 初始化數據庫配置
            DatabaseConfig::init();
            $config = DatabaseConfig::getConfig();
            
            // 創建數據庫連接
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";
            $pdo = new \PDO($dsn, $config['username'], $config['password'], [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]);
            
            $result = [
                'success' => true,
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
            // 獲取uploaded_files表中的文件
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
                    END as has_data
                FROM uploaded_files 
                ORDER BY upload_time DESC 
                LIMIT 50
            ");
            
            $result['uploaded_files'] = $stmt->fetchAll();
            
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8',
                'Access-Control-Allow-Origin' => '*'
            ], json_encode($result, JSON_UNESCAPED_UNICODE));
            
        } catch (\Exception $e) {
            return new Response(500, [
                'Content-Type' => 'application/json; charset=utf-8',
                'Access-Control-Allow-Origin' => '*'
            ], json_encode([
                'success' => false,
                'message' => '獲取文件列表失敗',
                'error' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * 格式化文件大小
     */
    private function formatFileSize($bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . 'MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . 'KB';
        } else {
            return $bytes . 'B';
        }
    }
}
