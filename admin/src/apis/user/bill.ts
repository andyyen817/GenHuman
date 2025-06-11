import http from '@/utils/http'

/**获取用户列表 */
export function getBillList(params) {
    return http.get('/Bill/getList', params)
}