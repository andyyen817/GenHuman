<?php
/**
 * Vidspark強制PHP配置設置
 * 當環境變量無法生效時，使用此腳本強制設置PHP配置
 */

// 設置PHP配置
ini_set('upload_max_filesize', '1000M');
ini_set('post_max_size', '1100M');
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', '1800');
ini_set('max_input_time', '1800');
ini_set('max_input_vars', '10000');
ini_set('max_file_uploads', '20');
ini_set('file_uploads', '1');

// 返回設置結果
header('Content-Type: application/json; charset=utf-8');

$result = [
    'success' => true,
    'message' => 'PHP配置已強制設置',
    'timestamp' => date('Y-m-d H:i:s'),
    'applied_settings' => [
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time'),
        'max_input_time' => ini_get('max_input_time'),
        'max_input_vars' => ini_get('max_input_vars'),
        'max_file_uploads' => ini_get('max_file_uploads'),
        'file_uploads' => ini_get('file_uploads')
    ],
    'note' => '這些設置僅在當前請求中有效，需要在每個上傳腳本中重新設置'
];

echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
