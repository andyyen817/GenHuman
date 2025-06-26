import http from '@/utils/http'

/** 获取应用数据 */
export function getAppList(params: Pagination) {
    return http.get<any>('/App/getList', params)
}
/** 获取应用数据 */
export function saveAppDetail(params: any) {
    return http.post<any>('/App/saveApp', params)
}

/** 获取应用详情 */
export function getAppDetail(params: { id: string }) {
    return http.get<any>('/App/getDetail', params)
}
/**删除 */
export function deleteApp(params: { id: string | number }) {
    return http.post<any>('/App/delete', params)
}
