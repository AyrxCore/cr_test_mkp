<template>
  <div v-if="products?.length > 0" class="relative mt-1 md:mt-5">
    <CarouselListSharedComponent
      :space-between="10"
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
        v-for="product in props.products"
        :key="product.id"
        class="!flex !h-auto items-stretch justify-center overflow-hidden rounded-lg bg-white"
      >
        <ProductComponent
          :product="product"
          @click-add-cart="$emit('click-add-cart', $event)"
          @click-title="$emit('click-title', $event)"
          @click-img="$emit('click-img', $event)"
          @click-moins-qty="$emit('click-moins-qty', $event)"
          @click-plus-qty="$emit('click-plus-qty', $event)"
        />
      </SwiperSlide>
    </CarouselListSharedComponent>
  </div>
  <ProductsLoadingCarouselComponent v-else-if="loading" />
</template>
<script lang="ts" setup>
import { SwiperSlide } from 'swiper/vue'

import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import ProductComponent from '@/vuejs/modules/products/components/ProductCardComponent.vue'
import ProductsLoadingCarouselComponent from '@/vuejs/modules/shared/ProductsLoadingCarouselComponent.vue'

const emit = defineEmits([
  'click-left',
  'click-right',
  'click-add-cart',
  'click-title',
  'click-img',
  'click-moins-qty',
  'click-plus-qty',
])

const props = defineProps({
  products: {
    required: true,
    type: Array,
  },
  loading: {
    required: true,
    type: Boolean,
  },
})
</script>
