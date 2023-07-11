<template>
  <h3 class="mt-8 mb-2 text-title-35 text-primary">Livraison</h3>
  <div class="flex flex-col-reverse lg:grid lg:grid-cols-4 lg:gap-4 lg:px-0">
    <div class="col-span-3">
      <template v-if="!isLoadingMethods">
        <CartShipmentComponent
          v-for="(order, key) in cart.orders"
          :order="order"
        >
          <template #order-index>
            ({{ key + 1 }} sur {{ cart.orders.length }})
          </template>
        </CartShipmentComponent>
      </template>
      <LoadingComponent v-else />
    </div>
    <CartRightSideComponent :show-shipment-price="!isLoadingMethods">
      <template #title>Récapitulatif</template>
      <template #button-next>
        <ButtonComponent
          class="button button-gradient mt-3 w-full"
          @click="goToPayment"
        >
          <ArrowRightIconComponent :stroke-color="'#FFFFFF'" />
          Continuer
        </ButtonComponent>
      </template>
    </CartRightSideComponent>
  </div>
</template>
<script lang="ts" setup>
import { onMounted, ref } from 'vue'
import { storeToRefs } from 'pinia'
import { useRouter } from 'vue-router'

import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import CartRightSideComponent from '@/vuejs/modules/cart/components/CartRightSideComponent.vue'
import CartShipmentComponent from '@/vuejs/modules/cart/components/CartShipmentComponent.vue'
import LoadingComponent from '@/vuejs/modules/shared/LoadingComponent.vue'

import { useCartStore } from '@/vuejs/stores/cart'
import { PageList } from '@/vuejs/router'
import { notifyError } from '@/vuejs/services/utils'

const router = useRouter()
const cartStore = useCartStore()

const { cart } = storeToRefs(cartStore)

const isLoadingMethods = ref<boolean>(true)

onMounted(async (): Promise<void> => {
  isLoadingMethods.value = true
  await cartStore.getCartShippingMethods(cart.value.id)
  isLoadingMethods.value = false
})

const goToPayment = async (): Promise<void> => {
  if (isLoadingMethods.value) return
  if (cart.value.orders.find((e) => e.shipments.length === 0)) {
    notifyError(
      'Une erreur est survenue dans le choix des méthodes de livraison.',
    )
    return
  }
  router.push({ name: PageList.CART_PAYMENT })
}
</script>

<style scoped></style>
