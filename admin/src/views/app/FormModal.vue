<template>
    <a-modal v-model:visible="visible" :title="title" width="90%" :mask-closable="false" unmount-on-close
        :modal-style="{ maxWidth: '35vW', maxHeight: '70vh', overflowY: 'auto' }" @before-ok="save" @close="close">
        <a-form ref="formRef" :model="form" :rules="rules" size="medium" auto-label-width>
            <a-form-item label="类型" field="type">
                <a-radio-group type="button" v-model="form.type">
                    <a-radio value="input">输入框</a-radio>
                    <!-- <a-radio value="select">下拉框</a-radio> -->
                    <a-radio value="radio">单选框</a-radio>
                    <a-radio value="checkbox">多选</a-radio>
                    <a-radio value="textarea">文本域</a-radio>
                </a-radio-group>
            </a-form-item>
            <a-form-item label="是否必填" field="require">
                <a-switch v-model="form.require" type="round" :checked-value="1" :unchecked-value="0" checked-text="必填"
                    unchecked-text="非必填" />
            </a-form-item>
            <a-form-item label="字段" field="field">
                <a-input v-model.trim="form.field" placeholder="请输入字段" allow-clear> </a-input>
            </a-form-item>
            <a-form-item label="字段名称" field="field_title">
                <a-input v-model.trim="form.field_title" placeholder="请输入字段名称" allow-clear> </a-input>
            </a-form-item>
            <a-form-item label="占位符" field="placeholder">
                <a-input v-model.trim="form.placeholder" placeholder="请输入提示占位符" allow-clear> </a-input>
            </a-form-item>
            <a-form-item label="默认值" field="default">
                <a-input v-model.trim="form.default" placeholder="请输入默认值" allow-clear> </a-input>
            </a-form-item>
            <a-form-item v-if=" form.type == 'radio' || form.type == 'checkbox'">
                <a-space direction="vertical">
                    <a-space v-for="(item, index) in form.option" :key="index">
                        <span>{{ item.label }}</span>
                        <span>{{ item.value }}</span>
                        <icon-close style="color: red;" @click="form.option.splice(index,1)" />
                    </a-space>
                    <a-space>
                        <a-input placeholder="选项名称" v-model="optionForm.label"></a-input>-
                        <a-input placeholder="选项值" v-model="optionForm.value"></a-input>
                        <a-button type="primary" @click="addOption">添加</a-button>
                    </a-space>
                </a-space>
            </a-form-item>
        </a-form>
    </a-modal>
</template>
  
<script setup lang="ts">

import type { FormInstance } from '@arco-design/web-vue'
import { useResetReactive } from '@/hooks'

const formRef = useTemplateRef('formRef')
const detail_id = ref('')
const isEdit = computed(() => !!detail_id.value)
const title = computed(() => (isEdit.value ? '表单信息收集' : '表单信息收集'))
const visible = ref(false)
const optionForm = ref({
    label: '',
    value: ''
})
const [form, resetForm] = useResetReactive({
    id: 0,
    type: 'input',
    require: 0,
    field: '',
    field_title: '',
    placeholder: '',
    default: '',
    option: <any>[]
})
const emit = defineEmits<{
    (e: 'save-success', form: any): void
}>()
const rules: FormInstance['rules'] = {
    type: [{ required: true, message: '请选择类型' }],
    field: [
        { required: true, message: '请输入字段' },
    ],
    field_title: [
        { required: true, message: '请输入字段名称' },
    ],
}
const add = () => {
    detail_id.value = ''
    form.id = Math.floor(Math.random() * 10000)
    visible.value = true
}

const edit = async (data: any) => {
    visible.value = true
    detail_id.value = data.id
    Object.assign(form, data)
}

const close = () => {
    formRef.value?.resetFields()
    resetForm()
}

const save = async () => {
    try {
        const valid = await formRef.value?.validate()
        if (valid) return false
        emit('save-success', { ...form })
    } catch (error) {
        return false
    }
}

const addOption = () => {
    form.option.push({...optionForm.value})
    optionForm.value.label = ''
    optionForm.value.value = ''
}

defineExpose({ add, edit })
</script>
  