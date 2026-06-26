<template>
  <ProductsLoadingCarouselComponent v-if="loading" />
  <div v-else-if="products?.length > 0" class="relative mt-1 md:mt-5">
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
      :space-between="10"
      class="swiper-nav-outside"
      @click-left="$emit('click-left')"
      @click-right="$emit('click-right')"
    >
      <SwiperSlide
        v-for="product in props.products"
        :key="product.id"
        class="!flex !h-auto items-stretch justify-center overflow-hidden rounded-lg bg-white"
      >
        <ProductComponent :product="product" />
      </SwiperSlide>
    </CarouselListSharedComponent>
  </div>
</template>
<script lang="ts" setup>
import { SwiperSlide } from 'swiper/vue'

import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import ProductComponent from '@/vuejs/modules/products/components/ProductCardComponent.vue'
import ProductsLoadingCarouselComponent from '@/vuejs/modules/shared/ProductsLoadingCarouselComponent.vue'

const emit = defineEmits(['click-left', 'click-right'])

const props = defineProps({
  products: {
    required: true,
    type: Array,
    default: [],
  },
  loading: {
    required: true,
    type: Boolean,
  },
})
</script>
