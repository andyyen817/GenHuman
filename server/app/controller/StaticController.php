<?php
/**
 * 靜態文件控制器
 * 處理H5應用的所有靜態資源
 */

namespace app\controller;

use support\Request;
use support\Response;

class StaticController
{
    /**
     * 處理H5資源文件
     * 支持assets目錄下的所有文件
     */
    public function assets(Request $request, $path): Response
    {
        $filePath = base_path() . '/public/h5/assets/' . $path;
        
        if (!file_exists($filePath) || !is_file($filePath)) {
            return new Response(404, [], 'Asset not found: ' . $path);
        }
        
        // 安全檢查：防止路徑遍歷攻擊
        $realPath = realpath($filePath);
        $basePath = realpath(base_path() . '/public/h5/assets/');
        
        if (!$realPath || !$basePath || strpos($realPath, $basePath) !== 0) {
            return new Response(403, [], 'Access denied');
        }
        
        // 根據文件擴展名設置正確的Content-Type
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $contentTypes = [
            'css' => 'text/css; charset=utf-8',
            'js' => 'application/javascript; charset=utf-8',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject',
            'json' => 'application/json; charset=utf-8',
            'xml' => 'application/xml; charset=utf-8',
            'txt' => 'text/plain; charset=utf-8'
        ];
        
        $contentType = $contentTypes[$extension] ?? 'application/octet-stream';
        
        // 設置緩存策略
        $cacheHeaders = [
            'Content-Type' => $contentType,
            'Cache-Control' => 'public, max-age=31536000, immutable', // 1年緩存，不可變
            'Expires' => gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT',
            'Last-Modified' => gmdate('D, d M Y H:i:s', filemtime($filePath)) . ' GMT',
            'ETag' => '"' . md5_file($filePath) . '"'
        ];
        
        // 檢查客戶端緩存
        $ifNoneMatch = $request->header('if-none-match');
        $ifModifiedSince = $request->header('if-modified-since');
        
        if ($ifNoneMatch && $ifNoneMatch === $cacheHeaders['ETag']) {
            return new Response(304, $cacheHeaders, '');
        }
        
        if ($ifModifiedSince && strtotime($ifModifiedSince) >= filemtime($filePath)) {
            return new Response(304, $cacheHeaders, '');
        }
        
        return new Response(200, $cacheHeaders, file_get_contents($filePath));
    }
    
    /**
     * 處理H5靜態文件
     * 支持static目錄下的所有文件
     */
    public function static(Request $request, $path): Response
    {
        $filePath = base_path() . '/public/h5/static/' . $path;
        
        if (!file_exists($filePath) || !is_file($filePath)) {
            return new Response(404, [], 'Static file not found: ' . $path);
        }
        
        // 安全檢查
        $realPath = realpath($filePath);
        $basePath = realpath(base_path() . '/public/h5/static/');
        
        if (!$realPath || !$basePath || strpos($realPath, $basePath) !== 0) {
            return new Response(403, [], 'Access denied');
        }
        
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $contentTypes = [
            'css' => 'text/css; charset=utf-8',
            'js' => 'application/javascript; charset=utf-8',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'gif' => 'image/gif'
        ];
        
        $contentType = $contentTypes[$extension] ?? 'application/octet-stream';
        
        $headers = [
            'Content-Type' => $contentType,
            'Cache-Control' => 'public, max-age=31536000'
        ];
        
        return new Response(200, $headers, file_get_contents($filePath));
    }
    
    /**
     * 處理H5其他文件
     */
    public function file(Request $request, $filename): Response
    {
        $filePath = base_path() . '/public/h5/' . $filename;
        
        if (!file_exists($filePath) || !is_file($filePath)) {
            return new Response(404, [], 'File not found: ' . $filename);
        }
        
        // 安全檢查
        $realPath = realpath($filePath);
        $basePath = realpath(base_path() . '/public/h5/');
        
        if (!$realPath || !$basePath || strpos($realPath, $basePath) !== 0) {
            return new Response(403, [], 'Access denied');
        }
        
        // 禁止訪問敏感文件
        if (in_array(strtolower($filename), ['.env', '.git', 'composer.json', 'package.json'])) {
            return new Response(403, [], 'Access denied');
        }
        
        return new Response(200, [], file_get_contents($filePath));
    }
    
    /**
     * H5應用主入口
     */
    public function index(): Response
    {
        $filePath = base_path() . '/public/h5/_original_index.html';
        
        if (!file_exists($filePath)) {
            return new Response(404, [], 'H5 App not found');
        }
        
        $htmlContent = file_get_contents($filePath);
        
        // 注入登入修復腳本
        $loginFixScript = '<script src="/h5/login-fix.js"></script>';
        
        // 在</head>標籤前注入腳本
        if (strpos($htmlContent, '</head>') !== false) {
            $htmlContent = str_replace('</head>', $loginFixScript . "\n</head>", $htmlContent);
        } else {
            // 如果沒有</head>標籤，在<body>標籤後注入
            if (strpos($htmlContent, '<body>') !== false) {
                $htmlContent = str_replace('<body>', '<body>' . "\n" . $loginFixScript, $htmlContent);
            } else {
                // 如果都沒有，直接添加到開頭
                $htmlContent = $loginFixScript . "\n" . $htmlContent;
            }
        }
        
        return new Response(200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ], $htmlContent);
    }
}
?>
