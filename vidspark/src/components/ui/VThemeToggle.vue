<template>
  <button
    :class="buttonClasses"
    @click="handleToggle"
    :aria-label="isDark ? '切換到淺色模式' : '切換到深色模式'"
    :title="isDark ? '切換到淺色模式' : '切換到深色模式'"
  >
    <transition name="icon-fade" mode="out-in">
      <svg
        v-if="isDark"
        key="sun"
        class="icon"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
        ></path>
      </svg>
      <svg
        v-else
        key="moon"
        class="icon"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
        ></path>
      </svg>
    </transition>
  </button>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useSimpleTheme } from '../../composables/useTheme';

interface Props {
  size?: 'sm' | 'md' | 'lg';
  variant?: 'default' | 'ghost' | 'outline';
  disabled?: boolean;
}

interface Emits {
  (e: 'toggle', mode: 'light' | 'dark'): void;
}

const props = withDefaults(defineProps<Props>(), {
  size: 'md',
  variant: 'ghost',
  disabled: false
});

const emit = defineEmits<Emits>();

const { mode, isDark, toggle } = useSimpleTheme();

// 計算按鈕樣式類名
const buttonClasses = computed(() => {
  const classes = [
    'theme-toggle',
    `theme-toggle--${props.size}`,
    `theme-toggle--${props.variant}`
  ];
  
  if (props.disabled) {
    classes.push('theme-toggle--disabled');
  }
  
  return classes;
});

// 處理主題切換
const handleToggle = () => {
  if (props.disabled) return;
  
  const newMode = toggle();
  emit('toggle', newMode);
};
</script>

<style scoped>
.theme-toggle {
  @apply relative inline-flex items-center justify-center;
  @apply rounded-lg border-0 cursor-pointer;
  @apply transition-all duration-200 ease-in-out;
  @apply focus:outline-none focus:ring-2 focus:ring-offset-2;
  @apply hover:scale-105 active:scale-95;
}

/* 尺寸變體 */
.theme-toggle--sm {
  @apply w-8 h-8 text-sm;
}

.theme-toggle--md {
  @apply w-10 h-10 text-base;
}

.theme-toggle--lg {
  @apply w-12 h-12 text-lg;
}

/* 樣式變體 */
.theme-toggle--default {
  @apply bg-gray-100 text-gray-700;
  @apply hover:bg-gray-200;
  @apply focus:ring-gray-300;
}

.theme-toggle--ghost {
  @apply bg-transparent text-gray-600;
  @apply hover:bg-gray-100 hover:text-gray-900;
  @apply focus:ring-gray-300;
}

.theme-toggle--outline {
  @apply bg-transparent border border-gray-300 text-gray-600;
  @apply hover:bg-gray-50 hover:border-gray-400;
  @apply focus:ring-gray-300;
}

/* 深色模式樣式 */
:global(.theme-dark) .theme-toggle--default {
  @apply bg-gray-800 text-gray-300;
  @apply hover:bg-gray-700;
  @apply focus:ring-gray-600;
}

:global(.theme-dark) .theme-toggle--ghost {
  @apply text-gray-400;
  @apply hover:bg-gray-800 hover:text-gray-100;
  @apply focus:ring-gray-600;
}

:global(.theme-dark) .theme-toggle--outline {
  @apply border-gray-600 text-gray-400;
  @apply hover:bg-gray-800 hover:border-gray-500;
  @apply focus:ring-gray-600;
}

/* 控制台頁面樣式 */
:global(.page-console) .theme-toggle--default {
  @apply bg-slate-800 text-slate-300;
  @apply hover:bg-slate-700;
  @apply focus:ring-slate-600;
}

:global(.page-console) .theme-toggle--ghost {
  @apply text-slate-400;
  @apply hover:bg-slate-800 hover:text-slate-100;
  @apply focus:ring-slate-600;
}

:global(.page-console) .theme-toggle--outline {
  @apply border-slate-600 text-slate-400;
  @apply hover:bg-slate-800 hover:border-slate-500;
  @apply focus:ring-slate-600;
}

/* 禁用狀態 */
.theme-toggle--disabled {
  @apply opacity-50 cursor-not-allowed;
  @apply hover:scale-100 active:scale-100;
}

/* 圖標樣式 */
.icon {
  @apply w-full h-full;
  @apply transition-transform duration-200;
}

/* 圖標動畫 */
.icon-fade-enter-active,
.icon-fade-leave-active {
  @apply transition-all duration-200;
}

.icon-fade-enter-from {
  @apply opacity-0 rotate-90 scale-75;
}

.icon-fade-leave-to {
  @apply opacity-0 -rotate-90 scale-75;
}

.icon-fade-enter-to,
.icon-fade-leave-from {
  @apply opacity-100 rotate-0 scale-100;
}

/* 響應式設計 */
@media (max-width: 640px) {
  .theme-toggle--lg {
    @apply w-10 h-10 text-base;
  }
  
  .theme-toggle--md {
    @apply w-9 h-9 text-sm;
  }
}

/* 無障礙支持 */
@media (prefers-reduced-motion: reduce) {
  .theme-toggle,
  .icon,
  .icon-fade-enter-active,
  .icon-fade-leave-active {
    @apply transition-none;
  }
  
  .theme-toggle {
    @apply hover:scale-100 active:scale-100;
  }
}

/* 高對比度模式 */
@media (prefers-contrast: high) {
  .theme-toggle--ghost {
    @apply border border-current;
  }
  
  .theme-toggle--outline {
    @apply border-2;
  }
}

/* 焦點可見性 */
.theme-toggle:focus-visible {
  @apply ring-2 ring-offset-2;
}

/* 觸摸設備優化 */
@media (hover: none) {
  .theme-toggle {
    @apply hover:scale-100;
  }
}
</style>