import http from '@/utils/http'

/** 获取作品数据 */
export function getWorksList(params: Pagination) {
    return http.get<any>('/Works/getList', params)
}



/**删除 */
export function deleteWorks(id: number) {
    return http.post<any>('/Works/delete', { id })
}