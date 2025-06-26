<template>
    <a-card title="套餐管理" class="gi_page_card">
        <a-row>
            <a-space wrap>
                <a-button type="primary" @click="onAdd">
                    <template #icon><icon-plus /></template>
                    <span>新增</span>
                </a-button>
            </a-space>
        </a-row>

        <a-table class="gi_table" row-key="id" :data="list" :bordered="{ cell: true }" :loading="loading"
            :scroll="{ x: '100%', y: '100%', minWidth: 1200 }" :pagination="pagination">
            <template #columns>
                <a-table-column title="序号" :width="64">
                    <template #cell="cell">{{ cell.rowIndex + 1 }}</template>
                </a-table-column>
                <a-table-column title="套餐名称" data-index="title"></a-table-column>
                <a-table-column title="套餐售价" data-index="price" :width="150"></a-table-column>
                <a-table-column title="原价" data-index="original_price" :width="150"></a-table-column>
                <a-table-column title="套餐点数" data-index="points" :ellipsis="true" :tooltip="true"></a-table-column>
                <a-table-column title="赠送点数" data-index="give" :ellipsis="true" :tooltip="true"></a-table-column>
                <a-table-column title="状态" :width="100" align="center">
                    <template #cell="{ record }">
                        <GiCellStatus :status="record.status"></GiCellStatus>
                    </template>
                </a-table-column>
                <a-table-column title="排序" data-index="sort"></a-table-column>
                <a-table-column title="创建时间" data-index="create_time" :width="180"></a-table-column>
                <a-table-column title="操作" :width="280" align="center" :fixed="fixed">
                    <template #cell="{ record }">
                        <a-space>
                            <a-button type="primary" size="mini" :disabled="record.disabled" @click="onEdit(record)">
                                <template #icon><icon-edit /></template>
                                <span>编辑</span>
                            </a-button>
                            <a-popconfirm type="warning" content="确定删除该套餐吗?" @ok="onDel(record.id)">
                                <a-button type="primary" status="danger" size="mini" :disabled="record.disabled">
                                    <template #icon><icon-delete /></template>
                                    <span>删除</span>
                                </a-button>
                            </a-popconfirm>
                        </a-space>
                    </template>
                </a-table-column>
            </template>
        </a-table>

        <AddPlansModal ref="AddPlansModalRef" @save-success="search"></AddPlansModal>
    </a-card>
</template>
  
<script setup lang="ts">
import { Message } from '@arco-design/web-vue'
import AddPlansModal from './AddPlansModal.vue'
import { useTable } from '@/hooks'
import { getPlansList, deletePlans } from '@/apis/marketing'


const AddPlansModalRef = useTemplateRef('AddPlansModalRef')

const form = reactive({
    title: '',
    status: ''
})

const {
    loading,
    tableData:list,
    pagination,
    search,
    fixed
} = useTable((page) => getPlansList(page), { immediate: true })

const reset = () => {
    form.title = ''
    form.status = ''
    search()
}

const onAdd = () => {
    AddPlansModalRef.value?.add()
}

const onEdit = (item: any) => {
    AddPlansModalRef.value?.edit(item.id)
}

const onDel = (id: number) => {
    deletePlans({ id: id }).then((res) => {
        Message.success('删除成功')
        search()
    }).catch((err) => {
        Message.error(err.message)
    })
}
</script>
  
<style lang="scss" scoped></style>
  