<template>
  <div :class="themeClasses" :style="themeStyles">
    <div class="theme-example">
      <header class="header">
        <h1 class="title">Vidspark 主題系統示例</h1>
        <div class="controls">
          <VThemeToggle 
            size="md" 
            variant="ghost"
            @toggle="onThemeToggle"
          />
          <span class="theme-info">
            當前模式: {{ isDark ? '深色' : '淺色' }}
          </span>
        </div>
      </header>

      <main class="main">
        <section class="section">
          <h2 class="section-title">頁面類型檢測</h2>
          <div class="info-grid">
            <div class="info-item">
              <label>頁面類型:</label>
              <span class="value">{{ pageType }}</span>
            </div>
            <div class="info-item">
              <label>是否靜態頁面:</label>
              <span class="value">{{ isStaticPage ? '是' : '否' }}</span>
            </div>
            <div class="info-item">
              <label>是否控制台頁面:</label>
              <span class="value">{{ isConsolePage ? '是' : '否' }}</span>
            </div>
          </div>
        </section>

        <section class="section">
          <h2 class="section-title">主題配色展示</h2>
          <div class="color-grid">
            <div 
              v-for="(color, name) in displayColors" 
              :key="name"
              class="color-item"
            >
              <div 
                class="color-swatch" 
                :style="{ backgroundColor: color }"
              ></div>
              <div class="color-info">
                <span class="color-name">{{ name }}</span>
                <span class="color-value">{{ color }}</span>
              </div>
            </div>
          </div>
        </section>

        <section class="section">
          <h2 class="section-title">組件示例</h2>
          <div class="component-grid">
            <VCard class="demo-card">
              <template #header>
                <h3>示例卡片</h3>
              </template>
              <template #default>
                <p>這是一個使用當前主題的卡片組件示例。</p>
                <VButton variant="primary" size="md">
                  主要按鈕
                </VButton>
                <VButton variant="secondary" size="md">
                  次要按鈕
                </VButton>
              </template>
            </VCard>

            <VCard class="demo-card">
              <template #header>
                <h3>表單示例</h3>
              </template>
              <template #default>
                <div class="form-group">
                  <VInput 
                    v-model="demoInput"
                    label="示例輸入"
                    placeholder="請輸入內容"
                  />
                </div>
                <div class="form-group">
                  <VInput 
                    v-model="demoEmail"
                    type="email"
                    label="電子郵件"
                    placeholder="example@email.com"
                  />
                </div>
              </template>
            </VCard>
          </div>
        </section>

        <section class="section">
          <h2 class="section-title">主題切換測試</h2>
          <div class="theme-controls">
            <VButton 
              @click="setMode('light')"
              :variant="!isDark ? 'primary' : 'outline'"
            >
              淺色模式
            </VButton>
            <VButton 
              @click="setMode('dark')"
              :variant="isDark ? 'primary' : 'outline'"
            >
              深色模式
            </VButton>
            <VButton 
              @click="toggle"
              variant="secondary"
            >
              切換主題
            </VButton>
          </div>
        </section>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useTheme } from '../composables/useTheme';
import VThemeToggle from '../components/ui/VThemeToggle.vue';
import VCard from '../components/ui/VCard.vue';
import VButton from '../components/ui/VButton.vue';
import VInput from '../components/ui/VInput.vue';

// 使用主題管理
const {
  themeMode,
  pageType,
  themeConfig,
  isDark,
  isStaticPage,
  isConsolePage,
  toggle,
  setMode,
  getThemeClasses,
  getThemeStyles
} = useTheme();

// 示例數據
const demoInput = ref('');
const demoEmail = ref('');

// 計算主題類名和樣式
const themeClasses = computed(() => getThemeClasses());
const themeStyles = computed(() => getThemeStyles());

// 計算要展示的顏色
const displayColors = computed(() => {
  const config = themeConfig.value;
  const colors: Record<string, string> = {};
  
  if (config.pageType === 'static') {
    colors['主要藍色'] = config.colors.primary;
    colors['深藍色'] = config.colors.primaryDark;
    colors['淺藍色'] = config.colors.primaryLight;
    colors['紫色'] = config.colors.secondary.purple;
    colors['綠色'] = config.colors.secondary.green;
    colors['橙色'] = config.colors.secondary.orange;
  } else {
    colors['背景色'] = config.colors.background;
    colors['卡片背景'] = config.colors.card;
    colors['邊框色'] = config.colors.border;
    colors['主要文字'] = config.colors.text.primary;
    colors['次要文字'] = config.colors.text.secondary;
    colors['強調色'] = config.colors.accent;
  }
  
  // 添加狀態色
  if (config.colors.status) {
    colors['成功色'] = config.colors.status.success;
    colors['警告色'] = config.colors.status.warning;
    colors['錯誤色'] = config.colors.status.error;
    colors['信息色'] = config.colors.status.info;
  }
  
  return colors;
});

// 主題切換處理
const onThemeToggle = (mode: 'light' | 'dark') => {
  console.log('主題已切換到:', mode);
};
</script>

<style scoped>
.theme-example {
  @apply min-h-screen p-6;
  @apply transition-colors duration-300;
}

.header {
  @apply flex items-center justify-between mb-8;
  @apply border-b pb-4;
}

.title {
  @apply text-3xl font-bold;
}

.controls {
  @apply flex items-center gap-4;
}

.theme-info {
  @apply text-sm opacity-75;
}

.main {
  @apply space-y-8;
}

.section {
  @apply space-y-4;
}

.section-title {
  @apply text-xl font-semibold;
}

.info-grid {
  @apply grid grid-cols-1 md:grid-cols-3 gap-4;
}

.info-item {
  @apply flex flex-col space-y-1;
  @apply p-4 rounded-lg border;
}

.info-item label {
  @apply text-sm font-medium opacity-75;
}

.info-item .value {
  @apply font-semibold;
}

.color-grid {
  @apply grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4;
}

.color-item {
  @apply flex flex-col space-y-2;
}

.color-swatch {
  @apply w-full h-16 rounded-lg border;
  @apply shadow-sm;
}

.color-info {
  @apply text-center space-y-1;
}

.color-name {
  @apply block text-sm font-medium;
}

.color-value {
  @apply block text-xs opacity-75 font-mono;
}

.component-grid {
  @apply grid grid-cols-1 lg:grid-cols-2 gap-6;
}

.demo-card {
  @apply h-fit;
}

.form-group {
  @apply mb-4;
}

.theme-controls {
  @apply flex flex-wrap gap-4;
}

/* 靜態頁面樣式 */
:global(.page-static) .theme-example {
  @apply bg-white text-gray-900;
}

:global(.page-static) .header {
  @apply border-gray-200;
}

:global(.page-static) .info-item {
  @apply bg-gray-50 border-gray-200;
}

/* 控制台頁面樣式 */
:global(.page-console.theme-light) .theme-example {
  @apply bg-white text-gray-900;
}

:global(.page-console.theme-light) .header {
  @apply border-gray-200;
}

:global(.page-console.theme-light) .info-item {
  @apply bg-gray-50 border-gray-200;
}

:global(.page-console.theme-dark) .theme-example {
  @apply bg-slate-900 text-slate-100;
}

:global(.page-console.theme-dark) .header {
  @apply border-slate-700;
}

:global(.page-console.theme-dark) .info-item {
  @apply bg-slate-800 border-slate-700;
}

/* 響應式設計 */
@media (max-width: 768px) {
  .header {
    @apply flex-col items-start space-y-4;
  }
  
  .controls {
    @apply w-full justify-between;
  }
  
  .color-grid {
    @apply grid-cols-2;
  }
  
  .component-grid {
    @apply grid-cols-1;
  }
}
</style>