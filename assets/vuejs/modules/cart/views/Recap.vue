<template>
  <div class="mt-8 mb-2 flex items-center justify-between">
    <h3 class="text-title-35 text-primary">
      Panier
      <span class="uppercase">{{ user.externalApiData.buyer.name }}</span>
    </h3>
    <ButtonComponent
      v-if="cart.orders && cart.orders.length > 0"
      class="button-secondary-outline"
      type="button"
      @click="openSaveCartForm"
    >
      Sauvegarder le panier
    </ButtonComponent>
    <SavedCartModal
      v-if="showSaveCartForm"
      class="modal"
      :is-loading="isLoading"
      @cancel="showSaveCartForm = false"
      @submit-saved-cart="onSubmitSavedCart"
    />
  </div>

  <div class="flex flex-col-reverse lg:grid lg:grid-cols-4 lg:gap-4 lg:px-0">
    <div
      v-if="cart.orders && cart.orders.length > 0"
      class="col-span-3 mt-10 rounded-lg lg:mt-0"
    >
      <CartOrderComponent
        v-for="(order, key) in cart.orders"
        :key="order.id"
        :order="order"
        :class="{
          'mb-5': cart.orders.length > 1 && key !== cart.orders.length,
        }"
      />
    </div>
    <template v-else>Votre panier est vide !</template>

    <CartRightSideComponent
      v-if="cart.orders && cart.orders.length > 0"
      :show-shipment-price="false"
    >
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
import { gtmCartTrackingEvent } from '@/vuejs/modules/cart'
import { useUserStore } from '@/vuejs/stores/user'
import SavedCartModal from '@/vuejs/modules/account/components/savedCart/SavedCartModal.vue'
import { useSavedCartStore } from '@/vuejs/stores/savedCart'

const router = useRouter()
const cartStore = useCartStore()
const savedCartStore = useSavedCartStore()
const { cart } = storeToRefs(cartStore)

const error = ref<string>(null)
const showSaveCartForm = ref<boolean>(false)
cartStore.termsOfSales = []

const userStore = useUserStore()
const { user } = storeToRefs(userStore)
const isLoading = ref<boolean>(false)

const goToAdress = async (): Promise<void> => {
  error.value = ''
  if (!cartStore.hasAllTermsChecked) {
    error.value = 'Veuillez accepter les conditions générales'
  } else {
    await gtmCartTrackingEvent('begin_checkout', cart.value)
    router.push({ name: CartPageList.CART_ADDRESSES })
  }
}

onMounted(async () => {
  await gtmCartTrackingEvent('view_cart', cart.value)
})

const openSaveCartForm = () => {
  showSaveCartForm.value = true
}

const onSubmitSavedCart = async (event) => {
  isLoading.value = true
  try {
    await savedCartStore.create(event.savedCart)
    await cartStore.getCart()
    showSaveCartForm.value = false
  } catch (error) {}

  isLoading.value = false
}
</script>

<style lang="postcss"></style>
