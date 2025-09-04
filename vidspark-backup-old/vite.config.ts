import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';

// https://vitejs.dev/config/
export default defineConfig({
  base: '/vidspark/',
  plugins: [vue()],
  resolve: {
    alias: {
      '@': resolve(__dirname, 'src'),
    },
  },
  build: {
    outDir: '../server/public/vidspark', // 構建到後端public目錄
    emptyOutDir: true,
    rollupOptions: {
      output: {
        entryFileNames: `assets/[name].js`,
        chunkFileNames: `assets/[name].js`,
        assetFileNames: `assets/[name].[ext]`,
      },
    },
  },
  server: {
    port: 3000,
    proxy: {
      '/vidspark-api-proxy': {
        target: 'http://localhost:8787', // 後端Webman服務地址
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/vidspark-api-proxy/, '/vidspark-api-proxy'),
      },
      '/vidspark-simple-upload': {
        target: 'http://localhost:8787',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/vidspark-simple-upload/, '/vidspark-simple-upload'),
      },
      '/vidspark/files': {
        target: 'http://localhost:8787',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/vidspark\/files/, '/vidspark/files'),
      },
    },
  },
});
