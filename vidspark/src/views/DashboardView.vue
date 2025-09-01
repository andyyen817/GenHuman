<template>
  <div class="dashboard-layout bg-gray-50 h-screen flex">
    <!-- Left Sidebar -->
    <div class="w-64 sidebar-dark flex flex-col">
      <!-- Logo Area -->
      <div class="p-6 border-b border-gray-700">
        <div class="flex items-center">
          <div class="w-8 h-8 bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg flex items-center justify-center mr-3">
            <i class="fas fa-video text-white text-sm"></i>
          </div>
          <h1 class="text-xl font-bold text-white">Vidspark</h1>
        </div>
      </div>

      <!-- Create Button -->
      <div class="p-4">
        <button 
          @click="showCreateMenu = !showCreateMenu"
          class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 px-4 rounded-lg font-medium transition-colors flex items-center justify-center"
        >
          <i class="fas fa-plus mr-2"></i>
          創建影片
        </button>
      </div>

      <!-- Main Navigation -->
      <nav class="flex-1 px-4">
        <ul class="space-y-2">
          <li>
            <router-link 
              to="/dashboard" 
              class="flex items-center px-4 py-3 text-white bg-purple-600 rounded-lg"
            >
              <i class="fas fa-home mr-3"></i>
              首頁
            </router-link>
          </li>
          <li>
            <router-link 
              to="/projects" 
              class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
            >
              <i class="fas fa-folder mr-3"></i>
              我的項目
            </router-link>
          </li>
          <li>
            <router-link 
              to="/avatars" 
              class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
            >
              <i class="fas fa-user-tie mr-3"></i>
              數字人庫
            </router-link>
          </li>
          <li>
            <router-link 
              to="/voices" 
              class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
            >
              <i class="fas fa-microphone mr-3"></i>
              聲音庫
            </router-link>
          </li>
          <li>
            <router-link 
              to="/templates" 
              class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
            >
              <i class="fas fa-layer-group mr-3"></i>
              模板庫
            </router-link>
          </li>
          <li>
            <router-link 
              to="/media" 
              class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
            >
              <i class="fas fa-images mr-3"></i>
              媒體庫
            </router-link>
          </li>
        </ul>

        <!-- Divider -->
        <div class="my-6 border-t border-gray-600"></div>

        <ul class="space-y-2">
          <li>
            <router-link 
              to="/settings" 
              class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
            >
              <i class="fas fa-cog mr-3"></i>
              應用設定
            </router-link>
          </li>
          <li>
            <a 
              href="#" 
              class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
            >
              <i class="fas fa-users mr-3"></i>
              創建團隊
            </a>
          </li>
        </ul>
      </nav>

      <!-- Bottom User Info -->
      <div class="p-4 border-t border-gray-700">
        <!-- Upgrade Button -->
        <div class="mb-4">
          <button class="w-full bg-gradient-to-r from-yellow-500 to-orange-500 text-white py-2 px-4 rounded-lg font-medium text-sm hover:from-yellow-600 hover:to-orange-600 transition-all">
            <i class="fas fa-crown mr-2"></i>
            升級方案
          </button>
        </div>
        
        <!-- User Avatar -->
        <div class="flex items-center">
          <img 
            src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face" 
            alt="用戶頭像" 
            class="w-10 h-10 rounded-full"
          >
          <div class="ml-3 flex-1">
            <p class="text-white text-sm font-medium">{{ userName }}</p>
            <p class="text-gray-400 text-xs">免費會員</p>
          </div>
          <button class="text-gray-400 hover:text-white" @click="showUserMenu = !showUserMenu">
            <i class="fas fa-ellipsis-v"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Right Main Content Area -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Top Header -->
      <header class="bg-white border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">歡迎回來，{{ userName }}！</h1>
            <p class="text-gray-600">準備創建您的下一個精彩影片了嗎？</p>
          </div>
          
          <div class="flex items-center space-x-4">
            <!-- Language Switcher -->
            <LanguageSwitcher />
            
            <!-- Notification Button -->
            <button class="relative p-2 text-gray-500 hover:text-gray-700">
              <i class="fas fa-bell text-xl"></i>
              <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>
            
            <!-- Help Button -->
            <button class="p-2 text-gray-500 hover:text-gray-700">
              <i class="fas fa-question-circle text-xl"></i>
            </button>
          </div>
        </div>
      </header>

      <!-- Main Content Scroll Area -->
      <main class="flex-1 overflow-y-auto p-6">
        <!-- Top Banner -->
        <div class="gradient-border mb-8">
          <div class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">專屬數字人，全新升級</h2>
                <p class="text-gray-600">立即用您的數字分身，為您的腳本帶來生命力</p>
              </div>
              <button 
                @click="startCustomAvatar"
                class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-colors"
              >
                立即試用 →
              </button>
            </div>
          </div>
        </div>

        <!-- Creation Entry Section -->
        <section class="mb-8">
          <h2 class="text-xl font-bold text-gray-900 mb-6">開始創作</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Free Digital Human Video -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 card-hover relative">
              <!-- Free Badge -->
              <div class="absolute top-4 right-4">
                <span class="free-badge text-white text-xs font-bold px-2 py-1 rounded-full">FREE</span>
              </div>
              
              <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-4">
                <i class="fas fa-user-tie text-purple-600 text-xl"></i>
              </div>
              <h3 class="text-lg font-semibold text-gray-900 mb-2">免費數字人影片</h3>
              <p class="text-gray-600 text-sm mb-4">
                最快的方式！輸入文字，選擇免費數字人，立即生成影片
              </p>
              <div class="flex items-center justify-between">
                <span class="text-green-600 text-sm font-medium">
                  <i class="fas fa-gift mr-1"></i>
                  每日 3 次免費
                </span>
                <button 
                  @click="startFreeVideo"
                  class="text-purple-600 hover:text-purple-700 font-medium"
                >
                  開始 →
                </button>
              </div>
            </div>

            <!-- Text to Video -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 card-hover">
              <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                <i class="fas fa-file-alt text-blue-600 text-xl"></i>
              </div>
              <h3 class="text-lg font-semibold text-gray-900 mb-2">文字轉影片</h3>
              <p class="text-gray-600 text-sm mb-4">
                將您的文章、腳本或網頁內容一鍵轉為影片
              </p>
              <div class="flex items-center justify-between">
                <span class="text-blue-600 text-sm font-medium">
                  <i class="fas fa-magic mr-1"></i>
                  AI 智能轉換
                </span>
                <button 
                  @click="startTextToVideo"
                  class="text-purple-600 hover:text-purple-700 font-medium"
                >
                  開始 →
                </button>
              </div>
            </div>

            <!-- Voice Cloning Experience -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 card-hover relative">
              <!-- Free Badge -->
              <div class="absolute top-4 right-4">
                <span class="free-badge text-white text-xs font-bold px-2 py-1 rounded-full">FREE</span>
              </div>
              
              <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                <i class="fas fa-microphone text-green-600 text-xl"></i>
              </div>
              <h3 class="text-lg font-semibold text-gray-900 mb-2">聲音克隆體驗</h3>
              <p class="text-gray-600 text-sm mb-4">
                60秒錄音，創建您的專屬 AI 聲音
              </p>
              <div class="flex items-center justify-between">
                <span class="text-green-600 text-sm font-medium">
                  <i class="fas fa-gift mr-1"></i>
                  1 次免費體驗
                </span>
                <button 
                  @click="startVoiceCloning"
                  class="text-purple-600 hover:text-purple-700 font-medium"
                >
                  開始 →
                </button>
              </div>
            </div>

            <!-- Mimic Video -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 card-hover">
              <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center mb-4">
                <i class="fas fa-link text-yellow-600 text-xl"></i>
              </div>
              <h3 class="text-lg font-semibold text-gray-900 mb-2">模仿影片創作</h3>
              <p class="text-gray-600 text-sm mb-4">
                貼上影片連結，AI 自動解析並生成相似風格的草稿
              </p>
              <div class="flex items-center justify-between">
                <span class="text-yellow-600 text-sm font-medium">
                  <i class="fas fa-robot mr-1"></i>
                  AI 風格學習
                </span>
                <button 
                  @click="startMimicVideo"
                  class="text-purple-600 hover:text-purple-700 font-medium"
                >
                  開始 →
                </button>
              </div>
            </div>

            <!-- PPT/PDF Explanation -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 card-hover">
              <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mb-4">
                <i class="fas fa-presentation text-red-600 text-xl"></i>
              </div>
              <h3 class="text-lg font-semibold text-gray-900 mb-2">PPT/PDF 講解</h3>
              <p class="text-gray-600 text-sm mb-4">
                上傳您的簡報，搭配數字人進行講解
              </p>
              <div class="flex items-center justify-between">
                <span class="text-red-600 text-sm font-medium">
                  <i class="fas fa-chalkboard-teacher mr-1"></i>
                  教育專用
                </span>
                <button 
                  @click="startPresentationVideo"
                  class="text-purple-600 hover:text-purple-700 font-medium"
                >
                  開始 →
                </button>
              </div>
            </div>

            <!-- Create Your Digital Human -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 card-hover">
              <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mb-4">
                <i class="fas fa-video text-indigo-600 text-xl"></i>
              </div>
              <h3 class="text-lg font-semibold text-gray-900 mb-2">創建您的數字人</h3>
              <p class="text-gray-600 text-sm mb-4">
                上傳您的影片，創建您自己的數字分身
              </p>
              <div class="flex items-center justify-between">
                <span class="text-indigo-600 text-sm font-medium">
                  <i class="fas fa-crown mr-1"></i>
                  高級功能
                </span>
                <button 
                  @click="startCustomDigitalHuman"
                  class="text-purple-600 hover:text-purple-700 font-medium"
                >
                  開始 →
                </button>
              </div>
            </div>
          </div>
        </section>

        <!-- Recent Creations -->
        <section>
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">最近的創作</h2>
            <router-link to="/projects" class="text-purple-600 hover:text-purple-700 font-medium">查看全部</router-link>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Project Card 1 -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden card-hover">
              <div class="relative">
                <img 
                  src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=300&h=160&fit=crop" 
                  alt="影片縮圖" 
                  class="w-full h-32 object-cover"
                >
                <div class="absolute inset-0 flex items-center justify-center">
                  <div class="w-8 h-8 bg-white bg-opacity-90 rounded-full flex items-center justify-center">
                    <i class="fas fa-play text-purple-600 text-sm ml-0.5"></i>
                  </div>
                </div>
                <div class="absolute bottom-2 right-2 bg-black bg-opacity-70 text-white text-xs px-1.5 py-0.5 rounded">
                  01:24
                </div>
              </div>
              <div class="p-4">
                <h3 class="font-medium text-gray-900 mb-1">產品介紹影片</h3>
                <p class="text-sm text-gray-500 mb-2">2小時前</p>
                <div class="flex items-center justify-between">
                  <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    已完成
                  </span>
                  <button class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-ellipsis-h"></i>
                  </button>
                </div>
              </div>
            </div>

            <!-- Project Card 2 -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden card-hover">
              <div class="relative">
                <img 
                  src="https://images.unsplash.com/photo-1522869635100-9f4c5e86aa37?w=300&h=160&fit=crop" 
                  alt="影片縮圖" 
                  class="w-full h-32 object-cover"
                >
                <div class="absolute inset-0 flex items-center justify-center">
                  <div class="w-8 h-8 bg-white bg-opacity-90 rounded-full flex items-center justify-center">
                    <i class="fas fa-play text-purple-600 text-sm ml-0.5"></i>
                  </div>
                </div>
                <div class="absolute bottom-2 right-2 bg-black bg-opacity-70 text-white text-xs px-1.5 py-0.5 rounded">
                  00:45
                </div>
              </div>
              <div class="p-4">
                <h3 class="font-medium text-gray-900 mb-1">課程宣傳片</h3>
                <p class="text-sm text-gray-500 mb-2">昨天</p>
                <div class="flex items-center justify-between">
                  <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    處理中
                  </span>
                  <button class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-ellipsis-h"></i>
                  </button>
                </div>
              </div>
            </div>

            <!-- Project Card 3 -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden card-hover">
              <div class="relative">
                <img 
                  src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=300&h=160&fit=crop" 
                  alt="影片縮圖" 
                  class="w-full h-32 object-cover"
                >
                <div class="absolute inset-0 flex items-center justify-center">
                  <div class="w-8 h-8 bg-white bg-opacity-90 rounded-full flex items-center justify-center">
                    <i class="fas fa-play text-purple-600 text-sm ml-0.5"></i>
                  </div>
                </div>
                <div class="absolute bottom-2 right-2 bg-black bg-opacity-70 text-white text-xs px-1.5 py-0.5 rounded">
                  02:15
                </div>
              </div>
              <div class="p-4">
                <h3 class="font-medium text-gray-900 mb-1">團隊介紹</h3>
                <p class="text-sm text-gray-500 mb-2">3天前</p>
                <div class="flex items-center justify-between">
                  <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    已完成
                  </span>
                  <button class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-ellipsis-h"></i>
                  </button>
                </div>
              </div>
            </div>

            <!-- Project Card 4 -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden card-hover">
              <div class="relative">
                <img 
                  src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=300&h=160&fit=crop" 
                  alt="影片縮圖" 
                  class="w-full h-32 object-cover"
                >
                <div class="absolute inset-0 flex items-center justify-center">
                  <div class="w-8 h-8 bg-white bg-opacity-90 rounded-full flex items-center justify-center">
                    <i class="fas fa-play text-purple-600 text-sm ml-0.5"></i>
                  </div>
                </div>
                <div class="absolute bottom-2 right-2 bg-black bg-opacity-70 text-white text-xs px-1.5 py-0.5 rounded">
                  01:58
                </div>
              </div>
              <div class="p-4">
                <h3 class="font-medium text-gray-900 mb-1">市場分析報告</h3>
                <p class="text-sm text-gray-500 mb-2">1週前</p>
                <div class="flex items-center justify-between">
                  <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                    草稿
                  </span>
                  <button class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-ellipsis-h"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </section>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import LanguageSwitcher from '@/components/LanguageSwitcher.vue';

const router = useRouter();

// State
const showCreateMenu = ref(false);
const showUserMenu = ref(false);
const userName = ref('張小明');

// Navigation Methods
const startFreeVideo = () => {
  console.log('🎬 開始免費數字人影片創作');
  router.push('/create/free-video');
};

const startTextToVideo = () => {
  console.log('📝 開始文字轉影片');
  router.push('/create/text-to-video');
};

const startVoiceCloning = () => {
  console.log('🎤 開始聲音克隆');
  router.push('/create/voice-cloning');
};

const startMimicVideo = () => {
  console.log('🔗 開始模仿影片創作');
  router.push('/create/mimic-video');
};

const startPresentationVideo = () => {
  console.log('📊 開始PPT/PDF講解');
  router.push('/create/presentation');
};

const startCustomDigitalHuman = () => {
  console.log('👤 開始創建自定義數字人');
  router.push('/create/custom-avatar');
};

const startCustomAvatar = () => {
  console.log('⭐ 開始專屬數字人試用');
  router.push('/premium/custom-avatar');
};
</script>

<style scoped>
.sidebar-dark {
  background: linear-gradient(180deg, #1a1c20 0%, #2d2f36 100%);
}

.card-hover {
  transition: all 0.3s ease;
}

.card-hover:hover {
  transform: translateY(-4px);
  box-shadow: 0 20px 40px rgba(93, 95, 239, 0.15);
}

.free-badge {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.gradient-border {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 2px;
  border-radius: 12px;
}

.gradient-border > div {
  background: white;
  border-radius: 10px;
}
</style>