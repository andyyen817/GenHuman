<template>
  <div class="bg-white">
    <!-- 導航欄 -->
    <nav class="bg-white/95 backdrop-blur-sm border-b border-gray-200 sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <!-- Logo -->
          <div class="flex items-center">
            <div class="flex-shrink-0 flex items-center">
              <div class="w-8 h-8 bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                <i class="fas fa-video text-white text-sm"></i>
              </div>
              <h1 class="text-xl font-bold text-gray-900">Vidspark</h1>
              <span class="ml-2 text-sm text-gray-400">點映</span>
            </div>
          </div>
          
          <!-- 主導航 -->
          <div class="hidden md:block">
            <div class="ml-10 flex items-baseline space-x-8">
              <a href="#features" class="text-gray-600 hover:text-purple-600 px-3 py-2 text-sm font-medium transition-colors">功能特色</a>
              <a href="#pricing" class="text-gray-600 hover:text-purple-600 px-3 py-2 text-sm font-medium transition-colors">定價方案</a>
              <a href="#examples" class="text-gray-600 hover:text-purple-600 px-3 py-2 text-sm font-medium transition-colors">案例展示</a>
              <a href="#help" class="text-gray-600 hover:text-purple-600 px-3 py-2 text-sm font-medium transition-colors">幫助支援</a>
            </div>
          </div>
          
          <!-- 右側按鈕 -->
          <div class="flex items-center space-x-4">
            <!-- 語言切換 -->
            <div class="relative">
              <select class="bg-gray-50 border border-gray-200 text-gray-600 text-sm rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                <option>繁體中文</option>
                <option>简体中文</option>
                <option>English</option>
              </select>
            </div>
            <router-link to="/login" class="text-gray-600 hover:text-purple-600 px-3 py-2 text-sm font-medium transition-colors">登入</router-link>
            <router-link to="/register" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors">免費註冊</router-link>
          </div>
        </div>
      </div>
    </nav>

    <!-- 英雄區 -->
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden">
      <!-- 背景漸變 -->
      <div class="absolute inset-0 hero-gradient opacity-5"></div>
      
      <!-- 浮動元素背景 -->
      <div class="absolute inset-0 overflow-hidden">
        <div class="floating-animation absolute top-20 left-10 w-64 h-64 bg-purple-200 rounded-full mix-blend-multiply filter blur-xl opacity-30"></div>
        <div class="floating-animation absolute top-40 right-10 w-72 h-72 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-30" style="animation-delay: 2s;"></div>
        <div class="floating-animation absolute bottom-20 left-1/2 w-80 h-80 bg-pink-200 rounded-full mix-blend-multiply filter blur-xl opacity-30" style="animation-delay: 4s;"></div>
      </div>

      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="max-w-4xl mx-auto">
          <!-- 主標題 -->
          <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold text-gray-900 mb-6 leading-tight">
            最具創新性的
            <span class="bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">AI 影片生成器</span>
          </h1>
          
          <!-- 副標題 -->
          <p class="text-xl sm:text-2xl text-gray-600 mb-8 max-w-3xl mx-auto leading-relaxed">
            將文字或想法轉化為專業級營銷影片。利用 AI 數字人、聲音克隆和海量模板，輕鬆打造引人注目的內容。
          </p>

          <!-- CTA按鈕組 -->
          <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-12">
            <router-link to="/register" class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-4 rounded-xl text-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
              <i class="fas fa-rocket mr-2"></i>免費開始創作
            </router-link>
            <a href="#demo" class="bg-white hover:bg-gray-50 text-gray-700 px-8 py-4 rounded-xl text-lg font-semibold border-2 border-gray-200 transition-all duration-300">
              <i class="fas fa-play mr-2"></i>觀看演示
            </a>
          </div>

          <!-- 免費標籤 -->
          <div class="flex justify-center items-center space-x-6 text-sm text-gray-500">
            <div class="flex items-center">
              <i class="fas fa-check-circle text-green-500 mr-2"></i>
              <span>免費數字人體驗</span>
            </div>
            <div class="flex items-center">
              <i class="fas fa-check-circle text-green-500 mr-2"></i>
              <span>免費聲音克隆</span>
            </div>
            <div class="flex items-center">
              <i class="fas fa-check-circle text-green-500 mr-2"></i>
              <span>無需信用卡</span>
            </div>
          </div>
        </div>

        <!-- 產品預覽圖 -->
        <div class="mt-16 relative">
          <div class="relative mx-auto max-w-5xl">
            <!-- 瀏覽器框架 -->
            <div class="bg-white rounded-t-xl shadow-2xl border border-gray-200">
              <div class="flex items-center px-4 py-3 bg-gray-50 rounded-t-xl border-b border-gray-200">
                <div class="flex space-x-2">
                  <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                  <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
                  <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                </div>
                <div class="flex-1 mx-4">
                  <div class="bg-white border border-gray-200 rounded-md px-3 py-1 text-sm text-gray-500">
                    https://vidspark.ai/dashboard
                  </div>
                </div>
              </div>
              <!-- 模擬產品界面 -->
              <div class="bg-gradient-to-br from-purple-50 to-blue-50 p-8 h-96 flex items-center justify-center">
                <div class="text-center">
                  <div class="w-24 h-24 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-6 pulse-animation">
                    <i class="fas fa-video text-white text-3xl"></i>
                  </div>
                  <h3 class="text-2xl font-bold text-gray-800 mb-2">AI 影片創作平台</h3>
                  <p class="text-gray-600">即將在您的體驗中呈現...</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 核心功能區 -->
    <section id="features" class="py-20 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-gray-900 mb-4">強大功能，觸手可及</h2>
          <p class="text-xl text-gray-600 max-w-3xl mx-auto">
            從文字到專業影片，只需幾分鐘。我們的 AI 技術讓每個人都能創造出令人驚豔的內容。
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <!-- 免費數字人 -->
          <div class="bg-white rounded-xl p-8 shadow-lg card-hover border border-gray-200">
            <div class="w-16 h-16 bg-purple-100 rounded-xl flex items-center justify-center mb-6">
              <i class="fas fa-user-tie text-purple-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">免費 AI 數字人</h3>
            <p class="text-gray-600 mb-4">
              5個高質量數字人形象，涵蓋不同風格和場景。每日3次免費生成，立即體驗專業級影片創作。
            </p>
            <div class="flex items-center text-sm text-green-600 font-medium">
              <i class="fas fa-gift mr-2"></i>
              完全免費使用
            </div>
          </div>

          <!-- 聲音克隆 -->
          <div class="bg-white rounded-xl p-8 shadow-lg card-hover border border-gray-200">
            <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center mb-6">
              <i class="fas fa-microphone text-blue-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">AI 聲音克隆</h3>
            <p class="text-gray-600 mb-4">
              只需60秒錄音，即可創建您的專屬 AI 聲音。支持多種情感表達，讓數字人說出您的聲音。
            </p>
            <div class="flex items-center text-sm text-purple-600 font-medium">
              <i class="fas fa-magic mr-2"></i>
              AI 技術驅動
            </div>
          </div>

          <!-- 智能模板 -->
          <div class="bg-white rounded-xl p-8 shadow-lg card-hover border border-gray-200">
            <div class="w-16 h-16 bg-green-100 rounded-xl flex items-center justify-center mb-6">
              <i class="fas fa-layer-group text-green-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">智能影片模板</h3>
            <p class="text-gray-600 mb-4">
              50+ 專業模板，涵蓋營銷、教育、娛樂等場景。一鍵套用，快速生成符合品牌風格的影片。
            </p>
            <div class="flex items-center text-sm text-orange-600 font-medium">
              <i class="fas fa-rocket mr-2"></i>
              快速創作
            </div>
          </div>

          <!-- 多平台適配 -->
          <div class="bg-white rounded-xl p-8 shadow-lg card-hover border border-gray-200">
            <div class="w-16 h-16 bg-pink-100 rounded-xl flex items-center justify-center mb-6">
              <i class="fas fa-mobile-alt text-pink-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">多平台適配</h3>
            <p class="text-gray-600 mb-4">
              自動生成適合 TikTok、YouTube、Instagram 等平台的不同尺寸版本，一次創作多處使用。
            </p>
            <div class="flex items-center text-sm text-blue-600 font-medium">
              <i class="fas fa-globe mr-2"></i>
              全平台覆蓋
            </div>
          </div>

          <!-- 批量創作 -->
          <div class="bg-white rounded-xl p-8 shadow-lg card-hover border border-gray-200">
            <div class="w-16 h-16 bg-yellow-100 rounded-xl flex items-center justify-center mb-6">
              <i class="fas fa-copy text-yellow-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">批量生成</h3>
            <p class="text-gray-600 mb-4">
              批量導入文本內容，自動生成多個影片。完美適合電商產品介紹、課程內容等批量需求。
            </p>
            <div class="flex items-center text-sm text-red-600 font-medium">
              <i class="fas fa-tachometer-alt mr-2"></i>
              高效批量
            </div>
          </div>

          <!-- 多語言支持 -->
          <div class="bg-white rounded-xl p-8 shadow-lg card-hover border border-gray-200">
            <div class="w-16 h-16 bg-indigo-100 rounded-xl flex items-center justify-center mb-6">
              <i class="fas fa-language text-indigo-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">多語言支持</h3>
            <p class="text-gray-600 mb-4">
              支援繁體中文、簡體中文、英文等多種語言。全球用戶都能享受本地化的創作體驗。
            </p>
            <div class="flex items-center text-sm text-purple-600 font-medium">
              <i class="fas fa-world mr-2"></i>
              全球化體驗
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA區域 -->
    <section class="py-20 hero-gradient">
      <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl font-bold text-white mb-6">
          準備好創造驚人的影片了嗎？
        </h2>
        <p class="text-xl text-white/90 mb-10">
          加入數萬名創作者，使用 Vidspark 將您的想法轉化為專業影片
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <router-link to="/register" class="bg-white hover:bg-gray-100 text-purple-600 px-8 py-4 rounded-xl text-lg font-semibold transition-all duration-300 transform hover:scale-105">
            立即免費開始
          </router-link>
          <a href="#contact" class="border-2 border-white text-white hover:bg-white hover:text-purple-600 px-8 py-4 rounded-xl text-lg font-semibold transition-all duration-300">
            聯繫銷售團隊
          </a>
        </div>
      </div>
    </section>

    <!-- 頁腳 -->
    <footer class="bg-gray-900 text-white py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <!-- Logo與描述 -->
          <div class="col-span-1 md:col-span-2">
            <div class="flex items-center mb-4">
              <div class="w-8 h-8 bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                <i class="fas fa-video text-white text-sm"></i>
              </div>
              <h3 class="text-xl font-bold">Vidspark</h3>
            </div>
            <p class="text-gray-400 mb-4 max-w-md">
              讓每個人都能輕鬆創作專業級 AI 營銷影片。通過 AI 技術降低影片創作門檻，點亮創意靈感。
            </p>
            <div class="flex space-x-4">
              <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter"></i></a>
              <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook"></i></a>
              <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-linkedin"></i></a>
              <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-youtube"></i></a>
            </div>
          </div>
          
          <!-- 產品連結 -->
          <div>
            <h4 class="font-semibold mb-4">產品</h4>
            <ul class="space-y-2 text-gray-400">
              <li><a href="#" class="hover:text-white">AI 數字人</a></li>
              <li><a href="#" class="hover:text-white">聲音克隆</a></li>
              <li><a href="#" class="hover:text-white">影片模板</a></li>
              <li><a href="#" class="hover:text-white">API 文檔</a></li>
            </ul>
          </div>
          
          <!-- 支援連結 -->
          <div>
            <h4 class="font-semibold mb-4">支援</h4>
            <ul class="space-y-2 text-gray-400">
              <li><a href="#" class="hover:text-white">幫助中心</a></li>
              <li><a href="#" class="hover:text-white">聯繫我們</a></li>
              <li><a href="#" class="hover:text-white">隱私政策</a></li>
              <li><a href="#" class="hover:text-white">服務條款</a></li>
            </ul>
          </div>
        </div>
        
        <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
          <p>&copy; 2025 Vidspark. 版權所有.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
// 暫時不需要複雜的邏輯
</script>

<style scoped>
.hero-gradient {
  background: linear-gradient(135deg, #5D5FEF 0%, #A78BFA 100%);
}

.card-hover {
  transition: all 0.3s ease;
}

.card-hover:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(93, 95, 239, 0.15);
}

.floating-animation {
  animation: float 6s ease-in-out infinite;
}

@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-20px); }
}

.pulse-animation {
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.7; }
}
</style>