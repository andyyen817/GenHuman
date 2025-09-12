<template>
  <button
    :class="buttonClasses"
    :disabled="disabled || loading"
    :type="type"
    @click="handleClick"
    v-bind="$attrs"
  >
    <span v-if="loading" class="v-button__loading">
      <svg class="v-button__spinner" viewBox="0 0 24 24">
        <circle
          class="v-button__spinner-circle"
          cx="12"
          cy="12"
          r="10"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        />
      </svg>
    </span>
    
    <span v-if="icon && !loading" class="v-button__icon v-button__icon--left">
      <component :is="icon" />
    </span>
    
    <span class="v-button__content">
      <slot />
    </span>
    
    <span v-if="iconRight" class="v-button__icon v-button__icon--right">
      <component :is="iconRight" />
    </span>
  </button>
</template>

<script setup lang="ts">
import { computed, defineEmits, defineProps } from 'vue'

/**
 * 按鈕組件屬性接口
 */
export interface VButtonProps {
  /** 按鈕變體 */
  variant?: 'primary' | 'secondary' | 'outline' | 'ghost' | 'danger'
  /** 按鈕尺寸 */
  size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl'
  /** 是否禁用 */
  disabled?: boolean
  /** 是否加載中 */
  loading?: boolean
  /** 按鈕類型 */
  type?: 'button' | 'submit' | 'reset'
  /** 左側圖標 */
  icon?: any
  /** 右側圖標 */
  iconRight?: any
  /** 是否為塊級元素 */
  block?: boolean
  /** 是否為圓形按鈕 */
  round?: boolean
}

/**
 * 按鈕組件事件接口
 */
export interface VButtonEmits {
  click: [event: MouseEvent]
}

// 定義屬性
const props = withDefaults(defineProps<VButtonProps>(), {
  variant: 'primary',
  size: 'md',
  disabled: false,
  loading: false,
  type: 'button',
  block: false,
  round: false
})

// 定義事件
const emit = defineEmits<VButtonEmits>()

// 計算按鈕樣式類
const buttonClasses = computed(() => {
  return [
    'v-button',
    `v-button--${props.variant}`,
    `v-button--${props.size}`,
    {
      'v-button--disabled': props.disabled,
      'v-button--loading': props.loading,
      'v-button--block': props.block,
      'v-button--round': props.round,
      'v-button--icon-only': !$slots.default && (props.icon || props.iconRight)
    }
  ]
})

// 處理點擊事件
const handleClick = (event: MouseEvent) => {
  if (!props.disabled && !props.loading) {
    emit('click', event)
  }
}
</script>

<style scoped>
.v-button {
  /* 基礎樣式 */
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: var(--spacing-2);
  border: 1px solid transparent;
  border-radius: var(--radius-md);
  font-family: var(--font-family-sans);
  font-weight: 500;
  text-decoration: none;
  cursor: pointer;
  transition: all var(--duration-fast) var(--easing-ease-in-out);
  user-select: none;
  white-space: nowrap;
  
  /* 禁用文本選擇和拖拽 */
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
}

/* 尺寸變體 */
.v-button--xs {
  padding: var(--spacing-1) var(--spacing-2);
  font-size: var(--font-size-xs);
  line-height: 1.25;
}

.v-button--sm {
  padding: var(--spacing-2) var(--spacing-3);
  font-size: var(--font-size-sm);
  line-height: 1.25;
}

.v-button--md {
  padding: var(--spacing-2) var(--spacing-4);
  font-size: var(--font-size-base);
  line-height: 1.5;
}

.v-button--lg {
  padding: var(--spacing-3) var(--spacing-6);
  font-size: var(--font-size-lg);
  line-height: 1.5;
}

.v-button--xl {
  padding: var(--spacing-4) var(--spacing-8);
  font-size: var(--font-size-xl);
  line-height: 1.5;
}

/* 顏色變體 */
.v-button--primary {
  background-color: var(--color-primary);
  border-color: var(--color-primary);
  color: white;
}

.v-button--primary:hover:not(.v-button--disabled):not(.v-button--loading) {
  background-color: color-mix(in srgb, var(--color-primary) 90%, black);
  border-color: color-mix(in srgb, var(--color-primary) 90%, black);
}

.v-button--primary:active:not(.v-button--disabled):not(.v-button--loading) {
  background-color: color-mix(in srgb, var(--color-primary) 80%, black);
  border-color: color-mix(in srgb, var(--color-primary) 80%, black);
}

.v-button--secondary {
  background-color: var(--color-secondary);
  border-color: var(--color-secondary);
  color: white;
}

.v-button--secondary:hover:not(.v-button--disabled):not(.v-button--loading) {
  background-color: color-mix(in srgb, var(--color-secondary) 90%, black);
  border-color: color-mix(in srgb, var(--color-secondary) 90%, black);
}

.v-button--outline {
  background-color: transparent;
  border-color: var(--color-border);
  color: var(--color-text-primary);
}

.v-button--outline:hover:not(.v-button--disabled):not(.v-button--loading) {
  background-color: var(--color-surface);
  border-color: var(--color-primary);
  color: var(--color-primary);
}

.v-button--ghost {
  background-color: transparent;
  border-color: transparent;
  color: var(--color-text-primary);
}

.v-button--ghost:hover:not(.v-button--disabled):not(.v-button--loading) {
  background-color: var(--color-surface);
  color: var(--color-primary);
}

.v-button--danger {
  background-color: var(--color-error);
  border-color: var(--color-error);
  color: white;
}

.v-button--danger:hover:not(.v-button--disabled):not(.v-button--loading) {
  background-color: color-mix(in srgb, var(--color-error) 90%, black);
  border-color: color-mix(in srgb, var(--color-error) 90%, black);
}

/* 狀態樣式 */
.v-button--disabled {
  opacity: 0.5;
  cursor: not-allowed;
  pointer-events: none;
}

.v-button--loading {
  cursor: wait;
  pointer-events: none;
}

.v-button--block {
  display: flex;
  width: 100%;
}

.v-button--round {
  border-radius: var(--radius-full);
}

.v-button--icon-only {
  padding: var(--spacing-2);
  aspect-ratio: 1;
}

.v-button--icon-only.v-button--xs {
  padding: var(--spacing-1);
}

.v-button--icon-only.v-button--sm {
  padding: var(--spacing-2);
}

.v-button--icon-only.v-button--lg {
  padding: var(--spacing-3);
}

.v-button--icon-only.v-button--xl {
  padding: var(--spacing-4);
}

/* 圖標樣式 */
.v-button__icon {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.v-button__icon svg {
  width: 1em;
  height: 1em;
}

/* 加載動畫 */
.v-button__loading {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.v-button__spinner {
  width: 1em;
  height: 1em;
  animation: spin 1s linear infinite;
}

.v-button__spinner-circle {
  stroke-dasharray: 31.416;
  stroke-dashoffset: 31.416;
  animation: dash 2s ease-in-out infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

@keyframes dash {
  0% {
    stroke-dasharray: 1, 150;
    stroke-dashoffset: 0;
  }
  50% {
    stroke-dasharray: 90, 150;
    stroke-dashoffset: -35;
  }
  100% {
    stroke-dasharray: 90, 150;
    stroke-dashoffset: -124;
  }
}

/* 加載狀態下隱藏內容 */
.v-button--loading .v-button__content,
.v-button--loading .v-button__icon {
  opacity: 0;
}

/* 焦點樣式 */
.v-button:focus-visible {
  outline: 2px solid var(--color-primary);
  outline-offset: 2px;
}

/* 響應式調整 */
@media (max-width: 640px) {
  .v-button {
    min-height: 44px; /* 移動端觸摸友好 */
  }
}

/* 深色主題適配 */
@media (prefers-color-scheme: dark) {
  .v-button--outline {
    border-color: var(--color-border);
  }
  
  .v-button--ghost:hover:not(.v-button--disabled):not(.v-button--loading) {
    background-color: color-mix(in srgb, var(--color-surface) 80%, white);
  }
}

/* 高對比度模式支持 */
@media (prefers-contrast: high) {
  .v-button {
    border-width: 2px;
  }
  
  .v-button--ghost {
    border: 2px solid var(--color-text-primary);
  }
}

/* 減少動畫偏好 */
@media (prefers-reduced-motion: reduce) {
  .v-button {
    transition: none;
  }
  
  .v-button__spinner {
    animation: none;
  }
  
  .v-button__spinner-circle {
    animation: none;
    stroke-dasharray: none;
    stroke-dashoffset: 0;
  }
}
</style>