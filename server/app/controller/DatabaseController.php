<?php
/**
 * GenHuman 數據庫初始化控制器 v2.0
 * 用於初始化空數據庫，導入基礎數據
 * 遵循：最小修改原則，零風險策略
 */

namespace app\controller;

use support\Response;
use think\facade\Db;

class DatabaseController
{
    /**
     * 數據庫初始化工具
     * 訪問路徑：/database/init
     */
    public function init(): Response
    {
        $output = "";
        $output .= "=== GenHuman 數據庫初始化工具 v2.0 ===\n";
        $output .= "開始時間：" . date('Y-m-d H:i:s') . "\n";
        $output .= "警告：此操作將初始化數據庫，請確保這是一個空數據庫！\n\n";

        try {
            // 1. 檢查數據庫連接
            $output .= "🔍 步驟1：檢查數據庫連接\n";
            $result = Db::query('SELECT 1 as test');
            $output .= "✅ 數據庫連接成功\n\n";

            // 2. 檢查是否已有數據
            $output .= "🔍 步驟2：檢查現有數據\n";
            $tables = Db::query("SHOW TABLES");
            $output .= "當前數據庫表數量: " . count($tables) . "\n";
            
            if (count($tables) > 0) {
                $output .= "⚠️  數據庫不為空，包含以下表：\n";
                foreach ($tables as $table) {
                    $tableName = array_values((array)$table)[0];
                    $output .= "  - {$tableName}\n";
                }
                $output .= "\n";
            }

            // 3. 創建yc_admin表
            $output .= "🔍 步驟3：創建管理員表\n";
            $createAdminTableSQL = "
            CREATE TABLE IF NOT EXISTS `yc_admin` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `avatar` varchar(255) NOT NULL COMMENT '头像',
                `username` varchar(50) NOT NULL COMMENT '用户账号',
                `nickname` varchar(50) NOT NULL COMMENT '昵称',
                `password` varchar(100) NOT NULL COMMENT '密码',
                `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 正常  2禁用',
                `role_id` int(11) NOT NULL DEFAULT 0 COMMENT '角色ID',
                `description` varchar(255) NULL DEFAULT NULL,
                `is_system` tinyint(1) NULL DEFAULT 0 COMMENT '1 超级管理员',
                `create_time` datetime NOT NULL,
                `update_time` datetime NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '管理员'";
            
            Db::query($createAdminTableSQL);
            $output .= "✅ yc_admin 表創建成功\n";

            // 4. 創建yc_upload表（正確結構）
            $output .= "🔍 步驟4：創建文件上傳表\n";
            $createUploadTableSQL = "
            CREATE TABLE IF NOT EXISTS `yc_upload` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `title` varchar(100) NULL DEFAULT NULL COMMENT '文件名称',
                `url` varchar(255) NULL DEFAULT NULL COMMENT '文件地址',
                `size` varchar(50) NULL DEFAULT NULL COMMENT '文件大小',
                `md5` varchar(50) NULL DEFAULT NULL COMMENT '文件唯一标识',
                `ext` varchar(20) NULL DEFAULT NULL COMMENT '扩展名',
                `type` tinyint(1) NULL DEFAULT 1 COMMENT '1 图片  2音频  3视频 4文档  5其他',
                `adapter` varchar(50) NULL DEFAULT NULL COMMENT '储存器',
                `mime_type` varchar(50) NULL DEFAULT NULL,
                `uid` int(11) NULL DEFAULT 0 COMMENT '用户ID',
                `admin_uid` int(11) NULL DEFAULT 0 COMMENT '管理员ID',
                `hidden` tinyint(1) NULL DEFAULT 1 COMMENT '1 显示 2隐藏',
                `create_time` datetime NULL DEFAULT NULL,
                `update_time` datetime NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '文件上傳記錄'";
            
            Db::query($createUploadTableSQL);
            $output .= "✅ yc_upload 表創建成功\n";

            // 5. 創建其他必要表
            $output .= "🔍 步驟5：創建其他必要表\n";
            
            // 創建yc_menu表
            $createMenuTableSQL = "
            CREATE TABLE IF NOT EXISTS `yc_menu` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `pid` int(11) NOT NULL DEFAULT 0 COMMENT '父級ID',
                `title` varchar(100) NOT NULL COMMENT '菜單名稱',
                `name` varchar(100) NOT NULL COMMENT '路由名稱',
                `path` varchar(255) NULL DEFAULT NULL COMMENT '路由路徑',
                `component` varchar(255) NULL DEFAULT NULL COMMENT '組件路徑',
                `icon` varchar(100) NULL DEFAULT NULL COMMENT '圖標',
                `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1菜單 2按鈕',
                `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1正常 2禁用',
                `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
                `create_time` datetime NOT NULL,
                `update_time` datetime NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '菜單'";
            
            Db::query($createMenuTableSQL);
            $output .= "✅ yc_menu 表創建成功\n";

            // 創建yc_role表
            $createRoleTableSQL = "
            CREATE TABLE IF NOT EXISTS `yc_role` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(50) NOT NULL COMMENT '角色名稱',
                `description` varchar(255) NULL DEFAULT NULL COMMENT '角色描述',
                `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1正常 2禁用',
                `create_time` datetime NOT NULL,
                `update_time` datetime NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '角色'";
            
            Db::query($createRoleTableSQL);
            $output .= "✅ yc_role 表創建成功\n";

            // 6. 檢查是否已有管理員
            $existingAdmin = Db::query("SELECT id FROM yc_admin WHERE username = 'admin' LIMIT 1");
            if (empty($existingAdmin)) {
                // 插入默認管理員
                $output .= "🔍 步驟6：創建默認管理員\n";
                $insertAdminSQL = "
                INSERT INTO `yc_admin` 
                (`avatar`, `username`, `nickname`, `password`, `status`, `role_id`, `description`, `is_system`, `create_time`, `update_time`) 
                VALUES 
                ('static/logo.png', 'admin', '超级管理員', '\$2y\$10\$FrmlLB/OjKq/OhTI0f55Ve.LO3FLg1/905x.gO0lZJt3gvgbU9SsS', 1, 1, '系統管理員', 1, NOW(), NOW())";
                
                Db::query($insertAdminSQL);
                $output .= "✅ 默認管理員創建成功\n";
                $output .= "   用戶名: admin\n";
                $output .= "   密碼: 123456\n";
            } else {
                $output .= "ℹ️  管理員用戶已存在，跳過創建\n";
            }

            // 7. 插入默認角色
            $existingRole = Db::query("SELECT id FROM yc_role WHERE id = 1 LIMIT 1");
            if (empty($existingRole)) {
                $output .= "🔍 步驟7：創建默認角色\n";
                $insertRoleSQL = "
                INSERT INTO `yc_role` 
                (`id`, `name`, `description`, `status`, `create_time`, `update_time`) 
                VALUES 
                (1, '超級管理員', '系統最高權限', 1, NOW(), NOW())";
                
                Db::query($insertRoleSQL);
                $output .= "✅ 默認角色創建成功\n";
            } else {
                $output .= "ℹ️  默認角色已存在，跳過創建\n";
            }

            // 8. 驗證創建結果
            $output .= "\n🔍 步驟8：驗證創建結果\n";
            $admin = Db::query("SELECT id, username, nickname FROM yc_admin WHERE username = 'admin' LIMIT 1");
            if (!empty($admin)) {
                $adminData = $admin[0];
                $output .= "✅ 管理員驗證成功\n";
                $output .= "   ID: {$adminData->id}\n";
                $output .= "   用戶名: {$adminData->username}\n";
                $output .= "   昵稱: {$adminData->nickname}\n";
            }

            // 9. 測試密碼驗證
            $output .= "\n🔍 步驟9：測試密碼驗證\n";
            $adminWithPassword = Db::query("SELECT password FROM yc_admin WHERE username = 'admin' LIMIT 1");
            if (!empty($adminWithPassword)) {
                $storedPassword = $adminWithPassword[0]->password;
                if (password_verify('123456', $storedPassword)) {
                    $output .= "✅ 密碼驗證成功\n";
                } else {
                    $output .= "❌ 密碼驗證失敗\n";
                }
            }

            // 10. 檢查所有表
            $output .= "\n🔍 步驟10：最終表檢查\n";
            $finalTables = Db::query("SHOW TABLES");
            $output .= "數據庫表總數: " . count($finalTables) . "\n";
            foreach ($finalTables as $table) {
                $tableName = array_values((array)$table)[0];
                $output .= "✅ {$tableName}\n";
            }

            $output .= "\n=== 數據庫初始化完成 ===\n";
            $output .= "完成時間：" . date('Y-m-d H:i:s') . "\n";
            $output .= "\n📋 下一步：\n";
            $output .= "1. 訪問管理後台：https://genhuman-digital-human.zeabur.app/admin#/login\n";
            $output .= "2. 使用 admin / 123456 登入\n";
            $output .= "3. 管理後台應該能正常加載\n";

        } catch (\Exception $e) {
            $output .= "❌ 數據庫初始化失敗: " . $e->getMessage() . "\n";
            $output .= "錯誤詳情: " . $e->getTraceAsString() . "\n";
        }

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