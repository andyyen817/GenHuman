<template>
    <a-modal v-model:visible="visible" :title="title" width="90%" :mask-closable="false"
        :modal-style="{ maxWidth: '520px' }" @before-ok="save" @close="close">
        <a-form ref="formRef" :model="form" :rules="rules" size="medium" auto-label-width>
            <a-form-item label="套餐名称" field="title">
                <a-input v-model.trim="form.title" placeholder="请输入套餐名称" allow-clear > </a-input>
            </a-form-item>
            <a-form-item label="价格" field="price">
                <a-input-number v-model.trim="form.price" placeholder="请输入价格" allow-clear  />
            </a-form-item>
            <a-form-item label="原价（划线价）" field="original_price">
                <a-input-number v-model.trim="form.original_price" placeholder="请输入原价（划线价）" allow-clear  />
            </a-form-item>
            <a-form-item label="点数" field="points">
                <a-input-number v-model.trim="form.points" placeholder="请输入获得点数" allow-clear  />
            </a-form-item>
            <a-form-item label="赠送点数" field="give">
                <a-input-number v-model.trim="form.give" placeholder="请输入赠送点数" allow-clear  />
            </a-form-item>
            <a-form-item label="排序（小到大）" field="sort">
                <a-input-number v-model.trim="form.sort" placeholder="请输入排序（小到大）" allow-clear  />
            </a-form-item>
            <a-form-item label="状态" field="status">
                <a-switch v-model="form.status" type="round" :checked-value="1" :unchecked-value="0" checked-text="正常"
                    unchecked-text="禁用" />
            </a-form-item>
        </a-form>
    </a-modal>
</template>
  
<script setup lang="ts">
import { type FormInstance, Message } from '@arco-design/web-vue'
import { getPlansDetail, savePlansDetail } from '@/apis/marketing'
import { useResetReactive } from '@/hooks'

const emit = defineEmits<{
    (e: 'save-success'): void
}>()

const formRef = useTemplateRef('formRef')
const roleId = ref('')
const isEdit = computed(() => !!roleId.value)
const title = computed(() => (isEdit.value ? '编辑文章分类' : '新增文章分类'))
const visible = ref(false)

const [form, resetForm] = useResetReactive({
    title: '',
    price: 0,
    original_price: 0,
    points: 0,
    give: 0,
    status: 1,
    sort: 0,
})

const rules: FormInstance['rules'] = {
    price: [{ required: true, message: '请输入套餐价格' }],
    original_ptice: [{ required: true, message: '请输入套餐原价' }],
    points: [{ required: true, message: '请输入套餐点数' }],
    give: [{ required: true, message: '请输入套餐赠送点数' }],
    title: [
        { required: true, message: '请输入套餐名称' },
        { min: 3, max: 10, message: '长度在 3 - 10个字符' }
    ],
    sort: [
        { required: true, message: '请输入套餐排序' },
    ],
    status: [{ required: true }]
}

const add = () => {
    roleId.value = ''
    visible.value = true
}

const edit = async (id: string) => {
    visible.value = true
    roleId.value = id
    const res = await getPlansDetail({ id })
    res.data.price = res.data.price * 1
    Object.assign(form, res.data)
}

const close = () => {
    formRef.value?.resetFields()
    resetForm()
}

const save = async () => {
    try {
        const valid = await formRef.value?.validate()
        if (valid) return false
        const res = await savePlansDetail(form)
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

defineExpose({ add, edit })
</script>
  