<template>
  <div class="h-screen flex bg-gray-50">
    <!-- 左側導航欄 - 簡化版v6.0風格 -->
    <div class="w-72 sidebar-dark flex flex-col">
      <!-- Logo區域 -->
      <div class="p-6 border-b border-gray-700">
        <div class="flex items-center">
          <div class="w-10 h-10 bg-gradient-to-r from-purple-600 to-blue-600 rounded-xl flex items-center justify-center mr-3">
            <i class="fas fa-video text-white text-lg"></i>
          </div>
          <h1 class="text-xl font-bold text-white">Vidspark</h1>
        </div>
      </div>

      <!-- 🆕 創建大按鈕 -->
      <div class="p-6">
        <button 
          @click="createNewProject"
          class="w-full bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white py-4 px-6 rounded-xl font-semibold transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center text-lg"
        >
          <i class="fas fa-plus mr-3 text-xl"></i>
          開始創作影片
        </button>
      </div>

      <!-- 🆕 核心工作流導航 -->
      <nav class="flex-1 px-6">
        <div class="mb-6">
          <h3 class="text-gray-400 text-sm font-medium uppercase tracking-wider mb-3">創作工作流</h3>
          <ul class="space-y-2">
            <li>
              <router-link 
                to="/" 
                class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors group"
              >
                <i class="fas fa-home mr-3 text-lg group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">儀表板</span>
              </router-link>
            </li>
            <li>
              <router-link 
                to="/inspiration" 
                class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors group"
              >
                <i class="fas fa-lightbulb mr-3 text-lg group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">靈感創作區</span>
              </router-link>
            </li>
            <li>
              <router-link 
                to="/scriptwriter" 
                class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors group"
              >
                <i class="fas fa-feather-alt mr-3 text-lg group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">AI編劇</span>
              </router-link>
            </li>
            <li>
              <router-link 
                to="/director" 
                class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors group"
              >
                <i class="fas fa-video mr-3 text-lg group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">AI導演</span>
              </router-link>
            </li>
            <li>
              <router-link 
                to="/editor" 
                class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors group"
              >
                <i class="fas fa-cut mr-3 text-lg group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">AI剪輯</span>
              </router-link>
            </li>
            <li>
              <router-link 
                to="/projects" 
                class="flex items-center px-4 py-3 text-white bg-purple-600 rounded-lg shadow-md"
              >
                <i class="fas fa-folder mr-3 text-lg"></i>
                <span class="font-medium">我的項目</span>
              </router-link>
            </li>
          </ul>
        </div>

        <div class="mb-6">
          <h3 class="text-gray-400 text-sm font-medium uppercase tracking-wider mb-3">資產管理</h3>
          <ul class="space-y-2">
            <li>
              <router-link 
                to="/avatars" 
                class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors group"
              >
                <i class="fas fa-user-tie mr-3 text-lg group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">數字人庫</span>
                <span class="ml-auto bg-blue-500 text-white text-xs px-2 py-1 rounded-full">{{ assetStats.avatars }}</span>
              </router-link>
            </li>
            <li>
              <router-link 
                to="/voices" 
                class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors group"
              >
                <i class="fas fa-microphone mr-3 text-lg group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">聲音庫</span>
                <span class="ml-auto bg-green-500 text-white text-xs px-2 py-1 rounded-full">{{ assetStats.voices }}</span>
              </router-link>
            </li>
            <li>
              <router-link 
                to="/media" 
                class="flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors group"
              >
                <i class="fas fa-images mr-3 text-lg group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">媒體庫</span>
                <span class="ml-auto bg-orange-500 text-white text-xs px-2 py-1 rounded-full">{{ assetStats.media }}</span>
              </router-link>
            </li>
          </ul>
        </div>
      </nav>

      <!-- 🆕 用戶信息與積分 -->
      <div class="p-6 border-t border-gray-700">
        <!-- 積分顯示 -->
        <div class="mb-4 p-4 bg-gradient-to-r from-yellow-500/20 to-orange-500/20 rounded-xl border border-yellow-500/30">
          <div class="flex items-center justify-between mb-2">
            <span class="text-yellow-200 text-sm font-medium">剩餘積分</span>
            <span class="text-yellow-100 text-lg font-bold">{{ userInfo.credits }}</span>
          </div>
          <button 
            @click="upgradePlan"
            class="w-full bg-gradient-to-r from-yellow-500 to-orange-500 text-white py-2 px-4 rounded-lg font-medium text-sm hover:from-yellow-600 hover:to-orange-600 transition-all"
          >
            <i class="fas fa-crown mr-2"></i>
            升級獲得更多積分
          </button>
        </div>
        
        <!-- 用戶頭像 -->
        <div class="flex items-center">
          <img 
            :src="userInfo.avatar" 
            :alt="userInfo.name" 
            class="w-12 h-12 rounded-full border-2 border-purple-500"
          >
          <div class="ml-3 flex-1">
            <p class="text-white text-sm font-semibold">{{ userInfo.name }}</p>
            <p class="text-gray-400 text-xs">{{ userInfo.plan }}</p>
          </div>
          <button class="text-gray-400 hover:text-white p-2 hover:bg-gray-700 rounded-lg transition-colors">
            <i class="fas fa-cog"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- 右側主內容區 - v6.0現代化設計 -->
    <div class="flex-1 flex flex-col overflow-hidden bg-white">
      <!-- 🆕 現代化頂部欄 -->
      <header class="bg-white border-b border-gray-100 px-8 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">我的項目</h1>
            <p class="text-gray-600 text-lg">管理您的所有影片創作項目 • 共 {{ projectStats.total }} 個項目</p>
          </div>
          
          <div class="flex items-center space-x-4">
            <!-- 🆕 現代化搜索框 -->
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="搜索項目標題或描述..."
                class="w-80 pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent text-lg shadow-sm bg-gray-50 focus:bg-white transition-all"
              >
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400 text-lg"></i>
              </div>
            </div>
            
            <!-- 🆕 精簡篩選按鈕 -->
            <div class="relative">
              <button 
                @click="showFilters = !showFilters"
                class="flex items-center px-6 py-3 border border-gray-200 rounded-xl hover:bg-gray-50 text-lg font-medium transition-all"
                :class="filterStatus || filterTime ? 'bg-purple-50 border-purple-300 text-purple-700' : 'text-gray-700'"
              >
                <i class="fas fa-filter mr-2"></i>
                篩選
                <span v-if="filterStatus || filterTime" class="ml-2 w-2 h-2 bg-purple-500 rounded-full"></span>
              </button>
              
              <!-- 篩選下拉菜單 -->
              <div 
                v-if="showFilters"
                class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-gray-200 z-10 overflow-hidden"
              >
                <div class="p-6">
                  <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">項目狀態</label>
                    <select v-model="filterStatus" class="w-full border border-gray-200 rounded-lg px-4 py-3 text-lg focus:ring-2 focus:ring-purple-500">
                      <option value="">全部狀態</option>
                      <option value="completed">✅ 已完成</option>
                      <option value="processing">⏳ 處理中</option>
                      <option value="draft">📝 草稿</option>
                    </select>
                  </div>
                  <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">創建時間</label>
                    <select v-model="filterTime" class="w-full border border-gray-200 rounded-lg px-4 py-3 text-lg focus:ring-2 focus:ring-purple-500">
                      <option value="">全部時間</option>
                      <option value="today">📅 今天</option>
                      <option value="week">📅 本週</option>
                      <option value="month">📅 本月</option>
                    </select>
                  </div>
                  <div class="flex space-x-2">
                    <button 
                      @click="clearFilters"
                      class="flex-1 bg-gray-100 text-gray-700 py-2 rounded-lg hover:bg-gray-200 font-medium"
                    >
                      清除
                    </button>
                    <button 
                      @click="applyFilters"
                      class="flex-1 bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 font-medium"
                    >
                      應用
                    </button>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- 🆕 現代化視圖切換 -->
            <div class="flex items-center bg-gray-100 rounded-xl p-1">
              <button 
                @click="viewMode = 'grid'"
                :class="viewMode === 'grid' ? 'bg-white text-purple-600 shadow-sm' : 'text-gray-600 hover:bg-gray-200'"
                class="p-3 rounded-lg transition-all font-medium"
              >
                <i class="fas fa-th text-lg"></i>
              </button>
              <button 
                @click="viewMode = 'list'"
                :class="viewMode === 'list' ? 'bg-white text-purple-600 shadow-sm' : 'text-gray-600 hover:bg-gray-200'"
                class="p-3 rounded-lg transition-all font-medium"
              >
                <i class="fas fa-list text-lg"></i>
              </button>
            </div>
          </div>
        </div>
      </header>

      <!-- 🆕 現代化主內容滾動區域 -->
      <main class="flex-1 overflow-y-auto p-8 bg-gray-50">
        <!-- 🆕 現代化項目統計 -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all group">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-semibold text-gray-600 mb-2">總項目數</p>
                <p class="text-3xl font-bold text-gray-900">{{ projectStats.total }}</p>
                <p class="text-sm text-green-600 mt-2">
                  <i class="fas fa-arrow-up mr-1"></i>
                  +2 本週新增
                </p>
              </div>
              <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-video text-white text-2xl"></i>
              </div>
            </div>
          </div>
          
          <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all group">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-semibold text-gray-600 mb-2">已完成</p>
                <p class="text-3xl font-bold text-gray-900">{{ projectStats.completed }}</p>
                <p class="text-sm text-blue-600 mt-2">
                  <i class="fas fa-chart-line mr-1"></i>
                  完成率 {{ Math.round((projectStats.completed / projectStats.total) * 100) }}%
                </p>
              </div>
              <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-check-circle text-white text-2xl"></i>
              </div>
            </div>
          </div>
          
          <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all group">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-semibold text-gray-600 mb-2">處理中</p>
                <p class="text-3xl font-bold text-gray-900">{{ projectStats.processing }}</p>
                <p class="text-sm text-orange-600 mt-2">
                  <i class="fas fa-clock mr-1"></i>
                  預計2小時完成
                </p>
              </div>
              <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-cogs text-white text-2xl"></i>
              </div>
            </div>
          </div>
          
          <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all group">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-semibold text-gray-600 mb-2">草稿</p>
                <p class="text-3xl font-bold text-gray-900">{{ projectStats.draft }}</p>
                <p class="text-sm text-gray-600 mt-2">
                  <i class="fas fa-edit mr-1"></i>
                  待完善編輯
                </p>
              </div>
              <div class="w-16 h-16 bg-gradient-to-br from-gray-400 to-gray-500 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-file-alt text-white text-2xl"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- 🆕 無項目狀態顯示 -->
        <div v-if="filteredProjects.length === 0 && !searchQuery && !filterStatus" class="text-center py-16">
          <div class="w-32 h-32 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-video text-gray-400 text-4xl"></i>
          </div>
          <h3 class="text-2xl font-bold text-gray-900 mb-4">開始您的第一個創作！</h3>
          <p class="text-gray-600 text-lg mb-8 max-w-md mx-auto">使用我們的AI創作工具，幾分鐘內創建專業的影片內容</p>
          <button 
            @click="createNewProject"
            class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold rounded-xl hover:from-purple-700 hover:to-blue-700 transition-all transform hover:-translate-y-0.5 shadow-lg hover:shadow-xl"
          >
            <i class="fas fa-plus mr-3 text-xl"></i>
            立即開始創作
          </button>
        </div>

        <!-- 🆕 現代化項目網格視圖 -->
        <div v-if="viewMode === 'grid' && filteredProjects.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
          <div 
            v-for="project in filteredProjects" 
            :key="project.id"
            @click="openProject(project)"
            class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-hover cursor-pointer group hover:shadow-xl transition-all duration-300"
          >
            <div class="relative">
              <img 
                :src="project.thumbnail" 
                :alt="project.title" 
                class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500"
              >
              <!-- 🆕 現代化預覽遮罩 -->
              <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center">
                <div class="w-16 h-16 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center transform scale-0 group-hover:scale-100 transition-transform duration-300">
                  <i class="fas fa-play text-purple-600 text-2xl ml-1"></i>
                </div>
              </div>
              
              <!-- 🆕 現代化時長標籤 -->
              <div class="absolute bottom-3 right-3 bg-black/80 backdrop-blur-sm text-white text-sm px-3 py-1 rounded-lg font-medium">
                {{ project.duration }}
              </div>
              
              <!-- 🆕 狀態指示器 -->
              <div class="absolute top-3 left-3">
                <span 
                  :class="getStatusBadgeClass(project.status)"
                  class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold backdrop-blur-sm"
                >
                  {{ getStatusIcon(project.status) }} {{ getStatusText(project.status) }}
                </span>
              </div>
              
              <!-- 🆕 項目菜單 -->
              <div class="absolute top-3 right-3">
                <button 
                  @click.stop="showProjectMenu(project, $event)"
                  class="w-10 h-10 bg-white/80 backdrop-blur-sm rounded-xl flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 hover:bg-white/90"
                >
                  <i class="fas fa-ellipsis-v text-gray-700"></i>
                </button>
              </div>
            </div>
            
            <!-- 🆕 現代化項目信息 -->
            <div class="p-6">
              <h3 class="font-bold text-gray-900 mb-2 text-lg truncate">{{ project.title }}</h3>
              <p class="text-gray-600 mb-4 text-sm line-clamp-2">{{ project.description || '暫無描述' }}</p>
              
              <div class="flex items-center justify-between">
                <div class="flex items-center text-sm text-gray-500">
                  <i class="fas fa-calendar-alt mr-2"></i>
                  {{ project.createdAt }}
                </div>
                <div class="flex items-center text-sm text-gray-500">
                  <i class="fas fa-eye mr-2"></i>
                  {{ project.views }}
                </div>
              </div>
              
              <!-- 🆕 項目進度條（處理中項目顯示） -->
              <div v-if="project.status === 'processing'" class="mt-4">
                <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                  <span>處理進度</span>
                  <span>{{ project.progress || 65 }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div 
                    class="bg-gradient-to-r from-blue-500 to-purple-500 h-2 rounded-full transition-all duration-300"
                    :style="{ width: (project.progress || 65) + '%' }"
                  ></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 🆕 搜索無結果狀態 -->
        <div v-if="filteredProjects.length === 0 && (searchQuery || filterStatus)" class="text-center py-16">
          <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-search text-gray-400 text-3xl"></i>
          </div>
          <h3 class="text-xl font-bold text-gray-900 mb-4">找不到相關項目</h3>
          <p class="text-gray-600 mb-6">嘗試調整搜索條件或篩選器</p>
          <button 
            @click="clearFilters"
            class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-medium rounded-xl hover:bg-purple-700 transition-colors"
          >
            <i class="fas fa-times mr-2"></i>
            清除篩選
          </button>
        </div>

        <!-- 項目列表視圖 -->
        <div v-if="viewMode === 'list'" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">項目</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">狀態</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">創建時間</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">時長</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">瀏覽次數</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr 
                  v-for="project in filteredProjects" 
                  :key="project.id"
                  @click="openProject(project)"
                  class="hover:bg-gray-50 cursor-pointer"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <img 
                        :src="project.thumbnail" 
                        :alt="project.title"
                        class="h-12 w-20 rounded-lg object-cover mr-4"
                      >
                      <div>
                        <div class="text-sm font-medium text-gray-900">{{ project.title }}</div>
                        <div class="text-sm text-gray-500">{{ project.description || '無描述' }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span 
                      :class="getStatusClass(project.status)"
                      class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    >
                      {{ getStatusText(project.status) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ project.createdAt }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ project.duration }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ project.views }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button 
                      @click.stop="showProjectMenu(project, $event)"
                      class="text-gray-400 hover:text-gray-600"
                    >
                      <i class="fas fa-ellipsis-h"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- 項目菜單彈窗 -->
        <div 
          v-if="projectMenu.show"
          class="fixed inset-0 z-50"
          @click="projectMenu.show = false"
        >
          <div 
            class="absolute bg-white rounded-lg shadow-lg border border-gray-200 py-2 min-w-[160px]"
            :style="{ top: projectMenu.y + 'px', left: projectMenu.x + 'px' }"
            @click.stop
          >
            <button 
              @click="editProject(projectMenu.project)"
              class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center"
            >
              <i class="fas fa-edit mr-2"></i>
              編輯
            </button>
            <button 
              @click="duplicateProject(projectMenu.project)"
              class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center"
            >
              <i class="fas fa-copy mr-2"></i>
              複製
            </button>
            <button 
              @click="downloadProject(projectMenu.project)"
              class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center"
            >
              <i class="fas fa-download mr-2"></i>
              下載
            </button>
            <button 
              @click="shareProject(projectMenu.project)"
              class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center"
            >
              <i class="fas fa-share mr-2"></i>
              分享
            </button>
            <div class="border-t border-gray-100 my-1"></div>
            <button 
              @click="deleteProject(projectMenu.project)"
              class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center"
            >
              <i class="fas fa-trash mr-2"></i>
              刪除
            </button>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

// 響應式數據
const searchQuery = ref('')
const showFilters = ref(false)
const filterStatus = ref('')
const filterTime = ref('')
const viewMode = ref('grid')

// 用戶信息
const userInfo = reactive({
  name: '張小明',
  plan: '免費會員',
  credits: 128,
  avatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face'
})

// 資產統計
const assetStats = reactive({
  avatars: 3,
  voices: 5,
  media: 12
})

// 項目統計
const projectStats = reactive({
  total: 12,
  completed: 8,
  processing: 2,
  draft: 2
})

// 項目菜單
const projectMenu = reactive({
  show: false,
  x: 0,
  y: 0,
  project: null
})

// 項目列表
const projects = ref([
  {
    id: 1,
    title: '產品介紹影片',
    thumbnail: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=300&h=200&fit=crop',
    duration: '01:24',
    createdAt: '2小時前',
    status: 'completed',
    views: 125,
    description: 'AI數字人產品功能介紹'
  },
  {
    id: 2,
    title: '課程宣傳片',
    thumbnail: 'https://images.unsplash.com/photo-1522869635100-9f4c5e86aa37?w=300&h=200&fit=crop',
    duration: '00:45',
    createdAt: '昨天',
    status: 'processing',
    views: 89,
    description: '線上課程推廣影片'
  },
  {
    id: 3,
    title: '團隊介紹',
    thumbnail: 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=300&h=200&fit=crop',
    duration: '02:15',
    createdAt: '3天前',
    status: 'completed',
    views: 203,
    description: '公司團隊成員介紹'
  },
  {
    id: 4,
    title: '市場分析報告',
    thumbnail: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=300&h=200&fit=crop',
    duration: '01:58',
    createdAt: '1週前',
    status: 'draft',
    views: 45,
    description: '2024年市場趨勢分析'
  },
  {
    id: 5,
    title: '客戶見證影片',
    thumbnail: 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=300&h=200&fit=crop',
    duration: '01:32',
    createdAt: '5天前',
    status: 'completed',
    views: 167,
    description: '客戶使用心得分享'
  },
  {
    id: 6,
    title: '新功能演示',
    thumbnail: 'https://images.unsplash.com/photo-1589254065878-42c9da997008?w=300&h=200&fit=crop',
    duration: '02:48',
    createdAt: '1週前',
    status: 'processing',
    views: 78,
    description: '最新功能操作演示'
  },
  {
    id: 7,
    title: '企業文化宣傳',
    thumbnail: 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=300&h=200&fit=crop',
    duration: '03:15',
    createdAt: '2週前',
    status: 'completed',
    views: 234,
    description: '企業價值觀展示'
  },
  {
    id: 8,
    title: '產品更新說明',
    thumbnail: 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?w=300&h=200&fit=crop',
    duration: '01:12',
    createdAt: '3週前',
    status: 'draft',
    views: 23,
    description: 'v2.0版本更新內容'
  }
])

// 計算屬性
const filteredProjects = computed(() => {
  let filtered = projects.value

  // 搜索篩選
  if (searchQuery.value) {
    filtered = filtered.filter(project => 
      project.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      (project.description && project.description.toLowerCase().includes(searchQuery.value.toLowerCase()))
    )
  }

  // 狀態篩選
  if (filterStatus.value) {
    filtered = filtered.filter(project => project.status === filterStatus.value)
  }

  // 時間篩選 (這裡簡化處理，實際開發中需要根據真實日期)
  if (filterTime.value) {
    // 根據filterTime.value篩選
  }

  return filtered
})

const getStatusClass = computed(() => (status: string) => {
  const classes = {
    completed: 'bg-green-100 text-green-800',
    processing: 'bg-blue-100 text-blue-800',
    draft: 'bg-gray-100 text-gray-800'
  }
  return classes[status as keyof typeof classes] || 'bg-gray-100 text-gray-800'
})

const getStatusText = computed(() => (status: string) => {
  const texts = {
    completed: '已完成',
    processing: '處理中',
    draft: '草稿'
  }
  return texts[status as keyof typeof texts] || '未知'
})

// 🆕 新增狀態徽章樣式
const getStatusBadgeClass = computed(() => (status: string) => {
  const classes = {
    completed: 'bg-green-500/90 text-white',
    processing: 'bg-blue-500/90 text-white',
    draft: 'bg-gray-500/90 text-white'
  }
  return classes[status as keyof typeof classes] || 'bg-gray-500/90 text-white'
})

// 🆕 新增狀態圖標
const getStatusIcon = computed(() => (status: string) => {
  const icons = {
    completed: '✅',
    processing: '⏳',
    draft: '📝'
  }
  return icons[status as keyof typeof icons] || '❓'
})

// 方法
const createNewProject = () => {
  console.log('創建新項目')
  // 跳轉到仪表板開始創作
  router.push('/')
}

const upgradePlan = () => {
  console.log('升級方案')
  window.open('/vidspark-v2/pricing.html', '_blank')
}

const applyFilters = () => {
  console.log('應用篩選:', { filterStatus: filterStatus.value, filterTime: filterTime.value })
  showFilters.value = false
}

// 🆕 清除篩選方法
const clearFilters = () => {
  filterStatus.value = ''
  filterTime.value = ''
  searchQuery.value = ''
  showFilters.value = false
}

const openProject = (project: any) => {
  console.log('打開項目:', project)
  // 跳轉到項目編輯或預覽頁面
}

const showProjectMenu = (project: any, event: MouseEvent) => {
  projectMenu.project = project
  projectMenu.x = event.clientX
  projectMenu.y = event.clientY
  projectMenu.show = true
}

const editProject = (project: any) => {
  console.log('編輯項目:', project)
  projectMenu.show = false
  // 跳轉到編輯頁面
}

const duplicateProject = (project: any) => {
  console.log('複製項目:', project)
  projectMenu.show = false
  // 複製項目邏輯
}

const downloadProject = (project: any) => {
  console.log('下載項目:', project)
  projectMenu.show = false
  // 下載邏輯
}

const shareProject = (project: any) => {
  console.log('分享項目:', project)
  projectMenu.show = false
  // 分享邏輯
}

const deleteProject = (project: any) => {
  if (confirm(`確定要刪除項目「${project.title}」嗎？此操作不可恢復。`)) {
    console.log('刪除項目:', project)
    const index = projects.value.findIndex(p => p.id === project.id)
    if (index > -1) {
      projects.value.splice(index, 1)
      // 更新統計數據
      projectStats.total--
      if (project.status === 'completed') projectStats.completed--
      else if (project.status === 'processing') projectStats.processing--
      else if (project.status === 'draft') projectStats.draft--
    }
  }
  projectMenu.show = false
}

// 生命週期
onMounted(() => {
  console.log('Projects 組件已掛載')
  // 載入項目數據
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
  transform: translateY(-4px);
  box-shadow: 0 20px 40px rgba(93, 95, 239, 0.15);
}
</style>