<template>
  <ButtonComponent
    :disabled="disabled"
    :is-loading="isLoading"
    :style="{
      color: betterTextColor('primary'),
    }"
    class="button-primary"
    @click="addToCart"
  >
    <ShoppingCartIconComponent
      :stroke="betterTextColor('primary')"
      class="mr-2 w-4"
    />
    Ajouter
  </ButtonComponent>
</template>

<script lang="ts" setup>
import { computed, PropType, ref } from 'vue'

import { useCartStore } from '@/vuejs/stores/cart'
import { betterTextColor } from '@/vuejs/services/utils'
import { Product } from '@/vuejs/types/Product'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import ShoppingCartIconComponent from '@/vuejs/modules/shared/icon/ShoppingCartIconComponent.vue'

const props = defineProps({
  product: {
    required: true,
    type: Object as PropType<Product>,
  },
  quantity: {
    required: false,
    type: Number,
    default: 1,
  },
  disabled: {
    required: false,
    type: Boolean,
    default: false,
  },
})

const cartStore = useCartStore()

const isLoading = ref<boolean>(false)

const addToCart = async (): Promise<void> => {
  if (!cartStore.cart) return
  isLoading.value = true
  try {
    const data = [
      {
        offerPriceId: offerPriceExternalId.value,
        quantity: props.quantity,
      },
    ]
    await cartStore.addProductsToCart(data)
    await cartStore.getCart()
    isLoading.value = false
  } finally {
    isLoading.value = false
  }
}

const offerPriceExternalId = computed<string>(() => {
  return props.product.offerPriceExternalId ?? props.product.variants?.[0]?.offerPriceExternalId
})
</script>
