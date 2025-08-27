<?php
/**
 * 主頁面控制器
 * 智能檢測用戶登入狀態，自動跳轉到適當頁面
 */

namespace app\controller;

use support\Request;
use support\Response;

class IndexController
{
    /**
     * 主頁面訪問控制
     * 檢測登入狀態，智能跳轉
     */
    public function index(Request $request): Response
    {
        // 檢查是否有登入token
        $userAgent = $request->header('user-agent', '');
        $referer = $request->header('referer', '');
        
        // 創建智能跳轉頁面
        $html = '<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GenHuman - 正在加載...</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .loading-container {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .logo h1 {
            color: #333;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .status {
            color: #666;
            font-size: 16px;
            margin: 20px 0;
        }
        .login-options {
            display: none;
            margin-top: 30px;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-secondary {
            background: #f8f9fa;
            color: #333;
            border: 2px solid #e1e5e9;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .user-info {
            display: none;
            background: #e3f2fd;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .user-info h3 {
            color: #0d47a1;
            margin-bottom: 10px;
        }
        .user-info p {
            color: #1565c0;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="loading-container">
        <div class="logo">
            <h1>GenHuman</h1>
        </div>
        
        <div class="spinner" id="spinner"></div>
        <div class="status" id="status">正在檢測登入狀態...</div>
        
        <div class="user-info" id="userInfo">
            <h3>歡迎回來！</h3>
            <p id="userDetails"></p>
            <p>正在跳轉到主應用...</p>
        </div>
        
        <div class="login-options" id="loginOptions">
            <h3 style="color: #333; margin-bottom: 20px;">請選擇登入方式</h3>
            <a href="/mobile/login" class="btn btn-primary">手機號登入</a>
            <a href="/mobile/register" class="btn btn-secondary">註冊新帳號</a>
            <button onclick="guestLogin()" class="btn btn-secondary">遊客體驗</button>
        </div>
    </div>

    <script>
        let checkCount = 0;
        const maxChecks = 3;
        
        // 檢查登入狀態
        function checkLoginStatus() {
            checkCount++;
            
            // 檢查localStorage中的token
            const token = localStorage.getItem("userToken");
            const userInfo = localStorage.getItem("userInfo");
            
            if (token && userInfo) {
                try {
                    const user = JSON.parse(userInfo);
                    showLoggedIn(user);
                    // 3秒後跳轉到新的應用
                    setTimeout(() => {
                        window.location.href = "/app";
                    }, 3000);
                    return;
                } catch (e) {
                    console.error("用戶信息解析錯誤:", e);
                }
            }
            
            // 如果沒有登入信息，顯示登入選項
            if (checkCount >= maxChecks) {
                showLoginOptions();
            } else {
                // 繼續檢查
                setTimeout(checkLoginStatus, 1000);
                updateStatus(`檢測中... (${checkCount}/${maxChecks})`);
            }
        }
        
        function showLoggedIn(user) {
            document.getElementById("spinner").style.display = "none";
            document.getElementById("status").style.display = "none";
            
            const userInfo = document.getElementById("userInfo");
            const userDetails = document.getElementById("userDetails");
            
            userDetails.innerHTML = `
                暱稱：${user.nickname || "未設置"}<br>
                手機：${user.phone || "未設置"}<br>
                登入時間：${new Date().toLocaleString()}
            `;
            
            userInfo.style.display = "block";
        }
        
        function showLoginOptions() {
            document.getElementById("spinner").style.display = "none";
            document.getElementById("status").style.display = "none";
            document.getElementById("loginOptions").style.display = "block";
        }
        
        function updateStatus(message) {
            document.getElementById("status").textContent = message;
        }
        
        // 遊客登入
        async function guestLogin() {
            updateStatus("正在創建遊客帳號...");
            
            const guestOpenid = "guest_" + Date.now() + "_" + Math.random().toString(36).substr(2, 9);
            
            try {
                const response = await fetch("/routetest/userlogin", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: `openid=${encodeURIComponent(guestOpenid)}`
                });
                
                const data = await response.json();
                
                if (data.code === 200) {
                    // 保存用戶信息
                    localStorage.setItem("userToken", data.data.token);
                    localStorage.setItem("userInfo", JSON.stringify(data.data));
                    
                    showLoggedIn(data.data);
                    
                    // 2秒後跳轉
                    setTimeout(() => {
                        window.location.href = "/app";
                    }, 2000);
                } else {
                    updateStatus("遊客登入失敗，請重試");
                }
            } catch (error) {
                console.error("遊客登入錯誤:", error);
                updateStatus("網絡錯誤，請重試");
            }
        }
        
        // 頁面加載完成後開始檢查
        window.addEventListener("load", () => {
            setTimeout(checkLoginStatus, 500);
        });
    </script>
</body>
</html>';

        return new Response(200, [
            'Content-Type' => 'text/html; charset=utf-8'
        ], $html);
    }
}
?>