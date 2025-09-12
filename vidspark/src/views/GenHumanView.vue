<template>
  <div class="max-w-6xl mx-auto p-6 bg-white min-h-screen">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-2">GenHuman 數字人製作工廠</h1>
      <p class="text-gray-600">完整的九步驟數字人製作工作流程</p>
    </div>
    
    <!-- API配置區域 -->
    <div class="bg-gray-50 p-6 rounded-lg mb-8">
      <h2 class="text-xl font-semibold mb-4">🔑 API 配置</h2>
      <div class="grid grid-cols-1 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            API Token *
          </label>
          <input
            type="password"
            v-model="workflowData.token"
            @input="handleTokenChange"
            placeholder="請輸入您的 GenHuman API Token"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            回調URL
          </label>
          <input
            type="url"
            v-model="workflowData.callbackUrl"
            placeholder="https://your-callback-url.com"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
      </div>
    </div>
    
    <!-- 工作流程配置 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
      <!-- 聲音配置 -->
      <div class="bg-blue-50 p-6 rounded-lg">
        <h2 class="text-xl font-semibold mb-4">🎵 聲音配置</h2>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              聲音名稱 *
            </label>
            <input
              type="text"
              v-model="workflowData.voiceName"
              placeholder="例如：張三的聲音"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              聲音描述 *
            </label>
            <textarea
              v-model="workflowData.voiceDescription"
              placeholder="描述這個聲音的特點..."
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              音頻文件 *
            </label>
            <input
              ref="audioFileRef"
              type="file"
              accept=".mp3,.m4a,.wav"
              @change="handleAudioFileChange"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <p v-if="workflowData.audioFile" class="text-sm text-gray-600 mt-1">
              已選擇: {{ workflowData.audioFile.name }}
            </p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              或音頻URL
            </label>
            <input
              type="url"
              v-model="workflowData.audioUrl"
              placeholder="https://example.com/audio.mp3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              聲音克隆類型
            </label>
            <select
              v-model="workflowData.voiceType"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="free">免費克隆</option>
              <option value="paid">深度克隆（付費）</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              要合成的文本 *
            </label>
            <textarea
              v-model="workflowData.speechText"
              placeholder="請輸入要讓數字人說的話..."
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              語音合成類型
            </label>
            <select
              v-model="workflowData.speechType"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="free">免費合成</option>
              <option value="paid">深度合成（付費）</option>
            </select>
          </div>
        </div>
      </div>
      
      <!-- 數字人配置 -->
      <div class="bg-green-50 p-6 rounded-lg">
        <h2 class="text-xl font-semibold mb-4">👤 數字人配置</h2>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              數字人名稱 *
            </label>
            <input
              type="text"
              v-model="workflowData.videoName"
              placeholder="例如：辦公室場景"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              視頻文件 *
            </label>
            <input
              ref="videoFileRef"
              type="file"
              accept=".mp4,.mov,.avi"
              @change="handleVideoFileChange"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
            />
            <p v-if="workflowData.videoFile" class="text-sm text-gray-600 mt-1">
              已選擇: {{ workflowData.videoFile.name }}
            </p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              或視頻URL
            </label>
            <input
              type="url"
              v-model="workflowData.videoUrl"
              placeholder="https://example.com/video.mp4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              數字人克隆類型
            </label>
            <select
              v-model="workflowData.digitalHumanType"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
            >
              <option value="free">免費克隆</option>
              <option value="paid">高級克隆（付費）</option>
            </select>
          </div>
        </div>
      </div>
    </div>
    
    <!-- 執行按鈕 -->
    <div class="text-center mb-8">
      <button
        @click="executeWorkflow"
        :disabled="isProcessing"
        :class="[
          'px-8 py-3 rounded-lg text-white font-semibold text-lg',
          isProcessing 
            ? 'bg-gray-400 cursor-not-allowed' 
            : 'bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500'
        ]"
      >
        {{ isProcessing ? '🔄 製作中...' : '🚀 開始製作數字人' }}
      </button>
    </div>
    
    <!-- 工作流程步驟 -->
    <div class="bg-gray-50 p-6 rounded-lg mb-8">
      <h2 class="text-xl font-semibold mb-4">📋 工作流程進度</h2>
      <div class="space-y-4">
        <div 
          v-for="step in steps" 
          :key="step.id" 
          :class="[
            'flex items-center p-4 rounded-lg border',
            step.status === 'completed' ? 'bg-green-50 border-green-200' :
            step.status === 'processing' ? 'bg-blue-50 border-blue-200' :
            step.status === 'error' ? 'bg-red-50 border-red-200' :
            'bg-white border-gray-200'
          ]"
        >
          <div class="text-2xl mr-4">
            {{ getStepIcon(step.status) }}
          </div>
          <div class="flex-1">
            <h3 :class="['font-semibold', getStepColor(step.status)]">
              步驟 {{ step.id }}: {{ step.title }}
            </h3>
            <p class="text-gray-600 text-sm">{{ step.description }}</p>
            <p v-if="step.error" class="text-red-600 text-sm mt-1">
              錯誤: {{ step.error }}
            </p>
          </div>
        </div>
      </div>
    </div>
    
    <!-- 最終結果 -->
    <div v-if="finalResult" class="bg-green-50 p-6 rounded-lg mb-8">
      <h2 class="text-xl font-semibold mb-4">🎉 製作完成</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <h3 class="font-semibold mb-2">視頻預覽</h3>
          <video 
            controls 
            class="w-full rounded-lg"
            :poster="finalResult.coverUrl"
          >
            <source :src="finalResult.videoUrl" type="video/mp4" />
            您的瀏覽器不支持視頻播放。
          </video>
        </div>
        <div>
          <h3 class="font-semibold mb-2">視頻信息</h3>
          <div class="space-y-2 text-sm">
            <p><strong>視頻名稱:</strong> {{ finalResult.videoName }}</p>
            <p><strong>時長:</strong> {{ finalResult.duration }} 秒</p>
            <p><strong>任務ID:</strong> {{ finalResult.video_task_id }}</p>
            <p class="text-orange-600"><strong>提示:</strong> {{ finalResult.tips }}</p>
          </div>
          <div class="mt-4 space-y-2">
            <a 
              :href="finalResult.videoUrl" 
              download
              class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
            >
              📥 下載視頻
            </a>
            <a 
              :href="finalResult.coverUrl" 
              download
              class="block w-full text-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700"
            >
              🖼️ 下載封面
            </a>
          </div>
        </div>
      </div>
    </div>
    
    <!-- 執行日誌 -->
    <div v-if="logs.length > 0" class="bg-gray-900 text-green-400 p-4 rounded-lg">
      <h2 class="text-lg font-semibold mb-2 text-white">📝 執行日誌</h2>
      <div class="max-h-64 overflow-y-auto font-mono text-sm">
        <div v-for="(log, index) in logs" :key="index" class="mb-1">
          {{ log }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'

// 定義接口
interface WorkflowStep {
  id: number
  title: string
  description: string
  status: 'pending' | 'processing' | 'completed' | 'error'
  result?: any
  error?: string
}

interface WorkflowData {
  token: string
  voiceName: string
  voiceDescription: string
  audioFile?: File
  audioUrl?: string
  voiceType: 'free' | 'paid'
  speechText: string
  speechType: 'free' | 'paid'
  videoName: string
  videoFile?: File
  videoUrl?: string
  digitalHumanType: 'free' | 'paid'
  callbackUrl: string
}

interface TaskStatusResult {
  video_task_id: number
  duration: number
  durationMs: number
  coverUrl: string
  videoUrl: string
  videoName: string
  tips: string
  state: number
}

// 響應式數據
const workflowData = reactive<WorkflowData>({
  token: '',
  voiceName: '',
  voiceDescription: '',
  voiceType: 'free',
  speechText: '',
  speechType: 'free',
  videoName: '',
  digitalHumanType: 'free',
  callbackUrl: 'https://baidu.com'
})

const steps = ref<WorkflowStep[]>([
  { id: 1, title: '聲音克隆', description: '上傳音頻文件並創建聲音模型', status: 'pending' },
  { id: 2, title: '語音合成', description: '使用聲音模型合成指定文本', status: 'pending' },
  { id: 3, title: '數字人克隆', description: '上傳視頻文件並創建數字人模型', status: 'pending' },
  { id: 4, title: '數字人合成', description: '合成數字人說話視頻', status: 'pending' },
  { id: 5, title: '任務輪詢', description: '等待視頻生成完成', status: 'pending' }
])

const isProcessing = ref(false)
const finalResult = ref<TaskStatusResult | null>(null)
const logs = ref<string[]>([])
const audioFileRef = ref<HTMLInputElement>()
const videoFileRef = ref<HTMLInputElement>()

// 常量
const FILE_LIMITS = {
  MAX_AUDIO_SIZE: 50 * 1024 * 1024, // 50MB
  MAX_VIDEO_SIZE: 200 * 1024 * 1024 // 200MB
}

const API_ENDPOINTS = {
  VOICE_CLONE_FREE: '/vidspark-api-proxy/clone-voice',
  VOICE_CLONE_PAID: '/vidspark-api-proxy/clone-voice-deep',
  SPEECH_SYNTHESIS_FREE: '/vidspark-api-proxy/synthesize-voice',
  SPEECH_SYNTHESIS_PAID: '/vidspark-api-proxy/synthesize-voice-deep',
  DIGITAL_HUMAN_CLONE_FREE: '/vidspark-api-proxy/create-scene',
  DIGITAL_HUMAN_CLONE_PAID: '/vidspark-api-proxy/create-scene-senior',
  DIGITAL_HUMAN_SYNTHESIS: '/vidspark-api-proxy/create-musetalk',
  TASK_STATUS: '/vidspark-api-proxy/musetalk-task',
  UPLOAD_AUDIO_SIMPLE: '/vidspark-simple-upload/audio',
  UPLOAD_VIDEO_SIMPLE: '/vidspark-simple-upload/video',
  UPLOAD_AUDIO_BASE64: '/vidspark-upload/audio-base64',
  UPLOAD_VIDEO_BASE64: '/vidspark-upload/video-base64'
}

// 生命週期
onMounted(() => {
  // 從本地存儲加載Token
  const savedToken = localStorage.getItem('genhuman_token')
  if (savedToken) {
    workflowData.token = savedToken
  }
})

// 方法
const addLog = (message: string) => {
  const timestamp = new Date().toLocaleTimeString()
  const logMessage = `[${timestamp}] ${message}`
  logs.value.push(logMessage)
  console.log(logMessage)
}

const updateStepStatus = (stepId: number, status: WorkflowStep['status'], result?: any, error?: string) => {
  const stepIndex = steps.value.findIndex(step => step.id === stepId)
  if (stepIndex !== -1) {
    steps.value[stepIndex] = {
      ...steps.value[stepIndex],
      status,
      result,
      error
    }
  }
}

const handleTokenChange = () => {
  localStorage.setItem('genhuman_token', workflowData.token)
}

const handleAudioFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  if (file) {
    if (file.size > FILE_LIMITS.MAX_AUDIO_SIZE) {
      alert(`音頻文件大小不能超過 ${FILE_LIMITS.MAX_AUDIO_SIZE / (1024 * 1024)}MB`)
      return
    }
    workflowData.audioFile = file
    addLog(`選擇音頻文件: ${file.name} (${(file.size / (1024 * 1024)).toFixed(2)}MB)`)
  }
}

const handleVideoFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  if (file) {
    if (file.size > FILE_LIMITS.MAX_VIDEO_SIZE) {
      alert(`視頻文件大小不能超過 ${FILE_LIMITS.MAX_VIDEO_SIZE / (1024 * 1024)}MB`)
      return
    }
    workflowData.videoFile = file
    addLog(`選擇視頻文件: ${file.name} (${(file.size / (1024 * 1024)).toFixed(2)}MB)`)
  }
}

const getStepIcon = (status: WorkflowStep['status']) => {
  switch (status) {
    case 'pending': return '⏳'
    case 'processing': return '🔄'
    case 'completed': return '✅'
    case 'error': return '❌'
    default: return '⏳'
  }
}

const getStepColor = (status: WorkflowStep['status']) => {
  switch (status) {
    case 'pending': return 'text-gray-500'
    case 'processing': return 'text-blue-500'
    case 'completed': return 'text-green-500'
    case 'error': return 'text-red-500'
    default: return 'text-gray-500'
  }
}

// API調用方法
const makeApiRequest = async (endpoint: string, data: any, method: 'POST' | 'GET' = 'POST') => {
  const requestOptions: RequestInit = {
    method,
    headers: {
      'Content-Type': 'application/json'
    }
  }
  
  if (method === 'POST' && data) {
    requestOptions.body = JSON.stringify(data)
  }
  
  const response = await fetch(endpoint, requestOptions)
  
  if (!response.ok) {
    throw new Error(`HTTP錯誤: ${response.status} ${response.statusText}`)
  }
  
  return await response.json()
}

const uploadFileAsBase64 = async (file: File, type: 'audio' | 'video') => {
  addLog(`使用Base64上傳: ${file.name}`)
  
  const fileData = await new Promise<string>((resolve, reject) => {
    const reader = new FileReader()
    reader.onload = () => {
      const base64 = (reader.result as string).split(',')[1]
      resolve(base64)
    }
    reader.onerror = reject
    reader.readAsDataURL(file)
  })
  
  const endpoint = type === 'audio' ? 
    API_ENDPOINTS.UPLOAD_AUDIO_BASE64 : 
    API_ENDPOINTS.UPLOAD_VIDEO_BASE64
  
  const response = await fetch(endpoint, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      fileName: file.name,
      fileData: fileData,
      fileSize: file.size
    })
  })
  
  const result = await response.json()
  
  if (result.success) {
    addLog(`Base64上傳成功: ${result.data.file_url}`)
    return result
  } else {
    throw new Error('Base64上傳失敗: ' + result.message)
  }
}

const uploadAudioFile = async (file: File) => {
  const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2)
  addLog(`上傳音頻文件: ${file.name} (${fileSizeMB}MB)`)
  
  try {
    const formData = new FormData()
    formData.append('audio', file)
    
    const response = await fetch(API_ENDPOINTS.UPLOAD_AUDIO_SIMPLE, {
      method: 'POST',
      body: formData
    })
    
    const result = await response.json()
    
    if (result.success) {
      addLog(`新系統上傳成功: ${result.data.file_url}`)
      return result
    } else {
      throw new Error('新系統上傳失敗: ' + result.message)
    }
  } catch (error) {
    addLog(`新系統上傳失敗，嘗試Base64備用: ${error}`)
    return uploadFileAsBase64(file, 'audio')
  }
}

const uploadVideoFile = async (file: File) => {
  const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2)
  addLog(`上傳視頻文件: ${file.name} (${fileSizeMB}MB)`)
  
  try {
    const formData = new FormData()
    formData.append('video', file)
    
    const response = await fetch(API_ENDPOINTS.UPLOAD_VIDEO_SIMPLE, {
      method: 'POST',
      body: formData
    })
    
    const result = await response.json()
    
    if (result.success) {
      addLog(`新系統上傳成功: ${result.data.file_url}`)
      return result
    } else {
      throw new Error('新系統上傳失敗: ' + result.message)
    }
  } catch (error) {
    addLog(`新系統上傳失敗，嘗試Base64備用: ${error}`)
    return uploadFileAsBase64(file, 'video')
  }
}

const sleep = (ms: number) => {
  return new Promise(resolve => setTimeout(resolve, ms))
}

const executeWorkflow = async () => {
  if (!workflowData.token) {
    alert('請輸入API Token')
    return
  }
  
  if (!workflowData.voiceName || !workflowData.speechText || !workflowData.videoName) {
    alert('請填寫所有必需字段')
    return
  }
  
  if (!workflowData.audioFile && !workflowData.audioUrl) {
    alert('請上傳音頻文件或提供音頻URL')
    return
  }
  
  if (!workflowData.videoFile && !workflowData.videoUrl) {
    alert('請上傳視頻文件或提供視頻URL')
    return
  }
  
  isProcessing.value = true
  finalResult.value = null
  logs.value = []
  
  // 重置所有步驟狀態
  steps.value.forEach(step => {
    step.status = 'pending'
    step.result = undefined
    step.error = undefined
  })
  
  try {
    addLog('🚀 開始執行完整數字人製作工作流程')
    
    // 處理Token
    let token = workflowData.token
    if (token.startsWith('Bearer ')) {
      token = token.substring(7)
    }
    
    // 步驟1：聲音克隆
    updateStepStatus(1, 'processing')
    addLog('步驟1: 開始聲音克隆...')
    
    let audioUrl = workflowData.audioUrl
    if (workflowData.audioFile && !audioUrl) {
      const uploadResult = await uploadAudioFile(workflowData.audioFile)
      audioUrl = uploadResult.data.file_url
    }
    
    const endpoint1 = workflowData.voiceType === 'paid' ? 
      API_ENDPOINTS.VOICE_CLONE_PAID : 
      API_ENDPOINTS.VOICE_CLONE_FREE
    
    const voiceResult = await makeApiRequest(endpoint1, {
      token,
      name: workflowData.voiceName,
      audio_url: audioUrl,
      description: workflowData.voiceDescription
    })
    
    if (voiceResult.code !== 200 || !voiceResult.data?.voice_id) {
      throw new Error(`聲音克隆失敗: ${voiceResult.msg || '未知錯誤'}`)
    }
    
    updateStepStatus(1, 'completed', voiceResult.data)
    addLog(`步驟1: 聲音克隆完成 - voice_id=${voiceResult.data.voice_id}`)
    
    // 步驟2：語音合成
    updateStepStatus(2, 'processing')
    addLog('步驟2: 開始語音合成...')
    
    const endpoint2 = workflowData.speechType === 'paid' ? 
      API_ENDPOINTS.SPEECH_SYNTHESIS_PAID : 
      API_ENDPOINTS.SPEECH_SYNTHESIS_FREE
    
    const speechResult = await makeApiRequest(endpoint2, {
      token,
      text: workflowData.speechText,
      voice_id: voiceResult.data.voice_id
    })
    
    if (speechResult.code !== 200 || !speechResult.data?.audio_url) {
      throw new Error(`語音合成失敗: ${speechResult.msg || '未知錯誤'}`)
    }
    
    updateStepStatus(2, 'completed', speechResult.data)
    addLog(`步驟2: 語音合成完成 - ${speechResult.data.audio_url}`)
    
    // 步驟3：數字人克隆
    updateStepStatus(3, 'processing')
    addLog('步驟3: 開始數字人克隆...')
    
    let videoUrl = workflowData.videoUrl
    if (workflowData.videoFile && !videoUrl) {
      const uploadResult = await uploadVideoFile(workflowData.videoFile)
      videoUrl = uploadResult.data.file_url
    }
    
    const endpoint3 = workflowData.digitalHumanType === 'paid' ? 
      API_ENDPOINTS.DIGITAL_HUMAN_CLONE_PAID : 
      API_ENDPOINTS.DIGITAL_HUMAN_CLONE_FREE
    
    const cloneResult = await makeApiRequest(endpoint3, {
      token,
      video_name: workflowData.videoName,
      video_url: videoUrl,
      callback_url: workflowData.callbackUrl
    })
    
    if (cloneResult.code !== 200 || !cloneResult.data?.scene_task_id) {
      throw new Error(`數字人克隆失敗: ${cloneResult.msg || '未知錯誤'}`)
    }
    
    updateStepStatus(3, 'completed', cloneResult.data)
    addLog(`步驟3: 數字人克隆完成 - scene_task_id=${cloneResult.data.scene_task_id}`)
    
    // 步驟4：數字人合成
    updateStepStatus(4, 'processing')
    addLog('步驟4: 開始數字人合成...')
    
    const synthesisResult = await makeApiRequest(API_ENDPOINTS.DIGITAL_HUMAN_SYNTHESIS, {
      token,
      scene_task_id: cloneResult.data.scene_task_id.toString(),
      audio_url: speechResult.data.audio_url,
      callback_url: workflowData.callbackUrl
    })
    
    if (synthesisResult.code !== 200 || !synthesisResult.data?.video_task_id) {
      throw new Error(`數字人合成失敗: ${synthesisResult.msg || '未知錯誤'}`)
    }
    
    updateStepStatus(4, 'completed', synthesisResult.data)
    addLog(`步驟4: 數字人合成提交完成 - video_task_id=${synthesisResult.data.video_task_id}`)
    
    // 步驟5：輪詢任務狀態
    updateStepStatus(5, 'processing')
    addLog('步驟5: 開始輪詢任務狀態...')
    
    const maxPollingTime = 10 * 60 * 1000 // 10分鐘
    const pollingInterval = 30 * 1000 // 30秒
    const startTime = Date.now()
    
    while (Date.now() - startTime < maxPollingTime) {
      try {
        const statusResult = await makeApiRequest(
          `${API_ENDPOINTS.TASK_STATUS}?task_id=${synthesisResult.data.video_task_id}`,
          null,
          'GET'
        )
        
        if (statusResult.code === 200 && statusResult.data) {
          addLog(`輪詢中... 狀態: ${statusResult.data.state}`)
          
          if (statusResult.data.state === 20) {
            updateStepStatus(5, 'completed', statusResult.data)
            finalResult.value = statusResult.data
            addLog('🎉 數字人製作完成！')
            addLog(`📹 視頻URL: ${statusResult.data.videoUrl}`)
            addLog(`🖼️ 封面URL: ${statusResult.data.coverUrl}`)
            addLog(`⏱️ 視頻時長: ${statusResult.data.duration}秒`)
            addLog(`💡 ${statusResult.data.tips}`)
            break
          }
        }
        
        addLog('⏳ 任務進行中，30秒後重試...')
        await sleep(pollingInterval)
        
      } catch (error) {
        addLog(`⚠️ 輪詢錯誤，繼續重試: ${error}`)
        await sleep(pollingInterval)
      }
    }
    
    if (!finalResult.value) {
      throw new Error('任務輪詢超時（10分鐘）')
    }
    
  } catch (error) {
    const errorMessage = error instanceof Error ? error.message : '未知錯誤'
    addLog(`❌ 工作流程執行失敗: ${errorMessage}`)
    
    // 標記當前處理中的步驟為錯誤
    const processingStep = steps.value.find(step => step.status === 'processing')
    if (processingStep) {
      updateStepStatus(processingStep.id, 'error', undefined, errorMessage)
    }
  } finally {
    isProcessing.value = false
  }
}
</script>

<style scoped>
/* 添加一些自定義樣式 */
.font-mono {
  font-family: 'Courier New', monospace;
}
</style>