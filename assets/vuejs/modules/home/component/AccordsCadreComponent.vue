<template>
  <div v-if="accordsCadre.length">
    <div class="mt-10">
      <h3 class="home-subtitle text-primary">
        Les accords-cadres incontournables
      </h3>
      <p class="text-sm text-gray-400 sm:text-lg">
        Etes-vous certains de profiter des touts les économies incluses dans
        votre adhésion ?
      </p>
    </div>
    <div class="relative mt-5">
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
          v-for="accord in accordsCadre"
          :key="accord.id"
          class="flex h-full items-center justify-center overflow-hidden rounded-lg bg-white"
        >
          <AccordCadreComponent :accord="accord" />
        </SwiperSlide>
      </CarouselListSharedComponent>
    </div>
  </div>

</template>

<script lang="ts" setup>
import { PRODUCT_ACCORD_PROPERTY } from '@/vuejs/services/utils'
import { onBeforeMount, ref } from 'vue'
import { SwiperSlide } from 'swiper/vue'
import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import { useAccordCadreStore } from '@/vuejs/stores/accord_cadre'
import AccordCadreComponent from '@/vuejs/modules/home/component/AccordCadreComponent.vue'

const accordCadreStore = useAccordCadreStore()
const accordsCadre = ref([])

onBeforeMount(async () => {
  const params =  {
    properties: [
      PRODUCT_ACCORD_PROPERTY
    ],
  }

  accordsCadre.value = await accordCadreStore.findAccordsCadresByParams(params)
})

</script>

<style scoped></style>
