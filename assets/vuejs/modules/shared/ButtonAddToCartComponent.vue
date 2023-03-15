<template>
  <ButtonComponent
    class="button-gradient"
    :is-loading="isLoading"
    @click="addProductToCart"
  >
    <ShoppingCartIconComponent class="mr-2 w-4" />Ajouter
  </ButtonComponent>
</template>

<script lang="ts" setup>
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import ShoppingCartIconComponent from '@/vuejs/modules/shared/icon/ShoppingCartIconComponent.vue'
import { PropType, computed, ref } from 'vue'

import { Product } from '@/vuejs/types/Product'
import { useCartStore } from '@/vuejs/stores/cart'

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
})

const isLoading = ref<boolean>(false)

const variant = computed(() => {
  return props.product.variants.find((v) => !v.is_master)
})

const addProductToCart = async (): Promise<void> => {
  if (!cartStore.cart) return
  isLoading.value = true
  await cartStore.addProductToCart(variant.value.id, props.quantity)
  await cartStore.getCart()
  isLoading.value = false
}
</script>

<style scoped></style>
