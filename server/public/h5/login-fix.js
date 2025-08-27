/**
 * H5登入修復腳本
 * 檢測新的登入token，如果已登入則隱藏微信登入界面
 */
(function() {
    'use strict';
    
    console.log('🔧 H5登入修復腳本啟動');
    
    // 檢查新的登入token
    function checkNewLoginToken() {
        const token = localStorage.getItem('userToken');
        const userInfo = localStorage.getItem('userInfo');
        
        if (token && userInfo) {
            console.log('✅ 檢測到新的登入token，用戶已登入');
            hideWechatLogin();
            showUserInfo();
            return true;
        }
        
        console.log('❌ 未檢測到登入token');
        return false;
    }
    
    // 隱藏微信登入相關元素
    function hideWechatLogin() {
        console.log('🚫 隱藏微信登入界面');
        
        // 隱藏微信登入按鈕
        const wechatButtons = document.querySelectorAll('[class*="weixin"], [class*="wechat"], [class*="微信"]');
        wechatButtons.forEach(btn => {
            if (btn) {
                btn.style.display = 'none';
                console.log('隱藏微信按鈕:', btn);
            }
        });
        
        // 隱藏包含"微信"文字的元素
        const allElements = document.querySelectorAll('*');
        allElements.forEach(el => {
            if (el.textContent && el.textContent.includes('微信')) {
                // 檢查是否是登入相關的微信元素
                if (el.textContent.includes('登入') || el.textContent.includes('登录') || el.textContent.includes('授权')) {
                    el.style.display = 'none';
                    console.log('隱藏微信登入元素:', el);
                }
            }
        });
        
        // 隱藏"一鍵登入/註冊"按鈕
        const loginButtons = document.querySelectorAll('button, [class*="button"], [class*="btn"]');
        loginButtons.forEach(btn => {
            if (btn.textContent && (
                btn.textContent.includes('一鍵登入') || 
                btn.textContent.includes('一键登录') || 
                btn.textContent.includes('注册') ||
                btn.textContent.includes('註冊')
            )) {
                btn.style.display = 'none';
                console.log('隱藏登入按鈕:', btn);
            }
        });
        
        // 特別處理：隱藏底部的登入按鈕區域
        const bottomLoginArea = document.querySelector('.uni-tabbar, [class*="login"], [class*="auth"]');
        if (bottomLoginArea) {
            const loginElements = bottomLoginArea.querySelectorAll('*');
            loginElements.forEach(el => {
                if (el.textContent && el.textContent.includes('一鍵')) {
                    el.style.display = 'none';
                }
            });
        }
    }
    
    // 顯示用戶信息
    function showUserInfo() {
        try {
            const userInfo = JSON.parse(localStorage.getItem('userInfo'));
            console.log('👤 用戶信息:', userInfo);
            
            // 在頁面頂部顯示歡迎信息
            const welcomeDiv = document.createElement('div');
            welcomeDiv.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 10px;
                text-align: center;
                font-size: 14px;
                z-index: 9999;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            `;
            
            welcomeDiv.innerHTML = `
                <div>歡迎，${userInfo.nickname || '用戶'}！</div>
                <div style="font-size: 12px; opacity: 0.9;">已成功登入 GenHuman 系統</div>
            `;
            
            document.body.insertBefore(welcomeDiv, document.body.firstChild);
            
            // 3秒後隱藏歡迎信息
            setTimeout(() => {
                welcomeDiv.style.transition = 'transform 0.5s ease';
                welcomeDiv.style.transform = 'translateY(-100%)';
                setTimeout(() => welcomeDiv.remove(), 500);
            }, 3000);
            
        } catch (e) {
            console.error('顯示用戶信息錯誤:', e);
        }
    }
    
    // 修復undefined API調用
    function fixUndefinedAPI() {
        // 攔截原有的API調用
        const originalFetch = window.fetch;
        window.fetch = function(...args) {
            let url = args[0];
            
            // 修復undefined URL
            if (typeof url === 'string' && url.includes('/undefined')) {
                console.log('🔧 修復undefined API調用:', url);
                // 重定向到我們的API
                url = url.replace('/undefined', '/routetest/apitest');
                args[0] = url;
            }
            
            return originalFetch.apply(this, args);
        };
        
        // 攔截XMLHttpRequest
        const originalXHROpen = XMLHttpRequest.prototype.open;
        XMLHttpRequest.prototype.open = function(method, url, ...rest) {
            if (typeof url === 'string' && url.includes('/undefined')) {
                console.log('🔧 修復XHR undefined調用:', url);
                url = url.replace('/undefined', '/routetest/apitest');
            }
            return originalXHROpen.call(this, method, url, ...rest);
        };
    }
    
    // 主執行函數
    function init() {
        console.log('🚀 H5登入修復初始化');
        
        // 立即檢查
        if (checkNewLoginToken()) {
            return; // 已登入，不需要進一步處理
        }
        
        // 修復API調用
        fixUndefinedAPI();
        
        // 監聽DOM變化，處理動態加載的登入界面
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.addedNodes.length > 0) {
                    // 重新檢查登入狀態
                    if (checkNewLoginToken()) {
                        observer.disconnect();
                        return;
                    }
                }
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
        
        // 定期檢查登入狀態
        const checkInterval = setInterval(() => {
            if (checkNewLoginToken()) {
                clearInterval(checkInterval);
                observer.disconnect();
            }
        }, 1000);
        
        // 5分鐘後停止檢查
        setTimeout(() => {
            clearInterval(checkInterval);
            observer.disconnect();
        }, 300000);
    }
    
    // 等待DOM加載完成
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        // DOM已經加載完成，延遲執行以確保其他腳本已運行
        setTimeout(init, 100);
    }
    
})();
