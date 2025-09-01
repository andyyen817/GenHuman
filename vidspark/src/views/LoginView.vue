<template>
  <div class="bg-gray-50 min-h-screen flex">
    <!-- 左側表單區域 -->
    <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-20 xl:px-24">
      <div class="mx-auto w-full max-w-sm lg:w-96">
        <!-- Logo -->
        <div class="flex items-center mb-8">
          <div class="w-10 h-10 bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg flex items-center justify-center mr-3">
            <i class="fas fa-video text-white"></i>
          </div>
          <h1 class="text-2xl font-bold text-gray-900">Vidspark</h1>
        </div>

        <!-- 標題 -->
        <div class="mb-8">
          <h2 class="text-3xl font-bold text-gray-900 mb-2">歡迎回來</h2>
          <p class="text-gray-600">登入您的帳戶繼續創作</p>
        </div>

        <!-- 登入表單 -->
        <form class="space-y-6" @submit.prevent="handleLogin">
          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
              電子郵件地址
            </label>
            <div class="relative">
              <input
                id="email"
                name="email"
                type="email"
                autocomplete="email"
                required
                v-model="loginForm.email"
                class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg input-focus focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus:z-10"
                placeholder="輸入您的 Email"
              >
              <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <i class="fas fa-envelope text-gray-400"></i>
              </div>
            </div>
          </div>

          <!-- 密碼 -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
              密碼
            </label>
            <div class="relative">
              <input
                id="password"
                name="password"
                :type="showPassword ? 'text' : 'password'"
                autocomplete="current-password"
                required
                v-model="loginForm.password"
                class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg input-focus focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus:z-10"
                placeholder="輸入您的密碼"
              >
              <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" 
                   class="text-gray-400 cursor-pointer hover:text-gray-600" 
                   @click="showPassword = !showPassword"></i>
              </div>
            </div>
          </div>

          <!-- 記住我 & 忘記密碼 -->
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input
                id="remember-me"
                name="remember-me"
                type="checkbox"
                v-model="loginForm.rememberMe"
                class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
              >
              <label for="remember-me" class="ml-2 block text-sm text-gray-600">
                記住我
              </label>
            </div>

            <div class="text-sm">
              <a href="#" class="font-medium text-purple-600 hover:text-purple-500">
                忘記密碼？
              </a>
            </div>
          </div>

          <!-- 提交按鈕 -->
          <div>
            <button
              type="submit"
              :disabled="loading"
              class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-300 transform hover:scale-105 disabled:opacity-50"
            >
              <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                <i class="fas fa-sign-in-alt text-purple-500 group-hover:text-purple-400"></i>
              </span>
              {{ loading ? '登入中...' : '立即登入' }}
            </button>
          </div>

          <!-- 分隔線 -->
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-gray-50 text-gray-500">或使用以下方式登入</span>
            </div>
          </div>

          <!-- 第三方登入按鈕 -->
          <div class="grid grid-cols-2 gap-3">
            <button
              type="button"
              class="w-full inline-flex justify-center py-3 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors"
              disabled
            >
              <i class="fab fa-google text-lg"></i>
              <span class="ml-2">Google</span>
            </button>
            <button
              type="button"
              class="w-full inline-flex justify-center py-3 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors"
              disabled
            >
              <i class="fab fa-apple text-lg"></i>
              <span class="ml-2">Apple</span>
            </button>
          </div>

          <!-- 註冊鏈接 -->
          <div class="text-center">
            <span class="text-sm text-gray-600">
              還沒有帳戶？ 
              <router-link to="/register" class="font-medium text-purple-600 hover:text-purple-500">
                立即註冊
              </router-link>
            </span>
          </div>
        </form>

        <!-- 登入提示 -->
        <div class="mt-8 p-4 bg-blue-50 rounded-lg">
          <h3 class="text-sm font-medium text-blue-800 mb-2">
            <i class="fas fa-lightbulb mr-2"></i>
            溫馨提示
          </h3>
          <p class="text-sm text-blue-700">
            首次登入後，系統會自動為您開通免費額度。立即體驗 AI 數字人影片生成的魅力！
          </p>
        </div>
      </div>
    </div>

    <!-- 右側動態背景區域 -->
    <div class="hidden lg:block relative w-0 flex-1">
      <!-- 背景漸變 -->
      <div class="absolute inset-0 video-bg"></div>
      
      <!-- 動態影片元素 -->
      <div class="absolute inset-0 flex items-center justify-center overflow-hidden">
        <!-- 中心播放器 -->
        <div class="relative z-10">
          <div class="bg-white rounded-2xl p-6 shadow-2xl max-w-sm">
            <!-- 影片縮圖 -->
            <div class="relative mb-4">
              <img 
                src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=400&h=200&fit=crop" 
                alt="AI生成影片預覽"
                class="w-full h-32 object-cover rounded-lg"
              >
              <!-- 播放按鈕 -->
              <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-12 h-12 bg-white bg-opacity-90 rounded-full flex items-center justify-center shadow-lg">
                  <i class="fas fa-play text-purple-600 ml-1"></i>
                </div>
              </div>
              <!-- 時長標籤 -->
              <div class="absolute bottom-2 right-2 bg-black bg-opacity-70 text-white text-xs px-2 py-1 rounded">
                01:24
              </div>
            </div>
            
            <!-- 影片信息 -->
            <h3 class="font-semibold text-gray-900 mb-2">產品推廣影片</h3>
            <p class="text-sm text-gray-600 mb-3">
              使用 AI 數字人創建的專業營銷影片
            </p>
            
            <!-- 狀態指示 -->
            <div class="flex items-center justify-between">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                <i class="fas fa-check-circle mr-1"></i>
                已完成
              </span>
              <div class="flex items-center text-sm text-gray-500">
                <i class="fas fa-clock mr-1"></i>
                2分鐘前
              </div>
            </div>
          </div>
        </div>

        <!-- 浮動創作元素 -->
        <div class="floating-element absolute top-20 left-20" style="animation-delay: 0.5s;">
          <div class="bg-white rounded-lg p-3 shadow-lg">
            <div class="flex items-center space-x-2">
              <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-microphone text-purple-600 text-sm"></i>
              </div>
              <span class="text-sm font-medium text-gray-700">聲音克隆</span>
            </div>
          </div>
        </div>

        <div class="floating-element absolute top-32 right-24" style="animation-delay: 1s;">
          <div class="bg-white rounded-lg p-3 shadow-lg">
            <div class="flex items-center space-x-2">
              <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-user-tie text-blue-600 text-sm"></i>
              </div>
              <span class="text-sm font-medium text-gray-700">AI 數字人</span>
            </div>
          </div>
        </div>

        <div class="floating-element absolute bottom-32 left-32" style="animation-delay: 1.5s;">
          <div class="bg-white rounded-lg p-3 shadow-lg">
            <div class="flex items-center space-x-2">
              <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-magic text-green-600 text-sm"></i>
              </div>
              <span class="text-sm font-medium text-gray-700">AI 生成</span>
            </div>
          </div>
        </div>

        <div class="floating-element absolute bottom-20 right-20" style="animation-delay: 2s;">
          <div class="bg-white rounded-lg p-3 shadow-lg">
            <div class="flex items-center space-x-2">
              <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-download text-yellow-600 text-sm"></i>
              </div>
              <span class="text-sm font-medium text-gray-700">一鍵導出</span>
            </div>
          </div>
        </div>

        <!-- 裝飾圓點 -->
        <div class="absolute top-16 right-16 w-3 h-3 bg-yellow-300 rounded-full opacity-60"></div>
        <div class="absolute bottom-24 left-24 w-2 h-2 bg-pink-300 rounded-full opacity-60"></div>
        <div class="absolute top-1/2 left-16 w-4 h-4 bg-blue-300 rounded-full opacity-60"></div>
        
        <!-- 底部文字 -->
        <div class="absolute bottom-8 left-8 right-8 text-center">
          <h3 class="text-2xl font-bold text-white mb-2">繼續您的創作之旅</h3>
          <p class="text-white/80">登入後立即訪問您的所有項目和創作工具</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

// 表單數據
const loginForm = ref({
  email: '',
  password: '',
  rememberMe: false
});

const showPassword = ref(false);
const loading = ref(false);

// 登入處理
const handleLogin = async () => {
  loading.value = true;
  
  try {
    // TODO: 實現實際的登入邏輯
    console.log('登入資料:', loginForm.value);
    
    // 模擬登入延遲
    await new Promise(resolve => setTimeout(resolve, 1000));
    
    // 登入成功後跳轉到儀表板
    router.push('/dashboard');
  } catch (error) {
    console.error('登入失敗:', error);
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.video-bg {
  background: linear-gradient(45deg, #667eea, #764ba2);
}

.floating-element {
  animation: float 4s ease-in-out infinite;
}

@keyframes float {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(-15px) rotate(2deg); }
}

.input-focus {
  transition: all 0.3s ease;
}

.input-focus:focus {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(93, 95, 239, 0.15);
}
</style>