<template>
    <a-card title="订单列表" class="gi_page_card">
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

                <a-table-column title="类型" align="center">
                    <template #cell="{ record }">
                        <a-tag  color="arcoblue">{{ getLabel(record.type) }}</a-tag>
                    </template>
                </a-table-column>
                <a-table-column title="消耗算力点" data-index="points"></a-table-column>

                <a-table-column title="结果"  align="center" ellipsis tooltip >
                    <template #cell="{ record }">
                        <span v-if="record.type=='oralCopy'">{{ record.result }}</span>
                        <a-image v-if="record.type=='facialFusion'"  width="60" height="60" :src="record.result"></a-image>
                        <a-image v-if="record.type=='photoHD'"  width="60" height="60" :src="record.result"></a-image>
                        <a-image v-if="record.type=='oldPhotoRestoration'"  width="60" height="60" :src="record.result"></a-image>
                        <a-image v-if="record.type=='hairStyle'"  width="60" height="60" :src="record.result"></a-image>
                    </template>
                </a-table-column>
                <a-table-column title="状态"  align="center">
                    <template #cell="{ record }">
                        <GiCellState :status="record.status"></GiCellState>
                    </template>
                </a-table-column>

                <a-table-column title="创建时间" data-index="create_time" ></a-table-column>
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
import { getRecordList, deleteRecord } from '@/apis/app'
import { Message } from '@arco-design/web-vue';
import { previewFileVideoModal } from '@/components/YUpload/components/index'
import {getLabel} from '../type'


const {
    loading,
    tableData: list,
    pagination,
    search,
    fixed,
    searchParams,
    reset
} = useTable((page) => getRecordList(page), { immediate: true })

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
        type: 'select',
        label: '状态',
        field: 'status',
        props: {
            options: [
                { label: '全部', value: '' },
                { label: '待支付', value: 0 },
                { label: '已支付', value: 1 },
            ]
        }
    }
]

const onDel = (id: number) => {
    deleteRecord(id).then((res) => {
        Message.success('删除成功')
        search()
    }).catch((err) => {
        Message.error(err.message)
    })
}

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
  