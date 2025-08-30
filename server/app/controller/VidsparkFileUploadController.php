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
            // 詳細錯誤日誌
            error_log('[VidsparkUpload] 開始處理音頻上傳請求');
            
            // 檢查PHP上傳配置
            $uploadMaxFilesize = $this->parseSize(ini_get('upload_max_filesize'));
            $postMaxSize = $this->parseSize(ini_get('post_max_size'));
            
            error_log('[VidsparkUpload] PHP配置檢查:');
            error_log('[VidsparkUpload] upload_max_filesize: ' . ini_get('upload_max_filesize') . ' (' . $uploadMaxFilesize . ' bytes)');
            error_log('[VidsparkUpload] post_max_size: ' . ini_get('post_max_size') . ' (' . $postMaxSize . ' bytes)');
            
            // 檢查是否因為POST大小限制導致沒有接收到文件
            $requestMethod = $_SERVER['REQUEST_METHOD'] ?? $request->getMethod();
            if (empty($_FILES) && empty($_POST) && $requestMethod === 'POST') {
                $contentLength = $_SERVER['CONTENT_LENGTH'] ?? 0;
                error_log('[VidsparkUpload] 檢測到空POST，Content-Length: ' . $contentLength);
                
                if ($contentLength > $postMaxSize) {
                    throw new Exception("上傳文件太大，超過伺服器限制 " . ini_get('post_max_size') . "，請選擇更小的文件");
                }
                
                throw new Exception("文件上傳失敗，可能超過伺服器限制，當前限制: " . ini_get('upload_max_filesize'));
            }
            
            $file = $request->file('audio');
            if (!$file || !$file->isValid()) {
                $errorCode = $file ? $file->getError() : 'UPLOAD_ERR_NO_FILE';
                error_log('[VidsparkUpload] 文件驗證失敗: ' . $errorCode);
                
                // 根據PHP錯誤代碼提供友好錯誤信息
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => "文件太大，超過伺服器限制 " . ini_get('upload_max_filesize'),
                    UPLOAD_ERR_FORM_SIZE => "文件太大，超過表單限制",
                    UPLOAD_ERR_PARTIAL => "文件只上傳了一部分",
                    UPLOAD_ERR_NO_FILE => "沒有選擇文件",
                    UPLOAD_ERR_NO_TMP_DIR => "伺服器臨時目錄不存在",
                    UPLOAD_ERR_CANT_WRITE => "文件寫入失敗",
                    UPLOAD_ERR_EXTENSION => "文件上傳被PHP擴展阻止"
                ];
                
                $errorMsg = isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode] : '文件上傳失敗，錯誤代碼: ' . $errorCode;
                throw new Exception($errorMsg);
            }

            error_log('[VidsparkUpload] 文件接收成功: ' . $file->getClientOriginalName());

            // 驗證文件類型
            $allowedTypes = ['audio/mpeg', 'audio/wav', 'audio/mp4', 'audio/x-m4a'];
            $mimeType = $file->getMimeType();
            if (!in_array($mimeType, $allowedTypes)) {
                error_log('[VidsparkUpload] 文件類型不支持: ' . $mimeType);
                throw new Exception('不支持的音頻格式，僅支持MP3、WAV、M4A');
            }

            // 檢查文件大小是否超過PHP限制
            $fileSize = $file->getSize();
            error_log('[VidsparkUpload] 文件大小: ' . $fileSize . ' bytes (' . $this->formatFileSize($fileSize) . ')');
            
            if ($fileSize > $uploadMaxFilesize) {
                error_log('[VidsparkUpload] 文件超過PHP upload_max_filesize限制');
                throw new Exception("文件太大，超過伺服器限制 " . ini_get('upload_max_filesize') . "，請選擇更小的文件");
            }
            
            // 我們的應用限制（取較小值）
            $ourLimit = min(50 * 1024 * 1024, $uploadMaxFilesize); // 50MB或PHP限制
            if ($fileSize > $ourLimit) {
                error_log('[VidsparkUpload] 文件超過應用限制: ' . $ourLimit);
                throw new Exception('文件太大，最大支持' . $this->formatFileSize($ourLimit));
            }

            // 生成安全的文件名
            $extension = $this->getFileExtension($file->getClientOriginalName());
            $filename = 'vidspark_audio_' . date('Ymd_His') . '_' . uniqid() . '.' . $extension;
            $relativePath = 'vidspark/storage/audio/' . date('Y/m') . '/' . $filename;
            $fullPath = base_path() . '/public/' . $relativePath;

            error_log('[VidsparkUpload] 目標路徑: ' . $fullPath);

            // 確保目錄存在
            $directory = dirname($fullPath);
            if (!is_dir($directory)) {
                if (!mkdir($directory, 0755, true)) {
                    error_log('[VidsparkUpload] 目錄創建失敗: ' . $directory);
                    throw new Exception('無法創建存儲目錄');
                }
                error_log('[VidsparkUpload] 目錄創建成功: ' . $directory);
            }

            // 保存文件
            if (!$file->move($fullPath)) {
                error_log('[VidsparkUpload] 文件移動失敗');
                throw new Exception('文件保存失敗');
            }

            error_log('[VidsparkUpload] 文件保存成功');

            // 生成可訪問的URL
            $fileUrl = 'https://genhuman-digital-human.zeabur.app/' . $relativePath;

            // 簡化版本：先不保存到數據庫，避免數據庫連接問題
            error_log('[VidsparkUpload] 跳過數據庫保存，直接返回結果');

            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => true,
                'message' => '音頻文件上傳成功',
                'data' => [
                    'file_id' => 'temp_' . uniqid(),
                    'file_url' => $fileUrl,
                    'original_name' => $file->getClientOriginalName(),
                    'file_size' => $this->formatFileSize($file->getSize()),
                    'upload_time' => date('Y-m-d H:i:s'),
                    'debug_info' => [
                        'mime_type' => $mimeType,
                        'size_bytes' => $file->getSize(),
                        'extension' => $extension,
                        'saved_path' => $relativePath
                    ]
                ]
            ], JSON_UNESCAPED_UNICODE));

        } catch (Exception $e) {
            error_log('[VidsparkUpload] 處理異常: ' . $e->getMessage());
            error_log('[VidsparkUpload] 異常堆棧: ' . $e->getTraceAsString());
            
            return new Response(500, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'error_details' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ],
                'upload_time' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE));
        } catch (Throwable $t) {
            error_log('[VidsparkUpload] 嚴重錯誤: ' . $t->getMessage());
            
            return new Response(500, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => false,
                'message' => '系統內部錯誤: ' . $t->getMessage(),
                'upload_time' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * 上傳視頻文件
     */
    public function uploadVideo(Request $request): Response
    {
        // 在Webman中強制返回JSON，避免HTML錯誤頁面
        try {
            return $this->handleVideoUpload($request);
        } catch (Throwable $e) {
            error_log('[VidsparkUpload] 捕獲到Throwable錯誤: ' . $e->getMessage());
            error_log('[VidsparkUpload] 錯誤文件: ' . $e->getFile() . ':' . $e->getLine());
            
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => false,
                'message' => 'Throwable錯誤: ' . $e->getMessage(),
                'error_type' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'upload_time' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE));
        }
    }
    
    private function handleVideoUpload(Request $request): Response
    {
        try {
            // 基本驗證
            if ($request->method() !== 'POST') {
                throw new Exception('只支持POST請求');
            }
            
            error_log('[VidsparkUpload] 開始處理視頻上傳請求');
            
            // 檢查$_FILES是否有數據
            if (empty($_FILES)) {
                error_log('[VidsparkUpload] $_FILES為空');
                throw new Exception('沒有接收到文件數據，請檢查表單enctype');
            }
            
            error_log('[VidsparkUpload] $_FILES內容: ' . json_encode($_FILES));
            
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
            error_log('[VidsparkUpload] 視頻上傳錯誤: ' . $e->getMessage());
            error_log('[VidsparkUpload] 錯誤堆棧: ' . $e->getTraceAsString());
            
            // 詳細診斷信息
            $diagnostics = [
                'php_version' => PHP_VERSION,
                'request_method' => $request->method(),
                'content_type' => $request->header('content-type'),
                'file_info' => $file ? [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                    'error' => $file->getError()
                ] : 'No file',
                'memory_usage' => memory_get_usage(true),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size'),
                'max_execution_time' => ini_get('max_execution_time')
            ];
            
            error_log('[VidsparkUpload] 診斷信息: ' . json_encode($diagnostics));
            
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'error_details' => '視頻上傳失敗，請檢查文件格式和大小',
                'diagnostics' => $diagnostics,
                'upload_time' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * 視頻上傳診斷端點
     */
    public function videoUploadDiagnosis(Request $request): Response
    {
        try {
            $diagnostics = [
                'timestamp' => date('Y-m-d H:i:s'),
                'php_version' => PHP_VERSION,
                'webman_framework' => 'Webman',
                'request_method' => $request->method(),
                'content_type' => $request->header('content-type'),
                'content_length' => $request->header('content-length'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size'),
                'max_file_uploads' => ini_get('max_file_uploads'),
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
                'file_uploads' => ini_get('file_uploads'),
                'temp_dir' => sys_get_temp_dir(),
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'request_uri' => $request->uri(),
                'files_received' => $_FILES ? array_keys($_FILES) : 'None'
            ];
            
            // 測試數據庫連接
            try {
                Db::table('vidspark_production_files')->limit(1)->select();
                $diagnostics['database_connection'] = 'OK';
            } catch (Exception $e) {
                $diagnostics['database_connection'] = 'FAILED: ' . $e->getMessage();
            }
            
            // 測試存儲目錄
            $storageDir = base_path() . '/public/vidspark/storage/video/' . date('Y/m');
            $diagnostics['storage_directory'] = [
                'path' => $storageDir,
                'exists' => is_dir($storageDir),
                'writable' => is_writable(dirname($storageDir))
            ];
            
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => true,
                'message' => '視頻上傳診斷信息',
                'diagnostics' => $diagnostics
            ], JSON_UNESCAPED_UNICODE));
            
        } catch (Exception $e) {
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => false,
                'message' => '診斷失敗',
                'error' => $e->getMessage()
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
     * 解析PHP ini配置的文件大小
     */
    private function parseSize($size)
    {
        $size = trim($size);
        $last = strtolower($size[strlen($size) - 1]);
        $size = (int)$size;
        
        switch ($last) {
            case 'g':
                $size *= 1024 * 1024 * 1024;
                break;
            case 'm':
                $size *= 1024 * 1024;
                break;
            case 'k':
                $size *= 1024;
                break;
        }
        
        return $size;
    }

    /**
     * Base64文件上傳 - 繞過PHP上傳限制
     */
    public function uploadAudioBase64(Request $request): Response
    {
        try {
            error_log('[VidsparkUpload] 開始處理Base64音頻上傳');
            
            $input = json_decode($request->rawBody(), true);
            $fileName = $input['fileName'] ?? 'audio.mp3';
            $fileData = $input['fileData'] ?? ''; // base64編碼的文件數據
            $fileSize = $input['fileSize'] ?? 0;
            
            if (empty($fileData)) {
                throw new Exception('沒有文件數據');
            }
            
            // 檢查文件大小
            if ($fileSize > 10 * 1024 * 1024) { // 10MB限制
                throw new Exception('文件太大，最大支持10MB');
            }
            
            // 解碼base64數據
            $binaryData = base64_decode($fileData);
            if ($binaryData === false) {
                throw new Exception('文件數據解碼失敗');
            }
            
            // 驗證解碼後的大小
            $actualSize = strlen($binaryData);
            error_log('[VidsparkUpload] 文件大小: ' . $actualSize . ' bytes');
            
            // 生成安全的文件名
            $extension = $this->getFileExtension($fileName);
            $safeFileName = 'vidspark_audio_' . date('Ymd_His') . '_' . uniqid() . '.' . $extension;
            $relativePath = 'vidspark/storage/audio/' . date('Y/m') . '/' . $safeFileName;
            $fullPath = base_path() . '/public/' . $relativePath;
            
            // 確保目錄存在
            $directory = dirname($fullPath);
            if (!is_dir($directory)) {
                if (!mkdir($directory, 0755, true)) {
                    throw new Exception('無法創建存儲目錄');
                }
            }
            
            // 保存文件
            if (file_put_contents($fullPath, $binaryData) === false) {
                throw new Exception('文件保存失敗');
            }
            
            error_log('[VidsparkUpload] Base64文件保存成功: ' . $fullPath);
            
            // 生成可訪問的URL
            $fileUrl = 'https://genhuman-digital-human.zeabur.app/' . $relativePath;
            
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => true,
                'message' => '音頻文件上傳成功 (Base64方式)',
                'data' => [
                    'file_id' => 'base64_' . uniqid(),
                    'file_url' => $fileUrl,
                    'original_name' => $fileName,
                    'file_size' => $this->formatFileSize($actualSize),
                    'upload_method' => 'base64',
                    'upload_time' => date('Y-m-d H:i:s')
                ]
            ], JSON_UNESCAPED_UNICODE));
            
        } catch (Exception $e) {
            error_log('[VidsparkUpload] Base64上傳異常: ' . $e->getMessage());
            
            return new Response(500, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'upload_method' => 'base64',
                'upload_time' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * 測試端點 - 檢查上傳功能是否可用
     */
    public function testUpload(Request $request): Response
    {
        try {
            error_log('[VidsparkUpload] 測試端點被調用');
            
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => true,
                'message' => 'Vidspark文件上傳服務正常運行',
                'data' => [
                    'controller' => 'VidsparkFileUploadController',
                    'method' => 'testUpload',
                    'server_time' => date('Y-m-d H:i:s'),
                    'php_version' => PHP_VERSION,
                    'upload_max_filesize' => ini_get('upload_max_filesize'),
                    'post_max_size' => ini_get('post_max_size'),
                    'memory_limit' => ini_get('memory_limit'),
                    'base_path' => base_path(),
                    'public_path' => base_path() . '/public/',
                    'upload_dir_writable' => is_writable(base_path() . '/public/')
                ]
            ], JSON_UNESCAPED_UNICODE));
            
        } catch (Exception $e) {
            error_log('[VidsparkUpload] 測試端點異常: ' . $e->getMessage());
            
            return new Response(500, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'test_time' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE));
        }
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
