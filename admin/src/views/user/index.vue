<template>
    <a-card title="用户列表" class="gi_page_card">
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
                <a-table-column title="算力点" data-index="points"></a-table-column>
                <a-table-column title="注册来源" align="center">
                    <template #cell="{ record }">
                        <a-tag color="orange" v-if="record.source == 'applet'">小程序</a-tag>
                        <a-tag color="arcoblue" v-else>公众号</a-tag>
                    </template>
                </a-table-column>
                <a-table-column title="注册时间" data-index="create_time" :width="180"></a-table-column>

                <a-table-column title="操作" :width="100" align="center" :fixed="fixed">
                    <template #cell="{ record }">
                        <a-button type="primary" size="mini" @click="onEdit(record)">
                            <template #icon><icon-edit /></template>
                            <span>充值</span>
                        </a-button>
                    </template>
                </a-table-column>
            </template>
        </a-table>

        <RechargeModal ref="RechargeModalRef" @save-success="search"></RechargeModal>
    </a-card>
</template>
  
<script setup lang="ts">
import { useTable } from '@/hooks'
import { getUserList } from '@/apis/user'
import RechargeModal from './RechargeModal.vue'
const RechargeModalRef = useTemplateRef('RechargeModalRef')

 
const {
    loading,
    tableData:list,
    pagination,
    search,
    fixed,
    reset,
    searchParams
} = useTable((page) => getUserList(page), { immediate: true })

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
    }
] 


const onEdit = (item: any) => {
    RechargeModalRef.value?.edit(item.id)
}
</script>
  
<style lang="scss" scoped></style>
  