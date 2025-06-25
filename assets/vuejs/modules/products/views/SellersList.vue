<template>
  <BaseTemplate :title="title" class="ff-roboto">
    <div
      v-if="sellersLoading"
      class="my-20 flex h-20 w-full flex-col items-center justify-center text-primary"
    >
      <LoadingComponent />
    </div>
    <div v-else class="mx-auto my-4 w-full px-4 md:px-8 lg:px-10 xl:px-12">
      <BreadcrumbSharedComponent current-page="Liste des partenaires" />
      <div
        v-if="resultNotFound || sellersSortedAlphabetically.length === 0"
        class="my-12 w-full rounded-md border p-2 text-center text-gray-500"
      >
        Aucun partenaire trouvé
      </div>
      <div v-else class="flex w-full flex-col">
        <div class="my-2 flex flex-col justify-between lg:flex-row">
          <div class="hidden w-full max-w-[300px] lg:mr-6 lg:block xl:mr-8">
            <FiltersSellerComponent
              v-if="categoriesFilters"
              class="w-full"
              :categories="categoriesFilters"
            />
            <p class="mt-8 text-center">
              <RouterLink
                :style="{
                  color: betterTextColor('primary'),
                }"
                :to="{ name: MainPageList.PARTNERS_MAP }"
                class="button button-primary !px-4"
              >
                <MarkerIconComponent size="30" />
                Carte des partenaires
              </RouterLink>
            </p>
          </div>

          <div class="flex w-full grow-0 flex-col">
            <div class="mb-4 flex flex-col">
              <h3 class="mb-2 text-2xl text-primary">
                {{ title }}
              </h3>
              <p>
                Retrouvez ici l'ensemble des partenaires référencés et leurs
                conditions négociées
              </p>
            </div>
            <MobileFiltersSellersComponent v-if="categoriesFilters" />
            <AlphabetNavigatorComponent
              :items="sellersSortedAlphabetically"
              @update:floatingStatus="handleFloatingStatus"
            />
            <div
              class="flex h-auto flex-col items-stretch justify-items-center sm:grid sm:grid-cols-2 md:grid-cols-3 md:gap-5 lg:grid-cols-4 2xl:grid-cols-5"
            >
              <template
                v-for="seller in sellersSortedAlphabetically"
                :key="seller.id"
              >
                <SellerCardComponent
                  :seller="seller"
                  :id="`letter-${seller.name.charAt(0).toUpperCase()}`"
                  class="mt-5 !h-full bg-white md:mt-0 md:!w-full md:max-w-[350px]"
                  :class="{ '!w-10/12': isFloatingActive }"
                  data-letter-section
                />
              </template>
            </div>
          </div>
        </div>
      </div>
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import { computed, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { storeToRefs } from 'pinia'

import { useSellerStore } from '@/vuejs/stores/seller'
import { useProductStore } from '@/vuejs/stores/product'
import { Seller } from '@/vuejs/types/Seller'
import { ProductCategory } from '@/vuejs/types/Product'
import { betterTextColor } from '@/vuejs/services/utils'

import { MainPageList } from '@/vuejs/router/pages-list'

import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import LoadingComponent from '@/vuejs/modules/shared/LoadingComponent.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import SellerCardComponent from '@/vuejs/modules/products/components/SellerCardComponent.vue'
import FiltersSellerComponent from '@/vuejs/modules/products/components/filters/FiltersSellerComponent.vue'
import MarkerIconComponent from '@/vuejs/modules/shared/icon/MarkerIconComponent.vue'
import MobileFiltersSellersComponent from '@/vuejs/modules/products/components/filters/MobileFiltersSellersComponent.vue'
import AlphabetNavigatorComponent from '@/vuejs/modules/shared/AlphabetNavigatorComponent.vue'

const sellerStore = useSellerStore()
const route = useRoute()
const productStore = useProductStore()
const { products } = storeToRefs(productStore)

const sellersLoading = ref<boolean>(false)
const paramsProducts = ref(null)
const paramsSellers = ref(null)
const resultNotFound = ref<boolean>(false)
const title = `Tous mes partenaires en un clin d'oeil`
const sellers = ref(null)
const isFloatingActive = ref<boolean>(false)

const categoriesFilters = computed((): Array<ProductCategory> => {
  return products.value?.filters?.categories
})

const sellersSortedAlphabetically = computed((): Array<Seller> => {
  return sellers.value.sort((a: Seller, b: Seller) => {
    return a.name.localeCompare(b.name)
  })
})

const handleFloatingStatus = (status: boolean) => {
  isFloatingActive.value = status
}

watch(
  () => route.query,
  async (routeObject) => {
    if (Object.keys(routeObject).length === 1 && routeObject.letter) return

    sellersLoading.value = true
    paramsProducts.value = {
      page: 1,
      withFilters: true,
    }
    paramsSellers.value = {}

    try {
      if (routeObject.category) {
        paramsSellers.value = {
          categories: [routeObject.category],
        }
        paramsProducts.value.categories = routeObject.category
      }
      await productStore.fetchProductsByParams(paramsProducts.value)

      sellers.value = await sellerStore.getSellersListing(paramsSellers.value)
    } catch (e) {
      resultNotFound.value = true
    } finally {
      sellersLoading.value = false
    }
  },
  { immediate: true },
)
</script>
