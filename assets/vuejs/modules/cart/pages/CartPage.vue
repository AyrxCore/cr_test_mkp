<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div class="xs:w-[100%] m-auto my-4 max-w-screen-2xl flex-1 px-5 sm:px-8">
      <BreadcrumbSharedComponent :current-page="'Panier'" />
      <div class="w-[100%] max-w-screen-2xl">
        <ContactUsButtonComponent />
      </div>
      <div class="m-auto my-2 w-[100%] max-w-screen-2xl">
        <div class="tabs clearfix flex w-max" data-tabgroup="first-tab-group">
          <CartBreadcrumbItemComponent :route-name="CartPageList.RECAP">
            Panier
          </CartBreadcrumbItemComponent>
          <CartBreadcrumbItemComponent :route-name="CartPageList.ADDRESSES">
            Adresses
          </CartBreadcrumbItemComponent>
          <CartBreadcrumbItemComponent :route-name="CartPageList.PAYMENT">
            Paiement
          </CartBreadcrumbItemComponent>
          <CartBreadcrumbItemComponent
            v-if="CartPageList.CONFIRMED === currentRouteName"
            :route-name="CartPageList.CONFIRMED"
          >
            Confirmation
          </CartBreadcrumbItemComponent>
        </div>
        <template
          v-if="!loadingCart || CartPageList.CONFIRMED === currentRouteName"
        >
          <RouterView />
        </template>
        <LoadingComponent v-else />
      </div>
    </div>
  </BaseTemplate>
</template>

<script lang="ts" setup>
import { storeToRefs } from 'pinia'
import { computed, onMounted, ref } from 'vue'
import { useRouter, RouteRecordName } from 'vue-router'

import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import CartBreadcrumbItemComponent from '@/vuejs/modules/cart/components/CartBreadcrumbItemComponent.vue'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import { CartPageList } from '@/vuejs/router/pages-list'
import { useCartStore } from '@/vuejs/stores/cart'
import LoadingComponent from '@/vuejs/modules/shared/LoadingComponent.vue'

const router = useRouter()
const cartStore = useCartStore()
const { cart } = storeToRefs(cartStore)

const loadingCart = ref<boolean>(true)

const currentRouteName = computed(
  (): RouteRecordName => router.currentRoute.value.name,
)

onMounted(async () => {
  await cartStore.getCart()
  loadingCart.value = false
})
</script>

<style scoped></style>
