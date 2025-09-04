<template>
  <div class="bg-gray-50 h-screen flex">
    <!-- 左側導航欄 -->
    <div class="w-64 sidebar-dark flex flex-col">
      <!-- Logo區域 -->
      <div class="p-6 border-b border-gray-700">
        <div class="flex items-center">
          <div class="w-8 h-8 bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg flex items-center justify-center mr-3">
            <i class="fas fa-video text-white text-sm"></i>
          </div>
          <h1 class="text-xl font-bold text-white">Vidspark</h1>
        </div>
      </div>

      <!-- 創建按鈕 -->
      <div class="p-4">
        <button 
          @click="createVideo"
          class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 px-4 rounded-lg font-medium transition-colors flex items-center justify-center"
        >
          <i class="fas fa-plus mr-2"></i>
          創建影片
        </button>
      </div>

      <!-- 主導航 -->
      <nav class="flex-1 px-4">
        <ul class="space-y-2">
          <li>
            <router-link 
              to="/dashboard" 
              class="flex items-center px-4 py-3 text-white bg-purple-600 rounded-lg"
              active-class="bg-purple-600 text-white"
            >
              <i class="fas fa-home mr-3"></i>
              首頁
            </router-link>
          </li>
          <li>
            <router-link 
              to="/projects" 
              class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
              active-class="bg-purple-600 text-white"
            >
              <i class="fas fa-folder mr-3"></i>
              我的項目
            </router-link>
          </li>
          <li>
            <router-link 
              to="/avatars" 
              class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
              active-class="bg-purple-600 text-white"
            >
              <i class="fas fa-user-tie mr-3"></i>
              數字人庫
            </router-link>
          </li>
          <li>
            <router-link 
              to="/voices" 
              class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
              active-class="bg-purple-600 text-white"
            >
              <i class="fas fa-microphone mr-3"></i>
              聲音庫
            </router-link>
          </li>
          <li>
            <router-link 
              to="/templates" 
              class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
              active-class="bg-purple-600 text-white"
            >
              <i class="fas fa-layer-group mr-3"></i>
              模板庫
            </router-link>
          </li>
          <li>
            <router-link 
              to="/media" 
              class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
              active-class="bg-purple-600 text-white"
            >
              <i class="fas fa-images mr-3"></i>
              媒體庫
            </router-link>
          </li>
        </ul>

        <!-- 分隔線 -->
        <div class="my-6 border-t border-gray-600"></div>

        <ul class="space-y-2">
          <li>
            <router-link 
              to="/settings" 
              class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
              active-class="bg-purple-600 text-white"
            >
              <i class="fas fa-cog mr-3"></i>
              應用設定
            </router-link>
          </li>
          <li>
            <router-link 
              to="/team" 
              class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
              active-class="bg-purple-600 text-white"
            >
              <i class="fas fa-users mr-3"></i>
              創建團隊
            </router-link>
          </li>
        </ul>
      </nav>

      <!-- 底部用戶信息 -->
      <div class="p-4 border-t border-gray-700">
        <!-- 升級按鈕 -->
        <div class="mb-4">
          <button 
            @click="upgradePlan"
            class="w-full bg-gradient-to-r from-yellow-500 to-orange-500 text-white py-2 px-4 rounded-lg font-medium text-sm hover:from-yellow-600 hover:to-orange-600 transition-all"
          >
            <i class="fas fa-crown mr-2"></i>
            升級方案
          </button>
        </div>
        
        <!-- 用戶頭像 -->
        <div class="flex items-center">
          <img 
            :src="userInfo.avatar" 
            :alt="userInfo.name" 
            class="w-10 h-10 rounded-full"
          >
          <div class="ml-3 flex-1">
            <p class="text-white text-sm font-medium">{{ userInfo.name }}</p>
            <p class="text-gray-400 text-xs">{{ userInfo.plan }}</p>
          </div>
          <button class="text-gray-400 hover:text-white">
            <i class="fas fa-ellipsis-v"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- 右側主內容區 -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- 頂部欄 -->
      <header class="bg-white border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">歡迎回來，{{ userInfo.name }}！</h1>
            <p class="text-gray-600">準備創建您的下一個精彩影片了嗎？</p>
          </div>
          
          <div class="flex items-center space-x-4">
            <!-- 語言切換 -->
            <select 
              v-model="selectedLanguage"
              class="bg-gray-50 border border-gray-200 text-gray-600 text-sm rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            >
              <option value="zh-TW">繁體中文</option>
              <option value="zh-CN">简体中文</option>
              <option value="en">English</option>
            </select>
            
            <!-- 通知按鈕 -->
            <button class="relative p-2 text-gray-500 hover:text-gray-700">
              <i class="fas fa-bell text-xl"></i>
              <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>
            
            <!-- 幫助按鈕 -->
            <button class="p-2 text-gray-500 hover:text-gray-700">
              <i class="fas fa-question-circle text-xl"></i>
            </button>
          </div>
        </div>
      </header>

      <!-- 主內容滾動區域 -->
      <main class="flex-1 overflow-y-auto p-6">
        <!-- 頂部橫幅 -->
        <div class="gradient-border mb-8">
          <div class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">專屬數字人，全新升級</h2>
                <p class="text-gray-600">立即用您的數字分身，為您的腳本帶來生命力</p>
              </div>
              <button 
                @click="tryDigitalHuman"
                class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-colors"
              >
                立即試用 →
              </button>
            </div>
          </div>
        </div>

        <!-- 創作入口區塊 -->
        <section class="mb-8">
          <h2 class="text-xl font-bold text-gray-900 mb-6">開始創作</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- 免費數字人影片 -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 card-hover relative">
              <!-- 免費標籤 -->
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

            <!-- 文字轉影片 -->
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

            <!-- 聲音克隆體驗 -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 card-hover relative">
              <!-- 免費標籤 -->
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
                  @click="startVoiceClone"
                  class="text-purple-600 hover:text-purple-700 font-medium"
                >
                  開始 →
                </button>
              </div>
            </div>

            <!-- 模仿影片創作 -->
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
                  @click="startImitateVideo"
                  class="text-purple-600 hover:text-purple-700 font-medium"
                >
                  開始 →
                </button>
              </div>
            </div>

            <!-- PPT/PDF 講解 -->
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
                  @click="startPPTVideo"
                  class="text-purple-600 hover:text-purple-700 font-medium"
                >
                  開始 →
                </button>
              </div>
            </div>

            <!-- 創建您的數字人 -->
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

        <!-- 最近的創作 -->
        <section>
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">最近的創作</h2>
            <router-link 
              to="/projects" 
              class="text-purple-600 hover:text-purple-700 font-medium"
            >
              查看全部
            </router-link>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- 項目卡片 1 -->
            <div 
              v-for="project in recentProjects" 
              :key="project.id"
              @click="openProject(project)"
              class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden card-hover cursor-pointer"
            >
              <div class="relative">
                <img 
                  :src="project.thumbnail" 
                  :alt="project.title" 
                  class="w-full h-32 object-cover"
                >
                <div class="absolute inset-0 flex items-center justify-center">
                  <div class="w-8 h-8 bg-white bg-opacity-90 rounded-full flex items-center justify-center">
                    <i class="fas fa-play text-purple-600 text-sm ml-0.5"></i>
                  </div>
                </div>
                <div class="absolute bottom-2 right-2 bg-black bg-opacity-70 text-white text-xs px-1.5 py-0.5 rounded">
                  {{ project.duration }}
                </div>
              </div>
              <div class="p-4">
                <h3 class="font-medium text-gray-900 mb-1">{{ project.title }}</h3>
                <p class="text-sm text-gray-500 mb-2">{{ project.createdAt }}</p>
                <div class="flex items-center justify-between">
                  <span 
                    :class="getStatusClass(project.status)"
                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                  >
                    {{ getStatusText(project.status) }}
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
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

// 響應式數據
const selectedLanguage = ref('zh-TW')

// 用戶信息
const userInfo = reactive({
  name: '張小明',
  plan: '免費會員',
  avatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face'
})

// 最近項目
const recentProjects = ref([
  {
    id: 1,
    title: '產品介紹影片',
    thumbnail: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=300&h=160&fit=crop',
    duration: '01:24',
    createdAt: '2小時前',
    status: 'completed'
  },
  {
    id: 2,
    title: '課程宣傳片',
    thumbnail: 'https://images.unsplash.com/photo-1522869635100-9f4c5e86aa37?w=300&h=160&fit=crop',
    duration: '00:45',
    createdAt: '昨天',
    status: 'processing'
  },
  {
    id: 3,
    title: '團隊介紹',
    thumbnail: 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=300&h=160&fit=crop',
    duration: '02:15',
    createdAt: '3天前',
    status: 'completed'
  },
  {
    id: 4,
    title: '市場分析報告',
    thumbnail: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=300&h=160&fit=crop',
    duration: '01:58',
    createdAt: '1週前',
    status: 'draft'
  }
])

const getStatusClass = computed(() => (status: string) => {
  const classes = {
    completed: 'bg-green-100 text-green-800',
    processing: 'bg-blue-100 text-blue-800',
    draft: 'bg-gray-100 text-gray-800'
  }
  return classes[status as keyof typeof classes] || 'bg-gray-100 text-gray-800'
})

const getStatusText = computed(() => (status: string) => {
  const texts = {
    completed: '已完成',
    processing: '處理中',
    draft: '草稿'
  }
  return texts[status as keyof typeof texts] || '未知'
})

// 方法
const createVideo = () => {
  console.log('創建影片')
  // 跳轉到創建頁面
}

const upgradePlan = () => {
  console.log('升級方案')
  // 跳轉到定價頁面
  window.open('/vidspark-v2/pricing.html', '_blank')
}

const tryDigitalHuman = () => {
  console.log('試用專屬數字人')
}

const startFreeVideo = () => {
  console.log('開始免費數字人影片')
}

const startTextToVideo = () => {
  console.log('開始文字轉影片')
}

const startVoiceClone = () => {
  console.log('開始聲音克隆')
}

const startImitateVideo = () => {
  console.log('開始模仿影片創作')
}

const startPPTVideo = () => {
  console.log('開始PPT/PDF講解')
}

const startCustomDigitalHuman = () => {
  console.log('創建自定義數字人')
}

const openProject = (project: any) => {
  console.log('打開項目:', project)
  // 跳轉到項目詳情或編輯頁面
}

// 生命週期
onMounted(() => {
  console.log('Dashboard 組件已掛載')
})
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