<?php
/**
 * 简单的路由测试
 */

echo "<h2>路由配置文件检查</h2>";

// 读取路由文件内容
$routeFile = __DIR__ . '/../config/route.php';
$content = file_get_contents($routeFile);

echo "<h3>检查 upload-base64 路由配置:</h3>";

if (strpos($content, 'upload-base64') !== false) {
    echo "<p style='color: green;'>✓ 在路由文件中找到 upload-base64 配置</p>";
    
    // 提取相关行
    $lines = explode("\n", $content);
    foreach ($lines as $lineNum => $line) {
        if (strpos($line, 'upload-base64') !== false) {
            echo "<p>第 " . ($lineNum + 1) . " 行: <code>" . htmlspecialchars($line) . "</code></p>";
        }
    }
} else {
    echo "<p style='color: red;'>✗ 在路由文件中未找到 upload-base64 配置</p>";
}

echo "<h3>检查控制器文件:</h3>";
$controllerFile = __DIR__ . '/../app/controller/VidsparkSimpleUploadController.php';
if (file_exists($controllerFile)) {
    $controllerContent = file_get_contents($controllerFile);
    if (strpos($controllerContent, 'uploadBase64') !== false) {
        echo "<p style='color: green;'>✓ 控制器中存在 uploadBase64 方法</p>";
    } else {
        echo "<p style='color: red;'>✗ 控制器中不存在 uploadBase64 方法</p>";
    }
} else {
    echo "<p style='color: red;'>✗ 控制器文件不存在</p>";
}

echo "<h3>服务器信息:</h3>";
echo "<p>PHP版本: " . phpversion() . "</p>";
echo "<p>服务器软件: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</p>";
echo "<p>当前时间: " . date('Y-m-d H:i:s') . "</p>";
?>