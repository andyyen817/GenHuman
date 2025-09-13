<?php
/**
 * 簡化的 Webman App 類
 * 用於處理 HTTP 請求路由
 */

namespace support;

use Webman\Route;
use Webman\Config;
use support\Request;
use support\Response;

// 全局輔助函數
if (!function_exists('base_path')) {
    function base_path($path = '') {
        $basePath = dirname(__DIR__);
        return $path ? $basePath . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : $basePath;
    }
}

class App
{
    /**
     * 運行應用
     */
    public static function run()
    {
        try {
            // 載入配置
            self::loadAllConfig();
            
            // 處理請求
            $request = Request::getInstance();
            $path = $request->path();
            $method = $request->method();
            
            // 載入路由
            self::loadRoutes();
            
            // 處理路由
            $response = self::handleRequest($request);
            
            // 發送響應
            self::sendResponse($response);
            
        } catch (\Exception $e) {
            // 錯誤處理
            http_response_code(500);
            echo json_encode([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }
    
    /**
     * 載入所有配置
     */
    public static function loadAllConfig($exclude = [])
    {
        // 簡化的配置載入，跳過路由文件
        // 不載入需要 Webman 框架的路由文件
    }
    
    /**
     * 載入路由
     */
    private static function loadRoutes()
    {
        // 跳過載入路由文件，直接在 handleRequest 中處理
    }
    
    /**
     * 處理請求
     */
    private static function handleRequest($request)
    {
        $path = $request->path();
        $method = $request->method();
        
        // 處理根路徑，返回 index.html
        if ($path === '/') {
            $indexPath = __DIR__ . '/../public/index.html';
            if (file_exists($indexPath)) {
                self::serveStaticFile($indexPath);
                return;
            }
        }
        
        // 處理其他靜態文件
        if ($path !== '/' && file_exists(__DIR__ . '/../public' . $path)) {
            self::serveStaticFile(__DIR__ . '/../public' . $path);
            return;
        }
        
        // 處理 VidsparkSimpleUploadController 的路由
        if (strpos($path, '/vidspark-simple-upload/') === 0) {
            $action = str_replace('/vidspark-simple-upload/', '', $path);
            
            $controller = new \app\controller\VidsparkSimpleUploadController();
            
            // 路由映射
            $routeMap = [
                'test' => ['method' => 'GET', 'action' => 'test'],
                'upload' => ['method' => 'POST', 'action' => 'uploadVideo'],
                'upload-video' => ['method' => 'POST', 'action' => 'uploadVideo'],
                'upload-base64' => ['method' => 'POST', 'action' => 'uploadBase64'],
                'audio' => ['method' => 'POST', 'action' => 'uploadAudio'],
                'video' => ['method' => 'POST', 'action' => 'uploadVideo'],
                'debug' => ['method' => 'ANY', 'action' => 'debug']
            ];
            
            if (isset($routeMap[$action])) {
                $route = $routeMap[$action];
                if (($method === $route['method'] || $route['method'] === 'ANY') && method_exists($controller, $route['action'])) {
                    $methodName = $route['action'];
                    return $controller->$methodName($request);
                }
            }
        }
        
        // 默認返回 404
        return new Response(404, ['Content-Type' => 'application/json'], json_encode([
            'error' => 'Not Found',
            'path' => $path,
            'method' => $method
        ]));
    }
    
    /**
     * 發送響應
     */
    private static function sendResponse($response)
    {
        if ($response instanceof Response) {
            http_response_code($response->getStatusCode());
            
            foreach ($response->getHeaders() as $name => $value) {
                header($name . ': ' . $value);
            }
            
            echo $response->getBody();
        } else {
            echo $response;
        }
    }
    
    /**
     * 服務靜態文件
     */
    private static function serveStaticFile($filePath)
    {
        if (!file_exists($filePath)) {
            http_response_code(404);
            echo '404 Not Found';
            return;
        }
        
        $mimeType = self::getMimeType($filePath);
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($filePath));
        
        readfile($filePath);
    }
    
    /**
     * 獲取文件 MIME 類型
     */
    private static function getMimeType($filePath)
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        $mimeTypes = [
            'html' => 'text/html',
            'htm' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'mp4' => 'video/mp4',
            'mp3' => 'audio/mpeg',
            'txt' => 'text/plain'
        ];
        
        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }
}