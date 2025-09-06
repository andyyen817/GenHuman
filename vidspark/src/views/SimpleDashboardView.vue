<template>
  <div class="homepage-container">
    <!-- 左侧导航栏 -->
    <nav class="simple-sidebar">
      <!-- 顶部Logo -->
      <div class="logo-section">
        <h1>🎬 Vidspark</h1>
        <p>AI影片创作</p>
      </div>
      
      <!-- 用户状态（简化） -->
      <div class="user-status">
        <div class="user-avatar">👤</div>
        <div class="user-info">
          <p>{{ userName }}</p>
          <span class="credits">💰 {{ userCredits }}积分</span>
        </div>
      </div>
      
      <!-- 主要功能导航（超级简化） -->
      <div class="main-nav">
        <a href="#" class="nav-item active" @click="currentSection = 'create'">
          <i>🏠</i>
          <span>开始创作</span>
        </a>
        
        <a href="#" class="nav-item" @click="currentSection = 'projects'">
          <i>📁</i>
          <span>我的影片</span>
        </a>
        
        <a href="#" class="nav-item" @click="currentSection = 'credits'">
          <i>💰</i>
          <span>充值积分</span>
        </a>
        
        <a href="#" class="nav-item" @click="currentSection = 'help'">
          <i>❓</i>
          <span>使用帮助</span>
        </a>
      </div>

      <!-- 语言选择 -->
      <div class="language-section">
        <h4>🌍 语言选择</h4>
        <select v-model="selectedLanguage" @change="changeLanguage" class="language-select">
          <option value="zh-CN">🇨🇳 简体中文</option>
          <option value="zh-TW">🇹🇼 繁體中文</option>
          <option value="en">🇺🇸 English</option>
        </select>
      </div>

      <!-- 用户资料 -->
      <div class="user-profile-section">
        <h4>📝 用户资料</h4>
        <button @click="showProfileModal = true" class="profile-btn">
          {{ hasUserProfile ? '编辑资料' : '设置资料' }}
        </button>
        <div v-if="hasUserProfile" class="profile-summary">
          <p v-if="userProfile.company">🏢 {{ userProfile.company.substring(0, 20) }}...</p>
          <p v-if="userProfile.product">📦 {{ userProfile.product.substring(0, 20) }}...</p>
          <p v-if="userProfile.personal">👤 {{ userProfile.personal.substring(0, 20) }}...</p>
        </div>
      </div>
      
      <!-- 底部快速帮助 -->
      <div class="quick-help">
        <div class="help-card">
          <h4>🎥 3分钟学会</h4>
          <p>观看教学视频</p>
          <button class="watch-btn" @click="showTutorial">立即观看</button>
        </div>
      </div>
    </nav>

    <!-- 右侧对话式创作区 -->
    <main class="conversation-area">
      <!-- AI制片人问候 -->
      <div class="ai-greeting">
        <div class="ai-avatar">
          <div class="ai-face">👩‍🎬</div>
        </div>
        <div class="greeting-bubble">
          <h2>你好，{{ userName }}！我是你的Vidspark AI专属制片人 Lucy</h2>
          <p class="greeting-text">请告诉我，你今天想从下面哪个场景，开始制作影片</p>
        </div>
      </div>
      
      <!-- 直接显示场景选择，不需要输入框 -->
      
      <!-- 6大场景选择 -->
      <div class="scenario-grid">
        <div class="scenario-cards">
          <!-- 场景1：从零创作影片 -->
          <div class="scenario-card popular" @click="selectScenario('fromScratch')">
            <div class="card-icon">🎬</div>
            <h4>從零創作影片</h4>
            <p>說出你的想法，我來幫你做成影片</p>
            <span class="popular-tag">🔥 最受欢迎</span>
            <button class="scenario-btn">立即开始</button>
          </div>
          
          <!-- 场景2：重做现有影片 -->
          <div class="scenario-card" @click="selectScenario('remake')">
            <div class="card-icon">🔄</div>
            <h4>重做現有影片</h4>
            <p>上傳影片，我用你的風格，幫你重新設計影片</p>
            <button class="scenario-btn">立即开始</button>
          </div>
          
          <!-- 场景3：模仿爆款影片 -->
          <div class="scenario-card" @click="selectScenario('imitate')">
            <div class="card-icon">🎭</div>
            <h4>模仿爆款影片</h4>
            <p>上傳影片，我模仿影片風格，幫你模仿爆款影片</p>
            <button class="scenario-btn">立即开始</button>
          </div>
          
          <!-- 场景4：快速制作数字人 -->
          <div class="scenario-card" @click="selectScenario('digitalHuman')">
            <div class="card-icon">👤</div>
            <h4>快速製作數字人</h4>
            <p>我有文案，用數字人，聲音克隆，快速製作影片</p>
            <button class="scenario-btn">立即开始</button>
          </div>
          
          <!-- 场景5：制作PPT影片 -->
          <div class="scenario-card new" @click="selectScenario('pptVideo')">
            <div class="card-icon">📊</div>
            <h4>製作PPT影片</h4>
            <p>我有PPT，想要加上文案跟配音，快速制作影片</p>
            <span class="new-tag">🆕 新功能</span>
            <button class="scenario-btn">立即开始</button>
          </div>
          
          <!-- 场景6：快速切片 -->
          <div class="scenario-card" @click="selectScenario('videoClip')">
            <div class="card-icon">✂️</div>
            <h4>快速切片</h4>
            <p>我有影片，想要讓AI幫我切出精采片段</p>
            <button class="scenario-btn">立即开始</button>
          </div>
        </div>
      </div>
    </main>

    <!-- 用户资料设置模态框 -->
    <div v-if="showProfileModal" class="modal-overlay" @click="showProfileModal = false">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>📝 设置用户资料</h3>
          <button @click="showProfileModal = false" class="close-btn">✕</button>
        </div>
        
        <div class="modal-body">
          <p class="modal-description">
            设置这些资料可以帮助AI更好地了解您，制作更符合您需求的影片内容。
          </p>
          
          <div class="profile-form">
            <div class="form-group">
              <label>🏢 公司介绍</label>
              <textarea 
                v-model="tempProfile.company"
                placeholder="请简单介绍您的公司、业务范围等..."
                rows="3"
              ></textarea>
            </div>
            
            <div class="form-group">
              <label>📦 产品介绍</label>
              <textarea 
                v-model="tempProfile.product"
                placeholder="请介绍您的主要产品或服务..."
                rows="3"
              ></textarea>
            </div>
            
            <div class="form-group">
              <label>👤 个人介绍</label>
              <textarea 
                v-model="tempProfile.personal"
                placeholder="请简单介绍您自己、专业背景等..."
                rows="3"
              ></textarea>
            </div>
          </div>
        </div>
        
        <div class="modal-footer">
          <button @click="showProfileModal = false" class="cancel-btn">取消</button>
          <button @click="saveProfile" class="save-btn">保存</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

// 用户数据
const userName = ref('用户')
const userCredits = ref(125)
const currentSection = ref('create')

// 语言选择
const selectedLanguage = ref('zh-CN')

// 用户资料
const showProfileModal = ref(false)
const userProfile = reactive({
  company: '',
  product: '',
  personal: ''
})

const tempProfile = reactive({
  company: '',
  product: '',
  personal: ''
})

// 计算属性：检查是否有用户资料
const hasUserProfile = computed(() => {
  return userProfile.company || userProfile.product || userProfile.personal
})

// 成功案例数据
const successExamples = reactive([
  {
    id: 1,
    title: '"3分钟学会理财"',
    author: '小明制作',
    thumbnail: '/api/placeholder/150/100'
  },
  {
    id: 2,
    title: '"宠物护理小贴士"',
    author: '小红制作', 
    thumbnail: '/api/placeholder/150/100'
  },
  {
    id: 3,
    title: '"产品介绍视频"',
    author: '老王制作',
    thumbnail: '/api/placeholder/150/100'
  }
])

// 处理场景选择
const selectScenario = (scenario: string) => {
  console.log('🎯 [场景选择]', scenario)
  
  const scenarios = {
    fromScratch: '從零創作影片',
    remake: '重做現有影片',
    imitate: '模仿爆款影片',
    digitalHuman: '快速製作數字人',
    pptVideo: '製作PPT影片',
    videoClip: '快速切片'
  }
  
  // 场景1、2、3需要到灵感发想页
  if (['fromScratch', 'remake', 'imitate'].includes(scenario)) {
    // 跳转到灵感发想页，传递场景参数
    router.push(`/inspiration?scenario=${scenario}`)
  } 
  // 场景4、5、6直接到编剧区
  else {
    // 跳转到AI编剧区，传递场景参数
    router.push(`/scriptwriter?scenario=${scenario}`)
  }
}


// 显示教程
const showTutorial = () => {
  console.log('🎥 [观看教程]')
  // TODO: 显示教程视频
  alert('准备播放教学视频')
}

// 切换语言
const changeLanguage = (event: Event) => {
  const target = event.target as HTMLSelectElement
  const newLang = target.value
  console.log('🌍 [切换语言]', newLang)
  
  // TODO: 实现多语言切换逻辑
  localStorage.setItem('vidspark_language', newLang)
  
  // 简单的语言提示
  const langNames: Record<string, string> = {
    'zh-CN': '简体中文',
    'zh-TW': '繁體中文',
    'en': 'English'
  }
  alert(`语言已切换到：${langNames[newLang] || newLang}`)
}

// 保存用户资料
const saveProfile = () => {
  // 复制临时数据到正式数据
  userProfile.company = tempProfile.company
  userProfile.product = tempProfile.product
  userProfile.personal = tempProfile.personal
  
  // 保存到本地存储
  localStorage.setItem('vidspark_user_profile', JSON.stringify(userProfile))
  
  console.log('📝 [保存用户资料]', userProfile)
  showProfileModal.value = false
  
  alert('用户资料已保存！AI将根据这些信息为您制作更符合需求的影片。')
}

// 从本地存储加载用户资料
const loadUserProfile = () => {
  const saved = localStorage.getItem('vidspark_user_profile')
  if (saved) {
    try {
      const profile = JSON.parse(saved)
      userProfile.company = profile.company || ''
      userProfile.product = profile.product || ''
      userProfile.personal = profile.personal || ''
      
      // 同步到临时数据
      tempProfile.company = userProfile.company
      tempProfile.product = userProfile.product
      tempProfile.personal = userProfile.personal
    } catch (error) {
      console.error('加载用户资料失败', error)
    }
  }
}

// 组件挂载时加载数据
const initData = () => {
  loadUserProfile()
  
  // 加载语言设置
  const savedLang = localStorage.getItem('vidspark_language')
  if (savedLang) {
    selectedLanguage.value = savedLang
  }
}

// 在脚本加载时初始化
initData()
</script>

<style scoped>
/* 整体布局 */
.homepage-container {
  display: flex;
  height: 100vh;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  background: #ffffff;
}

/* 左侧导航栏 */
.simple-sidebar {
  width: 280px;
  background: white;
  padding: 24px;
  box-shadow: 2px 0 10px rgba(0,0,0,0.1);
  display: flex;
  flex-direction: column;
  overflow-y: auto;
}

.logo-section h1 {
  font-size: 24px;
  margin: 0;
  color: #667eea;
}

.logo-section p {
  font-size: 14px;
  color: #6b7280;
  margin: 4px 0 0 0;
}

.user-status {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px;
  background: #f8fafc;
  border-radius: 12px;
  margin: 24px 0;
}

.user-avatar {
  font-size: 32px;
}

.user-info p {
  margin: 0;
  font-weight: 600;
  color: #374151;
}

.credits {
  font-size: 12px;
  color: #059669;
  font-weight: 600;
}

/* 导航项目 */
.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px;
  text-decoration: none;
  color: #374151;
  border-radius: 12px;
  margin-bottom: 8px;
  transition: all 0.2s ease;
  cursor: pointer;
}

.nav-item:hover {
  background: #f3f4f6;
}

.nav-item.active {
  background: #667eea;
  color: white;
}

.nav-item i {
  font-size: 20px;
}

/* 快速帮助 */
.quick-help {
  margin-top: auto;
  padding-top: 20px;
}

.help-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 16px;
  border-radius: 12px;
  text-align: center;
}

.help-card h4 {
  margin: 0 0 8px 0;
  font-size: 16px;
}

.help-card p {
  margin: 0 0 12px 0;
  font-size: 12px;
  opacity: 0.9;
}

.watch-btn {
  background: rgba(255,255,255,0.2);
  color: white;
  border: 1px solid rgba(255,255,255,0.3);
  padding: 8px 16px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 12px;
  transition: all 0.2s ease;
}

.watch-btn:hover {
  background: rgba(255,255,255,0.3);
}

/* 语言选择区域 */
.language-section {
  margin: 20px 0;
  padding: 16px 0;
  border-top: 1px solid #e5e7eb;
}

.language-section h4 {
  margin: 0 0 12px 0;
  font-size: 14px;
  color: #374151;
  font-weight: 600;
}

.language-select {
  width: 100%;
  padding: 8px 12px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  background: white;
  font-size: 14px;
  cursor: pointer;
  transition: border-color 0.2s ease;
}

.language-select:focus {
  outline: none;
  border-color: #667eea;
}

/* 用户资料区域 */
.user-profile-section {
  margin: 20px 0;
  padding: 16px 0;
  border-top: 1px solid #e5e7eb;
}

.user-profile-section h4 {
  margin: 0 0 12px 0;
  font-size: 14px;
  color: #374151;
  font-weight: 600;
}

.profile-btn {
  width: 100%;
  padding: 10px 16px;
  background: #f3f4f6;
  color: #374151;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s ease;
}

.profile-btn:hover {
  background: #e5e7eb;
  border-color: #667eea;
}

.profile-summary {
  margin-top: 12px;
  padding: 12px;
  background: #f8fafc;
  border-radius: 6px;
  border-left: 3px solid #667eea;
}

.profile-summary p {
  margin: 4px 0;
  font-size: 12px;
  color: #6b7280;
  line-height: 1.4;
}

/* 模态框样式 */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 16px;
  width: 90%;
  max-width: 500px;
  max-height: 80vh;
  overflow-y: auto;
  box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 {
  margin: 0;
  color: #1f2937;
  font-size: 18px;
}

.close-btn {
  background: none;
  border: none;
  font-size: 20px;
  color: #6b7280;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: background 0.2s ease;
}

.close-btn:hover {
  background: #f3f4f6;
  color: #374151;
}

.modal-body {
  padding: 24px;
}

.modal-description {
  margin: 0 0 20px 0;
  color: #6b7280;
  font-size: 14px;
  line-height: 1.6;
}

.profile-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  font-size: 14px;
  font-weight: 500;
  color: #374151;
}

.form-group textarea {
  padding: 12px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 14px;
  font-family: inherit;
  resize: vertical;
  min-height: 80px;
  transition: border-color 0.2s ease;
}

.form-group textarea:focus {
  outline: none;
  border-color: #667eea;
}

.form-group textarea::placeholder {
  color: #9ca3af;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding: 20px 24px;
  border-top: 1px solid #e5e7eb;
  background: #f8fafc;
  border-radius: 0 0 16px 16px;
}

.cancel-btn {
  padding: 10px 20px;
  background: #f3f4f6;
  color: #374151;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s ease;
}

.cancel-btn:hover {
  background: #e5e7eb;
}

.save-btn {
  padding: 10px 20px;
  background: #667eea;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s ease;
}

.save-btn:hover {
  background: #5a67d8;
  transform: translateY(-1px);
}

/* 右侧对话区 */
.conversation-area {
  flex: 1;
  padding: 40px;
  overflow-y: auto;
  max-width: 800px;
  margin: 0 auto;
}

/* AI问候区 */
.ai-greeting {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  margin-bottom: 32px;
}

.ai-avatar {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 50%;
  font-size: 24px;
}

.ai-face {
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
  font-size: 20px;
}

.greeting-text {
  color: #6b7280;
  line-height: 1.6;
  margin: 0;
}

/* 主输入区 */
.main-input-area {
  margin-bottom: 40px;
}

.input-container {
  background: white;
  padding: 24px;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.main-textarea {
  width: 100%;
  border: none;
  resize: none;
  font-size: 16px;
  line-height: 1.6;
  padding: 0;
  outline: none;
  background: transparent;
  color: #1f2937;
  box-sizing: border-box;
}

.main-textarea::placeholder {
  color: #9ca3af;
  line-height: 1.6;
}

.input-tools {
  display: flex;
  gap: 12px;
  margin: 16px 0;
  flex-wrap: wrap;
}

.tool-btn {
  padding: 8px 16px;
  border: 2px solid #e5e7eb;
  background: white;
  border-radius: 8px;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.tool-btn:hover {
  border-color: #667eea;
  background: #f8fafc;
}

.create-btn {
  width: 100%;
  padding: 16px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 18px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.create-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102,126,234,0.3);
}

.create-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* 场景卡片 */
.scenario-grid h3 {
  text-align: center;
  color: #1f2937;
  margin-bottom: 24px;
  font-size: 18px;
}

.scenario-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
  margin-bottom: 40px;
}

.scenario-card {
  background: white;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  text-align: center;
  position: relative;
  transition: all 0.2s ease;
  cursor: pointer;
}

.scenario-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}

.scenario-card.popular {
  border: 2px solid #f59e0b;
}

.card-icon {
  font-size: 40px;
  margin-bottom: 12px;
}

.scenario-card h4 {
  margin: 0 0 8px 0;
  color: #1f2937;
  font-size: 16px;
}

.scenario-card p {
  color: #6b7280;
  font-size: 14px;
  margin: 0 0 16px 0;
  line-height: 1.4;
}

.scenario-btn {
  width: 100%;
  padding: 10px;
  background: #667eea;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.scenario-btn:hover {
  background: #5a67d8;
}

.popular-tag, .new-tag {
  position: absolute;
  top: -8px;
  right: -8px;
  background: #f59e0b;
  color: white;
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 10px;
  font-weight: 600;
}

.new-tag {
  background: #10b981;
}

/* 成功案例 */
.success-examples h3 {
  text-align: center;
  color: #1f2937;
  margin-bottom: 24px;
  font-size: 18px;
}

.examples-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
}

.example-item {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  transition: all 0.2s ease;
  cursor: pointer;
}

.example-item:hover {
  transform: translateY(-2px);
}

.example-item img {
  width: 100%;
  height: 120px;
  object-fit: cover;
  background: #f3f4f6;
}

.example-item p {
  padding: 12px;
  margin: 0;
  color: #374151;
  font-size: 14px;
  text-align: center;
}

/* 响应式设计 */
@media (max-width: 768px) {
  .homepage-container {
    flex-direction: column;
  }
  
  .simple-sidebar {
    width: 100%;
    height: auto;
    padding: 16px;
    order: 2;
  }
  
  .main-nav {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding-bottom: 8px;
  }
  
  .nav-item {
    min-width: 120px;
    margin-bottom: 0;
  }
  
  .conversation-area {
    padding: 20px;
    order: 1;
  }
  
  .scenario-cards {
    grid-template-columns: 1fr;
  }
  
  .examples-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .input-tools {
    flex-direction: column;
  }
  
  .tool-btn {
    width: 100%;
  }
}
</style>
