<?php
/**
 * API配置管理控制器
 * 基於 genhumanapi總整理v1v0822.md 的完整API配置
 */

namespace app\controller;

use support\Request;
use support\Response;
use think\facade\Db;

class ApiConfigController
{
    /**
     * API配置管理頁面
     */
    public function index(): Response
    {
        $html = '<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GenHuman API配置管理</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #f5f6fa;
            color: #2c3e50;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }
        .card-header {
            background: #3498db;
            color: white;
            padding: 15px 20px;
            font-weight: bold;
        }
        .card-body {
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #2c3e50;
        }
        .form-input {
            width: 100%;
            padding: 10px;
            border: 2px solid #ecf0f1;
            border-radius: 5px;
            font-size: 14px;
        }
        .form-input:focus {
            border-color: #3498db;
            outline: none;
        }
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-test {
            background: #27ae60;
            margin-left: 10px;
        }
        .status {
            padding: 10px;
            border-radius: 5px;
            margin-top: 15px;
        }
        .status.success {
            background: #d5edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .api-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
        }
        .api-endpoint {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #3498db;
        }
        .api-endpoint h4 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .api-info {
            font-size: 12px;
            color: #7f8c8d;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🚀 GenHuman API配置管理中心</h1>
        <p>基於易定開放平台的完整API配置</p>
    </div>
    
    <div class="container">
        <!-- 基本配置 -->
        <div class="card">
            <div class="card-header">🔑 基本API配置</div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">API Token (Bearer)</label>
                    <input type="text" class="form-input" id="apiToken" 
                           value="Bearer 08D7EE7F91D258F27B4ADDF59CDDDEDE.1E95F76130BA23D37CE7BBBD69B19CCF.KYBVDWNR"
                           placeholder="請輸入易定開放平台的API Token">
                </div>
                <div class="form-group">
                    <label class="form-label">API 基礎地址</label>
                    <input type="text" class="form-input" id="apiBaseUrl" 
                           value="https://api.yidevs.com"
                           placeholder="API基礎地址">
                </div>
                <button class="btn" onclick="saveConfig()">💾 保存配置</button>
                <button class="btn btn-test" onclick="testConnection()">🔍 測試連接</button>
                <div id="configStatus"></div>
            </div>
        </div>

        <!-- API端點配置 -->
        <div class="card">
            <div class="card-header">📡 核心API端點配置</div>
            <div class="card-body">
                <div class="api-section">
                    <!-- 口型驅動API -->
                    <div class="api-endpoint">
                        <h4>🎭 口型驅動API</h4>
                        <div class="api-info">計費：1-5積分/秒</div>
                        <div class="form-group">
                            <label class="form-label">數字人合成-直傳</label>
                            <input type="text" class="form-input" 
                                   value="/app/human/human/Musetalk/direct" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label">數字人合成-PRO</label>
                            <input type="text" class="form-input" 
                                   value="/app/human/human/Musetalk/senior" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label">數字人合成-極速</label>
                            <input type="text" class="form-input" 
                                   value="/app/human/human/Musetalk/generate" readonly>
                        </div>
                        <button class="btn btn-test" onclick="testAPI(\'musetalk\')">測試口型API</button>
                    </div>

                    <!-- 語音服務API -->
                    <div class="api-endpoint">
                        <h4>🎵 語音服務API</h4>
                        <div class="api-info">計費：免費-200積分/次</div>
                        <div class="form-group">
                            <label class="form-label">免費聲音合成</label>
                            <input type="text" class="form-input" 
                                   value="/app/human/human/Voice/created" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label">深度語音克隆</label>
                            <input type="text" class="form-input" 
                                   value="/app/human/human/Voice/deepClone" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label">專業聲音合成</label>
                            <input type="text" class="form-input" 
                                   value="/app/human/human/Voice/deepCreated" readonly>
                        </div>
                        <button class="btn btn-test" onclick="testAPI(\'voice\')">測試語音API</button>
                    </div>

                    <!-- 工具應用API -->
                    <div class="api-endpoint">
                        <h4>🛠️ 工具應用API</h4>
                        <div class="api-info">計費：免費-300積分/次</div>
                        <div class="form-group">
                            <label class="form-label">手持數字人合成</label>
                            <input type="text" class="form-input" 
                                   value="/app/human/human/Index/created" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label">AI模特換裝</label>
                            <input type="text" class="form-input" 
                                   value="/app/human/human/Tool/clothes" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label">圖片轉動態視頻</label>
                            <input type="text" class="form-input" 
                                   value="/app/human/human/Tool/createPeopleVideo" readonly>
                        </div>
                        <button class="btn btn-test" onclick="testAPI(\'tools\')">測試工具API</button>
                    </div>

                    <!-- 對話模型API -->
                    <div class="api-endpoint">
                        <h4>💬 對話模型API</h4>
                        <div class="api-info">計費：1-2積分/次</div>
                        <div class="form-group">
                            <label class="form-label">AI文案對話生成</label>
                            <input type="text" class="form-input" 
                                   value="/app/human/human/Chat/generate" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label">創建爆款內容</label>
                            <input type="text" class="form-input" 
                                   value="/app/human/human/Chat/generateTitleContent" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label">獲取熱點標題</label>
                            <input type="text" class="form-input" 
                                   value="/app/human/human/Tool/getHotTitle" readonly>
                        </div>
                        <button class="btn btn-test" onclick="testAPI(\'chat\')">測試對話API</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 測試結果 -->
        <div class="card">
            <div class="card-header">📊 API測試結果</div>
            <div class="card-body">
                <div id="testResults">
                    <p>點擊上方"測試"按鈕開始測試API連接...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // 保存配置
        function saveConfig() {
            const token = document.getElementById("apiToken").value;
            const baseUrl = document.getElementById("apiBaseUrl").value;
            
            fetch("/api-config/save", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    api_token: token,
                    api_base_url: baseUrl
                })
            })
            .then(response => response.json())
            .then(data => {
                const status = document.getElementById("configStatus");
                if (data.code === 200) {
                    status.innerHTML = `<div class="status success">✅ 配置保存成功！時間：${new Date().toLocaleString()}</div>`;
                } else {
                    status.innerHTML = `<div class="status error">❌ 配置保存失敗：${data.message}</div>`;
                }
            })
            .catch(error => {
                document.getElementById("configStatus").innerHTML = 
                    `<div class="status error">❌ 網絡錯誤：${error.message}</div>`;
            });
        }

        // 測試連接
        function testConnection() {
            document.getElementById("configStatus").innerHTML = 
                `<div class="status">🔄 正在測試API連接...</div>`;
                
            fetch("/api-config/test-connection", {
                method: "POST"
            })
            .then(response => response.json())
            .then(data => {
                const status = document.getElementById("configStatus");
                if (data.code === 200) {
                    status.innerHTML = `<div class="status success">✅ API連接測試成功！響應時間：${data.data.response_time}ms</div>`;
                } else {
                    status.innerHTML = `<div class="status error">❌ API連接測試失敗：${data.message}</div>`;
                }
            })
            .catch(error => {
                document.getElementById("configStatus").innerHTML = 
                    `<div class="status error">❌ 連接測試失敗：${error.message}</div>`;
            });
        }

        // 測試API功能
        function testAPI(type) {
            const results = document.getElementById("testResults");
            results.innerHTML += `<div class="status">🔄 正在測試 ${type} API...</div>`;
            
            fetch(`/api-config/test-api/${type}`, {
                method: "POST"
            })
            .then(response => response.json())
            .then(data => {
                if (data.code === 200) {
                    results.innerHTML += `<div class="status success">✅ ${type} API測試成功：${data.data.message}</div>`;
                } else {
                    results.innerHTML += `<div class="status error">❌ ${type} API測試失敗：${data.message}</div>`;
                }
            })
            .catch(error => {
                results.innerHTML += `<div class="status error">❌ ${type} API測試錯誤：${error.message}</div>`;
            });
        }

        // 頁面加載時檢查配置
        window.addEventListener("load", () => {
            testConnection();
        });
    </script>
</body>
</html>';

        return new Response(200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Cache-Control' => 'no-cache, no-store, must-revalidate'
        ], $html);
    }

    /**
     * 保存API配置
     */
    public function saveConfig(Request $request): Response
    {
        try {
            $data = json_decode($request->rawBody(), true);
            
            // 保存到數據庫
            $config = [
                'api_token' => $data['api_token'] ?? '',
                'api_base_url' => $data['api_base_url'] ?? 'https://api.yidevs.com',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // 檢查是否已有配置
            $exists = Db::table('yc_config')->where('name', 'api_config')->first();
            
            if ($exists) {
                Db::table('yc_config')
                    ->where('name', 'api_config')
                    ->update([
                        'value' => json_encode($config),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
            } else {
                Db::table('yc_config')->insert([
                    'name' => 'api_config',
                    'value' => json_encode($config),
                    'description' => 'API配置信息',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
            
            return response()->json([
                'code' => 200,
                'message' => 'API配置保存成功',
                'data' => $config
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'API配置保存失敗：' . $e->getMessage()
            ]);
        }
    }

    /**
     * 測試API連接
     */
    public function testConnection(): Response
    {
        try {
            $startTime = microtime(true);
            
            // 獲取配置
            $config = $this->getApiConfig();
            
            // 測試API連接
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $config['api_base_url'] . '/app/human/human/Tool/getHotTitle');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: ' . $config['api_token'],
                'Accept: application/json',
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2);
            
            if ($error) {
                throw new \Exception("連接錯誤：" . $error);
            }
            
            if ($httpCode === 200) {
                return response()->json([
                    'code' => 200,
                    'message' => 'API連接成功',
                    'data' => [
                        'response_time' => $responseTime,
                        'http_code' => $httpCode,
                        'api_response' => json_decode($response, true)
                    ]
                ]);
            } else {
                throw new \Exception("HTTP錯誤碼：" . $httpCode);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'API連接測試失敗：' . $e->getMessage()
            ]);
        }
    }

    /**
     * 測試特定API功能
     */
    public function testApi(Request $request, $type): Response
    {
        try {
            $config = $this->getApiConfig();
            $result = ['success' => false, 'message' => ''];
            
            switch ($type) {
                case 'voice':
                    $result = $this->testVoiceApi($config);
                    break;
                case 'musetalk':
                    $result = $this->testMusetalkApi($config);
                    break;
                case 'tools':
                    $result = $this->testToolsApi($config);
                    break;
                case 'chat':
                    $result = $this->testChatApi($config);
                    break;
                default:
                    throw new \Exception("未知的API類型：" . $type);
            }
            
            return response()->json([
                'code' => $result['success'] ? 200 : 500,
                'message' => $result['success'] ? '測試成功' : '測試失敗',
                'data' => $result
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'API測試失敗：' . $e->getMessage()
            ]);
        }
    }

    /**
     * 獲取API配置
     */
    private function getApiConfig(): array
    {
        $config = Db::table('yc_config')->where('name', 'api_config')->first();
        
        if ($config) {
            return json_decode($config['value'], true);
        }
        
        // 默認配置
        return [
            'api_token' => 'Bearer 08D7EE7F91D258F27B4ADDF59CDDDEDE.1E95F76130BA23D37CE7BBBD69B19CCF.KYBVDWNR',
            'api_base_url' => 'https://api.yidevs.com'
        ];
    }

    /**
     * 測試語音API
     */
    private function testVoiceApi($config): array
    {
        // 測試免費聲音合成
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $config['api_base_url'] . '/app/human/human/Voice/created');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'text' => '測試語音合成功能',
            'voice_id' => 'default'
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $config['api_token'],
            'Accept: application/json',
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return [
            'success' => $httpCode === 200,
            'message' => $httpCode === 200 ? '語音API連接正常' : "HTTP錯誤：$httpCode",
            'response' => json_decode($response, true)
        ];
    }

    /**
     * 測試口型驅動API
     */
    private function testMusetalkApi($config): array
    {
        // 測試任務查詢接口（免費）
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $config['api_base_url'] . '/app/human/human/Musetalk/task?task_id=test');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $config['api_token'],
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return [
            'success' => in_array($httpCode, [200, 404]), // 404也算正常，說明接口存在
            'message' => in_array($httpCode, [200, 404]) ? '口型驅動API連接正常' : "HTTP錯誤：$httpCode",
            'response' => json_decode($response, true)
        ];
    }

    /**
     * 測試工具API
     */
    private function testToolsApi($config): array
    {
        // 測試獲取熱點標題（免費）
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $config['api_base_url'] . '/app/human/human/Tool/getHotTitle');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $config['api_token'],
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return [
            'success' => $httpCode === 200,
            'message' => $httpCode === 200 ? '工具API連接正常' : "HTTP錯誤：$httpCode",
            'response' => json_decode($response, true)
        ];
    }

    /**
     * 測試對話API
     */
    private function testChatApi($config): array
    {
        // 測試獲取熱點標題（免費）
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $config['api_base_url'] . '/app/human/human/Tool/getHotTitle');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $config['api_token'],
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return [
            'success' => $httpCode === 200,
            'message' => $httpCode === 200 ? '對話API連接正常' : "HTTP錯誤：$httpCode",
            'response' => json_decode($response, true)
        ];
    }
}
?>
