<template>
  <div v-if="products.length">
    <div class="mt-10 sm:w-[45rem]">
      <h3 class="home-subtitle text-primary">Une sélection de produits</h3>
      <p class="text-sm text-gray-400 sm:text-lg">
        Savez-vous que vous pouvez désormais acheter ces produits en quelques
        clics ?
      </p>
    </div>
    <ProductsCarouselComponent :products="products" />
  </div>
</template>

<script lang="ts" setup>
import ProductsCarouselComponent from '@/vuejs/modules/shared/ProductsCarouselComponent.vue'
import { HOME_SELECTION_PROPERTY } from '@/vuejs/services/utils'
import { computed, onBeforeMount } from 'vue'
import { useProductStore } from '@/vuejs/stores/product'

const productStore = useProductStore()

onBeforeMount(async () => {
  const params =  {
    properties: [
      HOME_SELECTION_PROPERTY
    ],
    cache_key: 'products-selection'
  }

  await productStore.findProductsSelection(params)
})
const products = computed(() => {
  return productStore.getProductsSelection
})


</script>

<style scoped></style>
