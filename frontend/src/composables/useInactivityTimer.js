import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'vue-toastification'

export function useInactivityTimer() {
  const router = useRouter()
  const authStore = useAuthStore()
  const toast = useToast()
  
  const INACTIVITY_TIMEOUT = 30 * 60 * 1000 // 30 minutes
  const WARNING_TIME = 2 * 60 * 1000 // 2 minutes before logout
  const CHECK_INTERVAL = 1000 // Check every second
  
  let inactivityTimer = null
  let warningTimer = null
  let checkInterval = null
  let lastActivity = Date.now()
  let warningShown = false
  
  const timeUntilLogout = ref(0)
  const showWarning = ref(false)
  
  // Reset the timer
  const resetTimer = () => {
    lastActivity = Date.now()
    warningShown = false
    showWarning.value = false
    
    if (warningTimer) {
      clearTimeout(warningTimer)
    }
    if (inactivityTimer) {
      clearTimeout(inactivityTimer)
    }
    
    // Set warning timer (28 minutes)
    warningTimer = setTimeout(() => {
      if (!warningShown) {
        warningShown = true
        showWarning.value = true
        toast.warning('سيتم تسجيل خروجك تلقائياً بعد دقيقتين بسبب عدم النشاط', {
          timeout: false,
          closeButton: false
        })
      }
    }, INACTIVITY_TIMEOUT - WARNING_TIME)
    
    // Set logout timer (30 minutes)
    inactivityTimer = setTimeout(() => {
      handleLogout()
    }, INACTIVITY_TIMEOUT)
  }
  
  // Handle automatic logout
  const handleLogout = async () => {
    toast.error('تم تسجيل خروجك تلقائياً بسبب عدم النشاط')
    await authStore.logout()
    window.location.href = '/parfumes/login'
  }
  
  // Update countdown
  const updateCountdown = () => {
    const elapsed = Date.now() - lastActivity
    const remaining = INACTIVITY_TIMEOUT - elapsed
    
    if (remaining > 0) {
      timeUntilLogout.value = Math.ceil(remaining / 1000)
      
      // Show warning if less than 2 minutes remaining
      if (remaining <= WARNING_TIME && !warningShown) {
        warningShown = true
        showWarning.value = true
        toast.warning('سيتم تسجيل خروجك تلقائياً بعد دقيقتين بسبب عدم النشاط', {
          timeout: false,
          closeButton: false
        })
      }
    } else {
      timeUntilLogout.value = 0
    }
  }
  
  // Activity event handlers
  const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click']
  
  const handleActivity = () => {
    resetTimer()
  }
  
  // Start monitoring
  const startMonitoring = () => {
    // Add event listeners
    events.forEach(event => {
      document.addEventListener(event, handleActivity, true)
    })
    
    // Start countdown interval
    checkInterval = setInterval(updateCountdown, CHECK_INTERVAL)
    
    // Initialize timer
    resetTimer()
  }
  
  // Stop monitoring
  const stopMonitoring = () => {
    // Remove event listeners
    events.forEach(event => {
      document.removeEventListener(event, handleActivity, true)
    })
    
    // Clear timers
    if (inactivityTimer) clearTimeout(inactivityTimer)
    if (warningTimer) clearTimeout(warningTimer)
    if (checkInterval) clearInterval(checkInterval)
  }
  
  // Auto-start on mount
  onMounted(() => {
    if (authStore.isAuthenticated) {
      startMonitoring()
    }
  })
  
  // Cleanup on unmount
  onUnmounted(() => {
    stopMonitoring()
  })
  
  return {
    timeUntilLogout,
    showWarning,
    resetTimer,
    startMonitoring,
    stopMonitoring
  }
}
