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
            :route-name="CartPageList.CONFIRMED"
            v-if="CartPageList.CONFIRMED === currentRouteName"
          >
            Confirmation
          </CartBreadcrumbItemComponent>
        </div>
        <template v-if="cart || CartPageList.CONFIRMED === currentRouteName">
          <RouterView />
        </template>
        <LoaderSharedComponent v-else class="loader-xl mt-6" />
      </div>
    </div>
  </BaseTemplate>
</template>

<script lang="ts" setup>
import { storeToRefs } from 'pinia'
import { computed } from 'vue'
import { useRouter, RouteRecordName } from 'vue-router'

import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import CartBreadcrumbItemComponent from '@/vuejs/modules/cart/components/CartBreadcrumbItemComponent.vue'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'

import { CartPageList } from '@/vuejs/router/pages-list'
import { useCartStore } from '@/vuejs/stores/cart'

const router = useRouter()
const cartStore = useCartStore()
const { cart } = storeToRefs(cartStore)

const currentRouteName = computed(
  (): RouteRecordName => router.currentRoute.value.name,
)
</script>

<style scoped></style>
