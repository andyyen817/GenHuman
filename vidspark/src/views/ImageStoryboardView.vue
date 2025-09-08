<template>
  <div class="container">
    <!-- 顶部导航 -->
    <header class="header">
      <div class="logo">🎬 Vidspark</div>
      <div class="progress-flow">
        <span class="flow-step completed">📝 编剧</span>
        <span class="flow-arrow">→</span>
        <span class="flow-step completed">🎤 声音分镜</span>
        <span class="flow-arrow">→</span>
        <span class="flow-step active">🎬 图像分镜</span>
        <span class="flow-arrow">→</span>
        <span class="flow-step pending">🎞️ 剪辑</span>
      </div>
      <div style="color: #6b7280; font-size: 14px;">
        项目：{{ projectName }}
      </div>
    </header>

    <!-- 主标题区 -->
    <section class="main-header">
      <h1 class="main-title">🎬 图像分镜制作</h1>
      <p class="main-subtitle">为每个分镜添加视觉内容，让你的故事更加生动</p>
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
      <button class="mega-button" @click="generateAllImages" :disabled="isGenerating">
        {{ isGenerating ? '⏳ 生成中...' : '🎨 一键生成所有图像' }}
      </button>
      <div class="global-settings">
        <div class="setting-group">
          <label>影片风格：</label>
          <select class="setting-selector" v-model="globalStyle">
            <option value="cartoon">卡通风格</option>
            <option value="realistic">写实风格</option>
            <option value="business">商务风格</option>
            <option value="handdrawn">手绘风格</option>
          </select>
        </div>
        <div class="setting-group">
          <label>画面比例：</label>
          <select class="setting-selector" v-model="aspectRatio">
            <option value="16:9">16:9 横屏</option>
            <option value="9:16">9:16 竖屏</option>
            <option value="1:1">1:1 方形</option>
          </select>
        </div>
        <div class="setting-group">
          <label>分辨率：</label>
          <select class="setting-selector" v-model="resolution">
            <option value="1080p">1080p 高清</option>
            <option value="720p">720p 标清</option>
            <option value="4k">4K 超清</option>
          </select>
        </div>
      </div>
    </section>

    <!-- 分镜网格 -->
    <main class="shots-grid">
      <div
        v-for="(shot, index) in imageShots"
        :key="shot.id"
        :class="['image-shot-card', shot.status]"
      >
        <div class="card-header">
          <h3 class="shot-title">🎬 第{{ index + 1 }}幕</h3>
          <span :class="['status-badge', shot.status]">
            {{ getStatusText(shot.status) }}
          </span>
        </div>
        
        <!-- 音频信息 -->
        <div class="audio-info">
          <div class="audio-icon">🎤</div>
          <div class="audio-details">
            <div class="audio-text">{{ shot.audioText || '暂无音频内容' }}</div>
            <div class="audio-meta">{{ shot.voiceName }} • 时长: {{ shot.duration || '0:08' }}</div>
          </div>
        </div>

        <!-- 内容选择标签页 -->
        <div class="content-tabs">
          <button
            :class="['tab-button', { active: shot.activeTab === 'avatar' }]"
            @click="switchTab(shot, 'avatar')"
          >
            👨 数字人
          </button>
          <button
            :class="['tab-button', { active: shot.activeTab === 'image' }]"
            @click="switchTab(shot, 'image')"
          >
            🖼️ 图片
          </button>
          <button
            :class="['tab-button', { active: shot.activeTab === 'ai' }]"
            @click="switchTab(shot, 'ai')"
          >
            🎨 AI生图
          </button>
        </div>

        <!-- 数字人标签页 -->
        <div v-show="shot.activeTab === 'avatar'" class="tab-content">
          <div class="avatar-selection">
            <div
              v-for="avatar in availableAvatars"
              :key="avatar.id"
              :class="['avatar-option', { selected: shot.selectedAvatar === avatar.id }]"
              @click="selectAvatar(shot, avatar.id)"
            >
              <div class="avatar-preview">{{ avatar.emoji }}</div>
              <div class="avatar-name">{{ avatar.name }}</div>
            </div>
          </div>
        </div>

        <!-- 图片标签页 -->
        <div v-show="shot.activeTab === 'image'" class="tab-content">
          <div
            :class="['image-upload', { 'has-image': shot.uploadedImage }]"
            @click="uploadImage(shot)"
          >
            <div class="upload-icon">{{ shot.uploadedImage ? '📊' : '📁' }}</div>
            <div class="upload-text">
              {{ shot.uploadedImage ? shot.uploadedImage.name : '点击上传图片' }}
            </div>
            <div class="upload-hint">支持JPG、PNG、PPT截图 • 最大10MB</div>
          </div>
        </div>

        <!-- AI生图标签页 -->
        <div v-show="shot.activeTab === 'ai'" class="tab-content">
          <div class="ai-generation">
            <textarea
              v-model="shot.aiPrompt"
              class="prompt-input"
              placeholder="描述你想要的画面..."
            ></textarea>
            <div class="prompt-tips">
              <span
                v-for="tip in promptTips"
                :key="tip"
                class="prompt-tip"
                @click="addPrompt(shot, tip)"
              >
                {{ tip }}
              </span>
            </div>
          </div>
        </div>

        <!-- 组合设置 -->
        <div class="composition-settings">
          <div class="setting-section">
            <div class="setting-title">组合方式：</div>
            <div class="setting-options">
              <div
                :class="['setting-option', { selected: shot.compositionMode === 'avatar-front' }]"
                @click="shot.compositionMode = 'avatar-front'"
              >
                数字人在前
              </div>
              <div
                :class="['setting-option', { selected: shot.compositionMode === 'image-front' }]"
                @click="shot.compositionMode = 'image-front'"
              >
                图片在前
              </div>
              <div
                :class="['setting-option', { selected: shot.compositionMode === 'none' }]"
                @click="shot.compositionMode = 'none'"
              >
                无组合
              </div>
            </div>
          </div>

          <div class="setting-section">
            <div class="setting-title">{{ shot.compositionMode === 'avatar-front' ? '数字人' : '图片' }}位置：</div>
            <div class="position-grid">
              <div
                v-for="position in positions"
                :key="position.value"
                :class="['position-option', { selected: shot.position === position.value }]"
                @click="shot.position = position.value"
              >
                {{ position.label }}
              </div>
            </div>
          </div>

          <div class="setting-section">
            <div class="setting-title">{{ shot.compositionMode === 'avatar-front' ? '数字人' : '图片' }}尺寸：</div>
            <div class="setting-options">
              <div
                v-for="size in sizes"
                :key="size.value"
                :class="['setting-option', { selected: shot.size === size.value }]"
                @click="shot.size = size.value"
              >
                {{ size.label }}
              </div>
            </div>
          </div>

          <div class="setting-section">
            <div class="setting-title">显示时长：</div>
            <div class="setting-options">
              <div
                v-for="duration in durations"
                :key="duration.value"
                :class="['setting-option', { selected: shot.displayDuration === duration.value }]"
                @click="shot.displayDuration = duration.value"
              >
                {{ duration.label }}
              </div>
            </div>
          </div>
        </div>

        <!-- 预览区域 -->
        <div class="preview-area">
          <div v-if="shot.previewUrl" class="preview-content">
            <img :src="shot.previewUrl" alt="预览" />
          </div>
          <div v-else class="preview-placeholder">
            {{ shot.status === 'generating' ? '⏳ AI正在生成图像...' : '点击上方标签选择内容类型' }}
            <small v-if="shot.status !== 'generating'" style="font-size: 14px; margin-top: 10px; display: block;">
              💡 建议：先选择数字人，再添加背景图片
            </small>
          </div>
        </div>

        <div class="card-actions">
          <button
            class="action-btn secondary"
            @click="previewShot(shot)"
            :disabled="!shot.previewUrl"
          >
            {{ shot.status === 'generating' ? '⏳ 生成中...' : '🔄 重新生成' }}
          </button>
          <button
            class="action-btn primary"
            @click="generateImage(shot)"
            :disabled="shot.status === 'generating'"
          >
            {{ shot.status === 'generating' ? '⏳ 处理中' : '🚀 开始生成' }}
          </button>
        </div>
      </div>
    </main>

    <!-- 底部导航 -->
    <footer class="footer-navigation">
      <router-link to="/voice-storyboard" class="nav-btn back">
        ← 返回声音分镜
      </router-link>
      <div class="progress-summary">
        <div>图像制作进度</div>
        <div class="progress-bar">
          <div class="progress-fill" :style="{ width: progressPercentage + '%' }"></div>
        </div>
        <div>{{ completedShots }} / {{ totalShots }} 已完成</div>
      </div>
      <router-link
        to="/editor"
        :class="['nav-btn', 'next', { disabled: !canProceed }]"
      >
        下一步：影片剪辑 →
      </router-link>
    </footer>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'

export default {
  name: 'ImageStoryboardView',
  setup() {
    const router = useRouter()
    
    // 响应式数据
    const projectName = ref('时间管理技巧分享')
    const isGenerating = ref(false)
    const globalStyle = ref('cartoon')
    const aspectRatio = ref('16:9')
    const resolution = ref('1080p')
    
    const imageShots = ref([])
    
    const availableAvatars = ref([
      { id: 'business-man', name: '商务男士', emoji: '👨‍💼' },
      { id: 'teacher-woman', name: '亲和女老师', emoji: '👩‍🏫' },
      { id: 'tech-person', name: '科技达人', emoji: '🧑‍💻' }
    ])
    
    const promptTips = ref([
      '现代办公室', '时钟元素', '简洁风格', '明亮光线',
      '番茄计时器', '25分钟', '专注氛围', '简约风格',
      '优先级矩阵', '四象限', '重要紧急', '清晰图表'
    ])
    
    const positions = ref([
      { value: 'top-left', label: '左上' },
      { value: 'top-center', label: '居中' },
      { value: 'top-right', label: '右上' },
      { value: 'center-left', label: '左中' },
      { value: 'center', label: '中心' },
      { value: 'center-right', label: '右中' },
      { value: 'bottom-left', label: '左下' },
      { value: 'bottom-center', label: '中下' },
      { value: 'bottom-right', label: '右下' }
    ])
    
    const sizes = ref([
      { value: 'small', label: '小' },
      { value: 'medium', label: '中' },
      { value: 'large', label: '大' },
      { value: 'fullscreen', label: '全屏' }
    ])
    
    const durations = ref([
      { value: 3, label: '3秒' },
      { value: 5, label: '5秒' },
      { value: 8, label: '8秒' },
      { value: 0, label: '自定义' }
    ])
    
    // 计算属性
    const totalShots = computed(() => imageShots.value.length)
    const completedShots = computed(() => 
      imageShots.value.filter(shot => shot.status === 'completed').length
    )
    const pendingShots = computed(() => 
      imageShots.value.filter(shot => shot.status === 'pending').length
    )
    const progressPercentage = computed(() => 
      totalShots.value > 0 ? (completedShots.value / totalShots.value) * 100 : 0
    )
    const canProceed = computed(() => completedShots.value === totalShots.value)
    
    // 生命周期
    onMounted(() => {
      initializeFromVoiceShots()
    })
    
    // 方法
    const initializeFromVoiceShots = () => {
      // 从localStorage获取声音分镜内容
      const voiceShots = localStorage.getItem('vidspark_voice_shots')
      if (voiceShots) {
        try {
          const shots = JSON.parse(voiceShots)
          imageShots.value = shots.map((voiceShot, index) => ({
            id: `image_shot_${index + 1}`,
            // 从声音分镜继承的数据
            audioText: voiceShot.text,
            voiceName: voiceShot.selectedVoice,
            duration: voiceShot.duration,
            // 图像分镜特有的数据
            activeTab: 'avatar',
            selectedAvatar: 'business-man',
            uploadedImage: null,
            aiPrompt: '',
            compositionMode: 'avatar-front',
            position: 'center',
            size: 'medium',
            displayDuration: 8,
            status: 'pending',
            previewUrl: null
          }))
        } catch (error) {
          console.error('解析声音分镜内容失败:', error)
          createDefaultShots()
        }
      } else {
        createDefaultShots()
      }
    }
    
    const createDefaultShots = () => {
      imageShots.value = Array.from({ length: 5 }, (_, index) => ({
        id: `image_shot_${index + 1}`,
        audioText: '',
        voiceName: '默认声音',
        duration: '0:08',
        activeTab: 'avatar',
        selectedAvatar: 'business-man',
        uploadedImage: null,
        aiPrompt: '',
        compositionMode: 'avatar-front',
        position: 'center',
        size: 'medium',
        displayDuration: 8,
        status: 'pending',
        previewUrl: null
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
    
    const switchTab = (shot, tabType) => {
      shot.activeTab = tabType
      saveImageShots()
    }
    
    const selectAvatar = (shot, avatarId) => {
      shot.selectedAvatar = avatarId
      saveImageShots()
    }
    
    const uploadImage = (shot) => {
      const input = document.createElement('input')
      input.type = 'file'
      input.accept = 'image/*'
      input.onchange = (event) => {
        const file = event.target.files[0]
        if (file) {
          shot.uploadedImage = {
            name: file.name,
            file: file,
            url: URL.createObjectURL(file)
          }
          saveImageShots()
        }
      }
      input.click()
    }
    
    const addPrompt = (shot, prompt) => {
      if (shot.aiPrompt) {
        shot.aiPrompt += `, ${prompt}`
      } else {
        shot.aiPrompt = prompt
      }
      saveImageShots()
    }
    
    const saveImageShots = () => {
      localStorage.setItem('vidspark_final_shots', JSON.stringify(imageShots.value))
    }
    
    const generateImage = async (shot) => {
      shot.status = 'generating'
      
      try {
        // 模拟API调用
        const response = await fetch('/api/generate-image', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            activeTab: shot.activeTab,
            selectedAvatar: shot.selectedAvatar,
            uploadedImage: shot.uploadedImage,
            aiPrompt: shot.aiPrompt,
            compositionMode: shot.compositionMode,
            position: shot.position,
            size: shot.size,
            displayDuration: shot.displayDuration,
            globalStyle: globalStyle.value,
            aspectRatio: aspectRatio.value,
            resolution: resolution.value
          })
        })
        
        if (response.ok) {
          const data = await response.json()
          shot.previewUrl = data.imageUrl
          shot.status = 'completed'
        } else {
          throw new Error('生成失败')
        }
      } catch (error) {
        console.error('图像生成失败:', error)
        shot.status = 'error'
        alert('图像生成失败，请重试')
      }
      
      saveImageShots()
    }
    
    const generateAllImages = async () => {
      if (isGenerating.value) return
      
      const pendingShots = imageShots.value.filter(shot => shot.status === 'pending')
      
      if (pendingShots.length === 0) {
        alert('没有待生成的图像分镜')
        return
      }
      
      if (!confirm(`确定要为${pendingShots.length}个分镜生成图像吗？这可能需要几分钟时间。`)) return
      
      isGenerating.value = true
      
      for (const shot of pendingShots) {
        await generateImage(shot)
        // 添加延迟避免API限制
        await new Promise(resolve => setTimeout(resolve, 2000))
      }
      
      isGenerating.value = false
    }
    
    const previewShot = (shot) => {
      if (shot.previewUrl) {
        // 打开预览窗口
        window.open(shot.previewUrl, '_blank')
      }
    }
    
    return {
      // 数据
      projectName,
      isGenerating,
      globalStyle,
      aspectRatio,
      resolution,
      imageShots,
      availableAvatars,
      promptTips,
      positions,
      sizes,
      durations,
      
      // 计算属性
      totalShots,
      completedShots,
      pendingShots,
      progressPercentage,
      canProceed,
      
      // 方法
      getStatusText,
      switchTab,
      selectAvatar,
      uploadImage,
      addPrompt,
      generateImage,
      generateAllImages,
      previewShot
    }
  }
}
</script>

<style scoped>
/* 复用声音分镜页面的大部分样式，这里只写特有的样式 */
.container {
  max-width: 1600px;
  margin: 0 auto;
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0,0,0,0.1);
  min-height: 100vh;
}

/* 图像分镜特有样式 */
.image-shot-card {
  background: white;
  border-radius: 20px;
  padding: 25px;
  box-shadow: 0 8px 30px rgba(0,0,0,0.1);
  transition: all 0.3s ease;
  border: 3px solid transparent;
}

.image-shot-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 50px rgba(0,0,0,0.15);
}

.image-shot-card.completed {
  border-color: #10b981;
}

.image-shot-card.generating {
  border-color: #f59e0b;
}

/* 音频信息显示 */
.audio-info {
  background: #f0f9ff;
  border: 2px solid #0ea5e9;
  border-radius: 12px;
  padding: 15px;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 12px;
}

.audio-icon {
  width: 40px;
  height: 40px;
  background: #0ea5e9;
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
}

.audio-details {
  flex: 1;
}

.audio-text {
  font-size: 14px;
  color: #374151;
  margin-bottom: 4px;
}

.audio-meta {
  font-size: 12px;
  color: #6b7280;
}

/* 内容选择标签页 */
.content-tabs {
  display: flex;
  border-bottom: 2px solid #e5e7eb;
  margin-bottom: 20px;
}

.tab-button {
  flex: 1;
  padding: 12px 16px;
  border: none;
  background: transparent;
  font-size: 14px;
  font-weight: 500;
  color: #6b7280;
  cursor: pointer;
  transition: all 0.2s ease;
  position: relative;
}

.tab-button.active {
  color: #667eea;
  font-weight: 600;
}

.tab-button.active::after {
  content: '';
  position: absolute;
  bottom: -2px;
  left: 0;
  right: 0;
  height: 2px;
  background: #667eea;
}

.tab-content {
  min-height: 200px;
}

/* 数字人选择 */
.avatar-selection {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: 15px;
  margin-bottom: 20px;
}

.avatar-option {
  background: #f8fafc;
  border: 3px solid #e5e7eb;
  border-radius: 15px;
  padding: 15px;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s ease;
}

.avatar-option:hover {
  border-color: #667eea;
  transform: translateY(-2px);
}

.avatar-option.selected {
  border-color: #667eea;
  background: #eef2ff;
}

.avatar-preview {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea, #764ba2);
  margin: 0 auto 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 36px;
  color: white;
}

.avatar-name {
  font-size: 13px;
  font-weight: 500;
  color: #374151;
}

/* 图片上传区 */
.image-upload {
  border: 3px dashed #d1d5db;
  border-radius: 15px;
  padding: 40px 20px;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-bottom: 20px;
}

.image-upload:hover {
  border-color: #667eea;
  background: #f8fafc;
}

.image-upload.has-image {
  border-style: solid;
  border-color: #10b981;
  background: #f0fdf4;
}

.upload-icon {
  font-size: 48px;
  color: #9ca3af;
  margin-bottom: 15px;
}

.upload-text {
  font-size: 16px;
  color: #6b7280;
  margin-bottom: 10px;
}

.upload-hint {
  font-size: 14px;
  color: #9ca3af;
}

/* AI生图设置 */
.ai-generation {
  background: #fef3c7;
  border: 2px solid #f59e0b;
  border-radius: 15px;
  padding: 20px;
  margin-bottom: 20px;
}

.prompt-input {
  width: 100%;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  padding: 15px;
  font-size: 14px;
  line-height: 1.5;
  margin-bottom: 15px;
  resize: vertical;
  min-height: 80px;
}

.prompt-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.prompt-tips {
  display: flex;
  gap: 10px;
  margin-bottom: 15px;
  flex-wrap: wrap;
}

.prompt-tip {
  background: #fff7ed;
  border: 1px solid #fed7aa;
  border-radius: 20px;
  padding: 6px 12px;
  font-size: 12px;
  color: #9a3412;
  cursor: pointer;
  transition: all 0.2s ease;
}

.prompt-tip:hover {
  background: #fed7aa;
}

/* 组合设置 */
.composition-settings {
  background: #f1f5f9;
  border-radius: 15px;
  padding: 20px;
  margin-top: 20px;
}

.setting-section {
  margin-bottom: 20px;
}

.setting-title {
  font-size: 14px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 10px;
}

.setting-options {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.setting-option {
  padding: 8px 15px;
  border: 2px solid #e5e7eb;
  border-radius: 20px;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.2s ease;
  background: white;
}

.setting-option.selected {
  border-color: #667eea;
  background: #eef2ff;
  color: #667eea;
  font-weight: 500;
}

/* 位置布局选择器 */
.position-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 8px;
  margin-top: 10px;
}

.position-option {
  aspect-ratio: 1;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  transition: all 0.2s ease;
  background: white;
}

.position-option.selected {
  border-color: #667eea;
  background: #eef2ff;
  color: #667eea;
}

/* 预览区域 */
.preview-area {
  background: #000;
  border-radius: 15px;
  aspect-ratio: 16/9;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 18px;
  margin-bottom: 20px;
  position: relative;
  overflow: hidden;
}

.preview-placeholder {
  text-align: center;
  opacity: 0.7;
}

.preview-content {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.preview-content img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

/* 继承其他样式 */
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

.shots-grid {
  padding: 30px;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
  gap: 30px;
  background: #f9fafb;
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

.card-actions {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
  margin-top: 20px;
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

  .card-actions {
    grid-template-columns: 1fr;
  }
}
</style>
