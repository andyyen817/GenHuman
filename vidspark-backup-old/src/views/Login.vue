<template>
  <div class="login-page">
    <div class="login-container">
      <div class="login-header">
        <h1>{{ $t('app.name') }}</h1>
        <p>{{ $t('app.tagline') }}</p>
      </div>
      
      <el-form :model="loginForm" :rules="rules" ref="formRef" class="login-form">
        <el-form-item prop="email">
          <el-input 
            v-model="loginForm.email"
            :placeholder="$t('auth.email')"
            size="large"
            prefix-icon="Message"
          />
        </el-form-item>
        
        <el-form-item prop="password">
          <el-input 
            v-model="loginForm.password"
            type="password"
            :placeholder="$t('auth.password')"
            size="large"
            prefix-icon="Lock"
            show-password
            @keyup.enter="handleLogin"
          />
        </el-form-item>
        
        <el-form-item>
          <el-button 
            type="primary" 
            size="large" 
            style="width: 100%"
            :loading="loading"
            @click="handleLogin"
          >
            {{ $t('auth.login') }}
          </el-button>
        </el-form-item>
      </el-form>
      
      <div class="login-footer">
        <p>
          {{ $t('auth.noAccount') }}
          <router-link to="/register">{{ $t('auth.register') }}</router-link>
        </p>
      </div>
      
      <!-- 語言切換器 -->
      <div class="language-switcher">
        <LanguageSwitcher />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { ElMessage } from 'element-plus'
import type { FormInstance, FormRules } from 'element-plus'
import { login } from '@/api/genhuman'
import LanguageSwitcher from '@/components/LanguageSwitcher.vue'

const { t } = useI18n()
const router = useRouter()

const formRef = ref<FormInstance>()
const loading = ref(false)

const loginForm = reactive({
  email: '',
  password: ''
})

const rules: FormRules = {
  email: [
    { required: true, message: t('auth.emailRequired'), trigger: 'blur' },
    { type: 'email', message: t('auth.invalidEmail'), trigger: 'blur' }
  ],
  password: [
    { required: true, message: t('auth.passwordRequired'), trigger: 'blur' },
    { min: 6, message: 'Password must be at least 6 characters', trigger: 'blur' }
  ]
}

async function handleLogin() {
  if (!formRef.value) return
  
  const valid = await formRef.value.validate().catch(() => false)
  if (!valid) return
  
  loading.value = true
  
  try {
    const response = await login(loginForm)
    const { token, user } = response.data
    
    // 保存token和用戶信息
    localStorage.setItem('vidspark_token', token)
    localStorage.setItem('vidspark_user', JSON.stringify(user))
    
    ElMessage.success(t('auth.loginSuccess'))
    router.push('/dashboard')
  } catch (error: any) {
    ElMessage.error(error.response?.data?.message || 'Login failed')
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 20px;
}

.login-container {
  background: white;
  border-radius: 16px;
  padding: 48px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 400px;
  position: relative;
}

.login-header {
  text-align: center;
  margin-bottom: 32px;
}

.login-header h1 {
  font-size: 32px;
  font-weight: bold;
  color: #5D5FEF;
  margin-bottom: 8px;
}

.login-header p {
  color: #666;
  font-size: 16px;
}

.login-form {
  margin-bottom: 24px;
}

.login-footer {
  text-align: center;
}

.login-footer a {
  color: #5D5FEF;
  text-decoration: none;
}

.login-footer a:hover {
  text-decoration: underline;
}

.language-switcher {
  position: absolute;
  top: 16px;
  right: 16px;
}
</style>
