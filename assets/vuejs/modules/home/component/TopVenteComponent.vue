<template>
  <div>
      <ProductsCarouselComponent :products="products" />
  </div>
</template>

<script lang="ts" setup>
import ProductsCarouselComponent from '@/vuejs/modules/shared/ProductsCarouselComponent.vue'
import { HOME_TOP_VENTE_PROPERTY } from '@/vuejs/services/utils'
import { computed, onBeforeMount } from 'vue'
import { useProductStore } from '@/vuejs/stores/product'

const productStore = useProductStore()

onBeforeMount(async () => {
  if (productStore.getProductsTopVente.length < 4) {
    const params =  {
      properties: [
        HOME_TOP_VENTE_PROPERTY
      ],
      cache_key: 'product-top-vente'
    }

    await productStore.findProductsTopVente(params)
  }
})
const products = computed(() => {
  return productStore.getProductsTopVente
})

</script>

<style scoped></style>
