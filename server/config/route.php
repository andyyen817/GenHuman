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

// 靜態文件處理（如果需要）
Route::fallback(function(){
    return response('API endpoint not found', 404);
});
?>