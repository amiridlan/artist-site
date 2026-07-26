import { ref, onMounted, watch } from 'vue'

export function useDarkMode() {
  const isDark = ref(false)
  const isReady = ref(false)

  const initDarkMode = () => {
    // 1. Check localStorage
    const saved = localStorage.getItem('klp48-admin-theme')
    if (saved) {
      isDark.value = saved === 'dark'
    } else {
      // 2. Fallback to system preference
      isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches
    }
    applyTheme()

    // Mark as ready to enable transitions
    setTimeout(() => {
      isReady.value = true
      document.documentElement.classList.add('dark-mode-ready')
    }, 50)
  }

  const applyTheme = () => {
    console.log('Applying theme:', isDark.value ? 'dark' : 'light')
    if (isDark.value) {
      document.documentElement.classList.add('dark')
      console.log('Added dark class to HTML')
    } else {
      document.documentElement.classList.remove('dark')
      console.log('Removed dark class from HTML')
    }
    console.log('HTML classes:', document.documentElement.className)
    localStorage.setItem('klp48-admin-theme', isDark.value ? 'dark' : 'light')
  }

  const toggleDarkMode = () => {
    isDark.value = !isDark.value
    applyTheme()
  }

  // Listen for system preference changes
  onMounted(() => {
    initDarkMode()

    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
    const handleChange = (e) => {
      // Only auto-switch if user hasn't set a preference
      if (!localStorage.getItem('klp48-admin-theme')) {
        isDark.value = e.matches
        applyTheme()
      }
    }

    mediaQuery.addEventListener('change', handleChange)

    // Cleanup
    return () => {
      mediaQuery.removeEventListener('change', handleChange)
    }
  })

  return {
    isDark,
    isReady,
    toggleDarkMode
  }
}
