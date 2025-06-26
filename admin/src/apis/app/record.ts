import http from '@/utils/http'

/** 获取记录 */
export function getRecordList(params: Pagination) {
    return http.get<any>('/Record/getList', params)
}

/** 删除 */
export function deleteRecord(id: string | number) {
    return http.post<any>('/Record/delete', { id })
}