<template>
  <div class="user-management">
    <el-card>
      <h2>{{ $t('nav.users') }}</h2>
      <p>用戶管理功能開發中...</p>
      <el-button @click="$router.push('/')">返回控制台</el-button>
    </el-card>
  </div>
</template>

<script setup lang="ts">
// 用戶管理功能將在UI/UX設計階段完善
</script>

<style scoped>
.user-management {
  padding: 20px;
}
</style>
