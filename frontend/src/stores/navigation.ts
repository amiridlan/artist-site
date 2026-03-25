import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useNavigationStore = defineStore('navigation', () => {
  const isMobileMenuOpen = ref(false)

  function toggleMobileMenu() {
    isMobileMenuOpen.value = !isMobileMenuOpen.value
    document.body.style.overflow = isMobileMenuOpen.value ? 'hidden' : ''
  }

  function closeMobileMenu() {
    isMobileMenuOpen.value = false
    document.body.style.overflow = ''
  }

  return {
    isMobileMenuOpen,
    toggleMobileMenu,
    closeMobileMenu,
  }
})
