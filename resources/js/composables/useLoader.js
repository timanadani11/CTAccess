import { ref } from 'vue'

// Estado reactivo global del loader
const isLoading = ref(false)

/**
 * Composable para controlar el loader global manualmente
 * 
 * Uso:
 * ```js
 * import { useLoader } from '@/composables/useLoader'
 * 
 * const { show, hide, isLoading } = useLoader()
 * 
 * // Mostrar loader
 * show()
 * 
 * // Ocultar loader
 * hide()
 * 
 * // Envolver una función asíncrona
 * await withLoader(async () => {
 *   await miPeticionLarga()
 * })
 * ```
 */
export function useLoader() {
  const show = () => {
    isLoading.value = true
  }

  const hide = () => {
    isLoading.value = false
  }

  /**
   * Ejecuta una función asíncrona mostrando el loader
   * @param {Function} fn - Función asíncrona a ejecutar
   * @returns {Promise} - Resultado de la función
   */
  const withLoader = async (fn) => {
    try {
      show()
      return await fn()
    } finally {
      hide()
    }
  }

  return {
    isLoading,
    show,
    hide,
    withLoader
  }
}
