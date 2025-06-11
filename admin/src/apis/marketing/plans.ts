import http from '@/utils/http'

/** 获取套餐数据 */
export function getPlansList(params: Pagination) {
    return http.get<any>('/Plans/getList', params)
}
/** 获取套餐数据 */
export function savePlansDetail(params: any) {
    return http.post<any>('/Plans/savePlans', params)
}

/** 获取套餐详情 */
export function getPlansDetail(params: { id: string }) {
    return http.get<any>('/Plans/getDetail', params)
}
/**删除 */
export function deletePlans(params: { id: string | number }) {
    return http.post<any>('/Plans/delete', params)
}

/**获取选项 */
export function getOptions() {
    return http.get<any>('/Plans/getOptions')
}