<template>
  <div class="login-view">
    <el-card class="login-card">
      <template #header>
        <div class="card-header">
          <h2>{{ $t('user.login') }}</h2>
        </div>
      </template>
      <el-form :model="loginForm" :rules="rules" ref="loginFormRef" label-width="80px">
        <el-form-item :label="$t('user.username')" prop="username">
          <el-input v-model="loginForm.username" :placeholder="$t('user.username')"></el-input>
        </el-form-item>
        <el-form-item :label="$t('user.password')" prop="password">
          <el-input v-model="loginForm.password" type="password" :placeholder="$t('user.password')"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="handleLogin" :loading="isLoading">
            {{ $t('user.login') }}
          </el-button>
          <el-button @click="goToRegister">{{ $t('user.register') }}</el-button>
        </el-form-item>
      </el-form>
    </el-card>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { ElMessage } from 'element-plus';
import { useUserStore } from '@/stores/user';
import { useUiStore } from '@/stores/ui';

const router = useRouter();
const { t } = useI18n();
const userStore = useUserStore();
const uiStore = useUiStore();

const loginFormRef = ref();
const isLoading = ref(false);

const loginForm = reactive({
  username: '',
  password: ''
});

const rules = {
  username: [
    { required: true, message: 'Please input username', trigger: 'blur' }
  ],
  password: [
    { required: true, message: 'Please input password', trigger: 'blur' }
  ]
};

const handleLogin = async () => {
  try {
    const valid = await loginFormRef.value.validate();
    if (valid) {
      isLoading.value = true;
      const success = await userStore.login(loginForm.username, loginForm.password);
      
      if (success) {
        ElMessage.success(t('common.success'));
        router.push('/dashboard');
      } else {
        ElMessage.error('Login failed');
      }
    }
  } catch (error) {
    console.error('Login error:', error);
    ElMessage.error('Login error');
  } finally {
    isLoading.value = false;
  }
};

const goToRegister = () => {
  // TODO: Implement registration
  ElMessage.info('Registration not implemented yet');
};
</script>

<style scoped>
.login-view {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 80vh;
  background-color: var(--el-bg-color-page);
}

.login-card {
  width: 400px;
}

.card-header {
  text-align: center;
}
</style>
