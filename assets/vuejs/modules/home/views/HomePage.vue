<template>
  <BaseTemplate title="Page d'accueil">
    <div class="m-auto mt-4 flex-1 xl:!pt-5">
      <div class="m-auto max-w-screen-90">
        <CarouselActualitesComponent />
      </div>

      <!-- Bloc accords cadre -->
      <div class="mt-10 bg-white px-6 py-8 md:px-12">
        <div class="m-auto max-w-screen-98">
          <h3 class="text-title-primary mb-3">
            Les accords-cadres incontournables
          </h3>
          <p class="text-sm sm:text-lg">
            Découvrez les partenaires et leurs conditions&nbsp;!
          </p>
        </div>
        <div class="m-auto mt-1 max-w-screen-94 md:mt-5">
          <AccordsCadreComponent
            :accords-cadres="productsAccordsCadre?.results"
            :loading="!productsAccordsCadre"
            @show-showcase-modal="handleShowcaseModal"
          />
          <ShowcaseModal
            v-if="showShowcaseModal"
            :accord="accordSelected"
            class="modal"
            @cancel="showShowcaseModal = false"
          />
        </div>
      </div>
      <!-- Fin Bloc accords cadre -->

      <!-- Bloc boutons sémantiques -->
      <SemanticButtonsComponent />
      <!-- Fin Bloc boutons sémantiques -->

      <!-- Bloc sélection de produits -->
      <div class="m-auto mt-4 max-w-screen-94 md:px-0">
        <div class="mt-10 sm:w-[45rem]">
          <h3 class="text-title-primary mb-3">Notre sélection de produits</h3>
          <p class="text-lg">
            Nous vous proposons une sélection de produits pour répondre à vos
            besoins.
          </p>
        </div>
        <div class="m-auto max-w-screen-94">
          <ProductsCarouselComponent
            :loading="!productsSelection"
            :products="filteredProductsSelection"
          />
        </div>
      </div>
      <div
        v-if="
          channelStore.isAllowedToShow(
            OPTIONAL_FRONT_BLOCKS.SUPPLIER_PARTNERS_HOMEPAGE,
          )
        "
        class="m-auto max-w-screen-94"
      >
        <div class="mt-10">
          <h3 class="text-title-primary">Nos partenaires fournisseurs</h3>
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
        <SellersCarousel class="mt-5" />
        <div
          class="mt-10 flex flex-col items-center justify-center md:flex-row"
        >
          <div>
            <RouterLink
              :style="{
                color: betterTextColor('primary'),
              }"
              :to="{ name: ProductPageList.SELLERS }"
              class="button button-primary"
            >
              Tous les partenaires
            </RouterLink>
          </div>
          <div>
            <RouterLink
              :to="{ name: MainPageList.PARTNERS_MAP }"
              class="button button-primary-outline ml-0 mt-4 flex items-center md:ml-4 md:mt-0"
            >
              <MarkerIconComponent class="mr-2" size="24" />
              Localiser les partenaires
            </RouterLink>
          </div>
        </div>
      </div>

      <template
        v-if="
          expertContents.length &&
          channelStore.isAllowedToShow(
            OPTIONAL_FRONT_BLOCKS.EXPERT_CONTENT_HOMEPAGE,
          )
        "
      >
        <div class="m-auto max-w-screen-94">
          <div class="mt-10">
            <h3 class="text-title-primary">
              Contenus experts spécialement conçus pour la communauté QANTIS
            </h3>
          </div>
          <div class="m-auto max-w-screen-94">
            <ExpertContentsComponent :contents="expertContents" />
          </div>
          <div class="flex justify-center">
            <p class="md:mt-10">
              <RouterLink
                :style="{
                  color: betterTextColor('primary'),
                }"
                :to="{ name: NewsPageList.NEWS }"
                class="button button-primary"
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
      v-if="channelStore.isAllowedToShow(OPTIONAL_FRONT_BLOCKS.RSE_HOMEPAGE)"
      class="text-cotext relative m-auto flex-1 bg-secondary py-14 text-white"
    >
      <div class="px-5 text-center">
        <h3
          :style="{
            color: betterTextColor('secondary'),
          }"
          class="sm:text-title-default-size text-[23px] font-bold leading-[27px] sm:leading-[38.11px]"
        >
          Vous faites des économies tout <br />
          en contribuant à votre démarche RSE
        </h3>
        <p
          :style="{
            color: betterTextColor('secondary'),
          }"
          class="mt-8 text-lg sm:mx-auto sm:text-base xl:w-[40%]"
        >
          Nos adhérents réalisent en moyenne 27 % d'économies, grâce à la
          mutualisation des achats. Nous notons et référençons nos partenaires
          fournisseurs à l'aide d'un référentiel RSE. Votre adhésion permet
          aussi de contribuer à la démarche RSE de votre entreprise.
        </p>
      </div>
    </div>
  </BaseTemplate>
</template>

<script lang="ts" setup>
import { computed, onBeforeMount, onMounted, ref } from 'vue'
import { storeToRefs } from 'pinia'

import { useChannelStore } from '@/vuejs/stores/channel'
import { useExpertContentStore } from '@/vuejs/stores/expertContent'
import { useUserStore } from '@/vuejs/stores/user'
import { useProductStore } from '@/vuejs/stores/product'
import { OPTIONAL_FRONT_BLOCKS } from '@/vuejs/services/const'
import { betterTextColor } from '@/vuejs/services/utils'
import {
  MainPageList,
  NewsPageList,
  ProductPageList,
} from '@/vuejs/router/pages-list'
import { Product } from '@/vuejs/types/Product'

import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import AccordsCadreComponent from '@/vuejs/modules/home/component/AccordsCadreComponent.vue'
import CarouselActualitesComponent from '@/vuejs/modules/home/component/CarouselActualitesComponent.vue'
import ExpertContentsComponent from '@/vuejs/modules/home/component/ExpertContentsComponent.vue'
import OurCategoriesComponent from '@/vuejs/modules/home/component/OurCategoriesComponent.vue'
import SellersCarousel from '@/vuejs/modules/shared/SellersCarouselComponent.vue'
import ProductsCarouselComponent from '@/vuejs/modules/shared/ProductsCarouselComponent.vue'
import ShowcaseModal from '@/vuejs/modules/home/component/ShowcaseModal.vue'
import SemanticButtonsComponent from '@/vuejs/modules/home/component/SemanticButtonsComponent.vue'
import MarkerIconComponent from '@/vuejs/modules/shared/icon/MarkerIconComponent.vue'

const productStore = useProductStore()
const expertContentStore = useExpertContentStore()
const channelStore = useChannelStore()

const { adherentTarifShowcases } = storeToRefs(useUserStore())
const { productsSelection } = storeToRefs(productStore)
const { productsAccordsCadre } = storeToRefs(productStore)

const expertContentsLoaded = ref<boolean>(false)
const showShowcaseModal = ref<boolean>(false)
const accordSelected = ref<Product>(null)

const emit = defineEmits(['show-showcase-modal', 'close-showcase-modal'])

onBeforeMount(async () => {
  const promises = []
  promises.push(productStore.initSliderProductsSelection())
  promises.push(productStore.initSliderAccordsCadres())
  if (
    channelStore.isAllowedToShow(OPTIONAL_FRONT_BLOCKS.EXPERT_CONTENT_HOMEPAGE)
  ) {
    promises.push(expertContentStore.init())
  }
  await Promise.all(promises)
})

onMounted(async () => {
  expertContentsLoaded.value = true
})

const expertContents = computed(() => {
  return expertContentStore.expertContents
})

const filteredProductsSelection = computed(() => {
  return productsSelection.value?.results.filter(
    (product) =>
      !adherentTarifShowcases.value.some(
        (showcase) => showcase.accordId === product.properties['accord-id'],
      ),
  )
})

const handleShowcaseModal = (accord) => {
  accordSelected.value = accord
  showShowcaseModal.value = true
}
</script>

<style lang="scss">
.list-categories {
  @apply mt-2
  flex h-auto w-full
  overflow-auto
  rounded-lg
  bg-transparent bg-white p-0 text-left
  text-gray-700 md:flex-wrap;
}

.list-categories-items {
  @apply mx-4 my-2 items-center
  justify-center text-nowrap rounded
  border-2 border-solid
  border-primary bg-white
  px-2.5 py-1.5 text-lg
  text-primary sm:text-base;
}

.dropdown:hover .dropdown-menu {
  @apply block xl:flex;
}
</style>
