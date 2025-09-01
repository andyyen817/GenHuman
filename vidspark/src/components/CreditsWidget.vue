<template>
  <div class="credits-widget">
    <el-icon><Coin /></el-icon>
    <span class="credits-amount">{{ currentBalance }}</span>
    <el-button size="small" type="success" @click="goToRecharge">
      {{ $t('credits.recharge') }}
    </el-button>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { Coin } from '@element-plus/icons-vue';
import { useCreditsStore } from '@/stores/credits';

const router = useRouter();
const { t } = useI18n();
const creditsStore = useCreditsStore();

const currentBalance = computed(() => creditsStore.currentBalance);

const goToRecharge = () => {
  router.push('/credits');
};

onMounted(() => {
  creditsStore.fetchCreditsBalance();
});
</script>

<style scoped>
.credits-widget {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  background-color: var(--el-color-success-light-9);
  border-radius: var(--el-border-radius-base);
  border: 1px solid var(--el-color-success-light-7);
}

.credits-amount {
  font-weight: bold;
  color: var(--el-color-success-dark-2);
  min-width: 40px;
  text-align: center;
}
</style>
