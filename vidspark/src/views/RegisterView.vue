<template>
  <div class="register-page bg-gray-50 min-h-screen flex">
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
        <form @submit.prevent="handleRegister" class="space-y-6">
          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
              電子郵件地址
            </label>
            <div class="relative">
              <input
                id="email"
                v-model="registerForm.email"
                name="email"
                type="email"
                autocomplete="email"
                required
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
                v-model="registerForm.password"
                name="password"
                :type="showPassword ? 'text' : 'password'"
                autocomplete="new-password"
                required
                class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg input-focus focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus:z-10"
                placeholder="8位以上，包含字母數字"
                @input="checkPasswordStrength"
              >
              <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <i 
                  :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"
                  class="text-gray-400 cursor-pointer hover:text-gray-600"
                  @click="showPassword = !showPassword"
                ></i>
              </div>
            </div>
            <!-- 密碼強度指示器 -->
            <div class="mt-2">
              <div class="flex space-x-1">
                <div 
                  v-for="(level, index) in 3" 
                  :key="index"
                  :class="passwordStrength > index ? 'bg-green-500' : 'bg-gray-200'"
                  class="h-1 flex-1 rounded transition-colors"
                ></div>
              </div>
              <p class="text-xs text-gray-500 mt-1">
                密碼強度：{{ passwordStrengthText }}
              </p>
            </div>
          </div>

          <!-- 服務條款 -->
          <div class="flex items-start">
            <div class="flex items-center h-5">
              <input
                id="terms"
                v-model="registerForm.acceptTerms"
                name="terms"
                type="checkbox"
                required
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
              :disabled="isLoading"
              class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                <i class="fas fa-rocket text-purple-500 group-hover:text-purple-400"></i>
              </span>
              {{ isLoading ? '註冊中...' : '立即註冊' }}
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
            >
              <i class="fab fa-google text-red-500 mr-2"></i>
              Google
            </button>
            <button
              type="button"
              class="w-full inline-flex justify-center py-3 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors"
            >
              <i class="fab fa-github mr-2"></i>
              GitHub
            </button>
          </div>
        </form>

        <!-- 登入連結 -->
        <div class="mt-6 text-center">
          <p class="text-sm text-gray-600">
            已經有帳戶了？
            <router-link to="/login" class="font-medium text-purple-600 hover:text-purple-500">
              立即登入
            </router-link>
          </p>
        </div>
      </div>
    </div>

    <!-- 右側圖片區域 -->
    <div class="hidden lg:block relative w-0 flex-1">
      <div class="absolute inset-0 gradient-bg flex items-center justify-center">
        <div class="text-center text-white p-8">
          <div class="avatar-float mb-8">
            <div class="w-32 h-32 bg-white/20 rounded-full flex items-center justify-center mx-auto backdrop-blur-sm">
              <i class="fas fa-user-tie text-6xl text-white"></i>
            </div>
          </div>
          <h3 class="text-3xl font-bold mb-4">數位分身，由您創造</h3>
          <p class="text-xl opacity-90 max-w-md mx-auto">
            加入數千位創作者，使用 AI 技術打造屬於您的專業影片內容
          </p>
          <div class="mt-8 flex justify-center space-x-4">
            <div class="flex items-center">
              <i class="fas fa-star text-yellow-400 mr-1"></i>
              <span class="text-sm">4.9 評分</span>
            </div>
            <div class="flex items-center">
              <i class="fas fa-users mr-1"></i>
              <span class="text-sm">10,000+ 用戶</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { ElMessage } from 'element-plus';

const router = useRouter();

const registerForm = ref({
  email: '',
  password: '',
  acceptTerms: false
});

const isLoading = ref(false);
const showPassword = ref(false);
const passwordStrength = ref(0);

const passwordStrengthText = computed(() => {
  const strength = passwordStrength.value;
  if (strength === 0) return '請輸入密碼';
  if (strength === 1) return '弱';
  if (strength === 2) return '中等';
  return '強';
});

const checkPasswordStrength = () => {
  const password = registerForm.value.password;
  let strength = 0;
  
  if (password.length >= 8) strength++;
  if (/[A-Z]/.test(password) && /[a-z]/.test(password)) strength++;
  if (/\d/.test(password)) strength++;
  
  passwordStrength.value = strength;
};

const handleRegister = async () => {
  try {
    isLoading.value = true;
    
    // TODO: 實現註冊邏輯
    await new Promise(resolve => setTimeout(resolve, 2000));
    
    ElMessage.success('註冊成功！歡迎加入 Vidspark');
    router.push('/dashboard');
    
    console.log(`[${new Date().toLocaleTimeString()}] ✅ 用戶註冊成功:`, registerForm.value.email);
  } catch (error) {
    console.error('註冊失敗:', error);
    ElMessage.error('註冊失敗，請稍後再試');
  } finally {
    isLoading.value = false;
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
