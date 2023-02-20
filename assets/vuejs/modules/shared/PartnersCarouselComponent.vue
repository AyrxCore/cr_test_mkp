<template>
  <div class="relative">
    <CarouselListSharedComponent
      :slides-per-view="2"
      :space-between="5"
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
        v-for="(seller, key) in sellers"
        :key="key"
        class="flex h-full items-center justify-center overflow-hidden rounded-lg bg-white"
      >
        <RouterLink
          v-if="seller.id"
          :to="{ path: `${PartnersPageList.LISTE_PRODUITS_PARTENAIRE}/${seller.id}` }"
          class="cursor-pointer pointer"
        >
          <img
            :src="getUpplerImage(seller.avatar)"
            :alt="seller.name"
            class="h-[107px] w-full object-contain cursor-pointer pointer"
          />
        </RouterLink>
        <img
          v-else
          :src="seller.avatar"
          :alt="seller.name"
          class="h-[107px] w-full object-contain"
        />

      </SwiperSlide>
    </CarouselListSharedComponent>
  </div>
</template>
<script lang="ts" setup>
import { onMounted, ref } from 'vue'

import { getImage, getUpplerImage } from '@/vuejs/services/utils'

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
import imgCrit from '@/vuejs/assets/img/samples/crit.png'
import imgEdenred from '@/vuejs/assets/img/samples/edenred.png'
import imgEuromaster from '@/vuejs/assets/img/samples/euromaster.png'
import imgFiducial from '@/vuejs/assets/img/samples/fiducial.png'
import imgSfr from '@/vuejs/assets/img/samples/sfr.png'
import imgShell from '@/vuejs/assets/img/samples/shell.png'
import imgBerner from '@/vuejs/assets/img/samples/berner.png'
import imgSynergie from '@/vuejs/assets/img/samples/synergie.jpeg'
import { useSellerStore } from '@/vuejs/stores/seller'
import { useUserStore } from '@/vuejs/stores/user'
import { PartnersPageList } from '@/vuejs/modules/partners/routerPartners'

const sellerStore = useSellerStore()
const userStore = useUserStore()

const sellers = ref([])

onMounted(async () => {
  if (userStore.getUser) {
    sellers.value = await sellerStore.getSellers()
  } else {
    sellers.value = [
      { avatar: getImage(imgPeugeot), name: 'Peugeot' },
      { avatar: getImage(imgRenault), name: 'Renault' },
      { avatar: getImage(imgLoxam), name: 'Loxam' },
      { avatar: getImage(imgKiloutou), name: 'Kiloutou' },
      { avatar: getImage(imgAlda), name: 'Alda' },
      { avatar: getImage(imgAdecco), name: 'Adecco' },
      { avatar: getImage(imgWurth), name: 'Wurth' },
      { avatar: getImage(imgLdlc), name: 'LDLC Pro' },
      { avatar: getImage(imgHertz), name: 'Herts' },
      { avatar: getImage(imgEuromaster), name: 'Euromaster' },
      { avatar: getImage(imgShell), name: 'Shell' },
      { avatar: getImage(imgSfr), name: 'SFR' },
      { avatar: getImage(imgFiducial), name: 'Fiducial' },
      { avatar: getImage(imgCrit), name: 'Crit' },
      { avatar: getImage(imgBerner), name: 'Berner' },
      { avatar: getImage(imgEdenred), name: 'Edenred' },
      { avatar: getImage(imgSynergie), name: 'Synergie' },
    ]
  }
})
</script>
