<template>
  <div class="relative mt-1 md:mt-5">
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
      @click-left="gtmEvent('click_slider_home_products_left')"
      @click-right="gtmEvent('click_slider_home_products_right')"
    >
      <SwiperSlide
        v-for="product in props.products"
        :key="product.id"
        class="flex h-full items-center justify-center overflow-hidden rounded-lg bg-white"
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
import { buildStandardGtmData, gtmMixinPushEvent } from '@/vuejs/services/gtm'
import { useUserStore } from '@/vuejs/stores/user'
import { useChannelStore } from '@/vuejs/stores/channel'

const userStore = useUserStore()
const channelStore = useChannelStore()

const currentChannel = channelStore.currentChannel

const props = defineProps({
  products: {
    required: true,
    type: Array,
  },
})

const gtmEvent = (eventName: string) => {
  gtmMixinPushEvent(
    eventName,
    buildStandardGtmData(userStore.user['@id'], currentChannel.name),
  )
}
</script>
