<template>
  <el-dropdown @command="handleLanguageChange">
    <el-button type="text" style="color: white;">
      {{ currentLanguageDisplay }}
      <el-icon class="el-icon--right">
        <ArrowDown />
      </el-icon>
    </el-button>
    <template #dropdown>
      <el-dropdown-menu>
        <el-dropdown-item command="zh-TW">繁體中文</el-dropdown-item>
        <el-dropdown-item command="zh-CN">简体中文</el-dropdown-item>
        <el-dropdown-item command="en">English</el-dropdown-item>
      </el-dropdown-menu>
    </template>
  </el-dropdown>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { ArrowDown } from '@element-plus/icons-vue'

const { locale } = useI18n()

const currentLanguageDisplay = computed(() => {
  switch (locale.value) {
    case 'zh-TW':
      return '繁體中文'
    case 'zh-CN':
      return '简体中文'
    case 'en':
      return 'English'
    default:
      return '繁體中文'
  }
})

const handleLanguageChange = (command: string) => {
  locale.value = command
  localStorage.setItem('vidspark_admin_locale', command)
}
</script>
