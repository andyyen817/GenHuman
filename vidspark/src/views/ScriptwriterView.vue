<template>
  <div class="scriptwriter-container">
    <!-- 顶部进度条（超简化） -->
    <div class="simple-progress">
      <div class="progress-steps">
        <div class="step active">
          <span class="step-icon">📝</span>
          <span class="step-text">第1步：写剧本</span>
        </div>
        <div class="step-arrow">→</div>
        <div class="step">
          <span class="step-icon">👤</span>
          <span class="step-text">第2步：选人物</span>
        </div>
        <div class="step-arrow">→</div>
        <div class="step">
          <span class="step-icon">🎤</span>
          <span class="step-text">第3步：配声音</span>
        </div>
      </div>
      <div class="current-indicator">当前：📝 写剧本</div>
    </div>

    <!-- 主要工作区（一栏式布局） -->
    <main class="scriptwriter-simple">
      <!-- Tony编剧问候（根据场景动态变化） -->
      <div class="tony-greeting">
        <div class="tony-avatar">
          <div class="tony-face">👨‍💻</div>
        </div>
        <div class="greeting-bubble">
          <h2>{{ currentGreeting.title }}</h2>
          <p>{{ currentGreeting.content }}</p>
        </div>
      </div>
      
      <!-- 大型写作区 -->
      <div class="writing-area">
        <textarea 
          v-model="scriptContent"
          class="script-textarea"
          placeholder="在这里写你的剧本...

例如：
• 第一幕：小明走进教室，发现桌上有一本神奇的书
• 第二幕：书本开始发光，带小明进入了奇幻世界  
• 第三幕：小明学会了时间管理，回到现实世界

你也可以说：'帮我写一个关于时间管理的故事'"
          rows="12"
        />
        
        <div class="writing-tools">
          <button class="big-btn" @click="handleAIWrite" :disabled="isGenerating">
            <span v-if="isGenerating">🤖 AI正在思考...</span>
            <span v-else>🤖 让AI帮我写</span>
          </button>
          <button class="big-btn" @click="handleSelfWrite">
            ✏️ 我自己写
          </button>
          <button class="big-btn" @click="handleTemplate">
            📋 使用模板
          </button>
        </div>

        <!-- AI生成状态提示 -->
        <div v-if="isGenerating" class="ai-generating">
          <div class="generating-animation">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
          </div>
          <p>AI正在为你创作剧本，请稍等...</p>
        </div>
      </div>
      
      <!-- 底部操作 -->
      <div class="step-footer">
        <button class="back-btn" @click="goBack">
          ← 返回首页
        </button>
        <button class="next-btn" @click="goToCharacterDesign" :disabled="!scriptContent.trim()">
          下一步：选择人物 →
        </button>
      </div>
    </main>

    <!-- 模板选择弹窗 -->
    <div v-if="showTemplateModal" class="template-modal" @click.self="showTemplateModal = false">
      <div class="modal-content">
        <h3>📋 选择剧本模板</h3>
        <div class="template-grid">
          <div 
            v-for="template in scriptTemplates" 
            :key="template.id"
            class="template-card"
            @click="selectTemplate(template)"
          >
            <div class="template-icon">{{ template.icon }}</div>
            <h4>{{ template.title }}</h4>
            <p>{{ template.description }}</p>
          </div>
        </div>
        <button class="close-btn" @click="showTemplateModal = false">关闭</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

// 获取场景参数
const scenario = ref(route.query.scenario as string || 'fromScratch')

// 状态管理
const scriptContent = ref('')
const isGenerating = ref(false)
const showTemplateModal = ref(false)

// Tony编剧的不同问候语（场景4、5、6专用）
const greetings = {
  digitalHuman: {
    title: '你好，你選擇了快速製作數字人',
    content: '我是你的Vidspark AI專屬編劇 Tony，請你上傳你現有的文案，我來幫你把文案分鏡到數字人影片'
  },
  pptVideo: {
    title: '你好，你選擇了製作PPT影片',
    content: '我是你的Vidspark AI專屬編劇 Tony，請你上傳你現有的PPT，我來根據PPT，幫你設計PPT每一頁文案'
  },
  videoClip: {
    title: '你好，你選擇了快速切片',
    content: '我是你的Vidspark AI專屬編劇 Tony，請你上傳你想切片的影片，影片需要有音頻，我來幫你切出精采片段'
  },
  // 默认（场景1、2、3或其他）
  default: {
    title: '📝 第一步：写剧本',
    content: '告诉AI你想讲什么故事，或者让AI帮你写'
  }
}

// 计算当前问候语
const currentGreeting = computed(() => {
  return greetings[scenario.value] || greetings.default
})

// 剧本模板
const scriptTemplates = reactive([
  {
    id: 1,
    icon: '📚',
    title: '教学介绍',
    description: '适合介绍知识、技能、产品的教学视频',
    content: `第一幕：我是[你的名字]，今天要和大家分享[主题]

第二幕：首先我们来了解一下[核心概念]，这个很重要因为[原因]

第三幕：接下来我来演示[具体步骤]，大家跟着我一起做

第四幕：总结一下，我们学会了[要点总结]，希望对大家有帮助`
  },
  {
    id: 2,
    icon: '💡',
    title: '故事叙述',
    description: '用故事的方式传达道理或经验',
    content: `第一幕：从前有一个[人物]，他遇到了[问题]

第二幕：为了解决这个问题，他尝试了[方法]，但是[遇到困难]

第三幕：后来他发现了[关键方法]，通过[具体行动]解决了问题

第四幕：这个故事告诉我们[道理]，在生活中我们也可以[应用]`
  },
  {
    id: 3,
    icon: '🎯',
    title: '产品介绍',
    description: '专业介绍产品特点和优势',
    content: `第一幕：大家好，今天给大家介绍[产品名称]

第二幕：这个产品最大的特点是[核心特色]，可以帮你[解决问题]

第三幕：具体的使用方法很简单，只需要[操作步骤]

第四幕：相比其他产品，我们的优势是[优势对比]，欢迎大家试用`
  }
])

// 处理AI写作
const handleAIWrite = async () => {
  if (isGenerating.value) return
  
  isGenerating.value = true
  console.log('🤖 [AI写作] 开始生成剧本')
  
  try {
    // 模拟AI生成过程
    await new Promise(resolve => setTimeout(resolve, 3000))
    
    // 生成示例剧本
    scriptContent.value = `第一幕：大家好，我是时间管理小助手，今天要和大家分享几个超实用的时间管理技巧

第二幕：首先是番茄工作法，把工作分成25分钟的小块，这样能保持专注力

第三幕：接下来是优先级排序，把重要紧急的事情放在第一位，避免被琐事干扰

第四幕：最后要记住，时间管理不是为了忙碌，而是为了有时间做真正重要的事情

第五幕：希望这些方法对大家有帮助，记得点赞关注哦！`

    console.log('✅ [AI写作] 剧本生成完成')
  } catch (error) {
    console.error('❌ [AI写作] 生成失败:', error)
    alert('AI生成失败，请重试')
  } finally {
    isGenerating.value = false
  }
}

// 处理自己写作
const handleSelfWrite = () => {
  console.log('✏️ [自己写作] 用户选择自己写')
  // 聚焦到文本框
  const textarea = document.querySelector('.script-textarea') as HTMLTextAreaElement
  if (textarea) {
    textarea.focus()
  }
}

// 处理模板选择
const handleTemplate = () => {
  console.log('📋 [模板选择] 显示模板弹窗')
  showTemplateModal.value = true
}

// 选择模板
const selectTemplate = (template: any) => {
  console.log('📋 [模板选择] 选择模板:', template.title)
  scriptContent.value = template.content
  showTemplateModal.value = false
}

// 返回首页
const goBack = () => {
  console.log('← [返回] 回到首页')
  router.push('/')
}

// 进入角色设计
const goToCharacterDesign = () => {
  if (!scriptContent.value.trim()) {
    alert('请先写好剧本再进入下一步！')
    return
  }
  
  console.log('→ [下一步] 进入角色设计')
  // TODO: 保存剧本到状态管理
  router.push('/director')
}
</script>

<style scoped>
.scriptwriter-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

/* 顶部进度条 */
.simple-progress {
  background: white;
  padding: 20px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  margin-bottom: 32px;
}

.progress-steps {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 12px;
}

.step {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  padding: 12px;
  border-radius: 8px;
  transition: all 0.2s ease;
}

.step.active {
  background: #667eea;
  color: white;
}

.step-icon {
  font-size: 24px;
}

.step-text {
  font-size: 14px;
  font-weight: 500;
}

.step-arrow {
  font-size: 20px;
  color: #9ca3af;
  margin: 0 16px;
}

.current-indicator {
  text-align: center;
  color: #667eea;
  font-weight: 600;
  font-size: 16px;
}

/* 主工作区 */
.scriptwriter-simple {
  max-width: 800px;
  margin: 0 auto;
  padding: 0 20px;
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

.greeting-bubble p {
  color: #6b7280;
  line-height: 1.6;
  margin: 0;
  font-size: 14px;
}

.current-step {
  text-align: center;
  margin-bottom: 32px;
}

.current-step h2 {
  font-size: 28px;
  color: #1f2937;
  margin: 0 0 12px 0;
}

.current-step p {
  font-size: 16px;
  color: #6b7280;
  margin: 0;
}

/* 写作区 */
.writing-area {
  background: white;
  padding: 32px;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  margin-bottom: 32px;
}

.script-textarea {
  width: 100%;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  padding: 20px;
  font-size: 16px;
  line-height: 1.8;
  resize: vertical;
  min-height: 300px;
  outline: none;
  transition: border-color 0.2s ease;
  box-sizing: border-box;
}

.script-textarea:focus {
  border-color: #667eea;
}

.script-textarea::placeholder {
  color: #9ca3af;
  line-height: 1.6;
}

.writing-tools {
  display: flex;
  gap: 16px;
  margin-top: 24px;
  justify-content: center;
  flex-wrap: wrap;
}

.big-btn {
  padding: 12px 24px;
  font-size: 16px;
  background: white;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  min-width: 140px;
}

.big-btn:hover:not(:disabled) {
  border-color: #667eea;
  background: #f8fafc;
}

.big-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* AI生成状态 */
.ai-generating {
  text-align: center;
  margin-top: 24px;
  padding: 20px;
  background: #f8fafc;
  border-radius: 8px;
}

.generating-animation {
  display: flex;
  justify-content: center;
  gap: 4px;
  margin-bottom: 12px;
}

.dot {
  width: 8px;
  height: 8px;
  background: #667eea;
  border-radius: 50%;
  animation: pulse 1.5s infinite;
}

.dot:nth-child(2) {
  animation-delay: 0.2s;
}

.dot:nth-child(3) {
  animation-delay: 0.4s;
}

@keyframes pulse {
  0%, 80%, 100% {
    transform: scale(1);
    opacity: 0.5;
  }
  40% {
    transform: scale(1.2);
    opacity: 1;
  }
}

/* 底部操作 */
.step-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 40px;
}

.back-btn {
  padding: 12px 24px;
  background: #f3f4f6;
  color: #374151;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 16px;
  transition: all 0.2s ease;
}

.back-btn:hover {
  background: #e5e7eb;
}

.next-btn {
  padding: 12px 24px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 16px;
  font-weight: 500;
  transition: all 0.2s ease;
}

.next-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102,126,234,0.3);
}

.next-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* 模板弹窗 */
.template-modal {
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
  padding: 32px;
  border-radius: 16px;
  max-width: 600px;
  width: 90%;
  max-height: 80vh;
  overflow-y: auto;
}

.modal-content h3 {
  text-align: center;
  margin: 0 0 24px 0;
  color: #1f2937;
  font-size: 20px;
}

.template-grid {
  display: grid;
  gap: 16px;
  margin-bottom: 24px;
}

.template-card {
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  padding: 20px;
  cursor: pointer;
  transition: all 0.2s ease;
  text-align: center;
}

.template-card:hover {
  border-color: #667eea;
  background: #f8fafc;
}

.template-icon {
  font-size: 32px;
  margin-bottom: 12px;
}

.template-card h4 {
  margin: 0 0 8px 0;
  color: #1f2937;
}

.template-card p {
  margin: 0;
  color: #6b7280;
  font-size: 14px;
}

.close-btn {
  width: 100%;
  padding: 12px;
  background: #f3f4f6;
  color: #374151;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 16px;
}

.close-btn:hover {
  background: #e5e7eb;
}

/* 响应式设计 */
@media (max-width: 768px) {
  .progress-steps {
    flex-direction: column;
    gap: 12px;
  }
  
  .step-arrow {
    transform: rotate(90deg);
    margin: 8px 0;
  }
  
  .scriptwriter-simple {
    padding: 0 16px;
  }
  
  .writing-tools {
    flex-direction: column;
  }
  
  .big-btn {
    width: 100%;
  }
  
  .step-footer {
    flex-direction: column;
    gap: 16px;
  }
  
  .back-btn, .next-btn {
    width: 100%;
  }
}
</style>
