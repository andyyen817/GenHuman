<?php
/**
 * GenHuman 登入診斷工具 v2.0
 * 目的：在不影響現有系統的情況下診斷登入問題
 * 遵循：最小修改原則，零風險策略
 */

require_once __DIR__ . '/vendor/autoload.php';

echo "=== GenHuman 登入診斷工具 v2.0 ===\n";
echo "開始時間：" . date('Y-m-d H:i:s') . "\n";
echo "檢查範圍：數據庫連接、管理員用戶、密碼驗證\n\n";

// 1. 檢查數據庫連接
echo "🔍 步驟1：檢查數據庫連接\n";
try {
    $config = require __DIR__ . '/config/think-orm.php';
    $mysqlConfig = $config['connections']['mysql'];
    
    echo "數據庫配置信息：\n";
    echo "  主機: {$mysqlConfig['hostname']}\n";
    echo "  數據庫: {$mysqlConfig['database']}\n";
    echo "  用戶名: {$mysqlConfig['username']}\n";
    echo "  密碼: " . str_repeat('*', strlen($mysqlConfig['password'])) . "\n";
    
    $dsn = "mysql:host={$mysqlConfig['hostname']};dbname={$mysqlConfig['database']};charset=utf8mb4";
    $db = new PDO($dsn, $mysqlConfig['username'], $mysqlConfig['password']);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ 數據庫連接成功\n\n";
} catch (Exception $e) {
    echo "❌ 數據庫連接失敗: " . $e->getMessage() . "\n";
    echo "🚨 無法繼續診斷，請檢查數據庫配置\n";
    exit(1);
}

// 2. 檢查管理員用戶表
echo "🔍 步驟2：檢查管理員用戶\n";
try {
    // 檢查表是否存在
    $stmt = $db->query("SHOW TABLES LIKE 'yc_admin'");
    if ($stmt->rowCount() == 0) {
        echo "❌ yc_admin 表不存在\n";
        exit(1);
    }
    echo "✅ yc_admin 表存在\n";
    
    // 檢查管理員用戶
    $stmt = $db->prepare("SELECT id, username, password, create_time FROM yc_admin WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "✅ 管理員用戶存在\n";
        echo "  用戶ID: {$admin['id']}\n";
        echo "  用戶名: {$admin['username']}\n";
        echo "  密碼Hash: " . substr($admin['password'], 0, 30) . "...\n";
        echo "  創建時間: {$admin['create_time']}\n";
        
        // 檢查密碼Hash格式
        if (strpos($admin['password'], '$2y$') === 0) {
            echo "✅ 密碼使用bcrypt加密\n";
        } else {
            echo "⚠️  密碼加密格式可能不正確\n";
        }
    } else {
        echo "❌ 管理員用戶不存在\n";
        
        // 檢查是否有其他用戶
        $stmt = $db->query("SELECT COUNT(*) as count FROM yc_admin");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "  yc_admin表中共有 {$result['count']} 個用戶\n";
        
        if ($result['count'] > 0) {
            $stmt = $db->query("SELECT username FROM yc_admin LIMIT 5");
            echo "  現有用戶名：";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo $row['username'] . " ";
            }
            echo "\n";
        }
        exit(1);
    }
} catch (Exception $e) {
    echo "❌ 檢查管理員用戶失敗: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n";

// 3. 測試密碼驗證
echo "🔍 步驟3：測試密碼驗證\n";
$testPassword = '123456';
echo "測試密碼: {$testPassword}\n";

if (password_verify($testPassword, $admin['password'])) {
    echo "✅ 密碼驗證成功！\n";
    echo "🎯 診斷結果：密碼系統正常，問題可能在前端或API層\n";
} else {
    echo "❌ 密碼驗證失敗\n";
    echo "🔧 嘗試生成新的密碼Hash...\n";
    $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
    echo "原Hash: {$admin['password']}\n";
    echo "新Hash: {$newHash}\n";
    
    // 測試新Hash是否能驗證
    if (password_verify($testPassword, $newHash)) {
        echo "✅ 新Hash驗證成功\n";
        echo "🎯 診斷結果：需要重置管理員密碼\n";
    } else {
        echo "❌ 新Hash驗證也失敗，PHP密碼函數可能有問題\n";
    }
}

echo "\n";

// 4. 檢查其他可能的問題
echo "🔍 步驟4：檢查其他配置\n";

// 檢查PHP擴展
$requiredExtensions = ['pdo', 'pdo_mysql', 'bcmath', 'mbstring'];
foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✅ {$ext} 擴展已加載\n";
    } else {
        echo "❌ {$ext} 擴展未加載\n";
    }
}

// 檢查Webman相關文件
$webmanFiles = [
    'start.php' => __DIR__ . '/start.php',
    'config/app.php' => __DIR__ . '/config/app.php',
    'app/admin/controller/permission/UserController.php' => __DIR__ . '/app/admin/controller/permission/UserController.php'
];

foreach ($webmanFiles as $name => $path) {
    if (file_exists($path)) {
        echo "✅ {$name} 文件存在\n";
    } else {
        echo "❌ {$name} 文件不存在\n";
    }
}

echo "\n";
echo "=== 診斷完成 ===\n";
echo "完成時間：" . date('Y-m-d H:i:s') . "\n";
echo "\n";
echo "📋 下一步建議：\n";
if (password_verify($testPassword, $admin['password'])) {
    echo "1. 密碼驗證正常，檢查前端API調用邏輯\n";
    echo "2. 檢查管理後台的登入接口\n";
    echo "3. 檢查CORS跨域設置\n";
} else {
    echo "1. 重置管理員密碼\n";
    echo "2. 重新測試登入功能\n";
}
echo "\n";
?>
