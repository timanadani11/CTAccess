import { ref, onMounted } from 'vue'

export function usePWA() {
  const isInstallable = ref(false)
  const isInstalled = ref(false)
  const showInstallPrompt = ref(false)
  let deferredPrompt = null

  onMounted(() => {
    // Check if app is already installed
    if (window.matchMedia('(display-mode: standalone)').matches) {
      isInstalled.value = true
    }

    // Listen for beforeinstallprompt event
    window.addEventListener('beforeinstallprompt', (e) => {
      e.preventDefault()
      deferredPrompt = e
      isInstallable.value = true
      showInstallPrompt.value = true
    })

    // Listen for app installation
    window.addEventListener('appinstalled', () => {
      isInstalled.value = true
      isInstallable.value = false
      showInstallPrompt.value = false
    })

    // Register service worker
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('/sw.js')
        .then(registration => {
          console.log('SW registered: ', registration)
        })
        .catch(registrationError => {
          console.log('SW registration failed: ', registrationError)
        })
    }
  })

  const installApp = async () => {
    if (deferredPrompt) {
      deferredPrompt.prompt()
      const { outcome } = await deferredPrompt.userChoice

      if (outcome === 'accepted') {
        console.log('PWA installation accepted')
      } else {
        console.log('PWA installation declined')
      }

      deferredPrompt = null
      showInstallPrompt.value = false
    }
  }

  const dismissInstallPrompt = () => {
    showInstallPrompt.value = false
  }

  return {
    isInstallable,
    isInstalled,
    showInstallPrompt,
    installApp,
    dismissInstallPrompt
  }
}