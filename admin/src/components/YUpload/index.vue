
<template>
    <a-upload list-type="picture-card" :on-before-remove="removeFile" :file-list="fileList" :on-button-click="onClick"
        :auto-upload="false" image-preview>
    </a-upload>
    <a-modal v-model:visible="visible" title="文件管理" width="60vw" @ok="closeModal">
        <div class="file-box">
            <a-row align="stretch" :gutter="14" class="file-manage">
                <a-col :xs="0" :sm="8" :md="7" :lg="6" :xl="5" :xxl="4" flex="220px" class="h-full overflow-hidden">
                    <FileAside @change="changeType"></FileAside>
                </a-col>
                <a-col :xs="24" :sm="16" :md="17" :lg="18" :xl="19" :xxl="20" flex="1" class="h-full overflow-hidden">
                    <FileMain ref="FileMainRef"></FileMain>
                </a-col>
            </a-row>
        </div>
    </a-modal>
</template>

<script lang="ts" setup>
const visible = ref(false)
import FileAside from './FileAside.vue'
import FileMain from './FileMain/index.vue'



const open = () => {
    visible.value = true
    FileMainRef.value.getListData()
}
const FileMainRef = ref<any>(null)
const changeType = (e: string) => {
    FileMainRef.value.setType(e)
}
const props = defineProps({
    limit: {
        type: Number,
        default: 1
    },
    mode: {
        type: String,
        default: 'select'
    }
})
const fileList = ref<any>([])
const onClick = () => {
    open()
    return new Promise((resolve) => { })
}


const modelValue = defineModel<any>({
    default: () => ([])
})

let initialized = false

watch(modelValue, (newVal) => {
    if (!initialized && newVal) {
        if (typeof newVal === 'string') {
            const urls = newVal.split(',').map((url: string) => url.trim()).filter(Boolean)
            fileList.value = urls.map((url: string) => ({ url }))
        } else if (Array.isArray(newVal)) {
            fileList.value = newVal.map((item: any) => ({
                url: item.url || item,
                id: item.id,
            }))
        }
        initialized = true
    }
}, { immediate: true })

//确认选择文件
const closeModal = () => {
    if (props.mode == 'select') {
        fileList.value = []
        const data = FileMainRef.value.getIdsData()
        if (data.length > 0) {
            data.forEach(e => {
                fileList.value.push({ url: e.url })

            });
            modelValue.value = fileList.value
        }
    }
}

//移除文件
const removeFile = (e) => {
    const index = fileList.value.findIndex((item) => item.url === e.url)
    if (index > -1) {
        fileList.value.splice(index, 1)
    }
}

provide('limit', props.limit)
provide('mode', props.mode)

defineExpose({ open })
defineOptions({ name: 'FileIndex' })
</script>


<style lang="scss" scoped>
.file-manage {
    flex: 1;
    padding: $margin;
    overflow: hidden;
    height: 100%;
    box-sizing: border-box;
}

.file-box {
    height: 65vh;
}

.container {
    width: 150px;
    height: 150px;
    border: 1px solid #000;
}
</style>