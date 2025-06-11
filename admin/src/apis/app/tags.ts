import http from '@/utils/http'

export function getTags(params: { key: string }) {
    return http.get<any>('/Tags/getTags', params)
}

export function saveTags(params: { name: string, id?: string, key: string, content: string }) {
    return http.post<any>('/Tags/saveTags', params)
}