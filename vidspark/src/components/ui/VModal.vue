<template>
  <Teleport to="body">
    <Transition name="modal" appear>
      <div
        v-if="modelValue"
        class="v-modal-overlay"
        :class="overlayClasses"
        @click="handleOverlayClick"
      >
        <div
          ref="modalRef"
          class="v-modal-container"
          :class="containerClasses"
          @click.stop
        >
          <!-- 標題欄 -->
          <div v-if="title || $slots.header || closable" class="v-modal-header">
            <div class="v-modal-title">
              <slot name="header">
                <h3 v-if="title" class="v-modal-title-text">{{ title }}</h3>
              </slot>
            </div>
            
            <button
              v-if="closable"
              type="button"
              class="v-modal-close"
              @click="handleClose"
            >
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
              </svg>
            </button>
          </div>
          
          <!-- 內容區域 -->
          <div class="v-modal-body" :class="bodyClasses">
            <slot />
          </div>
          
          <!-- 底部操作區 -->
          <div v-if="$slots.footer" class="v-modal-footer">
            <slot name="footer" />
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { computed, ref, watch, nextTick, onMounted, onUnmounted } from 'vue'

interface Props {
  modelValue: boolean
  title?: string
  size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl' | 'full'
  closable?: boolean
  maskClosable?: boolean
  persistent?: boolean
  centered?: boolean
  scrollable?: boolean
  fullscreen?: boolean
  zIndex?: number
}

const props = withDefaults(defineProps<Props>(), {
  size: 'md',
  closable: true,
  maskClosable: true,
  persistent: false,
  centered: true,
  scrollable: true,
  fullscreen: false,
  zIndex: 1000
})

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  close: []
  open: []
  'before-close': []
}>()

const modalRef = ref<HTMLElement>()

const overlayClasses = computed(() => {
  const classes = ['v-modal-overlay--base']
  
  if (props.centered) {
    classes.push('v-modal-overlay--centered')
  }
  
  return classes
})

const containerClasses = computed(() => {
  const classes = ['v-modal-container--base']
  
  if (props.fullscreen) {
    classes.push('v-modal-container--fullscreen')
  } else {
    classes.push(`v-modal-container--${props.size}`)
  }
  
  if (props.scrollable) {
    classes.push('v-modal-container--scrollable')
  }
  
  return classes
})

const bodyClasses = computed(() => {
  const classes = ['v-modal-body--base']
  
  if (props.scrollable) {
    classes.push('v-modal-body--scrollable')
  }
  
  return classes
})

const handleClose = () => {
  if (!props.persistent) {
    emit('before-close')
    emit('update:modelValue', false)
    emit('close')
  }
}

const handleOverlayClick = () => {
  if (props.maskClosable) {
    handleClose()
  }
}

// 鍵盤事件處理
const handleKeydown = (event: KeyboardEvent) => {
  if (event.key === 'Escape' && props.modelValue && props.closable) {
    handleClose()
  }
}

// 焦點管理
const focusModal = () => {
  nextTick(() => {
    if (modalRef.value) {
      modalRef.value.focus()
    }
  })
}

// 監聽模態框開關
watch(() => props.modelValue, (newValue) => {
  if (newValue) {
    emit('open')
    focusModal()
    // 防止背景滾動
    document.body.style.overflow = 'hidden'
  } else {
    // 恢復背景滾動
    document.body.style.overflow = ''
  }
})

// 生命週期
onMounted(() => {
  document.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown)
  // 確保恢復背景滾動
  document.body.style.overflow = ''
})
</script>

<style scoped>
.v-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  z-index: v-bind(zIndex);
  overflow-y: auto;
}

.v-modal-overlay--base {
  display: flex;
  padding: 1rem;
}

.v-modal-overlay--centered {
  align-items: center;
  justify-content: center;
}

.v-modal-container {
  background-color: white;
  border-radius: 0.5rem;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  max-height: calc(100vh - 2rem);
  outline: none;
}

.v-modal-container--base {
  /* 基礎樣式 */
}

/* 尺寸變體 */
.v-modal-container--xs {
  width: 100%;
  max-width: 20rem;
}

.v-modal-container--sm {
  width: 100%;
  max-width: 24rem;
}

.v-modal-container--md {
  width: 100%;
  max-width: 32rem;
}

.v-modal-container--lg {
  width: 100%;
  max-width: 48rem;
}

.v-modal-container--xl {
  width: 100%;
  max-width: 64rem;
}

.v-modal-container--fullscreen {
  width: 100vw;
  height: 100vh;
  max-width: none;
  max-height: none;
  border-radius: 0;
  margin: 0;
}

.v-modal-container--scrollable {
  overflow: hidden;
}

/* 標題欄 */
.v-modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem 1.5rem 0 1.5rem;
  border-bottom: 1px solid #f3f4f6;
  flex-shrink: 0;
}

.v-modal-title {
  flex: 1;
}

.v-modal-title-text {
  font-size: 1.125rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

.v-modal-close {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 2rem;
  height: 2rem;
  border: none;
  background: none;
  color: #6b7280;
  cursor: pointer;
  border-radius: 0.25rem;
  transition: all 150ms ease-in-out;
  margin-left: 1rem;
}

.v-modal-close:hover {
  background-color: #f3f4f6;
  color: #374151;
}

.v-modal-close:focus {
  outline: 2px solid #0ea5e9;
  outline-offset: 2px;
}

/* 內容區域 */
.v-modal-body--base {
  padding: 1.5rem;
  flex: 1;
}

.v-modal-body--scrollable {
  overflow-y: auto;
}

/* 底部操作區 */
.v-modal-footer {
  padding: 1rem 1.5rem 1.5rem 1.5rem;
  border-top: 1px solid #f3f4f6;
  background-color: #fafafa;
  flex-shrink: 0;
}

/* 動畫效果 */
.modal-enter-active,
.modal-leave-active {
  transition: all 200ms ease-out;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from .v-modal-container,
.modal-leave-to .v-modal-container {
  transform: scale(0.95) translateY(-20px);
}

.modal-enter-to .v-modal-container,
.modal-leave-from .v-modal-container {
  transform: scale(1) translateY(0);
}

/* 響應式設計 */
@media (max-width: 640px) {
  .v-modal-overlay {
    padding: 0;
  }
  
  .v-modal-overlay--centered {
    align-items: flex-end;
  }
  
  .v-modal-container {
    width: 100% !important;
    max-width: none !important;
    max-height: 90vh;
    border-radius: 0.5rem 0.5rem 0 0;
    margin: 0;
  }
  
  .v-modal-header {
    padding: 1rem;
  }
  
  .v-modal-body--base {
    padding: 1rem;
  }
  
  .v-modal-footer {
    padding: 1rem;
  }
  
  /* 移動端動畫 */
  .modal-enter-from .v-modal-container,
  .modal-leave-to .v-modal-container {
    transform: translateY(100%);
  }
  
  .modal-enter-to .v-modal-container,
  .modal-leave-from .v-modal-container {
    transform: translateY(0);
  }
}

/* 深色模式支持 */
@media (prefers-color-scheme: dark) {
  .v-modal-container {
    background-color: #1f2937;
    color: #f3f4f6;
  }
  
  .v-modal-header {
    border-bottom-color: #374151;
  }
  
  .v-modal-title-text {
    color: #f3f4f6;
  }
  
  .v-modal-close {
    color: #9ca3af;
  }
  
  .v-modal-close:hover {
    background-color: #374151;
    color: #f3f4f6;
  }
  
  .v-modal-footer {
    background-color: #111827;
    border-top-color: #374151;
  }
}

/* 無障礙支持 */
@media (prefers-reduced-motion: reduce) {
  .modal-enter-active,
  .modal-leave-active {
    transition: none;
  }
}

/* 高對比度模式 */
@media (prefers-contrast: high) {
  .v-modal-container {
    border: 2px solid;
  }
  
  .v-modal-close:focus {
    outline: 3px solid;
  }
}
</style>