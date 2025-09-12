<?php
/**
 * 簡化的 Request 類
 * 不依賴 Webman 框架，直接使用 PHP 超全局變量
 */

namespace support;

class Request
{
    private static $instance;
    private static $rawInput = null;
    
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
            // 保存原始輸入數據（只能讀取一次）
            if (self::$rawInput === null) {
                self::$rawInput = file_get_contents('php://input');
            }
        }
        return self::$instance;
    }
    /**
     * 獲取請求路徑
     */
    public function path()
    {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        return $path ?: '/';
    }
    
    /**
     * 獲取請求方法
     */
    public function method()
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }
    
    /**
     * 獲取 POST 數據
     */
    public function post($key = null, $default = null)
    {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }
    
    /**
     * 獲取 GET 數據
     */
    public function get($key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }
    
    /**
     * 獲取請求頭
     */
    public function header($key = null, $default = null)
    {
        if ($key === null) {
            return getallheaders();
        }
        
        $headers = getallheaders();
        $key = strtolower($key);
        
        foreach ($headers as $name => $value) {
            if (strtolower($name) === $key) {
                return $value;
            }
        }
        
        return $default;
    }
    
    /**
     * 獲取原始輸入數據
     */
    public function getRawInput()
    {
        return self::$rawInput ?? '';
    }
    
    /**
     * 手動解析 multipart/form-data
     */
    private function parseMultipartData()
    {
        $contentType = $this->header('content-type');
        if (!$contentType || strpos($contentType, 'multipart/form-data') === false) {
            return;
        }
        
        // 提取 boundary
        if (!preg_match('/boundary=([^;\s]+)/', $contentType, $matches)) {
            return;
        }
        
        $boundary = $matches[1];
        $rawData = self::$rawInput;
        
        if (empty($rawData)) {
            return;
        }
        
        // 分割數據
        $parts = explode('--' . $boundary, $rawData);
        
        foreach ($parts as $part) {
            if (empty(trim($part)) || trim($part) === '--') {
                continue;
            }
            
            // 分離頭部和內容
            $sections = explode("\r\n\r\n", $part, 2);
            if (count($sections) !== 2) {
                continue;
            }
            
            $headers = $sections[0];
            $content = rtrim($sections[1], "\r\n");
            
            // 解析 Content-Disposition
            if (preg_match('/Content-Disposition: form-data; name="([^"]+)"(?:; filename="([^"]+)")?/', $headers, $matches)) {
                $name = $matches[1];
                $filename = isset($matches[2]) ? $matches[2] : null;
                
                if ($filename) {
                    // 這是文件上傳
                    $tempFile = tempnam(sys_get_temp_dir(), 'upload_');
                    file_put_contents($tempFile, $content);
                    
                    $_FILES[$name] = [
                        'name' => $filename,
                        'type' => 'application/octet-stream',
                        'tmp_name' => $tempFile,
                        'error' => UPLOAD_ERR_OK,
                        'size' => strlen($content)
                    ];
                } else {
                    // 這是普通表單字段
                    $_POST[$name] = $content;
                }
            }
        }
    }
    
    /**
     * 獲取上傳的文件
     */
    public function file($key = null)
    {
        // 如果 $_FILES 為空，嘗試手動解析
        if (empty($_FILES) && $this->method() === 'POST') {
            $this->parseMultipartData();
        }
        
        if ($key === null) {
            // 返回所有文件
            $files = [];
            foreach ($_FILES as $name => $file) {
                if ($file['error'] === UPLOAD_ERR_OK) {
                    $files[$name] = new UploadedFile($file);
                }
            }
            return $files;
        }
        
        if (!isset($_FILES[$key])) {
            return null;
        }
        
        $file = $_FILES[$key];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        
        return new UploadedFile($file);
    }
}

/**
 * 簡化的上傳文件類
 */
class UploadedFile
{
    private $file;
    
    public function __construct($file)
    {
        $this->file = $file;
    }
    
    public function getClientOriginalName()
    {
        return $this->file['name'];
    }
    
    // Webman 兼容方法
    public function getUploadName()
    {
        return $this->file['name'];
    }
    
    public function getSize()
    {
        return $this->file['size'];
    }
    
    public function getClientMimeType()
    {
        return $this->file['type'];
    }
    
    public function getPathname()
    {
        return $this->file['tmp_name'];
    }
    
    public function move($destination)
    {
        return move_uploaded_file($this->file['tmp_name'], $destination);
    }
}