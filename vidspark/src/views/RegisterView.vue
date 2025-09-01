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
          <h2 class="text-3xl font-bold text-gray-900 mb-2">創建您的帳戶</h2>
          <p class="text-gray-600">開始您的 AI 影片創作之旅</p>
        </div>

        <!-- 註冊表單 -->
        <form class="space-y-6" @submit.prevent="handleRegister">
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
                v-model="registerForm.email"
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
              設定密碼
            </label>
            <div class="relative">
              <input
                id="password"
                name="password"
                :type="showPassword ? 'text' : 'password'"
                autocomplete="new-password"
                required
                v-model="registerForm.password"
                @input="calculatePasswordStrength"
                class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg input-focus focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus:z-10"
                placeholder="8位以上，包含字母數字"
              >
              <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" 
                   class="text-gray-400 cursor-pointer hover:text-gray-600" 
                   @click="showPassword = !showPassword"></i>
              </div>
            </div>
            <!-- 密碼強度指示器 -->
            <div class="mt-2">
              <div class="flex space-x-1">
                <div :class="passwordStrength >= 1 ? 'bg-red-500' : 'bg-gray-200'" class="h-1 flex-1 rounded"></div>
                <div :class="passwordStrength >= 2 ? 'bg-yellow-500' : 'bg-gray-200'" class="h-1 flex-1 rounded"></div>
                <div :class="passwordStrength >= 3 ? 'bg-green-500' : 'bg-gray-200'" class="h-1 flex-1 rounded"></div>
              </div>
              <p class="text-xs text-gray-500 mt-1">密碼強度：{{ getPasswordStrengthText() }}</p>
            </div>
          </div>

          <!-- 服務條款 -->
          <div class="flex items-start">
            <div class="flex items-center h-5">
              <input
                id="terms"
                name="terms"
                type="checkbox"
                required
                v-model="registerForm.acceptTerms"
                class="focus:ring-purple-500 h-4 w-4 text-purple-600 border-gray-300 rounded"
              >
            </div>
            <div class="ml-3 text-sm">
              <label for="terms" class="text-gray-600">
                我同意 
                <a href="#" class="text-purple-600 hover:text-purple-500">服務條款</a> 
                和 
                <a href="#" class="text-purple-600 hover:text-purple-500">隱私政策</a>
              </label>
            </div>
          </div>

          <!-- 提交按鈕 -->
          <div>
            <button
              type="submit"
              :disabled="loading || !canSubmit"
              class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-300 transform hover:scale-105 disabled:opacity-50"
            >
              <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                <i class="fas fa-rocket text-purple-500 group-hover:text-purple-400"></i>
              </span>
              {{ loading ? '註冊中...' : '立即註冊' }}
            </button>
          </div>

          <!-- 分隔線 -->
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-gray-50 text-gray-500">或使用以下方式註冊</span>
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

          <!-- 登入鏈接 -->
          <div class="text-center">
            <span class="text-sm text-gray-600">
              已有帳戶？ 
              <router-link to="/login" class="font-medium text-purple-600 hover:text-purple-500">
                立即登入
              </router-link>
            </span>
          </div>
        </form>

        <!-- 免費功能提示 -->
        <div class="mt-8 p-4 bg-purple-50 rounded-lg">
          <h3 class="text-sm font-medium text-purple-800 mb-2">註冊即可享受：</h3>
          <ul class="text-sm text-purple-700 space-y-1">
            <li class="flex items-center">
              <i class="fas fa-check-circle mr-2"></i>
              每日 3 次免費數字人影片生成
            </li>
            <li class="flex items-center">
              <i class="fas fa-check-circle mr-2"></i>
              1 次免費聲音克隆體驗
            </li>
            <li class="flex items-center">
              <i class="fas fa-check-circle mr-2"></i>
              5 個高質量數字人形象
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- 右側動態背景區域 -->
    <div class="hidden lg:block relative w-0 flex-1">
      <!-- 背景漸變 -->
      <div class="absolute inset-0 gradient-bg"></div>
      
      <!-- 浮動數字人頭像 -->
      <div class="absolute inset-0 flex items-center justify-center overflow-hidden">
        <!-- 數字人頭像群組 -->
        <div class="relative w-full h-full flex items-center justify-center">
          <!-- 中心大頭像 -->
          <div class="avatar-float relative z-10">
            <div class="w-32 h-32 bg-white rounded-full p-2 shadow-2xl">
              <img 
                src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face" 
                alt="AI數字人"
                class="w-full h-full rounded-full object-cover"
              >
            </div>
            <!-- 說話泡泡 -->
            <div class="absolute -top-8 -right-4 bg-white rounded-lg p-2 shadow-lg">
              <p class="text-xs text-gray-600">AI 生成中...</p>
              <div class="absolute bottom-0 left-4 w-0 h-0 border-l-4 border-r-4 border-t-4 border-l-transparent border-r-transparent border-t-white"></div>
            </div>
          </div>

          <!-- 環繞小頭像 -->
          <div class="absolute top-20 left-20 avatar-float" style="animation-delay: 0.5s;">
            <div class="w-16 h-16 bg-white rounded-full p-1 shadow-lg">
              <img 
                src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=80&h=80&fit=crop&crop=face" 
                alt="AI數字人"
                class="w-full h-full rounded-full object-cover"
              >
            </div>
          </div>

          <div class="absolute top-32 right-24 avatar-float" style="animation-delay: 1s;">
            <div class="w-20 h-20 bg-white rounded-full p-1 shadow-lg">
              <img 
                src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop&crop=face" 
                alt="AI數字人"
                class="w-full h-full rounded-full object-cover"
              >
            </div>
          </div>

          <div class="absolute bottom-32 left-32 avatar-float" style="animation-delay: 1.5s;">
            <div class="w-18 h-18 bg-white rounded-full p-1 shadow-lg">
              <img 
                src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=90&h=90&fit=crop&crop=face" 
                alt="AI數字人"
                class="w-full h-full rounded-full object-cover"
              >
            </div>
          </div>

          <div class="absolute bottom-20 right-20 avatar-float" style="animation-delay: 2s;">
            <div class="w-14 h-14 bg-white rounded-full p-1 shadow-lg">
              <img 
                src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=70&h=70&fit=crop&crop=face" 
                alt="AI數字人"
                class="w-full h-full rounded-full object-cover"
              >
            </div>
          </div>
        </div>

        <!-- 裝飾元素 -->
        <div class="absolute top-10 right-10 w-4 h-4 bg-yellow-300 rounded-full opacity-60"></div>
        <div class="absolute bottom-16 left-16 w-3 h-3 bg-pink-300 rounded-full opacity-60"></div>
        <div class="absolute top-1/3 left-10 w-2 h-2 bg-blue-300 rounded-full opacity-60"></div>
        
        <!-- 文字覆層 -->
        <div class="absolute bottom-8 left-8 right-8 text-center">
          <h3 class="text-2xl font-bold text-white mb-2">加入創作者社群</h3>
          <p class="text-white/80">數萬名創作者正在使用 Vidspark 創造精彩內容</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

// 表單數據
const registerForm = ref({
  email: '',
  password: '',
  acceptTerms: false
});

const showPassword = ref(false);
const loading = ref(false);
const passwordStrength = ref(0);

// 計算是否可以提交
const canSubmit = computed(() => {
  return registerForm.value.email && 
         registerForm.value.password.length >= 8 && 
         registerForm.value.acceptTerms;
});

// 計算密碼強度
const calculatePasswordStrength = () => {
  const password = registerForm.value.password;
  let strength = 0;
  
  if (password.length >= 8) strength++;
  if (/[A-Z]/.test(password) && /[a-z]/.test(password)) strength++;
  if (/\d/.test(password) && /[!@#$%^&*]/.test(password)) strength++;
  
  passwordStrength.value = strength;
};

// 獲取密碼強度文字
const getPasswordStrengthText = () => {
  if (registerForm.value.password === '') return '請輸入密碼';
  
  switch (passwordStrength.value) {
    case 0:
    case 1:
      return '弱';
    case 2:
      return '中等';
    case 3:
      return '強';
    default:
      return '請輸入密碼';
  }
};

// 註冊處理
const handleRegister = async () => {
  loading.value = true;
  
  try {
    // TODO: 實現實際的註冊邏輯
    console.log('註冊資料:', registerForm.value);
    
    // 模擬註冊延遲
    await new Promise(resolve => setTimeout(resolve, 1000));
    
    // 註冊成功後跳轉到儀表板
    router.push('/dashboard');
  } catch (error) {
    console.error('註冊失敗:', error);
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.avatar-float {
  animation: float 3s ease-in-out infinite;
}

@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
}

.gradient-bg {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.input-focus {
  transition: all 0.3s ease;
}

.input-focus:focus {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(93, 95, 239, 0.15);
}
</style>