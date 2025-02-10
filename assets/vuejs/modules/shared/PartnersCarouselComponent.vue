<template>
  <div class="relative m-auto max-w-screen-94">
    <LoaderSharedComponent v-if="sellersLoading" class="m-auto" />
    <CarouselListSharedComponent
      v-else
      :breakpoints="{
        640: {
          slidesPerView: 6,
          spaceBetween: 10,
        },
        1280: {
          slidesPerView: 8,
          spaceBetween: 20,
        },
      }"
      :slides-per-view="1"
      :space-between="10"
      class="swiper-nav-outside"
    >
      <SwiperSlide
        v-for="(seller, key) in sellers"
        :key="key"
        class="!flex h-full items-center justify-center overflow-hidden rounded-lg bg-white p-1.5"
      >
        <RouterLink
          :to="{
            name: ProductPageList.PRODUCTS,
            query: { company: seller?.id },
          }"
          replace
        >
          <img
            :alt="seller?.name"
            :src="getUpplerImage(seller?.avatar)"
            class="pointer h-[107px] w-full cursor-pointer object-contain"
            @click="emit('click-partner-slider', seller?.name)"
          />
        </RouterLink>
      </SwiperSlide>
    </CarouselListSharedComponent>
  </div>
</template>
<script lang="ts" setup>
import { onMounted, ref } from 'vue'
import { SwiperSlide } from 'swiper/vue'

import { ProductPageList } from '@/vuejs/router/pages-list'
import { getUpplerImage } from '@/vuejs/services/utils'
import { useSellerStore } from '@/vuejs/stores/seller'
import { Seller } from '@/vuejs/types/Seller'

import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'

const props = defineProps({
  params: {
    type: Object,
  },
})

const emit = defineEmits(['click-partner-slider'])

const sellerStore = useSellerStore()

const sellersLoading = ref<boolean>(false)
const sellers = ref<Seller[]>([])

onMounted(async () => {
  sellersLoading.value = true
  sellers.value = !props.params
    ? await sellerStore.getAllSellers()
    : await sellerStore.getSellersByParams(props.params)
  sellersLoading.value = false
})
</script>
