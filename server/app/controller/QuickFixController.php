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

            // 驗證修復結果
            $output .= "\n🔍 驗證修復結果：\n";
            $columns = Db::query("SHOW COLUMNS FROM yc_upload");
            $hasAdapter = false;
            foreach ($columns as $column) {
                if ($column->Field === 'adapter') {
                    $hasAdapter = true;
                    $output .= "✅ adapter字段已存在！\n";
                    break;
                }
            }

            if (!$hasAdapter) {
                $output .= "❌ adapter字段仍然不存在\n";
            } else {
                $output .= "\n🎉 修復完成！現在可以測試管理後台了\n";
                $output .= "📋 測試步驟：\n";
                $output .= "1. 訪問：https://genhuman-digital-human.zeabur.app/admin#/login\n";
                $output .= "2. 輸入：admin / 123456\n";
                $output .= "3. 檢查是否能正常跳轉\n";
            }

        } catch (\Exception $e) {
            $output .= "❌ 修復失敗: " . $e->getMessage() . "\n";
        }

        return new Response(200, [
            'Content-Type' => 'text/plain; charset=utf-8'
        ], $output);
    }
}
?>
