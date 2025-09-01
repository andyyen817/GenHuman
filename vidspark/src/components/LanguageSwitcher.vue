<template>
  <el-dropdown @command="handleLanguageChange">
    <span class="el-dropdown-link">
      <el-icon><Globe /></el-icon>
      {{ currentLanguageLabel }}
      <el-icon class="el-icon--right"><arrow-down /></el-icon>
    </span>
    <template #dropdown>
      <el-dropdown-menu>
        <el-dropdown-item command="en">English</el-dropdown-item>
        <el-dropdown-item command="zh-TW">繁體中文</el-dropdown-item>
        <el-dropdown-item command="zh-CN">简体中文</el-dropdown-item>
      </el-dropdown-menu>
    </template>
  </el-dropdown>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { Globe, ArrowDown } from '@element-plus/icons-vue';
import { setLanguage } from '@/i18n';

const { locale } = useI18n();

const languageLabels = {
  'en': 'English',
  'zh-TW': '繁體中文',
  'zh-CN': '简体中文'
};

const currentLanguageLabel = computed(() => {
  return languageLabels[locale.value as keyof typeof languageLabels] || 'English';
});

const handleLanguageChange = (command: string) => {
  setLanguage(command as 'en' | 'zh-TW' | 'zh-CN');
};
</script>

<style scoped>
.el-dropdown-link {
  cursor: pointer;
  color: var(--el-color-primary);
  display: flex;
  align-items: center;
}
.el-dropdown-link:hover {
  color: var(--el-color-primary-light-3);
}
</style>