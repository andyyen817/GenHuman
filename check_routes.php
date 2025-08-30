<?php
/**
 * 路由重複檢查腳本
 * 用於檢測route.php中的重複路由
 */

// 讀取路由文件
$routeFile = __DIR__ . '/server/config/route.php';
$content = file_get_contents($routeFile);

// 提取所有路由定義
preg_match_all('/Route::(get|post|put|delete)\s*\(\s*[\'"]([^\'"]+)[\'"]/', $content, $matches, PREG_SET_ORDER);

$routes = [];
$duplicates = [];

foreach ($matches as $match) {
    $method = $match[1];
    $path = $match[2];
    $key = $method . ':' . $path;
    
    if (isset($routes[$key])) {
        $duplicates[] = $key;
    } else {
        $routes[$key] = true;
    }
}

echo "路由重複檢查結果:\n";
echo "總路由數量: " . count($routes) . "\n";

if (empty($duplicates)) {
    echo "✅ 沒有發現重複路由\n";
} else {
    echo "❌ 發現重複路由:\n";
    foreach ($duplicates as $duplicate) {
        echo "  - $duplicate\n";
    }
}

// 列出所有Vidspark相關路由
echo "\nVidspark相關路由:\n";
foreach ($routes as $route => $value) {
    if (strpos($route, 'vidspark') !== false) {
        echo "  ✓ $route\n";
    }
}
?>
