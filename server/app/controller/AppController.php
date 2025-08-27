<?php
/**
 * 新的應用界面控制器
 * 完全繞過原有H5應用，創建乾淨的數位人應用
 */

namespace app\controller;

use support\Request;
use support\Response;
use think\facade\Db;

class AppController
{
    /**
     * 新的數位人應用主界面
     * 完全繞過原有H5，創建全新界面
     */
    public function main(): Response
    {
        $html = '<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GenHuman - 數位人系統</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }
        .header {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        .logo {
            color: white;
            font-size: 24px;
            font-weight: bold;
        }
        .user-info {
            color: white;
            font-size: 14px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .welcome-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin-bottom: 40px;
        }
        .welcome-card h1 {
            color: #333;
            font-size: 32px;
            margin-bottom: 10px;
        }
        .welcome-card p {
            color: #666;
            font-size: 18px;
            margin-bottom: 30px;
        }
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            cursor: pointer;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .feature-icon {
            font-size: 48px;
            margin-bottom: 20px;
        }
        .feature-card h3 {
            color: #333;
            font-size: 20px;
            margin-bottom: 15px;
        }
        .feature-card p {
            color: #666;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .logout-btn {
            background: #f44336;
            padding: 8px 20px;
            font-size: 14px;
        }
        .footer {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            padding: 20px;
            text-align: center;
            color: white;
            margin-top: 60px;
        }
        @media (max-width: 768px) {
            .features {
                grid-template-columns: 1fr;
            }
            .welcome-card {
                padding: 30px 20px;
            }
            .welcome-card h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">GenHuman</div>
        <div class="user-info">
            <span id="userWelcome">載入中...</span>
            <button class="btn logout-btn" onclick="logout()">登出</button>
        </div>
    </div>
    
    <div class="container">
        <div class="welcome-card">
            <h1>快速定制我的數字人視頻</h1>
            <p>AI驅動的數位人技術，讓您輕鬆創建專屬的虛擬形象</p>
            <button class="btn" onclick="startCreate()">立即開始創建</button>
        </div>
        
        <div class="features">
            <div class="feature-card" onclick="openFeature(\'voice\')">
                <div class="feature-icon">🎵</div>
                <h3>聲音克隆</h3>
                <p>AI語音合成技術，完美復制您的聲音特色，讓數位人擁有您的獨特音色</p>
            </div>
            
            <div class="feature-card" onclick="openFeature(\'appearance\')">
                <div class="feature-icon">🎭</div>
                <h3>形象克隆</h3>
                <p>高精度面部識別與建模，創建與您相似度極高的數位人形象</p>
            </div>
            
            <div class="feature-card" onclick="openFeature(\'video\')">
                <div class="feature-icon">🎬</div>
                <h3>視頻生成</h3>
                <p>一鍵生成專業級數位人視頻，支持多種場景和表情動作</p>
            </div>
            
            <div class="feature-card" onclick="openFeature(\'script\')">
                <div class="feature-icon">📝</div>
                <h3>腳本編輯</h3>
                <p>智能腳本生成與編輯工具，讓您的數位人說出更有趣的內容</p>
            </div>
            
            <div class="feature-card" onclick="openFeature(\'export\')">
                <div class="feature-icon">💾</div>
                <h3>導出分享</h3>
                <p>多格式視頻導出，支持社交媒體分享和商業應用</p>
            </div>
            
            <div class="feature-card" onclick="openFeature(\'template\')">
                <div class="feature-icon">🎨</div>
                <h3>模板庫</h3>
                <p>豐富的預設模板和場景，快速上手，輕鬆創作</p>
            </div>
            
            <div class="feature-card" onclick="openApiConfig()">
                <div class="feature-icon">⚙️</div>
                <h3>API配置</h3>
                <p>管理數位人API接口，配置易定開放平台服務</p>
            </div>
        </div>
    </div>
    
    <div class="footer">
        <p>&copy; 2025 GenHuman. 讓AI成為您的創作夥伴</p>
    </div>

    <script>
        // 加載用戶信息
        function loadUserInfo() {
            const userInfo = localStorage.getItem("userInfo");
            const token = localStorage.getItem("userToken");
            
            if (!userInfo || !token) {
                // 未登入，重定向到登入頁面
                window.location.href = "/";
                return;
            }
            
            try {
                const user = JSON.parse(userInfo);
                document.getElementById("userWelcome").textContent = 
                    `歡迎，${user.nickname || "用戶"}！`;
            } catch (e) {
                console.error("用戶信息解析錯誤:", e);
                document.getElementById("userWelcome").textContent = "歡迎！";
            }
        }
        
        // 開始創建
        function startCreate() {
            alert("創建功能開發中，敬請期待！\\n\\n這是一個全新的、無微信登入干擾的應用界面。");
        }
        
        // 打開功能
        function openFeature(feature) {
            const features = {
                voice: "聲音克隆",
                appearance: "形象克隆", 
                video: "視頻生成",
                script: "腳本編輯",
                export: "導出分享",
                template: "模板庫"
            };
            
            alert(`${features[feature]}功能開發中！\\n\\n當前版本重點解決登入問題，\\n所有創作功能將在下個版本推出。`);
        }
        
        // 打開API配置
        function openApiConfig() {
            window.open("/api-config", "_blank");
        }
        
        // 登出
        function logout() {
            if (confirm("確定要登出嗎？")) {
                localStorage.removeItem("userToken");
                localStorage.removeItem("userInfo");
                window.location.href = "/";
            }
        }
        
        // 頁面加載完成
        window.addEventListener("load", () => {
            loadUserInfo();
        });
    </script>
</body>
</html>';

        return new Response(200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ], $html);
    }
}
?>
