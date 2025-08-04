<template>
    <div class="gi_full_column gi_margin gi_bg_1">
        <a-card title="创作配置">
            <a-form layout="vertical" style="max-width: 800px;" size="small" :model="form">
                <a-form-item label="克隆分身扣点(每次)">
                    <a-input-number placeholder="请输入克隆分身扣点" v-model="form.scenePoint" />
                </a-form-item>
                <a-form-item label="克隆音色扣点(免费)">
                    <a-input-number placeholder="请输入克隆音色扣点(免费)" v-model="form.voicePoint" />
                </a-form-item>
                <a-form-item label="克隆音色扣点(深度)">
                    <a-input-number placeholder="请输入克隆音色扣点(深度)" v-model="form.voiceDeepPoint" />
                </a-form-item>
                <!-- <a-form-item label="克隆音色扣点(专业)">
                    <a-input-number placeholder="克隆音色扣点(专业)" v-model="form.voiceProfessionalPoint" />
                </a-form-item> -->
                <a-form-item label="创建视频扣点(每秒)">
                    <a-input-number placeholder="请输入创建视频扣点" v-model="form.worksPoint" />
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
import type { BaseInterface } from './type';
import { getConfigData, saveConfig } from '@/apis/system'
import { Message } from '@arco-design/web-vue'
const form = ref<BaseInterface>({
    scenePoint: 0,
    voicePoint: 0,
    worksPoint: 0,
    // voiceProfessionalPoint: 0,
    voiceDeepPoint: 0,
    aiCwPoint: 0
});

//获取配置
const getConfig = async () => {
    const data = await getConfigData({ key: 'created' })
    if (data.data) {
        form.value = { ...data.data }
    }
}

getConfig()
const submit = () => {
    const data = form.value
    saveConfig({ key: 'created', value: data }).then((res: any) => {
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