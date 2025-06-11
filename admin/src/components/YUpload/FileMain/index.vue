<template>
  <div class="file-main">
    <a-row justify="space-between" class="file-main__search">
      <a-space wrap>
        <a-upload :custom-request="uploadFile" :show-file-list="false" />
        <a-input-group>
          <a-input placeholder="请输入关键词..." allow-clear v-model="query.name"> </a-input>
          <a-button type="primary" @click="search">
            <template #icon><icon-search /></template>
            <template #default>搜索</template>
          </a-button>
        </a-input-group>
      </a-space>

      <!-- 右侧区域 -->
      <a-space wrap>
        <a-button v-if="isBatchMode" :disabled="!selectedFileIds.length" type="primary" status="danger"
          @click="handleMulDelete">
          <template #icon><icon-delete /></template>
        </a-button>
        <a-button type="primary" @click="isBatchMode = !isBatchMode">
          <template #icon><icon-select-all /></template>
          <template #default>{{ isBatchMode ? '取消批量' : '批量操作' }}</template>
        </a-button>
        <a-button-group>
          <a-tooltip content="视图" position="bottom">
            <a-button @click="toggleMode">
              <template #icon>
                <icon-apps v-if="mode === 'grid'" />
                <icon-list v-else />
              </template>
            </a-button>
          </a-tooltip>
        </a-button-group>
      </a-space>
    </a-row>

    <!-- 文件列表-宫格模式 -->
    <a-spin class="file-main__list" :loading="loading">
      <FileGrid v-show="fileList.length && mode === 'grid'" :data="fileList" :is-batch-mode="isBatchMode"
        :selected-file-ids="selectedFileIds" @click="handleClickFile" @select="handleSelectFile"
        @right-menu-click="handleRightMenuClick" ref="FileGridRef"></FileGrid>

      <!-- 文件列表-列表模式 -->
      <FileList v-show="fileList.length && mode === 'list'" :data="fileList" :is-batch-mode="isBatchMode"
        :selected-file-ids="selectedFileIds" @click="handleClickFile" @select="handleSelectFile"
        @right-menu-click="handleRightMenuClick"></FileList>

      <a-empty v-show="!fileList.length"></a-empty>
    </a-spin>
    <div style="margin-top: auto;">
      <a-pagination :total="total" size="small" :current="query.page" :page-size="query.limit" show-total
        @change="changePage" @page-size-change="changeLimit" show-page-size />
    </div>
  </div>
</template>

<script setup lang="ts">
import { Message, Modal } from '@arco-design/web-vue'

import { api as viewerApi } from 'v-viewer'
import 'viewerjs/dist/viewer.css'
import {
  openFileRenameModal,
  previewFileAudioModal,
  previewFileVideoModal
} from '../components/index'
import useFileManage from './useFileManage'
import FileGrid from './FileGrid.vue'
import { IMAGE_TYPES } from '@/constant/file'
import { type FileItem, delFile, getFileList, upload, delFiles } from '@/apis/file'

const FileList = defineAsyncComponent(() => import('./FileList.vue'))

const route = useRoute()
const router = useRouter()
const { mode, selectedFileIds, toggleMode, addSelectedFileItem } = useFileManage()

const loading = ref(false)
// 批量操作
const isBatchMode = ref(false)
// 文件列表数据
const fileList = ref<FileItem[]>([])
const query = ref<{ fileType: string | number, page: number, limit: number, name: string }>({
  fileType: 0,
  page: 1,
  limit: 20,
  name: ''
})
const total = ref<any>(0)
query.value.fileType = (route.query.fileType as string) || 0


const FileGridRef = ref()
const modeS = inject('mode')

const getListData = async () => {
  try {
    loading.value = true
    isBatchMode.value = false
    const res = await getFileList(query.value)
    total.value = res.data.total
    fileList.value = res.data.data
    FileGridRef.value.clearIds()
  } catch (error) {
    return error
  } finally {
    loading.value = false
  }
}

const search = () => {
  getListData()
}

const getIdsData = () => {
  return FileGridRef.value.getIdsData();
}

// onMounted(() => {
//   getListData()
// })

const setType = (e: string) => {
  query.value.fileType = e
  query.value.page = 1
  getListData()
}

onBeforeRouteUpdate((to) => {
  query.value.fileType = to.query.fileType as string || '0';
  getListData()
})

// 列表图片集合
const imageList = computed(() => {
  return fileList.value.filter((i) => IMAGE_TYPES.includes(i.ext)).map((a) => a.url ?? '')
})

// 点击文件
const handleClickFile = (item: FileItem) => {
  if (modeS == 'select') {
    return;
  }
  if (IMAGE_TYPES.includes(item.ext)) {
    if (item.url && imageList.value.length) {
      const index = imageList.value.findIndex((i) => i === item.url)
      viewerApi({
        images: imageList.value,
        options: {
          initialViewIndex: index
        }
      })
    }
  }
  if (item.ext === 'mp4') {
    previewFileVideoModal(item)
  }
  if (item.ext === 'mp3') {
    previewFileAudioModal(item)
  }


}

// 勾选文件
const handleSelectFile = (item: FileItem) => {
  addSelectedFileItem(item)
}

// 鼠标右键
const handleRightMenuClick = (mode: string, fileInfo: FileItem) => {
  if (mode === 'delete') {
    Modal.warning({
      title: '提示',
      content: '是否删除该文件？',
      hideCancel: false,
      okButtonProps: { status: 'danger' },
      onBeforeOk: async () => {
        delFile({ id: fileInfo.id }).then(() => {
          Message.success('删除成功')
          getListData()
        }).catch(() => {
          Message.error('删除失败')
        })
      }
    })
  }
  if (mode === 'rename') {
    openFileRenameModal(fileInfo, () => {
      getListData()
    })
  }

  if (mode === 'detail') {
    router.push({ path: '/file/detail' })
  }
}

// 批量删除
const handleMulDelete = () => {
  Modal.warning({
    title: '提示',
    content: '是否删除该文件？',
    hideCancel: false,
    okButtonProps: { status: 'danger' },
    onBeforeOk: async () => {
      delFiles({ ids: selectedFileIds.value }).then(() => {
        Message.success('删除成功')
        getListData()
      }).catch(() => {
        Message.error('删除失败')
      })
    }
  })
}

const changePage = (e) => {
  query.value.page = e
  getListData()
}

const changeLimit = (e) => {
  query.value.limit = e
  query.value.page = 1
  getListData()
}


const uploadFile = (option: any) => {
  const { fileItem } = option;
  upload(fileItem).then(e => {
    getListData()
    Message.success('上传成功')
  });
}
defineExpose({ getListData, setType, getIdsData })
const emit = defineEmits(['selectFile'])
</script>

<style lang="scss" scoped>
.file-main {
  height: 100%;
  background: var(--color-bg-1);
  border-radius: $radius-box;
  display: flex;
  flex-direction: column;
  overflow: hidden;

  &__search {
    border-bottom: 1px dashed var(--color-border-3);
    margin: 0 $padding;
  }

  &__list {
    flex: 1;
    padding: 0 $padding $padding;
    box-sizing: border-box;
    overflow: hidden;
    display: flex;
    flex-direction: column;
  }
}
</style>
