<?php
/**
 * 移動端登入控制器
 * 提供完整的前端登入界面和API
 */

namespace app\controller;

use support\Request;
use support\Response;
use think\facade\Db;

class MobileLoginController
{
    /**
     * 顯示登入頁面
     * 訪問路徑：/mobile/login
     */
    public function login(): Response
    {
        $html = '<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GenHuman - 用戶登入</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-container {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            position: relative;
            overflow: hidden;
        }
        .login-container::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo h1 {
            color: #333;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .logo p {
            color: #666;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }
        .form-control {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .login-btn, .guest-btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 15px;
        }
        .login-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .guest-btn {
            background: #f8f9fa;
            color: #333;
            border: 2px solid #e1e5e9;
        }
        .guest-btn:hover {
            background: #e9ecef;
            border-color: #adb5bd;
        }
        .loading {
            display: none;
            text-align: center;
            margin: 20px 0;
        }
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            display: none;
        }
        .result.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .result.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
            color: #adb5bd;
            font-size: 14px;
        }
        .divider::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e1e5e9;
            z-index: 1;
        }
        .divider span {
            background: white;
            padding: 0 15px;
            position: relative;
            z-index: 2;
        }
        .tips {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            font-size: 14px;
            color: #1565c0;
            line-height: 1.5;
        }
        .tips h4 {
            margin-bottom: 8px;
            color: #0d47a1;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>GenHuman</h1>
            <p>數位人智能系統</p>
        </div>
        
        <form id="loginForm">
            <div class="form-group">
                <label for="phone">手機號碼</label>
                <input type="tel" id="phone" class="form-control" placeholder="請輸入手機號碼">
            </div>
            
            <div class="form-group">
                <label for="code">驗證碼（可選）</label>
                <input type="text" id="code" class="form-control" placeholder="請輸入驗證碼（沒有可留空）">
            </div>
            
            <button type="submit" class="login-btn">手機號登入</button>
        </form>
        
        <div class="divider">
            <span>或</span>
        </div>
        
        <button type="button" class="guest-btn" onclick="guestLogin()">遊客登入（體驗模式）</button>
        
        <div class="loading">
            <div class="spinner"></div>
            <p>登入中，請稍候...</p>
        </div>
        
        <div class="result" id="result"></div>
        
        <div class="tips">
            <h4>💡 登入說明</h4>
            <p>• <strong>手機號登入</strong>：輸入手機號碼即可登入，驗證碼為可選項</p>
            <p>• <strong>遊客登入</strong>：無需註冊，直接體驗系統功能</p>
            <p>• 登入成功後將自動跳轉到主頁面</p>
        </div>
    </div>

    <script>
        const API_BASE = "";
        
        // 手機號登入
        document.getElementById("loginForm").addEventListener("submit", async (e) => {
            e.preventDefault();
            
            const phone = document.getElementById("phone").value.trim();
            const code = document.getElementById("code").value.trim();
            
            if (!phone) {
                showResult("請輸入手機號碼", "error");
                return;
            }
            
            showLoading(true);
            
            try {
                const response = await fetch("/routetest/userlogin", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: `phone=${encodeURIComponent(phone)}&code=${encodeURIComponent(code)}`
                });
                
                const data = await response.json();
                
                if (data.code === 200) {
                    // 保存用戶信息
                    localStorage.setItem("userToken", data.data.token);
                    localStorage.setItem("userInfo", JSON.stringify(data.data));
                    
                    showResult(`登入成功！歡迎 ${data.data.nickname}`, "success");
                    
                    // 3秒後跳轉
                    setTimeout(() => {
                        window.location.href = "/";
                    }, 2000);
                } else {
                    showResult(data.message || "登入失敗", "error");
                }
            } catch (error) {
                console.error("登入錯誤:", error);
                showResult("網絡錯誤，請重試", "error");
            } finally {
                showLoading(false);
            }
        });
        
        // 遊客登入
        async function guestLogin() {
            showLoading(true);
            
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
                    
                    showResult(`遊客登入成功！歡迎 ${data.data.nickname}`, "success");
                    
                    // 2秒後跳轉
                    setTimeout(() => {
                        window.location.href = "/";
                    }, 2000);
                } else {
                    showResult(data.message || "遊客登入失敗", "error");
                }
            } catch (error) {
                console.error("遊客登入錯誤:", error);
                showResult("網絡錯誤，請重試", "error");
            } finally {
                showLoading(false);
            }
        }
        
        function showLoading(show) {
            document.querySelector(".loading").style.display = show ? "block" : "none";
            document.querySelector("#loginForm button").disabled = show;
            document.querySelector(".guest-btn").disabled = show;
        }
        
        function showResult(message, type) {
            const result = document.getElementById("result");
            result.textContent = message;
            result.className = `result ${type}`;
            result.style.display = "block";
            
            if (type === "error") {
                setTimeout(() => {
                    result.style.display = "none";
                }, 5000);
            }
        }
        
        // 檢查是否已登入
        window.addEventListener("load", () => {
            const token = localStorage.getItem("userToken");
            if (token) {
                showResult("您已經登入，正在跳轉...", "success");
                setTimeout(() => {
                    window.location.href = "/";
                }, 1500);
            }
        });
    </script>
</body>
</html>';

        return new Response(200, [
            'Content-Type' => 'text/html; charset=utf-8'
        ], $html);
    }

    /**
     * 顯示註冊頁面
     * 訪問路徑：/mobile/register
     */
    public function register(): Response
    {
        $html = '<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GenHuman - 用戶註冊</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .register-container {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            position: relative;
            overflow: hidden;
        }
        .register-container::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo h1 {
            color: #333;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .logo p {
            color: #666;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }
        .form-control {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .register-btn, .login-btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 15px;
        }
        .register-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .register-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .login-btn {
            background: #f8f9fa;
            color: #333;
            border: 2px solid #e1e5e9;
        }
        .login-btn:hover {
            background: #e9ecef;
            border-color: #adb5bd;
        }
        .loading {
            display: none;
            text-align: center;
            margin: 20px 0;
        }
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            display: none;
        }
        .result.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .result.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
            color: #adb5bd;
            font-size: 14px;
        }
        .divider::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e1e5e9;
            z-index: 1;
        }
        .divider span {
            background: white;
            padding: 0 15px;
            position: relative;
            z-index: 2;
        }
        .tips {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            font-size: 14px;
            color: #1565c0;
            line-height: 1.5;
        }
        .tips h4 {
            margin-bottom: 8px;
            color: #0d47a1;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo">
            <h1>GenHuman</h1>
            <p>數位人智能系統 - 註冊</p>
        </div>
        
        <form id="registerForm">
            <div class="form-group">
                <label for="phone">手機號碼</label>
                <input type="tel" id="phone" class="form-control" placeholder="請輸入手機號碼" required>
            </div>
            
            <div class="form-group">
                <label for="nickname">暱稱</label>
                <input type="text" id="nickname" class="form-control" placeholder="請輸入暱稱（可選）">
            </div>
            
            <button type="submit" class="register-btn">立即註冊</button>
        </form>
        
        <div class="divider">
            <span>已有帳號？</span>
        </div>
        
        <button type="button" class="login-btn" onclick="window.location.href=\'/mobile/login\'">返回登入</button>
        
        <div class="loading">
            <div class="spinner"></div>
            <p>註冊中，請稍候...</p>
        </div>
        
        <div class="result" id="result"></div>
        
        <div class="tips">
            <h4>📝 註冊說明</h4>
            <p>• 只需要提供手機號碼即可完成註冊</p>
            <p>• 暱稱為可選項，系統會自動生成</p>
            <p>• 註冊成功後會自動登入</p>
        </div>
    </div>

    <script>
        // 註冊表單提交
        document.getElementById("registerForm").addEventListener("submit", async (e) => {
            e.preventDefault();
            
            const phone = document.getElementById("phone").value.trim();
            const nickname = document.getElementById("nickname").value.trim();
            
            if (!phone) {
                showResult("請輸入手機號碼", "error");
                return;
            }
            
            showLoading(true);
            
            try {
                const response = await fetch("/routetest/userlogin", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: `phone=${encodeURIComponent(phone)}&nickname=${encodeURIComponent(nickname)}`
                });
                
                const data = await response.json();
                
                if (data.code === 200) {
                    // 保存用戶信息
                    localStorage.setItem("userToken", data.data.token);
                    localStorage.setItem("userInfo", JSON.stringify(data.data));
                    
                    showResult(`註冊成功！歡迎 ${data.data.nickname}`, "success");
                    
                    // 3秒後跳轉
                    setTimeout(() => {
                        window.location.href = "/";
                    }, 2000);
                } else {
                    showResult(data.message || "註冊失敗", "error");
                }
            } catch (error) {
                console.error("註冊錯誤:", error);
                showResult("網絡錯誤，請重試", "error");
            } finally {
                showLoading(false);
            }
        });
        
        function showLoading(show) {
            document.querySelector(".loading").style.display = show ? "block" : "none";
            document.querySelector("#registerForm button").disabled = show;
        }
        
        function showResult(message, type) {
            const result = document.getElementById("result");
            result.textContent = message;
            result.className = `result ${type}`;
            result.style.display = "block";
            
            if (type === "error") {
                setTimeout(() => {
                    result.style.display = "none";
                }, 5000);
            }
        }
    </script>
</body>
</html>';

        return new Response(200, [
            'Content-Type' => 'text/html; charset=utf-8'
        ], $html);
    }
}
?>
