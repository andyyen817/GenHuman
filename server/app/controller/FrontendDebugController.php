<?php
/**
 * 前端API診斷控制器
 * 專門診斷前端登入註冊API問題
 */

namespace app\controller;

use support\Response;
use think\facade\Db;

class FrontendDebugController
{
    /**
     * 檢查前端API端點配置
     * 訪問路徑：/frontend/checkapi
     */
    public function checkapi(): Response
    {
        $output = "";
        $output .= "=== 前端API端點診斷 ===\n";
        $output .= "開始時間：" . date('Y-m-d H:i:s') . "\n\n";

        try {
            // 1. 檢查用戶API路由是否存在
            $output .= "🔍 步驟1：檢查API路由配置\n";
            
            // 檢查主要的API端點
            $apiEndpoints = [
                '/api/v1/user/login' => '用戶登入',
                '/api/v1/user/register' => '用戶註冊', 
                '/api/v1/user/info' => '獲取用戶信息',
                '/api/v1/app/list' => '獲取應用列表',
                '/api/v1/scene/list' => '獲取場景列表'
            ];

            foreach ($apiEndpoints as $endpoint => $description) {
                $output .= "📋 {$description}: {$endpoint}\n";
            }

            // 2. 檢查路由文件
            $output .= "\n🔍 步驟2：檢查路由文件配置\n";
            $routeFiles = [
                'config/route.php' => '主路由配置',
                'app/api/controller/UserController.php' => '用戶控制器',
                'app/api/controller/AppController.php' => '應用控制器'
            ];

            foreach ($routeFiles as $file => $description) {
                $filePath = __DIR__ . '/../../' . $file;
                if (file_exists($filePath)) {
                    $output .= "✅ {$description} 存在\n";
                } else {
                    $output .= "❌ {$description} 不存在: {$file}\n";
                }
            }

            // 3. 檢查資料庫連接
            $output .= "\n🔍 步驟3：檢查資料庫連接\n";
            try {
                $result = Db::query("SELECT COUNT(*) as count FROM yc_user");
                $userCount = $result[0]->count ?? 0;
                $output .= "✅ 資料庫連接正常，用戶表記錄數：{$userCount}\n";
            } catch (\Exception $e) {
                $output .= "❌ 資料庫連接失敗: " . $e->getMessage() . "\n";
            }

            $output .= "\n📋 下一步：創建缺失的API控制器\n";
            $output .= "訪問：https://genhuman-digital-human.zeabur.app/frontend/createapi\n";

        } catch (\Exception $e) {
            $output .= "❌ 診斷失敗: " . $e->getMessage() . "\n";
        }

        return new Response(200, [
            'Content-Type' => 'text/plain; charset=utf-8'
        ], $output);
    }

    /**
     * 創建前端需要的API控制器
     * 訪問路徑：/frontend/createapi
     */
    public function createapi(): Response
    {
        $output = "";
        $output .= "=== 創建前端API控制器 ===\n";
        $output .= "開始時間：" . date('Y-m-d H:i:s') . "\n\n";

        try {
            // 1. 確保api目錄存在
            $apiDir = __DIR__ . '/../api/controller';
            if (!is_dir($apiDir)) {
                mkdir($apiDir, 0755, true);
                $output .= "✅ 創建API控制器目錄\n";
            } else {
                $output .= "ℹ️  API控制器目錄已存在\n";
            }

            // 2. 創建UserController
            $userControllerPath = $apiDir . '/UserController.php';
            if (!file_exists($userControllerPath)) {
                $userControllerCode = $this->getUserControllerCode();
                file_put_contents($userControllerPath, $userControllerCode);
                $output .= "✅ 創建UserController成功\n";
            } else {
                $output .= "ℹ️  UserController已存在\n";
            }

            // 3. 創建AppController  
            $appControllerPath = $apiDir . '/AppController.php';
            if (!file_exists($appControllerPath)) {
                $appControllerCode = $this->getAppControllerCode();
                file_put_contents($appControllerPath, $appControllerCode);
                $output .= "✅ 創建AppController成功\n";
            } else {
                $output .= "ℹ️  AppController已存在\n";
            }

            // 4. 檢查路由配置
            $output .= "\n🔍 檢查路由配置...\n";
            $routePath = __DIR__ . '/../../config/route.php';
            if (file_exists($routePath)) {
                $output .= "✅ 路由配置文件存在\n";
            } else {
                $output .= "❌ 路由配置文件不存在，需要創建\n";
            }

            $output .= "\n🎉 API控制器創建完成！\n";
            $output .= "📋 測試API端點：\n";
            $output .= "1. https://genhuman-digital-human.zeabur.app/api/v1/user/test\n";
            $output .= "2. https://genhuman-digital-human.zeabur.app/api/v1/app/list\n";
            $output .= "3. 然後測試前端登入功能\n";

        } catch (\Exception $e) {
            $output .= "❌ 創建失敗: " . $e->getMessage() . "\n";
        }

        return new Response(200, [
            'Content-Type' => 'text/plain; charset=utf-8'
        ], $output);
    }

    /**
     * 測試API響應格式
     * 訪問路徑：/frontend/testapi
     */
    public function testapi(): Response
    {
        $output = "";
        $output .= "=== API響應格式測試 ===\n";
        $output .= "開始時間：" . date('Y-m-d H:i:s') . "\n\n";

        // 測試標準API響應格式
        $testData = [
            'code' => 200,
            'message' => 'success',
            'data' => [
                'user_id' => 1,
                'nickname' => '測試用戶',
                'avatar' => '/static/images/default-avatar.png',
                'token' => 'test_token_123456789'
            ]
        ];

        $output .= "📋 標準API響應格式：\n";
        $output .= json_encode($testData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

        $output .= "🔍 前端應該檢查的錯誤：\n";
        $output .= "1. API端點URL是否正確\n";
        $output .= "2. 請求方法（GET/POST）是否匹配\n";
        $output .= "3. 請求頭是否包含必要的Content-Type\n";
        $output .= "4. 響應數據處理是否正確\n";

        // 直接返回JSON格式用於前端測試
        return new Response(200, [
            'Content-Type' => 'application/json; charset=utf-8'
        ], json_encode($testData, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 獲取UserController代碼
     */
    private function getUserControllerCode(): string
    {
        return '<?php
/**
 * 前端用戶API控制器
 */

namespace app\api\controller;

use support\Request;
use support\Response;
use think\facade\Db;

class UserController
{
    /**
     * 用戶登入
     */
    public function login(Request $request): Response
    {
        try {
            $phone = $request->post("phone", "");
            $code = $request->post("code", "");
            $openid = $request->post("openid", "");

            // 簡化登入邏輯 - 直接通過openid或phone登入
            if (empty($openid) && empty($phone)) {
                return $this->jsonResponse(400, "缺少登入參數");
            }

            // 查找或創建用戶
            $where = [];
            if (!empty($openid)) {
                $where["openid"] = $openid;
            } else {
                $where["phone"] = $phone;
            }

            $user = Db::table("yc_user")->where($where)->find();
            
            if (!$user) {
                // 創建新用戶
                $userData = [
                    "openid" => $openid,
                    "phone" => $phone,
                    "nickname" => "用戶" . substr($phone ?: $openid, -4),
                    "avatar" => "/static/images/default-avatar.png",
                    "status" => 1,
                    "register_type" => $openid ? "wechat" : "phone",
                    "create_time" => date("Y-m-d H:i:s"),
                    "update_time" => date("Y-m-d H:i:s")
                ];
                
                $userId = Db::table("yc_user")->insertGetId($userData);
                $user = array_merge($userData, ["id" => $userId]);
            }

            // 更新最後登入時間
            Db::table("yc_user")->where("id", $user["id"])->update([
                "last_login_time" => date("Y-m-d H:i:s"),
                "update_time" => date("Y-m-d H:i:s")
            ]);

            // 生成token（簡化版）
            $token = "genhuman_" . $user["id"] . "_" . time();

            return $this->jsonResponse(200, "登入成功", [
                "user_id" => $user["id"],
                "nickname" => $user["nickname"],
                "avatar" => $user["avatar"],
                "phone" => $user["phone"],
                "token" => $token
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse(500, "登入失敗: " . $e->getMessage());
        }
    }

    /**
     * 獲取用戶信息
     */
    public function info(Request $request): Response
    {
        try {
            $userId = $request->get("user_id", 0);
            
            if (empty($userId)) {
                return $this->jsonResponse(400, "缺少用戶ID");
            }

            $user = Db::table("yc_user")->where("id", $userId)->find();
            
            if (!$user) {
                return $this->jsonResponse(404, "用戶不存在");
            }

            return $this->jsonResponse(200, "獲取成功", [
                "user_id" => $user["id"],
                "nickname" => $user["nickname"],
                "avatar" => $user["avatar"],
                "phone" => $user["phone"]
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse(500, "獲取失敗: " . $e->getMessage());
        }
    }

    /**
     * 測試API端點
     */
    public function test(): Response
    {
        return $this->jsonResponse(200, "API端點正常", [
            "server_time" => date("Y-m-d H:i:s"),
            "api_version" => "v1.0"
        ]);
    }

    /**
     * 統一JSON響應格式
     */
    private function jsonResponse(int $code, string $message, array $data = []): Response
    {
        return new Response(200, [
            "Content-Type" => "application/json; charset=utf-8"
        ], json_encode([
            "code" => $code,
            "message" => $message,
            "data" => $data
        ], JSON_UNESCAPED_UNICODE));
    }
}
?>';
    }

    /**
     * 獲取AppController代碼
     */
    private function getAppControllerCode(): string
    {
        return '<?php
/**
 * 應用API控制器
 */

namespace app\api\controller;

use support\Request;
use support\Response;
use think\facade\Db;

class AppController
{
    /**
     * 獲取應用列表
     */
    public function list(Request $request): Response
    {
        try {
            $apps = Db::table("yc_app")
                ->where("status", 1)
                ->order("sort", "desc")
                ->order("id", "desc")
                ->select();

            // 如果沒有應用，創建默認應用
            if (empty($apps)) {
                $defaultApps = [
                    [
                        "title" => "AI助手",
                        "sub_title" => "智能對話助手",
                        "image" => "/static/images/app-ai.png",
                        "status" => 1,
                        "sort" => 100,
                        "create_time" => date("Y-m-d H:i:s"),
                        "update_time" => date("Y-m-d H:i:s")
                    ],
                    [
                        "title" => "數字人",
                        "sub_title" => "虛擬數字人形象",
                        "image" => "/static/images/app-avatar.png",
                        "status" => 1,
                        "sort" => 90,
                        "create_time" => date("Y-m-d H:i:s"),
                        "update_time" => date("Y-m-d H:i:s")
                    ]
                ];

                foreach ($defaultApps as $app) {
                    Db::table("yc_app")->insert($app);
                }

                $apps = Db::table("yc_app")
                    ->where("status", 1)
                    ->order("sort", "desc")
                    ->select();
            }

            return $this->jsonResponse(200, "獲取成功", $apps);

        } catch (\Exception $e) {
            return $this->jsonResponse(500, "獲取失敗: " . $e->getMessage());
        }
    }

    /**
     * 統一JSON響應格式
     */
    private function jsonResponse(int $code, string $message, array $data = []): Response
    {
        return new Response(200, [
            "Content-Type" => "application/json; charset=utf-8"
        ], json_encode([
            "code" => $code,
            "message" => $message,
            "data" => $data
        ], JSON_UNESCAPED_UNICODE));
    }
}
?>';
    }
}
?>

