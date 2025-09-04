class I18nManager {
    constructor() {
        this.currentLanguage = localStorage.getItem('vidspark-language') || 'zh-TW';
        this.translations = {};
        console.log(`[${new Date().toLocaleTimeString()}] 🚀 I18n測試管理器初始化，當前語言：${this.currentLanguage}`);
    }

    init() {
        this.loadTranslations();
        this.createLanguageSwitcher();
        this.applyTranslations();
        console.log(`[${new Date().toLocaleTimeString()}] ✅ I18n測試系統初始化完成`);
    }

    loadTranslations() {
        try {
            const stored = localStorage.getItem('vidspark-i18n-dictionary');
            if (stored) {
                this.translations = JSON.parse(stored);
                console.log(`[${new Date().toLocaleTimeString()}] 📚 從localStorage載入翻譯字典: ${Object.keys(this.translations).length} 個字段`);
            } else {
                console.log(`[${new Date().toLocaleTimeString()}] ⚠️ localStorage中沒有翻譯字典，使用默認值`);
            }
        } catch (error) {
            console.error('載入翻譯字典失敗:', error);
        }
    }

    // 獲取語言標籤的翻譯
    getLanguageLabel() {
        const labels = {
            'zh-TW': '語言',
            'zh-CN': '语言', 
            'en': 'Lang'  // 使用縮寫避免過長
        };
        return labels[this.currentLanguage] || '語言';
    }

    saveTranslations() {
        localStorage.setItem('vidspark-i18n-dictionary', JSON.stringify(this.translations));
        console.log(`[${new Date().toLocaleTimeString()}] 💾 翻譯字典已保存到localStorage`);
    }

    switchLanguage(lang) {
        console.log(`[${new Date().toLocaleTimeString()}] 🔄 切換語言: ${this.currentLanguage} → ${lang}`);
        this.currentLanguage = lang;
        localStorage.setItem('vidspark-language', lang);
        this.updateLanguageSwitcher();
        this.applyTranslations();
        console.log(`[${new Date().toLocaleTimeString()}] ✅ 語言切換完成: ${lang}`);
    }

    createLanguageSwitcher() {
        // 檢查是否已存在語言切換器
        if (document.getElementById('vidspark-language-switcher')) {
            return;
        }

        // 檢查是否是landing-test頁面，使用特定的選擇器
        const navRightSection = document.querySelector('.flex.items-center.space-x-4');
        if (navRightSection) {
            // 創建響應式的語言切換器
            const switcher = document.createElement('div');
            switcher.id = 'vidspark-language-switcher';
            switcher.className = 'flex items-center space-x-1 sm:space-x-2'; // 手機版更緊湊
            
            // 動態創建內容
            this.updateLanguageSwitcherContent(switcher);
            
            // 插入到登入按鈕之前
            const loginLink = navRightSection.querySelector('a[href="login.html"]');
            if (loginLink) {
                navRightSection.insertBefore(switcher, loginLink);
            } else {
                navRightSection.appendChild(switcher);
            }
        } else {
            // 嘗試插入到其他導航欄結構
            const altNavSection = document.querySelector('nav.space-x-4, nav .flex.items-center.space-x-4');
            if (altNavSection) {
                const switcher = document.createElement('div');
                switcher.id = 'vidspark-language-switcher';
                switcher.className = 'flex items-center space-x-1 sm:space-x-2';
                
                this.updateLanguageSwitcherContent(switcher);
                
                const loginLink = altNavSection.querySelector('a[href="login.html"]');
                if (loginLink) {
                    altNavSection.insertBefore(switcher, loginLink);
                } else {
                    altNavSection.appendChild(switcher);
                }
            } else {
                // 備用方案：使用固定位置
                const switcher = document.createElement('div');
                switcher.id = 'vidspark-language-switcher';
                switcher.className = 'fixed top-20 right-4 z-50 bg-white shadow-lg rounded-lg p-2';
                
                this.updateLanguageSwitcherContent(switcher, true);
                document.body.appendChild(switcher);
            }
        }

        // 綁定切換事件
        document.getElementById('language-select').addEventListener('change', (e) => {
            this.switchLanguage(e.target.value);
        });
        
        console.log(`[${new Date().toLocaleTimeString()}] 🔧 語言切換器已創建`);
    }

    updateLanguageSwitcherContent(switcher, isFixed = false) {
        const languageLabel = this.getLanguageLabel();
        
        if (isFixed) {
            switcher.innerHTML = `
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-600">${languageLabel}:</span>
                    <select id="language-select" class="text-sm border border-gray-300 rounded px-2 py-1">
                        <option value="zh-TW" ${this.currentLanguage === 'zh-TW' ? 'selected' : ''}>繁體中文</option>
                        <option value="zh-CN" ${this.currentLanguage === 'zh-CN' ? 'selected' : ''}>简体中文</option>
                        <option value="en" ${this.currentLanguage === 'en' ? 'selected' : ''}>English</option>
                    </select>
                </div>
            `;
        } else {
            switcher.innerHTML = `
                <span class="text-sm text-gray-600 hidden sm:inline">${languageLabel}:</span>
                <span class="text-xs text-gray-600 sm:hidden">${languageLabel}</span>
                <select id="language-select" class="bg-gray-50 border border-gray-200 text-gray-600 text-sm rounded-lg px-2 py-1 sm:px-3 sm:py-1.5 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="zh-TW" ${this.currentLanguage === 'zh-TW' ? 'selected' : ''}>繁體中文</option>
                    <option value="zh-CN" ${this.currentLanguage === 'zh-CN' ? 'selected' : ''}>简体中文</option>
                    <option value="en" ${this.currentLanguage === 'en' ? 'selected' : ''}>English</option>
                </select>
            `;
        }
    }

    updateLanguageSwitcher() {
        const switcher = document.getElementById('vidspark-language-switcher');
        const select = document.getElementById('language-select');
        if (switcher && select) {
            // 更新標籤文字
            const label = switcher.querySelector('span');
            if (label) {
                const newLabel = this.getLanguageLabel();
                if (label.classList.contains('hidden')) {
                    label.textContent = newLabel;
                } else {
                    label.textContent = newLabel + ':';
                }
            }
            
            // 更新選中項
            select.value = this.currentLanguage;
            console.log(`[${new Date().toLocaleTimeString()}] 🔄 語言切換器已更新`);
        }
    }

    applyTranslations() {
        const elements = document.querySelectorAll('[data-i18n]');
        console.log(`[${new Date().toLocaleTimeString()}] 🔄 開始應用翻譯到 ${elements.length} 個元素`);
        
        elements.forEach(element => {
            const key = element.getAttribute('data-i18n');
            const defaultText = element.getAttribute('data-default');
            
            if (this.translations[key] && this.translations[key][this.currentLanguage]) {
                element.textContent = this.translations[key][this.currentLanguage];
            } else if (defaultText) {
                element.textContent = defaultText;
            }
            
            // 處理placeholder翻譯
            const placeholderKey = element.getAttribute('data-i18n-placeholder');
            if (placeholderKey && this.translations[placeholderKey] && this.translations[placeholderKey][this.currentLanguage]) {
                element.placeholder = this.translations[placeholderKey][this.currentLanguage];
            }
            
            // 處理title翻譯
            const titleKey = element.getAttribute('data-i18n-title');
            if (titleKey && this.translations[titleKey] && this.translations[titleKey][this.currentLanguage]) {
                element.title = this.translations[titleKey][this.currentLanguage];
            }
        });
        
        console.log(`[${new Date().toLocaleTimeString()}] ✅ 翻譯應用完成`);
    }

    addTranslation(key, translations) {
        this.translations[key] = translations;
        this.saveTranslations();
        console.log(`[${new Date().toLocaleTimeString()}] ➕ 新增翻譯: ${key}`);
    }

    removeTranslation(key) {
        if (this.translations[key]) {
            delete this.translations[key];
            this.saveTranslations();
            console.log(`[${new Date().toLocaleTimeString()}] ➖ 移除翻譯: ${key}`);
        }
    }

    exportTranslations() {
        const data = JSON.stringify(this.translations, null, 2);
        const blob = new Blob([data], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'vidspark-translations.json';
        a.click();
        URL.revokeObjectURL(url);
        console.log(`[${new Date().toLocaleTimeString()}] 📤 翻譯已導出`);
    }

    importTranslations(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            try {
                const imported = JSON.parse(e.target.result);
                this.translations = { ...this.translations, ...imported };
                this.saveTranslations();
                this.applyTranslations();
                console.log(`[${new Date().toLocaleTimeString()}] 📥 翻譯已導入`);
            } catch (error) {
                console.error('導入翻譯失敗:', error);
            }
        };
        reader.readAsText(file);
    }

    getStats() {
        const totalKeys = Object.keys(this.translations).length;
        const languages = ['zh-TW', 'zh-CN', 'en'];
        const stats = {
            totalKeys,
            languages: {}
        };
        
        languages.forEach(lang => {
            const translated = Object.values(this.translations).filter(t => t[lang]).length;
            stats.languages[lang] = {
                translated,
                percentage: totalKeys > 0 ? Math.round((translated / totalKeys) * 100) : 0
            };
        });
        
        return stats;
    }
}

// 創建全局實例
function I18nManager() {
    if (!window.vidsparkI18n) {
        window.vidsparkI18n = new I18nManager();
    }
    return window.vidsparkI18n;
}

// 導出到全局作用域
window.I18nManager = I18nManager;
