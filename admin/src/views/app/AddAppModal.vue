<template>
    <a-modal v-model:visible="visible" :title="title" width="90%" :mask-closable="false" unmount-on-close
        :modal-style="{ maxWidth: '55vW', maxHeight: '70vh', overflowY: 'auto' }" @before-ok="save" @close="close">
        <a-form ref="formRef" :model="form" :rules="rules" size="medium" auto-label-width>
            <a-form-item label="所属分类" field="type">
                <a-select placeholder="请选择分类" v-model="form.type">
                    <a-option v-for="item of AppList" :value="item.value" :label="item.label" />
                </a-select>
            </a-form-item>
            <a-form-item label="文章标题" field="title">
                <a-input v-model.trim="form.title" placeholder="请输入文章标题" allow-clear> </a-input>
            </a-form-item>
            <a-form-item label="文章副标题" field="sub_title">
                <a-input v-model.trim="form.sub_title" placeholder="请输入文章副标题" allow-clear> </a-input>
            </a-form-item>
            <a-form-item label="扣除算力" field="points">
                <a-input-number v-model="form.points" placeholder="请输入" :min="0" />
            </a-form-item>
            <a-form-item label="图标" field="image">
                <YUpload v-model="form.image"></YUpload>
            </a-form-item>
            <a-form-item label="排序（小到大）" field="sort">
                <a-input-number v-model.trim="form.sort" placeholder="请输入排序（小到大）" allow-clear />
            </a-form-item>
            <a-form-item label="角色指令" field="role_instruct"  v-if="form.type=='oralCopy'">
                <a-textarea v-model="form.role_instruct" auto-size></a-textarea>
            </a-form-item>
            <a-form-item label="内容指令" field="content_instruct"  v-if="form.type=='oralCopy'">
                <a-space direction="vertical" :fill="true"  style="width: 100%;">
                        <a-textarea v-model="form.content_instruct" auto-size placeholder="请输入内容指令"></a-textarea>
                    <a-space>
                        <a-tag @click="addContent(item)" color="green" v-for="(item, index1) in form.tableData">{{
                            item.field_title }}</a-tag>
                    </a-space>
                </a-space>
            </a-form-item>
            <a-form-item label="表单收集" field="tableData" v-if="form.type=='oralCopy'">
                <a-space direction="vertical">
                    <a-button @click="AddFormModalRef.add()">新增</a-button>
                    <a-table :data="form.tableData" :pagination="false">
                        <template #columns>
                            <a-table-column title="字段" data-index="field"></a-table-column>
                            <a-table-column title="字段标题" data-index="field_title"></a-table-column>

                            <a-table-column title="类型" align="center">
                                <template #cell="{ record }">
                                    <a-tag color="arcoblue">{{ getLabel(record.type, 'Component') }}</a-tag>
                                </template>
                            </a-table-column>
                            <a-table-column title="是否必填" align="requie">
                                <template #cell="{ record }">
                                    <a-tag color="arcoblue">{{ record.requie == 1 ? '是' : '否' }}</a-tag>
                                </template>
                            </a-table-column>

                            <a-table-column title="默认值" data-index="default"></a-table-column>
                            <a-table-column title="占位符" data-index="placeholder"></a-table-column>

                            <a-table-column title="操作" align="center">
                                <template #cell="{ record }">
                                    <a-space>
                                        <a-button type="text" size="mini" @click="AddFormModalRef.edit(record)">
                                            编辑
                                        </a-button>
                                        <a-popconfirm type="warning" content="确定删除该数据吗?" @ok="Del(record.id)">
                                            <a-button type="text" status="danger" size="mini">

                                                删除
                                            </a-button>
                                        </a-popconfirm>
                                    </a-space>
                                </template>
                            </a-table-column>
                        </template>
                    </a-table>
                </a-space>
            </a-form-item>

            <a-form-item label="状态" field="status">
                <a-switch v-model="form.status" type="round" :checked-value="1" :unchecked-value="0" checked-text="正常"
                    unchecked-text="禁用" />
            </a-form-item>
        </a-form>

        <FormModal ref="AddFormModalRef" @save-success="result"></FormModal>
    </a-modal>
</template>

<script setup lang="ts">
import FormModal from './FormModal.vue'
const AddFormModalRef = useTemplateRef('AddFormModalRef')

import { type FormInstance, Message } from '@arco-design/web-vue'
import { getAppDetail, saveAppDetail } from '@/apis/app'
import { useResetReactive } from '@/hooks'
import { AppList, getLabel } from './type'
const emit = defineEmits<{
    (e: 'save-success'): void
}>()
const formRef = useTemplateRef('formRef')
const detail_id = ref<string | number>('')
const isEdit = computed(() => !!detail_id.value)
const title = computed(() => (isEdit.value ? '编辑智能工具' : '新增智能工具'))
const visible = ref(false)

const [form, resetForm] = useResetReactive({
    title: '',
    sub_title: '',
    status: 1,
    sort: 0,
    image: '',
    type: '',
    points: 0,
    tableData: <any>[],
    role_instruct: `【角色】
你是一名精通热点解读与短视频爆款创作的新媒体文案助理，专为抖音、小红书、微博等平台定制具有观点、情绪张力和话题性的内容。你擅长把热点事件转化为吸引流量的口播文案，具备强烈的共情能力与年轻化表达风格。

【技能】

【技能 1：热点提炼与观点输出】
- 快速筛选并解读抖音、微博、小红书、百度等平台的热榜事件。
- 提炼事件背景、核心矛盾和大众情绪，提出独特、鲜明的个人观点。
- 创作能引发共鸣或争议的金句或评论式文案，适合用于微博、小红书等平台引发互动。

【技能 2：短视频口播文案创作】
- 根据热点事件创作适合短视频节奏的开头句（抓人眼球）和结尾句（引发讨论）。
- 善于使用口语化、节奏快、代入感强的表达方式，让用户停留、点赞、评论。
- 针对不同平台调性（如抖音更轻松口语，小红书更精致情绪），灵活调整文案风格。

【输出格式】
每个热点内容包含以下内容：

🔥事件简述（30~50字）
（用简洁话术快速介绍背景）

🎯观点输出（独特角度，简短有力）
（对事件表达立场或反转）

💬金句评论（适合发微博、小红书的那种“带情绪”的话）
（引发共鸣、思考或讨论）

🎬短视频文案
开头句（钩子式抓人）
（第一句话必须抓住注意力，情绪/反转/共鸣/疑问）

结尾句（引发共鸣或点赞讨论）
（引导评论、点赞或分享的句子）

【限制】
- 只聚焦热点话题的短视频口播文案，不涉及其他领域内容。
- 所有内容原创，禁止套模板或抄袭他人文案。
- 每条文案务必具备话题性、代入感、情绪张力。
`,
    content_instruct: ''
})

const rules: FormInstance['rules'] = {
    content_instruct: [{ required: true, message: '请输入角色指令' }],
    role_instruct: [{ required: true, message: '请输入角色指令' }],
    type: [{ required: true, message: '请选择类型' }],
    image: [{ required: true, message: '请上传文章封面' }],
    title: [
        { required: true, message: '请输入文章分类名称' },
    ],
    sub_title: [
        { required: true, message: '请输入文章分类名称' },
    ],
    sort: [
        { required: true, message: '请输入文章分类排序' },
    ],
    status: [{ required: true }]
}
const add = () => {
    detail_id.value = 0
    visible.value = true
}

const edit = async (id: string) => {
    visible.value = true
    detail_id.value = id
    const res = await getAppDetail({ id })
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
        const res = await saveAppDetail(form)
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

const result = (e: any) => {
    const fieldO = form.tableData.some((item) => item.field === e.field)
   
    const res = form.tableData.some((item) => item.id === e.id)

    if (fieldO&&!res) {
        Message.error('字段已存在，请修改后再添加')
        return
    }

    if (res) {
        form.tableData = form.tableData.map(item => {
            if (item.id === e.id) {
                return { ...item, ...e }
            }
            return item
        })
    } else {
        form.tableData.push(e)
    }
}
const Del = (id: string) => {
    form.tableData = form.tableData.filter(item => item.id !== id)
}
const addContent = (e: any) => {
    form.content_instruct += '${' + e.field + '}'
}

defineExpose({ add, edit })
</script>
