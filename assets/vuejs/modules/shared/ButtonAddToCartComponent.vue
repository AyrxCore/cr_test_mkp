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
  price: {
    required: false,
    type: Number,
    default: null,
  },
})

const isLoading = ref<boolean>(false)

const addProductToCart = async (): Promise<void> => {
  const priceValue = props.price ?? props.product.price?.displayPrice
  if (!cartStore.cart) return
  isLoading.value = true
  await cartStore.addProductToCart(props.variantId, props.quantity)
  isLoading.value = false
  window.dataLayer.push({ ecommerce: null })
  const itemObject = {
    item_id: props.product.id,
    item_name: props.product.name,
    affiliation: props.product.seller.name, // Nom du partenaire
    item_variant: props.variantId,
    price: priceValue,
    quantity: props.quantity,
    item_category: null,
  }
  const categories = Object.entries(props.product.categories)
  if (categories.length > 0) {
    itemObject.item_category = categories[0][1]
  }

  await window.dataLayer?.push({
    event: 'add_to_cart',
    ecommerce: {
      currency: 'EUR',
      value: priceValue,
      items: [
        itemObject,
      ],
    },
  })
}
</script>

<style scoped></style>
