import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    vueDevTools(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    },
  },
  // 🛡️ 防衝突配置：構建到 vidspark-v2 避免與舊項目衝突
  build: {
    outDir: '../server/public/vidspark-v2',
    emptyOutDir: true,
    assetsDir: 'assets',
    rollupOptions: {
      output: {
        manualChunks: {
          vendor: ['vue', 'vue-router', 'pinia']
        }
      }
    }
  },
  // 🚀 開發服務器配置 (端口5174符合安全防護要求)
  server: {
    port: 5174,
    host: true, // 允許所有網絡訪問
    open: true, // 自動打開瀏覽器
    cors: true
  },
  // 🎯 基礎路徑配置 (開發環境使用根路徑)
  base: process.env.NODE_ENV === 'production' ? '/vidspark-v2/' : '/'
})
