import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import { createPinia } from 'pinia';
import { i18n } from './i18n';
import 'element-plus/dist/index.css'; // Element Plus 樣式
import './styles/global.scss'; // 全局樣式

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);
app.use(i18n);
app.mount('#app');

console.log(`[${new Date().toLocaleTimeString()}] Vidspark Frontend App Mounted!`);