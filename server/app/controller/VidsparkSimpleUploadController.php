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
            
            // 檢查文件
            $file = $request->file('video');
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
            $storageDir = base_path() . '/public/vidspark/files/video';
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
