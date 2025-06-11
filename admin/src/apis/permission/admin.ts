import type * as T from './type'
import http from '@/utils/http'

import type { MenuItem } from '@/apis/permission'

export type * from './type'

/** 获取用户数据 */
export function getUserList(params: Pagination) {
  return http.get<PageRes<T.UserItem[]>>('/permission/User/getUserList', params)
}

/** 获取用户详情 */
export function getUserDetail(params: { id: string }) {
  return http.get<T.UserDetail>('/permission/User/getUserDetail', params)
}

/**
* 编辑用户信息
* @author:下次一定
* @email:1950781041@qq.com
* @Date:2025-05-22
*/
export function saveUser(params: T.UserDetail) {
  return http.post('/permission/User/saveUser', params)
}



/** 登录 */
export function login(data: { username: string, password: string }) {
  return http.post<T.Login>('/permission/User/login', data)
}

/** 退出登录 */
export function logout() {
  return http.post('/permission/User/logout')
}

/** 获取用户信息 */
export const getUserInfo = () => {
  return http.get<T.UserInfo>('/permission/User/getUserInfo')
}

/** 获取用户路由信息 */
export const getUserRoutes = () => {
  return http.get<MenuItem[]>('/permission/User/getUserRoutes')
}
