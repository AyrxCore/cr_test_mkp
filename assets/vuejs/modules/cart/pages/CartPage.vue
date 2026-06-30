<template>
  <BaseTemplate title="Panier">
    <div
      class="xs:w-full m-auto my-4 mb-24 h-full max-w-screen-2xl flex-1 px-5 sm:px-8"
    >
      <BreadcrumbSharedComponent
        current-page="Panier"
        gtm-event-name="click_cart_breadcrumbs"
      />
      <div class="m-auto my-2 w-[100%] max-w-screen-2xl">
        <div class="flex flex-wrap">
          <CartBreadcrumbItemComponent :route-name="CartPageList.CART_RECAP">
            Panier
          </CartBreadcrumbItemComponent>
          <CartBreadcrumbItemComponent
            :route-name="CartPageList.CART_ADDRESSES"
          >
            Adresses
          </CartBreadcrumbItemComponent>
          <CartBreadcrumbItemComponent
            :route-name="CartPageList.CART_SHIPMENTS"
          >
            Livraison
          </CartBreadcrumbItemComponent>
          <CartBreadcrumbItemComponent
            :route-name="[
              CartPageList.CART_PAYMENT,
              CartPageList.CART_PAYMENT_SEPA,
            ]"
          >
            Paiement
          </CartBreadcrumbItemComponent>
          <CartBreadcrumbItemComponent
            v-if="CartPageList.CART_CONFIRMED === currentRouteName"
            :route-name="CartPageList.CART_CONFIRMED"
          >
            Confirmation
          </CartBreadcrumbItemComponent>
        </div>
        <template
          v-if="isProcessingPaymentReturn"
        >
          <div class="flex flex-col items-center justify-center py-12">
            <div class="mb-4 h-12 w-12 animate-spin rounded-full border-4 border-primary border-t-transparent"></div>
            <p class="text-lg">Traitement du paiement en cours…</p>
          </div>
        </template>
        <template
          v-else-if="
            !loadingCart || CartPageList.CART_CONFIRMED === currentRouteName
          "
        >
          <RouterView />
        </template>
        <LoadingComponent v-else />
      </div>
    </div>
  </BaseTemplate>
</template>

<script lang="ts" setup>
import { computed, onMounted, ref } from 'vue'
import { RouteRecordName, useRouter } from 'vue-router'

import { CartPageList } from '@/vuejs/router/pages-list'
import { useCartStore } from '@/vuejs/stores/cart'
import { useAddressStore } from '@/vuejs/stores/address'
import {
  hasAdyenRedirectParams,
  useAdyenRedirectReturn,
} from '@/vuejs/adyen/composables/useAdyenRedirectReturn'

import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import CartBreadcrumbItemComponent from '@/vuejs/modules/cart/components/CartBreadcrumbItemComponent.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import LoadingComponent from '@/vuejs/modules/shared/LoadingComponent.vue'

const router = useRouter()
const cartStore = useCartStore()
const addressStore = useAddressStore()
const { handle: handleAdyenRedirectReturn } = useAdyenRedirectReturn()

const loadingCart = ref<boolean>(true)
const isProcessingPaymentReturn = ref<boolean>(false)

const currentRouteName = computed(
  (): RouteRecordName => router.currentRoute.value.name,
)

onMounted(async () => {
  if (hasAdyenRedirectParams()) {
    isProcessingPaymentReturn.value = true
    loadingCart.value = false
    await handleAdyenRedirectReturn()
    return
  }

  await addressStore.getAddresses()
  await cartStore.getCart()
  loadingCart.value = false
})
</script>
