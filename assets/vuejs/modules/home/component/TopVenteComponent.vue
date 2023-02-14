<template>
  <div v-if="products.length > 0">
    <div class="mt-10 sm:w-[45rem]">
      <h3 class="home-subtitle text-primary">Top ventes</h3>
      <p class="text-sm text-gray-400 sm:text-lg">
        D'autres adhérents ont déjà acheté ces produits
      </p>
    </div>
      <ProductsCarouselComponent :products="products" />
  </div>
</template>

<script lang="ts" setup>
import ProductsCarouselComponent from '@/vuejs/modules/shared/ProductsCarouselComponent.vue'
import { HOME_TOP_VENTE_PROPERTY } from '@/vuejs/services/utils'
import { computed, onBeforeMount, onMounted, ref } from 'vue'
import { useProductStore } from '@/vuejs/stores/product'

const productStore = useProductStore()
const products = ref([])

onMounted(async () => {
  const params =  {
    properties: [
      HOME_TOP_VENTE_PROPERTY
    ],
  }

  products.value = await productStore.getProductsTopVente(params)
})


</script>

<style scoped></style>
