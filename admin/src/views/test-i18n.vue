<template>
  <div class="i18n-test-page">
    <a-card title="多語言測試頁面" :bordered="false">
      <!-- 語言切換器 -->
      <div class="language-switcher-section">
        <h3>語言切換器</h3>
        <LanguageSwitcher />
      </div>
      
      <!-- 翻譯測試 -->
      <a-divider />
      
      <div class="translation-tests">
        <h3>翻譯效果測試</h3>
        
        <a-row :gutter="16">
          <a-col :span="8">
            <a-card title="通用詞匯" size="small">
              <p>{{ $t('common.submit') }}</p>
              <p>{{ $t('common.cancel') }}</p>
              <p>{{ $t('common.loading') }}</p>
              <p>{{ $t('common.success') }}</p>
              <p>{{ $t('common.error') }}</p>
            </a-card>
          </a-col>
          
          <a-col :span="8">
            <a-card title="導航菜單" size="small">
              <p>{{ $t('navigation.dashboard') }}</p>
              <p>{{ $t('navigation.create_video') }}</p>
              <p>{{ $t('navigation.my_projects') }}</p>
              <p>{{ $t('navigation.voice_clone') }}</p>
              <p>{{ $t('navigation.digital_human') }}</p>
            </a-card>
          </a-col>
          
          <a-col :span="8">
            <a-card title="用戶相關" size="small">
              <p>{{ $t('user.register') }}</p>
              <p>{{ $t('user.login') }}</p>
              <p>{{ $t('user.profile') }}</p>
              <p>{{ $t('user.email') }}</p>
              <p>{{ $t('user.password') }}</p>
            </a-card>
          </a-col>
        </a-row>
        
        <a-divider />
        
        <a-row :gutter="16">
          <a-col :span="8">
            <a-card title="數字人相關" size="small">
              <p>{{ $t('avatar.free_avatar') }}</p>
              <p>{{ $t('avatar.premium_avatar') }}</p>
              <p>{{ $t('avatar.select_avatar') }}</p>
              <p>{{ $t('avatar.avatar_preview') }}</p>
            </a-card>
          </a-col>
          
          <a-col :span="8">
            <a-card title="聲音克隆" size="small">
              <p>{{ $t('voice.voice_clone') }}</p>
              <p>{{ $t('voice.record_voice') }}</p>
              <p>{{ $t('voice.voice_preview') }}</p>
              <p>{{ $t('voice.clone_processing') }}</p>
            </a-card>
          </a-col>
          
          <a-col :span="8">
            <a-card title="配額管理" size="small">
              <p>{{ $t('quota.free_quota') }}</p>
              <p>{{ $t('quota.daily_limit') }}</p>
              <p>{{ $t('quota.remaining_quota', { count: 5 }) }}</p>
              <p>{{ $t('quota.upgrade_plan') }}</p>
            </a-card>
          </a-col>
        </a-row>
      </div>
      
      <!-- 測試按鈕 -->
      <a-divider />
      
      <div class="test-buttons">
        <h3>交互測試</h3>
        <a-space>
          <a-button type="primary">{{ $t('common.submit') }}</a-button>
          <a-button>{{ $t('common.cancel') }}</a-button>
          <a-button type="outline">{{ $t('common.save') }}</a-button>
          <a-button status="warning">{{ $t('common.edit') }}</a-button>
          <a-button status="danger">{{ $t('common.delete') }}</a-button>
        </a-space>
      </div>
      
      <!-- 當前語言信息 -->
      <a-divider />
      
      <div class="current-language-info">
        <h3>當前語言信息</h3>
        <a-descriptions :column="1" size="small">
          <a-descriptions-item label="當前語言代碼">
            {{ currentLocale }}
          </a-descriptions-item>
          <a-descriptions-item label="當前語言名稱">
            {{ $t('language.' + getLanguageKey(currentLocale)) }}
          </a-descriptions-item>
          <a-descriptions-item label="瀏覽器語言">
            {{ navigator.language }}
          </a-descriptions-item>
          <a-descriptions-item label="時間戳">
            {{ new Date().toLocaleString() }}
          </a-descriptions-item>
        </a-descriptions>
      </div>
    </a-card>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import LanguageSwitcher from '@/components/LanguageSwitcher.vue'
import type { SupportLocale } from '@/i18n'

defineOptions({ name: 'I18nTest' })

const { locale } = useI18n()

const currentLocale = computed(() => locale.value)

const getLanguageKey = (locale: SupportLocale): string => {
  const keyMap: Record<SupportLocale, string> = {
    'en': 'english',
    'zh-TW': 'traditional_chinese', 
    'zh-CN': 'simplified_chinese'
  }
  return keyMap[locale] || 'english'
}
</script>

<style scoped>
.i18n-test-page {
  padding: 16px;
}

.language-switcher-section,
.translation-tests,
.test-buttons,
.current-language-info {
  margin-bottom: 16px;
}

.translation-tests .arco-card {
  height: 200px;
}

.translation-tests p {
  margin: 4px 0;
  padding: 4px 8px;
  background: var(--color-fill-2);
  border-radius: 4px;
  font-size: 13px;
}
</style>
