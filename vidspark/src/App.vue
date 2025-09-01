<template>
  <div id="vidspark-app">
    <el-container>
      <el-header class="vidspark-header">
        <div class="logo">Vidspark</div>
        <div class="header-right">
          <LanguageSwitcher />
          <CreditsWidget />
          <el-button type="primary" @click="goToLogin">{{ $t('user.login') }}</el-button>
        </div>
      </el-header>
      <el-container class="vidspark-main-container">
        <el-aside width="200px" class="vidspark-sidebar">
          <el-menu :default-active="activeMenu" class="el-menu-vertical-demo" @select="handleMenuSelect">
            <el-menu-item index="/dashboard">
              <el-icon><House /></el-icon>
              <span>{{ $t('menu.dashboard') }}</span>
            </el-menu-item>
            <el-menu-item index="/audio">
              <el-icon><Microphone /></el-icon>
              <span>{{ $t('menu.audio') }}</span>
            </el-menu-item>
            <el-menu-item index="/video">
              <el-icon><VideoCamera /></el-icon>
              <span>{{ $t('menu.video') }}</span>
            </el-menu-item>
            <el-menu-item index="/files">
              <el-icon><Folder /></el-icon>
              <span>{{ $t('menu.files') }}</span>
            </el-menu-item>
            <el-menu-item index="/credits">
              <el-icon><Coin /></el-icon>
              <span>{{ $t('menu.credits') }}</span>
            </el-menu-item>
            <el-menu-item index="/settings">
              <el-icon><Setting /></el-icon>
              <span>{{ $t('menu.settings') }}</span>
            </el-menu-item>
          </el-menu>
        </el-aside>
        <el-main class="vidspark-content">
          <router-view v-slot="{ Component }">
            <transition name="fade" mode="out-in">
              <component :is="Component" />
            </transition>
          </router-view>
        </el-main>
      </el-container>
    </el-container>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n';
import LanguageSwitcher from './components/LanguageSwitcher.vue';
import CreditsWidget from './components/CreditsWidget.vue';
import {
  House,
  Microphone,
  VideoCamera,
  Folder,
  Coin,
  Setting,
} from '@element-plus/icons-vue';

const router = useRouter();
const route = useRoute();
const { t } = useI18n();

const activeMenu = ref(route.path);

watch(
  () => route.path,
  (newPath) => {
    activeMenu.value = newPath;
  }
);

const handleMenuSelect = (key: string) => {
  router.push(key);
};

const goToLogin = () => {
  router.push('/login');
};
</script>

<style lang="scss">
#vidspark-app {
  height: 100vh;
  display: flex;
  flex-direction: column;
}

.vidspark-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: var(--el-color-primary-light-9);
  color: var(--el-color-primary);
  padding: 0 20px;
  border-bottom: 1px solid var(--el-border-color-light);
}

.logo {
  font-size: 24px;
  font-weight: bold;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 15px;
}

.vidspark-main-container {
  flex: 1;
}

.vidspark-sidebar {
  background-color: var(--el-color-info-light-9);
  padding-top: 20px;
  border-right: 1px solid var(--el-border-color-light);
}

.vidspark-content {
  padding: 20px;
  background-color: var(--el-bg-color-page);
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