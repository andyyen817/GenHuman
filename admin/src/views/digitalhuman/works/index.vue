<template>
    <a-card title="数字分身" class="gi_page_card">
        <GiForm v-model="searchParams" search :columns="searchColumns"
            :grid-item-props="{ span: { xs: 24, sm: 12, md: 8, lg: 8, xl: 6, xxl: 6 } }" @search="search" @reset="reset">
        </GiForm>
        <a-table class="gi_table" row-key="id" :data="list" :bordered="{ cell: true }" :loading="loading"
            :scroll="{ x: '100%', y: '100%', minWidth: 1200 }" :pagination="pagination">
            <template #columns>
                <a-table-column title="序号" :width="64">
                    <template #cell="cell">{{ cell.rowIndex + 1 }}</template>
                </a-table-column>
                <a-table-column title="用户信息" data-index="nickname">
                    <template #cell="{ record }">
                        <GiCellAvatar :avatar="record.avatar" :name="record.nickname"></GiCellAvatar>
                    </template>
                </a-table-column>
                <a-table-column title="名称" data-index="title"></a-table-column>
                <a-table-column title="封面" :width="150">
                    <template #cell="{ record }">
                        <a-image :src="record.cover" width="60" height="60" />
                    </template>
                </a-table-column>
                <a-table-column title="视频地址" :width="150">
                    <template #cell="{ record }">
                        <div v-if="record.status == 2">
                            <a-button type="text" @click="play(record)">点击播放</a-button>
                            <a-button type="text" status="success" @click="download(record)">点击下载</a-button>
                        </div>
                        <div v-if="record.status == 1">
                            <a-button type="text" status="warning">创建中</a-button>
                        </div>
                        <div v-if="record.status == 3">
                            <a-button type="text" status="danger">失败</a-button>
                        </div>
                    </template>
                </a-table-column>
                <a-table-column title="任务ID" data-index="task_id" :width="150"></a-table-column>
                <a-table-column title="任务ID" data-index="task_id" :width="150"></a-table-column>


                <a-table-column title="状态" :width="150" align="center">
                    <template #cell="{ record }">
                        <GiCellState :status="record.status"></GiCellState>
                    </template>
                </a-table-column>
                <a-table-column title="创建时间" data-index="create_time" :width="180"></a-table-column>


                <a-table-column title="操作" :width="100" align="center" :fixed="fixed">
                    <template #cell="{ record }">
                        <a-popconfirm type="warning" content="确定删除该数据吗?" @ok="onDel(record.id)">
                            <a-button type="primary" status="danger" size="mini" :disabled="record.disabled">
                                <template #icon><icon-delete /></template>
                                <span>删除</span>
                            </a-button>
                        </a-popconfirm>
                    </template>
                </a-table-column>
            </template>
        </a-table>

    </a-card>
</template>
  
<script setup lang="ts">
import { useTable } from '@/hooks'
import { getWorksList, deleteWorks } from '@/apis/digitalhuman'
import { previewFileVideoModal } from '@/components/YUpload/components/index'
import { Message } from '@arco-design/web-vue';
const {
    loading,
    tableData:list,
    pagination,
    search,
    fixed,
    reset,
    searchParams
} = useTable((page) => getWorksList(page), { immediate: true })

const onDel = (id: number) => {
    deleteWorks(id).then((res) => {
        Message.success('删除成功')
        search()
    }).catch((err) => {
        Message.error(err.message)
    })
}
const searchColumns = [
    {
        type: 'input',
        label: '昵称',
        field: 'nickname'
    },
    {
        type: 'input',
        label: '用户ID',
        field: 'userId'
    },
    {
        type: 'input',
        label: '作品名称',
        field: 'title'
    },
    {
        type: 'select',
        label: '状态',
        field: 'status',
        props: {
            options: [
                { label: '全部', value: '' },
                { label: '创建中', value: 1 },
                { label: '已完成', value: 2 },
                { label: '失败', value: 3 }
            ]
        }
    }
]

const play = (e) => {
    previewFileVideoModal({
        name: e.title,
        id: e.id,
        url: e.video_url,
        filePath: e.video_url
    })
}
const download = (e) => {
    //跳转地址
    window.open(e.video_url, '_blank')
}
</script>
  
<style lang="scss" scoped></style>
  