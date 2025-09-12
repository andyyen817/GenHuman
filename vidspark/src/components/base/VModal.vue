<template>
  <!-- 模態框遮罩 -->
  <Teleport to="body">
    <Transition
      name="v-modal-backdrop"
      @enter="onBackdropEnter"
      @leave="onBackdropLeave"
    >
      <div
        v-if="modelValue"
        class="v-modal__backdrop"
        :class="backdropClasses"
        @click="handleBackdropClick"
        @keydown.esc="handleEscapeKey"
        tabindex="-1"
      >
        <!-- 模態框容器 -->
        <Transition
          name="v-modal-content"
          @enter="onContentEnter"
          @leave="onContentLeave"
        >
          <div
            v-if="modelValue"
            ref="modalRef"
            :class="modalClasses"
            :style="modalStyle"
            role="dialog"
            :aria-modal="true"
            :aria-labelledby="titleId"
            :aria-describedby="contentId"
            @click.stop
          >
            <!-- 模態框頭部 -->
            <div v-if="$slots.header || title || closable" class="v-modal__header">
              <div class="v-modal__header-content">
                <slot name="header">
                  <h2 v-if="title" :id="titleId" class="v-modal__title">
                    {{ title }}
                  </h2>
                </slot>
              </div>
              
              <button
                v-if="closable"
                type="button"
                class="v-modal__close"
                :aria-label="closeAriaLabel"
                @click="handleClose"
              >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M18 6L6 18M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </button>
            </div>

            <!-- 模態框內容 -->
            <div :id="contentId" class="v-modal__content">
              <slot />
            </div>

            <!-- 模態框底部 -->
            <div v-if="$slots.footer" class="v-modal__footer">
              <slot name="footer" />
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { computed, ref, watch, nextTick, onMounted, onUnmounted, defineEmits, defineProps } from 'vue'

/**
 * 模態框組件屬性接口
 */
export interface VModalProps {
  /** 是否顯示模態框 */
  modelValue?: boolean
  /** 模態框標題 */
  title?: string
  /** 模態框尺寸 */
  size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl' | 'full'
  /** 自定義寬度 */
  width?: string | number
  /** 自定義高度 */
  height?: string | number
  /** 最大寬度 */
  maxWidth?: string | number
  /** 最大高度 */
  maxHeight?: string | number
  /** 是否可關閉 */
  closable?: boolean
  /** 點擊遮罩是否關閉 */
  maskClosable?: boolean
  /** 按ESC是否關閉 */
  escapeClosable?: boolean
  /** 是否居中顯示 */
  centered?: boolean
  /** 是否全屏 */
  fullscreen?: boolean
  /** 是否可拖拽 */
  draggable?: boolean
  /** 是否可調整大小 */
  resizable?: boolean
  /** 層級 */
  zIndex?: number
  /** 關閉按鈕的無障礙標籤 */
  closeAriaLabel?: string
  /** 是否鎖定滾動 */
  lockScroll?: boolean
  /** 是否保持焦點 */
  trapFocus?: boolean
  /** 動畫持續時間 */
  duration?: number
}

/**
 * 模態框組件事件接口
 */
export interface VModalEmits {
  'update:modelValue': [value: boolean]
  'open': []
  'close': []
  'opened': []
  'closed': []
}

// 定義屬性
const props = withDefaults(defineProps<VModalProps>(), {
  modelValue: false,
  size: 'md',
  closable: true,
  maskClosable: true,
  escapeClosable: true,
  centered: true,
  fullscreen: false,
  draggable: false,
  resizable: false,
  zIndex: 1000,
  closeAriaLabel: '關閉',
  lockScroll: true,
  trapFocus: true,
  duration: 300
})

// 定義事件
const emit = defineEmits<VModalEmits>()

// 響應式數據
const modalRef = ref<HTMLElement>()
const titleId = ref(`v-modal-title-${Math.random().toString(36).substr(2, 9)}`)
const contentId = ref(`v-modal-content-${Math.random().toString(36).substr(2, 9)}`)
const originalOverflow = ref('')
const originalPaddingRight = ref('')
const focusableElements = ref<HTMLElement[]>([])
const lastFocusedElement = ref<HTMLElement | null>(null)

// 計算遮罩樣式類
const backdropClasses = computed(() => {
  return [
    {
      'v-modal__backdrop--centered': props.centered,
      'v-modal__backdrop--fullscreen': props.fullscreen
    }
  ]
})

// 計算模態框樣式類
const modalClasses = computed(() => {
  return [
    'v-modal',
    `v-modal--${props.size}`,
    {
      'v-modal--fullscreen': props.fullscreen,
      'v-modal--draggable': props.draggable,
      'v-modal--resizable': props.resizable
    }
  ]
})

// 計算模態框樣式
const modalStyle = computed(() => {
  const style: any = {
    zIndex: props.zIndex
  }
  
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
  
  return style
})

// 獲取可聚焦元素
const getFocusableElements = (): HTMLElement[] => {
  if (!modalRef.value) return []
  
  const focusableSelectors = [
    'button:not([disabled])',
    'input:not([disabled])',
    'select:not([disabled])',
    'textarea:not([disabled])',
    'a[href]',
    '[tabindex]:not([tabindex="-1"])'
  ].join(', ')
  
  return Array.from(modalRef.value.querySelectorAll(focusableSelectors)) as HTMLElement[]
}

// 鎖定滾動
const lockBodyScroll = () => {
  if (!props.lockScroll) return
  
  const body = document.body
  originalOverflow.value = body.style.overflow
  originalPaddingRight.value = body.style.paddingRight
  
  const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth
  
  body.style.overflow = 'hidden'
  if (scrollbarWidth > 0) {
    body.style.paddingRight = `${scrollbarWidth}px`
  }
}

// 解鎖滾動
const unlockBodyScroll = () => {
  if (!props.lockScroll) return
  
  const body = document.body
  body.style.overflow = originalOverflow.value
  body.style.paddingRight = originalPaddingRight.value
}

// 設置焦點陷阱
const setupFocusTrap = () => {
  if (!props.trapFocus) return
  
  lastFocusedElement.value = document.activeElement as HTMLElement
  
  nextTick(() => {
    focusableElements.value = getFocusableElements()
    if (focusableElements.value.length > 0) {
      focusableElements.value[0].focus()
    }
  })
}

// 移除焦點陷阱
const removeFocusTrap = () => {
  if (!props.trapFocus) return
  
  if (lastFocusedElement.value) {
    lastFocusedElement.value.focus()
  }
}

// 處理Tab鍵焦點循環
const handleTabKey = (event: KeyboardEvent) => {
  if (!props.trapFocus || event.key !== 'Tab') return
  
  const elements = getFocusableElements()
  if (elements.length === 0) return
  
  const firstElement = elements[0]
  const lastElement = elements[elements.length - 1]
  
  if (event.shiftKey) {
    if (document.activeElement === firstElement) {
      event.preventDefault()
      lastElement.focus()
    }
  } else {
    if (document.activeElement === lastElement) {
      event.preventDefault()
      firstElement.focus()
    }
  }
}

// 處理遮罩點擊
const handleBackdropClick = () => {
  if (props.maskClosable) {
    handleClose()
  }
}

// 處理ESC鍵
const handleEscapeKey = (event: KeyboardEvent) => {
  if (event.key === 'Escape' && props.escapeClosable) {
    handleClose()
  }
}

// 處理關閉
const handleClose = () => {
  emit('update:modelValue', false)
  emit('close')
}

// 遮罩進入動畫
const onBackdropEnter = () => {
  emit('open')
  lockBodyScroll()
}

// 遮罩離開動畫
const onBackdropLeave = () => {
  unlockBodyScroll()
}

// 內容進入動畫
const onContentEnter = () => {
  setupFocusTrap()
  emit('opened')
}

// 內容離開動畫
const onContentLeave = () => {
  removeFocusTrap()
  emit('closed')
}

// 監聽鍵盤事件
const handleKeydown = (event: KeyboardEvent) => {
  if (!props.modelValue) return
  
  handleTabKey(event)
  handleEscapeKey(event)
}

// 組件掛載
onMounted(() => {
  document.addEventListener('keydown', handleKeydown)
})

// 組件卸載
onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown)
  unlockBodyScroll()
})

// 監聽模態框顯示狀態變化
watch(() => props.modelValue, (newValue) => {
  if (newValue) {
    nextTick(() => {
      setupFocusTrap()
    })
  } else {
    removeFocusTrap()
  }
})
</script>

<style scoped>
/* 遮罩層 */
.v-modal__backdrop {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: color-mix(in srgb, var(--color-background) 20%, transparent);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: flex-start;
  justify-content: center;
  padding: var(--spacing-4);
  overflow-y: auto;
  z-index: 1000;
}

.v-modal__backdrop--centered {
  align-items: center;
}

.v-modal__backdrop--fullscreen {
  padding: 0;
}

/* 模態框 */
.v-modal {
  position: relative;
  background-color: var(--color-background);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-xl);
  display: flex;
  flex-direction: column;
  max-height: 90vh;
  margin: auto;
  overflow: hidden;
}

/* 尺寸變體 */
.v-modal--xs {
  width: 100%;
  max-width: 320px;
}

.v-modal--sm {
  width: 100%;
  max-width: 480px;
}

.v-modal--md {
  width: 100%;
  max-width: 640px;
}

.v-modal--lg {
  width: 100%;
  max-width: 800px;
}

.v-modal--xl {
  width: 100%;
  max-width: 1024px;
}

.v-modal--full {
  width: 100%;
  max-width: none;
}

.v-modal--fullscreen {
  width: 100vw;
  height: 100vh;
  max-width: none;
  max-height: none;
  border-radius: 0;
  margin: 0;
}

/* 可拖拽 */
.v-modal--draggable .v-modal__header {
  cursor: move;
  user-select: none;
}

/* 可調整大小 */
.v-modal--resizable {
  resize: both;
  overflow: auto;
}

/* 模態框頭部 */
.v-modal__header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: var(--spacing-4);
  padding: var(--spacing-6);
  border-bottom: 1px solid var(--color-border);
  background-color: var(--color-surface);
}

.v-modal__header-content {
  flex: 1;
  min-width: 0;
}

.v-modal__title {
  margin: 0;
  font-size: var(--font-size-lg);
  font-weight: 600;
  color: var(--color-text-primary);
  line-height: var(--line-height-tight);
}

.v-modal__close {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 2rem;
  height: 2rem;
  padding: 0;
  background: none;
  border: none;
  border-radius: var(--radius-md);
  color: var(--color-text-secondary);
  cursor: pointer;
  transition: all var(--duration-fast) var(--easing-ease-in-out);
  flex-shrink: 0;
}

.v-modal__close:hover {
  background-color: var(--color-surface);
  color: var(--color-text-primary);
}

.v-modal__close:focus {
  outline: 2px solid var(--color-primary);
  outline-offset: 2px;
}

.v-modal__close svg {
  width: 1.25rem;
  height: 1.25rem;
}

/* 模態框內容 */
.v-modal__content {
  flex: 1;
  padding: var(--spacing-6);
  overflow-y: auto;
  color: var(--color-text-primary);
  line-height: var(--line-height-relaxed);
}

/* 模態框底部 */
.v-modal__footer {
  padding: var(--spacing-4) var(--spacing-6);
  border-top: 1px solid var(--color-border);
  background-color: var(--color-surface);
  display: flex;
  justify-content: flex-end;
  gap: var(--spacing-3);
}

/* 動畫 */
.v-modal-backdrop-enter-active,
.v-modal-backdrop-leave-active {
  transition: all 300ms var(--easing-ease-in-out);
}

.v-modal-backdrop-enter-from,
.v-modal-backdrop-leave-to {
  opacity: 0;
}

.v-modal-content-enter-active,
.v-modal-content-leave-active {
  transition: all 300ms var(--easing-ease-in-out);
}

.v-modal-content-enter-from,
.v-modal-content-leave-to {
  opacity: 0;
  transform: scale(0.95) translateY(-20px);
}

/* 響應式調整 */
@media (max-width: 768px) {
  .v-modal__backdrop {
    padding: var(--spacing-2);
  }
  
  .v-modal {
    max-height: 95vh;
  }
  
  .v-modal--xs,
  .v-modal--sm,
  .v-modal--md,
  .v-modal--lg,
  .v-modal--xl {
    width: 100%;
    max-width: none;
  }
  
  .v-modal__header,
  .v-modal__content,
  .v-modal__footer {
    padding-left: var(--spacing-4);
    padding-right: var(--spacing-4);
  }
  
  .v-modal__header {
    padding-top: var(--spacing-4);
    padding-bottom: var(--spacing-4);
  }
  
  .v-modal__content {
    padding-top: var(--spacing-4);
    padding-bottom: var(--spacing-4);
  }
  
  .v-modal__footer {
    flex-direction: column;
    align-items: stretch;
  }
}

@media (max-width: 480px) {
  .v-modal__backdrop {
    padding: 0;
  }
  
  .v-modal {
    width: 100vw;
    height: 100vh;
    max-height: none;
    border-radius: 0;
    margin: 0;
  }
}

/* 深色主題適配 */
@media (prefers-color-scheme: dark) {
  .v-modal {
    background-color: var(--color-background-dark);
    border: 1px solid var(--color-border-dark);
  }
  
  .v-modal__header,
  .v-modal__footer {
    background-color: var(--color-surface-dark);
    border-color: var(--color-border-dark);
  }
  
  .v-modal__title {
    color: var(--color-text-primary-dark);
  }
  
  .v-modal__content {
    color: var(--color-text-primary-dark);
  }
  
  .v-modal__close {
    color: var(--color-text-secondary-dark);
  }
  
  .v-modal__close:hover {
    background-color: var(--color-surface-dark);
    color: var(--color-text-primary-dark);
  }
}

/* 減少動畫偏好 */
@media (prefers-reduced-motion: reduce) {
  .v-modal-backdrop-enter-active,
  .v-modal-backdrop-leave-active,
  .v-modal-content-enter-active,
  .v-modal-content-leave-active {
    transition: none;
  }
  
  .v-modal__close {
    transition: none;
  }
}

/* 高對比度模式 */
@media (prefers-contrast: high) {
  .v-modal {
    border: 2px solid var(--color-text-primary);
  }
  
  .v-modal__header,
  .v-modal__footer {
    border-color: var(--color-text-primary);
  }
  
  .v-modal__close {
    border: 1px solid var(--color-text-secondary);
  }
}
</style>