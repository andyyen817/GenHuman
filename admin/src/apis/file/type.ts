// 文件
export interface FileItem {
  id: string
  type?: string
  name?: string
  ext?: string
  url: string
  filePath?: string
  size?: number
  [propName: string]: any // 一个 interface 中任意属性只能有一个
}
