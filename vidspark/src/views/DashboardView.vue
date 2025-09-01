<template>
  <div class="dashboard-view">
    <h1>{{ $t('menu.dashboard') }}</h1>
    <el-row :gutter="20">
      <el-col :span="6">
        <el-card class="box-card">
          <template #header>
            <div class="card-header">
              <span>{{ $t('user.userLevel') }}</span>
            </div>
          </template>
          <div class="text item">
            {{ userLevel }}
          </div>
        </el-card>
      </el-col>
      <el-col :span="6">
        <el-card class="box-card">
          <template #header>
            <div class="card-header">
              <span>{{ $t('credits.balance') }}</span>
            </div>
          </template>
          <div class="text item">
            {{ currentBalance }}
          </div>
        </el-card>
      </el-col>
      <el-col :span="12">
        <el-card class="box-card">
          <template #header>
            <div class="card-header">
              <span>快速操作</span>
            </div>
          </template>
          <div class="quick-actions">
            <el-button type="primary" @click="goToAudio">{{ $t('audio.cloneVoice') }}</el-button>
            <el-button type="success" @click="goToVideo">{{ $t('video.generateVideo') }}</el-button>
          </div>
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { useUserStore } from '@/stores/user';
import { useCreditsStore } from '@/stores/credits';

const router = useRouter();
const { t } = useI18n();
const userStore = useUserStore();
const creditsStore = useCreditsStore();

const userLevel = computed(() => userStore.currentLevel);
const currentBalance = computed(() => creditsStore.currentBalance);

const goToAudio = () => {
  router.push('/audio');
};

const goToVideo = () => {
  router.push('/video');
};
</script>

<style scoped>
.dashboard-view {
  padding: 20px;
}

.box-card {
  margin-bottom: 20px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.text {
  font-size: 18px;
  font-weight: bold;
}

.item {
  margin-bottom: 18px;
}

.quick-actions {
  display: flex;
  gap: 10px;
}
</style>
