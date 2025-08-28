<template>
  <div class="language-switcher">
    <a-select 
      v-model="currentLocale" 
      :style="{ width: '160px' }"
      @change="handleLanguageChange"
    >
      <a-option 
        v-for="locale in availableLocales" 
        :key="locale.code" 
        :value="locale.code"
      >
        {{ locale.flag }} {{ locale.name }}
      </a-option>
    </a-select>
    
    <!-- 測試翻譯顯示 -->
    <div class="translation-test" style="margin-top: 8px; font-size: 12px; color: #666;">
      {{ $t('language.select_language') }}: {{ $t('common.loading') }}
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import type { SupportLocale } from '@/i18n'
import { saveLocalePreference, getLocalePreference, detectBrowserLocale } from '@/i18n/utils/storage'

const { locale } = useI18n()

// 可用語言列表
const availableLocales = ref([
  { code: 'en', name: 'English', flag: '🇺🇸' },
  { code: 'zh-TW', name: '繁體中文', flag: '🇹🇼' },
  { code: 'zh-CN', name: '简体中文', flag: '🇨🇳' }
])

// 當前語言
const currentLocale = ref<SupportLocale>('en')

// 處理語言切換
const handleLanguageChange = (value: SupportLocale) => {
  locale.value = value
  currentLocale.value = value
  saveLocalePreference(value)
  console.log('Language changed to:', value)
}

// 初始化語言
onMounted(() => {
  const savedLocale = getLocalePreference()
  const initialLocale = savedLocale || detectBrowserLocale()
  
  currentLocale.value = initialLocale
  locale.value = initialLocale
  
  console.log('Initial locale:', initialLocale)
})
</script>

<style scoped>
.language-switcher {
  display: inline-block;
}

.translation-test {
  font-size: 12px;
  color: #666;
  margin-top: 4px;
}
</style>
