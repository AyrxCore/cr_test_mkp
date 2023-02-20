<template>
  <div>
    <div class="mt-10 sm:w-[45rem]">
      <h3 class="home-subtitle text-primary">Top ventes</h3>
      <p class="text-sm text-gray-400 sm:text-lg">
        D'autres adhérents ont déjà acheté ces produits
      </p>
    </div>
      <ProductsLoadingCarouselComponent v-if="isLoading"/>
      <ProductsCarouselComponent v-else :products="products" />
  </div>
</template>

<script lang="ts" setup>
import ProductsCarouselComponent from '@/vuejs/modules/shared/ProductsCarouselComponent.vue'
import { onMounted, ref } from 'vue'
import { useProductStore } from '@/vuejs/stores/product'
import ProductsLoadingCarouselComponent from '@/vuejs/modules/shared/ProductsLoadingCarouselComponent.vue'

const productStore = useProductStore()
const products = ref([])
const isLoading = ref<boolean>(true)

const props = defineProps({
  properties: {
    required: true,
    type: Object,
  },
})

onMounted(async () => {
  const params =  {
    properties: [
      props.properties
    ],
  }

  products.value = await productStore.getProductsTopVente(params)
  isLoading.value = false
})


</script>

<style scoped></style>
