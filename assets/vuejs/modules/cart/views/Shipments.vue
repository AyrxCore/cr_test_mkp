<template>
  <h3 class="text-title-primary mb-2 mt-8">Livraison</h3>
  <div class="flex flex-col-reverse lg:grid lg:grid-cols-4 lg:gap-4 lg:px-0">
    <div class="col-span-3">
      <CartShipmentComponent
        v-for="(cartOrder, key) in sortedCartOrders"
        :key="key"
        :cart-order="cartOrder"
        @loaded="shipmentsLoaded[key] = $event"
      >
        <template #order-index>
          ({{ key + 1 }} sur {{ sortedCartOrders.length }})
        </template>
      </CartShipmentComponent>
    </div>
    <CartRightSideComponent :show-payment-methods="true">
      <template #title>Récapitulatif panier</template>
      <template #button-next>
        <ButtonComponent
          :disabled="!allShipmentsLoaded"
          class="button-primary mt-3 w-full"
          @click="goToPayment"
        >
          <ArrowRightIconComponent class="h-4 w-4" />
          Continuer
        </ButtonComponent>
      </template>
    </CartRightSideComponent>
  </div>
</template>

<script lang="ts" setup>
import { computed, ref } from 'vue'
import { storeToRefs } from 'pinia'
import { useRouter } from 'vue-router'
import { useHead } from '@unhead/vue'

import { PageList } from '@/vuejs/router'
import { useCartStore } from '@/vuejs/stores/cart'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import CartRightSideComponent from '@/vuejs/modules/cart/components/CartRightSideComponent.vue'
import CartShipmentComponent from '@/vuejs/modules/cart/components/CartShipmentComponent.vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'

const router = useRouter()
const cartStore = useCartStore()

const { cart } = storeToRefs(cartStore)

const shipmentsLoaded = ref<boolean[]>([])

const sortedCartOrders = computed(() => {
  if (!cart.value.cartOrders) return []
  return [...cart.value.cartOrders].sort((a, b) => {
    const nameA = a.seller.name?.toLowerCase() || ''
    const nameB = b.seller.name?.toLowerCase() || ''
    return nameA.localeCompare(nameB)
  })
})

const allShipmentsLoaded = computed((): boolean => {
  return (
    shipmentsLoaded.value.length > 0 && shipmentsLoaded.value.every((e) => !!e)
  )
})

const goToPayment = async (): Promise<void> => {
  await cartStore.updateCustomerInfoInLogisticOrders()
  router.push({ name: PageList.CART_PAYMENT })
}

useHead({
  title: 'Livraison | QANTIS Marketplace',
  meta: [{ property: 'og:title', content: 'Livraison | QANTIS Marketplace' }],
})
</script>
