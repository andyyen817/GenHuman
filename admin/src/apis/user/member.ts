import http from '@/utils/http'

/**获取用户列表 */
export function getUserList(params) {
    return http.get('User/getList', params)
}

/**充值 */
export function rechargeUser(params) {
    return http.post('User/recharge', params)
}