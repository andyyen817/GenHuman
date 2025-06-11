<template>
    <a-tag v-if="props.status === 1" color="orangered" size="small">
        <template #icon><icon-minus-circle-fill /></template>
        <template #default>创建中</template>
    </a-tag>
    <a-tag v-if="props.status === 2" color="arcoblue" size="small">
        <template #icon><icon-check-circle-fill /></template>
        <template #default>创建完成</template>
    </a-tag>
    <a-tag v-if="props.status === 3" color="red" size="small">
        <template #icon><icon-minus-circle-fill /></template>
        <template #default>创建失败</template>
    </a-tag>
</template>
  
<script lang="ts" setup>
defineOptions({ name: 'GiCellStatus' })

const props = withDefaults(defineProps<Props>(), {
    status: 1
})

interface Props {
    status: 0 | 1 | 2 | 3
}
</script>
  
<style lang="scss" scoped></style>
  