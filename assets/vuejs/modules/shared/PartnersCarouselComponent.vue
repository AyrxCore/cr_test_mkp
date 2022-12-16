<template>
  <div class="relative">
    <CarouselListSharedComponent
      :slides-per-view="1"
      :space-between="10"
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
    >
      <SwiperSlide
        v-for="(partenaire, key) in partenaires"
        :key="key"
        class="flex h-full items-center justify-center overflow-hidden rounded-lg bg-white"
      >
        <RouterLink :to="{ path: '/app/products-partner' }">
          <img
            :src="partenaire.img"
            :alt="partenaire.alt"
            class="w-full object-contain sm:h-[107px]"
          />
        </RouterLink>
      </SwiperSlide>
    </CarouselListSharedComponent>
  </div>
</template>
<script lang="ts" setup>
import { ref } from 'vue'

import { getImage } from '@/vuejs/services/utils'

import { SwiperSlide } from 'swiper/vue'
import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'

import imgPeugeot from '@/vuejs/assets/img/samples/peugeot.png'
import imgLoxam from '@/vuejs/assets/img/samples/loxam.png'
import imgAdecco from '@/vuejs/assets/img/samples/adecco.png'
import imgHertz from '@/vuejs/assets/img/samples/hertz.png'
import imgKiloutou from '@/vuejs/assets/img/samples/kiloutou.png'
import imgLdlc from '@/vuejs/assets/img/samples/ldlc.png'
import imgRenault from '@/vuejs/assets/img/samples/renault.png'
import imgWurth from '@/vuejs/assets/img/samples/wurth.png'
import imgAlda from '@/vuejs/assets/img/demo/alda.png'

const partenaires = ref([
  { img: getImage(imgPeugeot), alt: 'Peugeot' },
  { img: getImage(imgRenault), alt: 'Renault' },
  { img: getImage(imgLoxam), alt: 'Loxam' },
  { img: getImage(imgKiloutou), alt: 'Kiloutou' },
  { img: getImage(imgAlda), alt: 'Alda' },
  { img: getImage(imgAdecco), alt: 'Adecco' },
  { img: getImage(imgWurth), alt: 'Wurth' },
  { img: getImage(imgLdlc), alt: 'LDLC Pro' },
  { img: getImage(imgHertz), alt: 'Herts' },
])
</script>
