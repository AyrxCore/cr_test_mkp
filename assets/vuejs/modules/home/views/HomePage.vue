<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div class="m-auto my-4 max-w-screen-2xl flex-1 px-5 md:p-8">
      <div class="mt-7 flex md:justify-between">
        <div class="sm:w-[100%] md:w-[52rem]">
          <h3 class="home-title text-primary">
            Bienvenue sur la
            <span class="text-gradient"> marketplace réservée</span>
            à nos 30 000 entreprises adhérentes
          </h3>
          <p class="mt-2.5 text-sm text-gray-400 md:mt-5 md:text-lg">
            Cher adhérent, ce nouvel espace dédié vous permet d'acheter
            directement en ligne et de trouver vos accords-cadres, en quelques
            clics. Notre équipe, que vous connaissez, se tient à votre
            disposition pour répondre à toutes vos questions.
          </p>
        </div>
        <div class="hidden md:block">
          <ButtonComponent class="button-secondary">
            <MailIconLightComponent />
            <PhoneLightIconComponent />
            Contactez-nous
          </ButtonComponent>
        </div>
      </div>
      <div class="relative mt-2">
        <CarouselListSharedComponent
          :slides-per-view="1"
          :space-between="20"
          :breakpoints="{
            640: {
              slidesPerView: 1,
              spaceBetween: 20,
            },
          }"
        >
          <SwiperSlide
            v-for="(banniere, key) in bannieres"
            :key="key"
            class="flex h-full items-center justify-center overflow-hidden rounded-lg bg-white"
          >
            <img
              :src="banniere.image"
              alt="Picture"
              class="mx-auto items-center"
            />
          </SwiperSlide>
        </CarouselListSharedComponent>
      </div>
      <div class="mt-10 md:w-[45rem]">
        <h3 class="home-subtitle text-primary">Top ventes</h3>
        <p class="text-sm text-gray-400 md:text-lg">
          D'autres adhérents ont déjà acheté ces produits
        </p>
      </div>
      <ProductsCarouselComponent :products="productsTopVenteHomepage" />

      <div class="mt-10">
        <h3 class="home-subtitle text-primary">
          Les accords-cadres incontournables
        </h3>
        <p class="text-sm text-gray-400 md:text-lg">
          Etes-vous certains de profiter des touts les économies incluses dans
          votre adhésion ?
        </p>
      </div>
      <div class="relative mt-5">
        <CarouselListSharedComponent
          :slides-per-view="1"
          :space-between="20"
          :breakpoints="{
            640: {
              slidesPerView: 4,
              spaceBetween: 20,
            },
          }"
        >
          <SwiperSlide
            v-for="(accord, key) in accords"
            :key="key"
            class="flex h-full items-center justify-center overflow-hidden rounded-lg bg-white"
          >
            <div class="flex h-full flex-col rounded-md bg-primary p-8">
              <div class="mx-auto">
                <img :src="accord.img" alt="Image produit" />
              </div>
              <p class="my-8 text-sm font-normal text-white md:text-lg">
                Découvrez ou téléchargez les conditions négociées de ce
                partenaire
              </p>


              <RouterLink
                :to="{ path: '/app/partner' }"
                class="button button-white button-white-primary flex justify-center"
              >
                <ArrowRightIconComponent />Découvrir l'accord cadre
              </RouterLink>
            </div>
          </SwiperSlide>
        </CarouselListSharedComponent>
      </div>

      <!-- Bloc sélection de produits -->
      <div class="mt-10 md:w-[45rem]">
        <h3 class="home-subtitle text-primary">Une sélection de produits</h3>
        <p class="text-sm text-gray-400 md:text-lg">
          Savez-vous que vous pouvez désormais acheter ces produits en quelques
          clics ?
        </p>
      </div>

      <ProductsCarouselComponent
        :products="productsSelectionHomepage"
        class="mt-4"
      />
      <!-- Fin bloc sélection de produits -->
      <div class="mt-10">
        <h3 class="home-subtitle text-primary">Nos partenaires fournisseurs</h3>
        <p class="text-sm text-gray-400 md:text-lg">
          Plus de 200 partenaires fournisseurs, repartis en 26 catégories, sont
          référencés pour vos achats.
          <a href="#" class="font-normal text-secondary underline"
            >Découvrir toutes les catégories d'achats</a
          >
        </p>
      </div>

      <PartnersCarousel class="mt-5" />

      <div class="mt-16 text-center">
        <h3 class="home-subtitle font-bold text-primary">
          Nos catégories de produits et d'accords-cadres
        </h3>
      </div>
      <div
        class="mt-5 flex hidden w-[100%] flex-wrap justify-center text-center text-lg md:flex"
      >
        <div
          v-for="(categorie, id) in listCategories"
          :key="id"
          class="mr-8 mt-2 items-center justify-center rounded bg-[#404FE6] px-1.5 py-1 text-white"
        >
          {{ categorie }}
        </div>
      </div>
    </div>

    <div
      class="home-bloc-economie text-cotext m-auto mt-16 flex-1 py-4 text-white"
    >
      <div class="px-5 text-left md:text-center">
        <h3
          class="text-[23px] font-bold font-bold leading-[27px] md:text-[35px] md:leading-[38.11px]"
        >
          Vous faites des économies tout en <br />
          contribuant à votre démarche RSE
        </h3>
        <p class="mt-2 text-sm md:mx-auto md:w-[45%] md:text-lg">
          Nos adhérents réalisent en moyenne 27 % d'économies, grâce à la
          mutualisation des achats. Nous notons et référençons nos partenaires
          fournisseurs à l'aide d'un référentiel RSE. Votre adhésion permet
          aussi de contribuer à la démarche RSE de votre entreprise.
        </p>
        <p class="mt-10 flex justify-center">
          <a href="#" class="button button-gradient">
            <ArrowRightIconComponent :stroke-color="'#FFFFFF'" /> Toutes nos
            catégories d'achats
          </a>
        </p>
      </div>
    </div>

    <div
      class="m-auto my-6 mt-10 max-w-screen-2xl flex-1 rounded-md bg-white pb-4 shadow-md"
    >
      <div class="flex flex-col pt-1 text-center">
        <h3 class="primary home-subtitle mt-10 flex flex-col pl-8 font-bold">
          <p class="flex">Contenus experts spécialement conçus</p>
          <p class="flex">
            pour la
            <span class="text-gradient ml-2"> communauté QANTIS </span>
          </p>
        </h3>
        <ContenusExpertComponent :contenus="contenusExpert" />
        <div class="flex justify-center">
          <p class="mt-10">
            <RouterLink
              :to="{ path: '/app/actualites' }"
              class="button button-gradient"
            >
              <ArrowRightIconComponent :stroke-color="'#FFFFFF'" />
              Tous les contenus experts
            </RouterLink>
          </p>
        </div>
      </div>
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import { getImage, listCategories } from '@/vuejs/services/utils'
import banniere1 from '@/vuejs/assets/img/demo/banniere-1-desktop.png'
import banniere2 from '@/vuejs/assets/img/demo/banniere-2-desktop.png'
import { SwiperSlide } from 'swiper/vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import {
  productsTopVenteHomepage,
  productsSelectionHomepage,
} from '@/vuejs/modules/products'
import PartnersCarousel from '@/vuejs/modules/shared/PartnersCarouselComponent.vue'

import { ref } from 'vue'

import imgPeugeot from '@/vuejs/assets/img/samples/peugeot.png'
import imgLoxam from '@/vuejs/assets/img/samples/loxam.png'
import imgBusiness from '@/vuejs/assets/img/samples/business.png'
import imgAdecco from '@/vuejs/assets/img/samples/adecco.png'
import imgMaps from '@/vuejs/assets/img/samples/img-maps.png'
import imgCentral from '@/vuejs/assets/img/samples/img-central.png'
import imgMagazine from '@/vuejs/assets/img/samples/img-magazine.png'
import MailIconLightComponent from '@/vuejs/modules/shared/icon/MailIconLightComponent.vue'
import PhoneLightIconComponent from '@/vuejs/modules/shared/icon/PhoneLightIconComponent.vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import ProductsCarouselComponent from '@/vuejs/modules/shared/ProductsCarouselComponent.vue'
import ContenusExpertComponent from '@/vuejs/modules/home/component/ContenusExpertComponent.vue'

const bannieres = ref([
  { image: getImage(banniere1) },
  { image: getImage(banniere2) },
])

const accords = ref([
  { img: getImage(imgPeugeot), alt: 'Peugeot' },
  { img: getImage(imgLoxam), alt: 'Loxam' },
  { img: getImage(imgBusiness), alt: 'Business' },
  { img: getImage(imgAdecco), alt: 'Adecco' },
])

const contenusExpert = ref([
  {
    img: getImage(imgMaps),
    titre: 'Loi montagne : êtes-vous concernés ? ',
    btnNam: 'Actualités',
    desc: 'A partir du 1er novembre 2022, la loi Montagne...',
  },
  {
    img: getImage(imgCentral),
    titre: 'Tout savoir sur le décret tertiaire',
    btnNam: 'Actualités',
    desc: 'Le décret tertiaire impose une réduction \nde consommation...',
  },
  {
    img: getImage(imgMagazine),
    titre: 'Comment entretenir votre véhicule ? ',
    btnNam: 'Conseil',
    desc: 'Les équipes de QANTIS ont conçu pour vous un guide...',
  },
])
</script>

<style lang="scss">
@import 'assets/style/_variables.scss';

.home-bloc-economie {
  background: linear-gradient(87.3deg, #050056 0%, #404fe6 100%);
  width: 100%;
}

.home-title {
  @apply text-left text-[28px] font-bold leading-[33px] md:text-[42px] md:leading-[49px];
}

.home-subtitle {
  @apply text-left text-[23px] font-bold leading-[27px] md:text-[35px] md:leading-[38.11px];
}
</style>
