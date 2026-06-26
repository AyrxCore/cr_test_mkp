<template>
  <div class="relative mt-5">
    <CarouselListSharedComponent
      :slides-per-view="1"
      :space-between="20"
      :overflow-visible="true"
      :breakpoints="{
        1600: {
          slidesPerView: 5,
          spaceBetween: 20,
        },
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
        v-for="number in props.accordsCadresNumber"
        :key="number"
        class="!flex h-full items-center justify-center overflow-hidden rounded-lg border-4 border-solid border-secondary bg-primary bg-white"
      >
        <AccordCadreLoadingComponent />
      </SwiperSlide>
    </CarouselListSharedComponent>
  </div>
</template>
<script lang="ts" setup>
import { SwiperSlide } from 'swiper/vue'
import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import AccordCadreLoadingComponent from '@/vuejs/modules/products/components/AccordCadreLoadingComponent.vue'

const props = defineProps({
  accordsCadresNumber: {
    type: Number,
    default: 5,
  },
})
</script>

