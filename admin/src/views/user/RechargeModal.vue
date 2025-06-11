<template>
    <a-modal v-model:visible="visible" :title="title" width="90%" :mask-closable="false"
        :modal-style="{ maxWidth: '520px' }" @before-ok="save" @close="close">
        <a-form ref="formRef" :model="form" size="medium" auto-label-width>

            <a-form-item label="变化类型" field="title">
                <a-radio-group v-model="form.type">
                    <a-radio :value="1">扣除</a-radio>
                    <a-radio :value="2">增加</a-radio>
                </a-radio-group>
            </a-form-item>

            <a-form-item label="充值类型" v-if="form.type == 2">
                <a-radio-group v-model="form.recharge_type">
                    <a-radio :value="1">普通充值</a-radio>
                    <a-radio :value="2">套餐充值</a-radio>
                </a-radio-group>
            </a-form-item>
            <a-form-item label="充值套餐" v-if="form.recharge_type == 2 && form.type == 2">
                <a-select placeholder="选择套餐" v-model="form.plans_id">
                    <a-option v-for="item of planList" :value="item.value" :label="item.label" />
                </a-select>
            </a-form-item>
            <a-form-item label="变化数量" v-if="form.type == 1 || form.recharge_type == 1">
                <a-input-number v-model.trim="form.points" placeholder="请输入变化数量" allow-clear  />
            </a-form-item>
        </a-form>
    </a-modal>
</template>
  
<script setup lang="ts">
import { type FormInstance, Message } from '@arco-design/web-vue'
import { getOptions } from '@/apis/marketing'
import { rechargeUser } from '@/apis/user'
import { useResetReactive } from '@/hooks'

const emit = defineEmits<{
    (e: 'save-success'): void
}>()

const planList = ref<any>([])
const getPlans = () => {
    getOptions().then(res => {
        planList.value = res.data
    })
}
getPlans()

const formRef = useTemplateRef('formRef')
const userId = ref(0)
const title = '用户算力点充值'
const visible = ref(false)

const [form, resetForm] = useResetReactive({
    recharge_type: 1,
    type: 2,
    plans_id: '',
    points: '',
    id: 0
})


const edit = async (id: string) => {
    visible.value = true
    userId.value = id
}

const close = () => {
    formRef.value?.resetFields()
    resetForm()
}

const save = async () => {
    try {
        const valid = await formRef.value?.validate()
        if (valid) return false
        form.id = userId.value
        const res = await rechargeUser(form)
        if (res) {
            Message.success('保存成功')
            emit('save-success')
            return true
        } else {
            return false
        }
    } catch (error) {
        return false
    }
}

defineExpose({ edit })
</script>
  