<template>
  <div class="container">
    <!-- 顶部导航 -->
    <header class="header">
      <div class="logo">🎬 Vidspark</div>
      <div class="progress-flow">
        <span class="flow-step completed">📝 编剧</span>
        <span class="flow-arrow">→</span>
        <span class="flow-step active">🎤 声音分镜</span>
        <span class="flow-arrow">→</span>
        <span class="flow-step pending">🎬 图像分镜</span>
        <span class="flow-arrow">→</span>
        <span class="flow-step pending">🎞️ 剪辑</span>
      </div>
      <div style="color: #6b7280; font-size: 14px;">
        项目：{{ projectName }}
      </div>
    </header>

    <!-- 主标题区 -->
    <section class="main-header">
      <h1 class="main-title">🎤 声音分镜制作</h1>
      <p class="main-subtitle">为每个分镜配制专属语音，让你的故事更加生动</p>
      <div class="stats-bar">
        <div class="stat-item">
          <div class="stat-number">{{ totalShots }}</div>
          <div class="stat-label">总分镜数</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">{{ completedShots }}</div>
          <div class="stat-label">已完成</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">{{ pendingShots }}</div>
          <div class="stat-label">待处理</div>
        </div>
      </div>
    </section>

    <!-- 全局操作栏 -->
    <section class="global-actions">
      <button class="mega-button" @click="generateAllVoices" :disabled="isGenerating">
        {{ isGenerating ? '⏳ 生成中...' : '🎤 一键生成所有语音' }}
      </button>
      <div class="global-settings">
        <div class="setting-group">
          <label>默认音色：</label>
          <select class="setting-selector" v-model="defaultVoice">
            <option value="alex">Alex - 男声</option>
            <option value="sophie">Sophie - 女声</option>
            <option value="michael">Michael - 磁性男声</option>
            <option value="emma">Emma - 甜美女声</option>
          </select>
        </div>
        <div class="setting-group">
          <label>语速：</label>
          <select class="setting-selector" v-model="defaultSpeed">
            <option value="slow">慢速</option>
            <option value="normal">正常</option>
            <option value="fast">快速</option>
          </select>
        </div>
        <div class="setting-group">
          <label>音量：</label>
          <select class="setting-selector" v-model="defaultVolume">
            <option value="quiet">安静</option>
            <option value="normal">正常</option>
            <option value="loud">响亮</option>
          </select>
        </div>
      </div>
    </section>

    <!-- 声音分镜网格 -->
    <main class="shots-grid">
      <div
        v-for="(shot, index) in voiceShots"
        :key="shot.id"
        :class="['voice-shot-card', shot.status]"
      >
        <div class="card-header">
          <h3 class="shot-title">🎤 第{{ index + 1 }}幕</h3>
          <span :class="['status-badge', shot.status]">
            {{ getStatusText(shot.status) }}
          </span>
        </div>

        <!-- 脚本文本 -->
        <div class="script-text">
          <label class="script-label">分镜文本：</label>
          <textarea
            v-model="shot.text"
            class="script-input"
            :placeholder="`请输入第${index + 1}幕的配音文本...`"
            @input="onTextChange(shot)"
          ></textarea>
        </div>

        <!-- 语音选择 -->
        <div class="voice-selection">
          <label class="voice-label">选择声音：</label>
          <div class="voice-options">
            <div
              v-for="voice in availableVoices"
              :key="voice.id"
              :class="['voice-option', { selected: shot.selectedVoice === voice.id }]"
              @click="selectVoice(shot, voice.id)"
            >
              <div class="voice-avatar">{{ voice.avatar }}</div>
              <div class="voice-info">
                <div class="voice-name">{{ voice.name }}</div>
                <div class="voice-desc">{{ voice.description }}</div>
              </div>
              <button
                v-if="voice.id !== 'custom'"
                class="voice-preview-btn"
                @click.stop="previewVoice(voice.id)"
              >
                🔊
              </button>
            </div>
          </div>
        </div>

        <!-- 语音克隆区域 -->
        <div v-if="shot.selectedVoice === 'custom'" class="voice-clone-section">
          <div class="clone-upload">
            <input
              type="file"
              :id="`voice-upload-${shot.id}`"
              accept="audio/*"
              @change="uploadVoiceFile(shot, $event)"
              style="display: none;"
            >
            <label
              :for="`voice-upload-${shot.id}`"
              class="upload-btn"
              :class="{ 'has-file': shot.voiceFile }"
            >
              <i class="fas fa-microphone"></i>
              {{ shot.voiceFile ? shot.voiceFile.name : '上传语音样本' }}
            </label>
            <p class="upload-hint">
              支持MP3、WAV格式，建议30秒以上的清晰录音
            </p>
          </div>
        </div>

        <!-- 语音设置 -->
        <div class="voice-settings">
          <div class="setting-row">
            <label>语速：</label>
            <select v-model="shot.speed" class="mini-select">
              <option value="slow">慢速</option>
              <option value="normal">正常</option>
              <option value="fast">快速</option>
            </select>
          </div>
          <div class="setting-row">
            <label>音量：</label>
            <select v-model="shot.volume" class="mini-select">
              <option value="quiet">安静</option>
              <option value="normal">正常</option>
              <option value="loud">响亮</option>
            </select>
          </div>
          <div class="setting-row">
            <label>音调：</label>
            <select v-model="shot.pitch" class="mini-select">
              <option value="low">低沉</option>
              <option value="normal">正常</option>
              <option value="high">高亢</option>
            </select>
          </div>
        </div>

        <!-- 音频播放器 -->
        <div v-if="shot.audioUrl" class="audio-player">
          <div class="player-info">
            <i class="fas fa-volume-up"></i>
            <span>生成的语音 ({{ shot.duration || '0:08' }})</span>
          </div>
          <div class="player-controls">
            <button
              class="play-btn"
              @click="togglePlay(shot)"
            >
              {{ shot.isPlaying ? '⏸️' : '▶️' }}
            </button>
            <div class="audio-waveform">
              <div class="waveform-bar" v-for="i in 20" :key="i"></div>
            </div>
            <button class="retry-btn" @click="regenerateVoice(shot)">
              🔄 重新生成
            </button>
          </div>
        </div>

        <!-- 操作按钮 -->
        <div class="card-actions">
          <button
            class="action-btn secondary"
            @click="previewShot(shot)"
            :disabled="!shot.audioUrl"
          >
            👁️ 预览
          </button>
          <button
            class="action-btn primary"
            @click="generateVoice(shot)"
            :disabled="!shot.text || shot.status === 'generating'"
          >
            {{ shot.status === 'generating' ? '⏳ 生成中' : '🎤 生成语音' }}
          </button>
        </div>
      </div>
    </main>

    <!-- 底部导航 -->
    <footer class="footer-navigation">
      <router-link to="/scriptwriter" class="nav-btn back">
        ← 返回AI编剧
      </router-link>
      <div class="progress-summary">
        <div>声音制作进度</div>
        <div class="progress-bar">
          <div class="progress-fill" :style="{ width: progressPercentage + '%' }"></div>
        </div>
        <div>{{ completedShots }} / {{ totalShots }} 已完成</div>
      </div>
      <router-link
        to="/image-storyboard"
        class="nav-btn next"
        :class="{ disabled: !canProceed }"
      >
        下一步：图像分镜 →
      </router-link>
    </footer>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'

export default {
  name: 'VoiceStoryboardView',
  setup() {
    const router = useRouter()
    
    // 响应式数据
    const projectName = ref('时间管理技巧分享')
    const isGenerating = ref(false)
    const defaultVoice = ref('alex')
    const defaultSpeed = ref('normal')
    const defaultVolume = ref('normal')
    
    const voiceShots = ref([])
    
    const availableVoices = ref([
      {
        id: 'alex',
        name: 'Alex',
        description: '专业男声',
        avatar: '👨‍💼',
        previewUrl: '/audio/alex-preview.mp3'
      },
      {
        id: 'sophie',
        name: 'Sophie',
        description: '亲和女声',
        avatar: '👩‍🏫',
        previewUrl: '/audio/sophie-preview.mp3'
      },
      {
        id: 'michael',
        name: 'Michael',
        description: '磁性男声',
        avatar: '🧑‍💻',
        previewUrl: '/audio/michael-preview.mp3'
      },
      {
        id: 'emma',
        name: 'Emma',
        description: '甜美女声',
        avatar: '👩‍🎤',
        previewUrl: '/audio/emma-preview.mp3'
      },
      {
        id: 'custom',
        name: '我的声音',
        description: '克隆您的声音',
        avatar: '🎤',
        previewUrl: null
      }
    ])
    
    // 计算属性
    const totalShots = computed(() => voiceShots.value.length)
    const completedShots = computed(() => 
      voiceShots.value.filter(shot => shot.status === 'completed').length
    )
    const pendingShots = computed(() => 
      voiceShots.value.filter(shot => shot.status === 'pending').length
    )
    const progressPercentage = computed(() => 
      totalShots.value > 0 ? (completedShots.value / totalShots.value) * 100 : 0
    )
    const canProceed = computed(() => completedShots.value === totalShots.value)
    
    // 生命周期
    onMounted(() => {
      initializeFromScript()
    })
    
    // 方法
    const initializeFromScript = () => {
      // 从localStorage获取脚本内容
      const scriptContent = localStorage.getItem('vidspark_script_content')
      if (scriptContent) {
        try {
          const script = JSON.parse(scriptContent)
          // 根据脚本生成语音分镜
          voiceShots.value = script.acts?.map((act, index) => ({
            id: `shot_${index + 1}`,
            text: act.content || '',
            selectedVoice: defaultVoice.value,
            speed: defaultSpeed.value,
            volume: defaultVolume.value,
            pitch: 'normal',
            status: 'pending',
            audioUrl: null,
            duration: null,
            isPlaying: false,
            voiceFile: null
          })) || []
        } catch (error) {
          console.error('解析脚本内容失败:', error)
          createDefaultShots()
        }
      } else {
        createDefaultShots()
      }
    }
    
    const createDefaultShots = () => {
      // 创建默认的5个分镜
      voiceShots.value = Array.from({ length: 5 }, (_, index) => ({
        id: `shot_${index + 1}`,
        text: '',
        selectedVoice: defaultVoice.value,
        speed: defaultSpeed.value,
        volume: defaultVolume.value,
        pitch: 'normal',
        status: 'pending',
        audioUrl: null,
        duration: null,
        isPlaying: false,
        voiceFile: null
      }))
    }
    
    const getStatusText = (status) => {
      const statusMap = {
        pending: '⏰ 待处理',
        generating: '⏳ 生成中',
        completed: '✅ 已完成',
        error: '❌ 生成失败'
      }
      return statusMap[status] || status
    }
    
    const selectVoice = (shot, voiceId) => {
      shot.selectedVoice = voiceId
      onShotChange(shot)
    }
    
    const onTextChange = (shot) => {
      if (shot.text.trim()) {
        shot.status = 'pending'
      }
      onShotChange(shot)
    }
    
    const onShotChange = (shot) => {
      // 保存到localStorage
      saveVoiceShots()
    }
    
    const saveVoiceShots = () => {
      localStorage.setItem('vidspark_voice_shots', JSON.stringify(voiceShots.value))
    }
    
    const previewVoice = (voiceId) => {
      const voice = availableVoices.value.find(v => v.id === voiceId)
      if (voice?.previewUrl) {
        // 播放预览音频
        const audio = new Audio(voice.previewUrl)
        audio.play().catch(e => console.log('播放预览失败:', e))
      }
    }
    
    const uploadVoiceFile = (shot, event) => {
      const file = event.target.files[0]
      if (file) {
        shot.voiceFile = file
        onShotChange(shot)
      }
    }
    
    const generateVoice = async (shot) => {
      if (!shot.text.trim()) return
      
      shot.status = 'generating'
      
      try {
        // 模拟API调用
        const response = await fetch('/api/generate-voice', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            text: shot.text,
            voice: shot.selectedVoice,
            speed: shot.speed,
            volume: shot.volume,
            pitch: shot.pitch,
            voiceFile: shot.voiceFile
          })
        })
        
        if (response.ok) {
          const data = await response.json()
          shot.audioUrl = data.audioUrl
          shot.duration = data.duration
          shot.status = 'completed'
        } else {
          throw new Error('生成失败')
        }
      } catch (error) {
        console.error('语音生成失败:', error)
        shot.status = 'error'
        alert('语音生成失败，请重试')
      }
      
      saveVoiceShots()
    }
    
    const generateAllVoices = async () => {
      if (isGenerating.value) return
      
      const pendingShots = voiceShots.value.filter(shot => 
        shot.text.trim() && shot.status === 'pending'
      )
      
      if (pendingShots.length === 0) {
        alert('没有待生成的语音分镜')
        return
      }
      
      if (!confirm(`确定要为${pendingShots.length}个分镜生成语音吗？`)) return
      
      isGenerating.value = true
      
      for (const shot of pendingShots) {
        await generateVoice(shot)
        // 添加延迟避免API限制
        await new Promise(resolve => setTimeout(resolve, 1000))
      }
      
      isGenerating.value = false
    }
    
    const togglePlay = (shot) => {
      if (!shot.audioUrl) return
      
      // 停止其他正在播放的音频
      voiceShots.value.forEach(s => {
        if (s.id !== shot.id) s.isPlaying = false
      })
      
      shot.isPlaying = !shot.isPlaying
      
      if (shot.isPlaying) {
        const audio = new Audio(shot.audioUrl)
        audio.play().catch(e => console.log('播放失败:', e))
        audio.onended = () => {
          shot.isPlaying = false
        }
      }
    }
    
    const regenerateVoice = (shot) => {
      if (confirm('确定要重新生成这个语音吗？')) {
        shot.audioUrl = null
        shot.status = 'pending'
        generateVoice(shot)
      }
    }
    
    const previewShot = (shot) => {
      if (shot.audioUrl) {
        togglePlay(shot)
      }
    }
    
    return {
      // 数据
      projectName,
      isGenerating,
      defaultVoice,
      defaultSpeed,
      defaultVolume,
      voiceShots,
      availableVoices,
      
      // 计算属性
      totalShots,
      completedShots,
      pendingShots,
      progressPercentage,
      canProceed,
      
      // 方法
      getStatusText,
      selectVoice,
      onTextChange,
      previewVoice,
      uploadVoiceFile,
      generateVoice,
      generateAllVoices,
      togglePlay,
      regenerateVoice,
      previewShot
    }
  }
}
</script>

<style scoped>
/* 这里包含原HTML中的所有CSS样式，但去掉了body等全局样式 */
.container {
  max-width: 1600px;
  margin: 0 auto;
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0,0,0,0.1);
  min-height: 100vh;
}

/* 顶部导航 */
.header {
  background: #1a1c20;
  color: white;
  padding: 20px 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo {
  font-size: 24px;
  font-weight: bold;
  color: #667eea;
}

.progress-flow {
  display: flex;
  align-items: center;
  gap: 15px;
}

.flow-step {
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.3s ease;
}

.flow-step.completed {
  background: #10b981;
  color: white;
}

.flow-step.active {
  background: #f59e0b;
  color: white;
  animation: pulse 2s infinite;
}

.flow-step.pending {
  background: #6b7280;
  color: #d1d5db;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.7; }
}

.flow-arrow {
  color: #6b7280;
  font-size: 18px;
}

/* 主标题区 */
.main-header {
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  padding: 40px 30px;
  text-align: center;
  border-bottom: 1px solid #e5e7eb;
}

.main-title {
  font-size: 32px;
  font-weight: bold;
  color: #1f2937;
  margin-bottom: 10px;
}

.main-subtitle {
  font-size: 18px;
  color: #6b7280;
  margin-bottom: 20px;
}

.stats-bar {
  display: flex;
  justify-content: center;
  gap: 30px;
  margin-top: 20px;
}

.stat-item {
  text-align: center;
}

.stat-number {
  font-size: 24px;
  font-weight: bold;
  color: #667eea;
}

.stat-label {
  font-size: 14px;
  color: #6b7280;
  margin-top: 5px;
}

/* 全局操作栏 */
.global-actions {
  background: #f8fafc;
  padding: 20px 30px;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 15px;
}

.mega-button {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  padding: 15px 30px;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.mega-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

.mega-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.global-settings {
  display: flex;
  gap: 20px;
  align-items: center;
  flex-wrap: wrap;
}

.setting-group {
  display: flex;
  align-items: center;
  gap: 8px;
}

.setting-selector {
  padding: 8px 15px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 14px;
  background: white;
  cursor: pointer;
}

/* 分镜网格 */
.shots-grid {
  padding: 30px;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
  gap: 30px;
  background: #f9fafb;
}

/* 语音分镜卡片 */
.voice-shot-card {
  background: white;
  border-radius: 20px;
  padding: 25px;
  box-shadow: 0 8px 30px rgba(0,0,0,0.1);
  transition: all 0.3s ease;
  border: 3px solid transparent;
}

.voice-shot-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 50px rgba(0,0,0,0.15);
}

.voice-shot-card.completed {
  border-color: #10b981;
}

.voice-shot-card.generating {
  border-color: #f59e0b;
}

.voice-shot-card.error {
  border-color: #ef4444;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.shot-title {
  font-size: 20px;
  font-weight: bold;
  color: #1f2937;
}

.status-badge {
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 600;
}

.status-badge.completed {
  background: #dcfce7;
  color: #166534;
}

.status-badge.generating {
  background: #fef3c7;
  color: #92400e;
}

.status-badge.pending {
  background: #f3f4f6;
  color: #6b7280;
}

.status-badge.error {
  background: #fecaca;
  color: #dc2626;
}

/* 脚本文本 */
.script-text {
  margin-bottom: 20px;
}

.script-label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  margin-bottom: 8px;
}

.script-input {
  width: 100%;
  min-height: 80px;
  padding: 12px 15px;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  font-size: 14px;
  line-height: 1.5;
  resize: vertical;
  transition: border-color 0.2s ease;
}

.script-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* 语音选择 */
.voice-selection {
  margin-bottom: 20px;
}

.voice-label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  margin-bottom: 12px;
}

.voice-options {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.voice-option {
  display: flex;
  align-items: center;
  padding: 12px 15px;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.voice-option:hover {
  border-color: #667eea;
  background: #f8fafc;
}

.voice-option.selected {
  border-color: #667eea;
  background: #eef2ff;
}

.voice-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea, #764ba2);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  margin-right: 12px;
}

.voice-info {
  flex: 1;
}

.voice-name {
  font-weight: 500;
  color: #374151;
  margin-bottom: 2px;
}

.voice-desc {
  font-size: 12px;
  color: #6b7280;
}

.voice-preview-btn {
  width: 32px;
  height: 32px;
  border: none;
  background: #f3f4f6;
  border-radius: 50%;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s ease;
}

.voice-preview-btn:hover {
  background: #e5e7eb;
}

/* 语音克隆区域 */
.voice-clone-section {
  background: #fef3c7;
  border: 2px solid #f59e0b;
  border-radius: 15px;
  padding: 20px;
  margin-bottom: 20px;
}

.clone-upload {
  text-align: center;
}

.upload-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  background: #667eea;
  color: white;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s ease;
}

.upload-btn:hover {
  background: #5a67d8;
}

.upload-btn.has-file {
  background: #10b981;
}

.upload-hint {
  font-size: 12px;
  color: #92400e;
  margin-top: 8px;
}

/* 语音设置 */
.voice-settings {
  background: #f8fafc;
  border-radius: 12px;
  padding: 15px;
  margin-bottom: 20px;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 15px;
}

.setting-row {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.setting-row label {
  font-size: 12px;
  font-weight: 500;
  color: #6b7280;
}

.mini-select {
  padding: 6px 8px;
  border: 2px solid #e5e7eb;
  border-radius: 6px;
  font-size: 12px;
  background: white;
}

/* 音频播放器 */
.audio-player {
  background: #f0f9ff;
  border: 2px solid #0ea5e9;
  border-radius: 12px;
  padding: 15px;
  margin-bottom: 20px;
}

.player-info {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 10px;
  font-size: 14px;
  color: #0369a1;
  font-weight: 500;
}

.player-controls {
  display: flex;
  align-items: center;
  gap: 12px;
}

.play-btn {
  width: 40px;
  height: 40px;
  border: none;
  background: #0ea5e9;
  color: white;
  border-radius: 50%;
  cursor: pointer;
  font-size: 16px;
  transition: all 0.2s ease;
}

.play-btn:hover {
  background: #0284c7;
}

.audio-waveform {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 2px;
  height: 30px;
}

.waveform-bar {
  width: 3px;
  background: #0ea5e9;
  border-radius: 2px;
  height: 100%;
  animation: wave 1.5s ease-in-out infinite;
}

.waveform-bar:nth-child(even) {
  animation-delay: 0.2s;
}

.waveform-bar:nth-child(3n) {
  animation-delay: 0.4s;
}

@keyframes wave {
  0%, 100% { height: 20%; }
  50% { height: 100%; }
}

.retry-btn {
  padding: 8px 12px;
  border: 2px solid #0ea5e9;
  background: white;
  color: #0ea5e9;
  border-radius: 8px;
  cursor: pointer;
  font-size: 12px;
  font-weight: 500;
  transition: all 0.2s ease;
}

.retry-btn:hover {
  background: #0ea5e9;
  color: white;
}

/* 操作按钮 */
.card-actions {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.action-btn {
  padding: 12px 20px;
  border: none;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.action-btn.primary {
  background: #667eea;
  color: white;
}

.action-btn.primary:hover:not(:disabled) {
  background: #5a67d8;
  transform: translateY(-1px);
}

.action-btn.secondary {
  background: #f3f4f6;
  color: #374151;
  border: 2px solid #e5e7eb;
}

.action-btn.secondary:hover:not(:disabled) {
  background: #e5e7eb;
}

.action-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none !important;
}

/* 底部导航 */
.footer-navigation {
  background: white;
  padding: 25px 30px;
  border-top: 1px solid #e5e7eb;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.nav-btn {
  padding: 12px 25px;
  border-radius: 10px;
  font-size: 16px;
  font-weight: 500;
  text-decoration: none;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.nav-btn.back {
  background: #f3f4f6;
  color: #374151;
  border: 1px solid #e5e7eb;
}

.nav-btn.back:hover {
  background: #e5e7eb;
}

.nav-btn.next {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.nav-btn.next:hover:not(.disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.nav-btn.disabled {
  opacity: 0.5;
  cursor: not-allowed;
  pointer-events: none;
}

.progress-summary {
  text-align: center;
  font-size: 14px;
  color: #6b7280;
}

.progress-bar {
  width: 200px;
  height: 8px;
  background: #e5e7eb;
  border-radius: 4px;
  margin: 8px auto;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #10b981, #059669);
  border-radius: 4px;
  transition: width 0.3s ease;
}

/* 响应式设计 */
@media (max-width: 1200px) {
  .shots-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .shots-grid {
    padding: 20px;
    gap: 20px;
  }
  
  .global-actions {
    flex-direction: column;
    gap: 15px;
    text-align: center;
  }
  
  .footer-navigation {
    flex-direction: column;
    gap: 15px;
  }

  .voice-settings {
    grid-template-columns: 1fr;
  }

  .card-actions {
    grid-template-columns: 1fr;
  }
}
</style>

