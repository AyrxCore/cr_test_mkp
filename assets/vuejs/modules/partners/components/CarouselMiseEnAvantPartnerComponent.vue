<template>
  <div class="partner-carousel relative">
    <CarouselListSharedComponent
      class="mx-auto mt-10 items-center rounded-xl bg-white px-4 py-4 lg:h-[443px]"
      :slides-per-view="1"
      :space-between="10"
      :pagination="true"
      :breakpoints="{
            640: {
              slidesPerView: 1,
              spaceBetween: 10,
            },
          }"
    >
      <SwiperSlide v-for="i in 5" :key="i">
        <div class="mt-5 flex flex-col lg:flex-row">
          <div
            class="flex h-[217px!important] justify-center rounded-lg px-2 lg:ml-10
                lg:h-[374px!important] lg:w-1/2 lg:items-center lg:border lg:px-0 lg:py-8.5"
          >
            <img :src="aldaImg" alt="Picture" class="object-cover" />
          </div>
          <div
            class="mt-5 flex flex-col rounded-lg bg-white p-5 text-lg text-gray-500 lg:mt-0 lg:w-1/2 lg:pr-12"
          >
            <h3
              class="text-title-35 mb-[1.563rem] font-bold leading-9 text-primary lg:w-3/4"
            >
              Mise en avant partenaire
            </h3>
            <p class="mb-5 text-sm md:text-base xl:text-lg">
              Tout ce dont vous avez besoin auprès d’un seul fournisseur. Un
              groupe d’envergure à taille humaine spécialisé dans la
              distribution de fournitures de bureau, papier, consommables
              informatiques, ergonomie au travail et beaux-arts. Mais aussi
              des offres exclusives de produits vert et fabrication
              française à votre service.
            </p>

            <!--                <ButtonComponent class="button-secondary mt-6 w-1/2 hidden">-->
            <!--                  Bouton CTA-->
            <!--                </ButtonComponent>-->
          </div>
        </div>
      </SwiperSlide>
    </CarouselListSharedComponent>
  </div>
</template>
<script lang="ts" setup>
import { getImage } from '@/vuejs/services/utils'
import { SwiperSlide } from 'swiper/vue'
import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import alda from '@/vuejs/assets/img/demo/alda-mise-en-avant.jpg'

const aldaImg = getImage(alda)


</script>

<style scoped></style>
