<template>
  <div v-if="accordsCadres?.length" class="relative mt-1 md:mt-5">
    <CarouselListSharedComponent
      :slides-per-view="1"
      :space-between="20"
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
      class="swiper-nav-outside"
      @click-left="sendGaEvent('click_slider_home_fat_left')"
      @click-right="sendGaEvent('click_slider_home_fat_right')"
    >
      <SwiperSlide
        v-for="accord in accordsCadres"
        :key="accord.id"
        class="flex !h-auto items-stretch justify-center overflow-hidden rounded-lg border-4 border-solid border-secondary bg-white"
      >
        <AccordCadreComponent :accord="accord" />
      </SwiperSlide>
    </CarouselListSharedComponent>
  </div>
  <AccordsCadresLoadingCarouselComponent v-else />
</template>

<script lang="ts" setup>
import { SwiperSlide } from 'swiper/vue'
import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import AccordCadreComponent from '@/vuejs/modules/home/component/AccordCadreComponent.vue'
import AccordsCadresLoadingCarouselComponent from '@/vuejs/modules/shared/AccordsCadresLoadingCarouselComponent.vue'
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'
import { PropType } from 'vue'
import { Product } from '@/vuejs/types/Product'

const props = defineProps({
  accordsCadres: {
    required: true,
    type: Array || (Object as PropType<Product>),
  },
})
</script>

<style scoped>
>>> .swiper-wrapper {
  justify-content: space-between;
}
</style>
