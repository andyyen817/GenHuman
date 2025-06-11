import http from '@/utils/http'

/** 获取数据 */
export function getCategoryList(params: Pagination) {
    return http.get<any>('/Category/getList', params)
}
export function getCategoryOptions() {
    return http.get<any>('/Category/getOptions')
}
/** 编辑数据 */
export function saveCategory(params: any) {
    return http.post<any>('/Category/saveCategory', params)
}

/** 获取详情 */
export function getCategoryDetail(params: { id: string }) {
    return http.get<any>('/Category/getDetail', params)
}
/**删除 */
export function deleteCategory(params: { id: string | number }) {
    return http.post<any>('/Category/delete', params)
}


