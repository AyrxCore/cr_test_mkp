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
import { onMounted, ref } from 'vue'
import { useProductStore } from '@/vuejs/stores/product'

const productStore = useProductStore()
const products = ref([])

onMounted(async () => {
  const params =  {
    properties: [
      HOME_SELECTION_PROPERTY
    ],
  }

  products.value = await productStore.getProductsSelection(params)
})


</script>

<style scoped></style>
