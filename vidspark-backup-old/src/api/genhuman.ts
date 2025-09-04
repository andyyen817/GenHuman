import { apiClient } from './config'
import type { ApiResponse } from './config'

// GenHuman API 封裝
// 復用現有GenHuman API，但使用新的封裝方式

// 數字人相關API
export interface DigitalHumanData {
  id: string
  name: string
  description: string
  preview_image: string
  demo_video?: string
  is_free: boolean
  gender: 'male' | 'female' | 'other'
  style: 'professional' | 'casual' | 'friendly'
}

export interface VideoGenerationRequest {
  avatar_id: string
  text: string
  voice_id?: string
  background_id?: string
  language?: string
}

export interface VideoGenerationResponse {
  task_id: string
  status: 'pending' | 'processing' | 'completed' | 'failed'
  video_url?: string
  progress?: number
  estimated_time?: number
}

// 獲取數字人列表
export function getDigitalHumans(params?: {
  is_free?: boolean
  gender?: string
  style?: string
}): Promise<ApiResponse<DigitalHumanData[]>> {
  return apiClient.get('/digitalhuman/list', { params })
}

// 生成數字人影片
export function generateDigitalHumanVideo(
  data: VideoGenerationRequest
): Promise<ApiResponse<VideoGenerationResponse>> {
  return apiClient.post('/digitalhuman/generate', data)
}

// 查詢影片生成狀態
export function getVideoGenerationStatus(taskId: string): Promise<ApiResponse<VideoGenerationResponse>> {
  return apiClient.get(`/digitalhuman/status/${taskId}`)
}

// 聲音克隆相關API
export interface VoiceModel {
  id: string
  name: string
  description: string
  sample_audio_url: string
  is_custom: boolean
  language: string
  gender: 'male' | 'female'
}

export interface VoiceCloneRequest {
  name: string
  audio_file: File | Blob
  description?: string
}

export interface VoiceCloneResponse {
  task_id: string
  status: 'pending' | 'processing' | 'completed' | 'failed'
  voice_id?: string
  progress?: number
  estimated_time?: number
}

// 獲取聲音列表
export function getVoiceModels(params?: {
  is_custom?: boolean
  language?: string
  gender?: string
}): Promise<ApiResponse<VoiceModel[]>> {
  return apiClient.get('/voice/list', { params })
}

// 聲音克隆
export function cloneVoice(data: VoiceCloneRequest): Promise<ApiResponse<VoiceCloneResponse>> {
  const formData = new FormData()
  formData.append('name', data.name)
  formData.append('audio_file', data.audio_file)
  if (data.description) {
    formData.append('description', data.description)
  }
  
  return apiClient.post('/voice/clone', formData, {
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  })
}

// 查詢聲音克隆狀態
export function getVoiceCloneStatus(taskId: string): Promise<ApiResponse<VoiceCloneResponse>> {
  return apiClient.get(`/voice/clone/status/${taskId}`)
}

// 用戶相關API
export interface UserInfo {
  id: string
  email: string
  nickname: string
  avatar_url?: string
  membership_level: 'free' | 'basic' | 'pro' | 'enterprise'
  created_at: string
}

export interface UserQuota {
  daily_video_quota: number
  daily_video_used: number
  monthly_voice_quota: number
  monthly_voice_used: number
  total_storage_gb: number
  used_storage_gb: number
}

export interface LoginRequest {
  email: string
  password: string
}

export interface RegisterRequest {
  email: string
  password: string
  nickname?: string
}

export interface AuthResponse {
  token: string
  user: UserInfo
  expires_at: string
}

// 用戶登入
export function login(data: LoginRequest): Promise<ApiResponse<AuthResponse>> {
  return apiClient.post('/user/login', data)
}

// 用戶註冊
export function register(data: RegisterRequest): Promise<ApiResponse<AuthResponse>> {
  return apiClient.post('/user/register', data)
}

// 獲取用戶信息
export function getUserInfo(): Promise<ApiResponse<UserInfo>> {
  return apiClient.get('/user/info')
}

// 獲取用戶額度信息
export function getUserQuota(): Promise<ApiResponse<UserQuota>> {
  return apiClient.get('/user/quota')
}

// 項目管理相關API
export interface Project {
  id: string
  title: string
  description?: string
  scenario_type: 'avatar_video' | 'text_to_video' | 'voice_clone'
  status: 'draft' | 'processing' | 'completed' | 'failed'
  config: any
  result_data?: any
  created_at: string
  updated_at: string
}

// 獲取項目列表
export function getProjects(params?: {
  scenario_type?: string
  status?: string
  page?: number
  limit?: number
}): Promise<ApiResponse<{ projects: Project[], total: number }>> {
  return apiClient.get('/project/list', { params })
}

// 創建項目
export function createProject(data: Partial<Project>): Promise<ApiResponse<Project>> {
  return apiClient.post('/project/create', data)
}

// 更新項目
export function updateProject(id: string, data: Partial<Project>): Promise<ApiResponse<Project>> {
  return apiClient.put(`/project/${id}`, data)
}

// 刪除項目
export function deleteProject(id: string): Promise<ApiResponse<void>> {
  return apiClient.delete(`/project/${id}`)
}
