<template>
  <div id="vidspark-app" class="h-screen flex">
    <!-- 左側深色導航欄 (HeyGen風格) -->
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
        <button @click="goToCreate" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 px-4 rounded-lg font-medium transition-colors flex items-center justify-center">
          <i class="fas fa-plus mr-2"></i>
          創建影片
        </button>
      </div>

      <!-- 主導航 -->
      <nav class="flex-1 px-4">
        <ul class="space-y-2">
          <li>
            <router-link to="/dashboard" 
              :class="isActive('/dashboard') ? 'flex items-center px-4 py-3 text-white bg-purple-600 rounded-lg' : 'flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors'">
              <i class="fas fa-home mr-3"></i>
              {{ $t('menu.dashboard') }}
            </router-link>
          </li>
          <li>
            <router-link to="/projects" 
              :class="isActive('/projects') ? 'flex items-center px-4 py-3 text-white bg-purple-600 rounded-lg' : 'flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors'">
              <i class="fas fa-folder mr-3"></i>
              我的項目
            </router-link>
          </li>
          <li>
            <router-link to="/avatars" 
              :class="isActive('/avatars') ? 'flex items-center px-4 py-3 text-white bg-purple-600 rounded-lg' : 'flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors'">
              <i class="fas fa-user-tie mr-3"></i>
              數字人庫
            </router-link>
          </li>
          <li>
            <router-link to="/audio" 
              :class="isActive('/audio') ? 'flex items-center px-4 py-3 text-white bg-purple-600 rounded-lg' : 'flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors'">
              <i class="fas fa-microphone mr-3"></i>
              {{ $t('menu.audio') }}
            </router-link>
          </li>
          <li>
            <router-link to="/templates" 
              :class="isActive('/templates') ? 'flex items-center px-4 py-3 text-white bg-purple-600 rounded-lg' : 'flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors'">
              <i class="fas fa-layer-group mr-3"></i>
              模板庫
            </router-link>
          </li>
          <li>
            <router-link to="/files" 
              :class="isActive('/files') ? 'flex items-center px-4 py-3 text-white bg-purple-600 rounded-lg' : 'flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors'">
              <i class="fas fa-images mr-3"></i>
              {{ $t('menu.files') }}
            </router-link>
          </li>
        </ul>

        <!-- 分隔線 -->
        <div class="my-6 border-t border-gray-600"></div>

        <!-- 設置區域 -->
        <ul class="space-y-2">
          <li>
            <router-link to="/settings" 
              :class="isActive('/settings') ? 'flex items-center px-4 py-3 text-white bg-purple-600 rounded-lg' : 'flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors'">
              <i class="fas fa-cog mr-3"></i>
              {{ $t('menu.settings') }}
            </router-link>
          </li>
          <li>
            <router-link to="/team" 
              :class="isActive('/team') ? 'flex items-center px-4 py-3 text-white bg-purple-600 rounded-lg' : 'flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors'">
              <i class="fas fa-users mr-3"></i>
              創建團隊
            </router-link>
          </li>
        </ul>
      </nav>

      <!-- 升級方案按鈕 -->
      <div class="p-4 border-t border-gray-600">
        <div class="bg-gradient-to-r from-yellow-500 to-orange-500 text-white p-4 rounded-lg text-center">
          <div class="flex items-center justify-center mb-2">
            <i class="fas fa-crown mr-2"></i>
            <span class="font-bold">升級方案</span>
          </div>
          <p class="text-sm mb-3">解鎖更多功能</p>
          <button class="w-full bg-white text-orange-600 py-2 px-4 rounded-lg text-sm font-medium hover:bg-gray-100 transition-colors">
            立即升級
          </button>
        </div>
      </div>
    </div>

    <!-- 右側主內容區域 -->
    <div class="flex-1 flex flex-col bg-gray-50">
      <!-- 頂部導航欄 -->
      <header class="bg-white border-b border-gray-200 px-6 py-4">
        <div class="flex justify-between items-center">
          <!-- 歡迎信息 -->
          <div>
            <h1 class="text-2xl font-bold text-gray-900">歡迎回來，{{ userName }}！</h1>
            <p class="text-gray-600">準備創建您的下一個影片了嗎？</p>
          </div>

          <!-- 右側工具欄 -->
          <div class="flex items-center space-x-4">
            <LanguageSwitcher />
            <CreditsWidget />
            
            <!-- 通知按鈕 -->
            <button class="p-2 text-gray-400 hover:text-gray-600 relative">
              <i class="fas fa-bell text-lg"></i>
              <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>

            <!-- 用戶頭像 -->
            <div class="relative">
              <button class="flex items-center space-x-2 bg-gray-100 hover:bg-gray-200 rounded-full py-2 px-3 transition-colors">
                <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center">
                  <i class="fas fa-user text-white text-sm"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">{{ userName }}</span>
                <i class="fas fa-chevron-down text-xs text-gray-400"></i>
              </button>
            </div>
          </div>
        </div>
      </header>

      <!-- 主內容區域 -->
      <main class="flex-1 overflow-auto">
        <router-view v-slot="{ Component }">
          <transition name="fade" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { useUserStore } from '@/stores/user';
import LanguageSwitcher from './components/LanguageSwitcher.vue';
import CreditsWidget from './components/CreditsWidget.vue';

const router = useRouter();
const route = useRoute();
const { t } = useI18n();
const userStore = useUserStore();

const userName = computed(() => userStore.userInfo?.username || '張小明');

const isActive = (path: string) => {
  return route.path === path || route.path.startsWith(path + '/');
};

const goToCreate = () => {
  router.push('/create');
};
</script>

<style>
body {
  font-family: 'Inter', sans-serif;
  margin: 0;
  padding: 0;
}

#vidspark-app {
  font-family: 'Inter', sans-serif;
}

.sidebar-dark {
  background: linear-gradient(180deg, #1a1c20 0%, #2d2f36 100%);
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>