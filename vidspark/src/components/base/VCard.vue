<template>
  <div :class="cardClasses" :style="cardStyle">
    <!-- 卡片頭部 -->
    <div v-if="$slots.header || title || $slots.actions" class="v-card__header">
      <div class="v-card__header-content">
        <slot name="header">
          <h3 v-if="title" class="v-card__title">{{ title }}</h3>
          <p v-if="subtitle" class="v-card__subtitle">{{ subtitle }}</p>
        </slot>
      </div>
      <div v-if="$slots.actions" class="v-card__actions">
        <slot name="actions" />
      </div>
    </div>

    <!-- 卡片圖片 -->
    <div v-if="image || $slots.image" class="v-card__image">
      <slot name="image">
        <img
          v-if="image"
          :src="image"
          :alt="imageAlt"
          class="v-card__img"
          :loading="imageLoading"
        />
      </slot>
    </div>

    <!-- 卡片內容 -->
    <div v-if="$slots.default || content" class="v-card__content">
      <slot>
        <p v-if="content" class="v-card__text">{{ content }}</p>
      </slot>
    </div>

    <!-- 卡片底部 -->
    <div v-if="$slots.footer" class="v-card__footer">
      <slot name="footer" />
    </div>

    <!-- 加載遮罩 -->
    <div v-if="loading" class="v-card__loading">
      <div class="v-card__spinner"></div>
      <span v-if="loadingText" class="v-card__loading-text">{{ loadingText }}</span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, defineProps } from 'vue'

/**
 * 卡片組件屬性接口
 */
export interface VCardProps {
  /** 卡片標題 */
  title?: string
  /** 卡片副標題 */
  subtitle?: string
  /** 卡片內容 */
  content?: string
  /** 卡片圖片 */
  image?: string
  /** 圖片替代文本 */
  imageAlt?: string
  /** 圖片加載方式 */
  imageLoading?: 'lazy' | 'eager'
  /** 卡片變體 */
  variant?: 'default' | 'outlined' | 'elevated' | 'filled'
  /** 卡片尺寸 */
  size?: 'sm' | 'md' | 'lg'
  /** 是否可點擊 */
  clickable?: boolean
  /** 是否禁用 */
  disabled?: boolean
  /** 是否加載中 */
  loading?: boolean
  /** 加載文本 */
  loadingText?: string
  /** 自定義寬度 */
  width?: string | number
  /** 自定義高度 */
  height?: string | number
  /** 最大寬度 */
  maxWidth?: string | number
  /** 最大高度 */
  maxHeight?: string | number
  /** 邊框半徑 */
  rounded?: boolean | 'sm' | 'md' | 'lg' | 'xl' | 'full'
  /** 陰影 */
  shadow?: boolean | 'sm' | 'md' | 'lg' | 'xl'
  /** 邊框 */
  border?: boolean
  /** 懸停效果 */
  hover?: boolean
  /** 背景色 */
  background?: string
  /** 文本顏色 */
  color?: string
}

// 定義屬性
const props = withDefaults(defineProps<VCardProps>(), {
  variant: 'default',
  size: 'md',
  clickable: false,
  disabled: false,
  loading: false,
  imageLoading: 'lazy',
  rounded: true,
  shadow: false,
  border: false,
  hover: false
})

// 計算卡片樣式類
const cardClasses = computed(() => {
  return [
    'v-card',
    `v-card--${props.variant}`,
    `v-card--${props.size}`,
    {
      'v-card--clickable': props.clickable && !props.disabled,
      'v-card--disabled': props.disabled,
      'v-card--loading': props.loading,
      'v-card--hover': props.hover && !props.disabled,
      'v-card--border': props.border,
      [`v-card--rounded-${props.rounded}`]: typeof props.rounded === 'string',
      'v-card--rounded': props.rounded === true,
      [`v-card--shadow-${props.shadow}`]: typeof props.shadow === 'string',
      'v-card--shadow': props.shadow === true
    }
  ]
})

// 計算卡片樣式
const cardStyle = computed(() => {
  const style: any = {}
  
  if (props.width) {
    style.width = typeof props.width === 'number' ? `${props.width}px` : props.width
  }
  
  if (props.height) {
    style.height = typeof props.height === 'number' ? `${props.height}px` : props.height
  }
  
  if (props.maxWidth) {
    style.maxWidth = typeof props.maxWidth === 'number' ? `${props.maxWidth}px` : props.maxWidth
  }
  
  if (props.maxHeight) {
    style.maxHeight = typeof props.maxHeight === 'number' ? `${props.maxHeight}px` : props.maxHeight
  }
  
  if (props.background) {
    style.backgroundColor = props.background
  }
  
  if (props.color) {
    style.color = props.color
  }
  
  return style
})
</script>

<style scoped>
.v-card {
  position: relative;
  display: flex;
  flex-direction: column;
  background-color: var(--color-background);
  border-radius: var(--radius-md);
  overflow: hidden;
  transition: all var(--duration-normal) var(--easing-ease-in-out);
}

/* 變體樣式 */
.v-card--default {
  background-color: var(--color-background);
  border: 1px solid var(--color-border);
}

.v-card--outlined {
  background-color: transparent;
  border: 2px solid var(--color-border);
}

.v-card--elevated {
  background-color: var(--color-background);
  box-shadow: var(--shadow-md);
  border: none;
}

.v-card--filled {
  background-color: var(--color-surface);
  border: none;
}

/* 尺寸變體 */
.v-card--sm {
  font-size: var(--font-size-sm);
}

.v-card--md {
  font-size: var(--font-size-base);
}

.v-card--lg {
  font-size: var(--font-size-lg);
}

/* 圓角變體 */
.v-card--rounded {
  border-radius: var(--radius-md);
}

.v-card--rounded-sm {
  border-radius: var(--radius-sm);
}

.v-card--rounded-md {
  border-radius: var(--radius-md);
}

.v-card--rounded-lg {
  border-radius: var(--radius-lg);
}

.v-card--rounded-xl {
  border-radius: var(--radius-xl);
}

.v-card--rounded-full {
  border-radius: 9999px;
}

/* 陰影變體 */
.v-card--shadow {
  box-shadow: var(--shadow-md);
}

.v-card--shadow-sm {
  box-shadow: var(--shadow-sm);
}

.v-card--shadow-md {
  box-shadow: var(--shadow-md);
}

.v-card--shadow-lg {
  box-shadow: var(--shadow-lg);
}

.v-card--shadow-xl {
  box-shadow: var(--shadow-xl);
}

/* 邊框 */
.v-card--border {
  border: 1px solid var(--color-border);
}

/* 可點擊狀態 */
.v-card--clickable {
  cursor: pointer;
  user-select: none;
}

.v-card--clickable:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

.v-card--clickable:active {
  transform: translateY(0);
  box-shadow: var(--shadow-md);
}

/* 懸停效果 */
.v-card--hover:hover {
  box-shadow: var(--shadow-lg);
  border-color: var(--color-primary);
}

/* 禁用狀態 */
.v-card--disabled {
  opacity: 0.6;
  cursor: not-allowed;
  pointer-events: none;
}

/* 卡片頭部 */
.v-card__header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: var(--spacing-4);
  padding: var(--spacing-6);
  border-bottom: 1px solid var(--color-border);
}

.v-card__header-content {
  flex: 1;
  min-width: 0;
}

.v-card__title {
  margin: 0;
  font-size: var(--font-size-lg);
  font-weight: 600;
  color: var(--color-text-primary);
  line-height: var(--line-height-tight);
}

.v-card__subtitle {
  margin: var(--spacing-1) 0 0;
  font-size: var(--font-size-sm);
  color: var(--color-text-secondary);
  line-height: var(--line-height-normal);
}

.v-card__actions {
  display: flex;
  align-items: center;
  gap: var(--spacing-2);
  flex-shrink: 0;
}

/* 卡片圖片 */
.v-card__image {
  position: relative;
  overflow: hidden;
}

.v-card__img {
  width: 100%;
  height: auto;
  display: block;
  object-fit: cover;
  transition: transform var(--duration-normal) var(--easing-ease-in-out);
}

.v-card--clickable .v-card__img:hover {
  transform: scale(1.05);
}

/* 卡片內容 */
.v-card__content {
  flex: 1;
  padding: var(--spacing-6);
}

.v-card__text {
  margin: 0;
  color: var(--color-text-primary);
  line-height: var(--line-height-relaxed);
}

/* 卡片底部 */
.v-card__footer {
  padding: var(--spacing-4) var(--spacing-6);
  border-top: 1px solid var(--color-border);
  background-color: var(--color-surface);
}

/* 尺寸調整 */
.v-card--sm .v-card__header,
.v-card--sm .v-card__content {
  padding: var(--spacing-4);
}

.v-card--sm .v-card__footer {
  padding: var(--spacing-3) var(--spacing-4);
}

.v-card--sm .v-card__title {
  font-size: var(--font-size-base);
}

.v-card--sm .v-card__subtitle {
  font-size: var(--font-size-xs);
}

.v-card--lg .v-card__header,
.v-card--lg .v-card__content {
  padding: var(--spacing-8);
}

.v-card--lg .v-card__footer {
  padding: var(--spacing-6) var(--spacing-8);
}

.v-card--lg .v-card__title {
  font-size: var(--font-size-xl);
}

.v-card--lg .v-card__subtitle {
  font-size: var(--font-size-base);
}

/* 加載狀態 */
.v-card__loading {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap: var(--spacing-3);
  background-color: color-mix(in srgb, var(--color-background) 90%, transparent);
  backdrop-filter: blur(2px);
  z-index: 10;
}

.v-card__spinner {
  width: 2rem;
  height: 2rem;
  border: 2px solid var(--color-border);
  border-top: 2px solid var(--color-primary);
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

.v-card__loading-text {
  font-size: var(--font-size-sm);
  color: var(--color-text-secondary);
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* 響應式調整 */
@media (max-width: 768px) {
  .v-card__header {
    flex-direction: column;
    align-items: stretch;
    gap: var(--spacing-3);
  }
  
  .v-card__actions {
    justify-content: flex-end;
  }
  
  .v-card--sm .v-card__header,
  .v-card--sm .v-card__content {
    padding: var(--spacing-3);
  }
  
  .v-card--md .v-card__header,
  .v-card--md .v-card__content {
    padding: var(--spacing-4);
  }
  
  .v-card--lg .v-card__header,
  .v-card--lg .v-card__content {
    padding: var(--spacing-6);
  }
}

/* 深色主題適配 */
@media (prefers-color-scheme: dark) {
  .v-card--default {
    background-color: var(--color-background-dark);
    border-color: var(--color-border-dark);
  }
  
  .v-card--filled {
    background-color: var(--color-surface-dark);
  }
  
  .v-card__header {
    border-bottom-color: var(--color-border-dark);
  }
  
  .v-card__footer {
    border-top-color: var(--color-border-dark);
    background-color: var(--color-surface-dark);
  }
  
  .v-card__title {
    color: var(--color-text-primary-dark);
  }
  
  .v-card__subtitle,
  .v-card__loading-text {
    color: var(--color-text-secondary-dark);
  }
  
  .v-card__text {
    color: var(--color-text-primary-dark);
  }
}

/* 減少動畫偏好 */
@media (prefers-reduced-motion: reduce) {
  .v-card,
  .v-card__img {
    transition: none;
  }
  
  .v-card--clickable:hover {
    transform: none;
  }
  
  .v-card--clickable .v-card__img:hover {
    transform: none;
  }
  
  .v-card__spinner {
    animation: none;
  }
}

/* 高對比度模式 */
@media (prefers-contrast: high) {
  .v-card--default,
  .v-card--outlined {
    border-width: 2px;
    border-color: var(--color-text-primary);
  }
  
  .v-card__header,
  .v-card__footer {
    border-color: var(--color-text-primary);
  }
}
</style>