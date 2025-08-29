<?php

namespace app\controller;

use support\Request;
use support\Response;
use Exception;

/**
 * Vidspark API代理控制器
 * 解決CORS跨域問題，代理GenHuman API調用
 * 
 * 創建時間：2025-08-29
 * 版本：v1.0
 * 遵循：@genhuman開發規則.md 超小步修改原則
 */
class VidsparkApiProxyController
{
    /**
     * 驗證Token
     * 使用免費聲音合成API進行Token驗證
     */
    public function validateToken(Request $request): Response
    {
        try {
            $input = json_decode($request->rawBody(), true);
            $token = $input['token'] ?? '';
            $testText = $input['test_text'] ?? '測試Token';
            
            if (empty($token)) {
                throw new Exception('Token不能為空');
            }
            
            // 使用免費聲音合成API驗證Token
            $result = $this->callGenHumanAPI('/app/human/human/Voice/created', [
                'text' => $testText,
                'voice_id' => 'e2-1a6c-4679-aad2-a945d0034d72' // 使用文檔中的示例voice_id
            ], $token);
            
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode($result, JSON_UNESCAPED_UNICODE));
            
        } catch (Exception $e) {
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => false,
                'code' => 500,
                'msg' => $e->getMessage(),
                'test_time' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE));
        }
    }
    
    /**
     * 測試免費數字人
     * 實現完整的兩步驟流程：文本→聲音→數字人影片
     */
    public function testFreeAvatar(Request $request): Response
    {
        try {
            $input = json_decode($request->rawBody(), true);
            $token = $input['token'] ?? '';
            $text = $input['text'] ?? '這是Vidspark測試，測試免費數字人生成功能';
            
            if (empty($token)) {
                throw new Exception('Token不能為空');
            }
            
            // 步驟1：文本轉聲音
            $voiceResult = $this->callGenHumanAPI('/app/human/human/Voice/created', [
                'text' => $text,
                'voice_id' => 'e2-1a6c-4679-aad2-a945d0034d72' // 使用文檔示例
            ], $token);
            
            if (!isset($voiceResult['data']['audio_url'])) {
                throw new Exception('聲音合成失敗：' . ($voiceResult['msg'] ?? '未知錯誤'));
            }
            
            $audioUrl = $voiceResult['data']['audio_url'];
            
            // 注意：數字人合成需要scene_task_id，但我們暫時只測試到聲音合成
            // 返回聲音合成的結果作為測試成功的證明
            $result = [
                'code' => 200,
                'msg' => '免費聲音合成測試成功！數字人合成需要scene_task_id',
                'data' => [
                    'step1_voice_synthesis' => $voiceResult,
                    'audio_url' => $audioUrl,
                    'next_step' => '需要獲取scene_task_id才能繼續數字人合成',
                    'note' => '當前已驗證Token可以成功調用GenHuman API'
                ],
                '_proxy_info' => [
                    'workflow' => 'text_to_voice_success',
                    'audio_generated' => true,
                    'ready_for_avatar' => false
                ],
                '_test_time' => date('Y-m-d H:i:s')
            ];
            
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode($result, JSON_UNESCAPED_UNICODE));
            
        } catch (Exception $e) {
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => false,
                'code' => 500,
                'msg' => $e->getMessage(),
                'test_time' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE));
        }
    }
    
    /**
     * 調用GenHuman API的核心方法
     */
    private function callGenHumanAPI($endpoint, $data, $token)
    {
        $url = 'https://api.yidevs.com' . $endpoint;
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Authorization: ' . $this->formatAuthHeader($token),
                'Content-Type: application/json',
                'Accept: application/json',
                'User-Agent: Vidspark-Proxy/1.0'
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        
        // 記錄調用詳情
        $callDetails = [
            'url' => $url,
            'http_code' => $httpCode,
            'response_time' => $info['total_time'],
            'request_data' => $data,
            'token_mask' => $this->maskToken($token)
        ];
        
        if ($error) {
            throw new Exception("cURL錯誤: $error");
        }
        
        // 詳細記錄HTTP響應信息用於診斷
        $debugInfo = [
            'http_code' => $httpCode,
            'raw_response' => $response,
            'curl_error' => $error,
            'url' => $url,
            'request_data' => $data
        ];
        
        if ($httpCode !== 200) {
            // 增強錯誤診斷信息
            $errorMessage = "HTTP錯誤 $httpCode";
            if ($response) {
                $errorMessage .= " - 響應: " . substr($response, 0, 500);
            }
            $errorMessage .= " - 詳細: " . json_encode($debugInfo);
            throw new Exception($errorMessage);
        }
        
        $result = json_decode($response, true);
        if ($result === null) {
            throw new Exception("JSON解析失敗，原始響應: $response");
        }
        
        // 添加調用詳情到返回結果
        $result['_proxy_info'] = $callDetails;
        $result['_test_time'] = date('Y-m-d H:i:s');
        
        return $result;
    }
    
    /**
     * 掩碼Token顯示
     */
    private function maskToken($token)
    {
        if (!$token || strlen($token) < 10) {
            return 'invalid';
        }
        
        // 如果Token包含Bearer前綴，只掩碼實際Token部分
        $cleanToken = trim($token);
        if (stripos($cleanToken, 'Bearer ') === 0) {
            $actualToken = trim(substr($cleanToken, 7));
            return 'Bearer ' . substr($actualToken, 0, 8) . '...' . substr($actualToken, -4);
        }
        
        return substr($token, 0, 8) . '...' . substr($token, -4);
    }
    
    /**
     * 格式化授權Header，避免重複Bearer前綴
     */
    private function formatAuthHeader($token)
    {
        // 移除可能存在的Bearer前綴
        $cleanToken = trim($token);
        if (stripos($cleanToken, 'Bearer ') === 0) {
            $cleanToken = trim(substr($cleanToken, 7));
        }
        
        // 返回正確格式的授權header
        return 'Bearer ' . $cleanToken;
    }
    
    /**
     * 步驟1：聲音克隆
     */
    public function cloneVoice(Request $request): Response
    {
        try {
            $input = json_decode($request->rawBody(), true);
            $token = $input['token'] ?? '';
            $name = $input['name'] ?? 'Vidspark用戶聲音';
            $audioUrl = $input['audio_url'] ?? '';
            $description = $input['description'] ?? 'Vidspark聲音克隆';
            
            // 詳細記錄輸入參數
            $requestLog = [
                'method' => 'cloneVoice',
                'timestamp' => date('Y-m-d H:i:s'),
                'input_data' => [
                    'token_mask' => $this->maskToken($token),
                    'name' => $name,
                    'audio_url' => $audioUrl,
                    'description' => $description
                ]
            ];
            
            if (empty($token)) {
                throw new Exception('Token不能為空');
            }
            
            if (empty($audioUrl)) {
                throw new Exception('音頻地址不能為空');
            }
            
            // 檢查音頻URL是否可訪問
            if (!filter_var($audioUrl, FILTER_VALIDATE_URL)) {
                throw new Exception('音頻URL格式不正確: ' . $audioUrl);
            }
            
            $result = $this->callGenHumanAPI('/app/human/human/Voice/clone', [
                'name' => $name,
                'audio_url' => $audioUrl,
                'description' => $description
            ], $token);
            
            // 記錄成功結果
            $result['_request_log'] = $requestLog;
            
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode($result, JSON_UNESCAPED_UNICODE));
            
        } catch (Exception $e) {
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => false,
                'code' => 500,
                'msg' => $e->getMessage(),
                'debug_info' => $requestLog ?? null,
                'test_time' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * 步驟2：用克隆聲音合成語音
     */
    public function synthesizeWithClonedVoice(Request $request): Response
    {
        try {
            $input = json_decode($request->rawBody(), true);
            $token = $input['token'] ?? '';
            $text = $input['text'] ?? '';
            $voiceId = $input['voice_id'] ?? '';
            
            if (empty($token) || empty($text) || empty($voiceId)) {
                throw new Exception('Token、文字和聲音ID不能為空');
            }
            
            $result = $this->callGenHumanAPI('/app/human/human/Voice/created', [
                'text' => $text,
                'voice_id' => $voiceId
            ], $token);
            
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode($result, JSON_UNESCAPED_UNICODE));
            
        } catch (Exception $e) {
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => false,
                'code' => 500,
                'msg' => $e->getMessage(),
                'test_time' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * 步驟3：創建場景（上傳影片）
     */
    public function createScene(Request $request): Response
    {
        try {
            $input = json_decode($request->rawBody(), true);
            $token = $input['token'] ?? '';
            $videoUrl = $input['video_url'] ?? '';
            $videoName = $input['video_name'] ?? 'Vidspark用戶場景';
            $callbackUrl = $input['callback_url'] ?? 'https://genhuman-digital-human.zeabur.app/vidspark-admin/api/callback';
            
            if (empty($token) || empty($videoUrl)) {
                throw new Exception('Token和影片地址不能為空');
            }
            
            $result = $this->callGenHumanAPI('/app/human/human/Scene/created', [
                'callback_url' => $callbackUrl,
                'video_name' => $videoName,
                'video_url' => $videoUrl
            ], $token);
            
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode($result, JSON_UNESCAPED_UNICODE));
            
        } catch (Exception $e) {
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => false,
                'code' => 500,
                'msg' => $e->getMessage(),
                'test_time' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * 步驟4：合成數字人
     */
    public function synthesizeAvatar(Request $request): Response
    {
        try {
            $input = json_decode($request->rawBody(), true);
            $token = $input['token'] ?? '';
            $sceneTaskId = $input['scene_task_id'] ?? '';
            $audioUrl = $input['audio_url'] ?? '';
            $callbackUrl = $input['callback_url'] ?? 'https://genhuman-digital-human.zeabur.app/vidspark-admin/api/callback';
            
            if (empty($token) || empty($sceneTaskId) || empty($audioUrl)) {
                throw new Exception('Token、場景ID和音頻地址不能為空');
            }
            
            $result = $this->callGenHumanAPI('/app/human/human/Index/created', [
                'callback_url' => $callbackUrl,
                'scene_task_id' => $sceneTaskId,
                'audio_url' => $audioUrl
            ], $token);
            
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode($result, JSON_UNESCAPED_UNICODE));
            
        } catch (Exception $e) {
            return new Response(200, [
                'Content-Type' => 'application/json; charset=utf-8'
            ], json_encode([
                'success' => false,
                'code' => 500,
                'msg' => $e->getMessage(),
                'test_time' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * 獲取代理狀態
     */
    public function getProxyStatus(Request $request): Response
    {
        $status = [
            'proxy_version' => '2.0',
            'target_api' => 'https://api.yidevs.com',
            'available_endpoints' => [
                '/vidspark-api-proxy/validate-token' => 'Token驗證',
                '/vidspark-api-proxy/test-free-avatar' => '免費數字人測試',
                '/vidspark-api-proxy/clone-voice' => '聲音克隆',
                '/vidspark-api-proxy/synthesize-voice' => '用克隆聲音合成語音',
                '/vidspark-api-proxy/create-scene' => '創建場景',
                '/vidspark-api-proxy/synthesize-avatar' => '合成數字人'
            ],
            'cors_enabled' => true,
            'server_time' => date('Y-m-d H:i:s'),
            'proxy_purpose' => '解決CORS跨域問題，代理完整數字人生成流程'
        ];
        
        return new Response(200, [
            'Content-Type' => 'application/json; charset=utf-8'
        ], json_encode($status, JSON_UNESCAPED_UNICODE));
    }
}
