import { defineStore } from 'pinia'
import { getSiteConfig } from '@/apis/system'
import { ref, computed } from 'vue'

export const useSiteStore = defineStore('site', () => {
    // 顶层定义 ref，保持扁平结构
    const webName = ref('')
    const webLogo = ref('')

    /** 站点名称 */
    const name = computed(() => webName.value)

    /** 站点 Logo */
    const logo = computed(() => webLogo.value)

    /** 获取站点信息 */
    const getInfo = async (): Promise<void> => {
        try {
            const res = await getSiteConfig()
            const { webName: nameFromApi, webLogo: logoFromApi } = res.data

            webName.value = nameFromApi
            webLogo.value = logoFromApi
        } catch (error) {
            console.error('获取站点信息失败:', error)
            throw error
        }
    }

    return {
        webName,
        webLogo,
        name,
        logo,
        getInfo
    }
}, {
    persist: {
        paths: ['webName', 'webLogo'], // 扁平结构持久化
        storage: localStorage
    }
})
