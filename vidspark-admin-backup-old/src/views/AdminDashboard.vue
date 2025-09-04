<template>
  <div class="bg-gray-50 min-h-screen flex">
    <!-- 左側導航欄 -->
    <div class="w-64 sidebar-dark flex flex-col">
      <!-- Logo區域 -->
      <div class="p-6 border-b border-gray-700">
        <div class="flex items-center">
          <div class="w-8 h-8 bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg flex items-center justify-center mr-3">
            <i class="fas fa-cog text-white text-sm"></i>
          </div>
          <h1 class="text-xl font-bold text-white">Vidspark Admin</h1>
        </div>
      </div>

      <!-- 主導航 -->
      <nav class="flex-1 px-4 py-4">
        <ul class="space-y-2">
          <li>
            <router-link 
              to="/" 
              class="flex items-center px-4 py-3 text-white bg-purple-600 rounded-lg"
            >
              <i class="fas fa-tachometer-alt mr-3"></i>
              {{ $t('nav.dashboard') }}
            </router-link>
          </li>
          <li>
            <a href="#api-testing" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
              <i class="fas fa-flask mr-3"></i>
              API 測試
            </a>
          </li>
          <li>
            <router-link 
              to="/users" 
              class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
            >
              <i class="fas fa-users mr-3"></i>
              {{ $t('nav.users') }}
            </router-link>
          </li>
          <li>
            <router-link 
              to="/i18n" 
              class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
            >
              <i class="fas fa-language mr-3"></i>
              {{ $t('nav.i18n') }}
            </router-link>
          </li>
          <li>
            <router-link 
              to="/settings" 
              class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
            >
              <i class="fas fa-cogs mr-3"></i>
              {{ $t('nav.settings') }}
            </router-link>
          </li>
        </ul>
      </nav>

      <!-- 底部用戶信息 -->
      <div class="p-4 border-t border-gray-700">
        <div class="flex items-center">
          <img 
            src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face" 
            alt="管理員頭像" 
            class="w-10 h-10 rounded-full"
          >
          <div class="ml-3 flex-1">
            <p class="text-white text-sm font-medium">系統管理員</p>
            <p class="text-gray-400 text-xs">超級管理員</p>
          </div>
        </div>
      </div>
    </div>

    <!-- 右側主內容區 -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- 頂部欄 -->
      <header class="bg-white border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">管理控制台</h1>
            <p class="text-gray-600">Vidspark 系統管理與監控</p>
          </div>
          
          <div class="flex items-center space-x-4">
            <!-- API狀態指示器 -->
            <div class="flex items-center space-x-2">
              <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
              <span class="text-sm text-gray-600">API 正常</span>
            </div>
            
            <!-- 語言切換 -->
            <LanguageSwitcher />
            
            <!-- 通知 -->
            <button class="relative p-2 text-gray-500 hover:text-gray-700 transition-colors">
              <i class="fas fa-bell text-xl"></i>
              <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>

            <!-- 登出按鈕 -->
            <button 
              @click="logout"
              class="flex items-center px-3 py-2 text-gray-600 hover:text-red-600 transition-colors"
              title="登出"
            >
              <i class="fas fa-sign-out-alt mr-2"></i>
              <span class="text-sm">登出</span>
            </button>
          </div>
        </div>
      </header>

      <!-- 主內容滾動區域 -->
      <main class="flex-1 overflow-y-auto p-6">
        <!-- 統計卡片區域 -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- 今日用戶數 -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600">今日活躍用戶</p>
                <p class="text-3xl font-bold text-gray-900">{{ stats.activeUsers.toLocaleString() }}</p>
                <p class="text-sm text-green-600 mt-1">
                  <i class="fas fa-arrow-up mr-1"></i>
                  +{{ stats.activeUsersGrowth }}% 較昨日
                </p>
              </div>
              <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-blue-600 text-xl"></i>
              </div>
            </div>
          </div>

          <!-- 影片生成數 -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600">今日影片生成</p>
                <p class="text-3xl font-bold text-gray-900">{{ stats.videosGenerated.toLocaleString() }}</p>
                <p class="text-sm text-green-600 mt-1">
                  <i class="fas fa-arrow-up mr-1"></i>
                  +{{ stats.videosGrowth }}% 較昨日
                </p>
              </div>
              <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-video text-purple-600 text-xl"></i>
              </div>
            </div>
          </div>

          <!-- API 調用次數 -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600">API 調用次數</p>
                <p class="text-3xl font-bold text-gray-900">{{ stats.apiCalls.toLocaleString() }}</p>
                <p class="text-sm text-yellow-600 mt-1">
                  <i class="fas fa-arrow-right mr-1"></i>
                  穩定運行
                </p>
              </div>
              <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-flask text-green-600 text-xl"></i>
              </div>
            </div>
          </div>

          <!-- 系統健康度 -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600">系統健康度</p>
                <p class="text-3xl font-bold text-gray-900">{{ stats.systemHealth }}%</p>
                <p class="text-sm text-green-600 mt-1">
                  <i class="fas fa-check mr-1"></i>
                  運行正常
                </p>
              </div>
              <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-heartbeat text-red-600 text-xl"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- GenHuman API 快速測試區域 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-6">GenHuman API 快速測試</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- 免費數字人合成測試 -->
            <div class="border border-gray-200 rounded-lg p-4">
              <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">免費數字人合成</h3>
                <span class="api-status-success text-white text-xs font-bold px-2 py-1 rounded-full">正常</span>
              </div>
              <p class="text-sm text-gray-600 mb-4">
                測試端點：/app/human/human/Index/created
              </p>
              <div class="space-y-3">
                <input 
                  v-model="apiTests.humanSynthesis.callbackUrl"
                  type="text" 
                  placeholder="回調地址" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                >
                <input 
                  v-model="apiTests.humanSynthesis.sceneTaskId"
                  type="text" 
                  placeholder="場景任務ID" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                >
                <button 
                  @click="testHumanSynthesis"
                  :disabled="isApiTesting"
                  class="w-full bg-purple-600 hover:bg-purple-700 disabled:opacity-50 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors"
                >
                  <span v-if="isApiTesting">測試中...</span>
                  <span v-else>測試 API</span>
                </button>
              </div>
            </div>

            <!-- 免費聲音克隆測試 -->
            <div class="border border-gray-200 rounded-lg p-4">
              <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">免費聲音克隆</h3>
                <span class="api-status-success text-white text-xs font-bold px-2 py-1 rounded-full">正常</span>
              </div>
              <p class="text-sm text-gray-600 mb-4">
                測試端點：/app/human/human/Voice/clone
              </p>
              <div class="space-y-3">
                <input 
                  v-model="apiTests.voiceClone.customName"
                  type="text" 
                  placeholder="自定義名稱" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                >
                <input 
                  v-model="apiTests.voiceClone.audioUrl"
                  type="text" 
                  placeholder="音頻地址" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                >
                <button 
                  @click="testVoiceClone"
                  :disabled="isApiTesting"
                  class="w-full bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors"
                >
                  <span v-if="isApiTesting">測試中...</span>
                  <span v-else>測試 API</span>
                </button>
              </div>
            </div>

            <!-- 任務查詢測試 -->
            <div class="border border-gray-200 rounded-lg p-4">
              <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">任務狀態查詢</h3>
                <span class="api-status-success text-white text-xs font-bold px-2 py-1 rounded-full">正常</span>
              </div>
              <p class="text-sm text-gray-600 mb-4">
                測試端點：/app/human/human/Musetalk/task
              </p>
              <div class="space-y-3">
                <input 
                  v-model="apiTests.taskQuery.taskId"
                  type="text" 
                  placeholder="任務ID" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                <button 
                  @click="testTaskQuery"
                  :disabled="isApiTesting"
                  class="w-full bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors"
                >
                  <span v-if="isApiTesting">查詢中...</span>
                  <span v-else>查詢狀態</span>
                </button>
                <div class="bg-gray-50 rounded-lg p-3 text-sm">
                  <p class="text-gray-600">最近查詢結果：</p>
                  <p :class="[
                    'font-medium',
                    lastQueryResult.success ? 'text-green-600' : 'text-red-600'
                  ]">
                    {{ lastQueryResult.message }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 快速操作按鈕 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">快速操作</h3>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <button 
              @click="$router.push('/users')"
              class="flex flex-col items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors"
            >
              <i class="fas fa-users text-blue-600 text-2xl mb-2"></i>
              <span class="text-sm font-medium text-blue-800">{{ $t('nav.users') }}</span>
            </button>
            <button 
              @click="$router.push('/i18n')"
              class="flex flex-col items-center p-4 bg-green-50 hover:bg-green-100 rounded-lg transition-colors"
            >
              <i class="fas fa-language text-green-600 text-2xl mb-2"></i>
              <span class="text-sm font-medium text-green-800">{{ $t('nav.i18n') }}</span>
            </button>
            <button 
              @click="$router.push('/settings')"
              class="flex flex-col items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors"
            >
              <i class="fas fa-cogs text-purple-600 text-2xl mb-2"></i>
              <span class="text-sm font-medium text-purple-800">{{ $t('nav.settings') }}</span>
            </button>
            <button 
              @click="refreshData"
              class="flex flex-col items-center p-4 bg-orange-50 hover:bg-orange-100 rounded-lg transition-colors"
            >
              <i class="fas fa-sync-alt text-orange-600 text-2xl mb-2"></i>
              <span class="text-sm font-medium text-orange-800">刷新數據</span>
            </button>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import LanguageSwitcher from '@/components/LanguageSwitcher.vue'

// 路由和國際化
const router = useRouter()
const { t } = useI18n()

// 響應式數據
const isApiTesting = ref(false)

// 統計數據
const stats = reactive({
  activeUsers: 1234,
  activeUsersGrowth: 12,
  videosGenerated: 3567,
  videosGrowth: 8,
  apiCalls: 45678,
  systemHealth: 99.2
})

// API 測試表單
const apiTests = reactive({
  humanSynthesis: {
    callbackUrl: '',
    sceneTaskId: ''
  },
  voiceClone: {
    customName: '',
    audioUrl: ''
  },
  taskQuery: {
    taskId: ''
  }
})

// 最後查詢結果
const lastQueryResult = reactive({
  success: true,
  message: '任務完成 - 狀態碼：20'
})

// 時間間隔ID
let statsInterval: number | null = null

// 方法
const testHumanSynthesis = async () => {
  if (isApiTesting.value) return
  
  isApiTesting.value = true
  console.log('🧪 測試數字人合成 API:', apiTests.humanSynthesis)
  
  try {
    await new Promise(resolve => setTimeout(resolve, 2000))
    console.log('✅ 數字人合成測試成功')
    alert('數字人合成 API 測試成功！')
  } catch (error) {
    console.error('❌ 數字人合成測試失敗:', error)
    alert('數字人合成 API 測試失敗！')
  } finally {
    isApiTesting.value = false
  }
}

const testVoiceClone = async () => {
  if (isApiTesting.value) return
  
  isApiTesting.value = true
  console.log('🎤 測試聲音克隆 API:', apiTests.voiceClone)
  
  try {
    await new Promise(resolve => setTimeout(resolve, 2000))
    console.log('✅ 聲音克隆測試成功')
    alert('聲音克隆 API 測試成功！')
  } catch (error) {
    console.error('❌ 聲音克隆測試失敗:', error)
    alert('聲音克隆 API 測試失敗！')
  } finally {
    isApiTesting.value = false
  }
}

const testTaskQuery = async () => {
  if (isApiTesting.value) return
  
  isApiTesting.value = true
  console.log('🔍 測試任務查詢 API:', apiTests.taskQuery)
  
  try {
    await new Promise(resolve => setTimeout(resolve, 1500))
    
    const success = Math.random() > 0.3
    if (success) {
      lastQueryResult.success = true
      lastQueryResult.message = '任務完成 - 狀態碼：20'
      console.log('✅ 任務查詢測試成功')
    } else {
      lastQueryResult.success = false
      lastQueryResult.message = '任務失敗 - 狀態碼：30'
      console.log('⚠️ 任務查詢返回失敗狀態')
    }
  } catch (error) {
    lastQueryResult.success = false
    lastQueryResult.message = 'API 調用失敗'
    console.error('❌ 任務查詢測試失敗:', error)
  } finally {
    isApiTesting.value = false
  }
}

const logout = () => {
  if (confirm('確定要登出嗎？')) {
    localStorage.removeItem('vidspark_admin_auth')
    sessionStorage.removeItem('vidspark_admin_auth')
    console.log('🚪 管理員已登出')
    router.push('/login')
  }
}

const refreshData = () => {
  console.log('🔄 刷新數據')
  updateStats()
  alert('數據已刷新！')
}

const updateStats = () => {
  stats.activeUsers += Math.floor(Math.random() * 5) - 2
  stats.videosGenerated += Math.floor(Math.random() * 10)
  stats.apiCalls += Math.floor(Math.random() * 100)
}

// 生命周期
onMounted(() => {
  console.log('🚀 管理後台儀表板已載入')
  statsInterval = setInterval(updateStats, 30000)
})

onUnmounted(() => {
  if (statsInterval) {
    clearInterval(statsInterval)
  }
})
</script>

<style scoped>
.sidebar-dark {
  background: linear-gradient(180deg, #1a1c20 0%, #2d2f36 100%);
}

.api-status-success {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
