<template>
  <button
    :class="buttonClasses"
    :disabled="disabled"
    :type="type"
    @click="handleClick"
  >
    <slot name="icon-left" v-if="$slots['icon-left']" />
    <span v-if="$slots.default" class="button-text">
      <slot />
    </span>
    <slot name="icon-right" v-if="$slots['icon-right']" />
  </button>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { designSystem } from '@/config/design-system'

interface Props {
  variant?: 'primary' | 'secondary' | 'outline' | 'ghost' | 'danger'
  size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl'
  disabled?: boolean
  loading?: boolean
  type?: 'button' | 'submit' | 'reset'
  block?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'primary',
  size: 'md',
  disabled: false,
  loading: false,
  type: 'button',
  block: false
})

const emit = defineEmits<{
  click: [event: MouseEvent]
}>()

const buttonClasses = computed(() => {
  const classes = ['v-button']
  
  classes.push(`v-button--${props.variant}`)
  classes.push(`v-button--${props.size}`)
  
  if (props.disabled || props.loading) {
    classes.push('v-button--disabled')
  }
  
  if (props.block) {
    classes.push('v-button--block')
  }
  
  if (props.loading) {
    classes.push('v-button--loading')
  }
  
  return classes
})

const handleClick = (event: MouseEvent) => {
  if (!props.disabled && !props.loading) {
    emit('click', event)
  }
}
</script>

<style scoped>
.v-button {
  /* 基礎樣式 */
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  border-radius: 0.375rem;
  font-weight: 500;
  line-height: 1;
  transition: all 150ms ease-in-out;
  cursor: pointer;
  user-select: none;
  outline: none;
  border: none;
  text-decoration: none;
  white-space: nowrap;
}

/* 尺寸變體 */
.v-button--xs {
  padding: 0.25rem 0.5rem;
  font-size: 0.75rem;
}

.v-button--sm {
  padding: 0.5rem 0.75rem;
  font-size: 0.875rem;
}

.v-button--md {
  padding: 0.75rem 1rem;
  font-size: 1rem;
}

.v-button--lg {
  padding: 1rem 1.5rem;
  font-size: 1.125rem;
}

.v-button--xl {
  padding: 1rem 2rem;
  font-size: 1.25rem;
}

/* 顏色變體 */
.v-button--primary {
  background-color: #0ea5e9;
  color: white;
}

.v-button--primary:hover:not(.v-button--disabled) {
  background-color: #0284c7;
}

.v-button--primary:active:not(.v-button--disabled) {
  background-color: #0369a1;
}

.v-button--secondary {
  background-color: #f3f4f6;
  color: #111827;
}

.v-button--secondary:hover:not(.v-button--disabled) {
  background-color: #e5e7eb;
}

.v-button--secondary:active:not(.v-button--disabled) {
  background-color: #d1d5db;
}

.v-button--outline {
  background-color: transparent;
  color: #0ea5e9;
  border: 1px solid #0ea5e9;
}

.v-button--outline:hover:not(.v-button--disabled) {
  background-color: #f0f9ff;
}

.v-button--outline:active:not(.v-button--disabled) {
  background-color: #e0f2fe;
}

.v-button--ghost {
  background-color: transparent;
  color: #4b5563;
}

.v-button--ghost:hover:not(.v-button--disabled) {
  background-color: #f3f4f6;
  color: #111827;
}

.v-button--ghost:active:not(.v-button--disabled) {
  background-color: #e5e7eb;
}

.v-button--danger {
  background-color: #ef4444;
  color: white;
}

.v-button--danger:hover:not(.v-button--disabled) {
  background-color: #dc2626;
}

.v-button--danger:active:not(.v-button--disabled) {
  background-color: #b91c1c;
}

/* 狀態樣式 */
.v-button--disabled {
  background-color: #d1d5db !important;
  color: #9ca3af !important;
  cursor: not-allowed !important;
  border-color: #d1d5db !important;
}

.v-button--loading {
  cursor: wait;
}

.v-button--block {
  width: 100%;
}

/* 響應式設計 */
@media (max-width: 640px) {
  .v-button {
    min-height: 44px; /* 移動端觸控友好 */
  }
}

/* 焦點樣式 */
.v-button:focus-visible {
  outline: 2px solid #0ea5e9;
  outline-offset: 2px;
}

/* 動畫效果 */
.v-button--loading .button-text {
  opacity: 0.7;
}

/* 圖標間距 */
.v-button:has(.button-text) {
  gap: 0.5rem;
}
</style>