<template>
    <div class="gi_full_column gi_margin gi_bg_1">
        <a-card title="基础配置">
            <a-form layout="vertical" ref="formRef" style="max-width: 800px;" size="small" :model="form" :rules="rules">
                <a-form-item label="商户号" field="mchId">
                    <a-input v-model="form.mchId" placeholder="请输入商户号"></a-input>
                </a-form-item>
                <a-form-item label="商户API密钥" field="paySignKey">
                    <a-input v-model="form.paySignKey" placeholder="请输入商户API密钥"></a-input>
                </a-form-item>
                <a-form-item label="商户证书" field="apiclientCert">
                    <a-textarea style="height: 150px;" v-model="form.apiclientCert" placeholder="请输入商户证书"></a-textarea>
                </a-form-item>
                <a-form-item label="商户私钥" field="apiclientKey">
                    <a-textarea style="height: 150px;" v-model="form.apiclientKey" placeholder="请输入商户私钥"></a-textarea>
                </a-form-item>
                <a-form-item>
                    <a-button type="primary" @click="submit">保存</a-button>
                </a-form-item>
            </a-form>
        </a-card>
    </div>
</template>

<script setup lang="ts">
import { type FormInstance, Message } from '@arco-design/web-vue'
import { ref } from 'vue';
import type { WechatPayV3 } from '../type';
import { getConfigData, saveConfig } from '@/apis/system'
const formRef = useTemplateRef('formRef')
const form = ref<WechatPayV3>({
    mchId: '',
    paySignKey: '',
    apiclientCert: '',
    apiclientKey: ''
});


const rules: FormInstance['rules'] = {
    mchId: [{ required: true, message: '请输入商户号' }],
    paySignKey: [{ required: true, message: '请输入商户秘钥' }],
    apiclientCert: [{ required: true, message: '请输入商户证书' }],
    apiclientKey: [{ required: true, message: '请输入商户私钥' }],
}

//获取配置
const getConfig = async () => {
    const data = await getConfigData({ key: 'payWechat' })
    if (data.data) {
        form.value = { ...data.data }
    }
}
getConfig()
const submit = async () => {
    try {
        const valid = await formRef.value?.validate()
        if (valid) return false
        const data = form.value
        const res = await saveConfig({ key: 'payWechat', value: data })
        if (res) {
            Message.success('保存成功')
            return true
        } else {
            return false
        }
    } catch (error) {
        return false
    }
}
</script>