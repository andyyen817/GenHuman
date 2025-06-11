<template>
    <a-card>
        <a-form layout="vertical" style="max-width: 800px;" size="small" :model="modelValue">
            <a-form-item label="存储方式">
                <a-select placeholder="选择存储方式" v-model="modelValue.adapter">
                    <a-option value="public">本地存储</a-option>
                    <a-option value="oss">阿里云OSS</a-option>
                    <a-option value="cos">腾讯云COS</a-option>
                </a-select>
            </a-form-item>
            <a-form-item label="单文件最大大小(MB)">
                <a-input-number placeholder="请输入单文件最大大小" v-model="modelValue.size" />
            </a-form-item>
        </a-form>
    </a-card>
</template>

<script setup lang="ts">
import type { BaseInterface } from '../../type';
const modelValue = defineModel<BaseInterface>({
    default: () => ({
        adapter: 'public',
        size: 10
    })
})

</script>