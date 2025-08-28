<template>
  <div class="not-found">
    <div class="error-content">
      <h1>404</h1>
      <h2>Page Not Found</h2>
      <p>The page you are looking for doesn't exist.</p>
      <el-button type="primary" @click="$router.push('/')">
        Go Home
      </el-button>
    </div>
  </div>
</template>

<script setup lang="ts">
// 404 page
</script>

<style scoped>
.not-found {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  text-align: center;
}

.error-content h1 {
  font-size: 120px;
  color: #5D5FEF;
  margin: 0;
}

.error-content h2 {
  margin: 16px 0;
  color: #333;
}

.error-content p {
  color: #666;
  margin-bottom: 32px;
}
</style>
