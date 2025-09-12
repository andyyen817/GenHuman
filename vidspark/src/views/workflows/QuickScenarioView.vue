<template>
  <div class="quick-scenario-container">
    <!-- 頁面標題 -->
    <div class="page-header">
      <h1 class="page-title">快速數字人製作</h1>
      <p class="page-subtitle">選擇您的製作場景，開始創建專屬數字人</p>
    </div>

    <!-- 進度指示器 -->
    <div class="progress-indicator">
      <div class="step active">
        <div class="step-number">1</div>
        <div class="step-label">場景選擇</div>
      </div>
      <div class="step">
        <div class="step-number">2</div>
        <div class="step-label">AI編劇</div>
      </div>
      <div class="step">
        <div class="step-number">3</div>
        <div class="step-label">聲音克隆</div>
      </div>
      <div class="step">
        <div class="step-number">4</div>
        <div class="step-label">數字人合成</div>
      </div>
      <div class="step">
        <div class="step-number">5</div>
        <div class="step-label">成品展示</div>
      </div>
    </div>

    <!-- 項目信息 -->
    <div class="project-info-card">
      <h2>項目信息</h2>
      <div class="form-group">
        <label for="projectName">項目名稱</label>
        <input 
          id="projectName"
          v-model="projectData.projectName" 
          type="text" 
          placeholder="請輸入項目名稱"
          class="form-input"
        />
      </div>
    </div>

    <!-- 場景選擇卡片 -->
    <div class="scenario-cards">
      <h2>選擇製作場景</h2>
      
      <!-- 場景A：已準備文案 -->
      <div 
        class="scenario-card" 
        :class="{ active: selectedScenario === 'ready' }"
        @click="selectScenario('ready')"
      >
        <div class="card-icon">📝</div>
        <div class="card-content">
          <h3>場景A：已準備文案</h3>
          <p>您已經準備好完整的文案內容，直接開始製作數字人</p>
          <div class="card-features">
            <span class="feature">✓ 直接輸入文案</span>
            <span class="feature">✓ 快速開始</span>
          </div>
        </div>
      </div>

      <!-- 場景B：AI生成文案 -->
      <div 
        class="scenario-card" 
        :class="{ active: selectedScenario === 'generate' }"
        @click="selectScenario('generate')"
      >
        <div class="card-icon">🤖</div>
        <div class="card-content">
          <h3>場景B：AI生成文案</h3>
          <p>告訴我們您的想法，AI將為您生成專業文案</p>
          <div class="card-features">
            <span class="feature">✓ AI智能生成</span>
            <span class="feature">✓ 專業文案</span>
          </div>
        </div>
      </div>

      <!-- 場景C：已有聲音檔案 -->
      <div 
        class="scenario-card" 
        :class="{ active: selectedScenario === 'audio-file' }"
        @click="selectScenario('audio-file')"
      >
        <div class="card-icon">🎵</div>
        <div class="card-content">
          <h3>場景C：已有聲音檔案</h3>
          <p>您已經有現成的聲音檔案，直接進行數字人合成</p>
          <div class="card-features">
            <span class="feature">✓ 跳過聲音製作</span>
            <span class="feature">✓ 直接合成</span>
          </div>
        </div>
      </div>
    </div>

    <!-- 場景詳細配置 -->
    <div v-if="selectedScenario" class="scenario-config">
      <!-- 場景A配置 -->
      <div v-if="selectedScenario === 'ready'" class="config-section">
        <h3>輸入您的文案</h3>
        <textarea 
          v-model="projectData.content"
          placeholder="請輸入您準備好的文案內容..."
          class="form-textarea"
          rows="6"
        ></textarea>
        <div class="text-info">
          <span>字數：{{ contentLength }}</span>
          <span>預估時長：{{ estimatedDuration }}秒</span>
        </div>
      </div>

      <!-- 場景B配置 -->
      <div v-if="selectedScenario === 'generate'" class="config-section">
        <h3>AI文案生成設置</h3>
        <div class="form-group">
          <label>期望時長</label>
          <select v-model="projectData.duration" class="form-select">
            <option value="30">30秒</option>
            <option value="60">1分鐘</option>
            <option value="120">2分鐘</option>
            <option value="300">5分鐘</option>
          </select>
        </div>
        <div class="form-group">
          <label>您的想法</label>
          <textarea 
            v-model="projectData.ideas"
            placeholder="請描述您想要表達的內容、主題或關鍵信息..."
            class="form-textarea"
            rows="4"
          ></textarea>
        </div>
      </div>

      <!-- 場景C配置 -->
      <div v-if="selectedScenario === 'audio-file'" class="config-section">
        <h3>上傳聲音檔案</h3>
        <div class="file-upload-area" @click="triggerFileUpload">
          <input 
            ref="audioFileInput"
            type="file" 
            accept=".mp3,.m4a,.wav"
            @change="handleAudioFileUpload"
            style="display: none;"
          />
          <div v-if="!projectData.audioFile" class="upload-placeholder">
            <div class="upload-icon">🎵</div>
            <p>點擊上傳聲音檔案</p>
            <p class="upload-hint">支援 MP3、M4A、WAV 格式</p>
          </div>
          <div v-else class="uploaded-file">
            <div class="file-icon">🎵</div>
            <div class="file-info">
              <p class="file-name">{{ projectData.audioFile.name }}</p>
              <p class="file-size">{{ formatFileSize(projectData.audioFile.size) }}</p>
            </div>
            <button @click.stop="removeAudioFile" class="remove-btn">×</button>
          </div>
        </div>
      </div>
    </div>

    <!-- 操作按鈕 -->
    <div class="action-buttons">
      <button @click="goBack" class="btn btn-secondary">返回</button>
      <button 
        @click="nextStep" 
        :disabled="!canProceed"
        class="btn btn-primary"
      >
        {{ selectedScenario === 'audio-file' ? '跳轉到數字人合成' : '下一步：AI編劇' }}
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

interface ProjectData {
  projectName: string
  contentType: 'ready' | 'generate' | 'audio-file' | null
  content?: string
  duration?: number
  audioFile?: File
  ideas?: string
}

const router = useRouter()
const audioFileInput = ref<HTMLInputElement>()

const selectedScenario = ref<string | null>(null)
const projectData = ref<ProjectData>({
  projectName: '',
  contentType: null
})

// 計算屬性
const contentLength = computed(() => {
  return projectData.value.content?.length || 0
})

const estimatedDuration = computed(() => {
  // 假設每分鐘200字
  return Math.ceil((contentLength.value / 200) * 60)
})

const canProceed = computed(() => {
  if (!projectData.value.projectName || !selectedScenario.value) return false
  
  switch (selectedScenario.value) {
    case 'ready':
      return !!projectData.value.content
    case 'generate':
      return !!projectData.value.ideas && !!projectData.value.duration
    case 'audio-file':
      return !!projectData.value.audioFile
    default:
      return false
  }
})

// 方法
function selectScenario(scenario: string) {
  selectedScenario.value = scenario
  projectData.value.contentType = scenario as any
}

function triggerFileUpload() {
  audioFileInput.value?.click()
}

function handleAudioFileUpload(event: Event) {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  if (file) {
    projectData.value.audioFile = file
  }
}

function removeAudioFile() {
  projectData.value.audioFile = undefined
  if (audioFileInput.value) {
    audioFileInput.value.value = ''
  }
}

function formatFileSize(bytes: number): string {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

function goBack() {
  router.push('/vidspark')
}

function nextStep() {
  if (!canProceed.value) return
  
  // 存儲項目數據到 sessionStorage
  sessionStorage.setItem('quickWorkflowData', JSON.stringify(projectData.value))
  
  if (selectedScenario.value === 'audio-file') {
    // 場景C直接跳轉到數字人合成
    router.push('/workflows/quick-digital-human/human-director')
  } else {
    // 場景A和B進入AI編劇
    router.push('/workflows/quick-digital-human/scriptwriter')
  }
}
</script>

<style scoped>
.quick-scenario-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  min-height: 100vh;
}

.page-header {
  text-align: center;
  margin-bottom: 3rem;
  color: white;
}

.page-title {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.page-subtitle {
  font-size: 1.1rem;
  opacity: 0.9;
}

.progress-indicator {
  display: flex;
  justify-content: center;
  margin-bottom: 3rem;
  gap: 2rem;
}

.step {
  display: flex;
  flex-direction: column;
  align-items: center;
  color: white;
  opacity: 0.5;
  transition: opacity 0.3s;
}

.step.active {
  opacity: 1;
}

.step-number {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.step.active .step-number {
  background: white;
  color: #667eea;
}

.step-label {
  font-size: 0.9rem;
  text-align: center;
}

.project-info-card,
.scenario-cards,
.scenario-config {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  margin-bottom: 2rem;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.scenario-cards h2,
.project-info-card h2 {
  margin-bottom: 1.5rem;
  color: #333;
  font-size: 1.5rem;
  font-weight: 600;
}

.scenario-card {
  display: flex;
  align-items: center;
  padding: 1.5rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  margin-bottom: 1rem;
  cursor: pointer;
  transition: all 0.3s;
}

.scenario-card:hover {
  border-color: #667eea;
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(102, 126, 234, 0.1);
}

.scenario-card.active {
  border-color: #667eea;
  background: linear-gradient(135deg, #667eea10, #764ba210);
}

.card-icon {
  font-size: 3rem;
  margin-right: 1.5rem;
}

.card-content h3 {
  margin-bottom: 0.5rem;
  color: #333;
  font-size: 1.2rem;
  font-weight: 600;
}

.card-content p {
  color: #666;
  margin-bottom: 1rem;
  line-height: 1.5;
}

.card-features {
  display: flex;
  gap: 1rem;
}

.feature {
  background: #f3f4f6;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.85rem;
  color: #374151;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #374151;
}

.form-input,
.form-textarea,
.form-select {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 1rem;
  transition: border-color 0.3s;
}

.form-input:focus,
.form-textarea:focus,
.form-select:focus {
  outline: none;
  border-color: #667eea;
}

.text-info {
  display: flex;
  gap: 2rem;
  margin-top: 0.5rem;
  font-size: 0.9rem;
  color: #666;
}

.file-upload-area {
  border: 2px dashed #d1d5db;
  border-radius: 12px;
  padding: 2rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s;
}

.file-upload-area:hover {
  border-color: #667eea;
  background: #f8faff;
}

.upload-placeholder .upload-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.upload-hint {
  color: #666;
  font-size: 0.9rem;
}

.uploaded-file {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.file-icon {
  font-size: 2rem;
}

.file-info {
  flex: 1;
  text-align: left;
}

.file-name {
  font-weight: 500;
  margin-bottom: 0.25rem;
}

.file-size {
  color: #666;
  font-size: 0.9rem;
}

.remove-btn {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  border: none;
  background: #ef4444;
  color: white;
  cursor: pointer;
  font-size: 1.2rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.action-buttons {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
}

.btn {
  padding: 0.75rem 2rem;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s;
  border: none;
}

.btn-secondary {
  background: #f3f4f6;
  color: #374151;
}

.btn-secondary:hover {
  background: #e5e7eb;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none !important;
}

.config-section h3 {
  margin-bottom: 1.5rem;
  color: #333;
  font-size: 1.2rem;
  font-weight: 600;
}

@media (max-width: 768px) {
  .quick-scenario-container {
    padding: 1rem;
  }
  
  .progress-indicator {
    gap: 1rem;
  }
  
  .step-label {
    font-size: 0.8rem;
  }
  
  .scenario-card {
    flex-direction: column;
    text-align: center;
  }
  
  .card-icon {
    margin-right: 0;
    margin-bottom: 1rem;
  }
  
  .action-buttons {
    flex-direction: column;
  }
}
</style>