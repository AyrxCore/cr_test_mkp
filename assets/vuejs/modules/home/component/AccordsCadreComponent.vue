<template>
  <div v-if="accordsCadres?.length > 0" class="relative mt-1 md:mt-5">
    <CarouselListSharedComponent
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
      :slides-per-view="1"
      :space-between="20"
      class="swiper-nav-outside"
      @click-left="$emit('click-left')"
      @click-right="$emit('click-right')"
    >
      <SwiperSlide
        v-for="accord in accordsCadres"
        :key="accord.id"
        class="relative !flex !h-auto items-stretch justify-center overflow-hidden bg-white"
      >
        <AccordCadreComponent
          :accord="accord"
          @click-accord-cadre-card="$emit('click-accord-cadre-card', $event)"
          @show-showcase-modal="$emit('show-showcase-modal', $event)"
        />
      </SwiperSlide>
    </CarouselListSharedComponent>
  </div>
  <AccordsCadresLoadingCarouselComponent v-else-if="loading" />
</template>

<script lang="ts" setup>
import { SwiperSlide } from 'swiper/vue'
import { PropType } from 'vue'

import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import AccordCadreComponent from '@/vuejs/modules/home/component/AccordCadreComponent.vue'
import AccordsCadresLoadingCarouselComponent from '@/vuejs/modules/shared/AccordsCadresLoadingCarouselComponent.vue'
import { Product } from '@/vuejs/types/Product'

const props = defineProps({
  accordsCadres: {
    required: true,
    type: Array || (Object as PropType<Product>),
    default: [],
  },
  loading: {
    required: true,
    type: Boolean,
  },
})

const emit = defineEmits([
  'click-left',
  'click-right',
  'click-accord-cadre-card',
  'show-showcase-modal',
])
</script>
