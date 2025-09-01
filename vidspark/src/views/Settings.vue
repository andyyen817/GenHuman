<template>
  <div class="bg-gray-50 min-h-screen flex">
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
          @click="goToCreateVideo"
          class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 px-4 rounded-lg font-medium transition-colors flex items-center justify-center"
        >
          <i class="fas fa-plus mr-2"></i>
          {{ $t('navigation.create_video') }}
        </button>
      </div>

      <!-- 主導航 -->
      <nav class="flex-1 px-4">
        <ul class="space-y-2">
          <li>
            <router-link 
              to="/dashboard" 
              class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
            >
              <i class="fas fa-home mr-3"></i>
              {{ $t('navigation.dashboard') }}
            </router-link>
          </li>
          <li>
            <router-link 
              to="/my-videos" 
              class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
            >
              <i class="fas fa-folder mr-3"></i>
              {{ $t('navigation.my_videos') }}
            </router-link>
          </li>
          <li>
            <router-link 
              to="/digital-humans" 
              class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
            >
              <i class="fas fa-user-tie mr-3"></i>
              {{ $t('navigation.digital_humans') }}
            </router-link>
          </li>
          <li>
            <router-link 
              to="/voice-clone" 
              class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
            >
              <i class="fas fa-microphone mr-3"></i>
              {{ $t('navigation.voice_clone') }}
            </router-link>
          </li>
          <li>
            <router-link 
              to="/templates" 
              class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
            >
              <i class="fas fa-layer-group mr-3"></i>
              {{ $t('navigation.templates') }}
            </router-link>
          </li>
          <li>
            <a href="#" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
              <i class="fas fa-images mr-3"></i>
              媒體庫
            </a>
          </li>
        </ul>

        <!-- 分隔線 -->
        <div class="my-6 border-t border-gray-600"></div>

        <ul class="space-y-2">
          <li>
            <router-link 
              to="/settings" 
              class="flex items-center px-4 py-3 text-white bg-purple-600 rounded-lg"
            >
              <i class="fas fa-cog mr-3"></i>
              {{ $t('navigation.settings') }}
            </router-link>
          </li>
        </ul>
      </nav>

      <!-- 底部用戶信息 -->
      <div class="p-4 border-t border-gray-700">
        <div class="flex items-center">
          <img 
            :src="userProfile.avatar" 
            alt="用戶頭像" 
            class="w-10 h-10 rounded-full object-cover"
          >
          <div class="ml-3 flex-1">
            <p class="text-white text-sm font-medium">{{ userProfile.name }}</p>
            <p class="text-gray-400 text-xs">{{ userProfile.plan }}</p>
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
            <h1 class="text-2xl font-bold text-gray-900">{{ $t('navigation.settings') }}</h1>
            <p class="text-gray-600">管理您的帳戶和偏好設定</p>
          </div>
        </div>
      </header>

      <!-- 主內容區 -->
      <main class="flex-1 overflow-y-auto">
        <div class="max-w-4xl mx-auto p-6">
          <!-- 設置導航 -->
          <div class="flex space-x-8 mb-8 border-b border-gray-200">
            <button 
              v-for="tab in settingsTabs"
              :key="tab.id"
              @click="activeTab = tab.id"
              :class="[
                'pb-3 font-medium transition-colors',
                activeTab === tab.id 
                  ? 'border-b-2 border-purple-600 text-purple-600' 
                  : 'text-gray-500 hover:text-gray-700'
              ]"
            >
              {{ tab.name }}
            </button>
          </div>

          <div class="space-y-8">
            <!-- 個人資料卡片 -->
            <div v-if="activeTab === 'profile'" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-6">個人資料</h2>
              
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- 頭像上傳 -->
                <div class="md:col-span-1">
                  <label class="block text-sm font-medium text-gray-700 mb-3">個人頭像</label>
                  <div class="flex items-center space-x-4">
                    <img 
                      :src="userProfile.avatar" 
                      alt="用戶頭像" 
                      class="w-20 h-20 rounded-full object-cover"
                    >
                    <div>
                      <button 
                        @click="changeAvatar"
                        class="text-purple-600 hover:text-purple-700 font-medium text-sm transition-colors"
                      >
                        更換頭像
                      </button>
                      <p class="text-xs text-gray-500 mt-1">支援 JPG、PNG 格式</p>
                    </div>
                  </div>
                </div>

                <!-- 基本信息 -->
                <div class="md:col-span-2 space-y-4">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">姓名</label>
                      <input 
                        v-model="userProfile.name"
                        type="text" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                      >
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">電子郵件</label>
                      <input 
                        v-model="userProfile.email"
                        type="email" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                      >
                    </div>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">公司/組織</label>
                    <input 
                      v-model="userProfile.company"
                      type="text" 
                      placeholder="選填" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                    >
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">個人簡介</label>
                    <textarea 
                      v-model="userProfile.bio"
                      rows="3" 
                      placeholder="介紹一下您自己..." 
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 resize-none"
                    ></textarea>
                  </div>
                </div>
              </div>

              <div class="flex justify-end mt-6 pt-6 border-t border-gray-200">
                <button 
                  @click="saveProfile"
                  class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-medium transition-colors"
                >
                  保存變更
                </button>
              </div>
            </div>

            <!-- 會員方案卡片 -->
            <div v-if="activeTab === 'subscription'" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-6">會員方案</h2>
              
              <!-- 當前方案 -->
              <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="font-semibold text-gray-900">{{ subscriptionInfo.currentPlan }}</h3>
                    <p class="text-sm text-gray-600">{{ subscriptionInfo.description }}</p>
                  </div>
                  <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                    當前方案
                  </span>
                </div>
              </div>

              <!-- 額度使用情況 -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-blue-50 rounded-lg p-4">
                  <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-blue-900">數字人影片</span>
                    <span class="text-sm font-semibold text-blue-900">{{ subscriptionInfo.videoUsed }}/{{ subscriptionInfo.videoLimit }} 今日</span>
                  </div>
                  <div class="w-full bg-blue-200 rounded-full h-2">
                    <div 
                      class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                      :style="{ width: (subscriptionInfo.videoUsed / subscriptionInfo.videoLimit * 100) + '%' }"
                    ></div>
                  </div>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                  <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-green-900">聲音克隆</span>
                    <span class="text-sm font-semibold text-green-900">{{ subscriptionInfo.voiceUsed }}/{{ subscriptionInfo.voiceLimit }} 總計</span>
                  </div>
                  <div class="w-full bg-green-200 rounded-full h-2">
                    <div 
                      class="bg-green-600 h-2 rounded-full transition-all duration-300" 
                      :style="{ width: (subscriptionInfo.voiceUsed / subscriptionInfo.voiceLimit * 100) + '%' }"
                    ></div>
                  </div>
                </div>
              </div>

              <!-- 升級按鈕 -->
              <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg p-6">
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="font-semibold text-gray-900 mb-1">升級到專業版</h3>
                    <p class="text-sm text-gray-600">解鎖更多數字人、無限聲音克隆、高級功能</p>
                  </div>
                  <button 
                    @click="viewPlans"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors"
                  >
                    查看方案
                  </button>
                </div>
              </div>
            </div>

            <!-- 語言偏好卡片 -->
            <div v-if="activeTab === 'language'" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-6">語言偏好</h2>
              
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">界面語言</label>
                  <select 
                    v-model="languageSettings.locale"
                    @change="changeLanguage"
                    class="w-full md:w-64 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                  >
                    <option value="zh-TW">繁體中文</option>
                    <option value="zh-CN">简体中文</option>
                    <option value="en">English</option>
                  </select>
                  <p class="text-xs text-gray-500 mt-1">更改語言將立即生效</p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">時區</label>
                  <select 
                    v-model="languageSettings.timezone"
                    class="w-full md:w-64 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                  >
                    <option value="Asia/Taipei">台北 (GMT+8)</option>
                    <option value="Asia/Shanghai">上海 (GMT+8)</option>
                    <option value="America/New_York">紐約 (GMT-5)</option>
                    <option value="Europe/London">倫敦 (GMT+0)</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- 通知設定卡片 -->
            <div v-if="activeTab === 'notifications'" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-6">通知設定</h2>
              
              <div class="space-y-6">
                <!-- Email 通知 -->
                <div>
                  <h3 class="font-medium text-gray-900 mb-4">Email 通知</h3>
                  <div class="space-y-3">
                    <label 
                      v-for="notification in emailNotifications"
                      :key="notification.id"
                      class="flex items-center"
                    >
                      <input 
                        v-model="notification.enabled"
                        type="checkbox" 
                        class="rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                      >
                      <span class="ml-3 text-sm text-gray-700">{{ notification.label }}</span>
                    </label>
                  </div>
                </div>

                <!-- 瀏覽器通知 -->
                <div>
                  <h3 class="font-medium text-gray-900 mb-4">瀏覽器通知</h3>
                  <div class="space-y-3">
                    <label 
                      v-for="notification in browserNotifications"
                      :key="notification.id"
                      class="flex items-center"
                    >
                      <input 
                        v-model="notification.enabled"
                        type="checkbox" 
                        class="rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                      >
                      <span class="ml-3 text-sm text-gray-700">{{ notification.label }}</span>
                    </label>
                  </div>
                </div>
              </div>

              <div class="flex justify-end mt-6 pt-6 border-t border-gray-200">
                <button 
                  @click="saveNotifications"
                  class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-medium transition-colors"
                >
                  保存設置
                </button>
              </div>
            </div>

            <!-- 帳戶安全卡片 -->
            <div v-if="activeTab === 'security'" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <h2 class="text-lg font-semibold text-gray-900 mb-6">帳戶安全</h2>
              
              <div class="space-y-6">
                <!-- 密碼更改 -->
                <div>
                  <h3 class="font-medium text-gray-900 mb-4">變更密碼</h3>
                  <div class="space-y-4 max-w-md">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">目前密碼</label>
                      <input 
                        v-model="passwordForm.currentPassword"
                        type="password" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                      >
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">新密碼</label>
                      <input 
                        v-model="passwordForm.newPassword"
                        type="password" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                      >
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">確認新密碼</label>
                      <input 
                        v-model="passwordForm.confirmPassword"
                        type="password" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                      >
                    </div>
                    <button 
                      @click="updatePassword"
                      class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
                    >
                      更新密碼
                    </button>
                  </div>
                </div>

                <!-- 帳戶操作 -->
                <div class="pt-6 border-t border-gray-200">
                  <h3 class="font-medium text-gray-900 mb-4">帳戶操作</h3>
                  <div class="space-y-3">
                    <button 
                      @click="exportData"
                      class="flex items-center text-gray-600 hover:text-gray-800 text-sm font-medium transition-colors"
                    >
                      <i class="fas fa-download mr-2"></i>
                      導出我的數據
                    </button>
                    <button 
                      @click="deleteAccount"
                      class="flex items-center text-red-600 hover:text-red-700 text-sm font-medium transition-colors"
                    >
                      <i class="fas fa-trash mr-2"></i>
                      刪除帳戶
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useUserStore } from '@/stores/user'

// 路由和國際化
const router = useRouter()
const { locale } = useI18n()
const userStore = useUserStore()

// 響應式數據
const activeTab = ref('profile')

const settingsTabs = [
  { id: 'profile', name: '個人資料' },
  { id: 'subscription', name: '會員方案' },
  { id: 'language', name: '語言偏好' },
  { id: 'notifications', name: '通知設定' },
  { id: 'security', name: '隱私安全' }
]

// 用戶資料
const userProfile = reactive({
  name: '張小明',
  email: 'zhang@example.com',
  company: '',
  bio: '',
  avatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=80&h=80&fit=crop&crop=face',
  plan: '免費會員'
})

// 會員信息
const subscriptionInfo = reactive({
  currentPlan: '免費方案',
  description: '每日3次免費數字人影片生成',
  videoUsed: 1,
  videoLimit: 3,
  voiceUsed: 0,
  voiceLimit: 1
})

// 語言設置
const languageSettings = reactive({
  locale: 'zh-TW',
  timezone: 'Asia/Taipei'
})

// 通知設置
const emailNotifications = reactive([
  { id: 'video_complete', label: '影片生成完成通知', enabled: true },
  { id: 'system_update', label: '系統維護和更新通知', enabled: true },
  { id: 'new_features', label: '產品新功能和使用技巧', enabled: false },
  { id: 'promotions', label: '促銷活動和優惠信息', enabled: false }
])

const browserNotifications = reactive([
  { id: 'video_status', label: '影片處理狀態更新', enabled: true },
  { id: 'quota_warning', label: '額度使用提醒', enabled: false }
])

// 密碼表單
const passwordForm = reactive({
  currentPassword: '',
  newPassword: '',
  confirmPassword: ''
})

// 方法
const goToCreateVideo = () => {
  router.push('/create-video')
}

const changeAvatar = () => {
  console.log('更換頭像')
}

const saveProfile = () => {
  console.log('保存個人資料', userProfile)
  alert('個人資料已保存！')
}

const viewPlans = () => {
  console.log('查看會員方案')
}

const changeLanguage = () => {
  locale.value = languageSettings.locale
  console.log('語言已更改為:', languageSettings.locale)
}

const saveNotifications = () => {
  console.log('保存通知設置')
  alert('通知設置已保存！')
}

const updatePassword = () => {
  if (passwordForm.newPassword !== passwordForm.confirmPassword) {
    alert('新密碼與確認密碼不匹配！')
    return
  }
  
  console.log('更新密碼')
  alert('密碼已更新！')
  
  passwordForm.currentPassword = ''
  passwordForm.newPassword = ''
  passwordForm.confirmPassword = ''
}

const exportData = () => {
  console.log('導出用戶數據')
  alert('數據導出功能開發中...')
}

const deleteAccount = () => {
  if (confirm('確定要刪除帳戶嗎？此操作無法撤銷！')) {
    console.log('刪除帳戶')
    alert('帳戶刪除功能開發中...')
  }
}

onMounted(() => {
  console.log('Settings 組件已掛載')
})
</script>

<style scoped>
.sidebar-dark {
  background: linear-gradient(180deg, #1a1c20 0%, #2d2f36 100%);
}
</style>
