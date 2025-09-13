<?php
/**
 * Zeabur部署測試頁面
 * 檢查文件系統和路由配置
 */

header('Content-Type: application/json; charset=utf-8');

$result = [
    'status' => 'success',
    'timestamp' => date('Y-m-d H:i:s'),
    'server_info' => [
        'php_version' => PHP_VERSION,
        'current_dir' => getcwd(),
        'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'unknown',
        'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'unknown',
        'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown'
    ],
    'file_checks' => [
        'test_file_access_fixed_html' => file_exists('test-file-access-fixed.html'),
        'test_file_access_html' => file_exists('test-file-access.html'),
        'api_directory' => is_dir('../api'),
        'test_db_php' => file_exists('../api/test-db.php'),
        'list_files_php' => file_exists('../api/list-files.php')
    ],
    'directory_listing' => [
        'current_files' => array_slice(scandir('.'), 0, 20),
        'api_files' => is_dir('../api') ? array_slice(scandir('../api'), 0, 20) : 'API directory not found'
    ]
];

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>