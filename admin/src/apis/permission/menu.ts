import type * as T from './type'
import http from '@/utils/http'

/** 获取菜单数据 */
export function getMenuList() {
  return http.get<T.MenuItem[]>('/permission/Menu/getMenuList')
}

/** 获取菜单详情 */
export function getMenuDetail(params: { id: string }) {
  return http.get<T.MenuItem>('/permission/Menu/getMenuDetail', params)
}

/** 获取角色分配权限菜单树 */
export function getMenuOptions() {
  return http.get<T.MenuOptionsItem[]>('/permission/Menu/getMenuList')
}

/**
* 新增菜单
* @author:下次一定
* @email:1950781041@qq.com
* @Date:2025-05-21
*/
export function saveMenuDetail(params) {
  return http.post<T.MenuItem>('/permission/Menu/saveMenu', params)
}