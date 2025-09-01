<template>
  <div class="admin-login-container bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900 min-h-screen flex items-center justify-center px-4">
    <!-- 背景裝飾 -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="absolute -top-10 -left-10 w-80 h-80 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob"></div>
      <div class="absolute -top-10 -right-10 w-80 h-80 bg-blue-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-2000"></div>
      <div class="absolute -bottom-10 left-20 w-80 h-80 bg-indigo-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-4000"></div>
    </div>

    <!-- 登入卡片 -->
    <div class="relative bg-white bg-opacity-10 backdrop-blur-lg rounded-2xl shadow-2xl border border-white border-opacity-20 p-8 w-full max-w-md">
      <!-- Logo 區域 -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-purple-600 to-blue-600 rounded-xl mb-4">
          <i class="fas fa-video text-white text-2xl"></i>
        </div>
        <h1 class="text-3xl font-bold text-white mb-2">Vidspark</h1>
        <p class="text-gray-200 text-lg">{{ $t('admin.login.title') }}</p>
        <div class="w-12 h-0.5 bg-gradient-to-r from-purple-400 to-blue-400 mx-auto mt-3"></div>
      </div>

      <!-- 錯誤消息 -->
      <div v-if="errorMessage" class="mb-6 p-4 bg-red-500 bg-opacity-20 border border-red-400 border-opacity-50 rounded-lg">
        <div class="flex items-center">
          <i class="fas fa-exclamation-triangle text-red-300 mr-3"></i>
          <span class="text-red-100 text-sm">{{ errorMessage }}</span>
        </div>
      </div>

      <!-- 登入表單 -->
      <form @submit.prevent="handleLogin" class="space-y-6">
        <!-- 管理員帳號 -->
        <div class="space-y-2">
          <label class="block text-sm font-medium text-gray-200">
            {{ $t('admin.login.username') }}
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-user-shield text-gray-400"></i>
            </div>
            <input
              v-model="loginForm.username"
              type="text"
              required
              :placeholder="$t('admin.login.usernamePlaceholder')"
              class="w-full pl-10 pr-4 py-3 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-white placeholder-gray-300 backdrop-blur-sm transition-all duration-200"
            >
          </div>
        </div>

        <!-- 密碼 -->
        <div class="space-y-2">
          <label class="block text-sm font-medium text-gray-200">
            {{ $t('admin.login.password') }}
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-lock text-gray-400"></i>
            </div>
            <input
              v-model="loginForm.password"
              :type="showPassword ? 'text' : 'password'"
              required
              :placeholder="$t('admin.login.passwordPlaceholder')"
              class="w-full pl-10 pr-12 py-3 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-white placeholder-gray-300 backdrop-blur-sm transition-all duration-200"
            >
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-200 transition-colors"
            >
              <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
            </button>
          </div>
        </div>

        <!-- 記住登入 -->
        <div class="flex items-center justify-between">
          <label class="flex items-center">
            <input
              v-model="loginForm.rememberMe"
              type="checkbox"
              class="h-4 w-4 text-purple-600 bg-white bg-opacity-20 border-gray-300 border-opacity-30 rounded focus:ring-purple-500 focus:ring-2"
            >
            <span class="ml-2 text-sm text-gray-200">{{ $t('admin.login.rememberMe') }}</span>
          </label>
        </div>

        <!-- 登入按鈕 -->
        <button
          type="submit"
          :disabled="isLoading"
          class="w-full bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg"
        >
          <div v-if="isLoading" class="flex items-center justify-center">
            <div class="animate-spin rounded-full h-5 w-5 border-2 border-white border-t-transparent mr-3"></div>
            {{ $t('admin.login.loggingIn') }}
          </div>
          <div v-else class="flex items-center justify-center">
            <i class="fas fa-sign-in-alt mr-2"></i>
            {{ $t('admin.login.loginButton') }}
          </div>
        </button>
      </form>

      <!-- 安全提示 -->
      <div class="mt-8 pt-6 border-t border-gray-300 border-opacity-20">
        <div class="flex items-center justify-center text-gray-300 text-xs">
          <i class="fas fa-shield-alt mr-2"></i>
          <span>{{ $t('admin.login.securityNote') }}</span>
        </div>
      </div>

      <!-- 語言切換 -->
      <div class="mt-6 flex justify-center">
        <LanguageSwitcher variant="transparent" />
      </div>
    </div>

    <!-- 版本信息 -->
    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 text-gray-400 text-xs">
      Vidspark Admin v1.0.0
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import LanguageSwitcher from '@/components/LanguageSwitcher.vue'

// 路由和國際化
const router = useRouter()
const { t } = useI18n()

// 響應式數據
const isLoading = ref(false)
const showPassword = ref(false)
const errorMessage = ref('')

// 登入表單
const loginForm = reactive({
  username: '',
  password: '',
  rememberMe: false
})

// 登入處理
const handleLogin = async () => {
  if (!loginForm.username || !loginForm.password) {
    errorMessage.value = t('admin.login.errors.emptyFields')
    return
  }

  isLoading.value = true
  errorMessage.value = ''

  try {
    console.log('🔐 管理員登入嘗試:', {
      username: loginForm.username,
      timestamp: new Date().toISOString()
    })

    // 模擬登入 API 調用
    await new Promise(resolve => setTimeout(resolve, 1500))

    // 簡單的管理員驗證邏輯（實際開發中應該調用後端API）
    if (loginForm.username === 'admin' && loginForm.password === 'vidspark2024') {
      console.log('✅ 管理員登入成功')
      
      // 保存登入狀態
      const loginData = {
        username: loginForm.username,
        loginTime: new Date().toISOString(),
        rememberMe: loginForm.rememberMe
      }

      if (loginForm.rememberMe) {
        localStorage.setItem('vidspark_admin_auth', JSON.stringify(loginData))
      } else {
        sessionStorage.setItem('vidspark_admin_auth', JSON.stringify(loginData))
      }

      // 跳轉到管理後台
      router.push('/')
    } else {
      console.log('❌ 管理員登入失敗: 帳號或密碼錯誤')
      errorMessage.value = t('admin.login.errors.invalidCredentials')
    }
  } catch (error) {
    console.error('❌ 登入錯誤:', error)
    errorMessage.value = t('admin.login.errors.serverError')
  } finally {
    isLoading.value = false
  }
}

// 檢查是否已經登入
const checkAuthStatus = () => {
  const authData = localStorage.getItem('vidspark_admin_auth') || 
                   sessionStorage.getItem('vidspark_admin_auth')
  
  if (authData) {
    try {
      const { loginTime } = JSON.parse(authData)
      const timeDiff = Date.now() - new Date(loginTime).getTime()
      const hoursDiff = timeDiff / (1000 * 60 * 60)
      
      // 如果登入時間超過24小時，清除登入狀態
      if (hoursDiff > 24) {
        localStorage.removeItem('vidspark_admin_auth')
        sessionStorage.removeItem('vidspark_admin_auth')
      } else {
        // 還在有效期內，直接跳轉到後台
        router.push('/')
      }
    } catch (error) {
      console.error('解析登入狀態錯誤:', error)
      localStorage.removeItem('vidspark_admin_auth')
      sessionStorage.removeItem('vidspark_admin_auth')
    }
  }
}

// 生命周期
onMounted(() => {
  console.log('🚀 管理後台登入頁面已載入')
  checkAuthStatus()
})
</script>

<style scoped>
/* 背景動畫 */
@keyframes blob {
  0% {
    transform: translate(0px, 0px) scale(1);
  }
  33% {
    transform: translate(30px, -50px) scale(1.1);
  }
  66% {
    transform: translate(-20px, 20px) scale(0.9);
  }
  100% {
    transform: translate(0px, 0px) scale(1);
  }
}

.animate-blob {
  animation: blob 7s infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}

/* 玻璃效果增強 */
.admin-login-container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.05) 50%, transparent 70%);
  pointer-events: none;
}

/* 輸入框焦點效果 */
input:focus {
  transform: translateY(-1px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* 按鈕懸停效果 */
button:hover:not(:disabled) {
  box-shadow: 0 10px 25px rgba(139, 92, 246, 0.3);
}

/* 響應式設計 */
@media (max-width: 640px) {
  .admin-login-container > div {
    margin: 1rem;
    padding: 1.5rem;
  }
}
</style>
