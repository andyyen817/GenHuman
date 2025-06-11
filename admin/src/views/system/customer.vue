<template>
    <div class="gi_full_column gi_margin gi_bg_1">
        <a-card title="基础配置">
            <a-form layout="vertical" style="max-width: 800px;" size="small" :model="form">
                <a-form-item label="小程序客服类型">
                    <a-radio-group v-model="form.customerType">
                        <a-radio :value="1">企业客服</a-radio>
                        <a-radio :value="2">小程序客服</a-radio>
                        <a-radio :value="3">客服二维码</a-radio>
                    </a-radio-group>
                </a-form-item>
                <a-form-item label="企业客服ID">
                    <a-input placeholder="请输入企业客服ID" v-model="form.customerCorpId" />
                </a-form-item>
                <a-form-item label="企业客服链接">
                    <a-input placeholder="请输入企业客服链接" v-model="form.customerUrl" />
                </a-form-item>
                <a-form-item label="客服二维码">
                    <YUpload v-model="form.customerQrcode"></YUpload>
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
import type { CustomerFormInterface } from './type';
import { getConfigData, saveConfig } from '@/apis/system'
import { Message } from '@arco-design/web-vue'
const form = ref<CustomerFormInterface>({
    customerType: 1,
    customerCorpId: '',
    customerUrl: '',
    customerQrcode: ''
});

//获取配置
const getConfig = async () => {
    const data = await getConfigData({ key: 'customer' })
    if (data.data) {
        form.value = { ...data.data }
    }
}

getConfig()
const submit = () => {
    const data = form.value
    saveConfig({ key: 'customer', value: data }).then((res: any) => {
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