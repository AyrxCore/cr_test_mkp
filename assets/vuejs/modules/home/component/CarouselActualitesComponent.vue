<template>
  <span v-if="getExpertsContents.length>0">
    <div class="relative mt-2">
      <CarouselListSharedComponent
          :slides-per-view="1"
          :space-between="20"
          :breakpoints="{
              640: {
                slidesPerView: 1,
                spaceBetween: 20,
              },
            }"
      >
        <SwiperSlide
            v-for="expertContent in getExpertsContents"
            :key="expertContent.id"
            class="flex h-[303px] items-center justify-center overflow-hidden rounded-lg bg-white xl:h-full"
        >
          <img
              :src="expertContent.slider_img_mobile"
              alt="Picture"
              class="flex w-full items-center md:hidden"
          />
          <img
              :src="expertContent.slider_img_desktop"
              alt="Picture"
              class="mx-auto hidden items-center md:flex"
          />
        </SwiperSlide>
      </CarouselListSharedComponent>
    </div>
  </span>
</template>
<script lang="ts" setup>

import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import {SwiperSlide} from 'swiper/vue'
import {useExpertContentStore} from '@/vuejs/stores/expertContent'
import {storeToRefs} from 'pinia'

const expertContent = useExpertContentStore()
const {getExpertsContents} = storeToRefs(expertContent)

</script>
