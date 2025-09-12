<?php
// GenHuman API 測試腳本
header('Content-Type: text/html; charset=utf-8');

// 測試結果存儲
$testResults = [];

// 測試函數
function runTest($testName, $testFunction) {
    global $testResults;
    echo "<h3>測試: {$testName}</h3>";
    
    try {
        $result = $testFunction();
        $testResults[$testName] = ['status' => 'success', 'result' => $result];
        echo "<div style='color: green; padding: 10px; border: 1px solid green; margin: 10px 0;'>";
        echo "✓ 測試通過<br>";
        echo "結果: " . (is_array($result) ? json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $result);
        echo "</div>";
    } catch (Exception $e) {
        $testResults[$testName] = ['status' => 'failed', 'error' => $e->getMessage()];
        echo "<div style='color: red; padding: 10px; border: 1px solid red; margin: 10px 0;'>";
        echo "✗ 測試失敗<br>";
        echo "錯誤: " . $e->getMessage();
        echo "</div>";
    }
}

// 測試數據庫連接
function testDatabaseConnection() {
    require_once '../config/database.php';
    
    try {
        $pdo = getDatabaseConnection();
        if ($pdo) {
            return "數據庫連接成功";
        } else {
            throw new Exception("無法建立數據庫連接");
        }
    } catch (Exception $e) {
        throw new Exception("數據庫連接失敗: " . $e->getMessage());
    }
}

// 測試數據庫表初始化
function testDatabaseTables() {
    require_once '../config/database.php';
    
    try {
        $result = initializeDatabaseTables();
        return "數據庫表初始化: " . ($result ? "成功" : "失敗");
    } catch (Exception $e) {
        throw new Exception("數據庫表初始化失敗: " . $e->getMessage());
    }
}

// 測試文件上傳端點
function testUploadEndpoint() {
    $uploadUrl = '/api/upload-handler.php';
    
    // 檢查文件是否存在
    $filePath = '../api/upload-handler.php';
    if (!file_exists($filePath)) {
        throw new Exception("上傳處理文件不存在: {$filePath}");
    }
    
    return "上傳端點文件存在";
}

// 測試API代理端點
function testProxyEndpoint() {
    $proxyUrl = '/api/genhuman-proxy.php';
    
    // 檢查文件是否存在
    $filePath = '../api/genhuman-proxy.php';
    if (!file_exists($filePath)) {
        throw new Exception("API代理文件不存在: {$filePath}");
    }
    
    return "API代理端點文件存在";
}

// 測試文件訪問端點
function testFileAccessEndpoint() {
    $fileAccessUrl = '/api/file-access.php';
    
    // 檢查文件是否存在
    $filePath = '../api/file-access.php';
    if (!file_exists($filePath)) {
        throw new Exception("文件訪問端點不存在: {$filePath}");
    }
    
    return "文件訪問端點文件存在";
}

// 測試環境變量
function testEnvironmentVariables() {
    $requiredVars = ['GENHUMAN_API_KEY', 'GENHUMAN_API_URL'];
    $results = [];
    
    foreach ($requiredVars as $var) {
        $value = getenv($var);
        if ($value === false) {
            $results[] = "{$var}: 未設置";
        } else {
            $results[] = "{$var}: 已設置 (" . substr($value, 0, 10) . "...)";
        }
    }
    
    return implode("\n", $results);
}

// 測試配置文件
function testConfigFiles() {
    $configFiles = [
        '../config/database.php',
        '../api/genhuman-proxy.php',
        '../api/upload-handler.php',
        '../api/file-access.php'
    ];
    
    $results = [];
    foreach ($configFiles as $file) {
        if (file_exists($file)) {
            $results[] = basename($file) . ": 存在";
        } else {
            $results[] = basename($file) . ": 不存在";
        }
    }
    
    return implode("\n", $results);
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GenHuman API 後端測試</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        h1, h2, h3 {
            color: #333;
        }
        .test-summary {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>GenHuman API 後端測試</h1>
        <p>這個頁面測試GenHuman API後端組件的基本功能</p>
        
        <a href="genhuman-api-test.html" class="btn">前端API測試頁面</a>
        <a href="?run_tests=1" class="btn">運行所有測試</a>
    </div>

    <?php if (isset($_GET['run_tests'])): ?>
    <div class="container">
        <h2>測試結果</h2>
        
        <?php
        // 運行所有測試
        runTest('數據庫連接', 'testDatabaseConnection');
        runTest('數據庫表初始化', 'testDatabaseTables');
        runTest('配置文件檢查', 'testConfigFiles');
        runTest('環境變量檢查', 'testEnvironmentVariables');
        runTest('文件上傳端點', 'testUploadEndpoint');
        runTest('API代理端點', 'testProxyEndpoint');
        runTest('文件訪問端點', 'testFileAccessEndpoint');
        ?>
        
        <div class="test-summary">
            <h3>測試總結</h3>
            <?php
            $totalTests = count($testResults);
            $passedTests = array_filter($testResults, function($result) {
                return $result['status'] === 'success';
            });
            $passedCount = count($passedTests);
            $failedCount = $totalTests - $passedCount;
            
            echo "<p>總測試數: {$totalTests}</p>";
            echo "<p style='color: green;'>通過: {$passedCount}</p>";
            echo "<p style='color: red;'>失敗: {$failedCount}</p>";
            
            if ($failedCount === 0) {
                echo "<p style='color: green; font-weight: bold;'>🎉 所有測試都通過了！API後端準備就緒。</p>";
            } else {
                echo "<p style='color: red; font-weight: bold;'>⚠️ 有測試失敗，請檢查配置。</p>";
            }
            ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="container">
        <h2>使用說明</h2>
        <ol>
            <li><strong>運行後端測試</strong>: 點擊"運行所有測試"按鈕檢查後端配置</li>
            <li><strong>前端API測試</strong>: 點擊"前端API測試頁面"進行完整的API功能測試</li>
            <li><strong>檢查結果</strong>: 確保所有測試都通過後再進行實際的API調用</li>
        </ol>
        
        <h3>測試項目說明</h3>
        <ul>
            <li><strong>數據庫連接</strong>: 驗證MySQL數據庫連接是否正常</li>
            <li><strong>數據庫表初始化</strong>: 檢查必要的數據庫表是否創建</li>
            <li><strong>配置文件檢查</strong>: 確認所有必要的PHP文件是否存在</li>
            <li><strong>環境變量檢查</strong>: 驗證GenHuman API密鑰等環境變量</li>
            <li><strong>API端點檢查</strong>: 確認所有API端點文件是否就位</li>
        </ul>
    </div>
</body>
</html>