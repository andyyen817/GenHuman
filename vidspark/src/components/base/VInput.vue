<template>
  <div :class="wrapperClasses">
    <label v-if="label" :for="inputId" class="v-input__label">
      {{ label }}
      <span v-if="required" class="v-input__required">*</span>
    </label>
    
    <div class="v-input__container">
      <span v-if="prefixIcon" class="v-input__icon v-input__icon--prefix">
        <component :is="prefixIcon" />
      </span>
      
      <input
        :id="inputId"
        ref="inputRef"
        :class="inputClasses"
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :readonly="readonly"
        :required="required"
        :autocomplete="autocomplete"
        :maxlength="maxlength"
        :minlength="minlength"
        :min="min"
        :max="max"
        :step="step"
        :pattern="pattern"
        v-bind="$attrs"
        @input="handleInput"
        @change="handleChange"
        @focus="handleFocus"
        @blur="handleBlur"
        @keydown="handleKeydown"
      />
      
      <span v-if="suffixIcon || clearable" class="v-input__icon v-input__icon--suffix">
        <button
          v-if="clearable && modelValue && !disabled && !readonly"
          type="button"
          class="v-input__clear"
          @click="handleClear"
          :aria-label="'清除輸入'"
        >
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
        <component v-else-if="suffixIcon" :is="suffixIcon" />
      </span>
    </div>
    
    <div v-if="hasMessage" class="v-input__message">
      <span v-if="errorMessage" class="v-input__error">
        {{ errorMessage }}
      </span>
      <span v-else-if="helpText" class="v-input__help">
        {{ helpText }}
      </span>
    </div>
    
    <div v-if="showCharCount" class="v-input__char-count">
      {{ currentLength }}/{{ maxlength }}
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, nextTick, defineEmits, defineProps } from 'vue'

/**
 * 輸入框組件屬性接口
 */
export interface VInputProps {
  /** 輸入值 */
  modelValue?: string | number
  /** 輸入框類型 */
  type?: 'text' | 'password' | 'email' | 'number' | 'tel' | 'url' | 'search'
  /** 標籤文本 */
  label?: string
  /** 佔位符文本 */
  placeholder?: string
  /** 幫助文本 */
  helpText?: string
  /** 錯誤信息 */
  errorMessage?: string
  /** 輸入框尺寸 */
  size?: 'sm' | 'md' | 'lg'
  /** 是否禁用 */
  disabled?: boolean
  /** 是否只讀 */
  readonly?: boolean
  /** 是否必填 */
  required?: boolean
  /** 是否可清除 */
  clearable?: boolean
  /** 前綴圖標 */
  prefixIcon?: any
  /** 後綴圖標 */
  suffixIcon?: any
  /** 自動完成 */
  autocomplete?: string
  /** 最大長度 */
  maxlength?: number
  /** 最小長度 */
  minlength?: number
  /** 最小值（數字類型） */
  min?: number
  /** 最大值（數字類型） */
  max?: number
  /** 步長（數字類型） */
  step?: number
  /** 正則模式 */
  pattern?: string
  /** 是否顯示字符計數 */
  showCharCount?: boolean
}

/**
 * 輸入框組件事件接口
 */
export interface VInputEmits {
  'update:modelValue': [value: string | number]
  input: [event: Event]
  change: [event: Event]
  focus: [event: FocusEvent]
  blur: [event: FocusEvent]
  clear: []
  keydown: [event: KeyboardEvent]
}

// 定義屬性
const props = withDefaults(defineProps<VInputProps>(), {
  type: 'text',
  size: 'md',
  disabled: false,
  readonly: false,
  required: false,
  clearable: false,
  showCharCount: false
})

// 定義事件
const emit = defineEmits<VInputEmits>()

// 響應式引用
const inputRef = ref<HTMLInputElement>()
const isFocused = ref(false)

// 生成唯一ID
const inputId = computed(() => `v-input-${Math.random().toString(36).substr(2, 9)}`)

// 計算當前字符長度
const currentLength = computed(() => {
  return String(props.modelValue || '').length
})

// 是否有消息顯示
const hasMessage = computed(() => {
  return !!(props.errorMessage || props.helpText)
})

// 包裝器樣式類
const wrapperClasses = computed(() => {
  return [
    'v-input',
    `v-input--${props.size}`,
    {
      'v-input--disabled': props.disabled,
      'v-input--readonly': props.readonly,
      'v-input--error': props.errorMessage,
      'v-input--focused': isFocused.value,
      'v-input--has-prefix': props.prefixIcon,
      'v-input--has-suffix': props.suffixIcon || props.clearable
    }
  ]
})

// 輸入框樣式類
const inputClasses = computed(() => {
  return [
    'v-input__field',
    {
      'v-input__field--has-prefix': props.prefixIcon,
      'v-input__field--has-suffix': props.suffixIcon || (props.clearable && props.modelValue)
    }
  ]
})

// 處理輸入事件
const handleInput = (event: Event) => {
  const target = event.target as HTMLInputElement
  let value: string | number = target.value
  
  // 數字類型處理
  if (props.type === 'number') {
    value = target.valueAsNumber || 0
  }
  
  emit('update:modelValue', value)
  emit('input', event)
}

// 處理變化事件
const handleChange = (event: Event) => {
  emit('change', event)
}

// 處理焦點事件
const handleFocus = (event: FocusEvent) => {
  isFocused.value = true
  emit('focus', event)
}

// 處理失焦事件
const handleBlur = (event: FocusEvent) => {
  isFocused.value = false
  emit('blur', event)
}

// 處理鍵盤事件
const handleKeydown = (event: KeyboardEvent) => {
  emit('keydown', event)
}

// 處理清除事件
const handleClear = () => {
  emit('update:modelValue', '')
  emit('clear')
  
  // 聚焦到輸入框
  nextTick(() => {
    inputRef.value?.focus()
  })
}

// 暴露方法
defineExpose({
  focus: () => inputRef.value?.focus(),
  blur: () => inputRef.value?.blur(),
  select: () => inputRef.value?.select()
})
</script>

<style scoped>
.v-input {
  display: flex;
  flex-direction: column;
  gap: var(--spacing-1);
}

.v-input__label {
  font-size: var(--font-size-sm);
  font-weight: 500;
  color: var(--color-text-primary);
  line-height: 1.5;
}

.v-input__required {
  color: var(--color-error);
  margin-left: var(--spacing-1);
}

.v-input__container {
  position: relative;
  display: flex;
  align-items: center;
}

.v-input__field {
  width: 100%;
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  background-color: var(--color-background);
  color: var(--color-text-primary);
  font-family: var(--font-family-sans);
  font-size: var(--font-size-base);
  line-height: 1.5;
  transition: all var(--duration-fast) var(--easing-ease-in-out);
  outline: none;
}

/* 尺寸變體 */
.v-input--sm .v-input__field {
  padding: var(--spacing-2) var(--spacing-3);
  font-size: var(--font-size-sm);
}

.v-input--md .v-input__field {
  padding: var(--spacing-3) var(--spacing-4);
  font-size: var(--font-size-base);
}

.v-input--lg .v-input__field {
  padding: var(--spacing-4) var(--spacing-5);
  font-size: var(--font-size-lg);
}

/* 有前綴圖標時的內邊距 */
.v-input__field--has-prefix {
  padding-left: var(--spacing-10);
}

.v-input--sm .v-input__field--has-prefix {
  padding-left: var(--spacing-8);
}

.v-input--lg .v-input__field--has-prefix {
  padding-left: var(--spacing-12);
}

/* 有後綴圖標時的內邊距 */
.v-input__field--has-suffix {
  padding-right: var(--spacing-10);
}

.v-input--sm .v-input__field--has-suffix {
  padding-right: var(--spacing-8);
}

.v-input--lg .v-input__field--has-suffix {
  padding-right: var(--spacing-12);
}

/* 圖標樣式 */
.v-input__icon {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--color-text-secondary);
  pointer-events: none;
  z-index: 1;
}

.v-input__icon--prefix {
  left: var(--spacing-3);
}

.v-input__icon--suffix {
  right: var(--spacing-3);
}

.v-input__icon svg {
  width: 1.25rem;
  height: 1.25rem;
}

.v-input--sm .v-input__icon svg {
  width: 1rem;
  height: 1rem;
}

.v-input--lg .v-input__icon svg {
  width: 1.5rem;
  height: 1.5rem;
}

/* 清除按鈕 */
.v-input__clear {
  background: none;
  border: none;
  padding: var(--spacing-1);
  color: var(--color-text-secondary);
  cursor: pointer;
  border-radius: var(--radius-sm);
  transition: all var(--duration-fast) var(--easing-ease-in-out);
  pointer-events: auto;
}

.v-input__clear:hover {
  color: var(--color-text-primary);
  background-color: var(--color-surface);
}

.v-input__clear svg {
  width: 1rem;
  height: 1rem;
}

/* 狀態樣式 */
.v-input__field:hover:not(:disabled):not(:focus) {
  border-color: var(--color-primary);
}

.v-input__field:focus,
.v-input--focused .v-input__field {
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--color-primary) 20%, transparent);
}

.v-input--error .v-input__field {
  border-color: var(--color-error);
}

.v-input--error .v-input__field:focus {
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--color-error) 20%, transparent);
}

.v-input--disabled .v-input__field {
  background-color: var(--color-surface);
  color: var(--color-text-disabled);
  cursor: not-allowed;
  opacity: 0.6;
}

.v-input--readonly .v-input__field {
  background-color: var(--color-surface);
  cursor: default;
}

/* 消息樣式 */
.v-input__message {
  font-size: var(--font-size-sm);
  line-height: 1.4;
}

.v-input__error {
  color: var(--color-error);
}

.v-input__help {
  color: var(--color-text-secondary);
}

/* 字符計數 */
.v-input__char-count {
  font-size: var(--font-size-xs);
  color: var(--color-text-secondary);
  text-align: right;
  margin-top: var(--spacing-1);
}

/* 佔位符樣式 */
.v-input__field::placeholder {
  color: var(--color-text-disabled);
  opacity: 1;
}

/* 自動填充樣式 */
.v-input__field:-webkit-autofill {
  -webkit-box-shadow: 0 0 0 1000px var(--color-background) inset;
  -webkit-text-fill-color: var(--color-text-primary);
}

/* 數字輸入框樣式 */
.v-input__field[type="number"] {
  -moz-appearance: textfield;
}

.v-input__field[type="number"]::-webkit-outer-spin-button,
.v-input__field[type="number"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* 搜索輸入框樣式 */
.v-input__field[type="search"] {
  -webkit-appearance: none;
}

.v-input__field[type="search"]::-webkit-search-decoration,
.v-input__field[type="search"]::-webkit-search-cancel-button,
.v-input__field[type="search"]::-webkit-search-results-button,
.v-input__field[type="search"]::-webkit-search-results-decoration {
  -webkit-appearance: none;
}

/* 密碼輸入框樣式 */
.v-input__field[type="password"] {
  font-family: var(--font-family-mono);
  letter-spacing: 0.1em;
}

/* 響應式調整 */
@media (max-width: 640px) {
  .v-input__field {
    min-height: 44px; /* 移動端觸摸友好 */
  }
}

/* 高對比度模式支持 */
@media (prefers-contrast: high) {
  .v-input__field {
    border-width: 2px;
  }
  
  .v-input__field:focus {
    outline: 2px solid var(--color-primary);
    outline-offset: 2px;
  }
}

/* 減少動畫偏好 */
@media (prefers-reduced-motion: reduce) {
  .v-input__field {
    transition: none;
  }
  
  .v-input__clear {
    transition: none;
  }
}

/* 深色主題適配 */
@media (prefers-color-scheme: dark) {
  .v-input__field:-webkit-autofill {
    -webkit-box-shadow: 0 0 0 1000px var(--color-surface) inset;
  }
}
</style>