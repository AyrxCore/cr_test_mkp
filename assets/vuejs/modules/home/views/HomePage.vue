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

      <CarouselActualitesComponent />

      <!-- Bloc Produits top ventes -->
      <ProductHomeComponent
        :products="productsTopVente"
        title="Top vente"
      />
      <!-- Fin bloc Produits top ventes -->

      <!-- Bloc accords cadre -->
      <AccordCadreComponent />
      <!-- Fin Bloc accords cadre -->

      <!-- Bloc sélection de produits -->
      <ProductHomeComponent
        class="mt-4"
        :products="productsSelection"
        title="Sélection de produits"
      />
      <!-- Fin bloc sélection de produits -->

      <div class="mt-10">
        <h3 class="home-subtitle text-primary">Nos partenaires fournisseurs</h3>
        <p class="text-sm text-gray-400 sm:text-lg">
          Plus de 200 partenaires fournisseurs, repartis en 26 catégories, sont
          référencés pour vos achats.
          <RouterLink
            :to="{ name: ProductPageList.CATEGORIES }"
            class="font-normal text-secondary underline"
          >
            Découvrir toutes les catégories d'achats
          </RouterLink>
        </p>
      </div>

      <PartnersCarousel class="mt-5" />
      <OurCategoriesComponent />
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
          <RouterLink
            :to="{ name: ProductPageList.CATEGORIES }"
            class="button button-gradient"
          >
            <ArrowRightIconComponent :stroke-color="'#FFFFFF'" />
            Toutes nos catégories d'achats
          </RouterLink>
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
import PartnersCarousel from '@/vuejs/modules/shared/PartnersCarouselComponent.vue'
import { onBeforeMount, onMounted, ref } from 'vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import ContenusExpertComponent from '@/vuejs/modules/home/component/ContenusExpertComponent.vue'
import ProductHomeComponent from '@/vuejs/modules/home/component/ProductHomeComponent.vue'
import AccordCadreComponent from '@/vuejs/modules/home/component/AccordsCadreComponent.vue'
import { useExpertContentStore } from '@/vuejs/stores/expertContent'
import { storeToRefs } from 'pinia'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import OurCategoriesComponent from '@/vuejs/modules/home/component/OurCategoriesComponent.vue'
import CarouselActualitesComponent from '@/vuejs/modules/home/component/CarouselActualitesComponent.vue'
import { ProductPageList } from '@/vuejs/router/pages-list'
import { useProductStore } from '@/vuejs/stores/product'

const productStore = useProductStore()
const { productsTopVente, productsSelection } = storeToRefs(productStore)
const expertContentStore = useExpertContentStore()
const { getExpertsContents } = storeToRefs(expertContentStore)

const expertsContentsLoaded = ref<boolean>(false)

onBeforeMount(async () => {
  await Promise.all([
    productStore.initHomeProducts(),
    expertContentStore.init()
  ])
})

onMounted(async () => {
  expertsContentsLoaded.value = true
})
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
