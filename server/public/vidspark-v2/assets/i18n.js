/**
 * Vidspark i18n JavaScript庫
 * 與獨立版管理後台共享數據
 */

class VidsparkI18n {
    constructor() {
        this.currentLanguage = 'zh-TW'; // 默認繁體中文
        this.translations = {};
        this.init();
    }

    // 初始化
    init() {
        console.log(`[${new Date().toLocaleTimeString()}] 🌐 初始化Vidspark i18n系統開始`);
        try {
            this.loadTranslations();
            console.log(`[${new Date().toLocaleTimeString()}] ✅ 翻譯數據載入完成`);
            
            this.detectLanguage();
            console.log(`[${new Date().toLocaleTimeString()}] ✅ 語言檢測完成: ${this.currentLanguage}`);
            
            this.createLanguageSwitcher();
            console.log(`[${new Date().toLocaleTimeString()}] ✅ 語言切換器創建完成`);
            
            this.applyTranslations();
            console.log(`[${new Date().toLocaleTimeString()}] ✅ 翻譯應用完成`);
            
            console.log(`[${new Date().toLocaleTimeString()}] 🎉 Vidspark i18n系統初始化全部完成`);
        } catch (error) {
            console.error(`[${new Date().toLocaleTimeString()}] ❌ i18n初始化過程中發生錯誤:`, error);
        }
    }

    // 從localStorage載入翻譯數據（與管理後台共享）
    loadTranslations() {
        const stored = localStorage.getItem('vidspark-i18n-dictionary');
        if (stored) {
            this.translations = JSON.parse(stored);
            console.log(`[${new Date().toLocaleTimeString()}] ✅ 載入翻譯數據: ${Object.keys(this.translations).length} 個字段`);
        } else {
            console.warn(`[${new Date().toLocaleTimeString()}] ⚠️ 未找到翻譯數據，請先在管理後台設置翻譯`);
        }
    }

    // 檢測當前語言（從localStorage或瀏覽器設置）
    detectLanguage() {
        const savedLang = localStorage.getItem('vidspark-current-language');
        if (savedLang) {
            this.currentLanguage = savedLang;
        } else {
            // 根據瀏覽器語言自動檢測
            const browserLang = navigator.language || navigator.userLanguage;
            if (browserLang.startsWith('zh')) {
                this.currentLanguage = browserLang.includes('CN') ? 'zh-CN' : 'zh-TW';
            } else {
                this.currentLanguage = 'en';
            }
        }
        console.log(`[${new Date().toLocaleTimeString()}] 🌍 當前語言: ${this.currentLanguage}`);
    }

    // 獲取語言標籤的翻譯
    getLanguageLabel() {
        const labels = {
            'zh-TW': '語言',
            'zh-CN': '语言', 
            'en': 'Lg.'  // 按用戶要求使用Lg.縮寫
        };
        return labels[this.currentLanguage] || '語言';
    }

    // 創建語言切換器
    createLanguageSwitcher() {
        // 檢查是否已存在語言切換器
        if (document.getElementById('vidspark-language-switcher')) {
            return;
        }

        // 嘗試插入到導航欄中間
        const navRightSection = document.querySelector('nav.space-x-4, nav .flex.items-center.space-x-4');
        if (navRightSection) {
            // 創建導航欄內的語言切換器
            const switcher = document.createElement('div');
            switcher.id = 'vidspark-language-switcher';
            switcher.className = 'flex items-center space-x-2';
            switcher.innerHTML = `
                <span class="text-sm text-gray-600" id="language-label">${this.getLanguageLabel()}:</span>
                <select id="language-select" class="bg-gray-50 border border-gray-200 text-gray-600 text-sm rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="zh-TW" ${this.currentLanguage === 'zh-TW' ? 'selected' : ''}>繁體中文</option>
                    <option value="zh-CN" ${this.currentLanguage === 'zh-CN' ? 'selected' : ''}>简体中文</option>
                    <option value="en" ${this.currentLanguage === 'en' ? 'selected' : ''}>English</option>
                </select>
            `;
            
            // 插入到登入按鈕之前
            const loginLink = navRightSection.querySelector('a[href="login.html"]');
            if (loginLink) {
                navRightSection.insertBefore(switcher, loginLink);
            } else {
                navRightSection.appendChild(switcher);
            }
        } else {
            // 檢查是否是Login/Register頁面（在Logo旁邊）
            const logoSection = document.querySelector('.flex.items-center h1');
            if (logoSection && (window.location.pathname.includes('login.html') || window.location.pathname.includes('register.html'))) {
                const switcher = document.createElement('div');
                switcher.id = 'vidspark-language-switcher';
                switcher.className = 'flex items-center space-x-2 ml-8';
                switcher.innerHTML = `
                    <span class="text-sm text-gray-600" id="language-label">${this.getLanguageLabel()}:</span>
                    <select id="language-select" class="bg-gray-50 border border-gray-200 text-gray-600 text-sm rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="zh-TW" ${this.currentLanguage === 'zh-TW' ? 'selected' : ''}>繁體中文</option>
                        <option value="zh-CN" ${this.currentLanguage === 'zh-CN' ? 'selected' : ''}>简体中文</option>
                        <option value="en" ${this.currentLanguage === 'en' ? 'selected' : ''}>English</option>
                    </select>
                `;
                
                // 插入到Logo父容器中
                logoSection.parentElement.appendChild(switcher);
            } else {
                // 備用方案：使用固定位置
                const switcher = document.createElement('div');
                switcher.id = 'vidspark-language-switcher';
                switcher.className = 'fixed top-20 right-4 z-50 bg-white shadow-lg rounded-lg p-2';
                switcher.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600" id="language-label">${this.getLanguageLabel()}:</span>
                        <select id="language-select" class="text-sm border border-gray-300 rounded px-2 py-1">
                            <option value="zh-TW" ${this.currentLanguage === 'zh-TW' ? 'selected' : ''}>繁體中文</option>
                            <option value="zh-CN" ${this.currentLanguage === 'zh-CN' ? 'selected' : ''}>简体中文</option>
                            <option value="en" ${this.currentLanguage === 'en' ? 'selected' : ''}>English</option>
                        </select>
                    </div>
                `;
                document.body.appendChild(switcher);
            }
        }

        // 綁定切換事件
        document.getElementById('language-select').addEventListener('change', (e) => {
            this.switchLanguage(e.target.value);
        });

        console.log(`[${new Date().toLocaleTimeString()}] 🔧 語言切換器已創建`);
    }

    // 切換語言
    switchLanguage(language) {
        this.currentLanguage = language;
        localStorage.setItem('vidspark-current-language', language);
        this.updateLanguageLabel();
        this.applyTranslations();
        console.log(`[${new Date().toLocaleTimeString()}] 🔄 語言已切換至: ${language}`);
    }

    // 更新語言標籤
    updateLanguageLabel() {
        const languageLabel = document.getElementById('language-label');
        if (languageLabel) {
            languageLabel.textContent = this.getLanguageLabel() + ':';
            console.log(`[${new Date().toLocaleTimeString()}] 🏷️ 語言標籤已更新為: ${this.getLanguageLabel()}`);
        }
    }

    // 獲取翻譯文字
    t(key, defaultText = '') {
        if (this.translations[key] && this.translations[key][this.currentLanguage]) {
            return this.translations[key][this.currentLanguage];
        }
        
        // 如果沒有翻譯，返回默認文字或key
        if (defaultText) {
            return defaultText;
        }
        
        // 嘗試返回英文版本
        if (this.translations[key] && this.translations[key]['en']) {
            return this.translations[key]['en'];
        }
        
        return key; // 最後返回key本身
    }

    // 應用翻譯到頁面
    applyTranslations() {
        // 方法1: 通過data-i18n屬性
        document.querySelectorAll('[data-i18n]').forEach(element => {
            const key = element.getAttribute('data-i18n');
            const defaultText = element.getAttribute('data-default') || element.textContent;
            element.textContent = this.t(key, defaultText);
        });

        // 方法2: 通過data-i18n-placeholder屬性（用於placeholder）
        document.querySelectorAll('[data-i18n-placeholder]').forEach(element => {
            const key = element.getAttribute('data-i18n-placeholder');
            const defaultText = element.getAttribute('placeholder') || '';
            element.placeholder = this.t(key, defaultText);
        });

        // 方法3: 通過data-i18n-title屬性（用於title）
        document.querySelectorAll('[data-i18n-title]').forEach(element => {
            const key = element.getAttribute('data-i18n-title');
            const defaultText = element.getAttribute('title') || '';
            element.title = this.t(key, defaultText);
        });

        console.log(`[${new Date().toLocaleTimeString()}] ✅ 翻譯已應用到頁面`);
    }

    // 手動刷新翻譯（當管理後台更新數據後調用）
    refresh() {
        this.loadTranslations();
        this.applyTranslations();
        console.log(`[${new Date().toLocaleTimeString()}] 🔄 翻譯數據已刷新`);
    }

    // 自動掃描頁面並提取文字字段（用於管理後台）
    scanPageForFields() {
        const fields = new Set();
        
        // 掃描所有文字節點
        const walker = document.createTreeWalker(
            document.body,
            NodeFilter.SHOW_TEXT,
            {
                acceptNode: function(node) {
                    // 過濾掉腳本和樣式中的文字
                    const parent = node.parentElement;
                    if (!parent) return NodeFilter.FILTER_REJECT;
                    
                    const tagName = parent.tagName.toLowerCase();
                    if (['script', 'style', 'noscript'].includes(tagName)) {
                        return NodeFilter.FILTER_REJECT;
                    }
                    
                    // 過濾掉純空白
                    const text = node.textContent.trim();
                    if (!text || text.length < 2) {
                        return NodeFilter.FILTER_REJECT;
                    }
                    
                    return NodeFilter.FILTER_ACCEPT;
                }
            }
        );

        let node;
        while (node = walker.nextNode()) {
            const text = node.textContent.trim();
            
            // 轉換為駝峰命名
            const key = this.textToKey(text);
            if (key && !fields.has(key)) {
                fields.add(key);
            }
        }

        // 掃描常見屬性
        document.querySelectorAll('[placeholder]').forEach(el => {
            const text = el.placeholder.trim();
            const key = this.textToKey(text);
            if (key) fields.add(key);
        });

        document.querySelectorAll('[title]').forEach(el => {
            const text = el.title.trim();
            const key = this.textToKey(text);
            if (key) fields.add(key);
        });

        return Array.from(fields);
    }

    // 將文字轉換為駝峰命名key
    textToKey(text) {
        if (!text || text.length < 2) return null;
        
        // 移除特殊字符，只保留字母數字和空格
        let cleaned = text.replace(/[^\w\s\u4e00-\u9fff]/g, ' ').trim();
        
        // 如果是中文，使用拼音或簡化邏輯
        if (/[\u4e00-\u9fff]/.test(cleaned)) {
            // 簡化中文轉換邏輯
            const chineseMap = {
                '歡迎': 'welcome',
                '登入': 'login',
                '註冊': 'register',
                '密碼': 'password',
                '電子信箱': 'email',
                '關於我們': 'about',
                '方案價格': 'pricing',
                '開始使用': 'getStarted',
                '了解更多': 'learnMore',
                '聯絡我們': 'contactUs'
            };
            
            for (const [chinese, english] of Object.entries(chineseMap)) {
                if (cleaned.includes(chinese)) {
                    return english;
                }
            }
            
            // 如果沒有匹配，使用簡化命名
            return 'text' + Math.random().toString(36).substr(2, 5);
        }
        
        // 英文轉駝峰命名
        return cleaned
            .toLowerCase()
            .split(/\s+/)
            .map((word, index) => {
                if (index === 0) return word;
                return word.charAt(0).toUpperCase() + word.slice(1);
            })
            .join('')
            .replace(/[^a-zA-Z0-9]/g, '');
    }
}

// 全局初始化
let vidsparkI18n;

// DOM載入完成後自動初始化
document.addEventListener('DOMContentLoaded', function() {
    console.log(`[${new Date().toLocaleTimeString()}] 🚀 開始初始化Vidspark i18n系統`);
    try {
        vidsparkI18n = new VidsparkI18n();
        // 初始化完成後設置全局訪問
        window.vidsparkI18n = vidsparkI18n;
        console.log(`[${new Date().toLocaleTimeString()}] ✅ Vidspark i18n系統初始化成功`);
    } catch (error) {
        console.error(`[${new Date().toLocaleTimeString()}] ❌ Vidspark i18n系統初始化失敗:`, error);
    }
});

// 提供類的全局訪問
window.VidsparkI18n = VidsparkI18n;
