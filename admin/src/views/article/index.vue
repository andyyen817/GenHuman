<template>
    <a-card title="文章管理" class="gi_page_card">
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
                <a-table-column title="文章标题" data-index="title"></a-table-column>
                <a-table-column title="所属分类" data-index="category.title"></a-table-column>
                <a-table-column title="文章简介" data-index="desc"></a-table-column>
                <a-table-column title="封面" :width="150">
                    <template #cell="{ record }">
                        <a-image :src="record.cover" width="60" height="60" />
                    </template>
                </a-table-column>
                <a-table-column title="排序" data-index="sort"></a-table-column>
                <a-table-column title="状态" align="center">
                    <template #cell="{ record }">
                        <GiCellStatus :status="record.status"></GiCellStatus>
                    </template>
                </a-table-column>
                <a-table-column title="创建时间" data-index="create_time"></a-table-column>
                <a-table-column title="操作" align="center" :fixed="fixed">
                    <template #cell="{ record }">
                        <a-space>
                            <a-button type="primary" size="mini" :disabled="record.disabled" @click="onEdit(record)">
                                <template #icon><icon-edit /></template>
                                <span>编辑</span>
                            </a-button>
                            <a-popconfirm type="warning" content="确定删除该数据吗?" @ok="onDel(record.id)">
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

        <AddArticleModal ref="AddArticleModalRef" @save-success="search"></AddArticleModal>
    </a-card>
</template>
  
<script setup lang="ts">
import { Message } from '@arco-design/web-vue'
import AddArticleModal from './AddArticleModal.vue'
import { useTable } from '@/hooks'
import { getArticleList, deleteArticle } from '@/apis/app'


const AddArticleModalRef = useTemplateRef('AddArticleModalRef')

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
} = useTable((page) => getArticleList(page), { immediate: true })

const reset = () => {
    form.title = ''
    form.status = ''
    search()
}

const onAdd = () => {
    AddArticleModalRef.value?.add()
}

const onEdit = (item: any) => {
    AddArticleModalRef.value?.edit(item.id)
}

const onDel = (id: number) => {
    deleteArticle({ id: id }).then((res) => {
        Message.success('删除成功')
        search()
    }).catch((err) => {
        Message.error(err.message)
    })
}
</script>
  
<style lang="scss" scoped></style>
  