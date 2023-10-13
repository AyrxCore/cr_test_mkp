<template>
  <div>
    <div class="mt-10 sm:w-[45rem]">
      <h3 class="home-subtitle text-primary">{{ title }}</h3>
      <p class="text-sm text-gray-400 sm:text-lg">
        {{ description }}
      </p>
    </div>
    <ProductsLoadingCarouselComponent v-if="!products" />
    <ProductsCarouselComponent v-else :products="products" />
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
