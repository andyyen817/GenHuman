<template>
  <el-dropdown @command="handleLanguageChange" placement="bottom-end">
    <el-button type="text" class="language-trigger">
      <span class="flag">{{ currentLanguageInfo?.flag }}</span>
      <span class="name">{{ currentLanguageInfo?.name }}</span>
      <el-icon><ArrowDown /></el-icon>
    </el-button>
    
    <template #dropdown>
      <el-dropdown-menu>
        <el-dropdown-item 
          v-for="lang in supportedLanguages" 
          :key="lang.code"
          :command="lang.code"
          :class="{ active: currentLanguage === lang.code }"
        >
          <span class="flag">{{ lang.flag }}</span>
          <span class="name">{{ lang.name }}</span>
          <el-icon v-if="currentLanguage === lang.code" class="check"><Check /></el-icon>
        </el-dropdown-item>
      </el-dropdown-menu>
    </template>
  </el-dropdown>
</template>

<script setup lang="ts">
import { ArrowDown, Check } from '@element-plus/icons-vue'
import { useI18nStore } from '@/stores/i18n'
import { storeToRefs } from 'pinia'
import type { SupportedLanguage } from '@/i18n'

const i18nStore = useI18nStore()
const { currentLanguage, supportedLanguages } = storeToRefs(i18nStore)

// 獲取當前語言信息
const currentLanguageInfo = computed(() => i18nStore.getCurrentLanguageInfo())

// 處理語言切換
function handleLanguageChange(language: SupportedLanguage) {
  i18nStore.switchLanguage(language)
}
</script>

<style scoped>
.language-trigger {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  border-radius: 6px;
  transition: all 0.3s ease;
}

.language-trigger:hover {
  background-color: #f5f5f5;
}

.flag {
  font-size: 16px;
}

.name {
  font-size: 14px;
  color: #333;
}

.el-dropdown-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
}

.el-dropdown-item.active {
  background-color: #f0f0ff;
  color: #5D5FEF;
}

.check {
  margin-left: auto;
  color: #5D5FEF;
}
</style>
