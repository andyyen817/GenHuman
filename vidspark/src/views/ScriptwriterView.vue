<template>
  <div class="scriptwriter-container">
    <!-- 顶部项目信息 -->
    <header class="script-header">
      <div class="project-info">
        <h1>🎭 AI编剧区</h1>
        <div class="scenario-badge">{{ currentScenarioName }}</div>
      </div>
      <div class="progress-indicator">
        <span class="step active">📝 编剧</span>
        <span class="step-arrow">→</span>
        <span class="step">🎬 导演</span>
        <span class="step-arrow">→</span>
        <span class="step">🎞️ 剪辑</span>
      </div>
      <div class="header-actions">
        <button class="save-btn" @click="saveProject">💾 保存</button>
        <button class="back-btn" @click="goBack">← 返回</button>
      </div>
    </header>

    <!-- Tony编剧问候（全局） -->
    <div class="tony-greeting">
      <div class="tony-avatar">
        <div class="tony-face">👨‍💻</div>
      </div>
      <div class="greeting-bubble">
        <h2>{{ currentGreeting.title }}</h2>
        <p>{{ currentGreeting.content }}</p>
      </div>
    </div>

    <!-- 三栏式主工作区 -->
    <main class="script-workspace">
      <!-- 左栏：剧本编辑 -->
      <aside class="script-panel">
        <div class="panel-header">
          <h3>📝 剧本大纲</h3>
          <div class="panel-tools">
            <button class="tool-btn" @click="handleAIWrite" :disabled="isGenerating">
              🤖 AI写作
            </button>
            <button class="tool-btn" @click="handleTemplate">
              📋 模板
            </button>
          </div>
        </div>
        
        <div class="script-editor">
          <textarea 
            v-model="scriptContent"
            class="script-textarea"
            placeholder="在这里写你的剧本...

例如：
• 第一幕：介绍主题和问题
• 第二幕：展示解决方案  
• 第三幕：总结要点

或者点击'AI写作'让AI帮你创作"
            rows="15"
          />
          
          <!-- AI生成状态 -->
          <div v-if="isGenerating" class="ai-generating">
            <div class="generating-animation">
              <div class="dot"></div>
              <div class="dot"></div>
              <div class="dot"></div>
            </div>
            <p>AI正在创作剧本...</p>
          </div>
        </div>

        <!-- 分镜脚本预览 -->
        <div class="shots-preview">
          <h4>📋 分镜脚本</h4>
          <div class="shots-list">
            <div v-for="(shot, index) in generatedShots" :key="index" class="shot-item">
              <span class="shot-number">{{ index + 1 }}</span>
              <span class="shot-text">{{ shot }}</span>
            </div>
          </div>
        </div>
      </aside>

      <!-- 中栏：角色设计 -->
      <section class="character-panel">
        <div class="panel-header">
          <h3>👤 角色设计</h3>
          <div class="character-status">
            <span v-if="selectedCharacter">✅ 已选择</span>
            <span v-else>⏳ 待设置</span>
          </div>
        </div>
        
        <!-- Tab导航 -->
        <div class="tab-navigation">
          <button 
            class="tab-btn"
            :class="{ active: activeCharacterTab === 'ai' }"
            @click="activeCharacterTab = 'ai'"
          >
            🤖 AI生成
          </button>
          <button 
            class="tab-btn"
            :class="{ active: activeCharacterTab === 'upload' }"
            @click="activeCharacterTab = 'upload'"
          >
            📸 上传照片
          </button>
          <button 
            class="tab-btn"
            :class="{ active: activeCharacterTab === 'library' }"
            @click="activeCharacterTab = 'library'"
          >
            📁 资产库
          </button>
        </div>

        <!-- Tab内容 -->
        <div class="tab-content">
          <!-- AI生成角色 -->
          <div v-if="activeCharacterTab === 'ai'" class="ai-character-tab">
            <div class="character-input">
              <label>📝 角色描述</label>
              <textarea 
                v-model="characterDescription"
                placeholder="描述你想要的角色，例如：
一个戴眼镜的年轻老师，亲切的笑容，穿着整洁的白衬衫..."
                rows="4"
              />
              <button class="generate-btn" @click="generateCharacter" :disabled="isGeneratingCharacter">
                {{ isGeneratingCharacter ? '🎨 生成中...' : '🎨 生成角色' }}
              </button>
            </div>
            
            <!-- 角色三视图 -->
            <div v-if="generatedCharacter" class="character-views">
              <h4>🎭 角色三视图</h4>
              <div class="views-grid">
                <div class="view-item">
                  <img :src="generatedCharacter.front" alt="正面">
                  <span>正面</span>
                </div>
                <div class="view-item">
                  <img :src="generatedCharacter.side" alt="侧面">
                  <span>侧面</span>
                </div>
                <div class="view-item">
                  <img :src="generatedCharacter.back" alt="背面">
                  <span>背面</span>
                </div>
              </div>
              <button class="confirm-btn" @click="confirmCharacter('ai')">
                ✅ 确认使用此角色
              </button>
            </div>
          </div>

          <!-- 上传照片生成数字人 -->
          <div v-if="activeCharacterTab === 'upload'" class="upload-character-tab">
            <div class="upload-zone" @click="triggerPhotoUpload">
              <div v-if="!uploadedPhoto" class="upload-placeholder">
                <div class="upload-icon">📸</div>
                <h4>上传角色照片</h4>
                <p>支持 JPG、PNG 格式，建议正面清晰照片</p>
                <button class="upload-btn">选择照片</button>
              </div>
              <div v-else class="upload-success">
                <img :src="photoPreview" alt="上传的照片" class="photo-preview">
                <h4>{{ uploadedPhoto.name }}</h4>
                <button class="change-btn" @click="changePhoto">更换照片</button>
                <button class="process-btn" @click="processDigitalHuman" :disabled="isProcessingDigitalHuman">
                  {{ isProcessingDigitalHuman ? '🔄 处理中...' : '🔄 生成数字人' }}
                </button>
              </div>
            </div>
            
            <input 
              type="file" 
              ref="photoInput" 
              @change="handlePhotoUpload"
              accept="image/*"
              style="display: none"
            >
          </div>

          <!-- 资产库选择 -->
          <div v-if="activeCharacterTab === 'library'" class="library-character-tab">
            <div class="library-grid">
              <div 
                v-for="character in characterLibrary" 
                :key="character.id"
                class="library-item"
                :class="{ selected: selectedLibraryCharacter === character.id }"
                @click="selectLibraryCharacter(character)"
              >
                <img :src="character.thumbnail" :alt="character.name">
                <div class="character-info">
                  <h5>{{ character.name }}</h5>
                  <p>{{ character.description }}</p>
                </div>
                <div class="character-stats">
                  <span>使用 {{ character.usageCount }} 次</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- 右栏：声音设计 -->
      <aside class="voice-panel">
        <div class="panel-header">
          <h3>🎤 声音设计</h3>
          <div class="voice-status">
            <span v-if="selectedVoice">✅ 已选择</span>
            <span v-else>⏳ 待设置</span>
          </div>
        </div>

        <!-- 声音选择Tab -->
        <div class="voice-tabs">
          <button 
            class="tab-btn"
            :class="{ active: activeVoiceTab === 'ai' }"
            @click="activeVoiceTab = 'ai'"
          >
            🎙️ AI合成
          </button>
          <button 
            class="tab-btn"
            :class="{ active: activeVoiceTab === 'clone' }"
            @click="activeVoiceTab = 'clone'"
          >
            🔮 声音克隆
          </button>
          <button 
            class="tab-btn"
            :class="{ active: activeVoiceTab === 'library' }"
            @click="activeVoiceTab = 'library'"
          >
            📚 克隆库
          </button>
        </div>

        <!-- 声音Tab内容 -->
        <div class="voice-content">
          <!-- AI合成语音 -->
          <div v-if="activeVoiceTab === 'ai'" class="ai-voice-tab">
            <div class="voice-options">
              <div 
                v-for="voice in aiVoices" 
                :key="voice.id"
                class="voice-option"
                :class="{ selected: selectedAIVoice === voice.id }"
                @click="selectAIVoice(voice)"
              >
                <div class="voice-info">
                  <h5>{{ voice.name }}</h5>
                  <p>{{ voice.description }}</p>
                  <div class="voice-tags">
                    <span v-for="tag in voice.tags" :key="tag" class="tag">{{ tag }}</span>
                  </div>
                </div>
                <button class="play-btn" @click.stop="playVoicePreview(voice)">
                  ▶️
                </button>
              </div>
            </div>
          </div>

          <!-- 声音克隆 -->
          <div v-if="activeVoiceTab === 'clone'" class="clone-voice-tab">
            <div class="clone-input">
              <h4>🎤 录制或上传音频</h4>
              <div class="audio-upload">
                <button class="record-btn" @click="startRecording" :disabled="isRecording">
                  {{ isRecording ? '🔴 录制中...' : '🎤 开始录制' }}
                </button>
                <span class="or-divider">或</span>
                <button class="upload-audio-btn" @click="triggerAudioUpload">
                  📁 上传音频
                </button>
              </div>
              
              <div v-if="recordedAudio || uploadedAudio" class="audio-preview">
                <audio :src="audioPreviewUrl" controls></audio>
                <button class="clone-btn" @click="cloneVoice" :disabled="isCloningVoice">
                  {{ isCloningVoice ? '🔄 克隆中...' : '🔮 开始克隆' }}
                </button>
              </div>
            </div>
            
            <input 
              type="file" 
              ref="audioInput" 
              @change="handleAudioUpload"
              accept="audio/*"
              style="display: none"
            >
          </div>

          <!-- 克隆库 -->
          <div v-if="activeVoiceTab === 'library'" class="voice-library-tab">
            <div class="voice-library">
              <div 
                v-for="voice in voiceLibrary" 
                :key="voice.id"
                class="voice-library-item"
                :class="{ selected: selectedLibraryVoice === voice.id }"
                @click="selectLibraryVoice(voice)"
              >
                <div class="voice-info">
                  <h5>{{ voice.name }}</h5>
                  <p>创建于 {{ voice.createdAt }}</p>
                  <div class="quality-rating">
                    <span>质量: </span>
                    <div class="stars">
                      <span v-for="i in 5" :key="i" :class="{ filled: i <= voice.quality }">⭐</span>
                    </div>
                  </div>
                </div>
                <button class="play-btn" @click.stop="playVoicePreview(voice)">
                  ▶️
                </button>
              </div>
            </div>
          </div>
        </div>
      </aside>
    </main>

    <!-- 底部确认区 -->
    <footer class="script-footer">
      <div class="completion-status">
        <div class="status-item" :class="{ completed: scriptContent.trim() }">
          <span class="status-icon">{{ scriptContent.trim() ? '✅' : '⏳' }}</span>
          <span>剧本</span>
        </div>
        <div class="status-item" :class="{ completed: selectedCharacter }">
          <span class="status-icon">{{ selectedCharacter ? '✅' : '⏳' }}</span>
          <span>角色</span>
        </div>
        <div class="status-item" :class="{ completed: selectedVoice }">
          <span class="status-icon">{{ selectedVoice ? '✅' : '⏳' }}</span>
          <span>声音</span>
        </div>
      </div>
      
      <div class="footer-actions">
        <div class="credits-info">
          <span>预估消耗: {{ estimatedCredits }} 积分</span>
        </div>
        <button 
          class="director-btn" 
          @click="goToDirector"
          :disabled="!canProceed"
        >
          进入导演环节 🎬
        </button>
      </div>
    </footer>

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

// 角色设计状态
const activeCharacterTab = ref('ai')
const characterDescription = ref('')
const isGeneratingCharacter = ref(false)
const generatedCharacter = ref(null)
const selectedCharacter = ref(null)
const uploadedPhoto = ref(null)
const photoPreview = ref('')
const isProcessingDigitalHuman = ref(false)
const selectedLibraryCharacter = ref(null)

// 声音设计状态
const activeVoiceTab = ref('ai')
const selectedVoice = ref(null)
const selectedAIVoice = ref(null)
const isRecording = ref(false)
const recordedAudio = ref(null)
const uploadedAudio = ref(null)
const audioPreviewUrl = ref('')
const isCloningVoice = ref(false)
const selectedLibraryVoice = ref(null)

// 文件输入引用
const photoInput = ref<HTMLInputElement>()
const audioInput = ref<HTMLInputElement>()

// 场景名称映射
const scenarioNames = {
  fromScratch: '從零創作影片',
  remake: '重做現有影片', 
  imitate: '模仿爆款影片',
  digitalHuman: '快速製作數字人',
  pptVideo: '製作PPT影片',
  videoClip: '快速切片'
}

// 计算当前场景名称
const currentScenarioName = computed(() => {
  return scenarioNames[scenario.value] || '未知场景'
})

// Tony编剧的不同问候语
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
  default: {
    title: '📝 AI编剧助手',
    content: '我是你的专属编剧Tony，帮你创作出色的剧本，设计角色和配音'
  }
}

// 计算当前问候语
const currentGreeting = computed(() => {
  return greetings[scenario.value] || greetings.default
})

// 生成的分镜脚本
const generatedShots = computed(() => {
  if (!scriptContent.value.trim()) return []
  return scriptContent.value.split('\n').filter(line => line.trim().startsWith('第'))
})

// 积分预估
const estimatedCredits = computed(() => {
  let credits = 0
  if (scriptContent.value.trim()) credits += 1
  if (selectedCharacter.value) credits += 3
  if (selectedVoice.value) credits += 2
  return credits
})

// 是否可以进入下一步
const canProceed = computed(() => {
  return scriptContent.value.trim() && selectedCharacter.value && selectedVoice.value
})

// AI语音选项
const aiVoices = reactive([
  {
    id: 'ai_voice_1',
    name: '温和女声',
    description: '亲和力强，适合教学类内容',
    tags: ['女声', '温和', '教学']
  },
  {
    id: 'ai_voice_2', 
    name: '活力男声',
    description: '充满活力，适合产品介绍',
    tags: ['男声', '活力', '商务']
  },
  {
    id: 'ai_voice_3',
    name: '专业播音',
    description: '标准普通话，适合正式场合',
    tags: ['中性', '专业', '播音']
  }
])

// 角色库
const characterLibrary = reactive([
  {
    id: 'char_1',
    name: '年轻老师',
    description: '戴眼镜的年轻女教师',
    thumbnail: '/api/placeholder/120/120',
    usageCount: 5
  },
  {
    id: 'char_2',
    name: '商务人士',
    description: '专业的商务男性',
    thumbnail: '/api/placeholder/120/120', 
    usageCount: 3
  }
])

// 声音库
const voiceLibrary = reactive([
  {
    id: 'voice_1',
    name: '我的声音',
    createdAt: '2025-09-01',
    quality: 4
  },
  {
    id: 'voice_2',
    name: '客户录音',
    createdAt: '2025-08-28',
    quality: 5
  }
])

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
  }
])

// 处理AI写作
const handleAIWrite = async () => {
  if (isGenerating.value) return
  
  isGenerating.value = true
  console.log('🤖 [AI写作] 开始生成剧本')
  
  try {
    await new Promise(resolve => setTimeout(resolve, 3000))
    
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

// 处理模板选择
const handleTemplate = () => {
  showTemplateModal.value = true
}

// 选择模板
const selectTemplate = (template: any) => {
  scriptContent.value = template.content
  showTemplateModal.value = false
}

// 生成角色
const generateCharacter = async () => {
  if (!characterDescription.value.trim()) return
  
  isGeneratingCharacter.value = true
  try {
    await new Promise(resolve => setTimeout(resolve, 2000))
    generatedCharacter.value = {
      front: '/api/placeholder/150/200',
      side: '/api/placeholder/150/200', 
      back: '/api/placeholder/150/200'
    }
  } finally {
    isGeneratingCharacter.value = false
  }
}

// 确认角色
const confirmCharacter = (type: string) => {
  selectedCharacter.value = { type, data: generatedCharacter.value }
}

// 上传照片
const triggerPhotoUpload = () => {
  if (photoInput.value) {
    photoInput.value.click()
  }
}

const handlePhotoUpload = (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  
  if (file) {
    uploadedPhoto.value = file
    photoPreview.value = URL.createObjectURL(file)
  }
}

// 选择AI语音
const selectAIVoice = (voice: any) => {
  selectedAIVoice.value = voice.id
  selectedVoice.value = voice
}

// 播放语音预览
const playVoicePreview = (voice: any) => {
  console.log('🔊 播放语音预览:', voice.name)
}

// 保存项目
const saveProject = () => {
  console.log('💾 保存项目')
}

// 返回首页
const goBack = () => {
  router.push('/')
}

// 进入导演区
const goToDirector = () => {
  if (!canProceed.value) {
    alert('请完成剧本、角色和声音设置后再进入下一步！')
    return
  }
  
  router.push('/director')
}

// 其他函数的实现（简化）
const changePhoto = () => { uploadedPhoto.value = null; photoPreview.value = '' }
const processDigitalHuman = () => { console.log('处理数字人') }
const selectLibraryCharacter = (char: any) => { selectedLibraryCharacter.value = char.id }
const startRecording = () => { console.log('开始录制') }
const triggerAudioUpload = () => { if (audioInput.value) audioInput.value.click() }
const handleAudioUpload = () => { console.log('处理音频上传') }
const cloneVoice = () => { console.log('克隆声音') }
const selectLibraryVoice = (voice: any) => { selectedLibraryVoice.value = voice.id; selectedVoice.value = voice }
</script>

<style scoped>
.scriptwriter-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  display: flex;
  flex-direction: column;
}

/* 顶部项目信息 */
.script-header {
  background: white;
  padding: 20px 32px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 16px;
}

.project-info {
  display: flex;
  align-items: center;
  gap: 16px;
}

.project-info h1 {
  margin: 0;
  color: #1f2937;
  font-size: 24px;
}

.scenario-badge {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
}

.progress-indicator {
  display: flex;
  align-items: center;
  gap: 8px;
}

.step {
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
}

.step.active {
  background: #10b981;
  color: white;
}

.step-arrow {
  color: #9ca3af;
}

.header-actions {
  display: flex;
  gap: 12px;
}

.save-btn, .back-btn {
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s ease;
}

.save-btn {
  background: #10b981;
  color: white;
}

.back-btn {
  background: #f3f4f6;
  color: #374151;
}

/* Tony编剧问候 */
.tony-greeting {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  margin: 24px 32px;
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

/* 三栏式工作区 */
.script-workspace {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 24px;
  padding: 0 32px 32px;
  flex: 1;
}

/* 面板通用样式 */
.script-panel, .character-panel, .voice-panel {
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  overflow: hidden;
}

.panel-header {
  padding: 20px;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.panel-header h3 {
  margin: 0;
  color: #1f2937;
  font-size: 16px;
  font-weight: 600;
}

.panel-tools {
  display: flex;
  gap: 8px;
}

.tool-btn {
  padding: 6px 12px;
  background: #f3f4f6;
  color: #374151;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 12px;
  transition: all 0.2s ease;
}

.tool-btn:hover {
  background: #e5e7eb;
}

/* 剧本编辑面板 */
.script-editor {
  padding: 20px;
}

.script-textarea {
  width: 100%;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  padding: 16px;
  font-size: 14px;
  line-height: 1.6;
  resize: vertical;
  min-height: 200px;
  outline: none;
  transition: border-color 0.2s ease;
  box-sizing: border-box;
}

.script-textarea:focus {
  border-color: #10b981;
}

.ai-generating {
  text-align: center;
  padding: 16px;
  background: #f0fdf4;
  border-radius: 8px;
  margin-top: 16px;
}

.generating-animation {
  display: flex;
  justify-content: center;
  gap: 4px;
  margin-bottom: 8px;
}

.dot {
  width: 6px;
  height: 6px;
  background: #10b981;
  border-radius: 50%;
  animation: pulse 1.5s infinite;
}

.dot:nth-child(2) { animation-delay: 0.2s; }
.dot:nth-child(3) { animation-delay: 0.4s; }

@keyframes pulse {
  0%, 80%, 100% { transform: scale(1); opacity: 0.5; }
  40% { transform: scale(1.2); opacity: 1; }
}

.shots-preview {
  padding: 20px;
  border-top: 1px solid #e5e7eb;
}

.shots-preview h4 {
  margin: 0 0 12px 0;
  color: #1f2937;
  font-size: 14px;
}

.shots-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.shot-item {
  display: flex;
  gap: 8px;
  padding: 8px;
  background: #f8fafc;
  border-radius: 6px;
  font-size: 12px;
}

.shot-number {
  background: #10b981;
  color: white;
  padding: 2px 6px;
  border-radius: 4px;
  font-weight: 500;
  min-width: 20px;
  text-align: center;
}

.shot-text {
  color: #6b7280;
  line-height: 1.4;
}

/* Tab导航 */
.tab-navigation, .voice-tabs {
  display: flex;
  border-bottom: 1px solid #e5e7eb;
}

.tab-btn {
  flex: 1;
  padding: 12px;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 12px;
  color: #6b7280;
  transition: all 0.2s ease;
}

.tab-btn.active {
  background: #f0fdf4;
  color: #10b981;
  border-bottom: 2px solid #10b981;
}

.tab-content, .voice-content {
  padding: 20px;
}

/* 角色设计 */
.character-input {
  margin-bottom: 20px;
}

.character-input label {
  display: block;
  margin-bottom: 8px;
  color: #374151;
  font-size: 14px;
  font-weight: 500;
}

.character-input textarea {
  width: 100%;
  padding: 12px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 14px;
  line-height: 1.5;
  resize: vertical;
  outline: none;
  box-sizing: border-box;
}

.character-input textarea:focus {
  border-color: #10b981;
}

.generate-btn {
  width: 100%;
  padding: 12px;
  background: #10b981;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
  margin-top: 12px;
}

.character-views h4 {
  margin: 0 0 16px 0;
  color: #1f2937;
  font-size: 14px;
}

.views-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
  margin-bottom: 16px;
}

.view-item {
  text-align: center;
}

.view-item img {
  width: 100%;
  height: 120px;
  object-fit: cover;
  border-radius: 8px;
  border: 2px solid #e5e7eb;
}

.view-item span {
  display: block;
  margin-top: 4px;
  font-size: 12px;
  color: #6b7280;
}

.confirm-btn {
  width: 100%;
  padding: 12px;
  background: #059669;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
}

/* 上传区域 */
.upload-zone {
  border: 2px dashed #d1d5db;
  border-radius: 8px;
  padding: 20px;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s ease;
}

.upload-zone:hover {
  border-color: #10b981;
  background: #f0fdf4;
}

.upload-icon {
  font-size: 32px;
  margin-bottom: 12px;
}

.upload-placeholder h4 {
  margin: 0 0 8px 0;
  color: #374151;
  font-size: 16px;
}

.upload-placeholder p {
  margin: 0 0 16px 0;
  color: #6b7280;
  font-size: 14px;
}

.upload-btn {
  padding: 8px 16px;
  background: #10b981;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
}

.photo-preview {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: 8px;
  margin-bottom: 12px;
}

/* 资产库 */
.library-grid {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.library-item {
  display: flex;
  gap: 12px;
  padding: 12px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.library-item:hover {
  border-color: #10b981;
  background: #f0fdf4;
}

.library-item.selected {
  border-color: #10b981;
  background: #f0fdf4;
}

.library-item img {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 6px;
}

.character-info h5 {
  margin: 0 0 4px 0;
  color: #1f2937;
  font-size: 14px;
}

.character-info p {
  margin: 0 0 8px 0;
  color: #6b7280;
  font-size: 12px;
}

.character-stats {
  font-size: 11px;
  color: #9ca3af;
}

/* 声音设计 */
.voice-option {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  margin-bottom: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.voice-option:hover, .voice-option.selected {
  border-color: #10b981;
  background: #f0fdf4;
}

.voice-info h5 {
  margin: 0 0 4px 0;
  color: #1f2937;
  font-size: 14px;
}

.voice-info p {
  margin: 0 0 8px 0;
  color: #6b7280;
  font-size: 12px;
}

.voice-tags {
  display: flex;
  gap: 4px;
  flex-wrap: wrap;
}

.tag {
  background: #f3f4f6;
  color: #6b7280;
  padding: 2px 6px;
  border-radius: 10px;
  font-size: 10px;
}

.play-btn {
  background: #10b981;
  color: white;
  border: none;
  border-radius: 50%;
  width: 32px;
  height: 32px;
  cursor: pointer;
  font-size: 12px;
}

/* 底部确认区 */
.script-footer {
  background: white;
  padding: 20px 32px;
  box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.completion-status {
  display: flex;
  gap: 24px;
}

.status-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  border-radius: 8px;
  background: #f3f4f6;
  transition: all 0.2s ease;
}

.status-item.completed {
  background: #f0fdf4;
  color: #059669;
}

.footer-actions {
  display: flex;
  align-items: center;
  gap: 16px;
}

.credits-info {
  color: #6b7280;
  font-size: 14px;
}

.director-btn {
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

.director-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102,126,234,0.3);
}

.director-btn:disabled {
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
  border-color: #10b981;
  background: #f0fdf4;
}

.template-icon {
  font-size: 32px;
  margin-bottom: 12px;
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

/* 响应式设计 */
@media (max-width: 1024px) {
  .script-workspace {
    grid-template-columns: 1fr;
    gap: 16px;
  }
  
  .script-header {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .tony-greeting {
    margin: 16px;
  }
  
  .script-footer {
    flex-direction: column;
    gap: 16px;
    align-items: stretch;
  }
}
</style>