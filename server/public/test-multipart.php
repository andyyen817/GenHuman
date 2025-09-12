<?php
// 简单的 multipart 数据测试脚本
header('Content-Type: application/json; charset=utf-8');

$debug = [
    'method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
    'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'unknown',
    'content_length' => $_SERVER['CONTENT_LENGTH'] ?? 'unknown',
    'post_data' => $_POST,
    'files_data' => $_FILES,
    'raw_input_length' => 0,
    'raw_input_preview' => ''
];

// 尝试多种方式读取原始输入
$rawInput = file_get_contents('php://input');
$debug['raw_input_length'] = strlen($rawInput);
$debug['raw_input_preview'] = substr($rawInput, 0, 200);

// 尝试其他方式
$debug['http_raw_post_data'] = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? strlen($GLOBALS['HTTP_RAW_POST_DATA']) : 'not_available';

// 检查 PHP 配置
$debug['php_version'] = phpversion();
$debug['always_populate_raw_post_data'] = ini_get('always_populate_raw_post_data');
$debug['enable_post_data_reading'] = ini_get('enable_post_data_reading');
$debug['max_input_vars'] = ini_get('max_input_vars');

// 检查是否是 PHP 内置服务器的问题
$debug['server_software'] = $_SERVER['SERVER_SOFTWARE'] ?? 'unknown';
$debug['sapi_name'] = php_sapi_name();

// 如果有原始输入但 $_FILES 为空，尝试手动解析
if (!empty($rawInput) && empty($_FILES) && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'multipart/form-data') !== false) {
    // 提取 boundary
    if (preg_match('/boundary=([^;\s]+)/', $_SERVER['CONTENT_TYPE'], $matches)) {
        $boundary = $matches[1];
        $debug['boundary'] = $boundary;
        
        // 分割数据
        $parts = explode('--' . $boundary, $rawInput);
        $debug['parts_count'] = count($parts);
        
        foreach ($parts as $i => $part) {
            if (empty(trim($part)) || trim($part) === '--') {
                continue;
            }
            
            // 分离头部和内容
            $sections = explode("\r\n\r\n", $part, 2);
            if (count($sections) !== 2) {
                continue;
            }
            
            $headers = $sections[0];
            $content = rtrim($sections[1], "\r\n");
            
            $debug['part_' . $i] = [
                'headers' => $headers,
                'content_length' => strlen($content),
                'content_preview' => substr($content, 0, 50)
            ];
            
            // 解析 Content-Disposition
            if (preg_match('/Content-Disposition: form-data; name="([^"]+)"(?:; filename="([^"]+)")?/', $headers, $matches)) {
                $name = $matches[1];
                $filename = isset($matches[2]) ? $matches[2] : null;
                
                if ($filename) {
                    // 这是文件上传
                    $tempFile = tempnam(sys_get_temp_dir(), 'upload_');
                    file_put_contents($tempFile, $content);
                    
                    $_FILES[$name] = [
                        'name' => $filename,
                        'type' => 'application/octet-stream',
                        'tmp_name' => $tempFile,
                        'error' => UPLOAD_ERR_OK,
                        'size' => strlen($content)
                    ];
                    
                    $debug['manual_files'][$name] = [
                        'filename' => $filename,
                        'size' => strlen($content),
                        'temp_file' => $tempFile
                    ];
                } else {
                    // 这是普通表单字段
                    $_POST[$name] = $content;
                    $debug['manual_post'][$name] = $content;
                }
            }
        }
        
        // 更新解析后的数据
        $debug['post_data_after'] = $_POST;
        $debug['files_data_after'] = $_FILES;
    }
}

echo json_encode($debug, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>