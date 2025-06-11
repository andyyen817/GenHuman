import http from '@/utils/http'

/** 获取场景数据 */
export function getSceneList(params: Pagination) {
    return http.get<any>('/Scene/getList', params)
}
/**删除 */
export function deleteScene(id: string | number) {
    return http.post<any>('/Scene/delete', { id })
}