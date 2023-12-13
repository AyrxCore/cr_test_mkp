<template>
  <ButtonComponent
    class="button-primary"
    :style="{
      color: betterTextColor('primary'),
    }"
    :is-loading="isLoading"
    @click="addToCart"
  >
    <ShoppingCartIconComponent
      class="mr-2 w-4"
      :style="{
        stroke: betterTextColor('primary'),
      }"
    />
    Ajouter
  </ButtonComponent>
</template>

<script lang="ts" setup>
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import ShoppingCartIconComponent from '@/vuejs/modules/shared/icon/ShoppingCartIconComponent.vue'
import { PropType, ref } from 'vue'
import { useCartStore } from '@/vuejs/stores/cart'
import { Product } from '@/vuejs/types/Product'
import { addProductToCartGoogleAnalytics } from '@/vuejs/modules/products'
import { betterTextColor } from '@/vuejs/services/utils'

const cartStore = useCartStore()
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

const isLoading = ref<boolean>(false)

const addToCart = async (): Promise<void> => {
  if (!cartStore.cart) return
  isLoading.value = true
  try {
    await cartStore.addProductToCart(props.variantId, props.quantity)
    isLoading.value = false
    await addProductToCartGoogleAnalytics(
      props.product,
      props.variantId,
      props.quantity,
      props.price,
    )
  } catch (e) {
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped></style>
