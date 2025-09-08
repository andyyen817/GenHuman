<template>
  <div class="director-container">
    <!-- 顶部控制栏 -->
    <header class="director-header">
      <div class="project-info">
        <h1>🎬 AI导演区</h1>
        <p>为你的剧本制作影片片段</p>
      </div>
      <div class="header-stats">
        <span class="stat">总共 {{ totalShots }} 个片段</span>
        <span class="stat">{{ completedShots }} 个已完成</span>
      </div>
    </header>

    <!-- 主要工作区（左侧设定面板 + 右侧分镜矩阵） -->
    <main class="director-workspace">
      <!-- 左侧全局设定面板 -->
      <aside class="global-settings-panel">
        <!-- 基本设置 -->
        <div class="settings-section">
          <h3>📐 基本设置</h3>
          <div class="setting-item">
            <button 
              class="mega-btn primary" 
              @click="generateAllShots"
              :disabled="isGeneratingAll"
            >
              <span v-if="isGeneratingAll">🎬 正在制作中...</span>
              <span v-else>🎬 开始制作所有片段</span>
            </button>
            <p class="setting-desc">点击后AI会自动制作所有影片片段</p>
          </div>
        </div>

        <!-- 风格选择 -->
        <div class="settings-section">
          <h3>🎨 选择影片风格：</h3>
          <div class="style-options">
            <button 
              v-for="style in styleOptions" 
              :key="style.id"
              class="style-btn"
              :class="{ active: selectedStyle === style.id }"
              @click="selectedStyle = style.id"
            >
              {{ style.icon }} {{ style.name }}
            </button>
          </div>
        </div>

        <!-- 背景音乐 -->
        <div class="settings-section">
          <h3>🎵 背景音乐：</h3>
          <select v-model="selectedMusic" class="big-select">
            <option value="happy">轻松愉快</option>
            <option value="professional">专业严肃</option>
            <option value="inspiring">激励向上</option>
            <option value="none">不要音乐</option>
          </select>
        </div>

        <!-- 快速帮助 -->
        <div class="settings-section">
          <h3>💡 快速帮助</h3>
          <div class="help-items">
            <button class="help-btn" @click="showHelp('howto')">
              ❓ 如何使用？
            </button>
            <button class="help-btn" @click="showHelp('demo')">
              🎥 操作示范视频
            </button>
            <button class="help-btn" @click="showHelp('faq')">
              📋 常见问题
            </button>
          </div>
        </div>
      </aside>

      <!-- 右侧分镜矩阵区 -->
      <section class="storyboard-matrix">
        <div class="shots-grid">
          <!-- 分镜卡片（简化版，更直观） -->
          <div 
            v-for="shot in shots" 
            :key="shot.id"
            class="shot-card-large"
            :class="shot.status"
          >
            <!-- 卡片头部 -->
            <header class="shot-header">
              <div class="shot-title">🎬 {{ shot.title }}</div>
              <div class="shot-duration">
                <input 
                  type="range" 
                  min="1" 
                  max="10" 
                  v-model="shot.duration"
                  class="duration-slider"
                >
                <span class="duration-text">{{ shot.duration }}秒</span>
              </div>
              <div class="shot-status">
                <span class="status-indicator" :class="shot.status">
                  {{ getStatusText(shot.status) }}
                </span>
              </div>
            </header>
            
            <!-- 预览图片区（放大显示） -->
            <section class="shot-visual">
              <div class="visual-container">
                <img 
                  v-if="shot.preview" 
                  :src="shot.preview" 
                  :alt="`${shot.title}预览`"
                  class="preview-image"
                >
                <div v-else class="preview-placeholder">
                  <div class="placeholder-icon">📸</div>
                  <p>点击生成预览</p>
                </div>
                
                <!-- 生成进度覆盖层 -->
                <div v-if="shot.status === 'generating'" class="progress-overlay">
                  <div class="progress-animation">
                    <div class="spinner"></div>
                  </div>
                  <span class="progress-text">正在生成... {{ shot.progress || 0 }}%</span>
                </div>
              </div>
              
              <div class="visual-actions">
                <button 
                  class="action-btn"
                  @click="regenerateShot(shot)"
                  :disabled="shot.status === 'generating'"
                >
                  🔄 重新生成
                </button>
                <button 
                  class="action-btn"
                  @click="previewShot(shot)"
                  :disabled="!shot.preview"
                >
                  👁️ 预览
                </button>
              </div>
            </section>
            
            <!-- 🆕 文案区（内联编辑） -->
            <section class="shot-text">
              <!-- 非编辑状态：显示文案 -->
              <div v-if="editingShot?.id !== shot.id" class="text-display">
                <div 
                  class="text-content editable-text"
                  @click="startEditText(shot)"
                  :title="'点击编辑文案'"
                >
                  {{ shot.text }}
                  <div class="edit-hint">
                    <i class="fas fa-edit"></i>
                    点击编辑
                  </div>
                </div>
                <span class="char-count">{{ shot.text.length }}/200字</span>
              </div>
              
              <!-- 编辑状态：显示输入框 -->
              <div v-else class="text-editor">
                <textarea 
                  v-model="tempTextValue"
                  class="text-input"
                  placeholder="输入分镜文案..."
                  @keydown.enter.ctrl="saveTextEdit(shot)"
                  @keydown.esc="cancelTextEdit"
                  rows="3"
                  maxlength="200"
                  ref="textEditorRef"
                ></textarea>
                <div class="editor-actions">
                  <button class="save-btn" @click="saveTextEdit(shot)">
                    ✅ 保存
                  </button>
                  <button class="cancel-btn" @click="cancelTextEdit">
                    ❌ 取消
                  </button>
                  <span class="char-count">{{ tempTextValue.length }}/200字</span>
                </div>
                <div class="editor-tips">
                  <small>💡 Ctrl+Enter保存，Esc取消</small>
                </div>
              </div>
            </section>
            
            <!-- 音频区（简化） -->
            <section class="shot-audio">
              <div class="audio-player">
                <button 
                  class="play-btn"
                  @click="toggleAudio(shot)"
                  :disabled="!shot.audio"
                >
                  {{ shot.isPlaying ? '⏸️' : '▶️' }}
                </button>
                <div class="audio-info">
                  <span v-if="shot.audio">音频时长: {{ shot.audioDuration || '0:03' }}</span>
                  <span v-else>未生成音频</span>
                </div>
              </div>
              <div class="audio-actions">
                <button 
                  class="action-btn"
                  @click="regenerateAudio(shot)"
                  :disabled="shot.status === 'generating'"
                >
                  🎤 重新录音
                </button>
              </div>
            </section>
            
            <!-- 操作区（简化为大按钮） -->
            <footer class="shot-actions">
              <button class="action-btn" @click="copyShot(shot)">
                📋 复制
              </button>
              <button class="action-btn danger" @click="deleteShot(shot)">
                🗑️ 删除
              </button>
              <button class="action-btn" @click="moveShot(shot, 'up')" :disabled="shot.id === 1">
                ⬆️ 上移
              </button>
              <button class="action-btn" @click="moveShot(shot, 'down')" :disabled="shot.id === shots.length">
                ⬇️ 下移
              </button>
            </footer>
          </div>
        </div>
        
        <!-- 添加新片段按钮 -->
        <div class="add-shot-card" @click="addNewShot">
          <div class="add-icon">➕</div>
          <p>添加新片段</p>
        </div>
      </section>
    </main>

    <!-- 底部操作栏 -->
    <footer class="director-footer">
      <button class="back-btn" @click="goBack">
        ← 返回编剧区
      </button>
      <div class="progress-info">
        <span>进度: {{ completedShots }}/{{ totalShots }} 完成</span>
        <div class="progress-bar">
          <div class="progress-fill" :style="{ width: progressPercentage + '%' }"></div>
        </div>
      </div>
      <button 
        class="next-btn" 
        @click="goToEditor"
        :disabled="completedShots < totalShots"
      >
        下一步：影片剪辑 →
      </button>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, nextTick } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

// 状态管理
const isGeneratingAll = ref(false)
const selectedStyle = ref('cartoon')
const selectedMusic = ref('happy')

// 🆕 内联编辑状态管理
const editingShot = ref(null) // 当前正在编辑的shot
const tempTextValue = ref('') // 临时编辑的文本值

// 风格选项
const styleOptions = reactive([
  { id: 'cartoon', name: '卡通风格', icon: '😊' },
  { id: 'realistic', name: '真实风格', icon: '📷' },
  { id: 'business', name: '商务风格', icon: '💼' }
])

// 🆕 动态分镜数据（从编剧页面同步）
const shots = reactive([])

// 🆕 初始化分镜数据的函数
const initializeShotsFromScript = () => {
  console.log('🎬 [分镜同步] 开始初始化分镜数据')
  
  // 尝试从localStorage获取脚本内容
  const savedScript = localStorage.getItem('vidspark_script_content')
  let scriptLines = []
  
  if (savedScript) {
    console.log('📝 [分镜同步] 找到保存的脚本内容')
    // 解析脚本，提取"第X幕"的内容
    scriptLines = savedScript.split('\n').filter(line => line.trim().startsWith('第'))
  }
  
  // 如果没有脚本内容，使用默认示例
  if (scriptLines.length === 0) {
    console.log('📝 [分镜同步] 使用默认示例脚本（5幕）')
    scriptLines = [
      '第一幕：大家好，我是时间管理小助手，今天要和大家分享几个超实用的时间管理技巧',
      '第二幕：首先是番茄工作法，把工作分成25分钟的小块，这样能保持专注力',
      '第三幕：接下来是优先级排序，把重要紧急的事情放在第一位，避免被琐事干扰',
      '第四幕：最后要记住，时间管理不是为了忙碌，而是为了有时间做真正重要的事情',
      '第五幕：希望这些方法对大家有帮助，记得点赞关注注我！'
    ]
  }
  
  // 清空现有分镜
  shots.splice(0, shots.length)
  
  // 根据脚本内容创建分镜卡片
  scriptLines.forEach((line, index) => {
    const shotId = index + 1
    const text = line.replace(/^第.+?[：:]\s*/, '') // 移除"第X幕："前缀
    
    const newShot = {
      id: shotId,
      title: `Shot ${shotId}`,
      text: text,
      duration: 3 + Math.floor(Math.random() * 3), // 3-5秒随机时长
      status: shotId === 1 ? 'completed' : 'pending', // 第一个默认完成
      preview: shotId === 1 ? '/api/placeholder/300/200' : null,
      audio: shotId === 1 ? '/api/audio/shot1.mp3' : null,
      audioDuration: shotId === 1 ? '0:04' : null,
      progress: shotId === 1 ? 100 : 0,
      isPlaying: false
    }
    
    shots.push(newShot)
  })
  
  console.log(`✅ [分镜同步] 成功创建 ${shots.length} 个分镜卡片`)
}

// 计算属性
const totalShots = computed(() => shots.length)
const completedShots = computed(() => shots.filter(s => s.status === 'completed').length)
const progressPercentage = computed(() => 
  totalShots.value > 0 ? Math.round((completedShots.value / totalShots.value) * 100) : 0
)

// 获取状态文本
const getStatusText = (status: string) => {
  const statusMap = {
    'pending': '⏳ 等待生成',
    'generating': '🔄 生成中',
    'completed': '✅ 已完成',
    'failed': '❌ 生成失败'
  }
  return statusMap[status] || '未知状态'
}

// 生成所有片段
const generateAllShots = async () => {
  if (isGeneratingAll.value) return
  
  console.log('🎬 [批量生成] 开始生成所有片段')
  isGeneratingAll.value = true
  
  try {
    for (const shot of shots) {
      if (shot.status !== 'completed') {
        await generateSingleShot(shot)
      }
    }
    console.log('✅ [批量生成] 所有片段生成完成')
  } catch (error) {
    console.error('❌ [批量生成] 生成失败:', error)
  } finally {
    isGeneratingAll.value = false
  }
}

// 生成单个片段
const generateSingleShot = async (shot: any) => {
  shot.status = 'generating'
  shot.progress = 0
  
  console.log(`🎬 [单个生成] 开始生成 ${shot.title}`)
  
  // 模拟生成过程
  for (let i = 0; i <= 100; i += 10) {
    shot.progress = i
    await new Promise(resolve => setTimeout(resolve, 200))
  }
  
  // 模拟生成结果
  shot.preview = `/api/placeholder/300/200?shot=${shot.id}`
  shot.audio = `/api/audio/shot${shot.id}.mp3`
  shot.audioDuration = '0:04'
  shot.status = 'completed'
  
  console.log(`✅ [单个生成] ${shot.title} 生成完成`)
}

// 重新生成片段
const regenerateShot = async (shot: any) => {
  console.log(`🔄 [重新生成] ${shot.title}`)
  await generateSingleShot(shot)
}

// 预览片段
const previewShot = (shot: any) => {
  console.log(`👁️ [预览] ${shot.title}`)
  // TODO: 实现预览功能
  alert(`预览 ${shot.title}`)
}

// 🆕 开始内联编辑文案
const startEditText = (shot: any) => {
  console.log(`✏️ [内联编辑] 开始编辑 ${shot.title} 的文案`)
  editingShot.value = shot
  tempTextValue.value = shot.text
  
  // 下一个tick后聚焦到输入框
  nextTick(() => {
    const textEditor = document.querySelector('.text-input') as HTMLTextAreaElement
    if (textEditor) {
      textEditor.focus()
      textEditor.select()
    }
  })
}

// 🆕 保存文案编辑
const saveTextEdit = async (shot: any) => {
  if (tempTextValue.value.trim() && tempTextValue.value !== shot.text) {
    const oldText = shot.text
    shot.text = tempTextValue.value.trim()
    console.log(`💾 [内联编辑] 保存文案更改: "${oldText}" → "${shot.text}"`)
    
    // 重新生成音频
    await regenerateAudio(shot)
  }
  
  // 退出编辑状态
  editingShot.value = null
  tempTextValue.value = ''
}

// 🆕 取消文案编辑
const cancelTextEdit = () => {
  console.log(`❌ [内联编辑] 取消编辑`)
  editingShot.value = null
  tempTextValue.value = ''
}

// 播放/暂停音频
const toggleAudio = (shot: any) => {
  if (!shot.audio) return
  
  // 停止其他正在播放的音频
  shots.forEach(s => s.isPlaying = false)
  shot.isPlaying = !shot.isPlaying
  
  console.log(`${shot.isPlaying ? '▶️' : '⏸️'} [音频] ${shot.title}`)
}

// 重新生成音频
const regenerateAudio = async (shot: any) => {
  console.log(`🎤 [重新录音] ${shot.title}`)
  // TODO: 实现音频重新生成
  await new Promise(resolve => setTimeout(resolve, 1000))
  shot.audio = `/api/audio/shot${shot.id}-new.mp3`
}

// 复制片段
const copyShot = (shot: any) => {
  const newShot = { 
    ...shot, 
    id: shots.length + 1,
    title: `Shot ${shots.length + 1}`,
    status: 'pending',
    preview: null,
    audio: null
  }
  shots.push(newShot)
  console.log(`📋 [复制] 复制了 ${shot.title}`)
}

// 删除片段
const deleteShot = (shot: any) => {
  if (confirm(`确定要删除 ${shot.title} 吗？`)) {
    const index = shots.findIndex(s => s.id === shot.id)
    if (index > -1) {
      shots.splice(index, 1)
      console.log(`🗑️ [删除] 删除了 ${shot.title}`)
    }
  }
}

// 移动片段
const moveShot = (shot: any, direction: 'up' | 'down') => {
  const currentIndex = shots.findIndex(s => s.id === shot.id)
  const targetIndex = direction === 'up' ? currentIndex - 1 : currentIndex + 1
  
  if (targetIndex >= 0 && targetIndex < shots.length) {
    [shots[currentIndex], shots[targetIndex]] = [shots[targetIndex], shots[currentIndex]]
    console.log(`↕️ [移动] ${shot.title} ${direction === 'up' ? '上移' : '下移'}`)
  }
}

// 添加新片段
const addNewShot = () => {
  const newShot = {
    id: shots.length + 1,
    title: `Shot ${shots.length + 1}`,
    text: '在这里输入新片段的文案...',
    duration: 3,
    status: 'pending',
    preview: null,
    audio: null,
    audioDuration: null,
    progress: 0,
    isPlaying: false
  }
  shots.push(newShot)
  console.log(`➕ [添加] 添加了新片段`)
}

// 显示帮助
const showHelp = (type: string) => {
  const helpContent = {
    howto: '如何使用AI导演区？\n1. 点击"开始制作所有片段"或单独制作\n2. 等待AI生成图片和语音\n3. 不满意可以重新生成\n4. 所有片段完成后进入下一步',
    demo: '准备播放操作示范视频...',
    faq: '常见问题：\nQ: 生成速度慢怎么办？\nA: 请耐心等待，AI生成需要一些时间\n\nQ: 不满意生成结果怎么办？\nA: 可以点击重新生成按钮'
  }
  alert(helpContent[type] || '帮助信息')
}

// 返回编剧区
const goBack = () => {
  router.push('/scriptwriter')
}

// 进入编辑区
const goToEditor = () => {
  if (completedShots.value < totalShots.value) {
    alert('请等待所有片段生成完成再进入下一步')
    return
  }
  router.push('/editor')
}

// 🆕 生命周期钩子
onMounted(() => {
  console.log('🎬 [DirectorView] 组件已挂载')
  // 初始化分镜数据（从编剧页面同步）
  initializeShotsFromScript()
})
</script>

<style scoped>
.director-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  display: flex;
  flex-direction: column;
}

/* 顶部控制栏 */
.director-header {
  background: white;
  padding: 20px 32px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.project-info h1 {
  margin: 0 0 4px 0;
  color: #1f2937;
  font-size: 24px;
}

.project-info p {
  margin: 0;
  color: #6b7280;
}

.header-stats {
  display: flex;
  gap: 20px;
}

.stat {
  padding: 8px 16px;
  background: #f3f4f6;
  border-radius: 8px;
  font-size: 14px;
  color: #374151;
}

/* 主工作区 */
.director-workspace {
  flex: 1;
  display: flex;
  gap: 24px;
  padding: 24px;
}

/* 左侧全局设定面板 */
.global-settings-panel {
  width: 280px;
  background: white;
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  height: fit-content;
  position: sticky;
  top: 24px;
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
  font-size: 16px;
}

.mega-btn {
  width: 100%;
  padding: 16px;
  border: none;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-bottom: 8px;
}

.mega-btn.primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.mega-btn.primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102,126,234,0.3);
}

.mega-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.setting-desc {
  font-size: 12px;
  color: #6b7280;
  margin: 0;
  text-align: center;
}

.style-options {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.style-btn {
  padding: 12px;
  border: 2px solid #e5e7eb;
  background: white;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  text-align: left;
}

.style-btn:hover {
  border-color: #667eea;
}

.style-btn.active {
  border-color: #667eea;
  background: #f8fafc;
}

.big-select {
  width: 100%;
  padding: 12px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 14px;
  background: white;
}

.help-items {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.help-btn {
  padding: 8px 12px;
  border: 1px solid #e5e7eb;
  background: white;
  border-radius: 6px;
  cursor: pointer;
  font-size: 12px;
  text-align: left;
  transition: all 0.2s ease;
}

.help-btn:hover {
  background: #f3f4f6;
}

/* 右侧分镜矩阵区 */
.storyboard-matrix {
  flex: 1;
}

/* 🆕 内联编辑样式 */
.editable-text {
  position: relative;
  padding: 12px;
  border: 2px dashed transparent;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  min-height: 60px;
  display: flex;
  align-items: center;
  font-size: 14px;
  line-height: 1.4;
}

.editable-text:hover {
  border-color: #667eea;
  background: #f8fafc;
}

.edit-hint {
  position: absolute;
  top: 4px;
  right: 8px;
  display: none;
  font-size: 12px;
  color: #667eea;
  font-weight: 500;
}

.editable-text:hover .edit-hint {
  display: block;
}

.text-editor {
  padding: 8px;
  background: #f8fafc;
  border: 2px solid #667eea;
  border-radius: 8px;
}

.text-input {
  width: 100%;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  padding: 12px;
  font-size: 14px;
  line-height: 1.4;
  resize: vertical;
  min-height: 80px;
  font-family: inherit;
}

.text-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.editor-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 8px;
  gap: 8px;
}

.save-btn {
  background: #10b981;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 12px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.save-btn:hover {
  background: #059669;
  transform: translateY(-1px);
}

.cancel-btn {
  background: #ef4444;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 12px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.cancel-btn:hover {
  background: #dc2626;
  transform: translateY(-1px);
}

.editor-tips {
  margin-top: 4px;
  text-align: center;
}

.editor-tips small {
  color: #6b7280;
  font-size: 11px;
}

.shots-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
  margin-bottom: 20px;
}

.shot-card-large {
  background: white;
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  transition: all 0.2s ease;
}

.shot-card-large:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}

.shot-card-large.generating {
  border-left: 4px solid #3b82f6;
}

.shot-card-large.completed {
  border-left: 4px solid #10b981;
}

.shot-card-large.pending {
  border-left: 4px solid #fbbf24;
}

.shot-card-large.failed {
  border-left: 4px solid #ef4444;
}

/* 卡片头部 */
.shot-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  padding-bottom: 12px;
  border-bottom: 1px solid #e5e7eb;
}

.shot-title {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
}

.shot-duration {
  display: flex;
  align-items: center;
  gap: 8px;
}

.duration-slider {
  width: 60px;
}

.duration-text {
  font-size: 12px;
  color: #6b7280;
  min-width: 30px;
}

.status-indicator {
  font-size: 12px;
  padding: 4px 8px;
  border-radius: 4px;
  font-weight: 500;
}

.status-indicator.pending {
  background: #fef3c7;
  color: #92400e;
}

.status-indicator.generating {
  background: #dbeafe;
  color: #1e40af;
}

.status-indicator.completed {
  background: #d1fae5;
  color: #065f46;
}

.status-indicator.failed {
  background: #fee2e2;
  color: #991b1b;
}

/* 视觉预览区 */
.shot-visual {
  margin-bottom: 16px;
}

.visual-container {
  position: relative;
  width: 100%;
  height: 150px;
  border-radius: 8px;
  overflow: hidden;
  margin-bottom: 12px;
}

.preview-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.preview-placeholder {
  width: 100%;
  height: 100%;
  background: #f3f4f6;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  border: 2px dashed #d1d5db;
}

.placeholder-icon {
  font-size: 32px;
  margin-bottom: 8px;
}

.progress-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(59, 130, 246, 0.8);
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  color: white;
}

.progress-animation {
  margin-bottom: 12px;
}

.spinner {
  width: 32px;
  height: 32px;
  border: 3px solid rgba(255,255,255,0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.visual-actions {
  display: flex;
  gap: 8px;
}

/* 文案区 */
.shot-text {
  margin-bottom: 16px;
}

.text-display p {
  margin: 0 0 8px 0;
  line-height: 1.6;
  color: #374151;
  font-size: 14px;
}

.text-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.char-count {
  font-size: 12px;
  color: #6b7280;
}

/* 音频区 */
.shot-audio {
  margin-bottom: 16px;
}

.audio-player {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 8px;
}

.play-btn {
  padding: 8px;
  background: #f3f4f6;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 16px;
}

.play-btn:hover:not(:disabled) {
  background: #e5e7eb;
}

.play-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.audio-info {
  font-size: 12px;
  color: #6b7280;
}

/* 操作按钮 */
.shot-actions {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.action-btn {
  padding: 6px 12px;
  border: 1px solid #e5e7eb;
  background: white;
  border-radius: 6px;
  cursor: pointer;
  font-size: 12px;
  transition: all 0.2s ease;
  flex: 1;
  min-width: 60px;
}

.action-btn:hover:not(:disabled) {
  background: #f3f4f6;
  border-color: #9ca3af;
}

.action-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.action-btn.danger:hover {
  background: #fee2e2;
  border-color: #fca5a5;
  color: #dc2626;
}

/* 添加新片段 */
.add-shot-card {
  background: white;
  border: 2px dashed #d1d5db;
  border-radius: 16px;
  padding: 40px 20px;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s ease;
}

.add-shot-card:hover {
  border-color: #667eea;
  background: #f8fafc;
}

.add-icon {
  font-size: 32px;
  margin-bottom: 12px;
  color: #9ca3af;
}

.add-shot-card p {
  margin: 0;
  color: #6b7280;
}

/* 底部操作栏 */
.director-footer {
  background: white;
  padding: 20px 32px;
  box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.progress-info {
  text-align: center;
}

.progress-info span {
  display: block;
  font-size: 14px;
  color: #6b7280;
  margin-bottom: 8px;
}

.progress-bar {
  width: 200px;
  height: 8px;
  background: #e5e7eb;
  border-radius: 4px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  transition: width 0.3s ease;
}

.back-btn, .next-btn {
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 16px;
  transition: all 0.2s ease;
}

.back-btn {
  background: #f3f4f6;
  color: #374151;
}

.back-btn:hover {
  background: #e5e7eb;
}

.next-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.next-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102,126,234,0.3);
}

.next-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* 响应式设计 */
@media (max-width: 1024px) {
  .director-workspace {
    flex-direction: column;
  }
  
  .global-settings-panel {
    width: 100%;
    position: static;
  }
  
  .shots-grid {
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  }
}

@media (max-width: 768px) {
  .director-header {
    flex-direction: column;
    gap: 16px;
    text-align: center;
  }
  
  .header-stats {
    justify-content: center;
  }
  
  .shots-grid {
    grid-template-columns: 1fr;
  }
  
  .director-footer {
    flex-direction: column;
    gap: 16px;
  }
  
  .progress-bar {
    width: 100%;
  }
}
</style>
