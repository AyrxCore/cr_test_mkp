<template>
  <div>
    <div class="mt-10">
      <h3 class="home-subtitle text-primary">
        Les accords-cadres incontournables
      </h3>
      <p class="text-sm text-gray-400 sm:text-lg">
        Êtes-vous certains de profiter de toutes les économies incluses dans
        votre adhésion ?
      </p>
    </div>
    <div v-if="productsAccordsCadre.length" class="relative mt-5">
      <CarouselListSharedComponent
        :slides-per-view="1"
        :space-between="20"
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
          v-for="accord in productsAccordsCadre"
          :key="accord.id"
          class="flex h-full items-center justify-center overflow-hidden rounded-lg bg-white"
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

const productStore = useProductStore()
const { productsAccordsCadre } = storeToRefs(productStore)
</script>

<style scoped></style>
