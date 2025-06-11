<template>
  <div>
    <a-card title="文件类型" :bordered="false" :body-style="{ padding: 0 }">
      <a-menu :default-open-keys="['0']" :selected-keys="[selectedKey]">
        <a-sub-menu key="0">
          <template #icon>
            <icon-apps></icon-apps>
          </template>
          <template #title>文件类型</template>
          <a-menu-item v-for="item in FILE_TYPE_LIST" :key="item.value.toString()" @click="onClickItem(item)">
            <template #icon>
              <GiSvgIcon :size="28" :name="item.menuIcon"></GiSvgIcon>
            </template>
            <span>{{ item.name }}</span>
          </a-menu-item>
        </a-sub-menu>
      </a-menu>
    </a-card>
  </div>
</template>

<script setup lang="ts">
import { FILE_TYPE_LIST, type FileTypeListItem } from '@/constant/file'
import { ref } from 'vue';

const selectedKey = ref('0')

const emit = defineEmits(['change'])
// 点击事件
const onClickItem = (item: FileTypeListItem) => {
  selectedKey.value = item.value
  emit('change', item.value)
}
</script>

<style lang="scss" scoped>
:deep(.arco-card) {
  .arco-card-header {
    border-bottom-style: dashed;
    margin: 0 16px;
    padding-left: 0;
    padding-right: 0;
  }
}

:deep(.arco-progress) {

  .arco-progress-line,
  .arco-progress-line-bar-buffer,
  .arco-progress-line-bar {
    border-radius: 0;
  }
}

.percent {
  margin-top: 10px;
  padding: 14px 12px;
  box-sizing: border-box;
  background-color: var(--color-bg-1);
}
</style>
