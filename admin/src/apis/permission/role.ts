import type * as T from './type'
import http from '@/utils/http'

/** 获取角色数据 */
export function getRoleList(params: Pagination) {
  return http.get<PageRes<T.RoleItem[]>>('/permission/Role/getRoleList', params)
}

/** 获取角色详情 */
export function getRoleDetail(params: { id: string }) {
  return http.get<T.RoleItem>('/permission/Role/getRoleDetail', params)
}

/** 获取角色权限 */
export function getRoleMenuIds(params: { role: string }) {
  return http.get<string[]>('/permission/Role/getRoleMenuIds', params)
}


/**
* 添加角色
* @author:下次一定
* @email:1950781041@qq.com
* @Date:2025-05-21
*/
export function saveRole(params: T.RoleItem) {
  return http.post('/permission/Role/saveRole', params)
}

/**
* 编辑角色权限
* @author:下次一定
* @email:1950781041@qq.com
* @Date:2025-05-21
*/
export function editRoleMenu(params: { id: string, menu_ids: string | Array<number> }) {
  return http.post('/permission/Role/editRoleMenu', params)
}
