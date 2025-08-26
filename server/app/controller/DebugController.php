<?php
/**
 * GenHuman 登入診斷控制器 v2.0
 * 通過Webman路由系統提供診斷功能
 * 遵循：最小修改原則，零風險策略
 */

namespace app\controller;

use support\Response;
use think\facade\Db;

class DebugController
{
    /**
     * 登入診斷工具
     * 訪問路徑：/debug/login
     */
    public function login(): Response
    {
        $output = "";
        $output .= "=== GenHuman 登入診斷工具 v2.0 ===\n";
        $output .= "開始時間：" . date('Y-m-d H:i:s') . "\n";
        $output .= "檢查範圍：數據庫連接、管理員用戶、密碼驗證\n\n";

        // 1. 檢查數據庫連接
        $output .= "🔍 步驟1：檢查數據庫連接\n";
        try {
            // 正確讀取think-orm配置
            $config = config('think-orm.connections.mysql');
            
            if (!$config) {
                $output .= "❌ 無法讀取數據庫配置\n";
                return $this->textResponse($output);
            }
            
            $output .= "數據庫配置信息：\n";
            $output .= "  主機: {$config['hostname']}\n";
            $output .= "  數據庫: {$config['database']}\n";
            $output .= "  用戶名: {$config['username']}\n";
            $output .= "  密碼: " . str_repeat('*', strlen($config['password'])) . "\n";
            $output .= "  前綴: {$config['prefix']}\n";
            
            // 測試數據庫連接
            $result = Db::query('SELECT 1 as test');
            $output .= "✅ 數據庫連接成功\n\n";
        } catch (\Exception $e) {
            $output .= "❌ 數據庫連接失敗: " . $e->getMessage() . "\n";
            $output .= "🚨 無法繼續診斷，請檢查數據庫配置\n";
            return $this->textResponse($output);
        }

        // 2. 檢查管理員用戶表
        $output .= "🔍 步驟2：檢查管理員用戶\n";
        try {
            // 檢查表是否存在
            $tables = Db::query("SHOW TABLES LIKE 'yc_admin'");
            if (empty($tables)) {
                $output .= "❌ yc_admin 表不存在\n";
                return $this->textResponse($output);
            }
            $output .= "✅ yc_admin 表存在\n";
            
            // 檢查管理員用戶
            $admin = Db::query("SELECT id, username, password, create_time FROM yc_admin WHERE username = 'admin' LIMIT 1");
            
            if (!empty($admin)) {
                $admin = $admin[0];
                $output .= "✅ 管理員用戶存在\n";
                $output .= "  用戶ID: {$admin->id}\n";
                $output .= "  用戶名: {$admin->username}\n";
                $output .= "  密碼Hash: " . substr($admin->password, 0, 30) . "...\n";
                $output .= "  創建時間: {$admin->create_time}\n";
                
                // 檢查密碼Hash格式
                if (strpos($admin->password, '$2y$') === 0) {
                    $output .= "✅ 密碼使用bcrypt加密\n";
                } else {
                    $output .= "⚠️  密碼加密格式可能不正確\n";
                }
                
                // 3. 測試密碼驗證
                $output .= "\n🔍 步驟3：測試密碼驗證\n";
                $testPassword = '123456';
                $output .= "測試密碼: {$testPassword}\n";

                if (password_verify($testPassword, $admin->password)) {
                    $output .= "✅ 密碼驗證成功！\n";
                    $output .= "🎯 診斷結果：密碼系統正常，問題可能在前端或API層\n";
                } else {
                    $output .= "❌ 密碼驗證失敗\n";
                    $output .= "🔧 嘗試生成新的密碼Hash...\n";
                    $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
                    $output .= "原Hash: {$admin->password}\n";
                    $output .= "新Hash: {$newHash}\n";
                    
                    // 測試新Hash是否能驗證
                    if (password_verify($testPassword, $newHash)) {
                        $output .= "✅ 新Hash驗證成功\n";
                        $output .= "🎯 診斷結果：需要重置管理員密碼\n";
                    } else {
                        $output .= "❌ 新Hash驗證也失敗，PHP密碼函數可能有問題\n";
                    }
                }
            } else {
                $output .= "❌ 管理員用戶不存在\n";
                
                // 檢查是否有其他用戶
                $count = Db::query("SELECT COUNT(*) as count FROM yc_admin");
                $userCount = $count[0]->count ?? 0;
                $output .= "  yc_admin表中共有 {$userCount} 個用戶\n";
                
                if ($userCount > 0) {
                    $users = Db::query("SELECT username FROM yc_admin LIMIT 5");
                    $output .= "  現有用戶名：";
                    foreach ($users as $user) {
                        $output .= $user->username . " ";
                    }
                    $output .= "\n";
                }
                return $this->textResponse($output);
            }
        } catch (\Exception $e) {
            $output .= "❌ 檢查管理員用戶失敗: " . $e->getMessage() . "\n";
            return $this->textResponse($output);
        }

        $output .= "\n";

        // 4. 檢查其他配置
        $output .= "🔍 步驟4：檢查其他配置\n";

        // 檢查PHP擴展
        $requiredExtensions = ['pdo', 'pdo_mysql', 'bcmath', 'mbstring'];
        foreach ($requiredExtensions as $ext) {
            if (extension_loaded($ext)) {
                $output .= "✅ {$ext} 擴展已加載\n";
            } else {
                $output .= "❌ {$ext} 擴展未加載\n";
            }
        }

        $output .= "\n";
        $output .= "=== 診斷完成 ===\n";
        $output .= "完成時間：" . date('Y-m-d H:i:s') . "\n";
        $output .= "\n";
        $output .= "📋 下一步建議：\n";
        if (isset($admin) && password_verify('123456', $admin->password)) {
            $output .= "1. 密碼驗證正常，檢查前端API調用邏輯\n";
            $output .= "2. 檢查管理後台的登入接口\n";
            $output .= "3. 檢查CORS跨域設置\n";
        } else {
            $output .= "1. 重置管理員密碼\n";
            $output .= "2. 重新測試登入功能\n";
        }
        $output .= "\n";

        return $this->textResponse($output);
    }

    /**
     * 返回純文本響應
     */
    private function textResponse(string $content): Response
    {
        return new Response(200, [
            'Content-Type' => 'text/plain; charset=utf-8'
        ], $content);
    }
}
?>
