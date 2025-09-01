<template>
  <div class="relative">
    <select 
      v-model="selectedLanguage"
      @change="handleLanguageChange"
      class="bg-gray-50 border border-gray-200 text-gray-600 text-sm rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-purple-500 focus:border-transparent cursor-pointer"
    >
      <option value="zh-TW">繁體中文</option>
      <option value="zh-CN">简体中文</option>
      <option value="en">English</option>
    </select>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import { setLanguage } from '@/i18n';

const { locale } = useI18n();
const selectedLanguage = ref(locale.value);

const handleLanguageChange = () => {
  setLanguage(selectedLanguage.value as 'en' | 'zh-TW' | 'zh-CN');
  console.log(`[${new Date().toLocaleTimeString()}] 🌍 語言切換為: ${selectedLanguage.value}`);
};

onMounted(() => {
  selectedLanguage.value = locale.value;
});
</script>

<style scoped>
select {
  appearance: none;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 0.5rem center;
  background-repeat: no-repeat;
  background-size: 1.5em 1.5em;
  padding-right: 2.5rem;
}
</style>