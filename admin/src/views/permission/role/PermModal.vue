<template>
  <a-modal v-model:visible="visible" :title="title" :fullscreen="isMobile()" :mask-closable="true" @ok="save">
    <a-tree ref="treeRef" v-model:checked-keys="menuIds" size="mini" checkable :check-strictly="true" :data="treeData"
      :field-names="{ key: 'id' }"></a-tree>
  </a-modal>
</template>

<script lang="ts" setup>
import { mapTree } from 'xe-utils'
import { type MenuOptionsItem, getMenuOptions, editRoleMenu } from '@/apis/permission'
import { isMobile } from '@/utils'
import { Message } from '@arco-design/web-vue'

const treeRef = useTemplateRef('treeRef')
const treeData = ref<MenuOptionsItem[]>([])
const getTreeData = async () => {
  const res = await getMenuOptions()
  const data = mapTree(JSON.parse(JSON.stringify(res.data)), (i) => ({
    id: i.id,
    title: i.title,
    children: i.children
  }))
  treeData.value = data
  nextTick(() => {
    treeRef.value?.expandAll()
  })
}
getTreeData()

const title = computed(() => `分配权限`)
const visible = ref(false)
const menuIds = ref<string[]>([])
const role_id = ref<any>()
const open = (data: { code: string, title: string, menu_ids: string, id: number | string }) => {
  menuIds.value = []
  visible.value = true
  role_id.value = data.id
  if (data.menu_ids) {
    data.menu_ids.split(',').forEach(item => {
      menuIds.value.push(parseInt(item))
    })
  }
}

const save = () => {
  const ids = menuIds.value ? menuIds.value.join(',') : []
  editRoleMenu({ id: role_id.value, menu_ids: ids })
    .then(() => {
      Message.success('保存成功')
      visible.value = false
    })
    .catch(() => {
      visible.value = true
    })
}


defineExpose({ open })
</script>

<style lang="scss" scoped></style>
