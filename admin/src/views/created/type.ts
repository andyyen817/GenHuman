export interface BaseInterface {
    scenePoint: number | string
    voicePoint: number | string
    worksPoint: number | string
    aiCwPoint: number | string
    // voiceProfessionalPoint: number | string
    voiceDeepPoint: number | string
}

export interface YiDingInterface {
    worksChannel: string | number //创建类型
    voiceChannel: string | number //语音类型
    token: string //语音token
}