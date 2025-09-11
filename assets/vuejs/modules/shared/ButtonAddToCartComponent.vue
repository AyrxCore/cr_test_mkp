<template>
  <ButtonComponent
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
import { PropType, ref } from 'vue'

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
  variantId: {
    required: false,
    type: Number,
    default: null,
  },
  price: {
    required: false,
    type: Number,
    default: null,
  },
})

const cartStore = useCartStore()

const isLoading = ref<boolean>(false)

const addToCart = async (): Promise<void> => {
  if (!cartStore.cart) return
  isLoading.value = true
  try {
    await cartStore.addProductToCart(props.variantId, props.quantity)
    isLoading.value = false
  } catch (e) {
  } finally {
    isLoading.value = false
  }
}
</script>
