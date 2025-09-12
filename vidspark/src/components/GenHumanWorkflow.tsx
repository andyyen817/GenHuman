/**
 * GenHuman 數字人製作工作流程組件
 * 基於 GenHumanApiWrapper 封裝層
 * 實現完整的九步驟數字人製作流程
 */

import React, { useState, useRef } from 'react';
import { 
  GenHumanApiWrapper, 
  createGenHumanApi, 
  TaskStatusResult,
  GENHUMAN_CONSTANTS 
} from '../services/GenHumanApiWrapper';

interface WorkflowStep {
  id: number;
  title: string;
  description: string;
  status: 'pending' | 'processing' | 'completed' | 'error';
  result?: any;
  error?: string;
}

interface WorkflowData {
  // API配置
  token: string;
  
  // 聲音相關
  voiceName: string;
  voiceDescription: string;
  audioFile?: File;
  audioUrl?: string;
  voiceType: 'free' | 'paid';
  
  // 語音合成相關
  speechText: string;
  speechType: 'free' | 'paid';
  
  // 數字人相關
  videoName: string;
  videoFile?: File;
  videoUrl?: string;
  digitalHumanType: 'free' | 'paid';
  
  // 回調URL
  callbackUrl: string;
}

const GenHumanWorkflow: React.FC = () => {
  const [workflowData, setWorkflowData] = useState<WorkflowData>({
    token: localStorage.getItem('genhuman_token') || '',
    voiceName: '',
    voiceDescription: '',
    voiceType: 'free',
    speechText: '',
    speechType: 'free',
    videoName: '',
    digitalHumanType: 'free',
    callbackUrl: 'https://baidu.com'
  });
  
  const [steps, setSteps] = useState<WorkflowStep[]>([
    { id: 1, title: '聲音克隆', description: '上傳音頻文件並創建聲音模型', status: 'pending' },
    { id: 2, title: '語音合成', description: '使用聲音模型合成指定文本', status: 'pending' },
    { id: 3, title: '數字人克隆', description: '上傳視頻文件並創建數字人模型', status: 'pending' },
    { id: 4, title: '數字人合成', description: '合成數字人說話視頻', status: 'pending' },
    { id: 5, title: '任務輪詢', description: '等待視頻生成完成', status: 'pending' }
  ]);
  
  const [isProcessing, setIsProcessing] = useState(false);
  const [finalResult, setFinalResult] = useState<TaskStatusResult | null>(null);
  const [logs, setLogs] = useState<string[]>([]);
  
  const audioFileRef = useRef<HTMLInputElement>(null);
  const videoFileRef = useRef<HTMLInputElement>(null);
  
  // 添加日誌
  const addLog = (message: string) => {
    const timestamp = new Date().toLocaleTimeString();
    const logMessage = `[${timestamp}] ${message}`;
    setLogs(prev => [...prev, logMessage]);
    console.log(logMessage);
  };
  
  // 更新步驟狀態
  const updateStepStatus = (stepId: number, status: WorkflowStep['status'], result?: any, error?: string) => {
    setSteps(prev => prev.map(step => 
      step.id === stepId 
        ? { ...step, status, result, error }
        : step
    ));
  };
  
  // 處理文件選擇
  const handleAudioFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      if (file.size > GENHUMAN_CONSTANTS.FILE_LIMITS.MAX_AUDIO_SIZE) {
        alert(`音頻文件大小不能超過 ${GENHUMAN_CONSTANTS.FILE_LIMITS.MAX_AUDIO_SIZE / (1024 * 1024)}MB`);
        return;
      }
      setWorkflowData(prev => ({ ...prev, audioFile: file }));
      addLog(`選擇音頻文件: ${file.name} (${(file.size / (1024 * 1024)).toFixed(2)}MB)`);
    }
  };
  
  const handleVideoFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      if (file.size > GENHUMAN_CONSTANTS.FILE_LIMITS.MAX_VIDEO_SIZE) {
        alert(`視頻文件大小不能超過 ${GENHUMAN_CONSTANTS.FILE_LIMITS.MAX_VIDEO_SIZE / (1024 * 1024)}MB`);
        return;
      }
      setWorkflowData(prev => ({ ...prev, videoFile: file }));
      addLog(`選擇視頻文件: ${file.name} (${(file.size / (1024 * 1024)).toFixed(2)}MB)`);
    }
  };
  
  // 保存Token到本地存儲
  const handleTokenChange = (token: string) => {
    setWorkflowData(prev => ({ ...prev, token }));
    localStorage.setItem('genhuman_token', token);
  };
  
  // 執行完整工作流程
  const executeWorkflow = async () => {
    if (!workflowData.token) {
      alert('請輸入API Token');
      return;
    }
    
    if (!workflowData.voiceName || !workflowData.speechText || !workflowData.videoName) {
      alert('請填寫所有必需字段');
      return;
    }
    
    if (!workflowData.audioFile && !workflowData.audioUrl) {
      alert('請上傳音頻文件或提供音頻URL');
      return;
    }
    
    if (!workflowData.videoFile && !workflowData.videoUrl) {
      alert('請上傳視頻文件或提供視頻URL');
      return;
    }
    
    setIsProcessing(true);
    setFinalResult(null);
    setLogs([]);
    
    // 重置所有步驟狀態
    setSteps(prev => prev.map(step => ({ ...step, status: 'pending', result: undefined, error: undefined })));
    
    try {
      addLog('🚀 開始執行完整數字人製作工作流程');
      
      // 創建API實例
      const api = createGenHumanApi({ token: workflowData.token });
      
      // 執行完整工作流程
      const result = await api.executeCompleteWorkflow({
        voiceName: workflowData.voiceName,
        voiceDescription: workflowData.voiceDescription,
        audioFile: workflowData.audioFile,
        audioUrl: workflowData.audioUrl,
        voiceType: workflowData.voiceType,
        speechText: workflowData.speechText,
        speechType: workflowData.speechType,
        videoName: workflowData.videoName,
        videoFile: workflowData.videoFile,
        videoUrl: workflowData.videoUrl,
        digitalHumanType: workflowData.digitalHumanType,
        callbackUrl: workflowData.callbackUrl,
        onProgress: (step, message, data) => {
          addLog(`步驟${step}: ${message}`);
          
          if (data) {
            updateStepStatus(step, 'completed', data);
            if (step < 5) {
              updateStepStatus(step + 1, 'processing');
            }
          } else {
            updateStepStatus(step, 'processing');
          }
        }
      });
      
      setFinalResult(result);
      addLog('🎉 數字人製作完成！');
      addLog(`📹 視頻URL: ${result.videoUrl}`);
      addLog(`🖼️ 封面URL: ${result.coverUrl}`);
      addLog(`⏱️ 視頻時長: ${result.duration}秒`);
      addLog(`💡 ${result.tips}`);
      
    } catch (error) {
      const errorMessage = error instanceof Error ? error.message : '未知錯誤';
      addLog(`❌ 工作流程執行失敗: ${errorMessage}`);
      
      // 標記當前處理中的步驟為錯誤
      const processingStep = steps.find(step => step.status === 'processing');
      if (processingStep) {
        updateStepStatus(processingStep.id, 'error', undefined, errorMessage);
      }
    } finally {
      setIsProcessing(false);
    }
  };
  
  // 獲取步驟狀態圖標
  const getStepIcon = (status: WorkflowStep['status']) => {
    switch (status) {
      case 'pending': return '⏳';
      case 'processing': return '🔄';
      case 'completed': return '✅';
      case 'error': return '❌';
      default: return '⏳';
    }
  };
  
  // 獲取步驟狀態顏色
  const getStepColor = (status: WorkflowStep['status']) => {
    switch (status) {
      case 'pending': return 'text-gray-500';
      case 'processing': return 'text-blue-500';
      case 'completed': return 'text-green-500';
      case 'error': return 'text-red-500';
      default: return 'text-gray-500';
    }
  };
  
  return (
    <div className="max-w-6xl mx-auto p-6 bg-white">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900 mb-2">GenHuman 數字人製作工廠</h1>
        <p className="text-gray-600">完整的九步驟數字人製作工作流程</p>
      </div>
      
      {/* API配置區域 */}
      <div className="bg-gray-50 p-6 rounded-lg mb-8">
        <h2 className="text-xl font-semibold mb-4">🔑 API 配置</h2>
        <div className="grid grid-cols-1 gap-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              API Token *
            </label>
            <input
              type="password"
              value={workflowData.token}
              onChange={(e) => handleTokenChange(e.target.value)}
              placeholder="請輸入您的 GenHuman API Token"
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              回調URL
            </label>
            <input
              type="url"
              value={workflowData.callbackUrl}
              onChange={(e) => setWorkflowData(prev => ({ ...prev, callbackUrl: e.target.value }))}
              placeholder="https://your-callback-url.com"
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
        </div>
      </div>
      
      {/* 工作流程配置 */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        {/* 聲音配置 */}
        <div className="bg-blue-50 p-6 rounded-lg">
          <h2 className="text-xl font-semibold mb-4">🎵 聲音配置</h2>
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                聲音名稱 *
              </label>
              <input
                type="text"
                value={workflowData.voiceName}
                onChange={(e) => setWorkflowData(prev => ({ ...prev, voiceName: e.target.value }))}
                placeholder="例如：張三的聲音"
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                聲音描述 *
              </label>
              <textarea
                value={workflowData.voiceDescription}
                onChange={(e) => setWorkflowData(prev => ({ ...prev, voiceDescription: e.target.value }))}
                placeholder="描述這個聲音的特點..."
                rows={3}
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                音頻文件 *
              </label>
              <input
                ref={audioFileRef}
                type="file"
                accept=".mp3,.m4a,.wav"
                onChange={handleAudioFileChange}
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
              {workflowData.audioFile && (
                <p className="text-sm text-gray-600 mt-1">
                  已選擇: {workflowData.audioFile.name}
                </p>
              )}
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                或音頻URL
              </label>
              <input
                type="url"
                value={workflowData.audioUrl || ''}
                onChange={(e) => setWorkflowData(prev => ({ ...prev, audioUrl: e.target.value }))}
                placeholder="https://example.com/audio.mp3"
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                聲音克隆類型
              </label>
              <select
                value={workflowData.voiceType}
                onChange={(e) => setWorkflowData(prev => ({ ...prev, voiceType: e.target.value as 'free' | 'paid' }))}
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="free">免費克隆</option>
                <option value="paid">深度克隆（付費）</option>
              </select>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                要合成的文本 *
              </label>
              <textarea
                value={workflowData.speechText}
                onChange={(e) => setWorkflowData(prev => ({ ...prev, speechText: e.target.value }))}
                placeholder="請輸入要讓數字人說的話..."
                rows={4}
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                語音合成類型
              </label>
              <select
                value={workflowData.speechType}
                onChange={(e) => setWorkflowData(prev => ({ ...prev, speechType: e.target.value as 'free' | 'paid' }))}
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="free">免費合成</option>
                <option value="paid">深度合成（付費）</option>
              </select>
            </div>
          </div>
        </div>
        
        {/* 數字人配置 */}
        <div className="bg-green-50 p-6 rounded-lg">
          <h2 className="text-xl font-semibold mb-4">👤 數字人配置</h2>
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                數字人名稱 *
              </label>
              <input
                type="text"
                value={workflowData.videoName}
                onChange={(e) => setWorkflowData(prev => ({ ...prev, videoName: e.target.value }))}
                placeholder="例如：辦公室場景"
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                視頻文件 *
              </label>
              <input
                ref={videoFileRef}
                type="file"
                accept=".mp4,.mov,.avi"
                onChange={handleVideoFileChange}
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
              />
              {workflowData.videoFile && (
                <p className="text-sm text-gray-600 mt-1">
                  已選擇: {workflowData.videoFile.name}
                </p>
              )}
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                或視頻URL
              </label>
              <input
                type="url"
                value={workflowData.videoUrl || ''}
                onChange={(e) => setWorkflowData(prev => ({ ...prev, videoUrl: e.target.value }))}
                placeholder="https://example.com/video.mp4"
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                數字人克隆類型
              </label>
              <select
                value={workflowData.digitalHumanType}
                onChange={(e) => setWorkflowData(prev => ({ ...prev, digitalHumanType: e.target.value as 'free' | 'paid' }))}
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
              >
                <option value="free">免費克隆</option>
                <option value="paid">高級克隆（付費）</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      
      {/* 執行按鈕 */}
      <div className="text-center mb-8">
        <button
          onClick={executeWorkflow}
          disabled={isProcessing}
          className={`px-8 py-3 rounded-lg text-white font-semibold text-lg ${
            isProcessing 
              ? 'bg-gray-400 cursor-not-allowed' 
              : 'bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500'
          }`}
        >
          {isProcessing ? '🔄 製作中...' : '🚀 開始製作數字人'}
        </button>
      </div>
      
      {/* 工作流程步驟 */}
      <div className="bg-gray-50 p-6 rounded-lg mb-8">
        <h2 className="text-xl font-semibold mb-4">📋 工作流程進度</h2>
        <div className="space-y-4">
          {steps.map((step) => (
            <div key={step.id} className={`flex items-center p-4 rounded-lg border ${
              step.status === 'completed' ? 'bg-green-50 border-green-200' :
              step.status === 'processing' ? 'bg-blue-50 border-blue-200' :
              step.status === 'error' ? 'bg-red-50 border-red-200' :
              'bg-white border-gray-200'
            }`}>
              <div className="text-2xl mr-4">
                {getStepIcon(step.status)}
              </div>
              <div className="flex-1">
                <h3 className={`font-semibold ${getStepColor(step.status)}`}>
                  步驟 {step.id}: {step.title}
                </h3>
                <p className="text-gray-600 text-sm">{step.description}</p>
                {step.error && (
                  <p className="text-red-600 text-sm mt-1">錯誤: {step.error}</p>
                )}
              </div>
            </div>
          ))}
        </div>
      </div>
      
      {/* 最終結果 */}
      {finalResult && (
        <div className="bg-green-50 p-6 rounded-lg mb-8">
          <h2 className="text-xl font-semibold mb-4">🎉 製作完成</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h3 className="font-semibold mb-2">視頻預覽</h3>
              <video 
                controls 
                className="w-full rounded-lg"
                poster={finalResult.coverUrl}
              >
                <source src={finalResult.videoUrl} type="video/mp4" />
                您的瀏覽器不支持視頻播放。
              </video>
            </div>
            <div>
              <h3 className="font-semibold mb-2">視頻信息</h3>
              <div className="space-y-2 text-sm">
                <p><strong>視頻名稱:</strong> {finalResult.videoName}</p>
                <p><strong>時長:</strong> {finalResult.duration} 秒</p>
                <p><strong>任務ID:</strong> {finalResult.video_task_id}</p>
                <p className="text-orange-600"><strong>提示:</strong> {finalResult.tips}</p>
              </div>
              <div className="mt-4 space-y-2">
                <a 
                  href={finalResult.videoUrl} 
                  download
                  className="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                >
                  📥 下載視頻
                </a>
                <a 
                  href={finalResult.coverUrl} 
                  download
                  className="block w-full text-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700"
                >
                  🖼️ 下載封面
                </a>
              </div>
            </div>
          </div>
        </div>
      )}
      
      {/* 執行日誌 */}
      {logs.length > 0 && (
        <div className="bg-gray-900 text-green-400 p-4 rounded-lg">
          <h2 className="text-lg font-semibold mb-2 text-white">📝 執行日誌</h2>
          <div className="max-h-64 overflow-y-auto font-mono text-sm">
            {logs.map((log, index) => (
              <div key={index} className="mb-1">
                {log}
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
};

export default GenHumanWorkflow;