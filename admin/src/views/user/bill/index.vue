<template>
    <a-card title="用户账单" class="gi_page_card">
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
                <a-table-column title="变化值" data-index="number"></a-table-column>
                <a-table-column title="变化类型" align="center">
                    <template #cell="{ record }">
                        <a-tag color="red" v-if="record.type == 1">支出</a-tag>
                        <a-tag color="arcoblue" v-else>收入</a-tag>
                    </template>
                </a-table-column>
                <a-table-column title="说明" data-index="remarks"></a-table-column>
                <a-table-column title="变化时间" data-index="create_time"></a-table-column>
            </template>
        </a-table>

    </a-card>
</template>
  
<script setup lang="ts">
import { useTable } from '@/hooks'
import { getBillList } from '@/apis/user/index'

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
        field: 'type',
        props: {
            options: [
                { label: '全部', value: '' },
                { label: '支出', value: 1 },
                { label: '收入', value: 2 },
            ]
        }
    }
]
const {
    loading,
    tableData:list,
    pagination,
    search,
    fixed,
    searchParams,
    reset
} = useTable((page) => getBillList(page), { immediate: true })

 

</script>
  
<style lang="scss" scoped></style>
  