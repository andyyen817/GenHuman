<template>
    <a-card>
        <a-form layout="vertical" style="max-width: 800px;" size="small" :model="modelValue">
            <a-form-item label="AccessId">
                <a-input placeholder="请输入AccessId" v-model="modelValue.AccessId" />
            </a-form-item>
            <a-form-item label="access_secret">
                <a-input placeholder="请输入access_secret" v-model="modelValue.AccessSecret" />
            </a-form-item>
            <a-form-item label="Bucket">
                <a-input placeholder="请输入Bucket" v-model="modelValue.Bucket" />
            </a-form-item>
            <a-form-item label="Bucket域名">
                <a-input placeholder="请输入Bucket域名" v-model="modelValue.Endpoint" />
            </a-form-item>
            <a-form-item label="云存储访问域名地址">
                <a-input placeholder="请输入云存储访问域名地址" v-model="modelValue.url" />
            </a-form-item>
            <a-form-item label="私有空间">
                <a-switch type="round" v-model="modelValue.PrivateType" checked-value="1" unchecked-value="0"></a-switch>
            </a-form-item>
        </a-form>
    </a-card>
</template>

<script setup lang="ts">
import type { OssFormInterface } from '../../type';
const modelValue = defineModel<OssFormInterface>({
    default: () => ({
        AccessId: '',
        AccessSecret: '',
        Bucket: '',
        Endpoint: '',
        url: '',
        PrivateType: ''
    })
})

</script>