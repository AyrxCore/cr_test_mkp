<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div class="m-auto my-4 max-w-screen-2xl flex-1 px-5 xl:p-8">
      <div class="mt-7 flex sm:justify-between">
        <div class="sm:w-[100%] sm:w-[52rem]">
          <h3 class="home-title text-primary">
            Bienvenue sur la
            <span class="text-gradient"> marketplace réservée</span>
            à nos 30 000 entreprises adhérentes
          </h3>
          <p class="mt-2.5 text-sm text-gray-400 sm:mt-5 sm:text-base xl:text-lg">
            Cher adhérent, ce nouvel espace dédié vous permet d'acheter
            directement en ligne et de trouver vos accords-cadres, en quelques
            clics. Notre équipe, que vous connaissez, se tient à votre
            disposition pour répondre à toutes vos questions.
          </p>
        </div>
        <div class="hidden xl:block">
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
            class="flex h-[303px] items-center justify-center overflow-hidden rounded-lg bg-white xl:h-full"
          >
            <img
              :src="banniere.image_mobile"
              alt="Picture"
              class="flex w-full items-center md:hidden"
            />
            <img
              :src="banniere.image"
              alt="Picture"
              class="mx-auto hidden items-center md:flex"
            />
          </SwiperSlide>
        </CarouselListSharedComponent>
      </div>
      <div class="mt-10 sm:w-[45rem]">
        <h3 class="home-subtitle text-primary">Top ventes</h3>
        <p class="text-sm text-gray-400 sm:text-lg">
          D'autres adhérents ont déjà acheté ces produits
        </p>
      </div>
      <ProductsCarouselComponent :products="productsTopVenteHomepage" />

      <div class="mt-10">
        <h3 class="home-subtitle text-primary">
          Les accords-cadres incontournables
        </h3>
        <p class="text-sm text-gray-400 sm:text-lg">
          Etes-vous certains de profiter des touts les économies incluses dans
          votre adhésion ?
        </p>
      </div>
      <div class="relative mt-5">
        <CarouselListSharedComponent
          :slides-per-view="1"
          :space-between="20"
          :breakpoints="{
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
              <p class="my-8 text-sm font-normal text-white sm:text-lg">
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
      <div class="mt-10 sm:w-[45rem]">
        <h3 class="home-subtitle text-primary">Une sélection de produits</h3>
        <p class="text-sm text-gray-400 sm:text-lg">
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
        <p class="text-sm text-gray-400 sm:text-lg">
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
        class="mt-5 flex w-full text-lg sm:flex"
      >
        <div class="dropdown flex w-full sm:w-1/2 xl:w-auto">
          <button class="bg-white text-primary flex flex-row justify-between p-2.5 rounded items-center w-full xl:hidden">
            <span class="mr-1">Toutes les catégories</span>
            <ChevronDownIconComponent />
          </button>
          <ul class="dropdown-menu">
            <li
              v-for="(categorie, id) in listCategories"
              :key="id"
            >
              <a href="#">
                {{ categorie }}
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <div
      class="home-bloc-economie text-cotext m-auto mt-16 flex-1 py-4 text-white"
    >
      <div class="px-5 text-left sm:text-center">
        <h3
          class="text-[23px] font-bold font-bold leading-[27px] sm:text-[35px] sm:leading-[38.11px]"
        >
          Vous faites des économies tout en <br />
          contribuant à votre démarche RSE
        </h3>
        <p class="mt-2 text-sm sm:mx-auto xl:w-[45%] sm:text-base xl:text-lg">
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
      class="my-6 mx-4 mt-10 max-w-screen-2xl flex-1 rounded-md bg-white pb-4 shadow-md xl:mx-auto"
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
import banniereMobile1 from '@/vuejs/assets/img/demo/banniere-1-mobile.png'
import banniereMobile2 from '@/vuejs/assets/img/demo/banniere-2-mobile.png'
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
import ChevronDownIconComponent from '@/vuejs/modules/shared/icon/ChevronDownIconComponent.vue'

const bannieres = ref([
  { image: getImage(banniere1), image_mobile: getImage(banniereMobile1) },
  { image: getImage(banniere2), image_mobile: getImage(banniereMobile2) },
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
  @apply text-left text-[28px] font-bold leading-[33px] sm:text-[38px] sm:leading-[45px] xl:text-[42px] xl:leading-[49px];
}

.home-subtitle {
  @apply text-left text-[23px] font-bold leading-[27px] sm:text-[29px] sm:leading-[33px] xl:text-[35px] xl:leading-[38.11px];
}

.dropdown .dropdown-menu {
  @apply absolute hidden bg-white mt-14 px-2
  rounded-lg
  overflow-auto
  h-[366px] flex-wrap justify-center
  text-left
  xl:h-auto
  xl:px-0 xl:mt-2 xl:text-center xl:overflow-auto
  xl:bg-transparent text-gray-700 xl:flex xl:relative;
}

.dropdown .dropdown-menu li {
  @apply bg-white text-primary text-sm mt-2 items-center xl:justify-center xl:rounded  xl:px-2.5 xl:py-1.5 xl:text-white sm:text-base xl:text-lg xl:bg-[#404FE6] xl:mx-4;
}

.dropdown:hover .dropdown-menu {
  @apply block xl:flex ;
}

</style>
