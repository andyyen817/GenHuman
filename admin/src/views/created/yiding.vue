<template>
    <div class="gi_full_column gi_margin gi_bg_1">
        <a-card title="创作配置">
            <a-form layout="vertical" style="max-width: 800px;" size="small" :model="form">
                <a-form-item label="开放平台TOKEN">
                    <a-space direction="vertical" style="min-width: 800px;">
                        <a-input placeholder="请输入开放平台TOKEN" v-model="form.token" />
                        <span style="font-size: 12px;color:#4290f7;">壹定开放平台->
                            <a style="color: red;" target="_blank"
                                href="https://api.yidevs.com/?icode=nxNKa7bQ7pj6%2F3rDfdYvjW5lbjFKczVublRnSDgxS1ljSHVXbUE9PQ%3D%3D">点击申请</a></span>
                    </a-space>
                </a-form-item>

                <a-form-item label="语音克隆类型">
                    <div style="display: flex; flex-direction: column;gap:6px;">
                        <a-radio-group v-model="form.voiceChannel">
                            <a-radio :value="9999">全部</a-radio>
                            <a-radio :value="1">免费语音</a-radio>
                            <a-radio :value="2">深度语音</a-radio>
                        </a-radio-group>
                        <div style="color: red;font-size: 12px;">勾选全部后用户可以自选克隆类型</div>
                    </div>
                </a-form-item>
                <a-form-item label="视频克隆类型">
                    <a-radio-group v-model="form.worksChannel">
                        <a-radio :value="1">免费通道</a-radio>
                        <a-radio :value="2">统一通道</a-radio>
                        <a-radio :value="3">极速通道</a-radio>
                    </a-radio-group>
                </a-form-item>
                <a-form-item>
                    <a-button type="primary" @click="submit">保存</a-button>
                </a-form-item>
            </a-form>
        </a-card>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import type { YiDingInterface } from './type';
import { getConfigData, saveConfig } from '@/apis/system'
import { Message } from '@arco-design/web-vue'
const form = ref<YiDingInterface>({
    voiceChannel: 1,
    worksChannel: 1,
    token: ''
});

//获取配置
const getConfig = async () => {
    const data = await getConfigData({ key: 'yiding' })
    if (data.data) {
        form.value = { ...data.data }
    }
}

getConfig()
const submit = () => {
    const data = form.value
    saveConfig({ key: 'yiding', value: data }).then((res: any) => {
        if (res.code == 200) {
            Message.success('保存成功')
        } else {
            Message.error(res.msg)
        }
    }).catch(err => {
        Message.error(err)
    })
}
</script>