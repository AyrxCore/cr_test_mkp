<template>
  <h3 class="text-title-primary mb-2 mt-8">Choisir un type de paiement</h3>
  <div class="flex flex-col-reverse lg:grid lg:grid-cols-4 lg:gap-4 lg:px-0">
    <div class="col-span-3 flex flex-col">
      <template v-if="isLoadingPaymentMethods">
        <PaymentMethodSkeletonComponent />
      </template>

      <template v-else-if="!showDropin">
        <div class="lg:grid lg:grid-cols-2 lg:gap-2">
          <PaymentMethodComponent
            v-if="CBPaymentMethod"
            :is-loading="isCBLoading"
            :method="CBPaymentMethod"
            class="lg:mr-4"
            @select-method="initDropin"
          >
            <template #method-icon>
              <img :src="cbLogosImg" alt="CB Icons" class="m-auto h-20" />
            </template>
          </PaymentMethodComponent>
          <!--
          <PaymentMethodComponent
            v-for="method in SEPAPaymentMethods"
            :method="method"
            @select-method="selectSEPA(method)"
          >
            <template #method-icon>
              <SepaIconComponent class="m-auto" />
            </template>
          </PaymentMethodComponent>
          -->
          <template v-if="noMethodAvailable">
            Aucune méthode de paiement disponible
          </template>
        </div>
      </template>

      <template v-else-if="dropinConfig">
        <div class="relative">
          <div
            v-if="isProcessingPayment"
            class="absolute inset-0 z-10 flex flex-col items-center justify-center bg-white bg-opacity-90"
          >
            <div class="mb-4 h-12 w-12 animate-spin rounded-full border-4 border-primary border-t-transparent"></div>
            <p class="text-lg">Traitement du paiement en cours…</p>
          </div>

          <AdyenDropinComponent
            ref="dropinRef"
            :client-key="dropinConfig.clientKey"
            :environment="dropinConfig.environment"
            :payment-methods-response="dropinConfig.paymentMethodsResponse"
            :enable-credit-card-storage="enableCreditCardStorage"
            :total-amount-in-cents="dropinConfig.totalAmountInCents"
            :reference="dropinConfig.reference"
            :on-initiate-payment="handleInitiatePayment"
            :on-submit-details="handleSubmitDetails"
            @payment-completed="onDropinPaymentCompleted"
            @payment-failed="onDropinPaymentFailed"
            @error="onDropinError"
          />

          <div class="mt-4 flex gap-2">
            <button
              class="button button-secondary"
              :disabled="isProcessingPayment"
              @click="cancelDropin"
            >
              Retour
            </button>
            <button
              class="button button-primary"
              :disabled="isProcessingPayment"
              @click="submitDropin"
            >
              <span v-if="isProcessingPayment">Paiement en cours…</span>
              <span v-else>Payer</span>
            </button>
          </div>
        </div>
      </template>
    </div>

    <CartRightSideComponent :has-payment-methods="false">
      <template #title>Récapitulatif panier</template>
    </CartRightSideComponent>
  </div>
</template>

<script lang="ts" setup>
import { computed, onMounted, ref, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { useRouter } from 'vue-router'
import { useHead } from '@unhead/vue'

import { PageList } from '@/vuejs/router'
import { useCartStore } from '@/vuejs/stores/cart'
import { getImage } from '@/vuejs/services/utils'
import { formatCartItemsGtmEvent, sendGtmEvent } from '@/vuejs/services/gtm'
import {
  AdyenInitiatePaymentPayload,
  AdyenInitiatePaymentResponse,
  AdyenPaymentMethodType,
  AdyenSubmitDetailsPayload,
} from '@/vuejs/types/Cart'
import { storeAdyenPaymentCartId } from '@/vuejs/adyen/composables/useAdyenRedirectReturn'

import CartRightSideComponent from '@/vuejs/modules/cart/components/CartRightSideComponent.vue'
import PaymentMethodComponent from '@/vuejs/modules/cart/components/PaymentMethodComponent.vue'
import PaymentMethodSkeletonComponent from '@/vuejs/modules/cart/components/PaymentMethodSkeletonComponent.vue'
import AdyenDropinComponent from '@/vuejs/modules/cart/components/AdyenDropinComponent.vue'
import cbLogos from '@/vuejs/assets/img/cb-icons.png'
import type { AdyenCheckoutConfigOptions } from '@/vuejs/adyen/composables/useAdyenCheckoutConfig'

type DropinConfig = Pick<AdyenCheckoutConfigOptions, 'clientKey' | 'environment' | 'paymentMethodsResponse' | 'totalAmountInCents' | 'reference'>

const cartStore = useCartStore()
const router = useRouter()

const adyenClientKey = window.__ADYEN_CLIENT_KEY__ ?? ''
const adyenEnvironment = (window.__ADYEN_ENVIRONMENT__ ?? 'test') as
  | 'test'
  | 'live'

const {
  CBPaymentMethod,
  SEPAPaymentMethods,
  adyenPaymentMethods,
  storedPaymentMethods,
  enableCreditCardStorage,
} = storeToRefs(cartStore)

const isCBLoading = ref<boolean>(false)
const isLoadingPaymentMethods = ref<boolean>(true)
const isProcessingPayment = ref<boolean>(false)
const showDropin = ref<boolean>(false)
const dropinConfig = ref<DropinConfig | null>(null)
const dropinRef = ref<InstanceType<typeof AdyenDropinComponent> | null>(null)

const cbLogosImg = getImage(cbLogos)

const noMethodAvailable = computed(
  () => !CBPaymentMethod.value && SEPAPaymentMethods.value.length === 0,
)

const initDropin = async () => {
  if (!cartStore.cart?.id) return

  isCBLoading.value = true

  try {
    const cartId = String(cartStore.cart.id)

    const totalInCents = Math.round(
      ((cartStore.cart.totalPriceWithTax ?? 0) + cartStore.shippingCostTotal) * 100,
    )
    dropinConfig.value = {
      clientKey: adyenClientKey,
      environment: adyenEnvironment,
      paymentMethodsResponse: {
        paymentMethods: adyenPaymentMethods.value.filter(
          (m) => m.type === AdyenPaymentMethodType.SCHEME,
        ),
        storedPaymentMethods: storedPaymentMethods.value,
      },
      totalAmountInCents: totalInCents,
      reference: cartId,
    }

    showDropin.value = true

    sendGtmEvent('add_payment_info', {
      ecommerce: {
        currency: 'EUR',
        items: formatCartItemsGtmEvent(cartStore.cart),
      },
    })
  } finally {
    isCBLoading.value = false
  }
}

const cancelDropin = () => {
  showDropin.value = false
  dropinConfig.value = null
  cartStore.resetDropinState()
  isProcessingPayment.value = false
}

const submitDropin = () => {
  dropinRef.value?.submit()
}

const handleInitiatePayment = async (
  payload: AdyenInitiatePaymentPayload,
): Promise<AdyenInitiatePaymentResponse | null> => {
  isProcessingPayment.value = true
  storeAdyenPaymentCartId(payload.reference)
  const result = await cartStore.initiateAdyenPayment(payload)
  if (!result) isProcessingPayment.value = false
  return result
}

const handleSubmitDetails = async (
  stateData: Record<string, unknown>,
): Promise<AdyenInitiatePaymentResponse | null> => {
  const result = await cartStore.submitAdyenPaymentDetails(stateData as unknown as AdyenSubmitDetailsPayload)
  if (!result) isProcessingPayment.value = false
  return result
}

const redirectWithReload = (to: Parameters<typeof router.resolve>[0]) => {
  window.location.assign(router.resolve(to).href)
}

const onDropinPaymentCompleted = () => {
  sendGtmEvent('purchase', {
    ecommerce: {
      currency: 'EUR',
      items: formatCartItemsGtmEvent(cartStore.cart),
    },
  })

  const cartId = cartStore.cart?.id ?? ''
  cartStore.forceEmptyCart()

  redirectWithReload({ name: PageList.CART_CONFIRMED, params: { id: cartId } })
}

const onDropinPaymentFailed = () => {
  redirectWithReload({ name: PageList.CART_PAYMENT_ERROR })
}

const onDropinError = () => {
  redirectWithReload({ name: PageList.CART_PAYMENT_ERROR })
}

useHead({
  title: 'Paiement | QANTIS Marketplace',
  meta: [{ property: 'og:title', content: 'Paiement | QANTIS Marketplace' }],
})

onMounted(() => {
  sendGtmEvent('add_shipping_info', {
    ecommerce: {
      currency: 'EUR',
      items: formatCartItemsGtmEvent(cartStore.cart),
    },
  })
})

watch(
  () => cartStore.cart?.id,
  async (cartId) => {
    if (!cartId) {
      isLoadingPaymentMethods.value = false
      return
    }
    isLoadingPaymentMethods.value = true
    await cartStore.fetchAdyenPaymentMethods()
    isLoadingPaymentMethods.value = false
  },
  { immediate: true },
)
</script>
