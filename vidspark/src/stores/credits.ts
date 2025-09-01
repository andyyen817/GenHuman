import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export interface CreditTransaction {
  id: string;
  type: 'earn' | 'spend' | 'purchase';
  amount: number;
  reason: string;
  timestamp: string;
}

export const useCreditsStore = defineStore('credits', () => {
  // State
  const balance = ref(0);
  const transactions = ref<CreditTransaction[]>([]);
  const dailyLimit = ref(3);
  const dailyUsed = ref(0);
  const lastResetDate = ref<string>(new Date().toISOString().split('T')[0]);

  // Getters
  const availableDailyCredits = computed(() => Math.max(0, dailyLimit.value - dailyUsed.value));
  const hasCredits = computed(() => balance.value > 0 || availableDailyCredits.value > 0);
  
  // Actions
  const setBalance = (newBalance: number) => {
    balance.value = newBalance;
  };

  const addTransaction = (transaction: Omit<CreditTransaction, 'id' | 'timestamp'>) => {
    const newTransaction: CreditTransaction = {
      ...transaction,
      id: Date.now().toString(),
      timestamp: new Date().toISOString()
    };
    
    transactions.value.unshift(newTransaction);
    
    // Update balance
    if (transaction.type === 'earn' || transaction.type === 'purchase') {
      balance.value += transaction.amount;
    } else if (transaction.type === 'spend') {
      balance.value -= transaction.amount;
    }
  };

  const spendCredits = (amount: number, reason: string): boolean => {
    const today = new Date().toISOString().split('T')[0];
    
    // Reset daily credits if new day
    if (lastResetDate.value !== today) {
      dailyUsed.value = 0;
      lastResetDate.value = today;
    }

    // Check if user has enough credits
    const totalAvailable = balance.value + availableDailyCredits.value;
    if (totalAvailable < amount) {
      return false;
    }

    // Spend daily credits first
    const dailyToSpend = Math.min(amount, availableDailyCredits.value);
    const paidToSpend = amount - dailyToSpend;

    if (dailyToSpend > 0) {
      dailyUsed.value += dailyToSpend;
    }

    if (paidToSpend > 0) {
      balance.value -= paidToSpend;
      addTransaction({
        type: 'spend',
        amount: paidToSpend,
        reason
      });
    }

    console.log(`[${new Date().toLocaleTimeString()}] 💰 積分消費: ${amount} (${reason})`);
    return true;
  };

  const purchaseCredits = (amount: number, price: number) => {
    addTransaction({
      type: 'purchase',
      amount,
      reason: `購買積分包 - $${price}`
    });
    
    console.log(`[${new Date().toLocaleTimeString()}] 💳 購買積分: ${amount} ($${price})`);
  };

  const earnCredits = (amount: number, reason: string) => {
    addTransaction({
      type: 'earn',
      amount,
      reason
    });
    
    console.log(`[${new Date().toLocaleTimeString()}] 🎁 獲得積分: ${amount} (${reason})`);
  };

  const resetDailyCredits = () => {
    const today = new Date().toISOString().split('T')[0];
    if (lastResetDate.value !== today) {
      dailyUsed.value = 0;
      lastResetDate.value = today;
    }
  };

  // Initialize
  const initialize = () => {
    resetDailyCredits();
    
    // Load from localStorage if available
    const savedBalance = localStorage.getItem('vidspark_credits_balance');
    const savedDailyUsed = localStorage.getItem('vidspark_daily_used');
    const savedLastReset = localStorage.getItem('vidspark_last_reset');
    
    if (savedBalance) balance.value = parseInt(savedBalance);
    if (savedDailyUsed) dailyUsed.value = parseInt(savedDailyUsed);
    if (savedLastReset) lastResetDate.value = savedLastReset;
    
    resetDailyCredits();
  };

  // Save to localStorage
  const saveToStorage = () => {
    localStorage.setItem('vidspark_credits_balance', balance.value.toString());
    localStorage.setItem('vidspark_daily_used', dailyUsed.value.toString());
    localStorage.setItem('vidspark_last_reset', lastResetDate.value);
  };

  return {
    // State
    balance,
    transactions,
    dailyLimit,
    dailyUsed,
    lastResetDate,
    
    // Getters
    availableDailyCredits,
    hasCredits,
    
    // Actions
    setBalance,
    addTransaction,
    spendCredits,
    purchaseCredits,
    earnCredits,
    resetDailyCredits,
    initialize,
    saveToStorage
  };
});
