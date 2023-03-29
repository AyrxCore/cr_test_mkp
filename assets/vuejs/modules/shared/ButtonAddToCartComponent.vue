<template>
  <ButtonComponent
    class="button-gradient"
    :is-loading="isLoading"
    @click="addProductToCart"
  >
    <ShoppingCartIconComponent class="mr-2 w-4" />
    Ajouter
  </ButtonComponent>
</template>

<script lang="ts" setup>
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import ShoppingCartIconComponent from '@/vuejs/modules/shared/icon/ShoppingCartIconComponent.vue'
import { PropType, ref } from 'vue'
import { useCartStore } from '@/vuejs/stores/cart'
import { Product } from '@/vuejs/types/Product'

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
})

const isLoading = ref<boolean>(false)

const addProductToCart = async (): Promise<void> => {
  if (!cartStore.cart) return
  isLoading.value = true
  await cartStore.addProductToCart(props.variantId, props.quantity)
  isLoading.value = false
}
</script>

<style scoped></style>
