
import http from '@/utils/http'
/** 获取数据 */
export function getArticleList(params: Pagination) {
    return http.get<any>('/Article/getList', params)
}
/** 编辑数据 */
export function saveArticle(params: any) {
    return http.post<any>('/Article/saveArticle', params)
}

/** 获取详情 */
export function getArticleDetail(params: { id: string }) {
    return http.get<any>('/Article/getDetail', params)
}
/**删除 */
export function deleteArticle(params: { id: string | number }) {
    return http.post<any>('/Article/delete', params)
}
