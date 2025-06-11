import type * as T from './type'
import http from '@/utils/http'

export type * from './type'

/** 获取文件列表 */
export function getFileList(params: { fileType: string | number, page: number, limit: number }) {
  return http.get<PageRes<T.FileItem[]>>('/File/getFileList', params)
}

/**
* 上传文件
* @author:下次一定
* @email:1950781041@qq.com
* @Date:2025-05-22
*/
export function upload(params: { file: File }) {
  const formData = new FormData()
  formData.append('file', params.file)
  return http.post('/Upload/upload', formData, {
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  })
}


/** 修改名称 */
export function editName(params: { id: string, name: string }) {
  return http.post('/File/editName', params)
}

/** 删除文件 */
export function delFile(params: { id: string | number }) {
  return http.post('/File/delFile', params)
}

/**批量删除 */
export function delFiles(params: { ids: string[] }) {
  return http.post('/File/delFiles', params)
}