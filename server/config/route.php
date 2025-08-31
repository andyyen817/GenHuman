<?php
/**
 * GenHuman API路由配置
 * 支持前端和管理後台API
 */

use Webman\Route;
use support\Response;

// 前端API路由
Route::group('/api/v1', function () {
    // 用戶相關API
    Route::group('/user', function () {
        Route::post('/login', [app\api\controller\UserController::class, 'login']);
        Route::post('/register', [app\api\controller\UserController::class, 'login']); // 註冊和登入使用同一邏輯
        Route::get('/statistics', [app\api\controller\UserController::class, 'getUserStatistics']);
        Route::post('/edit', [app\api\controller\UserController::class, 'editUser']);
    });

    // 應用相關API
    Route::group('/app', function () {
        Route::get('/list', [app\api\controller\AppController::class, 'getList']);
        Route::get('/detail', [app\api\controller\AppController::class, 'getDetail']);
    });

    // 場景相關API
    Route::group('/scene', function () {
        Route::get('/list', [app\api\controller\AppController::class, 'getList']); // 暫時使用應用列表
    });
});

// 前端診斷API
Route::group('/frontend', function () {
    Route::get('/checkapi', [app\controller\FrontendDebugController::class, 'checkapi']);
    Route::get('/createapi', [app\controller\FrontendDebugController::class, 'createapi']);
    Route::get('/testapi', [app\controller\FrontendDebugController::class, 'testapi']);
});

// 快速修復工具路由
Route::group('/quickfix', function () {
    Route::get('/adapter', [app\controller\QuickFixController::class, 'adapter']);
    Route::get('/config', [app\controller\QuickFixController::class, 'config']);
    Route::get('/all', [app\controller\QuickFixController::class, 'all']);
    Route::get('/checkuser', [app\controller\QuickFixController::class, 'checkuser']);
    Route::get('/createuser', [app\controller\QuickFixController::class, 'createuser']);
});

// 調試工具路由
Route::group('/debug', function () {
    Route::get('/login', [app\controller\DebugController::class, 'login']);
});

// 數據庫工具路由
Route::group('/database', function () {
    Route::get('/init', [app\controller\DatabaseController::class, 'init']);
    Route::get('/fix', [app\controller\DatabaseController::class, 'fix']);
});

// 路由測試工具路由
Route::group('/routetest', function () {
    Route::get('/check', [app\controller\RouteTestController::class, 'check']);
    Route::get('/fixroute', [app\controller\RouteTestController::class, 'fixroute']);
    Route::get('/apitest', [app\controller\RouteTestController::class, 'apitest']);
    Route::any('/userlogin', [app\controller\RouteTestController::class, 'userlogin']);
    Route::get('/applist', [app\controller\RouteTestController::class, 'applist']);
});

// 移動端登入路由 - 解決微信登入問題
Route::group('/mobile', function () {
    Route::get('/login', [app\controller\MobileLoginController::class, 'login']);
    Route::get('/register', [app\controller\MobileLoginController::class, 'register']);
});

// 新的數位人應用 - 完全繞過原有H5
Route::get('/app', [app\controller\AppController::class, 'main']);

// GenHuman v3.0 用戶API控制台 - 全新獨立架構
Route::get('/user-api/dashboard', [app\controller\UserApiController::class, 'dashboard']);

// Admin管理後台主頁面路由
Route::get('/admin', function () {
    $filePath = base_path() . '/public/admin/index.html';
    if (file_exists($filePath)) {
        return response()->file($filePath, 200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Cache-Control' => 'no-cache, no-store, must-revalidate'
        ]);
    }
    return response('Admin page not found', 404);
});

// API配置管理路由
Route::group('/api-config', function () {
    Route::get('/', [app\controller\ApiConfigController::class, 'index']);
    Route::post('/save', [app\controller\ApiConfigController::class, 'saveConfig']);
    Route::post('/test-connection', [app\controller\ApiConfigController::class, 'testConnection']);
    Route::post('/test-api/{type}', [app\controller\ApiConfigController::class, 'testApi']);
});

// 主頁面路由 - 智能登入檢測
Route::get('/', [app\controller\IndexController::class, 'index']);

// 通用靜態資源路由（修復logo.svg 404問題）
Route::get('/static/{path:.+}', function ($request, $path) {
    // 嘗試多個可能的靜態文件位置
    $possiblePaths = [
        base_path() . '/public/static/' . $path,
        base_path() . '/public/admin/static/' . $path,
        base_path() . '/public/' . $path
    ];
    
    foreach ($possiblePaths as $filePath) {
        if (file_exists($filePath)) {
            $ext = pathinfo($filePath, PATHINFO_EXTENSION);
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
                'otf' => 'font/otf',
                'mp3' => 'audio/mpeg'
            ];
            $contentType = $contentTypes[$ext] ?? 'application/octet-stream';
            
            return response()->file($filePath, 200, [
                'Content-Type' => $contentType,
                'Cache-Control' => 'public, max-age=31536000'
            ]);
        }
    }
    return response('Static file not found: ' . $path, 404);
});

// Admin管理後台靜態資源路由
Route::get('/admin/static/{path:.+}', function ($request, $path) {
    $filePath = base_path() . '/public/admin/static/' . $path;
    if (file_exists($filePath)) {
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
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
            'otf' => 'font/otf',
            'mp3' => 'audio/mpeg'
        ];
        $contentType = $contentTypes[$ext] ?? 'application/octet-stream';
        
        return response()->file($filePath, 200, [
            'Content-Type' => $contentType,
            'Cache-Control' => 'public, max-age=31536000' // 1年緩存
        ]);
    }
    return response('Static file not found: ' . $path, 404);
});

// H5靜態資源路由 - 使用專用控制器
Route::get('/h5/assets/{path:.+}', [app\controller\StaticController::class, 'assets']);
Route::get('/h5/static/{path:.+}', [app\controller\StaticController::class, 'static']);
Route::get('/h5/login-fix.js', function () {
    $filePath = base_path() . '/public/h5/login-fix.js';
    if (file_exists($filePath)) {
        return response()->file($filePath, 200, [
            'Content-Type' => 'application/javascript; charset=utf-8',
            'Cache-Control' => 'no-cache, no-store, must-revalidate'
        ]);
    }
    return response('Login fix script not found', 404);
});
Route::get('/h5/{filename}', [app\controller\StaticController::class, 'file']);

// H5應用主入口
Route::get('/h5', [app\controller\StaticController::class, 'index']);

// Vidspark語言切換測試頁面路由
Route::get('/vidspark-i18n-test.html', function () {
    $filePath = base_path() . '/public/vidspark-i18n-test.html';
    if (file_exists($filePath)) {
        return response()->file($filePath, 200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Cache-Control' => 'no-cache, no-store, must-revalidate'
        ]);
    }
    return response('Vidspark test page not found', 404);
});

// Vidspark數據庫初始化腳本路由（使用專用控制器）
Route::get('/vidspark-database-init.php', [app\controller\VidsparkController::class, 'databaseInit']);

// Vidspark Token測試頁面（簡單安全版本）
Route::get('/vidspark-token-test', function () {
    $filePath = base_path() . '/public/vidspark-token-test.html';
    if (file_exists($filePath)) {
        return response(file_get_contents($filePath), 200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Cache-Control' => 'no-cache, no-store, must-revalidate'
        ]);
    }
    return response('Token test page not found', 404);
});

// Vidspark完整數字人生成流程頁面
Route::get('/vidspark-digital-human-complete', function () {
    $filePath = base_path() . '/public/vidspark-digital-human-complete.html';
    if (file_exists($filePath)) {
        return response(file_get_contents($filePath), 200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Cache-Control' => 'no-cache, no-store, must-revalidate'
        ]);
    }
    return response('Complete workflow page not found', 404);
});

// 修復：支持用戶常見的拼寫錯誤
Route::get('/vidspark-digital-human-completet', function () {
    return response('', 301, ['Location' => '/vidspark-digital-human-complete']);
});

// Vidspark PHP配置問題診斷頁面
Route::get('/vidspark-php-config-info', function () {
    $filePath = base_path() . '/public/vidspark-php-config-info.html';
    if (file_exists($filePath)) {
        return response(file_get_contents($filePath), 200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Cache-Control' => 'no-cache, no-store, must-revalidate'
        ]);
    }
    return response('PHP config info page not found', 404);
});

// Zeabur配置解決方案指南
Route::get('/zeabur-config-guide', function () {
    $filePath = base_path() . '/public/zeabur-config-guide.html';
    if (file_exists($filePath)) {
        return response(file_get_contents($filePath), 200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Cache-Control' => 'no-cache, no-store, must-revalidate'
        ]);
    }
    return response('Zeabur config guide not found', 404);
});

// Vidspark API代理路由（解決CORS問題）
Route::post('/vidspark-api-proxy/validate-token', [app\controller\VidsparkApiProxyController::class, 'validateToken']);
Route::post('/vidspark-api-proxy/test-free-avatar', [app\controller\VidsparkApiProxyController::class, 'testFreeAvatar']);

// Vidspark完整數字人生成流程API代理
Route::post('/vidspark-api-proxy/clone-voice', [app\controller\VidsparkApiProxyController::class, 'cloneVoice']);
Route::post('/vidspark-api-proxy/clone-voice-deep', [app\controller\VidsparkApiProxyController::class, 'cloneVoiceDeep']);
Route::post('/vidspark-api-proxy/synthesize-voice', [app\controller\VidsparkApiProxyController::class, 'synthesizeWithClonedVoice']);
Route::post('/vidspark-api-proxy/synthesize-voice-deep', [app\controller\VidsparkApiProxyController::class, 'synthesizeVoiceDeep']);
Route::post('/vidspark-api-proxy/create-scene', [app\controller\VidsparkApiProxyController::class, 'createScene']);
Route::post('/vidspark-api-proxy/create-scene-free', [app\controller\VidsparkApiProxyController::class, 'createSceneFree']);
Route::post('/vidspark-api-proxy/create-scene-paid', [app\controller\VidsparkApiProxyController::class, 'createScenePaid']);
Route::post('/vidspark-api-proxy/synthesize-avatar', [app\controller\VidsparkApiProxyController::class, 'synthesizeAvatar']);
Route::post('/vidspark-api-proxy/synthesize-avatar-free', [app\controller\VidsparkApiProxyController::class, 'synthesizeAvatarFree']);
Route::post('/vidspark-api-proxy/synthesize-avatar-paid', [app\controller\VidsparkApiProxyController::class, 'synthesizeAvatarPaid']);
Route::post('/vidspark-api-proxy/query-task', [app\controller\VidsparkApiProxyController::class, 'queryTask']);
Route::post('/vidspark-api-proxy/get-voice-result', [app\controller\VidsparkApiProxyController::class, 'getVoiceResult']);

Route::get('/vidspark-api-proxy/status', [app\controller\VidsparkApiProxyController::class, 'getProxyStatus']);
Route::post('/vidspark-api-proxy/get-voice-roles', [app\controller\VidsparkApiProxyController::class, 'getVoiceRoles']);

// Vidspark文件上傳路由（生產環境文件處理）
Route::get('/vidspark-upload/test', [app\controller\VidsparkFileUploadController::class, 'testUpload']);
Route::post('/vidspark-upload/audio', [app\controller\VidsparkFileUploadController::class, 'uploadAudio']);
Route::post('/vidspark-upload/audio-base64', [app\controller\VidsparkFileUploadController::class, 'uploadAudioBase64']);
Route::post('/vidspark-upload/video', [app\controller\VidsparkFileUploadController::class, 'uploadVideo']);
Route::post('/vidspark-upload/video-base64', [app\controller\VidsparkFileUploadController::class, 'uploadVideoBase64']);
Route::post('/vidspark-upload/save-generated-video', [app\controller\VidsparkFileUploadController::class, 'saveGeneratedVideo']);
Route::get('/vidspark-upload/files', [app\controller\VidsparkFileUploadController::class, 'getFileList']);
Route::any('/vidspark-upload/video-diagnosis', [app\controller\VidsparkFileUploadController::class, 'videoUploadDiagnosis']);

// Vidspark API連接測試路由
Route::post('/vidspark-api-proxy/test-connection', [app\controller\VidsparkApiProxyController::class, 'testApiConnection']);

// Zeabur配置檢查工具
Route::get('/zeabur-config-check', function() {
    return new \support\Response(200, [], file_get_contents(public_path() . '/zeabur-config-check.html'));
});

// 網絡連接測試工具
Route::get('/network-connectivity-test', function() {
    return new \support\Response(200, [], file_get_contents(public_path() . '/network-connectivity-test.html'));
});

// PHP配置詳細診斷工具
Route::get('/php-config-detailed-check', function() {
    return new \support\Response(200, [], file_get_contents(public_path() . '/php-config-detailed-check.html'));
});

// PHP配置現實檢查API
Route::get('/vidspark-config/reality-check', [app\controller\VidsparkConfigRealityController::class, 'configRealityCheck']);
Route::get('/vidspark-config/upload-reality', [app\controller\VidsparkConfigRealityController::class, 'uploadRealityCheck']);

// 超級簡單視頻Base64測試工具
Route::get('/simple-video-base64-test', function() {
    return new \support\Response(200, [], file_get_contents(public_path() . '/simple-video-base64-test.html'));
});

// 音頻任務查詢工具
Route::get('/retrieve-audio-by-id', function() {
    return new \support\Response(200, [], file_get_contents(public_path() . '/retrieve-audio-by-id.html'));
});
// Route::post('/vidspark-api-test/voice-clone', [app\controller\VidsparkApiTestController::class, 'testVoiceClone']);
// Route::get('/vidspark-api-test/task-status', [app\controller\VidsparkApiTestController::class, 'testTaskStatus']);
// Route::post('/vidspark-api-test/eight-steps-workflow', [app\controller\VidsparkApiTestController::class, 'testEightStepsWorkflow']);

// Vidspark前端應用路由
Route::get('/vidspark', function () {
    $filePath = base_path() . '/public/vidspark/index.html';
    if (file_exists($filePath)) {
        return response()->file($filePath, 200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Cache-Control' => 'no-cache, no-store, must-revalidate'
        ]);
    }
    return response('Vidspark app not found', 404);
});

// Vidspark前端應用路由（帶斜槓）
Route::get('/vidspark/', function () {
    $filePath = base_path() . '/public/vidspark/index.html';
    if (file_exists($filePath)) {
        return response()->file($filePath, 200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Cache-Control' => 'no-cache, no-store, must-revalidate'
        ]);
    }
    return response('Vidspark app not found', 404);
});

// Vidspark管理後台路由
Route::get('/vidspark-admin', function () {
    $filePath = base_path() . '/public/vidspark-admin/index.html';
    if (file_exists($filePath)) {
        return response()->file($filePath, 200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Cache-Control' => 'no-cache, no-store, must-revalidate'
        ]);
    }
    return response('Vidspark admin not found', 404);
});

// Vidspark管理後台路由（帶斜槓）
Route::get('/vidspark-admin/', function () {
    $filePath = base_path() . '/public/vidspark-admin/index.html';
    if (file_exists($filePath)) {
        return response()->file($filePath, 200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Cache-Control' => 'no-cache, no-store, must-revalidate'
        ]);
    }
    return response('Vidspark admin not found', 404);
});

// Vidspark靜態資源路由
Route::get('/vidspark/assets/{path:.+}', function ($request, $path) {
    $filePath = base_path() . '/public/vidspark/assets/' . $path;
    if (file_exists($filePath)) {
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
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
            'otf' => 'font/otf'
        ];
        $contentType = $contentTypes[$ext] ?? 'application/octet-stream';
        
        return response()->file($filePath, 200, [
            'Content-Type' => $contentType,
            'Cache-Control' => 'public, max-age=31536000'
        ]);
    }
    return response('Vidspark asset not found: ' . $path, 404);
});

// 🔧 CRITICAL: Vidspark存儲文件路由（解決視頻封面提取失敗問題）
// 支持HEAD和GET請求
Route::any('/vidspark/storage/{path:.+}', function ($request, $path) {
    try {
    // 修復路徑匹配邏輯
    // URL格式: /vidspark/storage/video/2025/08/file.mp4
    // 轉換為: /public/vidspark/storage/2025/08/video/file.mp4
    
    if (preg_match('/^(video|audio|images)\/(\d{4}\/\d{2})\/(.+)$/', $path, $matches)) {
        $type = $matches[1];        // video
        $yearMonth = $matches[2];   // 2025/08  
        $filename = $matches[3];    // filename.mp4
        $filePath = base_path() . '/public/vidspark/storage/' . $yearMonth . '/' . $type . '/' . $filename;
    } else {
        // 後備方案：直接拼接
        $filePath = base_path() . '/public/vidspark/storage/' . $path;
    }
    
    // 詳細調試日誌
    error_log("[Storage Route] 請求方法: {$request->method()}");
    error_log("[Storage Route] 請求路徑: {$path}");
    error_log("[Storage Route] 完整文件路徑: {$filePath}");
    error_log("[Storage Route] 文件是否存在: " . (file_exists($filePath) ? '是' : '否'));
    
    if (file_exists($filePath)) {
        // 獲取文件擴展名
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        $contentTypes = [
            'mp4' => 'video/mp4',
            'avi' => 'video/x-msvideo',
            'mov' => 'video/quicktime',
            'wmv' => 'video/x-ms-wmv',
            'flv' => 'video/x-flv',
            'webm' => 'video/webm',
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'ogg' => 'audio/ogg',
            'aac' => 'audio/aac',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp'
        ];
        $contentType = $contentTypes[$ext] ?? 'application/octet-stream';
        $fileSize = filesize($filePath);
        
        $headers = [
            'Content-Type' => $contentType,
            'Content-Length' => $fileSize,
            'Cache-Control' => 'public, max-age=86400',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, HEAD, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
            'Accept-Ranges' => 'bytes'
        ];
        
        // HEAD請求只返回頭信息
        if ($request->method() === 'HEAD') {
            error_log("[Storage Route] HEAD請求，返回頭信息，文件大小: {$fileSize}");
            return response('', 200, $headers);
        }
        
        // GET請求返回文件內容
        error_log("[Storage Route] GET請求，返回文件內容，文件大小: {$fileSize}");
        return response()->file($filePath, 200, $headers);
    }
    return response('Vidspark storage file not found: ' . $path, 404);
    
    } catch (Exception $e) {
        error_log("[Storage Route] 處理文件請求時發生錯誤: " . $e->getMessage());
        error_log("[Storage Route] 錯誤堆棧: " . $e->getTraceAsString());
        return response('Internal Server Error processing file request: ' . $e->getMessage(), 500);
    }
});

// Vidspark管理後台靜態資源路由
Route::get('/vidspark-admin/assets/{path:.+}', function ($request, $path) {
    $filePath = base_path() . '/public/vidspark-admin/assets/' . $path;
    if (file_exists($filePath)) {
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
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
            'otf' => 'font/otf'
        ];
        $contentType = $contentTypes[$ext] ?? 'application/octet-stream';
        
        return response()->file($filePath, 200, [
            'Content-Type' => $contentType,
            'Cache-Control' => 'public, max-age=31536000'
        ]);
    }
    return response('Vidspark admin asset not found: ' . $path, 404);
});

// Vidspark聲音克隆Debug頁面
Route::get('/vidspark-voice-clone-debug', function() {
    return new Response(200, [
        'Content-Type' => 'text/html; charset=utf-8'
    ], file_get_contents(runtime_path() . '/../public/vidspark-voice-clone-debug.html'));
});

// 音頻文件訪問測試頁面
Route::get('/test-audio-access', function() {
    return new Response(200, [
        'Content-Type' => 'text/html; charset=utf-8'
    ], file_get_contents(runtime_path() . '/../public/test-audio-access.html'));
});

// 音頻文件完整診斷工具
Route::get('/vidspark-audio-diagnosis', function() {
    return new Response(200, [
        'Content-Type' => 'text/html; charset=utf-8'
    ], file_get_contents(runtime_path() . '/../public/vidspark-audio-diagnosis.html'));
});

// Vidspark視頻上傳專項調試工具
Route::get('/vidspark-video-upload-debug', function() {
    return new Response(200, [
        'Content-Type' => 'text/html; charset=utf-8'
    ], file_get_contents(runtime_path() . '/../public/vidspark-video-upload-debug.html'));
});

// Vidspark路徑調試工具
Route::get('/vidspark-path-debug', function() {
    return new Response(200, [
        'Content-Type' => 'text/html; charset=utf-8'
    ], file_get_contents(runtime_path() . '/../public/vidspark-path-debug.html'));
});

// Vidspark快速數據庫初始化（使用控制器）
Route::get('/vidspark-quick-db-init', [app\controller\VidsparkStorageController::class, 'quickDbInit']);

// Vidspark存儲系統管理
Route::get('/vidspark-storage-status', [app\controller\VidsparkStorageSystemController::class, 'checkStorageStatus']);
Route::get('/vidspark-storage-init', [app\controller\VidsparkStorageSystemController::class, 'initializeStorage']);

// Vidspark檢查特定聲音狀態
Route::get('/vidspark-check-specific-voice', function() {
    return new Response(200, [
        'Content-Type' => 'text/html; charset=utf-8'
    ], file_get_contents(runtime_path() . '/../public/vidspark-check-specific-voice.html'));
});

// 🔧 存儲路徑診斷工具
Route::get('/debug-storage-path', function() {
    return new Response(200, [
        'Content-Type' => 'text/html; charset=utf-8'
    ], file_get_contents(runtime_path() . '/../public/debug-storage-path.html'));
});

// 🔍 實際文件檢查工具
Route::get('/check-actual-files', function() {
    return new Response(200, [
        'Content-Type' => 'text/html; charset=utf-8'
    ], file_get_contents(runtime_path() . '/../public/check-actual-files.html'));
});

// 🆕 Vidspark簡單上傳系統（歸零重寫）
Route::post('/vidspark-simple-upload/video', [app\controller\VidsparkSimpleUploadController::class, 'uploadVideo']);
Route::get('/vidspark-simple-upload/test', [app\controller\VidsparkSimpleUploadController::class, 'test']);

// 🆕 簡單文件存儲路由（與存儲結構完全匹配）
Route::any('/vidspark/files/{type}/{filename}', function ($request, $type, $filename) {
    try {
        // 直接映射，無複雜邏輯
        $filePath = base_path() . '/public/vidspark/files/' . $type . '/' . $filename;
        
        error_log("[SimpleStorage] 請求: {$type}/{$filename}");
        error_log("[SimpleStorage] 路徑: {$filePath}");
        error_log("[SimpleStorage] 存在: " . (file_exists($filePath) ? '是' : '否'));
        
        // 處理OPTIONS預檢請求
        if ($request->method() === 'OPTIONS') {
            error_log("[SimpleStorage] OPTIONS預檢請求");
            return response('', 200, [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, HEAD, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
                'Access-Control-Max-Age' => '86400'
            ]);
        }
        
        if (file_exists($filePath)) {
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $contentType = $ext === 'mp4' ? 'video/mp4' : 'application/octet-stream';
            
            // 安全獲取文件大小
            $fileSize = @filesize($filePath);
            if ($fileSize === false) {
                error_log("[SimpleStorage] 無法獲取文件大小: {$filePath}");
                $fileSize = 0;
            }
            
            $headers = [
                'Content-Type' => $contentType,
                'Content-Length' => $fileSize,
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, HEAD, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
                'Accept-Ranges' => 'bytes',
                'Cache-Control' => 'public, max-age=86400'
            ];
            
            if ($request->method() === 'HEAD') {
                error_log("[SimpleStorage] HEAD請求，返回頭信息");
                return response('', 200, $headers);
            }
            
            error_log("[SimpleStorage] GET請求，返回文件內容");
            return response()->file($filePath, 200, $headers);
        }
        
        return response('File not found: ' . $type . '/' . $filename, 404);
        
    } catch (Exception $e) {
        error_log("[SimpleStorage] 錯誤: " . $e->getMessage());
        return response('Storage error: ' . $e->getMessage(), 500);
    }
});

// 舊的存儲管理路由（已被新系統替代）
// Route::get('/vidspark-storage-status-old', [app\controller\VidsparkStorageController::class, 'status']);

// 靜態文件處理（如果需要）
Route::fallback(function(){
    return response('API endpoint not found', 404);
});
?>