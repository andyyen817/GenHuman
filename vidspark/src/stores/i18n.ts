import { defineStore } from 'pinia'
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { SUPPORTED_LANGUAGES, type SupportedLanguage, setLanguage, getCurrentLanguage } from '@/i18n'

export const useI18nStore = defineStore('i18n', () => {
  const { locale } = useI18n()
  
  const currentLanguage = ref<SupportedLanguage>('zh-TW')
  const supportedLanguages = SUPPORTED_LANGUAGES

  // 初始化語言設置
  function initializeLanguage() {
    const savedLanguage = getCurrentLanguage()
    currentLanguage.value = savedLanguage
    locale.value = savedLanguage
  }

  // 切換語言
  function switchLanguage(language: SupportedLanguage) {
    currentLanguage.value = language
    setLanguage(language)
    locale.value = language
  }

  // 獲取當前語言信息
  function getCurrentLanguageInfo() {
    return supportedLanguages.find(lang => lang.code === currentLanguage.value)
  }

  return {
    currentLanguage,
    supportedLanguages,
    initializeLanguage,
    switchLanguage,
    getCurrentLanguageInfo
  }
})
