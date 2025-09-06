<template>
  <div class="inspiration-container">
    <!-- 顶部导航 -->
    <header class="inspiration-header">
      <button class="back-btn" @click="goBack">
        ← 返回首页
      </button>
      <h1>💡 灵感发想</h1>
      <div class="scenario-info">
        <span class="scenario-badge">{{ currentScenarioName }}</span>
      </div>
    </header>

    <!-- 主要内容区 -->
    <main class="inspiration-main">
      <!-- 左侧导航栏（简化版） -->
      <nav class="side-nav">
        <div class="nav-section">
          <h3>🎬 创作进度</h3>
          <div class="progress-steps">
            <div class="step active">
              <span class="step-icon">💡</span>
              <span class="step-text">灵感发想</span>
            </div>
            <div class="step">
              <span class="step-icon">📝</span>
              <span class="step-text">AI编剧</span>
            </div>
            <div class="step">
              <span class="step-icon">🎬</span>
              <span class="step-text">AI导演</span>
            </div>
            <div class="step">
              <span class="step-icon">🎞️</span>
              <span class="step-text">AI剪辑</span>
            </div>
          </div>
        </div>
        
        <div class="nav-section">
          <h3>💡 快速提示</h3>
          <div class="tips-list">
            <div class="tip-item" v-for="tip in scenarioTips" :key="tip.id">
              <span class="tip-icon">{{ tip.icon }}</span>
              <span class="tip-text">{{ tip.text }}</span>
            </div>
          </div>
        </div>
      </nav>

      <!-- 右侧主工作区 -->
      <section class="main-workspace">
        <!-- Tony编剧问候 -->
        <div class="tony-greeting">
          <div class="tony-avatar">
            <div class="tony-face">👨‍💻</div>
          </div>
          <div class="greeting-bubble">
            <h2>{{ currentGreeting.title }}</h2>
            <p class="greeting-text">{{ currentGreeting.content }}</p>
          </div>
        </div>

        <!-- 输入区域 -->
        <div class="input-section">
          <!-- 场景1: 文字输入区 -->
          <div v-if="scenario === 'fromScratch'" class="text-input-area">
            <div class="input-container">
              <textarea 
                v-model="userInput"
                class="main-textarea"
                placeholder="告诉我你想做什么影片...

举例：
• 時間管理的影片
• TikTok教學影片  
• 健身瘦身教學
• 產品介紹影片
• 料理分享影片

或者告诉我你的影片方向，我可以提供你十个标题想法！"
                rows="8"
              />
              <div class="input-actions">
                <button class="action-btn" @click="generateTitles" :disabled="!userInput.trim()">
                  💡 生成10个标题想法
                </button>
                <button class="primary-btn" @click="continueToScript" :disabled="!userInput.trim()">
                  继续制作影片 →
                </button>
              </div>
            </div>
          </div>

          <!-- 场景2&3: 文件上传区 -->
          <div v-else-if="['remake', 'imitate'].includes(scenario)" class="upload-area">
            <div class="upload-container">
              <div class="upload-zone" @click="triggerFileUpload" :class="{ 'has-file': uploadedFile }">
                <div v-if="!uploadedFile" class="upload-placeholder">
                  <div class="upload-icon">🎬</div>
                  <h3>点击上传影片</h3>
                  <p>支持 MP4、MOV、AVI 格式，最大 500MB</p>
                  <button class="upload-btn">选择影片文件</button>
                </div>
                <div v-else class="upload-success">
                  <div class="success-icon">✅</div>
                  <h3>{{ uploadedFile.name }}</h3>
                  <p>文件大小: {{ formatFileSize(uploadedFile.size) }}</p>
                  <button class="change-btn" @click="changeFile">更换文件</button>
                </div>
              </div>
              
              <div class="url-option">
                <div class="divider">
                  <span>或者</span>
                </div>
                <div class="url-input">
                  <input 
                    type="url" 
                    v-model="videoUrl"
                    placeholder="粘贴 YouTube、TikTok 等视频链接"
                    class="url-field"
                  >
                  <button class="url-btn" @click="analyzeUrl" :disabled="!videoUrl.trim()">
                    🔗 解析链接
                  </button>
                </div>
              </div>

              <div v-if="uploadedFile || videoUrl" class="continue-section">
                <button class="primary-btn" @click="continueToScript">
                  开始{{ scenario === 'remake' ? '重新设计' : '模仿制作' }}影片 →
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- 标题生成结果（场景1专用） -->
        <div v-if="scenario === 'fromScratch' && generatedTitles.length > 0" class="titles-section">
          <h3>💡 为你生成的标题想法：</h3>
          <div class="titles-grid">
            <div 
              v-for="title in generatedTitles" 
              :key="title.id"
              class="title-card"
              :class="{ selected: selectedTitle === title.id }"
              @click="selectTitle(title)"
            >
              <div class="title-content">
                <h4>{{ title.title }}</h4>
                <p>{{ title.description }}</p>
              </div>
              <div class="title-actions">
                <button class="select-btn" v-if="selectedTitle !== title.id">选择</button>
                <button class="selected-btn" v-else>已选择 ✓</button>
              </div>
            </div>
          </div>
          
          <div v-if="selectedTitle" class="selected-actions">
            <button class="primary-btn" @click="continueWithSelectedTitle">
              使用选中标题制作影片 →
            </button>
          </div>
        </div>

        <!-- 解析结果（场景2&3专用） -->
        <div v-if="['remake', 'imitate'].includes(scenario) && analysisResult" class="analysis-section">
          <h3>🔍 影片解析结果：</h3>
          <div class="analysis-card">
            <div class="analysis-thumbnail">
              <img :src="analysisResult.thumbnail" :alt="analysisResult.title">
              <div class="duration-badge">{{ analysisResult.duration }}</div>
            </div>
            <div class="analysis-info">
              <h4>{{ analysisResult.title }}</h4>
              <p>{{ analysisResult.description }}</p>
              <div class="analysis-tags">
                <span v-for="tag in analysisResult.tags" :key="tag" class="tag">{{ tag }}</span>
              </div>
            </div>
          </div>
          
          <div class="analysis-actions">
            <button class="primary-btn" @click="continueToScript">
              开始{{ scenario === 'remake' ? '重新设计' : '模仿制作' }}影片 →
            </button>
          </div>
        </div>
      </section>
    </main>

    <!-- 隐藏的文件输入 -->
    <input 
      type="file" 
      ref="fileInput" 
      @change="handleFileUpload"
      accept="video/*"
      style="display: none"
    >
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

// 获取场景参数
const scenario = ref(route.query.scenario as string || 'fromScratch')

// 用户输入和状态
const userInput = ref('')
const videoUrl = ref('')
const uploadedFile = ref<File | null>(null)
const selectedTitle = ref<number | null>(null)
const generatedTitles = ref<any[]>([])
const analysisResult = ref<any>(null)

// 文件输入引用
const fileInput = ref<HTMLInputElement>()

// 场景名称映射
const scenarioNames = {
  fromScratch: '從零創作影片',
  remake: '重做現有影片', 
  imitate: '模仿爆款影片'
}

// 计算当前场景名称
const currentScenarioName = computed(() => {
  return scenarioNames[scenario.value] || '未知场景'
})

// Tony编剧的不同开场白
const greetings = {
  fromScratch: {
    title: '你好，你選擇了 從零創作影片',
    content: '我是你的Vidspark AI專屬編劇 Tony，請你告訴我你想做什麼樣的影片，舉例：時間管理的影片，TikTok教學影片，或者是你有方向還沒有標題，那你也可以輸入你想要做的影片方向，我可以提供你十個標題想法'
  },
  remake: {
    title: '你好，你選擇了 重做現有影片',
    content: '我是你的Vidspark AI專屬編劇 Tony，請你上傳你想重新設計的影片，我用你的風格，幫你重新設計影片'
  },
  imitate: {
    title: '你好，你選擇了 模仿爆款影片',
    content: '我是你的Vidspark AI專屬編劇 Tony，請你上傳你想模仿的爆款影片，我幫你模仿爆款影片'
  }
}

// 计算当前问候语
const currentGreeting = computed(() => {
  return greetings[scenario.value] || greetings.fromScratch
})

// 场景提示
const scenarioTips = computed(() => {
  const tips = {
    fromScratch: [
      { id: 1, icon: '💡', text: '描述越详细，AI理解越准确' },
      { id: 2, icon: '🎯', text: '可以说明目标受众' },
      { id: 3, icon: '⏱️', text: '可以指定影片长度' },
      { id: 4, icon: '🎨', text: '可以说明想要的风格' }
    ],
    remake: [
      { id: 1, icon: '📹', text: '支持主流视频格式' },
      { id: 2, icon: '🔗', text: '支持YouTube等链接' },
      { id: 3, icon: '⚡', text: 'AI会保持你的风格' },
      { id: 4, icon: '🎨', text: '会优化视觉效果' }
    ],
    imitate: [
      { id: 1, icon: '🔥', text: '选择热门爆款视频' },
      { id: 2, icon: '📊', text: 'AI会分析成功要素' },
      { id: 3, icon: '🎯', text: '保持核心创意逻辑' },
      { id: 4, icon: '✨', text: '加入你的独特元素' }
    ]
  }
  
  return tips[scenario.value] || tips.fromScratch
})

// 生成标题想法
const generateTitles = async () => {
  if (!userInput.value.trim()) return
  
  console.log('💡 [生成标题] 开始生成标题想法')
  
  // 模拟AI生成标题
  await new Promise(resolve => setTimeout(resolve, 2000))
  
  generatedTitles.value = [
    {
      id: 1,
      title: '5分钟掌握高效时间管理',
      description: '简单实用的时间管理技巧，让你每天多出2小时'
    },
    {
      id: 2,
      title: '时间管理大师的秘密武器',
      description: '揭秘成功人士都在用的时间管理方法'
    },
    {
      id: 3,
      title: '从拖延症到高效达人',
      description: '3步告别拖延，成为时间管理高手'
    },
    {
      id: 4,
      title: '时间管理误区大揭秘',
      description: '避开这些坑，时间管理效果翻倍'
    },
    {
      id: 5,
      title: '学生党必备时间管理法',
      description: '专为学生设计的高效学习时间安排'
    }
  ]
}

// 选择标题
const selectTitle = (title: any) => {
  selectedTitle.value = title.id
  console.log('✅ [选择标题]', title.title)
}

// 触发文件上传
const triggerFileUpload = () => {
  if (fileInput.value) {
    fileInput.value.click()
  }
}

// 处理文件上传
const handleFileUpload = (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  
  if (file) {
    uploadedFile.value = file
    console.log('📁 [文件上传]', file.name, formatFileSize(file.size))
  }
}

// 更换文件
const changeFile = () => {
  uploadedFile.value = null
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

// 格式化文件大小
const formatFileSize = (bytes: number) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

// 解析视频链接
const analyzeUrl = async () => {
  if (!videoUrl.value.trim()) return
  
  console.log('🔗 [链接解析]', videoUrl.value)
  
  // 模拟AI解析
  await new Promise(resolve => setTimeout(resolve, 3000))
  
  analysisResult.value = {
    title: '时间管理大师的一天',
    description: '跟拍成功企业家24小时，揭秘高效时间管理的秘密',
    thumbnail: '/api/placeholder/200/120',
    duration: '3:25',
    tags: ['时间管理', '效率提升', '生活方式', '成功人士']
  }
}

// 继续到编剧页面
const continueToScript = () => {
  const params = new URLSearchParams({
    scenario: scenario.value,
    input: userInput.value || '',
    videoUrl: videoUrl.value || '',
    fileName: uploadedFile.value?.name || ''
  })
  
  router.push(`/scriptwriter?${params.toString()}`)
}

// 使用选中标题继续
const continueWithSelectedTitle = () => {
  const selectedTitleObj = generatedTitles.value.find(t => t.id === selectedTitle.value)
  if (selectedTitleObj) {
    const params = new URLSearchParams({
      scenario: scenario.value,
      title: selectedTitleObj.title,
      description: selectedTitleObj.description
    })
    
    router.push(`/scriptwriter?${params.toString()}`)
  }
}

// 返回首页
const goBack = () => {
  router.push('/')
}

// 组件挂载
onMounted(() => {
  console.log('💡 [灵感发想] 页面加载，场景:', scenario.value)
})
</script>

<style scoped>
.inspiration-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  display: flex;
  flex-direction: column;
}

/* 顶部导航 */
.inspiration-header {
  background: white;
  padding: 20px 32px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.back-btn {
  padding: 8px 16px;
  background: #f3f4f6;
  color: #374151;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s ease;
}

.back-btn:hover {
  background: #e5e7eb;
}

.inspiration-header h1 {
  margin: 0;
  color: #1f2937;
  font-size: 24px;
}

.scenario-badge {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
}

/* 主要内容区 */
.inspiration-main {
  flex: 1;
  display: flex;
  gap: 24px;
  padding: 24px;
}

/* 左侧导航 */
.side-nav {
  width: 240px;
  background: white;
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  height: fit-content;
}

.nav-section {
  margin-bottom: 32px;
}

.nav-section:last-child {
  margin-bottom: 0;
}

.nav-section h3 {
  margin: 0 0 16px 0;
  color: #1f2937;
  font-size: 14px;
  font-weight: 600;
}

.progress-steps {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.step {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  border-radius: 8px;
  transition: all 0.2s ease;
}

.step.active {
  background: #667eea;
  color: white;
}

.step-icon {
  font-size: 16px;
}

.step-text {
  font-size: 12px;
  font-weight: 500;
}

.tips-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.tip-item {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  padding: 8px;
  background: #f8fafc;
  border-radius: 6px;
}

.tip-icon {
  font-size: 14px;
  margin-top: 2px;
}

.tip-text {
  font-size: 12px;
  color: #6b7280;
  line-height: 1.4;
}

/* 右侧主工作区 */
.main-workspace {
  flex: 1;
  max-width: 800px;
}

/* Tony编剧问候 */
.tony-greeting {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  margin-bottom: 32px;
}

.tony-avatar {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  border-radius: 50%;
  font-size: 24px;
}

.tony-face {
  color: white;
}

.greeting-bubble {
  background: white;
  padding: 24px;
  border-radius: 20px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  flex: 1;
  position: relative;
}

.greeting-bubble::before {
  content: '';
  position: absolute;
  left: -10px;
  top: 20px;
  border: 10px solid transparent;
  border-right-color: white;
}

.greeting-bubble h2 {
  margin: 0 0 12px 0;
  color: #1f2937;
  font-size: 18px;
}

.greeting-text {
  color: #6b7280;
  line-height: 1.6;
  margin: 0;
  font-size: 14px;
}

/* 输入区域 */
.input-section {
  margin-bottom: 32px;
}

.input-container {
  background: white;
  padding: 32px;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.main-textarea {
  width: 100%;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  padding: 20px;
  font-size: 16px;
  line-height: 1.8;
  resize: vertical;
  min-height: 200px;
  outline: none;
  transition: border-color 0.2s ease;
  box-sizing: border-box;
}

.main-textarea:focus {
  border-color: #10b981;
}

.main-textarea::placeholder {
  color: #9ca3af;
  line-height: 1.6;
}

.input-actions {
  display: flex;
  gap: 12px;
  margin-top: 20px;
  justify-content: flex-end;
}

.action-btn {
  padding: 12px 20px;
  border: 2px solid #e5e7eb;
  background: white;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s ease;
}

.action-btn:hover:not(:disabled) {
  border-color: #10b981;
  background: #f0fdf4;
}

.action-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.primary-btn {
  padding: 12px 24px;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.2s ease;
}

.primary-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(16,185,129,0.3);
}

.primary-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* 文件上传区 */
.upload-container {
  background: white;
  padding: 32px;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.upload-zone {
  border: 2px dashed #d1d5db;
  border-radius: 12px;
  padding: 40px;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-bottom: 24px;
}

.upload-zone:hover {
  border-color: #10b981;
  background: #f0fdf4;
}

.upload-zone.has-file {
  border-color: #10b981;
  background: #f0fdf4;
}

.upload-icon {
  font-size: 48px;
  margin-bottom: 16px;
}

.upload-placeholder h3 {
  margin: 0 0 8px 0;
  color: #374151;
  font-size: 18px;
}

.upload-placeholder p {
  margin: 0 0 20px 0;
  color: #6b7280;
}

.upload-btn {
  padding: 12px 24px;
  background: #10b981;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
}

.success-icon {
  font-size: 48px;
  margin-bottom: 16px;
}

.upload-success h3 {
  margin: 0 0 8px 0;
  color: #059669;
  font-size: 18px;
}

.upload-success p {
  margin: 0 0 20px 0;
  color: #6b7280;
}

.change-btn {
  padding: 8px 16px;
  background: #f3f4f6;
  color: #374151;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 12px;
}

.divider {
  text-align: center;
  margin: 20px 0;
  position: relative;
}

.divider::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 1px;
  background: #e5e7eb;
}

.divider span {
  background: white;
  padding: 0 16px;
  color: #6b7280;
  font-size: 14px;
  position: relative;
}

.url-input {
  display: flex;
  gap: 12px;
  align-items: center;
}

.url-field {
  flex: 1;
  padding: 12px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 14px;
  outline: none;
}

.url-field:focus {
  border-color: #10b981;
}

.url-btn {
  padding: 12px 20px;
  background: #10b981;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
  white-space: nowrap;
}

.url-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.continue-section {
  margin-top: 24px;
  text-align: center;
}

/* 标题生成结果 */
.titles-section {
  background: white;
  padding: 32px;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  margin-bottom: 32px;
}

.titles-section h3 {
  margin: 0 0 24px 0;
  color: #1f2937;
  font-size: 18px;
}

.titles-grid {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 24px;
}

.title-card {
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  padding: 20px;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.title-card:hover {
  border-color: #10b981;
  background: #f0fdf4;
}

.title-card.selected {
  border-color: #10b981;
  background: #f0fdf4;
}

.title-content h4 {
  margin: 0 0 8px 0;
  color: #1f2937;
  font-size: 16px;
}

.title-content p {
  margin: 0;
  color: #6b7280;
  font-size: 14px;
}

.select-btn, .selected-btn {
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 12px;
  font-weight: 500;
}

.select-btn {
  background: #10b981;
  color: white;
}

.selected-btn {
  background: #059669;
  color: white;
}

.selected-actions {
  text-align: center;
}

/* 解析结果 */
.analysis-section {
  background: white;
  padding: 32px;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  margin-bottom: 32px;
}

.analysis-section h3 {
  margin: 0 0 24px 0;
  color: #1f2937;
  font-size: 18px;
}

.analysis-card {
  display: flex;
  gap: 20px;
  margin-bottom: 24px;
}

.analysis-thumbnail {
  position: relative;
  width: 200px;
  height: 120px;
  border-radius: 8px;
  overflow: hidden;
}

.analysis-thumbnail img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.duration-badge {
  position: absolute;
  bottom: 8px;
  right: 8px;
  background: rgba(0,0,0,0.8);
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
}

.analysis-info {
  flex: 1;
}

.analysis-info h4 {
  margin: 0 0 12px 0;
  color: #1f2937;
  font-size: 16px;
}

.analysis-info p {
  margin: 0 0 16px 0;
  color: #6b7280;
  line-height: 1.6;
}

.analysis-tags {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.tag {
  background: #f3f4f6;
  color: #6b7280;
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 12px;
}

.analysis-actions {
  text-align: center;
}

/* 响应式设计 */
@media (max-width: 1024px) {
  .inspiration-main {
    flex-direction: column;
  }
  
  .side-nav {
    width: 100%;
  }
  
  .progress-steps {
    flex-direction: row;
    overflow-x: auto;
  }
}

@media (max-width: 768px) {
  .inspiration-header {
    padding: 16px;
  }
  
  .inspiration-main {
    padding: 16px;
  }
  
  .tony-greeting {
    flex-direction: column;
    text-align: center;
  }
  
  .greeting-bubble::before {
    display: none;
  }
  
  .analysis-card {
    flex-direction: column;
  }
  
  .analysis-thumbnail {
    width: 100%;
  }
  
  .input-actions {
    flex-direction: column;
  }
  
  .url-input {
    flex-direction: column;
  }
}
</style>
