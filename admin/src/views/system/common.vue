<template>
    <div class="gi_full_column gi_margin gi_bg_1">
        <a-card title="基础配置">
            <a-form layout="vertical" style="max-width: 800px;" size="small" :model="form">
                <a-form-item label="用户默认头像">
                    <YUpload v-model="form.avatar"></YUpload>
                </a-form-item>
                <a-form-item label="首页图片">
                    <YUpload v-model="form.headerImg"></YUpload>
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
import type { CommonFormInterface } from './type';
import { getConfigData, saveConfig } from '@/apis/system'
import { Message } from '@arco-design/web-vue'
const form = ref<CommonFormInterface>({
    avatar: '',
    headerImg: ''
});

//获取配置
const getConfig = async () => {
    const data = await getConfigData({ key: 'common' })
    if (data.data) {
        form.value = { ...data.data }
    }
}

getConfig()
const submit = () => {
    const data = form.value
    saveConfig({ key: 'common', value: data }).then((res: any) => {
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