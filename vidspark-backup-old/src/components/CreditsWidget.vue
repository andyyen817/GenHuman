<template>
  <div class="flex items-center space-x-3 bg-white rounded-lg px-4 py-2 border border-gray-200">
    <div class="flex items-center space-x-1">
      <i class="fas fa-coins text-yellow-500"></i>
      <span class="text-sm font-medium text-gray-700">{{ currentBalance }}</span>
    </div>
    <button 
      @click="goToRecharge"
      class="bg-purple-600 hover:bg-purple-700 text-white text-xs px-3 py-1 rounded-md transition-colors"
    >
      {{ $t('credits.recharge') }}
    </button>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
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
/* 已在template中使用TailwindCSS類，無需額外樣式 */
</style>