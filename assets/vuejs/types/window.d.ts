/**
 * Variables globales injectées par Symfony/Twig dans le HTML
 * via <script>window.__ADYEN_CLIENT_KEY__ = '...'</script>
 */
declare global {
  interface Window {
    __ADYEN_CLIENT_KEY__: string
    __ADYEN_ENVIRONMENT__: 'test' | 'live'
    __CARTO_API_KEY__: string
    APP_MODE: string | null
    MKP_GIT_TAG: string | null
  }
}

export {}

