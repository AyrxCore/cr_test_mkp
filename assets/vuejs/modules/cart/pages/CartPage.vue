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
          v-if="
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

import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import CartBreadcrumbItemComponent from '@/vuejs/modules/cart/components/CartBreadcrumbItemComponent.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import LoadingComponent from '@/vuejs/modules/shared/LoadingComponent.vue'

const router = useRouter()
const cartStore = useCartStore()
const addressStore = useAddressStore()

const loadingCart = ref<boolean>(true)

const currentRouteName = computed(
  (): RouteRecordName => router.currentRoute.value.name,
)

onMounted(async () => {
  await addressStore.getAddresses()
  await cartStore.getCart()
  loadingCart.value = false
})
</script>
