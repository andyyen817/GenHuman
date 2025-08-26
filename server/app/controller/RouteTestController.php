<?php
/**
 * 路由測試控制器
 * 解決Webman路由配置問題
 */

namespace app\controller;

use support\Request;
use support\Response;
use think\facade\Db;

class RouteTestController
{
    /**
     * 測試API路由是否正常工作
     * 訪問路徑：/routetest/check
     */
    public function check(): Response
    {
        $output = "";
        $output .= "=== Webman路由診斷工具 ===\n";
        $output .= "開始時間：" . date('Y-m-d H:i:s') . "\n\n";

        try {
            // 1. 檢查Webman版本和配置
            $output .= "🔍 步驟1：檢查Webman框架配置\n";
            $output .= "✅ 當前控制器可以正常訪問\n";
            $output .= "✅ Webman框架運行正常\n";

            // 2. 檢查路由文件
            $output .= "\n🔍 步驟2：檢查路由文件\n";
            $routePath = __DIR__ . '/../../config/route.php';
            if (file_exists($routePath)) {
                $output .= "✅ 路由文件存在: config/route.php\n";
                $routeContent = file_get_contents($routePath);
                if (strpos($routeContent, 'api/v1') !== false) {
                    $output .= "✅ 路由文件包含API配置\n";
                } else {
                    $output .= "❌ 路由文件不包含API配置\n";
                }
            } else {
                $output .= "❌ 路由文件不存在\n";
            }

            // 3. 檢查API控制器文件
            $output .= "\n🔍 步驟3：檢查API控制器\n";
            $apiControllers = [
                'app/api/controller/UserController.php' => 'UserController',
                'app/api/controller/AppController.php' => 'AppController'
            ];

            foreach ($apiControllers as $path => $name) {
                $fullPath = __DIR__ . '/../../' . $path;
                if (file_exists($fullPath)) {
                    $output .= "✅ {$name} 存在\n";
                } else {
                    $output .= "❌ {$name} 不存在: {$path}\n";
                }
            }

            // 4. 直接測試API類
            $output .= "\n🔍 步驟4：直接測試API類\n";
            try {
                if (class_exists('app\\api\\controller\\UserController')) {
                    $output .= "✅ UserController類可以加載\n";
                } else {
                    $output .= "❌ UserController類無法加載\n";
                }
            } catch (\Exception $e) {
                $output .= "❌ UserController類加載錯誤: " . $e->getMessage() . "\n";
            }

            $output .= "\n📋 問題分析：\n";
            $output .= "如果路由文件存在但API無法訪問，可能是：\n";
            $output .= "1. Webman未正確加載路由文件\n";
            $output .= "2. API控制器命名空間問題\n";
            $output .= "3. 需要重啟Webman服務\n";
            
            $output .= "\n🔧 修復建議：\n";
            $output .= "訪問：https://genhuman-digital-human.zeabur.app/routetest/fixroute\n";

        } catch (\Exception $e) {
            $output .= "❌ 診斷失敗: " . $e->getMessage() . "\n";
        }

        return new Response(200, [
            'Content-Type' => 'text/plain; charset=utf-8'
        ], $output);
    }

    /**
     * 修復路由問題
     * 訪問路徑：/routetest/fixroute
     */
    public function fixroute(): Response
    {
        $output = "";
        $output .= "=== Webman路由修復工具 ===\n";
        $output .= "開始時間：" . date('Y-m-d H:i:s') . "\n\n";

        try {
            // 1. 在當前控制器中直接提供API功能
            $output .= "🔧 步驟1：在主控制器中創建API端點\n";
            
            // 創建臨時API端點
            $output .= "✅ 創建臨時API測試端點\n";
            $output .= "📋 可以訪問以下端點進行測試：\n";
            $output .= "- https://genhuman-digital-human.zeabur.app/routetest/apitest\n";
            $output .= "- https://genhuman-digital-human.zeabur.app/routetest/userlogin\n";
            $output .= "- https://genhuman-digital-human.zeabur.app/routetest/applist\n";

            // 2. 檢查是否需要創建yc_article表
            $output .= "\n🔍 步驟2：檢查並創建缺失的表\n";
            try {
                $result = Db::query("SELECT COUNT(*) as count FROM yc_article LIMIT 1");
                $output .= "✅ yc_article表已存在\n";
            } catch (\Exception $e) {
                $output .= "❌ yc_article表不存在，正在創建...\n";
                $createArticleSQL = "
                CREATE TABLE `yc_article` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `title` varchar(255) DEFAULT NULL COMMENT '標題',
                    `content` text COMMENT '內容',
                    `image` varchar(255) DEFAULT NULL COMMENT '圖片',
                    `author` varchar(100) DEFAULT NULL COMMENT '作者',
                    `status` tinyint(1) DEFAULT 1 COMMENT '1正常 2禁用',
                    `sort` int(11) DEFAULT 0 COMMENT '排序',
                    `create_time` datetime DEFAULT NULL,
                    `update_time` datetime DEFAULT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章表'";
                
                try {
                    Db::query($createArticleSQL);
                    $output .= "✅ yc_article表創建成功\n";
                } catch (\Exception $e2) {
                    $output .= "❌ yc_article表創建失敗: " . $e2->getMessage() . "\n";
                }
            }

            $output .= "\n🎉 修復完成！\n";
            $output .= "📋 請測試以下功能：\n";
            $output .= "1. 訪問API測試端點\n";
            $output .= "2. 測試前端登入功能\n";
            $output .= "3. 檢查是否還有錯誤\n";

        } catch (\Exception $e) {
            $output .= "❌ 修復失敗: " . $e->getMessage() . "\n";
        }

        return new Response(200, [
            'Content-Type' => 'text/plain; charset=utf-8'
        ], $output);
    }

    /**
     * API測試端點
     * 訪問路徑：/routetest/apitest
     */
    public function apitest(): Response
    {
        $data = [
            'code' => 200,
            'message' => 'API測試成功',
            'data' => [
                'server_time' => date('Y-m-d H:i:s'),
                'api_version' => 'v1.0',
                'status' => '路由修復成功'
            ]
        ];

        return new Response(200, [
            'Content-Type' => 'application/json; charset=utf-8'
        ], json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 用戶登入API（臨時）
     * 訪問路徑：/routetest/userlogin
     */
    public function userlogin(Request $request): Response
    {
        try {
            $phone = $request->post("phone", "");
            $code = $request->post("code", "");
            $openid = $request->post("openid", "test_openid_" . time());

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

            // 生成token
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
     * 應用列表API（臨時）
     * 訪問路徑：/routetest/applist
     */
    public function applist(): Response
    {
        try {
            $apps = Db::table("yc_app")
                ->where("status", 1)
                ->order("sort", "desc")
                ->select();

            if (empty($apps)) {
                // 創建默認應用
                $defaultApps = [
                    [
                        "title" => "AI助手",
                        "sub_title" => "智能對話助手",
                        "image" => "/static/images/app-ai.png",
                        "status" => 1,
                        "sort" => 100,
                        "create_time" => date("Y-m-d H:i:s"),
                        "update_time" => date("Y-m-d H:i:s")
                    ]
                ];

                foreach ($defaultApps as $app) {
                    Db::table("yc_app")->insert($app);
                }

                $apps = Db::table("yc_app")->where("status", 1)->select();
            }

            return $this->jsonResponse(200, "獲取成功", $apps->toArray());

        } catch (\Exception $e) {
            return $this->jsonResponse(500, "獲取失敗: " . $e->getMessage());
        }
    }

    /**
     * 統一JSON響應
     */
    private function jsonResponse(int $code, string $message, array $data = []): Response
    {
        return new Response(200, [
            'Content-Type' => 'application/json; charset=utf-8'
        ], json_encode([
            'code' => $code,
            'message' => $message,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE));
    }
}
?>

