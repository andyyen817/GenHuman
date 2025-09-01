<template>
  <div class="p-6">
    <!-- 升級提示卡片 -->
    <div class="gradient-border mb-8">
      <div class="p-6">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">專屬數字人，全新升級</h2>
            <p class="text-gray-600">立即使用您的數字分身，為您的劇本帶來生命力</p>
          </div>
          <button class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
            立即試用 →
          </button>
        </div>
      </div>
    </div>

    <!-- 開始創作區域 -->
    <div class="mb-8">
      <h2 class="text-2xl font-bold text-gray-900 mb-6">開始創作</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- 免費數字人影片 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover relative">
          <div class="absolute top-4 right-4">
            <span class="free-badge text-white text-xs font-bold px-2 py-1 rounded-full">FREE</span>
          </div>
          <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
            <i class="fas fa-user-tie text-purple-600 text-xl"></i>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">免費數字人影片</h3>
          <p class="text-gray-600 text-sm mb-4">最保險的方式！輸入文字，選擇免費數字人，立即生成影片</p>
          <div class="flex items-center text-green-600 text-sm mb-4">
            <i class="fas fa-gift mr-2"></i>
            每日 3 次免費
          </div>
          <button @click="goToFreeVideo" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 rounded-lg text-sm font-medium transition-colors">
            開始 →
          </button>
        </div>

        <!-- 文字轉影片 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover">
          <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
            <i class="fas fa-file-text text-blue-600 text-xl"></i>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">文字轉影片</h3>
          <p class="text-gray-600 text-sm mb-4">將您的文字，圖片或網頁內容一鍵轉為影片</p>
          <div class="flex items-center text-blue-600 text-sm mb-4">
            <i class="fas fa-wand-magic-sparkles mr-2"></i>
            AI 智能轉換
          </div>
          <button @click="goToTextToVideo" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 rounded-lg text-sm font-medium transition-colors">
            開始 →
          </button>
        </div>

        <!-- 聲音克隆體驗 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover relative">
          <div class="absolute top-4 right-4">
            <span class="free-badge text-white text-xs font-bold px-2 py-1 rounded-full">FREE</span>
          </div>
          <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
            <i class="fas fa-microphone text-green-600 text-xl"></i>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">聲音克隆體驗</h3>
          <p class="text-gray-600 text-sm mb-4">60秒錄音，創建您的專屬 AI 聲音</p>
          <div class="flex items-center text-green-600 text-sm mb-4">
            <i class="fas fa-gift mr-2"></i>
            1 次免費體驗
          </div>
          <button @click="goToVoiceClone" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 rounded-lg text-sm font-medium transition-colors">
            開始 →
          </button>
        </div>

        <!-- 模仿影片創作 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover">
          <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
            <i class="fas fa-link text-orange-600 text-xl"></i>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">模仿影片創作</h3>
          <p class="text-gray-600 text-sm mb-4">貼上影片連結，AI 會解析並重新創作相似的影片</p>
          <button @click="goToVideoImitation" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 rounded-lg text-sm font-medium transition-colors">
            開始 →
          </button>
        </div>

        <!-- PPT/PDF 讀解 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover">
          <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
            <i class="fas fa-file-powerpoint text-red-600 text-xl"></i>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">PPT/PDF 讀解</h3>
          <p class="text-gray-600 text-sm mb-4">上傳您的簡報，搭配數字人進行講解</p>
          <button @click="goToPptParser" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 rounded-lg text-sm font-medium transition-colors">
            開始 →
          </button>
        </div>

        <!-- 創建您的數字人 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover">
          <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
            <i class="fas fa-video text-purple-600 text-xl"></i>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">創建您的數字人</h3>
          <p class="text-gray-600 text-sm mb-4">上傳您的影片，創建您自己的數字分身</p>
          <button @click="goToCreateAvatar" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 rounded-lg text-sm font-medium transition-colors">
            開始 →
          </button>
        </div>
      </div>
    </div>

    <!-- 快速統計 -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <div class="bg-white rounded-lg p-6 text-center">
        <div class="text-2xl font-bold text-purple-600 mb-2">{{ stats.totalVideos }}</div>
        <div class="text-sm text-gray-600">已創建影片</div>
      </div>
      <div class="bg-white rounded-lg p-6 text-center">
        <div class="text-2xl font-bold text-green-600 mb-2">{{ stats.credits }}</div>
        <div class="text-sm text-gray-600">剩餘積分</div>
      </div>
      <div class="bg-white rounded-lg p-6 text-center">
        <div class="text-2xl font-bold text-blue-600 mb-2">{{ stats.avatars }}</div>
        <div class="text-sm text-gray-600">我的數字人</div>
      </div>
      <div class="bg-white rounded-lg p-6 text-center">
        <div class="text-2xl font-bold text-orange-600 mb-2">{{ stats.voices }}</div>
        <div class="text-sm text-gray-600">克隆聲音</div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';

const router = useRouter();
const { t } = useI18n();

const stats = ref({
  totalVideos: 12,
  credits: 150,
  avatars: 3,
  voices: 2
});

const goToFreeVideo = () => {
  router.push('/create/free-video');
};

const goToTextToVideo = () => {
  router.push('/create/text-to-video');
};

const goToVoiceClone = () => {
  router.push('/audio/clone');
};

const goToVideoImitation = () => {
  router.push('/create/imitation');
};

const goToPptParser = () => {
  router.push('/create/ppt-parser');
};

const goToCreateAvatar = () => {
  router.push('/avatars/create');
};
</script>

<style scoped>
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