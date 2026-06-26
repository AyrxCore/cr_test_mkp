<template>
  <div ref="containerRef" />
</template>

<script lang="ts" setup>
import { onMounted, onUnmounted, ref } from 'vue'
import { AdyenCheckout, Dropin } from '@adyen/adyen-web/auto'
import '@adyen/adyen-web/styles/adyen.css'
import type { AdyenCheckoutError } from '@adyen/adyen-web/auto'

import type {
  AdyenAdditionalDetails,
  AdyenInitiatePaymentPayload,
  AdyenInitiatePaymentResponse,
} from '@/vuejs/types/Cart'

import { useAdyenCheckoutConfig } from '@/vuejs/adyen/composables/useAdyenCheckoutConfig'

const props = defineProps<{
  clientKey: string
  environment: 'test' | 'live'
  paymentMethodsResponse: {
    paymentMethods: unknown[]
    storedPaymentMethods?: unknown[]
  }
  enableCreditCardStorage: boolean
  totalAmountInCents: number
  reference: string
  onInitiatePayment: (
    payload: AdyenInitiatePaymentPayload,
  ) => Promise<AdyenInitiatePaymentResponse | null>
  onSubmitDetails: (
    details: AdyenAdditionalDetails,
  ) => Promise<AdyenInitiatePaymentResponse | null>
}>()

const emit = defineEmits<{
  (e: 'payment-completed', result: AdyenInitiatePaymentResponse): void
  (e: 'payment-failed', result: AdyenInitiatePaymentResponse): void
  (e: 'error', error: AdyenCheckoutError): void
}>()

const containerRef = ref<HTMLElement | null>(null)
let dropinInstance: InstanceType<typeof Dropin> | null = null

const submit = () => dropinInstance?.submit()
defineExpose({ submit })

onMounted(async () => {
  if (!containerRef.value) return

  const { coreConfiguration, dropinConfiguration } = useAdyenCheckoutConfig({
    clientKey:               props.clientKey,
    environment:             props.environment,
    totalAmountInCents:      props.totalAmountInCents,
    reference:               props.reference,
    enableCreditCardStorage: props.enableCreditCardStorage,
    paymentMethodsResponse:  props.paymentMethodsResponse,
    onInitiatePayment:       props.onInitiatePayment,
    onSubmitDetails:         props.onSubmitDetails,
    onPaymentCompleted:      (result) => emit('payment-completed', result),
    onPaymentFailed:         (result) => emit('payment-failed', result),
    onError:                 (error)  => emit('error', error),
  })

  const adyenCheckout = await AdyenCheckout(coreConfiguration)
  dropinInstance = new Dropin(adyenCheckout, dropinConfiguration).mount(
    containerRef.value,
  )
})

onUnmounted(() => {
  dropinInstance?.unmount()
  dropinInstance = null
})
</script>
