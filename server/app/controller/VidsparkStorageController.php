<?php

namespace app\controller;

use support\Request;
use support\Response;
use Exception;

/**
 * Vidspark存儲管理控制器
 */
class VidsparkStorageController
{
    /**
     * 初始化存儲目錄
     */
    public function init(Request $request): Response
    {
        try {
            $basePath = base_path() . '/public/vidspark/storage';
            $audioPaths = [
                $basePath . '/audio/2025/08',
                $basePath . '/video/2025/08',
                $basePath . '/images/2025/08'
            ];
            
            $results = [];
            
            foreach ($audioPaths as $path) {
                if (!is_dir($path)) {
                    if (mkdir($path, 0755, true)) {
                        $results[] = "✅ 創建目錄成功: $path";
                    } else {
                        $results[] = "❌ 創建目錄失敗: $path";
                    }
                } else {
                    $results[] = "✅ 目錄已存在: $path";
                }
            }
            
            // 創建測試文件
            $testFile = $basePath . '/audio/2025/08/test.txt';
            if (file_put_contents($testFile, 'Vidspark storage test file - ' . date('Y-m-d H:i:s'))) {
                $results[] = "✅ 測試文件創建成功: $testFile";
            } else {
                $results[] = "❌ 測試文件創建失敗: $testFile";
            }
            
            // 檢查權限
            $permissions = [];
            foreach ($audioPaths as $path) {
                if (is_dir($path)) {
                    $perms = fileperms($path);
                    $permissions[] = "$path: " . sprintf('%o', $perms & 0777);
                }
            }
            
            // 測試URL訪問
            $testUrls = [];
            if (file_exists($testFile)) {
                $relativeUrl = '/vidspark/storage/audio/2025/08/test.txt';
                $fullUrl = 'https://genhuman-digital-human.zeabur.app' . $relativeUrl;
                $testUrls[] = [
                    'relative' => $relativeUrl,
                    'full' => $fullUrl,
                    'file_exists' => true
                ];
            }
            
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => true,
                'message' => 'Vidspark存儲目錄初始化完成',
                'results' => $results,
                'permissions' => $permissions,
                'test_urls' => $testUrls,
                'base_path' => $basePath,
                'time' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            
        } catch (Exception $e) {
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => false,
                'error' => $e->getMessage(),
                'time' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE));
        }
    }
    
    /**
     * 檢查存儲狀態
     */
    public function status(Request $request): Response
    {
        try {
            $basePath = base_path() . '/public/vidspark/storage';
            $checkPaths = [
                'audio' => $basePath . '/audio',
                'video' => $basePath . '/video', 
                'images' => $basePath . '/images'
            ];
            
            $status = [];
            foreach ($checkPaths as $type => $path) {
                $status[$type] = [
                    'exists' => is_dir($path),
                    'writable' => is_writable($path),
                    'path' => $path
                ];
                
                if (is_dir($path)) {
                    $files = glob($path . '/**/*', GLOB_BRACE);
                    $status[$type]['file_count'] = count($files);
                }
            }
            
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => true,
                'storage_status' => $status,
                'base_path' => $basePath,
                'time' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            
        } catch (Exception $e) {
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => false,
                'error' => $e->getMessage(),
                'time' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE));
        }
    }
}
?>
