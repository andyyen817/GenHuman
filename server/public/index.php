<?php
/**
 * 簡化框架 Web 入口文件
 * 處理所有 HTTP 請求並路由到相應的控制器
 */

// 設置工作目錄為項目根目錄
chdir(dirname(__DIR__));

// 載入我們的簡化框架
require_once __DIR__ . '/../support/App.php';
require_once __DIR__ . '/../support/Request.php';
require_once __DIR__ . '/../support/Response.php';
require_once __DIR__ . '/../app/controller/VidsparkSimpleUploadController.php';

// 啟動應用
support\App::run();