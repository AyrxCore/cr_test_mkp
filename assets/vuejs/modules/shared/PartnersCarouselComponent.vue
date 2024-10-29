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
        v-for="(seller, key) in customSellers"
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
import { onMounted, ref, computed } from 'vue'
import { storeToRefs } from 'pinia'
import { SwiperSlide } from 'swiper/vue'

import { ProductPageList } from '@/vuejs/router/pages-list'
import { getUpplerImage } from '@/vuejs/services/utils'
import { useChannelStore } from '@/vuejs/stores/channel'
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

const channelStore = useChannelStore()
const sellerStore = useSellerStore()

const { channel } = storeToRefs(channelStore)
const { sellers } = storeToRefs(sellerStore)
const sellersLoading = ref<boolean>(false)

const customSellers = computed((): Seller[] => {
  const suppliersList = channel?.value?.options?.SUPPLIER_PARTNERS_HOMEPAGE_LIST
  if (sellers.value.length > 0 && suppliersList && !props.params) {
    return suppliersList.split(',').reduce((acc, e) => {
      const seller = sellers.value.find((s) => s.id === parseInt(e))
      if (seller) acc.push(seller)
      return acc
    }, [])
  }

  return sellers.value
})

onMounted(async () => {
  sellersLoading.value = true
  await sellerStore.getSellers(props.params)
  sellersLoading.value = false
})
</script>
