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
        v-for="number in props.productsNumber"
        :key="number"
        class="flex h-full items-center justify-center overflow-hidden rounded-lg bg-white"
      >
        <ProductLoadingComponent />
      </SwiperSlide>
    </CarouselListSharedComponent>
  </div>
</template>
<script lang="ts" setup>
import { SwiperSlide } from 'swiper/vue'
import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import ProductLoadingComponent from '@/vuejs/modules/products/components/ProductLoadingComponent.vue'

const props = defineProps({
  productsNumber: {
    type: Number,
    default: 4,
  },
})
</script>
