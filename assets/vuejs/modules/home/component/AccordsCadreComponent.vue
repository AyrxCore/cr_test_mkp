<template>
  <div class="mt-10 bg-white px-12 py-8">
    <div class="m-auto max-w-screen-98">
      <h3 class="text-title-primary mb-3">
        Les accords-cadres incontournables
      </h3>
      <p class="text-sm sm:text-lg">
        Profitez de toutes les économies incluses dans votre adhésion&nbsp;!
      </p>
    </div>
    <div
      v-if="productsAccordsCadre?.results.length"
      class="relative m-auto mt-1 max-w-screen-94 md:mt-5"
    >
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
          v-for="accord in productsAccordsCadre?.results"
          :key="accord.id"
          class="flex h-full items-center justify-center overflow-hidden rounded-lg border-4 border-solid border-secondary bg-white"
        >
          <AccordCadreComponent :accord="accord" />
        </SwiperSlide>
      </CarouselListSharedComponent>
    </div>
    <AccordsCadresLoadingCarouselComponent v-else />
  </div>
</template>

<script lang="ts" setup>
import { SwiperSlide } from 'swiper/vue'
import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import AccordCadreComponent from '@/vuejs/modules/home/component/AccordCadreComponent.vue'
import { useProductStore } from '@/vuejs/stores/product'
import { storeToRefs } from 'pinia'
import AccordsCadresLoadingCarouselComponent from '@/vuejs/modules/shared/AccordsCadresLoadingCarouselComponent.vue'
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'

const productStore = useProductStore()
const { productsAccordsCadre } = storeToRefs(productStore)
</script>

<style scoped></style>
