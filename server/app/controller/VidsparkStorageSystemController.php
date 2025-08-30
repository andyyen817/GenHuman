<?php

namespace app\controller;

use support\Request;
use support\Response;
use Exception;

/**
 * Vidspark存儲系統控制器
 * 設計理念：簡單、穩健、易於維護
 * 
 * 存儲結構：
 * /vidspark/storage/
 * ├── 2025/08/           # 按年月分組（便於管理和備份）
 * │   ├── video/         # 視頻文件
 * │   ├── audio/         # 音頻文件
 * │   └── images/        # 圖片文件
 * └── users/             # 用戶專用目錄（可選，暫不實現）
 */
class VidsparkStorageSystemController
{
    /**
     * 獲取標準化存儲路徑
     * 統一全項目的路徑計算邏輯
     */
    public static function getStoragePath($type = 'video', $yearMonth = null): string
    {
        if (!$yearMonth) {
            $yearMonth = date('Y/m');
        }
        
        $basePath = base_path() . '/public/vidspark/storage';
        return $basePath . '/' . $yearMonth . '/' . $type;
    }
    
    /**
     * 獲取公開訪問URL
     */
    public static function getPublicUrl($relativePath): string
    {
        $baseUrl = 'https://genhuman-digital-human.zeabur.app';
        return $baseUrl . '/vidspark/storage/' . ltrim($relativePath, '/');
    }
    
    /**
     * 確保目錄存在（自動創建）
     */
    public static function ensureDirectoryExists($path): bool
    {
        if (!is_dir($path)) {
            return mkdir($path, 0755, true);
        }
        return true;
    }
    
    /**
     * 生成安全的文件名
     */
    public static function generateSafeFilename($originalName, $type = 'video'): string
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $timestamp = date('Ymd_His');
        $random = substr(md5(uniqid()), 0, 8);
        
        return "vidspark_{$type}_{$timestamp}_{$random}.{$extension}";
    }
    
    /**
     * 存儲系統狀態檢查
     */
    public function checkStorageStatus(Request $request): Response
    {
        try {
            $currentYearMonth = date('Y/m');
            $types = ['video', 'audio', 'images'];
            $status = [];
            
            foreach ($types as $type) {
                $path = self::getStoragePath($type, $currentYearMonth);
                $status[$type] = [
                    'path' => $path,
                    'exists' => is_dir($path),
                    'writable' => is_dir($path) ? is_writable($path) : false,
                    'auto_created' => false
                ];
                
                // 自動創建目錄
                if (!$status[$type]['exists']) {
                    if (self::ensureDirectoryExists($path)) {
                        $status[$type]['exists'] = true;
                        $status[$type]['writable'] = is_writable($path);
                        $status[$type]['auto_created'] = true;
                    }
                }
            }
            
            // 全局統計
            $allExists = array_reduce($status, function($carry, $item) {
                return $carry && $item['exists'];
            }, true);
            
            $allWritable = array_reduce($status, function($carry, $item) {
                return $carry && $item['writable'];
            }, true);
            
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => $allExists && $allWritable,
                'message' => $allExists && $allWritable ? '存儲系統正常' : '存儲系統需要修復',
                'current_year_month' => $currentYearMonth,
                'storage_status' => $status,
                'summary' => [
                    'all_exists' => $allExists,
                    'all_writable' => $allWritable,
                    'auto_created_count' => count(array_filter($status, function($item) {
                        return $item['auto_created'];
                    }))
                ],
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            
        } catch (Exception $e) {
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => false,
                'message' => '存儲系統檢查失敗',
                'error' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE));
        }
    }
    
    /**
     * 存儲系統初始化（創建所有必要目錄）
     */
    public function initializeStorage(Request $request): Response
    {
        try {
            $results = [];
            $types = ['video', 'audio', 'images'];
            $currentYearMonth = date('Y/m');
            
            // 創建當前月份目錄
            foreach ($types as $type) {
                $path = self::getStoragePath($type, $currentYearMonth);
                
                if (self::ensureDirectoryExists($path)) {
                    $results[] = "✅ 成功創建/驗證目錄: {$type} -> {$path}";
                } else {
                    $results[] = "❌ 目錄創建失敗: {$type} -> {$path}";
                }
            }
            
            // 創建下個月目錄（預創建）
            $nextMonth = date('Y/m', strtotime('+1 month'));
            foreach ($types as $type) {
                $path = self::getStoragePath($type, $nextMonth);
                
                if (self::ensureDirectoryExists($path)) {
                    $results[] = "✅ 預創建下月目錄: {$type} -> {$path}";
                } else {
                    $results[] = "⚠️ 下月目錄創建失敗: {$type} -> {$path}";
                }
            }
            
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => true,
                'message' => '存儲系統初始化完成',
                'results' => $results,
                'paths_created' => [
                    'current_month' => $currentYearMonth,
                    'next_month' => $nextMonth
                ],
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            
        } catch (Exception $e) {
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => false,
                'message' => '存儲系統初始化失敗',
                'error' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE));
        }
    }
}
?>
