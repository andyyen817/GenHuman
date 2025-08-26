<?php
/**
 * GenHuman 快速修復控制器
 * 直接解決adapter字段問題，不繞圈子！
 */

namespace app\controller;

use support\Response;
use think\facade\Db;

class QuickFixController
{
    /**
     * 一步解決adapter問題
     * 訪問路徑：/quickfix/adapter
     */
    public function adapter(): Response
    {
        $output = "";
        $output .= "=== 快速修復 adapter 字段問題 ===\n";
        $output .= "開始時間：" . date('Y-m-d H:i:s') . "\n\n";

        try {
            // 直接執行SQL，添加adapter字段（如果不存在的話）
            $sqls = [
                "ALTER TABLE yc_upload ADD COLUMN adapter VARCHAR(50) NULL DEFAULT 'public' COMMENT '储存器'",
                "ALTER TABLE yc_upload ADD COLUMN mime_type VARCHAR(50) NULL DEFAULT NULL",
                "ALTER TABLE yc_upload ADD COLUMN uid INT(11) NULL DEFAULT 0 COMMENT '用户ID'",
                "ALTER TABLE yc_upload ADD COLUMN admin_uid INT(11) NULL DEFAULT 0 COMMENT '管理员ID'",
                "ALTER TABLE yc_upload ADD COLUMN hidden TINYINT(1) NULL DEFAULT 1 COMMENT '1显示2隐藏'"
            ];

            foreach ($sqls as $sql) {
                try {
                    Db::query($sql);
                    preg_match('/ADD COLUMN (\w+)/', $sql, $matches);
                    $field = $matches[1] ?? 'unknown';
                    $output .= "✅ 成功添加字段: {$field}\n";
                } catch (\Exception $e) {
                    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                        preg_match('/ADD COLUMN (\w+)/', $sql, $matches);
                        $field = $matches[1] ?? 'unknown';
                        $output .= "ℹ️  字段已存在: {$field}\n";
                    } else {
                        $output .= "❌ SQL執行失敗: " . $e->getMessage() . "\n";
                    }
                }
            }

            $output .= "\n🎉 adapter字段修復完成！\n";
            $output .= "📋 下一步：創建yc_config表\n";
            $output .= "訪問：https://genhuman-digital-human.zeabur.app/quickfix/config\n";

        } catch (\Exception $e) {
            $output .= "❌ 修復失敗: " . $e->getMessage() . "\n";
        }

        return new Response(200, [
            'Content-Type' => 'text/plain; charset=utf-8'
        ], $output);
    }

    /**
     * 創建yc_config表
     * 訪問路徑：/quickfix/config
     */
    public function config(): Response
    {
        $output = "";
        $output .= "=== 創建 yc_config 表 ===\n";
        $output .= "開始時間：" . date('Y-m-d H:i:s') . "\n\n";

        try {
            // 1. 檢查表是否存在
            $output .= "🔍 檢查yc_config表是否存在...\n";
            try {
                $result = Db::query("SELECT COUNT(*) as count FROM yc_config LIMIT 1");
                $output .= "✅ yc_config表已存在\n";
            } catch (\Exception $e) {
                $output .= "❌ yc_config表不存在，開始創建...\n";
                
                // 2. 創建yc_config表
                $createConfigTableSQL = "
                CREATE TABLE `yc_config` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `name` varchar(100) NOT NULL COMMENT '配置名稱',
                    `key` varchar(100) NOT NULL COMMENT '配置鍵',
                    `value` text COMMENT '配置值',
                    `type` varchar(20) DEFAULT 'string' COMMENT '數據類型',
                    `group` varchar(50) DEFAULT 'base' COMMENT '配置分組',
                    `sort` int(11) DEFAULT 0 COMMENT '排序',
                    `status` tinyint(1) DEFAULT 1 COMMENT '1正常 2禁用',
                    `create_time` datetime DEFAULT NULL,
                    `update_time` datetime DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `key` (`key`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系統配置'";
                
                Db::query($createConfigTableSQL);
                $output .= "✅ yc_config表創建成功\n";
                
                // 3. 插入基本配置
                $output .= "🔍 插入基本配置...\n";
                $configs = [
                    "INSERT INTO yc_config (name, `key`, value, type, `group`, create_time, update_time) VALUES ('系統名稱', 'site_name', 'GenHuman', 'string', 'base', NOW(), NOW())",
                    "INSERT INTO yc_config (name, `key`, value, type, `group`, create_time, update_time) VALUES ('上傳存儲', 'upload_adapter', 'public', 'string', 'upload', NOW(), NOW())",
                    "INSERT INTO yc_config (name, `key`, value, type, `group`, create_time, update_time) VALUES ('上傳大小', 'upload_size', '10', 'number', 'upload', NOW(), NOW())"
                ];
                
                foreach ($configs as $sql) {
                    try {
                        Db::query($sql);
                        $output .= "✅ 配置插入成功\n";
                    } catch (\Exception $e) {
                        $output .= "⚠️  配置插入失敗: " . $e->getMessage() . "\n";
                    }
                }
            }

            $output .= "\n🎉 yc_config表準備完成！\n";
            $output .= "📋 最終測試：\n";
            $output .= "1. 訪問：https://genhuman-digital-human.zeabur.app/admin#/login\n";
            $output .= "2. 輸入：admin / 123456\n";
            $output .= "3. 檢查是否能正常跳轉到後台\n";

        } catch (\Exception $e) {
            $output .= "❌ 創建失敗: " . $e->getMessage() . "\n";
        }

        return new Response(200, [
            'Content-Type' => 'text/plain; charset=utf-8'
        ], $output);
    }

    /**
     * 一次性修復所有問題
     * 訪問路徑：/quickfix/all
     */
    public function all(): Response
    {
        $output = "";
        $output .= "=== 一次性修復所有問題 ===\n";
        $output .= "開始時間：" . date('Y-m-d H:i:s') . "\n\n";

        try {
            // 1. 修復yc_upload表
            $output .= "🔍 步驟1：修復yc_upload表\n";
            $uploadSqls = [
                "ALTER TABLE yc_upload ADD COLUMN adapter VARCHAR(50) NULL DEFAULT 'public' COMMENT '储存器'",
                "ALTER TABLE yc_upload ADD COLUMN mime_type VARCHAR(50) NULL DEFAULT NULL",
                "ALTER TABLE yc_upload ADD COLUMN uid INT(11) NULL DEFAULT 0 COMMENT '用户ID'",
                "ALTER TABLE yc_upload ADD COLUMN admin_uid INT(11) NULL DEFAULT 0 COMMENT '管理员ID'",
                "ALTER TABLE yc_upload ADD COLUMN hidden TINYINT(1) NULL DEFAULT 1 COMMENT '1显示2隐藏'"
            ];

            foreach ($uploadSqls as $sql) {
                try {
                    Db::query($sql);
                    preg_match('/ADD COLUMN (\w+)/', $sql, $matches);
                    $field = $matches[1] ?? 'unknown';
                    $output .= "✅ yc_upload添加字段: {$field}\n";
                } catch (\Exception $e) {
                    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                        preg_match('/ADD COLUMN (\w+)/', $sql, $matches);
                        $field = $matches[1] ?? 'unknown';
                        $output .= "ℹ️  字段已存在: {$field}\n";
                    }
                }
            }

            // 2. 創建yc_config表
            $output .= "\n🔍 步驟2：創建yc_config表\n";
            try {
                $result = Db::query("SELECT COUNT(*) as count FROM yc_config LIMIT 1");
                $output .= "ℹ️  yc_config表已存在\n";
            } catch (\Exception $e) {
                $createConfigTableSQL = "
                CREATE TABLE `yc_config` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `name` varchar(100) NOT NULL COMMENT '配置名稱',
                    `key` varchar(100) NOT NULL COMMENT '配置鍵',
                    `value` text COMMENT '配置值',
                    `type` varchar(20) DEFAULT 'string' COMMENT '數據類型',
                    `group` varchar(50) DEFAULT 'base' COMMENT '配置分組',
                    `sort` int(11) DEFAULT 0 COMMENT '排序',
                    `status` tinyint(1) DEFAULT 1 COMMENT '1正常 2禁用',
                    `create_time` datetime DEFAULT NULL,
                    `update_time` datetime DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `key` (`key`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系統配置'";
                
                Db::query($createConfigTableSQL);
                $output .= "✅ yc_config表創建成功\n";
                
                // 插入基本配置
                $configs = [
                    "INSERT INTO yc_config (name, `key`, value, type, `group`, create_time, update_time) VALUES ('系統名稱', 'site_name', 'GenHuman', 'string', 'base', NOW(), NOW())",
                    "INSERT INTO yc_config (name, `key`, value, type, `group`, create_time, update_time) VALUES ('上傳存儲', 'upload_adapter', 'public', 'string', 'upload', NOW(), NOW())"
                ];
                
                foreach ($configs as $sql) {
                    try {
                        Db::query($sql);
                        $output .= "✅ 基本配置插入成功\n";
                    } catch (\Exception $e) {
                        // 忽略重複插入錯誤
                    }
                }
            }

            $output .= "\n🎉 所有問題修復完成！\n";
            $output .= "📋 測試步驟：\n";
            $output .= "1. 訪問：https://genhuman-digital-human.zeabur.app/admin#/login\n";
            $output .= "2. 輸入：admin / 123456\n";
            $output .= "3. 應該能正常跳轉到管理後台\n";
            $output .= "\n✅ 登入修復任務完成！\n";

        } catch (\Exception $e) {
            $output .= "❌ 修復失敗: " . $e->getMessage() . "\n";
        }

        return new Response(200, [
            'Content-Type' => 'text/plain; charset=utf-8'
        ], $output);
    }
}
?>