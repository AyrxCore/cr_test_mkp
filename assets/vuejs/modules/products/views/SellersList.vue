<template>
  <BaseTemplate :title="title" class="ff-roboto">
    <div class="mx-auto my-4 w-full px-4 md:px-8 lg:px-10 xl:px-12">
      <BreadcrumbSharedComponent current-page="Liste des partenaires" />
      <div class="flex w-full flex-col">
        <div class="my-2 flex flex-col justify-between lg:flex-row">
          <div class="hidden w-full max-w-[300px] lg:mr-6 lg:block xl:mr-8">
            <FiltersSellerComponent
              v-if="categories && categories.length"
              class="w-full"
              :categories="categories"
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
            <div
              v-if="isFirstLoad"
              class="my-20 flex h-20 w-full flex-col items-center justify-center text-primary"
            >
              <LoadingComponent />
            </div>
            <div
              v-else-if="
                resultNotFound || sellersSortedAlphabetically.length === 0
              "
              class="my-12 w-full rounded-md border p-2 text-center text-gray-500"
            >
              Aucun partenaire trouvé
            </div>
            <template v-else>
              <div class="mb-4 flex flex-col">
                <h3 class="mb-2 text-2xl text-primary">
                  {{ title }}
                </h3>
                <p>
                  Retrouvez ici l'ensemble des partenaires référencés et leurs
                  conditions négociées
                </p>
              </div>
              <div class="lg:hidden">
                <RouterLink
                  :style="{
                    color: betterTextColor('primary'),
                  }"
                  :to="{ name: MainPageList.PARTNERS_MAP }"
                  class="my-2 flex w-full items-center justify-center gap-2 !rounded-none border border-gray-300 !bg-white px-4 py-2 !text-primary"
                >
                  <MarkerIconComponent size="30" />
                  <span class="text-lg">Carte des partenaires</span>
                </RouterLink>
              </div>
              <MobileFiltersSellersComponent v-if="categories" />
              <AlphabetNavigatorComponent
                :items="sellersSortedAlphabetically"
                @update:floatingStatus="handleFloatingStatus"
              />
              <div
                v-if="isRefreshing"
                class="my-20 flex h-20 w-full flex-col items-center justify-center text-primary"
              >
                <LoadingComponent />
              </div>
              <div
                v-else
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
            </template>
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
import { useCategoryStore } from '@/vuejs/stores/category'
import { Seller } from '@/vuejs/types/Seller'
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

const route = useRoute()
const sellerStore = useSellerStore()
const categoryStore = useCategoryStore()

const { categories } = storeToRefs(categoryStore)

const isFirstLoad = ref<boolean>(true)
const isRefreshing = ref<boolean>(false)
const paramsSellers = ref(null)
const resultNotFound = ref<boolean>(false)
const title = `Tous mes partenaires en un clin d'oeil`
const sellers = ref<Seller[]>([])
const isFloatingActive = ref<boolean>(false)

const sellersSortedAlphabetically = computed((): Array<Seller> => {
  if (!sellers.value) return []
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

    isRefreshing.value = true

    paramsSellers.value = {}

    try {
      if (routeObject.category) {
        paramsSellers.value = {
          categories: routeObject.category,
        }
      }

      sellers.value = await sellerStore.getSellersByParams(paramsSellers.value)
    } catch (e) {
      resultNotFound.value = true
    } finally {
      isFirstLoad.value = false
      isRefreshing.value = false
    }
  },
  { immediate: true },
)
</script>
