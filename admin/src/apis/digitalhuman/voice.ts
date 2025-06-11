import http from '@/utils/http'

/** 获取音色数据 */
export function getVoiceList(params: Pagination) {
    return http.get<any>('/Voice/getList', params)
}


/**删除 */
export function deleteVoice(id: string | number) {
    return http.post<any>('/Voice/delete', { id })
}