<template>
  <BaseTemplate title="Page d'accueil" class="ff-roboto">
    <div class="m-auto mt-4 flex-1 xl:!pt-5">
      <div class="m-auto max-w-screen-90">
        <CarouselActualitesComponent />
      </div>

      <!-- Bloc accords cadre -->
      <AccordsCadreComponent />
      <!-- Fin Bloc accords cadre -->

      <!-- Bloc sélection de produits -->
      <div class="m-auto mt-4 max-w-screen-94">
        <div class="mt-10 sm:w-[45rem]">
          <h3 class="home-title mb-3 text-primary">
            Notre sélection de produits
          </h3>
          <p class="text-lg">
            Nous vous proposons une sélection de produits pour répondre à vos
            besoins.
          </p>
        </div>
        <div class="m-auto max-w-screen-94">
          <ProductsLoadingCarouselComponent
            v-if="!productsSelection?.results"
          />
          <ProductsCarouselComponent
            v-else
            :products="productsSelection?.results"
            @click-left="sendGtmEvent('click_slider_home_products_left')"
            @click-right="sendGtmEvent('click_slider_home_products_right')"
            @click-add-cart="
              sendGtmEvent('click_slider_home_products_cta', $event)
            "
            @click-title="
              sendGtmEvent('click_slider_home_products_title', $event)
            "
            @click-img="sendGtmEvent('click_slider_home_products_img', $event)"
          />
        </div>
      </div>
      <!-- Fin bloc sélection de produits -->

      <div
        v-if="
          channelStore.isAllowedToShow(
            OPTIONAL_FRONT_BLOCKS.SUPPLIER_PARTNERS_HOMEPAGE_QANTIS,
          )
        "
        class="m-auto max-w-screen-94"
      >
        <div class="mt-10">
          <h3 class="home-title text-primary">Nos partenaires fournisseurs</h3>
          <p class="text-sm sm:text-lg">
            Plus de 200 partenaires fournisseurs, répartis en 26 catégories,
            sont référencés pour vos achats.
            <RouterLink
              :to="{ name: ProductPageList.CATEGORIES }"
              class="font-normal underline"
            >
              Découvrir toutes les catégories d'achats
            </RouterLink>
          </p>
        </div>
        <PartnersCarousel class="mt-5" />
      </div>

      <template
        v-if="
          expertContents.length &&
          channelStore.isAllowedToShow(
            OPTIONAL_FRONT_BLOCKS.EXPERT_CONTENT_HOMEPAGE_QANTIS,
          )
        "
      >
        <div class="m-auto max-w-screen-94">
          <div class="mt-10">
            <h3 class="home-title text-primary">
              Contenus experts spécialement conçus pour la communauté QANTIS
            </h3>
          </div>
          <div class="m-auto max-w-screen-94">
            <ExpertContentsComponent :contents="expertContents" />
          </div>
          <div class="flex justify-center">
            <p class="md:mt-10">
              <RouterLink
                :to="{ name: NewsPageList.NEWS }"
                class="button button-primary"
                :style="{
                  color: betterTextColor('primary'),
                }"
              >
                Tous nos contenus experts
              </RouterLink>
            </p>
          </div>
        </div>
      </template>
      <OurCategoriesComponent />
    </div>

    <div
      v-if="
        channelStore.isAllowedToShow(OPTIONAL_FRONT_BLOCKS.RSE_HOMEPAGE_QANTIS)
      "
      class="text-cotext relative m-auto flex-1 bg-secondary py-14 text-white"
    >
      <div class="px-5 text-center">
        <h3
          class="text-[23px] font-bold leading-[27px] sm:text-3xl sm:leading-[38.11px]"
          :style="{
            color: betterTextColor('secondary'),
          }"
        >
          Vous faites des économies tout <br />
          en contribuant à votre démarche RSE
        </h3>
        <p
          class="mt-8 text-lg sm:mx-auto sm:text-base xl:w-[40%]"
          :style="{
            color: betterTextColor('secondary'),
          }"
        >
          Nos adhérents réalisent en moyenne 27 % d'économies, grâce à la
          mutualisation des achats. Nous notons et référençons nos partenaires
          fournisseurs à l'aide d'un référentiel RSE. Votre adhésion permet
          aussi de contribuer à la démarche RSE de votre entreprise.
        </p>
      </div>
      <div class="absolute right-[5%] bottom-0">
        <QantisIconComponent />
      </div>
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import { computed, onBeforeMount, onMounted, ref } from 'vue'
import { storeToRefs } from 'pinia'

import AccordsCadreComponent from '@/vuejs/modules/home/component/AccordsCadreComponent.vue'
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import CarouselActualitesComponent from '@/vuejs/modules/home/component/CarouselActualitesComponent.vue'
import ExpertContentsComponent from '@/vuejs/modules/home/component/ExpertContentsComponent.vue'
import OurCategoriesComponent from '@/vuejs/modules/home/component/OurCategoriesComponent.vue'
import PartnersCarousel from '@/vuejs/modules/shared/PartnersCarouselComponent.vue'
import QantisIconComponent from '@/vuejs/modules/shared/icon/QantisIconComponent.vue'

import { OPTIONAL_FRONT_BLOCKS } from '@/vuejs/services/const'
import { betterTextColor } from '@/vuejs/services/utils'
import { NewsPageList, ProductPageList } from '@/vuejs/router/pages-list'
import { useExpertContentStore } from '@/vuejs/stores/expertContent'
import { useFavoriteStore } from '@/vuejs/stores/favorite'
import { useChannelStore } from '@/vuejs/stores/channel'
import { useProductStore } from '@/vuejs/stores/product'
import ProductsCarouselComponent from '@/vuejs/modules/shared/ProductsCarouselComponent.vue'
import ProductsLoadingCarouselComponent from '@/vuejs/modules/shared/ProductsLoadingCarouselComponent.vue'
import { sendGtmEvent } from '@/vuejs/services/gtm'

const favoriteStore = useFavoriteStore()
const productStore = useProductStore()
const expertContentStore = useExpertContentStore()
const channelStore = useChannelStore()

const { productsSelection } = storeToRefs(productStore)
const expertContentsLoaded = ref<boolean>(false)

onBeforeMount(async () => {
  await Promise.all([
    productStore.initHomeProducts(),
    expertContentStore.init(),
    favoriteStore.fetchFavorites(),
  ])
})

onMounted(async () => {
  expertContentsLoaded.value = true
})

const expertContents = computed(() => {
  return expertContentStore.expertContents
})
</script>

<style lang="scss">
.home-title {
  @apply text-left font-cotext text-[23px] font-bold leading-[27px] sm:text-[29px] sm:leading-[33px] xl:text-[24px] xl:leading-[38.11px];
}

.list-categories {
  @apply h-[366px] flex-wrap
  overflow-auto
  rounded-lg bg-white p-1
  text-left
  text-gray-700
  xl:relative xl:mt-2 xl:flex xl:h-auto
  xl:overflow-auto xl:bg-transparent xl:p-0 xl:text-center;
}

.list-categories-items {
  @apply my-2 items-center
  border-2 border-solid border-primary
  bg-white text-sm text-primary
  sm:text-base xl:mx-4
  xl:justify-center xl:rounded xl:px-2.5
  xl:py-1.5 xl:text-lg;
}

.dropdown:hover .dropdown-menu {
  @apply block xl:flex;
}
</style>
