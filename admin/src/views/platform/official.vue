<template>
    <div class="gi_full_column gi_margin gi_bg_1">
        <a-card title="公众号配置">
            <a-form layout="vertical" style="max-width: 800px;" size="small" :model="form">
                <a-form-item label="公众号名称">
                    <a-input placeholder="请输入公众号名称" v-model="form.name" />
                </a-form-item>
                <a-form-item label="公众号Appid">
                    <a-input placeholder="请输入Appid" v-model="form.appid" />
                </a-form-item>
                <a-form-item label="公众号Secret">
                    <a-input placeholder="请输入Secret" v-model="form.secret" />
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
import type { officialForm } from './index';
import { getConfigData, saveConfig } from '@/apis/system'
import { Message } from '@arco-design/web-vue'
const form = ref<officialForm>({
    name: '',
    appid: '',
    secret: ''
});

//获取配置
const getConfig = async () => {
    const data = await getConfigData({ key: 'official' })
    if (data.data) {
        form.value = { ...data.data }
    }
}

getConfig()
const submit = () => {
    const data = form.value
    saveConfig({ key: 'official', value: data }).then((res: any) => {
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