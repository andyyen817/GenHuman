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
        <span class="flow-step completed">🎬 图像分镜</span>
        <span class="flow-arrow">→</span>
        <span class="flow-step active">🎞️ 剪辑</span>
      </div>
      <div style="color: #6b7280; font-size: 14px;">
        项目：{{ projectName }}
      </div>
    </header>

    <!-- 主标题区 -->
    <section class="main-header">
      <h1 class="main-title">🎞️ AI智能剪辑</h1>
      <p class="main-subtitle">自动合成你的创作内容，生成专业的短视频</p>
      <div class="project-stats">
        <div class="stat-item">
          <div class="stat-number">{{ totalShots }}</div>
          <div class="stat-label">分镜片段</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">{{ totalDuration }}</div>
          <div class="stat-label">总时长(秒)</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">{{ outputQuality }}</div>
          <div class="stat-label">输出质量</div>
        </div>
      </div>
    </section>

    <!-- 主工作区 -->
    <main class="editor-workspace">
      <!-- 左侧主编辑区 -->
      <div class="main-editor">
        <!-- 视频预览器 -->
        <div class="video-preview">
          <div class="preview-content">
            <div v-if="previewVideo" class="video-container">
              <video
                ref="videoPlayer"
                :src="previewVideo"
                @timeupdate="updateTimeDisplay"
                @loadedmetadata="onVideoLoaded"
              ></video>
            </div>
            <div v-else class="preview-placeholder">
              <div style="text-align: center;">
                <div style="font-size: 48px; margin-bottom: 15px;">👨‍💼</div>
                <div style="font-size: 18px; margin-bottom: 10px;">{{ projectName }}</div>
                <div style="font-size: 14px; opacity: 0.8;">预览：{{ totalShots }}个分镜已自动合成</div>
              </div>
            </div>
          </div>
          <div class="preview-overlay">
            <div class="play-controls">
              <button class="play-button" @click="togglePlay">
                {{ isPlaying ? '⏸️' : '▶️' }}
              </button>
              <div class="time-display">{{ formatTime(currentTime) }} / {{ formatTime(duration) }}</div>
            </div>
            <div class="video-info">
              {{ outputQuality }} • {{ aspectRatio }} • 已添加字幕和背景音乐
            </div>
          </div>
        </div>

        <!-- 时间轴编辑器 -->
        <div class="timeline-container">
          <div class="timeline-header">
            <h3 class="timeline-title">📏 时间轴编辑</h3>
            <div class="timeline-controls">
              <button
                v-for="filter in timelineFilters"
                :key="filter.key"
                :class="['timeline-btn', { active: activeFilter === filter.key }]"
                @click="setTimelineFilter(filter.key)"
              >
                {{ filter.label }}
              </button>
            </div>
          </div>
          
          <div class="timeline-tracks">
            <!-- 视频轨道 -->
            <div v-show="showTrack('video')" class="track">
              <div class="track-label">视频</div>
              <div class="track-content">
                <div
                  v-for="(clip, index) in videoClips"
                  :key="`video-${index}`"
                  class="timeline-clip video"
                  @click="selectClip('video', index)"
                >
                  🎬 第{{ index + 1 }}幕 {{ clip.duration }}s
                </div>
              </div>
            </div>
            
            <!-- 音频轨道 -->
            <div v-show="showTrack('audio')" class="track">
              <div class="track-label">音频</div>
              <div class="track-content">
                <div
                  v-for="(clip, index) in audioClips"
                  :key="`audio-${index}`"
                  class="timeline-clip audio"
                  @click="selectClip('audio', index)"
                >
                  🎤 配音-{{ index + 1 }}
                </div>
              </div>
            </div>
            
            <!-- 字幕轨道 -->
            <div v-show="showTrack('subtitle')" class="track">
              <div class="track-label">字幕</div>
              <div class="track-content">
                <div
                  v-for="(clip, index) in subtitleClips"
                  :key="`subtitle-${index}`"
                  class="timeline-clip text"
                  @click="selectClip('subtitle', index)"
                >
                  📝 字幕-{{ index + 1 }}
                </div>
              </div>
            </div>
            
            <!-- 背景音乐轨道 -->
            <div v-show="showTrack('music')" class="track">
              <div class="track-label">音乐</div>
              <div class="track-content">
                <div class="timeline-clip music">
                  🎵 {{ backgroundMusic.name }} ({{ totalDuration }}s)
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 右侧设置面板 -->
      <aside class="settings-panel">
        <!-- 字幕设置 -->
        <div class="panel-section">
          <h3 class="panel-title">📝 字幕设置</h3>
          
          <div class="setting-group">
            <label class="setting-label">字幕样式</label>
            <div class="setting-options">
              <div
                v-for="style in subtitleStyles"
                :key="style.value"
                :class="['setting-option', { selected: subtitleSettings.style === style.value }]"
                @click="subtitleSettings.style = style.value"
              >
                {{ style.label }}
              </div>
            </div>
            <div class="subtitle-preview">
              <div class="subtitle-text" :style="getSubtitleStyle()">
                大家好，我是时间管理小助手
              </div>
            </div>
          </div>

          <div class="setting-group">
            <label class="setting-label">字体大小</label>
            <input
              type="range"
              class="setting-input"
              min="12"
              max="48"
              v-model="subtitleSettings.fontSize"
            >
            <div style="text-align: center; font-size: 12px; color: #6b7280; margin-top: 5px;">
              当前: {{ subtitleSettings.fontSize }}px
            </div>
          </div>

          <div class="setting-group">
            <label class="setting-label">字幕位置</label>
            <div class="setting-options">
              <div
                v-for="position in subtitlePositions"
                :key="position.value"
                :class="['setting-option', { selected: subtitleSettings.position === position.value }]"
                @click="subtitleSettings.position = position.value"
              >
                {{ position.label }}
              </div>
            </div>
          </div>
        </div>

        <!-- 转场效果 -->
        <div class="panel-section">
          <h3 class="panel-title">🎬 转场效果</h3>
          
          <div class="setting-group">
            <label class="setting-label">转场类型</label>
            <div class="setting-options">
              <div
                v-for="transition in transitionTypes"
                :key="transition.value"
                :class="['setting-option', { selected: transitionSettings.type === transition.value }]"
                @click="transitionSettings.type = transition.value"
              >
                {{ transition.label }}
              </div>
            </div>
          </div>

          <div class="setting-group">
            <label class="setting-label">转场时长</label>
            <select class="setting-input" v-model="transitionSettings.duration">
              <option value="0.3">0.3秒</option>
              <option value="0.5">0.5秒</option>
              <option value="0.8">0.8秒</option>
              <option value="1.0">1.0秒</option>
            </select>
          </div>
        </div>

        <!-- 音频设置 -->
        <div class="panel-section">
          <h3 class="panel-title">🎵 音频设置</h3>
          
          <div class="setting-group">
            <label class="setting-label">背景音乐</label>
            <select class="setting-input" v-model="audioSettings.backgroundMusic">
              <option value="relaxed">轻松愉快</option>
              <option value="business">专业商务</option>
              <option value="warm">温馨暖心</option>
              <option value="tech">科技感</option>
              <option value="none">无背景音乐</option>
            </select>
          </div>

          <div class="setting-group">
            <label class="setting-label">音乐音量</label>
            <input
              type="range"
              class="setting-input"
              min="0"
              max="100"
              v-model="audioSettings.musicVolume"
            >
            <div style="text-align: center; font-size: 12px; color: #6b7280; margin-top: 5px;">
              {{ audioSettings.musicVolume }}% (推荐音量)
            </div>
          </div>

          <div class="setting-group">
            <label class="setting-label">配音音量</label>
            <input
              type="range"
              class="setting-input"
              min="0"
              max="100"
              v-model="audioSettings.voiceVolume"
            >
            <div style="text-align: center; font-size: 12px; color: #6b7280; margin-top: 5px;">
              {{ audioSettings.voiceVolume }}% (推荐音量)
            </div>
          </div>
        </div>

        <!-- 输出设置 -->
        <div class="panel-section">
          <h3 class="panel-title">📤 输出设置</h3>
          
          <div class="export-settings">
            <div class="setting-group">
              <label class="setting-label">输出质量</label>
              <div class="quality-options">
                <div
                  v-for="quality in qualityOptions"
                  :key="quality.value"
                  :class="['quality-option', { selected: exportSettings.quality === quality.value }]"
                  @click="exportSettings.quality = quality.value"
                >
                  <div>
                    <div class="quality-name">{{ quality.name }}</div>
                    <div class="quality-details">{{ quality.resolution }} • {{ quality.description }}</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="setting-group">
              <label class="setting-label">输出格式</label>
              <select class="setting-input" v-model="exportSettings.format">
                <option value="mp4">MP4 (推荐)</option>
                <option value="mov">MOV</option>
                <option value="avi">AVI</option>
                <option value="gif">GIF</option>
              </select>
            </div>

            <div class="setting-group">
              <label class="setting-label">预计文件大小</label>
              <div style="padding: 10px; background: #f0f9ff; border-radius: 6px; text-align: center;">
                <div style="font-weight: 500; color: #0369a1;">约 {{ estimatedFileSize }}</div>
                <div style="font-size: 12px; color: #6b7280; margin-top: 2px;">
                  基于当前设置估算
                </div>
              </div>
            </div>
          </div>
        </div>
      </aside>
    </main>

    <!-- 底部操作区 -->
    <footer class="bottom-actions">
      <div class="action-group">
        <router-link to="/image-storyboard" class="action-btn secondary">
          ← 返回图像分镜
        </router-link>
        <button class="action-btn secondary" @click="saveProject">
          💾 保存项目
        </button>
      </div>

      <div class="action-group">
        <button class="action-btn secondary large" @click="previewVideo">
          👁️ 预览视频
        </button>
        <button class="action-btn primary large" @click="exportVideo" :disabled="isExporting">
          {{ isExporting ? '⏳ 生成中...' : '🚀 生成视频' }}
        </button>
      </div>
    </footer>

    <!-- 处理进度弹窗 -->
    <div v-if="showProcessing" class="processing-overlay">
      <div class="processing-modal">
        <div class="processing-icon">🎬</div>
        <div class="processing-title">正在生成视频</div>
        <div class="processing-text">AI正在自动合成你的创作内容...</div>
        <div class="processing-progress">
          <div class="processing-fill" :style="{ width: processingProgress + '%' }"></div>
        </div>
        <div class="processing-info">
          {{ processingStep }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'

export default {
  name: 'EditorView',
  setup() {
    const router = useRouter()
    
    // 响应式数据
    const projectName = ref('时间管理技巧分享')
    const isPlaying = ref(false)
    const currentTime = ref(8)
    const duration = ref(42)
    const isExporting = ref(false)
    const showProcessing = ref(false)
    const processingProgress = ref(0)
    const processingStep = ref('')
    const activeFilter = ref('all')
    const aspectRatio = ref('16:9')
    
    const videoPlayer = ref(null)
    const previewVideo = ref(null)
    
    // 时间轴数据
    const videoClips = ref([])
    const audioClips = ref([])
    const subtitleClips = ref([])
    const backgroundMusic = ref({ name: '轻松背景音乐' })
    
    const timelineFilters = ref([
      { key: 'all', label: '全部' },
      { key: 'video', label: '视频' },
      { key: 'audio', label: '音频' },
      { key: 'subtitle', label: '字幕' }
    ])
    
    // 设置数据
    const subtitleSettings = ref({
      style: 'simple',
      fontSize: 24,
      position: 'bottom'
    })
    
    const transitionSettings = ref({
      type: 'fade',
      duration: '0.5'
    })
    
    const audioSettings = ref({
      backgroundMusic: 'relaxed',
      musicVolume: 30,
      voiceVolume: 85
    })
    
    const exportSettings = ref({
      quality: '1080p',
      format: 'mp4'
    })
    
    // 选项数据
    const subtitleStyles = ref([
      { value: 'simple', label: '简约' },
      { value: 'outlined', label: '描边' },
      { value: 'shadow', label: '阴影' },
      { value: 'colored', label: '彩色' }
    ])
    
    const subtitlePositions = ref([
      { value: 'top', label: '顶部' },
      { value: 'bottom', label: '底部' }
    ])
    
    const transitionTypes = ref([
      { value: 'fade', label: '淡入淡出' },
      { value: 'cut', label: '切割' },
      { value: 'slide', label: '滑动' },
      { value: 'zoom', label: '缩放' }
    ])
    
    const qualityOptions = ref([
      {
        value: '720p',
        name: '720p 标清',
        resolution: '1280×720',
        description: '适合快速分享',
        fileSize: '8.5 MB'
      },
      {
        value: '1080p',
        name: '1080p 高清',
        resolution: '1920×1080',
        description: '推荐质量',
        fileSize: '15.2 MB'
      },
      {
        value: '4k',
        name: '4K 超清',
        resolution: '3840×2160',
        description: '最高质量',
        fileSize: '42.8 MB'
      }
    ])
    
    // 计算属性
    const totalShots = computed(() => videoClips.value.length)
    const totalDuration = computed(() => {
      return videoClips.value.reduce((sum, clip) => sum + clip.duration, 0)
    })
    const outputQuality = computed(() => exportSettings.value.quality)
    const estimatedFileSize = computed(() => {
      const quality = qualityOptions.value.find(q => q.value === exportSettings.value.quality)
      return quality?.fileSize || '15.2 MB'
    })
    
    // 生命周期
    onMounted(() => {
      initializeFromFinalShots()
    })
    
    // 方法
    const initializeFromFinalShots = () => {
      // 从localStorage获取最终分镜内容
      const finalShots = localStorage.getItem('vidspark_final_shots')
      if (finalShots) {
        try {
          const shots = JSON.parse(finalShots)
          
          // 生成视频片段
          videoClips.value = shots.map((shot, index) => ({
            id: `video_${index + 1}`,
            name: `第${index + 1}幕`,
            duration: shot.displayDuration || 8,
            url: shot.previewUrl
          }))
          
          // 生成音频片段
          audioClips.value = shots.map((shot, index) => ({
            id: `audio_${index + 1}`,
            name: `配音-${index + 1}`,
            duration: shot.displayDuration || 8,
            text: shot.audioText || ''
          }))
          
          // 生成字幕片段
          subtitleClips.value = shots.map((shot, index) => ({
            id: `subtitle_${index + 1}`,
            name: `字幕-${index + 1}`,
            duration: shot.displayDuration || 8,
            text: shot.audioText || ''
          }))
          
        } catch (error) {
          console.error('解析最终分镜内容失败:', error)
          createDefaultClips()
        }
      } else {
        createDefaultClips()
      }
    }
    
    const createDefaultClips = () => {
      const defaultClips = Array.from({ length: 5 }, (_, index) => ({
        id: `clip_${index + 1}`,
        name: `第${index + 1}幕`,
        duration: 8
      }))
      
      videoClips.value = [...defaultClips]
      audioClips.value = [...defaultClips]
      subtitleClips.value = [...defaultClips]
    }
    
    const formatTime = (seconds) => {
      const mins = Math.floor(seconds / 60)
      const secs = seconds % 60
      return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`
    }
    
    const togglePlay = () => {
      isPlaying.value = !isPlaying.value
      
      if (videoPlayer.value) {
        if (isPlaying.value) {
          videoPlayer.value.play()
        } else {
          videoPlayer.value.pause()
        }
      } else {
        // 模拟播放
        if (isPlaying.value) {
          const playInterval = setInterval(() => {
            if (!isPlaying.value) {
              clearInterval(playInterval)
              return
            }
            currentTime.value++
            if (currentTime.value >= duration.value) {
              currentTime.value = duration.value
              isPlaying.value = false
              clearInterval(playInterval)
            }
          }, 1000)
        }
      }
    }
    
    const updateTimeDisplay = () => {
      if (videoPlayer.value) {
        currentTime.value = Math.floor(videoPlayer.value.currentTime)
      }
    }
    
    const onVideoLoaded = () => {
      if (videoPlayer.value) {
        duration.value = Math.floor(videoPlayer.value.duration)
      }
    }
    
    const setTimelineFilter = (filter) => {
      activeFilter.value = filter
    }
    
    const showTrack = (trackType) => {
      return activeFilter.value === 'all' || activeFilter.value === trackType
    }
    
    const selectClip = (trackType, index) => {
      console.log(`选中${trackType}轨道的第${index + 1}个片段`)
      // 这里可以添加选中逻辑
    }
    
    const getSubtitleStyle = () => {
      const style = {
        fontSize: subtitleSettings.value.fontSize + 'px'
      }
      
      switch (subtitleSettings.value.style) {
        case 'outlined':
          style.textShadow = '1px 1px 0 #000, -1px -1px 0 #000, 1px -1px 0 #000, -1px 1px 0 #000'
          break
        case 'shadow':
          style.textShadow = '2px 2px 4px rgba(0,0,0,0.5)'
          break
        case 'colored':
          style.color = '#667eea'
          style.fontWeight = 'bold'
          break
        default:
          style.color = 'white'
      }
      
      return style
    }
    
    const saveProject = () => {
      // 保存项目设置到localStorage
      const projectData = {
        subtitleSettings: subtitleSettings.value,
        transitionSettings: transitionSettings.value,
        audioSettings: audioSettings.value,
        exportSettings: exportSettings.value,
        clips: {
          video: videoClips.value,
          audio: audioClips.value,
          subtitle: subtitleClips.value
        }
      }
      
      localStorage.setItem('vidspark_editor_settings', JSON.stringify(projectData))
      alert('项目已保存！所有设置和进度都已自动保存。')
    }
    
    const previewVideo = () => {
      alert('生成预览中...\n\n预览功能将在新窗口打开，显示当前设置下的视频效果。')
    }
    
    const exportVideo = async () => {
      if (!confirm(`确定要生成最终视频吗？\n\n预计处理时间: 2-3分钟\n文件大小: 约${estimatedFileSize.value}`)) {
        return
      }
      
      await startProcessing()
    }
    
    const startProcessing = async () => {
      showProcessing.value = true
      isExporting.value = true
      processingProgress.value = 0
      
      const steps = [
        '正在合成分镜 1/5...',
        '正在合成分镜 2/5...',
        '正在合成分镜 3/5...',
        '正在合成分镜 4/5...',
        '正在合成分镜 5/5...',
        '正在添加字幕...',
        '正在混合音频...',
        '正在添加转场效果...',
        '正在优化视频质量...',
        '正在生成最终文件...'
      ]
      
      for (let i = 0; i < steps.length; i++) {
        processingStep.value = steps[i]
        processingProgress.value = ((i + 1) / steps.length) * 100
        await new Promise(resolve => setTimeout(resolve, 800))
      }
      
      processingStep.value = '生成完成！正在准备下载...'
      
      setTimeout(() => {
        showProcessing.value = false
        isExporting.value = false
        alert(`🎉 视频生成成功！\n\n文件名: ${projectName.value}.${exportSettings.value.format}\n大小: ${estimatedFileSize.value}\n质量: ${outputQuality.value}\n\n视频已保存到"我的项目"，您现在可以下载或分享。`)
        
        // 跳转到项目页面
        router.push('/projects')
      }, 1500)
    }
    
    return {
      // 数据
      projectName,
      isPlaying,
      currentTime,
      duration,
      isExporting,
      showProcessing,
      processingProgress,
      processingStep,
      activeFilter,
      aspectRatio,
      videoPlayer,
      previewVideo,
      videoClips,
      audioClips,
      subtitleClips,
      backgroundMusic,
      timelineFilters,
      subtitleSettings,
      transitionSettings,
      audioSettings,
      exportSettings,
      subtitleStyles,
      subtitlePositions,
      transitionTypes,
      qualityOptions,
      
      // 计算属性
      totalShots,
      totalDuration,
      outputQuality,
      estimatedFileSize,
      
      // 方法
      formatTime,
      togglePlay,
      updateTimeDisplay,
      onVideoLoaded,
      setTimelineFilter,
      showTrack,
      selectClip,
      getSubtitleStyle,
      saveProject,
      previewVideo,
      exportVideo
    }
  }
}
</script>

<style scoped>
.container {
  max-width: 1800px;
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
  padding: 30px;
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

.project-stats {
  display: flex;
  justify-content: center;
  gap: 40px;
  margin-top: 20px;
}

.stat-item {
  text-align: center;
}

.stat-number {
  font-size: 28px;
  font-weight: bold;
  color: #667eea;
}

.stat-label {
  font-size: 14px;
  color: #6b7280;
  margin-top: 5px;
}

/* 主工作区 */
.editor-workspace {
  display: grid;
  grid-template-columns: 1fr 300px;
  gap: 0;
  min-height: 700px;
}

/* 左侧主编辑区 */
.main-editor {
  background: #f9fafb;
  display: flex;
  flex-direction: column;
}

/* 预览播放器 */
.video-preview {
  background: #000;
  aspect-ratio: 16/9;
  margin: 20px;
  border-radius: 15px;
  overflow: hidden;
  position: relative;
  box-shadow: 0 8px 30px rgba(0,0,0,0.3);
}

.preview-content {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.video-container {
  width: 100%;
  height: 100%;
}

.video-container video {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.preview-placeholder {
  text-align: center;
  font-size: 24px;
}

.preview-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(transparent, rgba(0,0,0,0.8));
  padding: 20px;
  color: white;
}

.play-controls {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 10px;
}

.play-button {
  width: 50px;
  height: 50px;
  background: #667eea;
  border: none;
  border-radius: 50%;
  color: white;
  font-size: 20px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.play-button:hover {
  background: #5a67d8;
  transform: scale(1.1);
}

.time-display {
  font-size: 14px;
  font-weight: 500;
}

.video-info {
  font-size: 12px;
  opacity: 0.8;
}

/* 时间轴 */
.timeline-container {
  background: white;
  padding: 20px;
  border-top: 1px solid #e5e7eb;
  flex: 1;
}

.timeline-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.timeline-title {
  font-size: 18px;
  font-weight: 600;
  color: #374151;
}

.timeline-controls {
  display: flex;
  gap: 10px;
}

.timeline-btn {
  padding: 8px 15px;
  border: 2px solid #e5e7eb;
  background: white;
  border-radius: 8px;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.timeline-btn:hover {
  border-color: #667eea;
  background: #f8fafc;
}

.timeline-btn.active {
  border-color: #667eea;
  background: #eef2ff;
  color: #667eea;
}

/* 时间轴轨道 */
.timeline-tracks {
  background: #f8fafc;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  padding: 15px;
  min-height: 200px;
}

.track {
  display: flex;
  align-items: center;
  margin-bottom: 15px;
  min-height: 40px;
}

.track-label {
  width: 80px;
  font-size: 13px;
  font-weight: 500;
  color: #6b7280;
  text-align: right;
  margin-right: 15px;
}

.track-content {
  flex: 1;
  display: flex;
  gap: 5px;
  background: #e5e7eb;
  border-radius: 6px;
  padding: 3px;
  min-height: 35px;
}

.timeline-clip {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  padding: 8px 12px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  gap: 5px;
}

.timeline-clip:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.timeline-clip.audio {
  background: linear-gradient(135deg, #10b981, #059669);
}

.timeline-clip.video {
  background: linear-gradient(135deg, #f59e0b, #d97706);
}

.timeline-clip.text {
  background: linear-gradient(135deg, #ef4444, #dc2626);
}

.timeline-clip.music {
  background: linear-gradient(135deg, #8b5cf6, #7c3aed);
  flex: 1;
}

/* 右侧设置面板 */
.settings-panel {
  background: white;
  border-left: 1px solid #e5e7eb;
  padding: 20px;
  overflow-y: auto;
}

.panel-section {
  margin-bottom: 25px;
}

.panel-title {
  font-size: 16px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 15px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.setting-group {
  margin-bottom: 20px;
}

.setting-label {
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  margin-bottom: 8px;
  display: block;
}

.setting-input {
  width: 100%;
  padding: 10px 12px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 14px;
  transition: border-color 0.2s ease;
}

.setting-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.setting-options {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
}

.setting-option {
  padding: 10px;
  border: 2px solid #e5e7eb;
  background: white;
  border-radius: 8px;
  text-align: center;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.setting-option:hover {
  border-color: #667eea;
}

.setting-option.selected {
  border-color: #667eea;
  background: #eef2ff;
  color: #667eea;
  font-weight: 500;
}

/* 字幕样式预览 */
.subtitle-preview {
  background: #000;
  color: white;
  padding: 20px;
  border-radius: 8px;
  text-align: center;
  margin-top: 10px;
  font-size: 16px;
  position: relative;
}

.subtitle-text {
  position: relative;
  z-index: 2;
}

/* 输出设置 */
.export-settings {
  background: #f8fafc;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  padding: 15px;
}

.quality-options {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.quality-option {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 12px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.quality-option:hover {
  border-color: #667eea;
}

.quality-option.selected {
  border-color: #667eea;
  background: #eef2ff;
}

.quality-name {
  font-weight: 500;
  color: #374151;
}

.quality-details {
  font-size: 12px;
  color: #6b7280;
}

/* 底部操作区 */
.bottom-actions {
  background: white;
  border-top: 1px solid #e5e7eb;
  padding: 20px 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.action-group {
  display: flex;
  gap: 15px;
  align-items: center;
}

.action-btn {
  padding: 12px 24px;
  border: none;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
}

.action-btn.primary {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
}

.action-btn.primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
}

.action-btn.secondary {
  background: #f3f4f6;
  color: #374151;
  border: 2px solid #e5e7eb;
}

.action-btn.secondary:hover {
  background: #e5e7eb;
}

.action-btn.large {
  padding: 15px 30px;
  font-size: 16px;
}

.action-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none !important;
}

/* 处理进度条 */
.processing-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.processing-modal {
  background: white;
  padding: 40px;
  border-radius: 20px;
  text-align: center;
  max-width: 400px;
  width: 90%;
}

.processing-icon {
  font-size: 64px;
  margin-bottom: 20px;
  animation: spin 2s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.processing-title {
  font-size: 24px;
  font-weight: bold;
  color: #374151;
  margin-bottom: 10px;
}

.processing-text {
  font-size: 16px;
  color: #6b7280;
  margin-bottom: 20px;
}

.processing-progress {
  width: 100%;
  height: 8px;
  background: #e5e7eb;
  border-radius: 4px;
  overflow: hidden;
  margin-bottom: 15px;
}

.processing-fill {
  height: 100%;
  background: linear-gradient(90deg, #10b981, #059669);
  width: 0%;
  transition: width 0.3s ease;
}

.processing-info {
  font-size: 14px;
  color: #6b7280;
}

/* 响应式设计 */
@media (max-width: 1200px) {
  .editor-workspace {
    grid-template-columns: 1fr;
    grid-template-rows: 1fr auto;
  }
  
  .settings-panel {
    border-left: none;
    border-top: 1px solid #e5e7eb;
    max-height: 400px;
  }
}

@media (max-width: 768px) {
  .container {
    margin: 10px;
    border-radius: 15px;
  }
  
  .video-preview {
    margin: 15px;
  }
  
  .timeline-container {
    padding: 15px;
  }
  
  .bottom-actions {
    flex-direction: column;
    gap: 15px;
  }
  
  .action-group {
    width: 100%;
    justify-content: center;
  }
}
</style>