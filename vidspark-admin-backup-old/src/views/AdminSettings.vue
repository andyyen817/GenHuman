<template>
  <div class="admin-settings">
    <el-card>
      <h2>{{ $t('nav.settings') }}</h2>
      <p>系統設置功能開發中...</p>
      <el-button @click="$router.push('/')">返回控制台</el-button>
    </el-card>
  </div>
</template>

<script setup lang="ts">
// 系統設置功能將在UI/UX設計階段完善
</script>

<style scoped>
.admin-settings {
  padding: 20px;
}
</style>
