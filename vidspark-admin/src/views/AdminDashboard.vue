<template>
  <div class="admin-dashboard">
    <el-container>
      <el-header>
        <h1>{{ $t('app.title') }}</h1>
        <LanguageSwitcher />
      </el-header>
      
      <el-main>
        <el-row :gutter="20">
          <el-col :span="6">
            <el-card class="stats-card">
              <h3>總用戶數</h3>
              <div class="stats-number">1,234</div>
            </el-card>
          </el-col>
          
          <el-col :span="6">
            <el-card class="stats-card">
              <h3>今日影片生成</h3>
              <div class="stats-number">56</div>
            </el-card>
          </el-col>
          
          <el-col :span="6">
            <el-card class="stats-card">
              <h3>活躍語言</h3>
              <div class="stats-number">3</div>
            </el-card>
          </el-col>
          
          <el-col :span="6">
            <el-card class="stats-card">
              <h3>系統狀態</h3>
              <div class="stats-status">正常</div>
            </el-card>
          </el-col>
        </el-row>
        
        <el-row :gutter="20" style="margin-top: 20px;">
          <el-col :span="24">
            <el-card>
              <h3>快速操作</h3>
              <el-button-group>
                <el-button type="primary" @click="$router.push('/users')">
                  {{ $t('nav.users') }}
                </el-button>
                <el-button type="success" @click="$router.push('/i18n')">
                  {{ $t('nav.i18n') }}
                </el-button>
                <el-button type="info" @click="$router.push('/settings')">
                  {{ $t('nav.settings') }}
                </el-button>
              </el-button-group>
            </el-card>
          </el-col>
        </el-row>
      </el-main>
    </el-container>
  </div>
</template>

<script setup lang="ts">
import LanguageSwitcher from '../components/LanguageSwitcher.vue'
</script>

<style scoped>
.admin-dashboard {
  min-height: 100vh;
  background-color: #f5f5f5;
}

.el-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #409eff;
  color: white;
}

.stats-card {
  text-align: center;
}

.stats-number {
  font-size: 2rem;
  font-weight: bold;
  color: #409eff;
}

.stats-status {
  font-size: 1.2rem;
  color: #67c23a;
}
</style>
