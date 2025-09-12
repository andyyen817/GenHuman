<?php
/**
 * GenHuman API 代理端點
 * 遵循開發規則：安全處理API調用，避免直接暴露第三方API
 * 支持文件上傳和Base64備用方案
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// 錯誤處理函數
function sendError($message, $code = 400) {
    http_response_code($code);
    echo json_encode([
        'success' => false,
        'message' => $message,
        'code' => $code
    ]);
    exit;
}

// 成功響應函數
function sendSuccess($data, $message = 'success') {
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// 日誌記錄函數
function logRequest($endpoint, $data) {
    $logData = [
        'timestamp' => date('Y-m-d H:i:s'),
        'endpoint' => $endpoint,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ];
    
    $logFile = __DIR__ . '/../logs/genhuman-api.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    file_put_contents($logFile, json_encode($logData) . "\n", FILE_APPEND | LOCK_EX);
}

// 獲取請求數據
$input = json_decode(file_get_contents('php://input'), true);
$method = $_SERVER['REQUEST_METHOD'];
$path = $_GET['path'] ?? '';

// 驗證Token
if (empty($input['token']) && $path !== 'musetalk-task') {
    sendError('API Token is required');
}

$token = $input['token'] ?? '';
if ($token && !str_starts_with($token, 'Bearer ')) {
    $token = 'Bearer ' . $token;
}

// API端點映射
$endpoints = [
    'clone-voice' => 'https://api.yidevs.com/app/human/human/Voice/clone',
    'clone-voice-deep' => 'https://api.yidevs.com/app/human/human/Voice/clone', // 同一端點
    'synthesize-voice' => 'https://api.yidevs.com/app/human/human/Voice/deepCreated',
    'synthesize-voice-deep' => 'https://api.yidevs.com/app/human/human/Voice/deepCreated',
    'create-scene' => 'https://api.yidevs.com/app/human/human/Scene/senior',
    'create-scene-senior' => 'https://api.yidevs.com/app/human/human/Scene/senior',
    'create-musetalk' => 'https://api.yidevs.com/app/human/human/Musetalk/create',
    'musetalk-task' => 'https://api.yidevs.com/app/human/human/Musetalk/task'
];

if (!isset($endpoints[$path])) {
    sendError('Invalid API endpoint: ' . $path);
}

$apiUrl = $endpoints[$path];

// 記錄請求
logRequest($path, $input);

// 準備請求數據
$requestData = $input;
unset($requestData['token']); // 移除token，避免傳遞給第三方API

// 處理GET請求（任務查詢）
if ($method === 'GET' && $path === 'musetalk-task') {
    $taskId = $_GET['task_id'] ?? '';
    if (empty($taskId)) {
        sendError('task_id is required');
    }
    
    $apiUrl .= '?task_id=' . urlencode($taskId);
    $requestData = null;
}

// 發送API請求
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 60,
    CURLOPT_CONNECTTIMEOUT => 30,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: ' . $token
    ],
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false
]);

if ($method === 'POST' && $requestData) {
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
}

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    sendError('API request failed: ' . $error, 500);
}

if ($httpCode !== 200) {
    sendError('API returned error code: ' . $httpCode, $httpCode);
}

$responseData = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    sendError('Invalid JSON response from API', 500);
}

// 返回API響應
header('Content-Type: application/json');
echo json_encode($responseData);
?>