<?php

namespace app\controller;

use support\Request;
use support\Response;
use support\Db;
use Exception;

/**
 * Vidspark文件上傳控制器
 * 處理音頻、視頻文件上傳，保存到Zeabur存儲
 * 
 * 創建時間：2025-08-29
 * 版本：v1.0
 * 遵循：@genhuman開發規則.md 超小步修改原則
 */
class VidsparkFileUploadController
{
    /**
     * 上傳音頻文件
     */
    public function uploadAudio(Request $request): Response
    {
        try {
            $file = $request->file('audio');
            if (!$file || !$file->isValid()) {
                throw new Exception('沒有上傳文件或文件無效');
            }

            // 驗證文件類型
            $allowedTypes = ['audio/mpeg', 'audio/wav', 'audio/mp4', 'audio/x-m4a'];
            $mimeType = $file->getMimeType();
            if (!in_array($mimeType, $allowedTypes)) {
                throw new Exception('不支持的音頻格式，僅支持MP3、WAV、M4A');
            }

            // 驗證文件大小 (最大50MB)
            if ($file->getSize() > 50 * 1024 * 1024) {
                throw new Exception('文件太大，最大支持50MB');
            }

            // 生成安全的文件名
            $extension = $this->getFileExtension($file->getClientOriginalName());
            $filename = 'vidspark_audio_' . date('Ymd_His') . '_' . uniqid() . '.' . $extension;
            $relativePath = 'vidspark/storage/audio/' . date('Y/m') . '/' . $filename;
            $fullPath = base_path() . '/public/' . $relativePath;

            // 確保目錄存在
            $directory = dirname($fullPath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            // 保存文件
            if (!$file->move($fullPath)) {
                throw new Exception('文件保存失敗');
            }

            // 生成可訪問的URL
            $fileUrl = 'https://genhuman-digital-human.zeabur.app/' . $relativePath;

            // 保存到數據庫
            $fileRecord = [
                'file_type' => 'audio',
                'original_name' => $file->getClientOriginalName(),
                'file_name' => $filename,
                'file_path' => $relativePath,
                'file_url' => $fileUrl,
                'file_size' => $file->getSize(),
                'mime_type' => $mimeType,
                'upload_time' => date('Y-m-d H:i:s'),
                'user_ip' => $request->getRealIp()
            ];

            $fileId = Db::table('vidspark_production_files')->insertGetId($fileRecord);

            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => true,
                'message' => '音頻文件上傳成功',
                'data' => [
                    'file_id' => $fileId,
                    'file_url' => $fileUrl,
                    'original_name' => $file->getClientOriginalName(),
                    'file_size' => $this->formatFileSize($file->getSize()),
                    'upload_time' => date('Y-m-d H:i:s')
                ]
            ], JSON_UNESCAPED_UNICODE));

        } catch (Exception $e) {
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
     * 上傳視頻文件
     */
    public function uploadVideo(Request $request): Response
    {
        try {
            $file = $request->file('video');
            if (!$file || !$file->isValid()) {
                throw new Exception('沒有上傳文件或文件無效');
            }

            // 驗證文件類型
            $allowedTypes = ['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/webm'];
            $mimeType = $file->getMimeType();
            if (!in_array($mimeType, $allowedTypes)) {
                throw new Exception('不支持的視頻格式，僅支持MP4、MOV、AVI、WebM');
            }

            // 驗證文件大小 (最大200MB)
            if ($file->getSize() > 200 * 1024 * 1024) {
                throw new Exception('文件太大，最大支持200MB');
            }

            // 生成安全的文件名
            $extension = $this->getFileExtension($file->getClientOriginalName());
            $filename = 'vidspark_video_' . date('Ymd_His') . '_' . uniqid() . '.' . $extension;
            $relativePath = 'vidspark/storage/video/' . date('Y/m') . '/' . $filename;
            $fullPath = base_path() . '/public/' . $relativePath;

            // 確保目錄存在
            $directory = dirname($fullPath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            // 保存文件
            if (!$file->move($fullPath)) {
                throw new Exception('文件保存失敗');
            }

            // 生成可訪問的URL
            $fileUrl = 'https://genhuman-digital-human.zeabur.app/' . $relativePath;

            // 保存到數據庫
            $fileRecord = [
                'file_type' => 'video',
                'original_name' => $file->getClientOriginalName(),
                'file_name' => $filename,
                'file_path' => $relativePath,
                'file_url' => $fileUrl,
                'file_size' => $file->getSize(),
                'mime_type' => $mimeType,
                'upload_time' => date('Y-m-d H:i:s'),
                'user_ip' => $request->getRealIp()
            ];

            $fileId = Db::table('vidspark_production_files')->insertGetId($fileRecord);

            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => true,
                'message' => '視頻文件上傳成功',
                'data' => [
                    'file_id' => $fileId,
                    'file_url' => $fileUrl,
                    'original_name' => $file->getClientOriginalName(),
                    'file_size' => $this->formatFileSize($file->getSize()),
                    'upload_time' => date('Y-m-d H:i:s')
                ]
            ], JSON_UNESCAPED_UNICODE));

        } catch (Exception $e) {
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
     * 保存生成的數字人視頻
     */
    public function saveGeneratedVideo(Request $request): Response
    {
        try {
            $input = json_decode($request->rawBody(), true);
            $videoTaskId = $input['video_task_id'] ?? '';
            $videoUrl = $input['video_url'] ?? '';
            $billId = $input['bill_id'] ?? '';
            $token = $input['token'] ?? '';
            
            if (empty($videoTaskId) || empty($videoUrl)) {
                throw new Exception('視頻任務ID和視頻URL不能為空');
            }

            // 保存生成的視頻記錄
            $videoRecord = [
                'file_type' => 'generated_video',
                'original_name' => 'generated_avatar_' . $videoTaskId . '.mp4',
                'file_name' => 'generated_avatar_' . $videoTaskId . '.mp4',
                'file_path' => 'external/' . $videoTaskId,
                'file_url' => $videoUrl,
                'file_size' => 0, // 外部文件暫時設為0
                'mime_type' => 'video/mp4',
                'upload_time' => date('Y-m-d H:i:s'),
                'user_ip' => $request->getRealIp(),
                'extra_data' => json_encode([
                    'video_task_id' => $videoTaskId,
                    'bill_id' => $billId,
                    'token_mask' => $this->maskToken($token),
                    'generation_time' => date('Y-m-d H:i:s')
                ])
            ];

            $fileId = Db::table('vidspark_production_files')->insertGetId($videoRecord);

            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => true,
                'message' => '生成的數字人視頻已保存',
                'data' => [
                    'file_id' => $fileId,
                    'video_task_id' => $videoTaskId,
                    'video_url' => $videoUrl,
                    'save_time' => date('Y-m-d H:i:s')
                ]
            ], JSON_UNESCAPED_UNICODE));

        } catch (Exception $e) {
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'save_time' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * 獲取文件擴展名
     */
    private function getFileExtension($filename)
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    /**
     * 格式化文件大小
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1024 * 1024 * 1024) {
            return round($bytes / (1024 * 1024 * 1024), 2) . ' GB';
        } elseif ($bytes >= 1024 * 1024) {
            return round($bytes / (1024 * 1024), 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }

    /**
     * 掩碼Token顯示
     */
    private function maskToken($token)
    {
        if (!$token || strlen($token) < 10) {
            return 'invalid';
        }
        return substr($token, 0, 8) . '...' . substr($token, -4);
    }

    /**
     * 獲取文件列表
     */
    public function getFileList(Request $request): Response
    {
        try {
            $fileType = $request->get('type', 'all'); // all, audio, video, generated_video
            $limit = (int)$request->get('limit', 20);
            $offset = (int)$request->get('offset', 0);

            $query = Db::table('vidspark_production_files')
                ->orderBy('upload_time', 'desc')
                ->limit($limit)
                ->offset($offset);

            if ($fileType !== 'all') {
                $query->where('file_type', $fileType);
            }

            $files = $query->get();
            $total = Db::table('vidspark_production_files')
                ->when($fileType !== 'all', function ($q) use ($fileType) {
                    return $q->where('file_type', $fileType);
                })
                ->count();

            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => true,
                'data' => [
                    'files' => $files,
                    'total' => $total,
                    'limit' => $limit,
                    'offset' => $offset
                ],
                'query_time' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE));

        } catch (Exception $e) {
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'query_time' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE));
        }
    }
}
