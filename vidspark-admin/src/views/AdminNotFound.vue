<template>
  <div class="admin-not-found">
    <el-result
      icon="warning"
      title="404"
      sub-title="頁面不存在"
    >
      <template #extra>
        <el-button type="primary" @click="$router.push('/')">
          返回首頁
        </el-button>
      </template>
    </el-result>
  </div>
</template>

<script setup lang="ts">
// 404頁面
</script>

<style scoped>
.admin-not-found {
  padding: 50px;
  text-align: center;
}
</style>
