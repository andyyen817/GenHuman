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
     */
    public function validateToken(Request $request): Response
    {
        try {
            $input = json_decode($request->rawBody(), true);
            $token = $input['token'] ?? '';
            $testText = $input['test_text'] ?? '測試';
            
            if (empty($token)) {
                throw new Exception('Token不能為空');
            }
            
            $result = $this->callGenHumanAPI('/app/human/human/Index/created', [
                'text' => $testText
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
     */
    public function testFreeAvatar(Request $request): Response
    {
        try {
            $input = json_decode($request->rawBody(), true);
            $token = $input['token'] ?? '';
            $text = $input['text'] ?? '測試數字人';
            $avatarId = $input['avatar_id'] ?? 1;
            $voiceId = $input['voice_id'] ?? 1;
            
            if (empty($token)) {
                throw new Exception('Token不能為空');
            }
            
            $result = $this->callGenHumanAPI('/app/human/human/Index/created', [
                'text' => $text,
                'avatar_id' => $avatarId,
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
                'Authorization: Bearer ' . $token,
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
        
        if ($httpCode !== 200) {
            throw new Exception("HTTP錯誤 $httpCode: $response");
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
        return substr($token, 0, 8) . '...' . substr($token, -4);
    }
    
    /**
     * 獲取代理狀態
     */
    public function getProxyStatus(Request $request): Response
    {
        $status = [
            'proxy_version' => '1.0',
            'target_api' => 'https://api.yidevs.com',
            'available_endpoints' => [
                '/vidspark-api-proxy/validate-token',
                '/vidspark-api-proxy/test-free-avatar'
            ],
            'cors_enabled' => true,
            'server_time' => date('Y-m-d H:i:s'),
            'proxy_purpose' => '解決CORS跨域問題，代理GenHuman API調用'
        ];
        
        return new Response(200, [
            'Content-Type' => 'application/json; charset=utf-8'
        ], json_encode($status, JSON_UNESCAPED_UNICODE));
    }
}
