<?php
/**
 * GenHuman API路由配置
 * 支持前端和管理後台API
 */

use Webman\Route;

// 前端API路由
Route::group('/api/v1', function () {
    // 用戶相關API
    Route::group('/user', function () {
        Route::post('/login', [app\api\controller\UserController::class, 'login']);
        Route::post('/register', [app\api\controller\UserController::class, 'login']); // 註冊和登入使用同一邏輯
        Route::get('/info', [app\api\controller\UserController::class, 'info']);
        Route::get('/test', [app\api\controller\UserController::class, 'test']);
    });

    // 應用相關API
    Route::group('/app', function () {
        Route::get('/list', [app\api\controller\AppController::class, 'list']);
    });

    // 場景相關API
    Route::group('/scene', function () {
        Route::get('/list', [app\api\controller\AppController::class, 'list']); // 暫時使用應用列表
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
    Route::get('/clean', [app\controller\DatabaseController::class, 'clean']);
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

// Vidspark數據庫初始化腳本路由
Route::get('/vidspark-database-init.php', function () {
    $filePath = base_path() . '/public/vidspark-database-init.php';
    if (file_exists($filePath)) {
        // 執行PHP腳本並返回結果
        ob_start();
        include $filePath;
        $output = ob_get_clean();
        
        return response($output, 200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Cache-Control' => 'no-cache, no-store, must-revalidate'
        ]);
    }
    return response('Vidspark database init script not found', 404);
});

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

// 靜態文件處理（如果需要）
Route::fallback(function(){
    return response('API endpoint not found', 404);
});
?>