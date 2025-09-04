<template>
  <div class="bg-gray-50 min-h-screen flex">
    <!-- 左側導航欄 -->
    <div class="w-64 sidebar-dark flex flex-col">
      <!-- Logo區域 -->
      <div class="p-6 border-b border-gray-700">
        <div class="flex items-center">
          <div class="w-8 h-8 bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg flex items-center justify-center mr-3">
            <i class="fas fa-video text-white text-sm"></i>
          </div>
          <h1 class="text-xl font-bold text-white">Vidspark</h1>
        </div>
      </div>

      <!-- 創建按鈕 -->
      <div class="p-4">
        <button 
          @click="goToCreateVideo"
          class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 px-4 rounded-lg font-medium transition-colors flex items-center justify-center"
        >
          <i class="fas fa-plus mr-2"></i>
          {{ $t('navigation.create_video') }}
        </button>
      </div>

      <!-- 主導航 -->
      <nav class="flex-1 px-4">
        <ul class="space-y-2">
          <li>
            <router-link 
              to="/dashboard" 
              class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
            >
              <i class="fas fa-home mr-3"></i>
              {{ $t('navigation.dashboard') }}
            </router-link>
          </li>
          <li>
            <router-link 
              to="/my-videos" 
              class="flex items-center px-4 py-3 text-white bg-purple-600 rounded-lg"
            >
              <i class="fas fa-folder mr-3"></i>
              {{ $t('navigation.my_videos') }}
            </router-link>
          </li>
          <li>
            <router-link 
              to="/digital-humans" 
              class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
            >
              <i class="fas fa-user-tie mr-3"></i>
              {{ $t('navigation.digital_humans') }}
            </router-link>
          </li>
          <li>
            <router-link 
              to="/voice-clone" 
              class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
            >
              <i class="fas fa-microphone mr-3"></i>
              {{ $t('navigation.voice_clone') }}
            </router-link>
          </li>
          <li>
            <router-link 
              to="/templates" 
              class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
            >
              <i class="fas fa-layer-group mr-3"></i>
              {{ $t('navigation.templates') }}
            </router-link>
          </li>
          <li>
            <a href="#" class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
              <i class="fas fa-images mr-3"></i>
              媒體庫
            </a>
          </li>
        </ul>
      </nav>

      <!-- 底部用戶信息 -->
      <div class="p-4 border-t border-gray-700">
        <div class="flex items-center">
          <img 
            src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face" 
            alt="用戶頭像" 
            class="w-10 h-10 rounded-full"
          >
          <div class="ml-3 flex-1">
            <p class="text-white text-sm font-medium">{{ userStore.user?.username || '用戶' }}</p>
            <p class="text-gray-400 text-xs">免費會員</p>
          </div>
        </div>
      </div>
    </div>

    <!-- 右側主內容區 -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- 頂部欄 -->
      <header class="bg-white border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $t('navigation.my_videos') }}</h1>
            <p class="text-gray-600">管理您的所有影片創作項目</p>
          </div>
          
          <div class="flex items-center space-x-4">
            <!-- 搜索框 -->
            <div class="relative">
              <input 
                v-model="searchQuery"
                type="text" 
                placeholder="搜索項目..." 
                class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 w-64"
              >
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                <i class="fas fa-search text-gray-400"></i>
              </div>
            </div>
            
            <!-- 篩選按鈕 -->
            <button 
              @click="showFilterMenu = !showFilterMenu"
              class="flex items-center px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
            >
              <i class="fas fa-filter mr-2"></i>
              篩選
            </button>
            
            <!-- 視圖切換 -->
            <div class="flex bg-gray-100 rounded-lg p-1">
              <button 
                @click="viewMode = 'grid'"
                :class="[
                  'px-3 py-1 text-sm font-medium rounded transition-colors',
                  viewMode === 'grid' 
                    ? 'bg-white text-gray-900 shadow-sm' 
                    : 'text-gray-500 hover:text-gray-900'
                ]"
              >
                <i class="fas fa-th-large"></i>
              </button>
              <button 
                @click="viewMode = 'list'"
                :class="[
                  'px-3 py-1 text-sm font-medium rounded transition-colors',
                  viewMode === 'list' 
                    ? 'bg-white text-gray-900 shadow-sm' 
                    : 'text-gray-500 hover:text-gray-900'
                ]"
              >
                <i class="fas fa-list"></i>
              </button>
            </div>
          </div>
        </div>
      </header>

      <!-- 篩選標籤欄 -->
      <div class="bg-white border-b border-gray-200 px-6 py-3">
        <div class="flex items-center space-x-4">
          <span class="text-sm font-medium text-gray-700">快速篩選：</span>
          <button 
            @click="activeFilter = 'all'"
            :class="[
              'px-3 py-1 rounded-full text-sm font-medium transition-colors',
              activeFilter === 'all' 
                ? 'bg-purple-100 text-purple-800' 
                : 'text-gray-600 hover:bg-gray-100'
            ]"
          >
            全部 ({{ totalProjects }})
          </button>
          <button 
            @click="activeFilter = 'completed'"
            :class="[
              'px-3 py-1 rounded-full text-sm font-medium transition-colors',
              activeFilter === 'completed' 
                ? 'bg-purple-100 text-purple-800' 
                : 'text-gray-600 hover:bg-gray-100'
            ]"
          >
            已完成 ({{ completedProjects }})
          </button>
          <button 
            @click="activeFilter = 'processing'"
            :class="[
              'px-3 py-1 rounded-full text-sm font-medium transition-colors',
              activeFilter === 'processing' 
                ? 'bg-purple-100 text-purple-800' 
                : 'text-gray-600 hover:bg-gray-100'
            ]"
          >
            處理中 ({{ processingProjects }})
          </button>
          <button 
            @click="activeFilter = 'draft'"
            :class="[
              'px-3 py-1 rounded-full text-sm font-medium transition-colors',
              activeFilter === 'draft' 
                ? 'bg-purple-100 text-purple-800' 
                : 'text-gray-600 hover:bg-gray-100'
            ]"
          >
            草稿 ({{ draftProjects }})
          </button>
        </div>
      </div>

      <!-- 主內容滾動區域 -->
      <main class="flex-1 overflow-y-auto p-6">
        <!-- 項目網格 -->
        <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <!-- 項目卡片 -->
          <div 
            v-for="project in filteredProjects" 
            :key="project.id"
            class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden card-hover group cursor-pointer"
            @click="openProject(project)"
          >
            <div class="relative">
              <!-- 已完成項目的封面圖片 -->
              <img 
                v-if="project.status === 'completed' && project.thumbnail" 
                :src="project.thumbnail" 
                :alt="project.title" 
                class="w-full h-40 object-cover"
              >
              <!-- 處理中項目的封面圖片 + 遮罩 -->
              <div v-else-if="project.status === 'processing'" class="relative">
                <img 
                  v-if="project.thumbnail"
                  :src="project.thumbnail" 
                  :alt="project.title" 
                  class="w-full h-40 object-cover"
                >
                <div v-else class="w-full h-40 bg-gray-200"></div>
                <div class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center">
                  <div class="text-white text-center">
                    <div class="animate-spin w-8 h-8 border-2 border-white border-t-transparent rounded-full mx-auto mb-2"></div>
                    <p class="text-sm">生成中...</p>
                  </div>
                </div>
              </div>
              <!-- 草稿項目的佔位符 -->
              <div v-else class="w-full h-40 bg-gray-100 flex items-center justify-center">
                <div class="text-center">
                  <i class="fas fa-file-alt text-gray-400 text-3xl mb-2"></i>
                  <p class="text-gray-500 text-sm">草稿</p>
                </div>
              </div>
              
              <!-- 懸停播放按鈕（僅已完成項目） -->
              <div 
                v-if="project.status === 'completed'"
                class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center"
              >
                <button class="opacity-0 group-hover:opacity-100 w-12 h-12 bg-white bg-opacity-90 rounded-full flex items-center justify-center transition-all duration-300">
                  <i class="fas fa-play text-purple-600 ml-1"></i>
                </button>
              </div>
              
              <!-- 狀態標籤 -->
              <div class="absolute top-3 left-3">
                <span 
                  :class="[
                    'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
                    getStatusClass(project.status)
                  ]"
                >
                  {{ getStatusText(project.status) }}
                </span>
              </div>
              
              <!-- 時長標籤（已完成項目） -->
              <div v-if="project.status === 'completed' && project.duration" class="absolute bottom-3 right-3 bg-black bg-opacity-70 text-white text-xs px-2 py-1 rounded">
                {{ project.duration }}
              </div>
            </div>
            
            <div class="p-4">
              <h3 class="font-semibold text-gray-900 mb-1 truncate">{{ project.title }}</h3>
              <p class="text-sm text-gray-500 mb-3">{{ formatTime(project.updatedAt) }} • {{ project.digitalHuman || '尚未選擇數字人' }}</p>
              
              <!-- 已完成項目的操作按鈕 -->
              <div v-if="project.status === 'completed'" class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                  <button 
                    @click.stop="downloadProject(project)"
                    class="text-gray-400 hover:text-purple-600 transition-colors"
                    title="下載"
                  >
                    <i class="fas fa-download"></i>
                  </button>
                  <button 
                    @click.stop="shareProject(project)"
                    class="text-gray-400 hover:text-purple-600 transition-colors"
                    title="分享"
                  >
                    <i class="fas fa-share-alt"></i>
                  </button>
                  <button 
                    @click.stop="editProject(project)"
                    class="text-gray-400 hover:text-purple-600 transition-colors"
                    title="編輯"
                  >
                    <i class="fas fa-edit"></i>
                  </button>
                </div>
                <button 
                  @click.stop="showProjectMenu(project)"
                  class="text-gray-400 hover:text-gray-600 transition-colors"
                >
                  <i class="fas fa-ellipsis-h"></i>
                </button>
              </div>
              
              <!-- 處理中項目的進度條 -->
              <div v-else-if="project.status === 'processing'" class="flex items-center justify-between">
                <div class="w-full bg-yellow-200 rounded-full h-2">
                  <div 
                    class="bg-yellow-600 h-2 rounded-full transition-all duration-300" 
                    :style="{ width: project.progress + '%' }"
                  ></div>
                </div>
                <span class="text-xs text-yellow-600 ml-2 font-medium">{{ project.progress }}%</span>
              </div>
              
              <!-- 草稿項目的繼續編輯按鈕 -->
              <div v-else class="flex items-center justify-between">
                <button 
                  @click.stop="continueEditing(project)"
                  class="text-purple-600 hover:text-purple-700 font-medium text-sm transition-colors"
                >
                  繼續編輯
                </button>
                <button 
                  @click.stop="showProjectMenu(project)"
                  class="text-gray-400 hover:text-gray-600 transition-colors"
                >
                  <i class="fas fa-ellipsis-h"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- 空狀態 -->
        <div v-if="filteredProjects.length === 0" class="text-center py-12">
          <i class="fas fa-folder-open text-gray-300 text-5xl mb-4"></i>
          <h3 class="text-lg font-medium text-gray-900 mb-2">尚無項目</h3>
          <p class="text-gray-500 mb-6">開始創建您的第一個影片項目</p>
          <button 
            @click="goToCreateVideo"
            class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-colors"
          >
            <i class="fas fa-plus mr-2"></i>
            創建影片
          </button>
        </div>

        <!-- 分頁 -->
        <div v-if="filteredProjects.length > 0" class="flex items-center justify-between mt-8">
          <div class="text-sm text-gray-500">
            顯示 {{ (currentPage - 1) * pageSize + 1 }}-{{ Math.min(currentPage * pageSize, filteredProjects.length) }} 項，共 {{ filteredProjects.length }} 項項目
          </div>
          <div class="flex items-center space-x-2">
            <button 
              @click="previousPage"
              :disabled="currentPage === 1"
              class="px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              上一頁
            </button>
            <button 
              v-for="page in visiblePages"
              :key="page"
              @click="currentPage = page"
              :class="[
                'px-3 py-2 border rounded-lg text-sm font-medium transition-colors',
                page === currentPage 
                  ? 'bg-purple-600 border-purple-600 text-white' 
                  : 'border-gray-300 text-gray-700 hover:bg-gray-50'
              ]"
            >
              {{ page }}
            </button>
            <button 
              @click="nextPage"
              :disabled="currentPage === totalPages"
              class="px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              下一頁
            </button>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/user'

// 路由和store
const router = useRouter()
const userStore = useUserStore()

// 響應式數據
const searchQuery = ref('')
const activeFilter = ref('all')
const viewMode = ref('grid')
const showFilterMenu = ref(false)
const currentPage = ref(1)
const pageSize = ref(8)
const projects = ref([
  {
    id: 1,
    title: '產品介紹影片',
    status: 'completed',
    thumbnail: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=300&h=200&fit=crop',
    duration: '01:24',
    digitalHuman: 'Alex數字人',
    updatedAt: new Date(Date.now() - 2 * 60 * 60 * 1000), // 2小時前
    progress: 100
  },
  {
    id: 2,
    title: '課程宣傳片',
    status: 'processing',
    thumbnail: 'https://images.unsplash.com/photo-1522869635100-9f4c5e86aa37?w=300&h=200&fit=crop',
    duration: '00:45',
    digitalHuman: 'Sophie數字人',
    updatedAt: new Date(Date.now() - 24 * 60 * 60 * 1000), // 昨天
    progress: 75
  },
  {
    id: 3,
    title: '團隊介紹',
    status: 'completed',
    thumbnail: 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=300&h=200&fit=crop',
    duration: '02:15',
    digitalHuman: 'Michael數字人',
    updatedAt: new Date(Date.now() - 3 * 24 * 60 * 60 * 1000), // 3天前
    progress: 100
  },
  {
    id: 4,
    title: '市場分析報告',
    status: 'draft',
    digitalHuman: null,
    updatedAt: new Date(Date.now() - 7 * 24 * 60 * 60 * 1000), // 1週前
    progress: 0
  }
])

// 計算屬性
const filteredProjects = computed(() => {
  let filtered = projects.value

  // 按搜索查詢過濾
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(project => 
      project.title.toLowerCase().includes(query) ||
      (project.digitalHuman && project.digitalHuman.toLowerCase().includes(query))
    )
  }

  // 按狀態過濾
  if (activeFilter.value !== 'all') {
    filtered = filtered.filter(project => project.status === activeFilter.value)
  }

  return filtered
})

const totalProjects = computed(() => projects.value.length)
const completedProjects = computed(() => projects.value.filter(p => p.status === 'completed').length)
const processingProjects = computed(() => projects.value.filter(p => p.status === 'processing').length)
const draftProjects = computed(() => projects.value.filter(p => p.status === 'draft').length)

const totalPages = computed(() => Math.ceil(filteredProjects.value.length / pageSize.value))

const visiblePages = computed(() => {
  const pages = []
  const start = Math.max(1, currentPage.value - 2)
  const end = Math.min(totalPages.value, start + 4)
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

// 方法
const getStatusClass = (status: string) => {
  switch (status) {
    case 'completed':
      return 'bg-green-100 text-green-800'
    case 'processing':
      return 'bg-blue-100 text-blue-800'
    case 'draft':
      return 'bg-gray-100 text-gray-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const getStatusText = (status: string) => {
  switch (status) {
    case 'completed':
      return '已完成'
    case 'processing':
      return '處理中'
    case 'draft':
      return '草稿'
    default:
      return '未知'
  }
}

const formatTime = (date: Date) => {
  const now = new Date()
  const diff = now.getTime() - date.getTime()
  const minutes = Math.floor(diff / (1000 * 60))
  const hours = Math.floor(diff / (1000 * 60 * 60))
  const days = Math.floor(diff / (1000 * 60 * 60 * 24))
  const weeks = Math.floor(diff / (1000 * 60 * 60 * 24 * 7))

  if (minutes < 60) {
    return minutes <= 1 ? '剛剛' : `${minutes}分鐘前`
  } else if (hours < 24) {
    return `${hours}小時前`
  } else if (days < 7) {
    return `${days}天前`
  } else {
    return `${weeks}週前`
  }
}

const goToCreateVideo = () => {
  router.push('/create-video')
}

const openProject = (project: any) => {
  if (project.status === 'completed') {
    console.log('打開項目:', project.title)
  } else if (project.status === 'draft') {
    continueEditing(project)
  }
}

const downloadProject = (project: any) => {
  console.log('下載項目:', project.title)
}

const shareProject = (project: any) => {
  console.log('分享項目:', project.title)
}

const editProject = (project: any) => {
  console.log('編輯項目:', project.title)
  router.push(`/edit-video/${project.id}`)
}

const continueEditing = (project: any) => {
  console.log('繼續編輯:', project.title)
  router.push(`/create-video?draft=${project.id}`)
}

const showProjectMenu = (project: any) => {
  console.log('顯示項目菜單:', project.title)
}

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

onMounted(() => {
  console.log('MyVideos 組件已掛載')
})
</script>

<style scoped>
.sidebar-dark {
  background: linear-gradient(180deg, #1a1c20 0%, #2d2f36 100%);
}

.card-hover {
  transition: all 0.3s ease;
}

.card-hover:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>
