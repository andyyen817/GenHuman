<template>
    <div class="gi_full_column gi_margin gi_bg_1">
        <a-tabs default-active-key="1" size="large" style="max-width: 800px;">
            <a-tab-pane key="1" title="基础设置">
                <Setting v-model="baseForm"></Setting>
            </a-tab-pane>
            <a-tab-pane key="2" title="本地存储">
                <Public v-model="PublicForm"></Public>
            </a-tab-pane>
            <a-tab-pane key="3" title="阿里云存储">
                <Oss v-model="ossForm"></Oss>
            </a-tab-pane>
            <a-tab-pane key="4" title="腾讯云存储">
                <Cos v-model="cosForm"></Cos>
            </a-tab-pane>
        </a-tabs>
        <a-form-item>
            <a-button type="primary" @click="submit">保存</a-button>
        </a-form-item>
    </div>
</template>

<script lang="ts" setup>
import { Message } from '@arco-design/web-vue'
import Oss from './components/Oss.vue'
import Cos from './components/Cos.vue'
import Setting from './components/Setting.vue'
import Public from './components/Public.vue'
import type { OssFormInterface, BaseInterface, CosFormInterface, PublicInterface } from '../type';
import { getConfigData, saveConfig } from '@/apis/system'
//基础配置
const baseForm = ref<BaseInterface>({
    adapter: 'public',
    size: 10
})
//阿里云配置
const ossForm = ref<OssFormInterface>({
    AccessId: '',
    AccessSecret: '',
    Bucket: '',
    Endpoint: '',
    url: '',
    PrivateType: 0
})

const cosForm = ref<CosFormInterface>({
    Appid: '',
    SecretId: '',
    SecretKey: '',
    Region: '',
    Bucket: '',
    url: '',
    PrivateType: 0
})

const PublicForm = ref<PublicInterface>({
    url: ''
})
//获取配置
const getConfig = async () => {
    const data = await getConfigData({ key: 'upload' })
    if (data.data.cos) {
        cosForm.value = data.data.cos
    }
    if (data.data.oss) {
        ossForm.value = data.data.oss
    }
    if (data.data.base) {
        baseForm.value = data.data.base
    }
    if (data.data.public) {
        PublicForm.value = data.data.public
    }
}

getConfig()

//保存配置
const submit = () => {
    const data = {
        base: baseForm.value,
        oss: ossForm.value,
        cos: cosForm.value,
        public: PublicForm.value
    }
    saveConfig({ key: 'upload', value: data }).then((res: any) => {
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