import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export interface UserInfo {
  id: string;
  username: string;
  email: string;
  avatar?: string;
  level: 'Bronze' | 'Silver' | 'Gold' | 'Platinum';
  credits: number;
  createdAt: string;
}

export const useUserStore = defineStore('user', () => {
  // State
  const userInfo = ref<UserInfo | null>(null);
  const isLoggedIn = ref(false);
  const token = ref<string | null>(null);

  // Getters
  const userName = computed(() => userInfo.value?.username || '用戶');
  const userLevel = computed(() => userInfo.value?.level || 'Bronze');
  const userCredits = computed(() => userInfo.value?.credits || 0);

  // Actions
  const setUser = (user: UserInfo) => {
    userInfo.value = user;
    isLoggedIn.value = true;
  };

  const setToken = (newToken: string) => {
    token.value = newToken;
    localStorage.setItem('vidspark_user_token', newToken);
  };

  const logout = () => {
    userInfo.value = null;
    isLoggedIn.value = false;
    token.value = null;
    localStorage.removeItem('vidspark_user_token');
  };

  const updateCredits = (newCredits: number) => {
    if (userInfo.value) {
      userInfo.value.credits = newCredits;
    }
  };

  const updateLevel = (newLevel: UserInfo['level']) => {
    if (userInfo.value) {
      userInfo.value.level = newLevel;
    }
  };

  // Initialize from localStorage
  const initializeAuth = () => {
    const savedToken = localStorage.getItem('vidspark_user_token');
    if (savedToken) {
      token.value = savedToken;
      isLoggedIn.value = true;
      // TODO: 驗證token並獲取用戶信息
    }
  };

  return {
    // State
    userInfo,
    isLoggedIn,
    token,
    
    // Getters
    userName,
    userLevel,
    userCredits,
    
    // Actions
    setUser,
    setToken,
    logout,
    updateCredits,
    updateLevel,
    initializeAuth
  };
});
