<template>
  <div class="relative mt-5">
    <CarouselListSharedComponent
      :slides-per-view="1"
      :space-between="10"
      :breakpoints="{
        1280: {
          slidesPerView: 4,
          spaceBetween: 20,
        },
        1024: {
          slidesPerView: 3,
          spaceBetween: 20,
        },
        640: {
          slidesPerView: 2,
          spaceBetween: 20,
        },
      }"
    >
      <SwiperSlide
        v-for="(product, key) in props.products"
        :key="key"
        class="flex h-full items-center justify-center overflow-hidden rounded-lg bg-white"
      >
        <ProductComponent :product="product" />
      </SwiperSlide>
    </CarouselListSharedComponent>
  </div>
</template>
<script lang="ts" setup>
import { PropType } from 'vue'
import { SwiperSlide } from 'swiper/vue'

import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import ProductComponent from '@/vuejs/modules/products/components/ProductComponent.vue'

import { Product } from '@/vuejs/types/Product'

const props = defineProps({
  products: {
    required: true,
    type: Object as PropType<Product[]>,
  },
})
</script>
