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
                <a-table-column title="购买套餐" data-index="title"></a-table-column>
                <a-table-column title="订单编号" data-index="order_no"></a-table-column>
                <a-table-column title="微信侧订单号" data-index="wechat_order_no" :width="150"></a-table-column>
                <a-table-column title="订单金额" data-index="price" :width="150"></a-table-column>
                <a-table-column title="订单算力点" data-index="points" :width="150"></a-table-column>
                <a-table-column title="状态" :width="150" align="center">
                    <template #cell="{ record }">
                        <a-tag color="red" v-if="record.status==0" >未支付</a-tag>
                        <a-tag color="arcoblue" v-if="record.status==1">已支付</a-tag>
                    </template>
                </a-table-column>
                <a-table-column title="订单来源" align="center">
                    <template #cell="{ record }">
                        <a-tag color="orange" v-if="record.source == 'applet'">小程序</a-tag>
                        <a-tag color="arcoblue" v-else>公众号</a-tag>
                    </template>
                </a-table-column>
                <a-table-column title="支付时间" data-index="pay_time" ></a-table-column>
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
import { getOrderList, deleteOrder } from '@/apis/order/index.ts'
import { Message } from '@arco-design/web-vue';
import { previewFileVideoModal } from '@/components/YUpload/components/index'

const {
    loading,
    tableData:list,
    pagination,
    search,
    fixed,
    searchParams,
    reset
} = useTable((page) => getOrderList(page), { immediate: true })

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
    deleteOrder(id).then((res) => {
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
  