<template>
    <a-card>
        <a-form layout="vertical" style="max-width: 800px;" size="small" :model="modelValue">
            <a-form-item label="访问域名">
                <a-input placeholder="请输入访问域名" v-model="modelValue.url" />
            </a-form-item>
        </a-form>
    </a-card>
</template>

<script setup lang="ts">
import type { PublicInterface } from '../../type';
const modelValue = defineModel<PublicInterface>({
    default: () => ({
        url: '',
    })
})
</script>