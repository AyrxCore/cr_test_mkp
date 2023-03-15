<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div class="m-auto my-4 max-w-screen-2xl flex-1 px-5 xl:p-8">
      <div class="mt-7 flex sm:justify-between lg:mt-0">
        <div class="sm:w-[100%] md:w-[52rem]">
          <h3 class="home-title text-primary">
            Bienvenue sur la
            <span class="text-gradient"> marketplace réservée</span>
            à nos 30 000 entreprises adhérentes
          </h3>
          <p
            class="mt-2.5 text-sm text-gray-400 sm:mt-5 sm:text-base xl:text-lg"
          >
            Cher adhérent, ce nouvel espace dédié vous permet d'acheter
            directement en ligne et de trouver vos accords-cadres, en quelques
            clics. Notre équipe, que vous connaissez, se tient à votre
            disposition pour répondre à toutes vos questions.
          </p>
        </div>
        <div class="hidden xl:block">
          <ContactUsButtonComponent />
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

      <!-- Bloc Produits top ventes -->
      <ProductHomeComponent type="top-vente" title="Top vente" />
      <!-- Fin bloc Produits top ventes -->

      <!-- Bloc accords cadre -->
      <AccordCadreComponent />
      <!-- Fin Bloc accords cadre -->

      <!-- Bloc sélection de produits -->
      <ProductHomeComponent
        class="mt-4"
        type="selection"
        title="Sélection de produits"
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
      <div class="mt-5 flex w-full text-lg sm:flex">
        <DropdownListComponent>
          <template #button-label> Toutes les catégories</template>
          <template #content>
            <div class="list-categories">
              <div
                v-for="(categorie, id) in listCategories"
                :key="id"
                class="list-categories-items"
              >
                <a href="#">
                  {{ categorie }}
                </a>
              </div>
            </div>
          </template>
        </DropdownListComponent>
      </div>
    </div>

    <div
      class="home-bloc-economie text-cotext m-auto mt-16 flex-1 py-4 text-white"
    >
      <div class="px-5 text-left sm:text-center">
        <h3
          class="text-[23px] font-bold leading-[27px] sm:text-[35px] sm:leading-[38.11px]"
        >
          Vous faites des économies tout en <br />
          contribuant à votre démarche RSE
        </h3>
        <p class="mt-2 text-sm sm:mx-auto sm:text-base xl:w-[45%] xl:text-lg">
          Nos adhérents réalisent en moyenne 27 % d'économies, grâce à la
          mutualisation des achats. Nous notons et référençons nos partenaires
          fournisseurs à l'aide d'un référentiel RSE. Votre adhésion permet
          aussi de contribuer à la démarche RSE de votre entreprise.
        </p>
        <p class="mt-10 flex justify-center">
          <a href="#" class="button button-gradient">
            <ArrowRightIconComponent :stroke-color="'#FFFFFF'" />
            Toutes nos catégories d'achats
          </a>
        </p>
      </div>
    </div>

    <template v-if="getExpertsContents.length">
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
          <ContenusExpertComponent />
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
    </template>
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
import PartnersCarousel from '@/vuejs/modules/shared/PartnersCarouselComponent.vue'
import { onMounted, ref } from 'vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import ContenusExpertComponent from '@/vuejs/modules/home/component/ContenusExpertComponent.vue'
import DropdownListComponent from '@/vuejs/modules/shared/DropdownListComponent.vue'
import ProductHomeComponent from '@/vuejs/modules/home/component/ProductHomeComponent.vue'
import AccordCadreComponent from '@/vuejs/modules/home/component/AccordsCadreComponent.vue'
import { useExpertContentStore } from '@/vuejs/stores/expertContent'
import { storeToRefs } from 'pinia'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'

const expertContentStore = useExpertContentStore()
const { getExpertsContents } = storeToRefs(expertContentStore)

const expertsContentsLoaded = ref<boolean>(false)

onMounted(async () => {
  await expertContentStore.init()
  expertsContentsLoaded.value = true
})

const bannieres = ref([
  { image: getImage(banniere1), image_mobile: getImage(banniereMobile1) },
  { image: getImage(banniere2), image_mobile: getImage(banniereMobile2) },
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

.list-categories {
  @apply h-[366px] flex-wrap
  justify-center
  overflow-auto
  rounded-lg bg-white p-1
  text-left
  text-gray-700
  xl:relative xl:mt-2 xl:flex xl:h-auto
  xl:overflow-auto xl:bg-transparent xl:p-0 xl:text-center;
}

.list-categories-items {
  @apply mt-2 items-center bg-white text-sm text-primary sm:text-base xl:mx-4  xl:justify-center xl:rounded xl:bg-[#404FE6] xl:px-2.5 xl:py-1.5 xl:text-lg xl:text-white;
}

.dropdown:hover .dropdown-menu {
  @apply block xl:flex;
}
</style>
