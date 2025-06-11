
<template>
    <div style="border: 1px solid #ccc;min-height: 300px;">
        <Toolbar style="border-bottom: 1px solid #ccc" :editor="editorRef" :defaultConfig="toolbarConfig" :mode="mode" />
        <Editor style="overflow-y: auto;" v-model="content" :defaultConfig="editorConfig" @onChange="change" :mode="mode"
            @onCreated="handleCreated" />
    </div>
</template>

<script setup lang="ts">
import '@wangeditor/editor/dist/css/style.css'
import { Editor, Toolbar } from '@wangeditor/editor-for-vue'
import { upload } from '@/apis/file'

const props = defineProps({
    modelValue: {
        type: [String, Object],
        default: ''
    }
})

const emit = defineEmits(['update:modelValue'])

const content = ref(props.modelValue)
const editorRef = shallowRef()
const toolbarConfig = {}

watch(() => props.modelValue, (newVal) => {
    content.value = newVal
}, { immediate: true })
const change = (e) => {
    emit('update:modelValue', e.getHtml())
}
// 编辑器配置
const editorConfig = {
    placeholder: '请输入内容...',
    MENU_CONF: {},
    autoFocus: false
}
const mode = 'default'


// 组件创建时
const handleCreated = (editor) => {
    editorRef.value = editor
}
// 组件销毁时，也及时销毁编辑器
onBeforeUnmount(() => {
    console.log(11)
    const editor = editorRef.value
    if (editor == null) return
    editor.destroy()
})


const uploadConfig = {
    async customUpload(file, insertFn) {
        upload({ file: file }).then(res => {
            insertFn(res.data.file_url)
        })
    }
}
editorConfig.MENU_CONF['uploadImage'] = uploadConfig
</script>