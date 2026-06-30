import { useRouter } from 'vue-router'
import type { RouteLocationRaw } from 'vue-router'

import { PageList } from '@/vuejs/router'
import { useCartStore } from '@/vuejs/stores/cart'
import { getUrlParam } from '@/vuejs/services/utils'
import { AdyenResultCode } from '@/vuejs/types/Cart'

const CART_ID_STORAGE_KEY = 'adyen_payment_cart_id'
const REDIRECT_PARAMS = ['redirectResult', 'MD', 'cres'] as const

export const hasAdyenRedirectParams = (): boolean =>
  REDIRECT_PARAMS.some((param) =>
    new URLSearchParams(window.location.search).has(param),
  )

export const storeAdyenPaymentCartId = (cartId: string): void => {
  if (cartId) sessionStorage.setItem(CART_ID_STORAGE_KEY, cartId)
}

export function useAdyenRedirectReturn() {
  const cartStore = useCartStore()
  const router = useRouter()

  const redirectWithReload = (to: RouteLocationRaw): void => {
    window.location.assign(router.resolve(to).href)
  }

  const handle = async (): Promise<void> => {
    const redirectResult = getUrlParam('redirectResult')
    const MD = getUrlParam('MD')
    const PaRes = getUrlParam('PaRes')
    const cres = getUrlParam('cres')

    if (!redirectResult && !MD && !cres) return

    const url = new URL(window.location.href)
    ;['redirectResult', 'MD', 'PaRes', 'cres'].forEach((p) =>
      url.searchParams.delete(p),
    )
    window.history.replaceState({}, '', url.toString())

    const details: Record<string, string> = {}
    if (redirectResult) details.redirectResult = redirectResult
    if (MD) details.MD = MD
    if (PaRes) details.PaRes = PaRes
    if (cres) details.cres = cres

    const cartId = sessionStorage.getItem(CART_ID_STORAGE_KEY) ?? ''
    sessionStorage.removeItem(CART_ID_STORAGE_KEY)

    const result = await cartStore.submitAdyenPaymentDetails({ details })

    const failedCodes = [
      AdyenResultCode.REFUSED,
      AdyenResultCode.CANCELLED,
      AdyenResultCode.ERROR,
    ].map((c) => c.toLowerCase())
    const resultCodeNormalized = result?.resultCode?.toLowerCase() ?? ''
    const isSuccess = !!result && !failedCodes.includes(resultCodeNormalized)

    if (isSuccess) {
      cartStore.forceEmptyCart()
      redirectWithReload({
        name: PageList.CART_CONFIRMED,
        params: { id: cartId || '0' },
      })
      return
    }

    redirectWithReload({ name: PageList.CART_PAYMENT_ERROR })
  }

  return { handle }
}
