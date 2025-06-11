<template>
    <a-modal v-model:visible="visible" :title="title" width="90%" :mask-closable="false" unmount-on-close
        :modal-style="{ maxWidth: '55vW', maxHeight: '70vh', overflowY: 'auto' }" @before-ok="save" @close="close">
        <a-form ref="formRef" :model="form" :rules="rules" size="medium" auto-label-width>
            <a-form-item label="所属分类" field="category_id">
                <a-select placeholder="请选择分类" v-model="form.category_id">
                    <a-option v-for="item of categoryList" :value="item.value" :label="item.label" />
                </a-select>
            </a-form-item>

            <a-form-item label="文章封面" field="cover">
                <YUpload v-model="form.cover"></YUpload>
            </a-form-item>
            <a-form-item label="文章标题" field="title">
                <a-input v-model.trim="form.title" placeholder="请输入文章标题" allow-clear > </a-input>
            </a-form-item>
            <a-form-item label="文章简介" field="desc">
                <a-input v-model.trim="form.desc" placeholder="请输入文章简介" allow-clear > </a-input>
            </a-form-item>
            <a-form-item label="排序（小到大）" field="sort">
                <a-input-number v-model.trim="form.sort" placeholder="请输入排序（小到大）" allow-clear  />
            </a-form-item>
            <a-form-item label="状态" field="status">
                <a-switch v-model="form.status" type="round" :checked-value="1" :unchecked-value="0" checked-text="正常"
                    unchecked-text="禁用" />
            </a-form-item>
            <a-form-item label="是否推荐" field="recommend">
                <a-switch v-model="form.recommend" type="round" :checked-value="1" :unchecked-value="0" checked-text="推荐"
                    unchecked-text="不推荐" />
            </a-form-item>
            <a-form-item label="文章内容" field="desc" :wrapper-col-style="{ overflowY: 'auto' }">
                <YEditor v-model="form.content" />
            </a-form-item>
        </a-form>
    </a-modal>
</template>
  
<script setup lang="ts">


import { type FormInstance, Message } from '@arco-design/web-vue'
import { getArticleDetail, saveArticle, getCategoryOptions } from '@/apis/app'
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
    cover: '',
    desc: '',
    content: '',
    title: '',
    status: 1,
    sort: 0,
    recommend: 0,
    category_id: ''
})

const rules: FormInstance['rules'] = {
    category_id: [{ required: true, message: '请选择文章分类' }],
    cover: [{ required: true, message: '请上传文章封面' }],
    title: [
        { required: true, message: '请输入文章分类名称' },
        { min: 3, max: 10, message: '长度在 3 - 10个字符' }
    ],
    desc: [
        { required: true, message: '请输入文章分类名称' },
        { min: 3, max: 10, message: '长度在 3 - 10个字符' }
    ],
    sort: [
        { required: true, message: '请输入文章分类排序' },
    ],
    status: [{ required: true }]
}
const categoryList = ref<any>([])
const gerCate = () => {
    getCategoryOptions().then(res => {
        categoryList.value = res.data
    })
}
gerCate()
const add = () => {
    roleId.value = ''
    visible.value = true
}

const edit = async (id: string) => {
    visible.value = true
    roleId.value = id
    const res = await getArticleDetail({ id })
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
        const res = await saveArticle(form)
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
  