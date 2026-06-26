<template>
  <div :id="stepsBlockContent?.componentName">
    <div
      class="relative flex h-[426px] max-w-full flex-col items-center justify-center rounded-2xl bg-primary py-4 text-white"
    >
      <h2 class="mb-12 px-4 text-center font-cotext text-[28px] font-bold">
        {{ stepsBlockContent?.title }}
      </h2>
      <div class="hidden justify-around w-full gap-12 xl:flex">
        <Step
          v-for="(stepItem, key) in stepsBlockContent?.stepItems"
          :key="key"
          :step-item="stepItem"
          :step-key="key + 1"
          class="w-full max-w-[calc(20%-1.5rem)]"
        />
      </div>

      <div class="w-full px-4 xl:hidden">
        <CarouselListSharedComponent
          :breakpoints="{
            1024: {
              slidesPerView: 3,
              spaceBetween: 20,
            },
            768: {
              slidesPerView: 2,
              spaceBetween: 20,
            },
            640: {
              slidesPerView: 1,
              spaceBetween: 20,
            },
          }"
          :slides-per-view="1"
          :space-between="20"
          @click-left="$emit('click-left')"
          @click-right="$emit('click-right')"
        >
          <SwiperSlide
            v-for="(stepItem, key) in stepsBlockContent?.stepItems"
            :key="key"
            class="relative !flex !h-auto items-stretch justify-center overflow-hidden"
          >
            <Step :step-item="stepItem" :step-key="key + 1" />
          </SwiperSlide>
        </CarouselListSharedComponent>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { storeToRefs } from 'pinia'
import { SwiperSlide } from 'swiper/vue'

import { useAccordCadreStore } from '@/vuejs/stores/accordCadre.ts'

import Step from '@/vuejs/modules/products/components/accord-cadre/Step.vue'
import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'

const { stepsBlockContent } = storeToRefs(useAccordCadreStore())
</script>
