<template>
    <div class="gi_full_column gi_margin gi_bg_1">
        <a-card>
            <a-form layout="vertical" style="max-width: 800px;" size="small" :model="form">
                <a-form-item label="标题">
                    <a-input placeholder="请输入标题" v-model="form.title" />
                </a-form-item>
                <a-form-item label="内容" field="desc" :wrapper-col-style="{ overflowY: 'auto' }">
                    <YEditor v-model="form.content"></YEditor>
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
import { getTags, saveTags } from '@/apis/app'
import { Message } from '@arco-design/web-vue'
import { useRoute } from 'vue-router'

const getLastSegment = (path) => {
    if (!path) return '';
    const segments = path.split('/');
    return segments[segments.length - 1] || '';
}

const route = useRoute()
const key = getLastSegment(route.path)
const form = ref<any>({
    title: '',
    content: ''
});


//获取配置
const getConfig = async () => {
    const data = await getTags({ key: key })
    if (data.data) {
        form.value = { ...data.data }
    }
}
getConfig()
const submit = () => {
    const data = form.value
    saveTags({ key: key, ...data }).then((res: any) => {
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