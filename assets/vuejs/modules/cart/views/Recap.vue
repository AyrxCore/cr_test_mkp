<template>
  <h3 class="mt-10 mb-2 text-title-35 text-primary">Votre panier</h3>
  <div class="flex flex-col-reverse lg:grid lg:grid-cols-4 lg:gap-4 lg:px-0">
    <div class="col-span-3 mt-10 rounded-lg lg:mt-0">
      <CartOrderComponent
        v-if="cart.orders && cart.orders.length > 0"
        v-for="(order, key) in cart.orders"
        :key="order.id"
        :order="order"
        :class="{
          'mb-5': cart.orders.length > 1 && key !== cart.orders.length,
        }"
      />
      <template v-else>Votre panier est vide !</template>
    </div>

    <div v-if="cart.orders && cart.orders.length > 0">
      <CartRightSideComponent>
        <template #title>Récapitulatif panier</template>
        <template #button-next>
          <ButtonComponent
            class="button button-gradient mt-3 w-full"
            @click="goToAdress"
          >
            <ArrowRightIconComponent :stroke-color="'#FFFFFF'" />
            Passer la commande
          </ButtonComponent>
          <div v-if="error" class="mt-2 text-center text-xs text-red-600">
            {{ error }}
          </div>
        </template>
      </CartRightSideComponent>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { storeToRefs } from 'pinia'
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'

import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import CartOrderComponent from '@/vuejs/modules/cart/components/CartOrderComponent.vue'
import CartRightSideComponent from '@/vuejs/modules/cart/components/CartRightSideComponent.vue'

import { CartPageList } from '@/vuejs/router/pages-list'

import { useCartStore } from '@/vuejs/stores/cart'
import { gtmCartTrackingEvent } from '@/vuejs/modules/cart';

const router = useRouter()
const cartStore = useCartStore()

const { cart } = storeToRefs(cartStore)

const error = ref<string>(null)

cartStore.termsOfSales = []

const goToAdress = async (): Promise<void> => {
  error.value = ''
  if (!cartStore.hasAllTermsChecked) {
    error.value = 'Veuillez accepter les conditions générales'
  } else if (!cartStore.hasAllShippingMethodsSelected) {
    error.value = 'Une ou plusieurs méthodes de livraisons sont incorrectes'
  } else {
    await gtmCartTrackingEvent('begin_checkout', cart.value)
    await router.push({ name: CartPageList.ADDRESSES })
  }
}

onMounted(async () => {
  await gtmCartTrackingEvent('view_cart', cart.value)
})
</script>

<style lang="postcss"></style>
