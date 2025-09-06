<template>
  <div class="editor-container">
    <!-- 顶部进度条 -->
    <header class="editor-header">
      <div class="progress-section">
        <h1>🎞️ 最后一步：合成影片</h1>
        <p>AI会自动把所有片段拼接成完整影片，添加字幕和背景音乐</p>
        <div v-if="isProcessing" class="processing-status">
          <div class="status-indicator">
            <div class="spinner"></div>
            <span>{{ currentProcessingStep }}</span>
          </div>
          <div class="progress-bar">
            <div class="progress-fill" :style="{ width: processingProgress + '%' }"></div>
          </div>
          <span class="progress-text">{{ processingProgress }}% 完成 | 预计还需 {{ estimatedTime }} 分钟</span>
        </div>
      </div>
    </header>

    <!-- 主要工作区 -->
    <main class="editor-workspace">
      <!-- 视频预览区 -->
      <div class="video-preview-section">
        <div class="video-preview-large">
          <video 
            v-if="finalVideoUrl" 
            :src="finalVideoUrl" 
            controls 
            width="100%"
            class="preview-video"
          >
            您的浏览器不支持视频播放
          </video>
          <div v-else class="preview-placeholder">
            <div class="placeholder-content">
              <div class="placeholder-icon">🎬</div>
              <h3>影片预览</h3>
              <p>点击下方"开始合成"按钮生成最终影片</p>
            </div>
          </div>
        </div>
        
        <div v-if="finalVideoUrl" class="preview-info">
          <div class="info-grid">
            <div class="info-item">
              <span class="info-label">总时长:</span>
              <span class="info-value">{{ totalDuration }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">片段数:</span>
              <span class="info-value">{{ totalShots }} 个</span>
            </div>
            <div class="info-item">
              <span class="info-label">字幕:</span>
              <span class="info-value">{{ subtitleEnabled ? '已添加' : '无字幕' }}</span>
            </div>
            <div class="info-item">
              <span class="info-label">背景音乐:</span>
              <span class="info-value">{{ backgroundMusic || '无音乐' }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- 设置面板 -->
      <div class="settings-panel">
        <div class="settings-section">
          <h3>🎨 字幕设置</h3>
          <div class="setting-item">
            <label>字幕样式:</label>
            <select v-model="subtitleStyle" class="big-select">
              <option value="simple">简洁白字</option>
              <option value="bordered">彩色边框</option>
              <option value="shadow">阴影效果</option>
              <option value="none">不要字幕</option>
            </select>
          </div>
          
          <div v-if="subtitleStyle !== 'none'" class="setting-item">
            <label>字幕位置:</label>
            <select v-model="subtitlePosition" class="big-select">
              <option value="bottom">底部</option>
              <option value="center">居中</option>
              <option value="top">顶部</option>
            </select>
          </div>
        </div>

        <div class="settings-section">
          <h3>🎵 音频设置</h3>
          <div class="setting-item">
            <label>背景音乐:</label>
            <select v-model="backgroundMusic" class="big-select">
              <option value="">不要背景音乐</option>
              <option value="happy">轻松愉快</option>
              <option value="professional">专业严肃</option>
              <option value="inspiring">激励向上</option>
            </select>
          </div>
          
          <div v-if="backgroundMusic" class="setting-item">
            <label>音乐音量:</label>
            <input 
              type="range" 
              min="0" 
              max="100" 
              v-model="musicVolume"
              class="volume-slider"
            >
            <span class="volume-text">{{ musicVolume }}%</span>
          </div>
        </div>

        <div class="settings-section">
          <h3>📱 影片设置</h3>
          <div class="setting-item">
            <label>影片质量:</label>
            <select v-model="videoQuality" class="big-select">
              <option value="720p">高清 720p (推荐)</option>
              <option value="480p">标清 480p (快速)</option>
              <option value="1080p">超高清 1080p (慢速)</option>
            </select>
          </div>
          
          <div class="setting-item">
            <label>影片比例:</label>
            <select v-model="aspectRatio" class="big-select">
              <option value="16:9">横屏 16:9</option>
              <option value="9:16">竖屏 9:16</option>
              <option value="1:1">正方形 1:1</option>
            </select>
          </div>
        </div>
      </div>
    </main>

    <!-- 底部操作区 -->
    <footer class="editor-footer">
      <button class="back-btn" @click="goBack">
        ← 返回导演区
      </button>
      
      <div class="cost-section">
        <div class="cost-info">
          <span class="cost-label">💰 本次制作需要:</span>
          <span class="cost-value">{{ totalCost }} 积分</span>
        </div>
        <div class="balance-info">
          <span class="balance-label">你还有:</span>
          <span class="balance-value">{{ userCredits }} 积分</span>
        </div>
      </div>

      <div class="action-buttons">
        <button 
          v-if="!finalVideoUrl"
          class="mega-btn primary"
          @click="startComposition"
          :disabled="isProcessing || userCredits < totalCost"
        >
          <span v-if="isProcessing">⏳ 正在合成中...</span>
          <span v-else>✨ 开始合成最终影片</span>
        </button>
        
        <div v-else class="final-actions">
          <button class="action-btn" @click="previewFullscreen">
            👁️ 全屏预览
          </button>
          <button class="action-btn" @click="downloadVideo">
            📥 下载影片
          </button>
          <button class="action-btn" @click="shareVideo">
            📤 分享影片
          </button>
          <button class="mega-btn success" @click="startNewProject">
            🎬 制作新影片
          </button>
        </div>
      </div>
    </footer>

    <!-- 处理进度弹窗 -->
    <div v-if="isProcessing" class="processing-modal">
      <div class="modal-content">
        <div class="processing-animation">
          <div class="film-reel">🎬</div>
          <div class="processing-dots">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
          </div>
        </div>
        <h3>正在制作你的影片...</h3>
        <p>{{ currentProcessingStep }}</p>
        <div class="progress-details">
          <div class="progress-bar">
            <div class="progress-fill" :style="{ width: processingProgress + '%' }"></div>
          </div>
          <span class="progress-percentage">{{ processingProgress }}%</span>
        </div>
        <div class="processing-tips">
          <p>💡 制作期间你可以：</p>
          <ul>
            <li>🍵 泡杯茶放松一下</li>
            <li>📱 刷刷朋友圈</li>
            <li>📝 想想下个作品的创意</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

// 基础数据
const userCredits = ref(125)
const totalShots = ref(4)

// 设置选项
const subtitleStyle = ref('simple')
const subtitlePosition = ref('bottom')
const backgroundMusic = ref('happy')
const musicVolume = ref(30)
const videoQuality = ref('720p')
const aspectRatio = ref('16:9')

// 处理状态
const isProcessing = ref(false)
const processingProgress = ref(0)
const currentProcessingStep = ref('')
const estimatedTime = ref(0)
const finalVideoUrl = ref('')

// 处理步骤
const processingSteps = [
  '正在收集所有片段...',
  '正在拼接影片片段...',
  '正在生成字幕文件...',
  '正在添加背景音乐...',
  '正在调整音频音量...',
  '正在渲染最终影片...',
  '正在优化影片质量...',
  '影片制作完成！'
]

// 计算属性
const totalDuration = computed(() => {
  // 模拟计算总时长
  return '2分30秒'
})

const subtitleEnabled = computed(() => subtitleStyle.value !== 'none')

const totalCost = computed(() => {
  let cost = 5 // 基础合成费用
  if (subtitleEnabled.value) cost += 2
  if (backgroundMusic.value) cost += 2
  if (videoQuality.value === '1080p') cost += 3
  return cost
})

// 开始合成
const startComposition = async () => {
  if (isProcessing.value) return
  
  if (userCredits.value < totalCost.value) {
    alert('积分不足，请先充值！')
    return
  }

  console.log('✨ [开始合成] 启动影片合成流程')
  isProcessing.value = true
  processingProgress.value = 0
  estimatedTime.value = 3

  try {
    for (let i = 0; i < processingSteps.length; i++) {
      currentProcessingStep.value = processingSteps[i]
      console.log(`📋 [处理步骤] ${currentProcessingStep.value}`)
      
      // 模拟处理时间
      const stepDuration = i === processingSteps.length - 1 ? 500 : 3000
      await simulateProcessing(stepDuration, i)
      
      processingProgress.value = Math.round(((i + 1) / processingSteps.length) * 100)
      estimatedTime.value = Math.max(0, estimatedTime.value - 0.5)
    }

    // 生成最终视频URL
    finalVideoUrl.value = '/api/video/final-composition.mp4'
    
    // 扣除积分
    userCredits.value -= totalCost.value
    
    console.log('✅ [合成完成] 影片制作成功')
    alert('🎉 影片制作完成！')
    
  } catch (error) {
    console.error('❌ [合成失败]', error)
    alert('制作失败，请重试')
  } finally {
    isProcessing.value = false
  }
}

// 模拟处理过程
const simulateProcessing = (duration: number, step: number) => {
  return new Promise((resolve) => {
    const interval = setInterval(() => {
      // 可以在这里添加更详细的进度更新
    }, 100)
    
    setTimeout(() => {
      clearInterval(interval)
      resolve(true)
    }, duration)
  })
}

// 全屏预览
const previewFullscreen = () => {
  console.log('👁️ [全屏预览] 开启全屏模式')
  const video = document.querySelector('.preview-video') as HTMLVideoElement
  if (video && video.requestFullscreen) {
    video.requestFullscreen()
  }
}

// 下载视频
const downloadVideo = () => {
  console.log('📥 [下载影片] 开始下载')
  // 创建下载链接
  const link = document.createElement('a')
  link.href = finalVideoUrl.value
  link.download = '我的AI影片.mp4'
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

// 分享视频
const shareVideo = () => {
  console.log('📤 [分享影片] 开启分享')
  
  if (navigator.share) {
    navigator.share({
      title: '我用AI制作的影片',
      text: '快来看看我用Vidspark制作的影片！',
      url: window.location.href
    }).catch(console.error)
  } else {
    // 复制链接到剪贴板
    navigator.clipboard.writeText(window.location.href).then(() => {
      alert('链接已复制到剪贴板！')
    }).catch(() => {
      alert('分享链接：' + window.location.href)
    })
  }
}

// 开始新项目
const startNewProject = () => {
  console.log('🎬 [新项目] 开始制作新影片')
  if (confirm('确定要制作新影片吗？当前设置将被重置。')) {
    router.push('/')
  }
}

// 返回导演区
const goBack = () => {
  if (isProcessing.value) {
    if (!confirm('正在制作中，确定要返回吗？进度将丢失。')) {
      return
    }
  }
  router.push('/director')
}

// 组件挂载
onMounted(() => {
  console.log('🎞️ [AI剪辑区] 页面加载完成')
  // 可以在这里加载项目数据
})
</script>

<style scoped>
.editor-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  display: flex;
  flex-direction: column;
}

/* 顶部进度条 */
.editor-header {
  background: white;
  padding: 32px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  text-align: center;
}

.progress-section h1 {
  margin: 0 0 8px 0;
  color: #1f2937;
  font-size: 28px;
}

.progress-section p {
  margin: 0 0 24px 0;
  color: #6b7280;
  font-size: 16px;
}

.processing-status {
  max-width: 400px;
  margin: 0 auto;
}

.status-indicator {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  margin-bottom: 16px;
}

.spinner {
  width: 20px;
  height: 20px;
  border: 2px solid #e5e7eb;
  border-top-color: #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

.progress-bar {
  width: 100%;
  height: 8px;
  background: #e5e7eb;
  border-radius: 4px;
  overflow: hidden;
  margin-bottom: 8px;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  transition: width 0.3s ease;
}

.progress-text {
  font-size: 14px;
  color: #6b7280;
}

/* 主工作区 */
.editor-workspace {
  flex: 1;
  display: flex;
  gap: 32px;
  padding: 32px;
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
}

.video-preview-section {
  flex: 2;
}

.video-preview-large {
  background: white;
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  margin-bottom: 20px;
}

.preview-video {
  width: 100%;
  border-radius: 12px;
}

.preview-placeholder {
  width: 100%;
  height: 300px;
  background: #f3f4f6;
  border: 2px dashed #d1d5db;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.placeholder-content {
  text-align: center;
}

.placeholder-icon {
  font-size: 48px;
  margin-bottom: 16px;
}

.placeholder-content h3 {
  margin: 0 0 8px 0;
  color: #374151;
  font-size: 20px;
}

.placeholder-content p {
  margin: 0;
  color: #6b7280;
}

.preview-info {
  background: white;
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}

.info-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px;
  background: #f8fafc;
  border-radius: 8px;
}

.info-label {
  font-size: 14px;
  color: #6b7280;
}

.info-value {
  font-size: 14px;
  color: #1f2937;
  font-weight: 500;
}

/* 设置面板 */
.settings-panel {
  flex: 1;
  background: white;
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  height: fit-content;
}

.settings-section {
  margin-bottom: 32px;
}

.settings-section:last-child {
  margin-bottom: 0;
}

.settings-section h3 {
  margin: 0 0 16px 0;
  color: #1f2937;
  font-size: 18px;
}

.setting-item {
  margin-bottom: 16px;
}

.setting-item label {
  display: block;
  margin-bottom: 8px;
  color: #374151;
  font-size: 14px;
  font-weight: 500;
}

.big-select {
  width: 100%;
  padding: 12px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 14px;
  background: white;
  outline: none;
  transition: border-color 0.2s ease;
}

.big-select:focus {
  border-color: #667eea;
}

.volume-slider {
  width: calc(100% - 50px);
  margin-right: 12px;
}

.volume-text {
  font-size: 14px;
  color: #6b7280;
  min-width: 35px;
}

/* 底部操作区 */
.editor-footer {
  background: white;
  padding: 24px 32px;
  box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 20px;
}

.cost-section {
  text-align: center;
}

.cost-info, .balance-info {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 4px;
}

.cost-label, .balance-label {
  font-size: 14px;
  color: #6b7280;
}

.cost-value {
  font-size: 16px;
  color: #dc2626;
  font-weight: 600;
}

.balance-value {
  font-size: 16px;
  color: #059669;
  font-weight: 600;
}

.action-buttons {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}

.mega-btn {
  padding: 16px 32px;
  border: none;
  border-radius: 12px;
  font-size: 18px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.mega-btn.primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.mega-btn.success {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
}

.mega-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102,126,234,0.3);
}

.mega-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.final-actions {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
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

.action-btn:hover {
  border-color: #667eea;
  background: #f8fafc;
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

/* 处理进度弹窗 */
.processing-modal {
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

.modal-content {
  background: white;
  padding: 40px;
  border-radius: 20px;
  max-width: 400px;
  width: 90%;
  text-align: center;
}

.processing-animation {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 16px;
  margin-bottom: 24px;
}

.film-reel {
  font-size: 48px;
  animation: rotate 2s linear infinite;
}

@keyframes rotate {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.processing-dots {
  display: flex;
  gap: 4px;
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

.modal-content h3 {
  margin: 0 0 12px 0;
  color: #1f2937;
  font-size: 20px;
}

.modal-content p {
  margin: 0 0 24px 0;
  color: #6b7280;
}

.progress-details {
  margin-bottom: 24px;
}

.progress-percentage {
  font-size: 14px;
  color: #6b7280;
  margin-top: 8px;
  display: block;
}

.processing-tips {
  background: #f8fafc;
  padding: 16px;
  border-radius: 8px;
  text-align: left;
}

.processing-tips p {
  margin: 0 0 8px 0;
  font-weight: 500;
  color: #374151;
}

.processing-tips ul {
  margin: 0;
  padding-left: 20px;
}

.processing-tips li {
  margin-bottom: 4px;
  color: #6b7280;
  font-size: 14px;
}

/* 响应式设计 */
@media (max-width: 1024px) {
  .editor-workspace {
    flex-direction: column;
  }
  
  .info-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .editor-workspace {
    padding: 20px;
  }
  
  .editor-footer {
    flex-direction: column;
    text-align: center;
  }
  
  .final-actions {
    justify-content: center;
  }
  
  .action-btn, .mega-btn {
    width: 100%;
  }
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
