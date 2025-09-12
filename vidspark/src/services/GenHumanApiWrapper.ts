/**
 * GenHuman API 封裝層 v1.0
 * 基於 vidspark-digital-human-complete.html 的成功實現
 * 遵循 genhuman開發規則.md 和避免 genhuman开发错误.md 中的問題
 * 
 * 核心功能：
 * 1. 聲音克隆 (免費/付費)
 * 2. 語音合成 (免費/深度)
 * 3. 數字人克隆 (免費/付費)
 * 4. 數字人合成 (統一API)
 * 5. 異步輪詢機制
 * 6. 文件上傳處理
 * 7. 錯誤處理和重試
 */

export interface GenHumanConfig {
  token: string;
  baseUrl?: string;
  timeout?: number;
}

export interface VoiceCloneParams {
  name: string;
  audio_url?: string;
  audio_file?: File;
  description: string;
  type?: 'free' | 'paid'; // 免費聲音克隆 vs 深度聲音克隆
}

export interface VoiceCloneResult {
  voice_id: string;
  task_id: number;
}

export interface SpeechSynthesisParams {
  text: string;
  voice_id: string;
  type?: 'free' | 'paid'; // 免費語音合成 vs 深度語音合成
}

export interface SpeechSynthesisResult {
  audio_url: string;
  audio_base64?: string;
  task_id?: number; // 異步模式時返回
}

export interface DigitalHumanCloneParams {
  video_name: string;
  video_url?: string;
  video_file?: File;
  callback_url: string;
  type?: 'free' | 'paid'; // 免費克隆 vs 付費克隆
}

export interface DigitalHumanCloneResult {
  scene_task_id: number;
}

export interface DigitalHumanSynthesisParams {
  scene_task_id: number;
  audio_url: string;
  callback_url: string;
}

export interface DigitalHumanSynthesisResult {
  video_task_id: number;
  bill_id: string;
}

export interface TaskStatusResult {
  video_task_id: number;
  duration: number;
  durationMs: number;
  coverUrl: string;
  videoUrl: string;
  videoName: string;
  tips: string;
  state: number; // 20 = 完成
}

export interface FileUploadResult {
  success: boolean;
  data: {
    file_url: string;
    original_name: string;
  };
  message?: string;
}

/**
 * GenHuman API 封裝類
 * 基於成功的測試頁面實現，提供完整的數字人製作工作流程
 */
export class GenHumanApiWrapper {
  private config: GenHumanConfig;
  private readonly API_ENDPOINTS = {
    // 聲音克隆
    VOICE_CLONE_FREE: '/api/genhuman-proxy.php?path=clone-voice',
    VOICE_CLONE_PAID: '/api/genhuman-proxy.php?path=clone-voice-deep',
    
    // 語音合成
    SPEECH_SYNTHESIS_FREE: '/api/genhuman-proxy.php?path=synthesize-voice',
    SPEECH_SYNTHESIS_PAID: '/api/genhuman-proxy.php?path=synthesize-voice-deep',
    
    // 數字人克隆
    DIGITAL_HUMAN_CLONE_FREE: '/api/genhuman-proxy.php?path=create-scene',
    DIGITAL_HUMAN_CLONE_PAID: '/api/genhuman-proxy.php?path=create-scene-senior',
    
    // 數字人合成
    DIGITAL_HUMAN_SYNTHESIS: '/api/genhuman-proxy.php?path=create-musetalk',
    
    // 任務查詢
    TASK_STATUS: '/api/genhuman-proxy.php?path=musetalk-task',
    
    // 文件上傳
    UPLOAD_AUDIO_SIMPLE: '/api/upload-handler.php?type=audio',
    UPLOAD_VIDEO_SIMPLE: '/api/upload-handler.php?type=video',
    UPLOAD_AUDIO_BASE64: '/api/upload-handler.php?type=audio&format=base64',
    UPLOAD_VIDEO_BASE64: '/api/upload-handler.php?type=video&format=base64'
  };

  constructor(config: GenHumanConfig) {
    this.config = {
      timeout: 300000, // 5分鐘默認超時
      ...config
    };
    
    // 處理Bearer前綴
    if (this.config.token.startsWith('Bearer ')) {
      this.config.token = this.config.token.substring(7);
    }
  }

  /**
   * 步驟1：聲音克隆
   * 支持免費和付費兩種模式
   * 支持文件上傳和URL兩種音頻來源
   */
  async cloneVoice(params: VoiceCloneParams): Promise<VoiceCloneResult> {
    const startTime = new Date().toLocaleTimeString();
    console.log(`[${startTime}] 🚀 開始聲音克隆: ${params.type || 'free'} 模式`);
    
    try {
      let audioUrl = params.audio_url;
      
      // 如果有文件上傳，先處理文件上傳
      if (params.audio_file && !audioUrl) {
        console.log(`[${new Date().toLocaleTimeString()}] 📤 開始上傳音頻文件: ${params.audio_file.name}`);
        const uploadResult = await this.uploadAudioFile(params.audio_file);
        audioUrl = uploadResult.data.file_url;
        console.log(`[${new Date().toLocaleTimeString()}] ✅ 音頻文件上傳成功: ${audioUrl}`);
      }
      
      if (!audioUrl) {
        throw new Error('音頻URL或文件是必需的');
      }
      
      // 測試音頻URL可訪問性
      await this.testUrlAccessibility(audioUrl, 'audio');
      
      // 選擇API端點
      const endpoint = params.type === 'paid' ? 
        this.API_ENDPOINTS.VOICE_CLONE_PAID : 
        this.API_ENDPOINTS.VOICE_CLONE_FREE;
      
      console.log(`[${new Date().toLocaleTimeString()}] 📡 調用聲音克隆API: ${endpoint}`);
      
      const response = await this.makeApiRequest(endpoint, {
        token: this.config.token,
        name: params.name,
        audio_url: audioUrl,
        description: params.description
      });
      
      if (response.code === 200 && response.data && response.data.voice_id) {
        console.log(`[${new Date().toLocaleTimeString()}] ✅ 聲音克隆成功: voice_id=${response.data.voice_id}`);
        return {
          voice_id: response.data.voice_id,
          task_id: response.data.task_id
        };
      } else {
        throw new Error(`聲音克隆失敗: ${response.msg || '未知錯誤'}`);
      }
    } catch (error) {
      console.error(`[${new Date().toLocaleTimeString()}] ❌ 聲音克隆錯誤:`, error);
      throw error;
    }
  }

  /**
   * 步驟2：語音合成
   * 支持免費和深度兩種模式
   * 支持同步和異步兩種處理方式
   */
  async synthesizeSpeech(params: SpeechSynthesisParams): Promise<SpeechSynthesisResult> {
    const startTime = new Date().toLocaleTimeString();
    console.log(`[${startTime}] 🎵 開始語音合成: ${params.type || 'free'} 模式`);
    
    try {
      // 選擇API端點
      const endpoint = params.type === 'paid' ? 
        this.API_ENDPOINTS.SPEECH_SYNTHESIS_PAID : 
        this.API_ENDPOINTS.SPEECH_SYNTHESIS_FREE;
      
      console.log(`[${new Date().toLocaleTimeString()}] 📡 調用語音合成API: ${endpoint}`);
      
      const response = await this.makeApiRequest(endpoint, {
        token: this.config.token,
        text: params.text,
        voice_id: params.voice_id
      });
      
      if (response.code === 200 && response.data) {
        if (response.data.audio_url) {
          // 同步模式：立即返回音頻URL
          console.log(`[${new Date().toLocaleTimeString()}] ✅ 語音合成成功 (同步): ${response.data.audio_url}`);
          return {
            audio_url: response.data.audio_url,
            audio_base64: response.data.audio_base64
          };
        } else if (response.data.task_id) {
          // 異步模式：需要輪詢
          console.log(`[${new Date().toLocaleTimeString()}] ⏰ 語音合成進入異步模式: task_id=${response.data.task_id}`);
          return {
            audio_url: '', // 異步模式下暫時為空
            task_id: response.data.task_id
          };
        }
      }
      
      throw new Error(`語音合成失敗: ${response.msg || '未知錯誤'}`);
    } catch (error) {
      console.error(`[${new Date().toLocaleTimeString()}] ❌ 語音合成錯誤:`, error);
      throw error;
    }
  }

  /**
   * 步驟3：數字人克隆
   * 支持免費和付費兩種模式
   * 支持文件上傳和URL兩種視頻來源
   */
  async cloneDigitalHuman(params: DigitalHumanCloneParams): Promise<DigitalHumanCloneResult> {
    const startTime = new Date().toLocaleTimeString();
    console.log(`[${startTime}] 👤 開始數字人克隆: ${params.type || 'free'} 模式`);
    
    try {
      let videoUrl = params.video_url;
      
      // 如果有文件上傳，先處理文件上傳
      if (params.video_file && !videoUrl) {
        console.log(`[${new Date().toLocaleTimeString()}] 📤 開始上傳視頻文件: ${params.video_file.name}`);
        const uploadResult = await this.uploadVideoFile(params.video_file);
        videoUrl = uploadResult.data.file_url;
        console.log(`[${new Date().toLocaleTimeString()}] ✅ 視頻文件上傳成功: ${videoUrl}`);
      }
      
      if (!videoUrl) {
        throw new Error('視頻URL或文件是必需的');
      }
      
      // 測試視頻URL可訪問性
      await this.testUrlAccessibility(videoUrl, 'video');
      
      // 選擇API端點
      const endpoint = params.type === 'paid' ? 
        this.API_ENDPOINTS.DIGITAL_HUMAN_CLONE_PAID : 
        this.API_ENDPOINTS.DIGITAL_HUMAN_CLONE_FREE;
      
      console.log(`[${new Date().toLocaleTimeString()}] 📡 調用數字人克隆API: ${endpoint}`);
      
      const response = await this.makeApiRequest(endpoint, {
        token: this.config.token,
        video_name: params.video_name,
        video_url: videoUrl,
        callback_url: params.callback_url
      });
      
      if (response.code === 200 && response.data && response.data.scene_task_id) {
        console.log(`[${new Date().toLocaleTimeString()}] ✅ 數字人克隆成功: scene_task_id=${response.data.scene_task_id}`);
        return {
          scene_task_id: response.data.scene_task_id
        };
      } else {
        throw new Error(`數字人克隆失敗: ${response.msg || '未知錯誤'}`);
      }
    } catch (error) {
      console.error(`[${new Date().toLocaleTimeString()}] ❌ 數字人克隆錯誤:`, error);
      throw error;
    }
  }

  /**
   * 步驟4：數字人合成
   * 統一使用 Musetalk API
   * 計費：1積分/秒
   */
  async synthesizeDigitalHuman(params: DigitalHumanSynthesisParams): Promise<DigitalHumanSynthesisResult> {
    const startTime = new Date().toLocaleTimeString();
    console.log(`[${startTime}] 🤖 開始數字人合成`);
    
    try {
      console.log(`[${new Date().toLocaleTimeString()}] 📡 調用數字人合成API: ${this.API_ENDPOINTS.DIGITAL_HUMAN_SYNTHESIS}`);
      
      const response = await this.makeApiRequest(this.API_ENDPOINTS.DIGITAL_HUMAN_SYNTHESIS, {
        token: this.config.token,
        scene_task_id: params.scene_task_id.toString(),
        audio_url: params.audio_url,
        callback_url: params.callback_url
      });
      
      if (response.code === 200 && response.data && response.data.video_task_id) {
        console.log(`[${new Date().toLocaleTimeString()}] ✅ 數字人合成提交成功: video_task_id=${response.data.video_task_id}`);
        return {
          video_task_id: response.data.video_task_id,
          bill_id: response.data.bill_id
        };
      } else {
        throw new Error(`數字人合成失敗: ${response.msg || '未知錯誤'}`);
      }
    } catch (error) {
      console.error(`[${new Date().toLocaleTimeString()}] ❌ 數字人合成錯誤:`, error);
      throw error;
    }
  }

  /**
   * 步驟5：任務狀態查詢
   * 用於輪詢數字人合成進度
   * 免費API，1qps/s限制
   */
  async getTaskStatus(videoTaskId: number): Promise<TaskStatusResult> {
    try {
      console.log(`[${new Date().toLocaleTimeString()}] 🔍 查詢任務狀態: video_task_id=${videoTaskId}`);
      
      const response = await this.makeApiRequest(
        `${this.API_ENDPOINTS.TASK_STATUS}&task_id=${videoTaskId}`,
        null,
        'GET'
      );
      
      if (response.code === 200 && response.data) {
        console.log(`[${new Date().toLocaleTimeString()}] 📊 任務狀態: state=${response.data.state}`);
        return response.data;
      } else {
        throw new Error(`查詢任務狀態失敗: ${response.msg || '未知錯誤'}`);
      }
    } catch (error) {
      console.error(`[${new Date().toLocaleTimeString()}] ❌ 查詢任務狀態錯誤:`, error);
      throw error;
    }
  }

  /**
   * 輪詢任務直到完成
   * 最大輪詢時間：10分鐘
   * 輪詢間隔：30秒
   */
  async pollTaskUntilComplete(
    videoTaskId: number, 
    onProgress?: (status: TaskStatusResult) => void
  ): Promise<TaskStatusResult> {
    const maxPollingTime = 10 * 60 * 1000; // 10分鐘
    const pollingInterval = 30 * 1000; // 30秒
    const startTime = Date.now();
    
    console.log(`[${new Date().toLocaleTimeString()}] ⏰ 開始輪詢任務: video_task_id=${videoTaskId}`);
    
    while (Date.now() - startTime < maxPollingTime) {
      try {
        const status = await this.getTaskStatus(videoTaskId);
        
        if (onProgress) {
          onProgress(status);
        }
        
        if (status.state === 20) {
          console.log(`[${new Date().toLocaleTimeString()}] ✅ 任務完成: ${status.videoUrl}`);
          return status;
        }
        
        console.log(`[${new Date().toLocaleTimeString()}] ⏳ 任務進行中，30秒後重試...`);
        await this.sleep(pollingInterval);
        
      } catch (error) {
        console.warn(`[${new Date().toLocaleTimeString()}] ⚠️ 輪詢錯誤，繼續重試:`, error);
        await this.sleep(pollingInterval);
      }
    }
    
    throw new Error('任務輪詢超時（10分鐘）');
  }

  /**
   * 上傳音頻文件
   * 優先使用新的簡單上傳系統，失敗時使用Base64備用
   */
  private async uploadAudioFile(file: File): Promise<FileUploadResult> {
    const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
    console.log(`[${new Date().toLocaleTimeString()}] 📤 上傳音頻文件: ${file.name} (${fileSizeMB}MB)`);
    
    try {
      // 優先使用新的簡單上傳系統
      const formData = new FormData();
      formData.append('audio', file);
      
      const response = await fetch(this.API_ENDPOINTS.UPLOAD_AUDIO_SIMPLE, {
        method: 'POST',
        body: formData
      });
      
      const result = await response.json();
      
      if (result.success) {
        console.log(`[${new Date().toLocaleTimeString()}] ✅ 新系統上傳成功: ${result.data.file_url}`);
        return result;
      } else {
        throw new Error('新系統上傳失敗: ' + result.message);
      }
    } catch (error) {
      console.warn(`[${new Date().toLocaleTimeString()}] ⚠️ 新系統上傳失敗，嘗試Base64備用:`, error);
      
      // 使用Base64備用系統
      return this.uploadFileAsBase64(file, 'audio');
    }
  }

  /**
   * 上傳視頻文件
   * 優先使用新的簡單上傳系統，失敗時使用Base64備用
   */
  private async uploadVideoFile(file: File): Promise<FileUploadResult> {
    const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
    console.log(`[${new Date().toLocaleTimeString()}] 📤 上傳視頻文件: ${file.name} (${fileSizeMB}MB)`);
    
    try {
      // 優先使用新的簡單上傳系統
      const formData = new FormData();
      formData.append('video', file);
      
      const response = await fetch(this.API_ENDPOINTS.UPLOAD_VIDEO_SIMPLE, {
        method: 'POST',
        body: formData
      });
      
      const result = await response.json();
      
      if (result.success) {
        console.log(`[${new Date().toLocaleTimeString()}] ✅ 新系統上傳成功: ${result.data.file_url}`);
        return result;
      } else {
        throw new Error('新系統上傳失敗: ' + result.message);
      }
    } catch (error) {
      console.warn(`[${new Date().toLocaleTimeString()}] ⚠️ 新系統上傳失敗，嘗試Base64備用:`, error);
      
      // 使用Base64備用系統
      return this.uploadFileAsBase64(file, 'video');
    }
  }

  /**
   * Base64文件上傳備用方案
   */
  private async uploadFileAsBase64(file: File, type: 'audio' | 'video'): Promise<FileUploadResult> {
    console.log(`[${new Date().toLocaleTimeString()}] 📤 使用Base64上傳: ${file.name}`);
    
    const fileData = await new Promise<string>((resolve, reject) => {
      const reader = new FileReader();
      reader.onload = () => {
        const base64 = (reader.result as string).split(',')[1];
        resolve(base64);
      };
      reader.onerror = reject;
      reader.readAsDataURL(file);
    });
    
    const endpoint = type === 'audio' ? 
      this.API_ENDPOINTS.UPLOAD_AUDIO_BASE64 : 
      this.API_ENDPOINTS.UPLOAD_VIDEO_BASE64;
    
    const response = await fetch(endpoint, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        fileName: file.name,
        fileData: fileData,
        fileSize: file.size
      })
    });
    
    const result = await response.json();
    
    if (result.success) {
      console.log(`[${new Date().toLocaleTimeString()}] ✅ Base64上傳成功: ${result.data.file_url}`);
      return result;
    } else {
      throw new Error('Base64上傳失敗: ' + result.message);
    }
  }

  /**
   * 測試URL可訪問性
   */
  private async testUrlAccessibility(url: string, type: 'audio' | 'video'): Promise<void> {
    try {
      console.log(`[${new Date().toLocaleTimeString()}] 🧪 測試${type}URL可訪問性: ${url}`);
      
      const response = await fetch(url, {
        method: 'HEAD',
        mode: 'cors'
      });
      
      console.log(`[${new Date().toLocaleTimeString()}] 🧪 URL測試結果: ${response.status} ${response.statusText}`);
      console.log(`[${new Date().toLocaleTimeString()}] 🧪 Content-Type: ${response.headers.get('content-type')}`);
      console.log(`[${new Date().toLocaleTimeString()}] 🧪 Content-Length: ${response.headers.get('content-length')} bytes`);
      
      if (response.status !== 200) {
        console.warn(`[${new Date().toLocaleTimeString()}] ⚠️ URL返回非200狀態，但繼續嘗試API調用`);
      }
    } catch (error) {
      console.warn(`[${new Date().toLocaleTimeString()}] ⚠️ URL測試失敗: ${error.message}，但繼續嘗試API調用`);
    }
  }

  /**
   * 統一API請求方法
   */
  private async makeApiRequest(endpoint: string, data: any, method: 'POST' | 'GET' = 'POST'): Promise<any> {
    const requestOptions: RequestInit = {
      method,
      headers: {
        'Content-Type': 'application/json'
      }
    };
    
    if (method === 'POST' && data) {
      requestOptions.body = JSON.stringify(data);
    }
    
    const response = await fetch(endpoint, requestOptions);
    
    if (!response.ok) {
      throw new Error(`HTTP錯誤: ${response.status} ${response.statusText}`);
    }
    
    return await response.json();
  }

  /**
   * 睡眠函數
   */
  private sleep(ms: number): Promise<void> {
    return new Promise(resolve => setTimeout(resolve, ms));
  }

  /**
   * 完整的數字人製作工作流程
   * 按照用戶提供的九步驟流程執行
   */
  async executeCompleteWorkflow(params: {
    // 聲音相關
    voiceName: string;
    voiceDescription: string;
    audioFile?: File;
    audioUrl?: string;
    voiceType?: 'free' | 'paid';
    
    // 語音合成相關
    speechText: string;
    speechType?: 'free' | 'paid';
    
    // 數字人相關
    videoName: string;
    videoFile?: File;
    videoUrl?: string;
    digitalHumanType?: 'free' | 'paid';
    
    // 回調URL
    callbackUrl: string;
    
    // 進度回調
    onProgress?: (step: number, message: string, data?: any) => void;
  }): Promise<TaskStatusResult> {
    const { onProgress } = params;
    
    try {
      // 步驟1：聲音克隆
      onProgress?.(1, '開始聲音克隆...');
      const voiceResult = await this.cloneVoice({
        name: params.voiceName,
        description: params.voiceDescription,
        audio_file: params.audioFile,
        audio_url: params.audioUrl,
        type: params.voiceType || 'free'
      });
      onProgress?.(1, '聲音克隆完成', voiceResult);
      
      // 步驟2：語音合成
      onProgress?.(2, '開始語音合成...');
      const speechResult = await this.synthesizeSpeech({
        text: params.speechText,
        voice_id: voiceResult.voice_id,
        type: params.speechType || 'free'
      });
      onProgress?.(2, '語音合成完成', speechResult);
      
      // 步驟3：數字人克隆
      onProgress?.(3, '開始數字人克隆...');
      const cloneResult = await this.cloneDigitalHuman({
        video_name: params.videoName,
        video_file: params.videoFile,
        video_url: params.videoUrl,
        callback_url: params.callbackUrl,
        type: params.digitalHumanType || 'free'
      });
      onProgress?.(3, '數字人克隆完成', cloneResult);
      
      // 步驟4：數字人合成
      onProgress?.(4, '開始數字人合成...');
      const synthesisResult = await this.synthesizeDigitalHuman({
        scene_task_id: cloneResult.scene_task_id,
        audio_url: speechResult.audio_url,
        callback_url: params.callbackUrl
      });
      onProgress?.(4, '數字人合成提交完成，開始輪詢...', synthesisResult);
      
      // 步驟5：輪詢直到完成
      const finalResult = await this.pollTaskUntilComplete(
        synthesisResult.video_task_id,
        (status) => {
          onProgress?.(5, `輪詢中... 狀態: ${status.state}`, status);
        }
      );
      
      onProgress?.(5, '數字人製作完成！', finalResult);
      return finalResult;
      
    } catch (error) {
      console.error('完整工作流程執行失敗:', error);
      throw error;
    }
  }
}

// 導出默認實例創建函數
export function createGenHumanApi(config: GenHumanConfig): GenHumanApiWrapper {
  return new GenHumanApiWrapper(config);
}

// 導出常量
export const GENHUMAN_CONSTANTS = {
  TASK_STATE: {
    COMPLETED: 20
  },
  POLLING: {
    MAX_TIME: 10 * 60 * 1000, // 10分鐘
    INTERVAL: 30 * 1000 // 30秒
  },
  FILE_LIMITS: {
    MAX_AUDIO_SIZE: 50 * 1024 * 1024, // 50MB
    MAX_VIDEO_SIZE: 200 * 1024 * 1024 // 200MB
  }
};