<template>
  <div class="relative">
    <CarouselListSharedComponent
      :slides-per-view="1"
      :space-between="10"
      :breakpoints="{
        640: {
          slidesPerView: 8,
          spaceBetween: 50,
        },
      }"
      :navigation="{
        prevEl: '.swiper-button-partners-prev',
        nextEl: '.swiper-button-partners-next',
      }"
    >
      <SwiperSlide
        v-for="(partenaire, key) in partenaires"
        :key="key"
        class="flex h-full items-center justify-center overflow-hidden rounded-lg bg-white"
      >
        <img
          :src="partenaire.img"
          :alt="partenaire.alt"
          class="w-full object-contain sm:h-[107px] sm:w-[178px]"
        />
      </SwiperSlide>
      <div class="swiper-button-partners-prev"><ChevronLeftIcon /></div>
      <div class="swiper-button-partners-next"><ChevronLeftIcon /></div>
    </CarouselListSharedComponent>
  </div>
</template>
<script lang="ts" setup>
import { ref } from 'vue'

import { getImage } from '@/vuejs/services/utils'

import { SwiperSlide } from 'swiper/vue'
import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import ChevronLeftIcon from '@/vuejs/modules/shared/icon/ChevronLeftIconComponent.vue'

import imgPeugeot from '@/vuejs/assets/img/samples/peugeot.png'
import imgLoxam from '@/vuejs/assets/img/samples/loxam.png'
import imgAdecco from '@/vuejs/assets/img/samples/adecco.png'
import imgHertz from '@/vuejs/assets/img/samples/hertz.png'
import imgKiloutou from '@/vuejs/assets/img/samples/kiloutou.png'
import imgLdlc from '@/vuejs/assets/img/samples/ldlc.png'
import imgRenault from '@/vuejs/assets/img/samples/renault.png'
import imgWurth from '@/vuejs/assets/img/samples/wurth.png'

const partenaires = ref([
  { img: getImage(imgPeugeot), alt: 'Peugeot' },
  { img: getImage(imgRenault), alt: 'Renault' },
  { img: getImage(imgLoxam), alt: 'Loxam' },
  { img: getImage(imgKiloutou), alt: 'Kiloutou' },
  { img: getImage(imgAdecco), alt: 'Adecco' },
  { img: getImage(imgWurth), alt: 'Wurth' },
  { img: getImage(imgLdlc), alt: 'LDLC Pro' },
  { img: getImage(imgHertz), alt: 'Herts' },
])
</script>

<style lang="postcss" scoped>
.swiper {
  position: unset;
  &-button-partners {
    &-prev,
    &-next {
      @apply absolute top-0 bottom-0 z-10 flex cursor-pointer items-center text-primary;
      svg {
        @apply h-12 w-12;
      }
    }
    &-prev {
      @apply left-0 sm:-left-12;
    }
    &-next {
      @apply right-0 sm:-right-12;
      svg {
        @apply rotate-180;
      }
    }
  }
}
</style>
