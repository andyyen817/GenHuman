<template>
  <div class="v-input-wrapper">
    <label v-if="label" :for="inputId" class="v-input-label">
      {{ label }}
      <span v-if="required" class="v-input-required">*</span>
    </label>
    
    <div class="v-input-container" :class="containerClasses">
      <div v-if="$slots['prefix-icon']" class="v-input-prefix">
        <slot name="prefix-icon" />
      </div>
      
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
        @input="handleInput"
        @change="handleChange"
        @focus="handleFocus"
        @blur="handleBlur"
        @keydown="handleKeydown"
      />
      
      <div v-if="$slots['suffix-icon'] || clearable" class="v-input-suffix">
        <button
          v-if="clearable && modelValue && !disabled && !readonly"
          type="button"
          class="v-input-clear"
          @click="handleClear"
        >
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
        <slot name="suffix-icon" />
      </div>
    </div>
    
    <div v-if="errorMessage || helperText" class="v-input-message">
      <span v-if="errorMessage" class="v-input-error">{{ errorMessage }}</span>
      <span v-else-if="helperText" class="v-input-helper">{{ helperText }}</span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, nextTick } from 'vue'
import { generateId } from '@/utils/helpers'

interface Props {
  modelValue?: string | number
  type?: 'text' | 'email' | 'password' | 'number' | 'tel' | 'url' | 'search'
  label?: string
  placeholder?: string
  helperText?: string
  errorMessage?: string
  size?: 'sm' | 'md' | 'lg'
  disabled?: boolean
  readonly?: boolean
  required?: boolean
  clearable?: boolean
  autocomplete?: string
  maxlength?: number
  minlength?: number
  min?: number | string
  max?: number | string
  step?: number | string
}

const props = withDefaults(defineProps<Props>(), {
  type: 'text',
  size: 'md',
  disabled: false,
  readonly: false,
  required: false,
  clearable: false
})

const emit = defineEmits<{
  'update:modelValue': [value: string | number]
  change: [value: string | number]
  focus: [event: FocusEvent]
  blur: [event: FocusEvent]
  keydown: [event: KeyboardEvent]
  clear: []
}>()

const inputRef = ref<HTMLInputElement>()
const isFocused = ref(false)
const inputId = generateId('v-input')

const containerClasses = computed(() => {
  const classes = ['v-input-container--base']
  
  classes.push(`v-input-container--${props.size}`)
  
  if (props.disabled) {
    classes.push('v-input-container--disabled')
  }
  
  if (props.readonly) {
    classes.push('v-input-container--readonly')
  }
  
  if (props.errorMessage) {
    classes.push('v-input-container--error')
  }
  
  if (isFocused.value) {
    classes.push('v-input-container--focused')
  }
  
  return classes
})

const inputClasses = computed(() => {
  const classes = ['v-input']
  
  classes.push(`v-input--${props.size}`)
  
  return classes
})

const handleInput = (event: Event) => {
  const target = event.target as HTMLInputElement
  const value = props.type === 'number' ? Number(target.value) : target.value
  emit('update:modelValue', value)
}

const handleChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  const value = props.type === 'number' ? Number(target.value) : target.value
  emit('change', value)
}

const handleFocus = (event: FocusEvent) => {
  isFocused.value = true
  emit('focus', event)
}

const handleBlur = (event: FocusEvent) => {
  isFocused.value = false
  emit('blur', event)
}

const handleKeydown = (event: KeyboardEvent) => {
  emit('keydown', event)
}

const handleClear = () => {
  emit('update:modelValue', '')
  emit('clear')
  nextTick(() => {
    inputRef.value?.focus()
  })
}

// 暴露方法給父組件
defineExpose({
  focus: () => inputRef.value?.focus(),
  blur: () => inputRef.value?.blur(),
  select: () => inputRef.value?.select()
})
</script>

<style scoped>
.v-input-wrapper {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.v-input-label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.v-input-required {
  color: #ef4444;
}

.v-input-container {
  position: relative;
  display: flex;
  align-items: center;
}

.v-input-container--base {
  background-color: white;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  transition: border-color 150ms ease-in-out, box-shadow 150ms ease-in-out;
}

.v-input-container--sm {
  min-height: 2rem;
}

.v-input-container--md {
  min-height: 2.5rem;
}

.v-input-container--lg {
  min-height: 3rem;
}

.v-input-container--focused {
  border-color: #0ea5e9;
  box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
}

.v-input-container--error {
  border-color: #ef4444;
}

.v-input-container--error.v-input-container--focused {
  border-color: #ef4444;
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.v-input-container--disabled {
  background-color: #f9fafb;
  border-color: #e5e7eb;
  cursor: not-allowed;
}

.v-input-container--readonly {
  background-color: #f9fafb;
}

.v-input {
  flex: 1;
  border: none;
  outline: none;
  background: transparent;
  color: #111827;
  font-size: 1rem;
  line-height: 1.5;
}

.v-input--sm {
  padding: 0.5rem 0.75rem;
  font-size: 0.875rem;
}

.v-input--md {
  padding: 0.75rem 1rem;
  font-size: 1rem;
}

.v-input--lg {
  padding: 1rem 1.25rem;
  font-size: 1.125rem;
}

.v-input::placeholder {
  color: #9ca3af;
}

.v-input:disabled {
  color: #6b7280;
  cursor: not-allowed;
}

.v-input-prefix,
.v-input-suffix {
  display: flex;
  align-items: center;
  color: #6b7280;
}

.v-input-prefix {
  padding-left: 0.75rem;
}

.v-input-suffix {
  padding-right: 0.75rem;
  gap: 0.5rem;
}

.v-input-clear {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 1.5rem;
  height: 1.5rem;
  border: none;
  background: none;
  color: #9ca3af;
  cursor: pointer;
  border-radius: 0.25rem;
  transition: color 150ms ease-in-out;
}

.v-input-clear:hover {
  color: #6b7280;
}

.v-input-message {
  font-size: 0.875rem;
  line-height: 1.25;
}

.v-input-error {
  color: #ef4444;
}

.v-input-helper {
  color: #6b7280;
}

/* 響應式設計 */
@media (max-width: 640px) {
  .v-input-container--sm {
    min-height: 2.5rem;
  }
  
  .v-input-container--md {
    min-height: 2.75rem;
  }
  
  .v-input-container--lg {
    min-height: 3.25rem;
  }
  
  .v-input--sm {
    font-size: 1rem; /* 防止iOS縮放 */
  }
}

/* 深色模式支持 */
@media (prefers-color-scheme: dark) {
  .v-input-label {
    color: #f3f4f6;
  }
  
  .v-input-container--base {
    background-color: #374151;
    border-color: #4b5563;
  }
  
  .v-input {
    color: #f3f4f6;
  }
  
  .v-input::placeholder {
    color: #9ca3af;
  }
  
  .v-input-container--disabled {
    background-color: #1f2937;
    border-color: #374151;
  }
  
  .v-input-helper {
    color: #9ca3af;
  }
}
</style>