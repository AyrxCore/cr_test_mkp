<template>
  <div class="m-auto max-w-screen-94">
    <div class="mt-10 sm:w-[45rem]">
      <h3 class="home-title mb-3 text-primary">{{ title }}</h3>
      <p class="text-lg">
        {{ description }}
      </p>
    </div>
    <div class="m-auto max-w-screen-94">
      <ProductsLoadingCarouselComponent v-if="!products" />
      <ProductsCarouselComponent v-else :products="products" />
    </div>
  </div>
</template>

<script lang="ts" setup>
import ProductsCarouselComponent from '@/vuejs/modules/shared/ProductsCarouselComponent.vue'
import ProductsLoadingCarouselComponent from '@/vuejs/modules/shared/ProductsLoadingCarouselComponent.vue'
import { PropType } from 'vue'
import { Product } from '@/vuejs/types/Product'

defineProps({
  products: {
    type: Array as PropType<Product[]>,
    default: null,
  },
  title: {
    required: true,
    type: String,
  },
  description: {
    required: true,
    type: String,
  },
})
</script>

<style scoped></style>
