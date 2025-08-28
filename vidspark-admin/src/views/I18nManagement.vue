<template>
  <div class="i18n-management">
    <el-card>
      <h2>{{ $t('nav.i18n') }}</h2>
      <p>多語言管理功能開發中...</p>
      <el-button @click="$router.push('/')">返回控制台</el-button>
    </el-card>
  </div>
</template>

<script setup lang="ts">
// 多語言管理功能將在UI/UX設計階段完善
</script>

<style scoped>
.i18n-management {
  padding: 20px;
}
</style>
