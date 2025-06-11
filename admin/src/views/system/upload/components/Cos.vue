<template>
    <a-card>
        <a-form layout="vertical" style="max-width: 800px;" size="small" :model="modelValue">
            <a-form-item label="Appid">
                <a-input placeholder="请输入Appid" v-model="modelValue.Appid" />
            </a-form-item>
            <a-form-item label="SecretId">
                <a-input placeholder="请输入SecretId" v-model="modelValue.SecretId" />
            </a-form-item>
            <a-form-item label="SecretKey">
                <a-input placeholder="请输入SecretKey" v-model="modelValue.SecretKey" />
            </a-form-item>
            <a-form-item label="Region">
                <a-input placeholder="请输入Region" v-model="modelValue.Region" />
            </a-form-item>
            <a-form-item label="Bucket">
                <a-input placeholder="请输入Bucket" v-model="modelValue.Bucket" />
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
import type { CosFormInterface } from '../../type';
const modelValue = defineModel<CosFormInterface>({
    default: () => ({
        Appid: '',
        SecretId: '',
        SecretKey: '',
        Region: '',
        Bucket: '',
        url: '',
        PrivateType: 0
    })
})
</script>