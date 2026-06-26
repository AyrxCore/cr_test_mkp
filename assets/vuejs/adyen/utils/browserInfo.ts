import type { AdyenInitiatePaymentPayload } from '@/vuejs/types/Cart'

export function buildBrowserInfo(): AdyenInitiatePaymentPayload['browserInfo'] {
  return {
    acceptHeader: '*/*',
    colorDepth: window.screen.colorDepth,
    javaEnabled: false,
    javaScriptEnabled: true,
    language: navigator.language,
    screenHeight: window.screen.height,
    screenWidth: window.screen.width,
    timeZoneOffset: new Date().getTimezoneOffset(),
    userAgent: navigator.userAgent,
  }
}
