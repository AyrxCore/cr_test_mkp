<template>
  <div class="relative mt-5">
    <CarouselListSharedComponent
      :space-between="10"
      :loop="false"
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
        v-for="product in props.products"
        :key="product.id"
        class="flex h-full items-center justify-center overflow-hidden rounded-lg bg-white"
      >
        <ProductComponent :product="product" />
      </SwiperSlide>
    </CarouselListSharedComponent>
  </div>
</template>
<script lang="ts" setup>
import { SwiperSlide } from 'swiper/vue'

import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import ProductComponent from '@/vuejs/modules/products/components/ProductComponent.vue'

const props = defineProps({
  products: {
    required: true,
    type: Array,
  },
})
</script>
