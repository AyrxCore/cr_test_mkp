<template>
  <div v-if="accordsCadres?.length > 0" class="relative mt-1 md:mt-5">
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
      @click-left="$emit('click-left')"
      @click-right="$emit('click-right')"
    >
      <SwiperSlide
        v-for="accord in accordsCadres"
        :key="accord.id"
        class="flex !h-auto items-stretch justify-center overflow-hidden rounded-lg border-4 border-solid border-secondary bg-white"
      >
        <AccordCadreComponent
          :accord="accord"
          @click-cta="$emit('click-cta', $event)"
          @click-title="$emit('click-title', $event)"
          @click-img="$emit('click-img', $event)"
        />
      </SwiperSlide>
    </CarouselListSharedComponent>
  </div>
  <AccordsCadresLoadingCarouselComponent v-else-if="loading" />
</template>

<script lang="ts" setup>
import { SwiperSlide } from 'swiper/vue'
import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import AccordCadreComponent from '@/vuejs/modules/home/component/AccordCadreComponent.vue'
import AccordsCadresLoadingCarouselComponent from '@/vuejs/modules/shared/AccordsCadresLoadingCarouselComponent.vue'
import { PropType } from 'vue'
import { Product } from '@/vuejs/types/Product'

const props = defineProps({
  accordsCadres: {
    required: true,
    type: Array || (Object as PropType<Product>),
  },
  loading: {
    required: true,
    type: Boolean,
  },
})

const emit = defineEmits([
  'click-left',
  'click-right',
  'click-cta',
  'click-title',
  'click-img',
])
</script>
