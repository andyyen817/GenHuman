<template>
  <div :class="wrapperClasses">
    <!-- 表格工具欄 -->
    <div v-if="$slots.toolbar || searchable || showRefresh" class="v-table__toolbar">
      <div class="v-table__toolbar-left">
        <slot name="toolbar" />
      </div>
      <div class="v-table__toolbar-right">
        <div v-if="searchable" class="v-table__search">
          <VInput
            v-model="searchQuery"
            :placeholder="searchPlaceholder"
            size="sm"
            clearable
            prefix-icon="SearchIcon"
            @input="handleSearch"
          />
        </div>
        <VButton
          v-if="showRefresh"
          variant="ghost"
          size="sm"
          icon="RefreshIcon"
          @click="handleRefresh"
          :loading="refreshing"
        >
          刷新
        </VButton>
      </div>
    </div>

    <!-- 表格容器 -->
    <div class="v-table__container" :style="containerStyle">
      <table class="v-table__table">
        <!-- 表頭 -->
        <thead class="v-table__header">
          <tr class="v-table__header-row">
            <th
              v-if="selectable"
              class="v-table__header-cell v-table__header-cell--selection"
            >
              <input
                type="checkbox"
                class="v-table__checkbox"
                :checked="isAllSelected"
                :indeterminate="isIndeterminate"
                @change="handleSelectAll"
              />
            </th>
            <th
              v-for="column in visibleColumns"
              :key="column.key"
              :class="getHeaderCellClasses(column)"
              :style="getColumnStyle(column)"
              @click="handleSort(column)"
            >
              <div class="v-table__header-content">
                <span class="v-table__header-title">{{ column.title }}</span>
                <span v-if="column.sortable" class="v-table__sort-icon">
                  <svg
                    v-if="sortColumn === column.key && sortOrder === 'asc'"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                  >
                    <path d="M7 14l5-5 5 5z" />
                  </svg>
                  <svg
                    v-else-if="sortColumn === column.key && sortOrder === 'desc'"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                  >
                    <path d="M7 10l5 5 5-5z" />
                  </svg>
                  <svg v-else viewBox="0 0 24 24" fill="currentColor" opacity="0.3">
                    <path d="M7 14l5-5 5 5z" />
                  </svg>
                </span>
              </div>
            </th>
            <th v-if="hasActions" class="v-table__header-cell v-table__header-cell--actions">
              操作
            </th>
          </tr>
        </thead>

        <!-- 表體 -->
        <tbody class="v-table__body">
          <template v-if="loading">
            <tr class="v-table__loading-row">
              <td :colspan="totalColumns" class="v-table__loading-cell">
                <div class="v-table__loading">
                  <div class="v-table__spinner"></div>
                  <span>{{ loadingText }}</span>
                </div>
              </td>
            </tr>
          </template>
          
          <template v-else-if="paginatedData.length === 0">
            <tr class="v-table__empty-row">
              <td :colspan="totalColumns" class="v-table__empty-cell">
                <div class="v-table__empty">
                  <slot name="empty">
                    <div class="v-table__empty-content">
                      <svg class="v-table__empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="m9 9 6 6"></path>
                        <path d="m15 9-6 6"></path>
                      </svg>
                      <p>{{ emptyText }}</p>
                    </div>
                  </slot>
                </div>
              </td>
            </tr>
          </template>
          
          <template v-else>
            <tr
              v-for="(row, index) in paginatedData"
              :key="getRowKey(row, index)"
              :class="getRowClasses(row, index)"
              @click="handleRowClick(row, index)"
            >
              <td v-if="selectable" class="v-table__cell v-table__cell--selection">
                <input
                  type="checkbox"
                  class="v-table__checkbox"
                  :checked="isRowSelected(row)"
                  @change="handleRowSelect(row, $event)"
                  @click.stop
                />
              </td>
              <td
                v-for="column in visibleColumns"
                :key="column.key"
                :class="getCellClasses(column, row)"
                :style="getColumnStyle(column)"
              >
                <div class="v-table__cell-content">
                  <slot
                    :name="`cell-${column.key}`"
                    :row="row"
                    :column="column"
                    :value="getCellValue(row, column.key)"
                    :index="index"
                  >
                    {{ formatCellValue(getCellValue(row, column.key), column) }}
                  </slot>
                </div>
              </td>
              <td v-if="hasActions" class="v-table__cell v-table__cell--actions">
                <div class="v-table__actions">
                  <slot name="actions" :row="row" :index="index" />
                </div>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <!-- 分頁 -->
    <div v-if="pagination && !loading" class="v-table__pagination">
      <div class="v-table__pagination-info">
        顯示 {{ paginationInfo.start }} - {{ paginationInfo.end }} 項，共 {{ paginationInfo.total }} 項
      </div>
      <div class="v-table__pagination-controls">
        <VButton
          variant="ghost"
          size="sm"
          :disabled="currentPage === 1"
          @click="handlePageChange(currentPage - 1)"
        >
          上一頁
        </VButton>
        
        <div class="v-table__page-numbers">
          <VButton
            v-for="page in visiblePages"
            :key="page"
            :variant="page === currentPage ? 'primary' : 'ghost'"
            size="sm"
            @click="handlePageChange(page)"
          >
            {{ page }}
          </VButton>
        </div>
        
        <VButton
          variant="ghost"
          size="sm"
          :disabled="currentPage === totalPages"
          @click="handlePageChange(currentPage + 1)"
        >
          下一頁
        </VButton>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, watch, defineEmits, defineProps } from 'vue'
import VInput from './VInput.vue'
import VButton from './VButton.vue'

/**
 * 表格列配置接口
 */
export interface TableColumn {
  /** 列鍵值 */
  key: string
  /** 列標題 */
  title: string
  /** 列寬度 */
  width?: string | number
  /** 最小寬度 */
  minWidth?: string | number
  /** 是否可排序 */
  sortable?: boolean
  /** 是否可篩選 */
  filterable?: boolean
  /** 對齊方式 */
  align?: 'left' | 'center' | 'right'
  /** 是否固定 */
  fixed?: 'left' | 'right'
  /** 是否隱藏 */
  hidden?: boolean
  /** 格式化函數 */
  formatter?: (value: any, row: any) => string
  /** 自定義渲染 */
  render?: (value: any, row: any) => any
}

/**
 * 表格組件屬性接口
 */
export interface VTableProps {
  /** 表格數據 */
  data?: any[]
  /** 列配置 */
  columns?: TableColumn[]
  /** 是否加載中 */
  loading?: boolean
  /** 加載文本 */
  loadingText?: string
  /** 空數據文本 */
  emptyText?: string
  /** 表格尺寸 */
  size?: 'sm' | 'md' | 'lg'
  /** 是否顯示邊框 */
  bordered?: boolean
  /** 是否顯示斑馬紋 */
  striped?: boolean
  /** 是否可選擇 */
  selectable?: boolean
  /** 選中的行 */
  selectedRows?: any[]
  /** 行鍵值字段 */
  rowKey?: string
  /** 是否可搜索 */
  searchable?: boolean
  /** 搜索佔位符 */
  searchPlaceholder?: string
  /** 是否顯示刷新按鈕 */
  showRefresh?: boolean
  /** 是否刷新中 */
  refreshing?: boolean
  /** 是否分頁 */
  pagination?: boolean
  /** 每頁條數 */
  pageSize?: number
  /** 當前頁 */
  currentPage?: number
  /** 總條數 */
  total?: number
  /** 最大高度 */
  maxHeight?: string | number
  /** 是否可點擊行 */
  clickableRows?: boolean
}

/**
 * 表格組件事件接口
 */
export interface VTableEmits {
  'update:selectedRows': [rows: any[]]
  'update:currentPage': [page: number]
  'row-click': [row: any, index: number]
  'row-select': [row: any, selected: boolean]
  'select-all': [selected: boolean]
  'sort-change': [column: string, order: 'asc' | 'desc' | null]
  'search': [query: string]
  'refresh': []
  'page-change': [page: number]
}

// 定義屬性
const props = withDefaults(defineProps<VTableProps>(), {
  data: () => [],
  columns: () => [],
  loading: false,
  loadingText: '加載中...',
  emptyText: '暫無數據',
  size: 'md',
  bordered: false,
  striped: true,
  selectable: false,
  selectedRows: () => [],
  rowKey: 'id',
  searchable: false,
  searchPlaceholder: '搜索...',
  showRefresh: false,
  refreshing: false,
  pagination: false,
  pageSize: 10,
  currentPage: 1,
  total: 0,
  clickableRows: false
})

// 定義事件
const emit = defineEmits<VTableEmits>()

// 響應式數據
const searchQuery = ref('')
const sortColumn = ref<string | null>(null)
const sortOrder = ref<'asc' | 'desc' | null>(null)

// 計算可見列
const visibleColumns = computed(() => {
  return props.columns.filter(column => !column.hidden)
})

// 計算是否有操作列
const hasActions = computed(() => {
  return !!$slots.actions
})

// 計算總列數
const totalColumns = computed(() => {
  let count = visibleColumns.value.length
  if (props.selectable) count++
  if (hasActions.value) count++
  return count
})

// 計算包裝器樣式類
const wrapperClasses = computed(() => {
  return [
    'v-table',
    `v-table--${props.size}`,
    {
      'v-table--bordered': props.bordered,
      'v-table--striped': props.striped,
      'v-table--loading': props.loading,
      'v-table--clickable': props.clickableRows
    }
  ]
})

// 計算容器樣式
const containerStyle = computed(() => {
  const style: any = {}
  if (props.maxHeight) {
    style.maxHeight = typeof props.maxHeight === 'number' 
      ? `${props.maxHeight}px` 
      : props.maxHeight
    style.overflowY = 'auto'
  }
  return style
})

// 篩選和排序後的數據
const filteredData = computed(() => {
  let data = [...props.data]
  
  // 搜索篩選
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    data = data.filter(row => {
      return visibleColumns.value.some(column => {
        const value = getCellValue(row, column.key)
        return String(value).toLowerCase().includes(query)
      })
    })
  }
  
  // 排序
  if (sortColumn.value && sortOrder.value) {
    data.sort((a, b) => {
      const aValue = getCellValue(a, sortColumn.value!)
      const bValue = getCellValue(b, sortColumn.value!)
      
      let result = 0
      if (aValue < bValue) result = -1
      else if (aValue > bValue) result = 1
      
      return sortOrder.value === 'desc' ? -result : result
    })
  }
  
  return data
})

// 分頁數據
const paginatedData = computed(() => {
  if (!props.pagination) {
    return filteredData.value
  }
  
  const start = (props.currentPage - 1) * props.pageSize
  const end = start + props.pageSize
  return filteredData.value.slice(start, end)
})

// 總頁數
const totalPages = computed(() => {
  if (!props.pagination) return 1
  return Math.ceil(filteredData.value.length / props.pageSize)
})

// 可見頁碼
const visiblePages = computed(() => {
  const pages = []
  const total = totalPages.value
  const current = props.currentPage
  
  if (total <= 7) {
    for (let i = 1; i <= total; i++) {
      pages.push(i)
    }
  } else {
    if (current <= 4) {
      for (let i = 1; i <= 5; i++) {
        pages.push(i)
      }
      pages.push('...', total)
    } else if (current >= total - 3) {
      pages.push(1, '...')
      for (let i = total - 4; i <= total; i++) {
        pages.push(i)
      }
    } else {
      pages.push(1, '...')
      for (let i = current - 1; i <= current + 1; i++) {
        pages.push(i)
      }
      pages.push('...', total)
    }
  }
  
  return pages.filter(page => page !== '...' || pages.indexOf(page) === pages.lastIndexOf(page))
})

// 分頁信息
const paginationInfo = computed(() => {
  const total = filteredData.value.length
  const start = total === 0 ? 0 : (props.currentPage - 1) * props.pageSize + 1
  const end = Math.min(props.currentPage * props.pageSize, total)
  
  return { start, end, total }
})

// 是否全選
const isAllSelected = computed(() => {
  return paginatedData.value.length > 0 && 
    paginatedData.value.every(row => isRowSelected(row))
})

// 是否部分選中
const isIndeterminate = computed(() => {
  const selectedCount = paginatedData.value.filter(row => isRowSelected(row)).length
  return selectedCount > 0 && selectedCount < paginatedData.value.length
})

// 獲取行鍵值
const getRowKey = (row: any, index: number): string => {
  return row[props.rowKey] || index.toString()
}

// 獲取單元格值
const getCellValue = (row: any, key: string): any => {
  return key.split('.').reduce((obj, k) => obj?.[k], row)
}

// 格式化單元格值
const formatCellValue = (value: any, column: TableColumn): string => {
  if (column.formatter) {
    return column.formatter(value, {})
  }
  return value == null ? '' : String(value)
}

// 判斷行是否選中
const isRowSelected = (row: any): boolean => {
  const rowKey = getRowKey(row, 0)
  return props.selectedRows.some(selectedRow => 
    getRowKey(selectedRow, 0) === rowKey
  )
}

// 獲取表頭單元格樣式類
const getHeaderCellClasses = (column: TableColumn) => {
  return [
    'v-table__header-cell',
    {
      'v-table__header-cell--sortable': column.sortable,
      'v-table__header-cell--sorted': sortColumn.value === column.key,
      [`v-table__header-cell--${column.align}`]: column.align
    }
  ]
}

// 獲取行樣式類
const getRowClasses = (row: any, index: number) => {
  return [
    'v-table__row',
    {
      'v-table__row--selected': isRowSelected(row),
      'v-table__row--clickable': props.clickableRows
    }
  ]
}

// 獲取單元格樣式類
const getCellClasses = (column: TableColumn, row: any) => {
  return [
    'v-table__cell',
    {
      [`v-table__cell--${column.align}`]: column.align
    }
  ]
}

// 獲取列樣式
const getColumnStyle = (column: TableColumn) => {
  const style: any = {}
  
  if (column.width) {
    style.width = typeof column.width === 'number' 
      ? `${column.width}px` 
      : column.width
  }
  
  if (column.minWidth) {
    style.minWidth = typeof column.minWidth === 'number' 
      ? `${column.minWidth}px` 
      : column.minWidth
  }
  
  return style
}

// 處理排序
const handleSort = (column: TableColumn) => {
  if (!column.sortable) return
  
  if (sortColumn.value === column.key) {
    if (sortOrder.value === 'asc') {
      sortOrder.value = 'desc'
    } else if (sortOrder.value === 'desc') {
      sortColumn.value = null
      sortOrder.value = null
    }
  } else {
    sortColumn.value = column.key
    sortOrder.value = 'asc'
  }
  
  emit('sort-change', sortColumn.value, sortOrder.value)
}

// 處理搜索
const handleSearch = () => {
  emit('search', searchQuery.value)
}

// 處理刷新
const handleRefresh = () => {
  emit('refresh')
}

// 處理行點擊
const handleRowClick = (row: any, index: number) => {
  if (props.clickableRows) {
    emit('row-click', row, index)
  }
}

// 處理行選擇
const handleRowSelect = (row: any, event: Event) => {
  const target = event.target as HTMLInputElement
  const selected = target.checked
  
  let newSelectedRows = [...props.selectedRows]
  const rowKey = getRowKey(row, 0)
  
  if (selected) {
    if (!isRowSelected(row)) {
      newSelectedRows.push(row)
    }
  } else {
    newSelectedRows = newSelectedRows.filter(selectedRow => 
      getRowKey(selectedRow, 0) !== rowKey
    )
  }
  
  emit('update:selectedRows', newSelectedRows)
  emit('row-select', row, selected)
}

// 處理全選
const handleSelectAll = (event: Event) => {
  const target = event.target as HTMLInputElement
  const selected = target.checked
  
  let newSelectedRows = [...props.selectedRows]
  
  if (selected) {
    // 添加當前頁所有未選中的行
    paginatedData.value.forEach(row => {
      if (!isRowSelected(row)) {
        newSelectedRows.push(row)
      }
    })
  } else {
    // 移除當前頁所有選中的行
    const currentPageKeys = paginatedData.value.map(row => getRowKey(row, 0))
    newSelectedRows = newSelectedRows.filter(selectedRow => 
      !currentPageKeys.includes(getRowKey(selectedRow, 0))
    )
  }
  
  emit('update:selectedRows', newSelectedRows)
  emit('select-all', selected)
}

// 處理頁面變化
const handlePageChange = (page: number) => {
  if (page >= 1 && page <= totalPages.value) {
    emit('update:currentPage', page)
    emit('page-change', page)
  }
}

// 監聽搜索查詢變化，重置到第一頁
watch(searchQuery, () => {
  if (props.pagination && props.currentPage !== 1) {
    handlePageChange(1)
  }
})
</script>

<style scoped>
.v-table {
  display: flex;
  flex-direction: column;
  gap: var(--spacing-4);
  background-color: var(--color-background);
  border-radius: var(--radius-lg);
}

/* 工具欄 */
.v-table__toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: var(--spacing-4);
  padding: var(--spacing-4);
  border-bottom: 1px solid var(--color-border);
}

.v-table__toolbar-left,
.v-table__toolbar-right {
  display: flex;
  align-items: center;
  gap: var(--spacing-3);
}

.v-table__search {
  min-width: 200px;
}

/* 表格容器 */
.v-table__container {
  overflow-x: auto;
  border-radius: var(--radius-md);
}

.v-table__table {
  width: 100%;
  border-collapse: collapse;
  font-size: var(--font-size-sm);
}

/* 表頭 */
.v-table__header {
  background-color: var(--color-surface);
}

.v-table__header-row {
  border-bottom: 1px solid var(--color-border);
}

.v-table__header-cell {
  padding: var(--spacing-3) var(--spacing-4);
  text-align: left;
  font-weight: 600;
  color: var(--color-text-primary);
  white-space: nowrap;
  user-select: none;
}

.v-table__header-cell--sortable {
  cursor: pointer;
  transition: background-color var(--duration-fast) var(--easing-ease-in-out);
}

.v-table__header-cell--sortable:hover {
  background-color: color-mix(in srgb, var(--color-surface) 80%, var(--color-primary));
}

.v-table__header-cell--center {
  text-align: center;
}

.v-table__header-cell--right {
  text-align: right;
}

.v-table__header-cell--selection,
.v-table__header-cell--actions {
  width: 1%;
  text-align: center;
}

.v-table__header-content {
  display: flex;
  align-items: center;
  gap: var(--spacing-2);
}

.v-table__sort-icon {
  display: flex;
  align-items: center;
  width: 1rem;
  height: 1rem;
}

.v-table__sort-icon svg {
  width: 100%;
  height: 100%;
}

/* 表體 */
.v-table__body {
  background-color: var(--color-background);
}

.v-table__row {
  border-bottom: 1px solid var(--color-border);
  transition: background-color var(--duration-fast) var(--easing-ease-in-out);
}

.v-table__row:last-child {
  border-bottom: none;
}

.v-table__row--clickable {
  cursor: pointer;
}

.v-table__row--clickable:hover {
  background-color: var(--color-surface);
}

.v-table__row--selected {
  background-color: color-mix(in srgb, var(--color-primary) 10%, var(--color-background));
}

.v-table__cell {
  padding: var(--spacing-3) var(--spacing-4);
  color: var(--color-text-primary);
  vertical-align: middle;
}

.v-table__cell--center {
  text-align: center;
}

.v-table__cell--right {
  text-align: right;
}

.v-table__cell--selection,
.v-table__cell--actions {
  text-align: center;
  width: 1%;
}

.v-table__cell-content {
  word-break: break-word;
}

/* 尺寸變體 */
.v-table--sm .v-table__header-cell,
.v-table--sm .v-table__cell {
  padding: var(--spacing-2) var(--spacing-3);
  font-size: var(--font-size-xs);
}

.v-table--lg .v-table__header-cell,
.v-table--lg .v-table__cell {
  padding: var(--spacing-4) var(--spacing-6);
  font-size: var(--font-size-base);
}

/* 邊框樣式 */
.v-table--bordered .v-table__table {
  border: 1px solid var(--color-border);
}

.v-table--bordered .v-table__header-cell,
.v-table--bordered .v-table__cell {
  border-right: 1px solid var(--color-border);
}

.v-table--bordered .v-table__header-cell:last-child,
.v-table--bordered .v-table__cell:last-child {
  border-right: none;
}

/* 斑馬紋 */
.v-table--striped .v-table__row:nth-child(even) {
  background-color: color-mix(in srgb, var(--color-surface) 50%, var(--color-background));
}

/* 複選框 */
.v-table__checkbox {
  width: 1rem;
  height: 1rem;
  cursor: pointer;
}

/* 操作按鈕 */
.v-table__actions {
  display: flex;
  align-items: center;
  gap: var(--spacing-2);
  justify-content: center;
}

/* 加載狀態 */
.v-table__loading-cell {
  padding: var(--spacing-8) var(--spacing-4);
  text-align: center;
}

.v-table__loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--spacing-3);
  color: var(--color-text-secondary);
}

.v-table__spinner {
  width: 2rem;
  height: 2rem;
  border: 2px solid var(--color-border);
  border-top: 2px solid var(--color-primary);
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* 空狀態 */
.v-table__empty-cell {
  padding: var(--spacing-8) var(--spacing-4);
  text-align: center;
}

.v-table__empty-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--spacing-3);
  color: var(--color-text-secondary);
}

.v-table__empty-icon {
  width: 3rem;
  height: 3rem;
  opacity: 0.5;
}

/* 分頁 */
.v-table__pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: var(--spacing-4);
  border-top: 1px solid var(--color-border);
}

.v-table__pagination-info {
  font-size: var(--font-size-sm);
  color: var(--color-text-secondary);
}

.v-table__pagination-controls {
  display: flex;
  align-items: center;
  gap: var(--spacing-2);
}

.v-table__page-numbers {
  display: flex;
  gap: var(--spacing-1);
}

/* 響應式調整 */
@media (max-width: 768px) {
  .v-table__toolbar {
    flex-direction: column;
    align-items: stretch;
    gap: var(--spacing-3);
  }
  
  .v-table__toolbar-left,
  .v-table__toolbar-right {
    justify-content: space-between;
  }
  
  .v-table__search {
    min-width: auto;
    flex: 1;
  }
  
  .v-table__pagination {
    flex-direction: column;
    gap: var(--spacing-3);
  }
  
  .v-table__pagination-controls {
    flex-wrap: wrap;
    justify-content: center;
  }
}

/* 減少動畫偏好 */
@media (prefers-reduced-motion: reduce) {
  .v-table__row,
  .v-table__header-cell--sortable {
    transition: none;
  }
  
  .v-table__spinner {
    animation: none;
  }
}
</style>