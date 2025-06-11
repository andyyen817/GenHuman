import http from '@/utils/http'


/** 获取配置 */
export function getConfigData(params: { key: string }) {
    return http.get<any>('/Config/getConfig', params)
}
/** 保存配置 */
export function saveConfig(params: { key: string, value: any }) {
    return http.post<any>('/Config/save', params)
}

/**站点配置 */
export function getSiteConfig() {
    return http.get('/Config/getSiteConfig')
}