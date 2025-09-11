<template>
  <div class="mb-2 mt-8 flex items-center justify-between">
    <h3 class="text-title-primary">
      Panier
      <span class="uppercase">{{ user.externalApiData.buyer.name }}</span>
    </h3>
    <ButtonComponent
      v-if="
        cart.orders &&
        cart.orders.length > 0 &&
        channelStore.isAllowedToShow(OPTIONAL_FRONT_BLOCKS.SAVED_CARTS)
      "
      :disabled="isNeoAutoLogin"
      class="button-primary-outline"
      type="button"
      @click="openSaveCartForm"
    >
      Sauvegarder le panier
    </ButtonComponent>
    <SavedCartModal
      v-if="showSaveCartForm"
      :is-loading="isLoading"
      class="modal"
      @cancel="showSaveCartForm = false"
      @submit-saved-cart="onSubmitSavedCart"
    />
  </div>

  <div class="flex flex-col-reverse lg:grid lg:grid-cols-4 lg:gap-4 lg:px-0">
    <div
      v-if="cart.orders && cart.orders.length > 0"
      class="col-span-3 mt-5 rounded-lg lg:mt-0"
    >
      <CartOrderComponent
        v-for="(order, key) in cart.orders"
        :key="order.id"
        :class="{
          'mb-5': cart.orders.length > 1 && key !== cart.orders.length,
        }"
        :order="order"
      />
    </div>
    <template v-else>Votre panier est vide !</template>

    <CartRightSideComponent
      v-if="cart.orders && cart.orders.length > 0"
      :show-shipment-price="false"
    >
      <template #title>Récapitulatif panier</template>
      <template #button-next>
        <ButtonComponent class="button-primary mt-3 w-full" @click="goToAdress">
          <ArrowRightIconComponent class="h-4 w-4" />
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
import { onMounted, ref } from 'vue'
import { storeToRefs } from 'pinia'
import { useRouter } from 'vue-router'

import { CartPageList } from '@/vuejs/router/pages-list'
import { useCartStore } from '@/vuejs/stores/cart'
import { useUserStore } from '@/vuejs/stores/user'
import { useSavedCartStore } from '@/vuejs/stores/savedCart'
import { useChannelStore } from '@/vuejs/stores/channel'
import { formatCartItemsGtmEvent, sendGtmEvent } from '@/vuejs/services/gtm'
import { OPTIONAL_FRONT_BLOCKS } from '@/vuejs/services/const'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import CartOrderComponent from '@/vuejs/modules/cart/components/CartOrderComponent.vue'
import CartRightSideComponent from '@/vuejs/modules/cart/components/CartRightSideComponent.vue'
import SavedCartModal from '@/vuejs/modules/account/components/savedCart/SavedCartModal.vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'

const router = useRouter()
const cartStore = useCartStore()
const savedCartStore = useSavedCartStore()
const channelStore = useChannelStore()

const { cart } = storeToRefs(cartStore)

const error = ref<string>(null)
const showSaveCartForm = ref<boolean>(false)
cartStore.termsOfSales = []

const userStore = useUserStore()
const { user, isNeoAutoLogin } = storeToRefs(userStore)
const isLoading = ref<boolean>(false)

const goToAdress = async (): Promise<void> => {
  error.value = ''
  if (!cartStore.hasAllTermsChecked) {
    error.value = 'Veuillez accepter les conditions générales'
  } else {
    router.push({ name: CartPageList.CART_ADDRESSES })
  }
}

onMounted(() => {
  sendGtmEvent('view_cart', {
    ecommerce: {
      currency: 'EUR',
      items: formatCartItemsGtmEvent(cart.value),
    },
  })
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
