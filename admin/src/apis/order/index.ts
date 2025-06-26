import http from '@/utils/http'

/** 获取订单 */
export function getOrderList(params: Pagination) {
    return http.get<any>('/Order/getList', params)
}

/** 删除 */
export function deleteOrder(id: string | number) {
    return http.post<any>('/Order/delete', { id })
}