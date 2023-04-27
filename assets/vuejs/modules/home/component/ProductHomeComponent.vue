<template>
  <div>
    <div class="mt-10 sm:w-[45rem]">
      <h3 class="home-subtitle text-primary">{{ props.title }}</h3>
      <p class="text-sm text-gray-400 sm:text-lg">
        D'autres adhérents ont déjà acheté ces produits
      </p>
    </div>
    <ProductsLoadingCarouselComponent v-if="props.products.length === 0" />
    <ProductsCarouselComponent v-else :products="props.products" />
  </div>
</template>

<script lang="ts" setup>
import ProductsCarouselComponent from '@/vuejs/modules/shared/ProductsCarouselComponent.vue'
import ProductsLoadingCarouselComponent from '@/vuejs/modules/shared/ProductsLoadingCarouselComponent.vue'

const props = defineProps({
  products: {
    type: Array,
    default: null,
  },
  title: {
    required: true,
    type: String,
  },
})
</script>

<style scoped></style>
