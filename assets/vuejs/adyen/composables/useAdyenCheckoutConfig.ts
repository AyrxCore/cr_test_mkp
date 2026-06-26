import type {
  AdditionalDetailsActions,
  AdditionalDetailsData,
  AdyenCheckoutError,
  CheckoutAdvancedFlowResponse,
  CoreConfiguration,
  PaymentCompletedData,
  PaymentFailedData,
  SubmitActions,
  SubmitData,
  UIElement,
} from '@adyen/adyen-web/auto'

import type {
  AdyenAdditionalDetails,
  AdyenInitiatePaymentPayload,
  AdyenInitiatePaymentResponse,
} from '@/vuejs/types/Cart'

import { buildBrowserInfo } from '@/vuejs/adyen/utils/browserInfo'

export interface AdyenCheckoutConfigOptions {
  clientKey: string
  environment: 'test' | 'live'
  totalAmountInCents: number
  reference: string
  enableCreditCardStorage: boolean
  paymentMethodsResponse: {
    paymentMethods: unknown[]
    storedPaymentMethods?: unknown[]
  }
  onInitiatePayment: (
    payload: AdyenInitiatePaymentPayload,
  ) => Promise<AdyenInitiatePaymentResponse | null>
  onSubmitDetails: (
    details: AdyenAdditionalDetails,
  ) => Promise<AdyenInitiatePaymentResponse | null>
  onPaymentCompleted: (result: AdyenInitiatePaymentResponse) => void
  onPaymentFailed: (result: AdyenInitiatePaymentResponse) => void
  onError: (error: AdyenCheckoutError) => void
}

function toAdvancedFlowResponse(result: AdyenInitiatePaymentResponse): CheckoutAdvancedFlowResponse {
  const { pspReference: _psp, action, ...clientSafe } = result as AdyenInitiatePaymentResponse & {
    pspReference?: string
    action?: { type?: string; [key: string]: unknown }
  }

  // action transmise uniquement si elle est un objet valide avec un type (3DS, redirect…)
  if (action && typeof action.type === 'string' && action.type !== '') {
    return { ...clientSafe, action } as unknown as CheckoutAdvancedFlowResponse
  }

  return clientSafe as unknown as CheckoutAdvancedFlowResponse
}

export function useAdyenCheckoutConfig(options: AdyenCheckoutConfigOptions) {
  const COUNTRY_CODE = 'FR' as const
  const amount = { value: options.totalAmountInCents, currency: 'EUR' as const }

  const coreConfiguration: CoreConfiguration = {
    clientKey: options.clientKey,
    environment: options.environment,
    locale: 'fr-FR',
    countryCode: COUNTRY_CODE,
    amount,
    paymentMethodsResponse:
      options.paymentMethodsResponse as CoreConfiguration['paymentMethodsResponse'],
    showPayButton: false,

    onSubmit: async (
      state: SubmitData,
      _component: UIElement,
      actions: SubmitActions,
    ) => {
      const data = state.data as unknown as {
        paymentMethod?: object
        browserInfo?: AdyenInitiatePaymentPayload['browserInfo']
        storePaymentMethod?: boolean
      }

      const payload: AdyenInitiatePaymentPayload = {
        browserInfo: data.browserInfo ?? buildBrowserInfo(),
        customerUserIP: '',
        paymentMethodData: (data.paymentMethod ?? {}) as Record<string, unknown>,
        reference: options.reference,
        storePaymentMethod: data.storePaymentMethod ?? false,
        returnPath: '/cart/payment',
        countryCode: COUNTRY_CODE,
        amount,
      }

      const result = await options.onInitiatePayment(payload)

      if (!result) {
        actions.reject()
        return
      }

      const resolved = toAdvancedFlowResponse(result)
      actions.resolve(resolved)
    },

    onAdditionalDetails: async (
      state: AdditionalDetailsData,
      _component: UIElement,
      actions: AdditionalDetailsActions,
    ) => {
      // 3DS2 natif : { details: { threeDSResult }, paymentData }
      // 3DS1 compat : { MD, PaRes } → wrappé en { details: { MD, PaRes } }
      const rawData = (state.data as Record<string, unknown>) ?? {}
      const stateData: Record<string, unknown> = 'details' in rawData
        ? rawData
        : { details: rawData }

      let result: AdyenInitiatePaymentResponse | null = null
      try {
        result = await options.onSubmitDetails(stateData as AdyenAdditionalDetails)
      } catch {
        actions.reject()
        return
      }

      if (!result) {
        actions.reject()
        return
      }

      const resolved = toAdvancedFlowResponse(result)
      actions.resolve(resolved)
    },

    onPaymentCompleted: (result: PaymentCompletedData) => {
      const resultCode = (result as unknown as { resultCode?: string }).resultCode

      const FAILED_CODES = ['refused', 'cancelled', 'error']
      if (resultCode && FAILED_CODES.includes(resultCode.toLowerCase())) {
        options.onPaymentFailed(result as unknown as AdyenInitiatePaymentResponse)
        return
      }

      options.onPaymentCompleted(result as unknown as AdyenInitiatePaymentResponse)
    },

    onPaymentFailed: (result?: PaymentFailedData) => {
      options.onPaymentFailed((result ?? {}) as unknown as AdyenInitiatePaymentResponse)
    },

    onError: (error: AdyenCheckoutError) => {
      options.onError(error)
    },
  }

  const dropinConfiguration = {
    paymentMethodsConfiguration: {
      card: {
        hasHolderName: true,
        holderNameRequired: true,
        enableStoreDetails: options.enableCreditCardStorage,
        challengeWindowSize: '05' as const,
      },
    },
    showStoredPaymentMethods: options.enableCreditCardStorage,
    enableStoreDetails: options.enableCreditCardStorage,
  }

  return { coreConfiguration, dropinConfiguration }
}
