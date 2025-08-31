<?php

namespace app\controller;

use support\Request;
use support\Response;

/**
 * Vidspark配置現實檢查控制器
 * 面對現實：某些PHP配置無法在運行時修改
 * 提供實用的替代解決方案
 */
class VidsparkConfigRealityController
{
    /**
     * 配置現實檢查
     * 分析哪些配置可以修改，哪些不能，並提供解決方案
     */
    public function configRealityCheck(Request $request): Response
    {
        // 強制執行我們的配置設置
        $this->forcePhpConfig();
        
        // 獲取當前配置
        $currentConfig = [
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'max_input_time' => ini_get('max_input_time'),
            'max_input_vars' => ini_get('max_input_vars'),
            'max_file_uploads' => ini_get('max_file_uploads')
        ];
        
        // 期望配置
        $expectedConfig = [
            'upload_max_filesize' => '1000M',
            'post_max_size' => '1100M',
            'memory_limit' => '2048M',
            'max_execution_time' => '1800',
            'max_input_time' => '1800',
            'max_input_vars' => '10000',
            'max_file_uploads' => '20'
        ];
        
        // 分析結果
        $analysisResults = [];
        $modifiableConfigs = [];
        $restrictedConfigs = [];
        
        foreach ($expectedConfig as $key => $expectedValue) {
            $currentValue = $currentConfig[$key];
            $isModifiable = ($currentValue === $expectedValue) || ($currentValue !== 'Unknown');
            
            $analysis = [
                'config_name' => $key,
                'expected' => $expectedValue,
                'current' => $currentValue,
                'is_modifiable' => $isModifiable,
                'status' => $this->getConfigStatus($expectedValue, $currentValue),
                'solution' => $this->getSolutionForConfig($key, $isModifiable)
            ];
            
            $analysisResults[] = $analysis;
            
            if ($isModifiable && $currentValue === $expectedValue) {
                $modifiableConfigs[] = $key;
            } else {
                $restrictedConfigs[] = $key;
            }
        }
        
        // 生成實用的解決方案
        $practicalSolutions = $this->generatePracticalSolutions($restrictedConfigs);
        
        return new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'success' => true,
            'message' => 'PHP配置現實檢查完成',
            'timestamp' => date('Y-m-d H:i:s'),
            'php_version' => PHP_VERSION,
            'environment' => 'Zeabur Docker Container',
            'analysis_results' => $analysisResults,
            'summary' => [
                'modifiable_configs' => $modifiableConfigs,
                'restricted_configs' => $restrictedConfigs,
                'modifiable_count' => count($modifiableConfigs),
                'restricted_count' => count($restrictedConfigs)
            ],
            'practical_solutions' => $practicalSolutions,
            'recommendations' => $this->getRecommendations($restrictedConfigs)
        ], JSON_UNESCAPED_UNICODE));
    }
    
    /**
     * 文件上傳現實檢查
     * 在當前配置限制下，實際能處理多大的文件
     */
    public function uploadRealityCheck(Request $request): Response
    {
        $currentUploadLimit = $this->parseSize(ini_get('upload_max_filesize'));
        $currentPostLimit = $this->parseSize(ini_get('post_max_size'));
        $memoryLimit = $this->parseSize(ini_get('memory_limit'));
        
        // 計算實際可用的上傳大小
        $actualUploadLimit = min($currentUploadLimit, $currentPostLimit);
        
        // 測試不同大小文件的處理能力
        $fileSizeTests = [
            '100KB' => 100 * 1024,
            '500KB' => 500 * 1024,
            '1MB' => 1024 * 1024,
            '2MB' => 2 * 1024 * 1024,
            '5MB' => 5 * 1024 * 1024,
            '10MB' => 10 * 1024 * 1024
        ];
        
        $uploadCapability = [];
        foreach ($fileSizeTests as $sizeName => $sizeBytes) {
            $canHandle = ($sizeBytes <= $actualUploadLimit);
            $uploadCapability[] = [
                'size_name' => $sizeName,
                'size_bytes' => $sizeBytes,
                'can_handle' => $canHandle,
                'status' => $canHandle ? '✅ 可以處理' : '❌ 超出限制'
            ];
        }
        
        return new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'success' => true,
            'message' => '文件上傳現實檢查完成',
            'current_limits' => [
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size'),
                'memory_limit' => ini_get('memory_limit'),
                'actual_upload_limit_bytes' => $actualUploadLimit,
                'actual_upload_limit_mb' => round($actualUploadLimit / (1024 * 1024), 2)
            ],
            'upload_capability' => $uploadCapability,
            'recommendations' => [
                'optimal_file_size' => '低於 ' . ini_get('upload_max_filesize'),
                'fallback_solutions' => [
                    'Base64編碼上傳（繞過文件上傳限制）',
                    '分片上傳（將大文件切分）', 
                    '外部存儲服務（如Amazon S3）',
                    '客戶端壓縮（減小文件大小）'
                ]
            ]
        ], JSON_UNESCAPED_UNICODE));
    }
    
    /**
     * 強制設置PHP配置（簡化版）
     */
    private function forcePhpConfig()
    {
        $settings = [
            'memory_limit' => '2048M',
            'max_execution_time' => '1800',
            'max_input_vars' => '10000',
            'max_file_uploads' => '20',
            'upload_max_filesize' => '1000M',
            'post_max_size' => '1100M',
            'max_input_time' => '1800'
        ];
        
        foreach ($settings as $setting => $value) {
            ini_set($setting, $value);
        }
    }
    
    /**
     * 獲取配置狀態
     */
    private function getConfigStatus($expected, $current)
    {
        if ($current === 'Unknown') {
            return 'unknown';
        } elseif ($current === $expected) {
            return 'correct';
        } else {
            return 'incorrect';
        }
    }
    
    /**
     * 獲取具體配置的解決方案
     */
    private function getSolutionForConfig($configName, $isModifiable)
    {
        $solutions = [
            'upload_max_filesize' => [
                'modifiable' => '通過ini_set動態設置',
                'restricted' => '使用Base64上傳或分片上傳繞過限制'
            ],
            'post_max_size' => [
                'modifiable' => '通過ini_set動態設置',
                'restricted' => '使用GET請求或分片傳輸'
            ],
            'max_input_time' => [
                'modifiable' => '通過ini_set動態設置',
                'restricted' => '優化處理邏輯，減少輸入處理時間'
            ],
            'memory_limit' => [
                'modifiable' => '通過ini_set動態設置',
                'restricted' => '優化內存使用，使用流式處理'
            ],
            'max_execution_time' => [
                'modifiable' => '通過ini_set動態設置',
                'restricted' => '使用異步處理或分步處理'
            ]
        ];
        
        $configSolutions = $solutions[$configName] ?? ['modifiable' => '標準設置', 'restricted' => '需要系統級配置'];
        
        return $isModifiable ? $configSolutions['modifiable'] : $configSolutions['restricted'];
    }
    
    /**
     * 生成實用解決方案
     */
    private function generatePracticalSolutions($restrictedConfigs)
    {
        $solutions = [];
        
        if (in_array('upload_max_filesize', $restrictedConfigs) || in_array('post_max_size', $restrictedConfigs)) {
            $solutions[] = [
                'problem' => '文件上傳大小限制',
                'solution' => 'Base64編碼上傳',
                'description' => '將文件轉換為Base64字符串，通過JSON傳輸，繞過PHP文件上傳限制',
                'implementation' => '前端使用FileReader.readAsDataURL()，後端解碼base64數據'
            ];
            
            $solutions[] = [
                'problem' => '大文件上傳',
                'solution' => '分片上傳',
                'description' => '將大文件切分成小塊，逐個上傳後在服務器端合併',
                'implementation' => '前端分片，後端接收並合併文件塊'
            ];
        }
        
        if (in_array('max_input_time', $restrictedConfigs)) {
            $solutions[] = [
                'problem' => '輸入時間限制',
                'solution' => '異步處理',
                'description' => '使用隊列系統，將耗時操作放入後台處理',
                'implementation' => '前端提交任務，後端返回任務ID，前端輪詢結果'
            ];
        }
        
        return $solutions;
    }
    
    /**
     * 獲取推薦解決方案
     */
    private function getRecommendations($restrictedConfigs)
    {
        $recommendations = [
            'immediate_actions' => [
                '接受現實：某些PHP配置在Docker容器中無法修改',
                '使用替代方案：Base64上傳、分片上傳、異步處理',
                '優化代碼：減少內存使用，提高處理效率'
            ],
            'long_term_solutions' => [
                '聯繫Zeabur技術支持：咨詢是否可以提供自定義PHP配置',
                '考慮VPS部署：獲得完整的系統控制權',
                '使用外部服務：如Amazon S3用於大文件存儲'
            ],
            'current_workarounds' => [
                '小文件(< 2MB)：直接上傳',
                '中等文件(2-10MB)：Base64編碼上傳',
                '大文件(> 10MB)：分片上傳或外部存儲',
                '超時問題：使用異步處理和輪詢'
            ]
        ];
        
        return $recommendations;
    }
    
    /**
     * 解析大小字符串為字節數
     */
    private function parseSize($size)
    {
        $size = trim($size);
        $last = strtolower($size[strlen($size)-1]);
        $size = (int) $size;
        
        switch($last) {
            case 'g':
                $size *= 1024;
            case 'm':
                $size *= 1024;
            case 'k':
                $size *= 1024;
        }
        
        return $size;
    }
}
