<template>
  <div class="dashboard">
    <h1>{{ $t('dashboard.welcome') }}</h1>
    <p>{{ $t('app.tagline') }}</p>
    
    <!-- 語言切換器（臨時測試） -->
    <LanguageSwitcher />
    
    <!-- 快速開始 -->
    <div class="quick-start">
      <h2>{{ $t('dashboard.quickStart') }}</h2>
      <div class="action-cards">
        <el-card class="action-card" @click="$router.push('/create')">
          <div class="card-content">
            <el-icon><VideoPlay /></el-icon>
            <h3>{{ $t('video.freeDigitalHuman') }}</h3>
            <p>{{ $t('quota.freeQuota') }}</p>
          </div>
        </el-card>
        
        <el-card class="action-card" @click="$router.push('/voice-clone')">
          <div class="card-content">
            <el-icon><Microphone /></el-icon>
            <h3>{{ $t('voice.cloneVoice') }}</h3>
            <p>{{ $t('voice.instructions') }}</p>
          </div>
        </el-card>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { VideoPlay, Microphone } from '@element-plus/icons-vue'
import LanguageSwitcher from '@/components/LanguageSwitcher.vue'
</script>

<style scoped>
.dashboard {
  padding: 24px;
  max-width: 1200px;
  margin: 0 auto;
}

.quick-start {
  margin-top: 32px;
}

.action-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 24px;
  margin-top: 16px;
}

.action-card {
  cursor: pointer;
  transition: all 0.3s ease;
}

.action-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.card-content {
  text-align: center;
  padding: 24px;
}

.card-content .el-icon {
  font-size: 48px;
  color: #5D5FEF;
  margin-bottom: 16px;
}

.card-content h3 {
  margin: 16px 0 8px 0;
  color: #333;
}

.card-content p {
  color: #666;
  font-size: 14px;
}
</style>
