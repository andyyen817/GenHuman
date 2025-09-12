<template>
  <div :class="cardClasses" @click="handleClick">
    <div v-if="$slots.header" class="v-card-header">
      <slot name="header" />
    </div>
    
    <div v-if="$slots.default" class="v-card-body" :class="bodyClasses">
      <slot />
    </div>
    
    <div v-if="$slots.footer" class="v-card-footer">
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  variant?: 'elevated' | 'outlined' | 'flat'
  padding?: 'none' | 'sm' | 'md' | 'lg' | 'xl'
  hoverable?: boolean
  clickable?: boolean
  loading?: boolean
  disabled?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'elevated',
  padding: 'md',
  hoverable: false,
  clickable: false,
  loading: false,
  disabled: false
})

const emit = defineEmits<{
  click: [event: MouseEvent]
}>()

const cardClasses = computed(() => {
  const classes = ['v-card']
  
  classes.push(`v-card--${props.variant}`)
  
  if (props.hoverable) {
    classes.push('v-card--hoverable')
  }
  
  if (props.clickable) {
    classes.push('v-card--clickable')
  }
  
  if (props.loading) {
    classes.push('v-card--loading')
  }
  
  if (props.disabled) {
    classes.push('v-card--disabled')
  }
  
  return classes
})

const bodyClasses = computed(() => {
  const classes = ['v-card-body--base']
  
  classes.push(`v-card-body--${props.padding}`)
  
  return classes
})

const handleClick = (event: MouseEvent) => {
  if (!props.disabled && !props.loading) {
    emit('click', event)
  }
}
</script>

<style scoped>
.v-card {
  background-color: white;
  border-radius: 0.5rem;
  overflow: hidden;
  transition: all 150ms ease-in-out;
  position: relative;
}

/* 變體樣式 */
.v-card--elevated {
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
}

.v-card--outlined {
  border: 1px solid #e5e7eb;
  box-shadow: none;
}

.v-card--flat {
  box-shadow: none;
}

/* 互動樣式 */
.v-card--hoverable:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
}

.v-card--clickable {
  cursor: pointer;
}

.v-card--clickable:hover {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
}

.v-card--clickable:active {
  transform: translateY(1px);
}

/* 狀態樣式 */
.v-card--loading {
  pointer-events: none;
  opacity: 0.7;
}

.v-card--disabled {
  pointer-events: none;
  opacity: 0.5;
  cursor: not-allowed;
}

/* 卡片區域 */
.v-card-header {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #f3f4f6;
  background-color: #fafafa;
}

.v-card-body--base {
  /* 基礎樣式 */
}

.v-card-body--none {
  padding: 0;
}

.v-card-body--sm {
  padding: 0.75rem;
}

.v-card-body--md {
  padding: 1.5rem;
}

.v-card-body--lg {
  padding: 2rem;
}

.v-card-body--xl {
  padding: 3rem;
}

.v-card-footer {
  padding: 1rem 1.5rem;
  border-top: 1px solid #f3f4f6;
  background-color: #fafafa;
}

/* 響應式設計 */
@media (max-width: 640px) {
  .v-card {
    border-radius: 0.375rem;
  }
  
  .v-card-header,
  .v-card-footer {
    padding: 0.75rem 1rem;
  }
  
  .v-card-body--sm {
    padding: 0.5rem;
  }
  
  .v-card-body--md {
    padding: 1rem;
  }
  
  .v-card-body--lg {
    padding: 1.5rem;
  }
  
  .v-card-body--xl {
    padding: 2rem;
  }
}

/* 深色模式支持 */
@media (prefers-color-scheme: dark) {
  .v-card {
    background-color: #1f2937;
    color: #f3f4f6;
  }
  
  .v-card--outlined {
    border-color: #374151;
  }
  
  .v-card-header,
  .v-card-footer {
    background-color: #111827;
    border-color: #374151;
  }
}

/* 載入動畫 */
.v-card--loading::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255, 255, 255, 0.2),
    transparent
  );
  animation: loading 1.5s infinite;
}

@keyframes loading {
  0% {
    left: -100%;
  }
  100% {
    left: 100%;
  }
}

/* 焦點樣式 */
.v-card--clickable:focus-visible {
  outline: 2px solid #0ea5e9;
  outline-offset: 2px;
}

/* 特殊效果 */
.v-card--hoverable {
  will-change: transform;
}

.v-card--elevated.v-card--hoverable:hover {
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
}
</style>