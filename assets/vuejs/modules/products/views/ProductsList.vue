<template>
  <BaseTemplate :title="resultsHeadlineLine" class="ff-roboto">
    <div
      v-if="isLoading"
      class="my-20 flex h-20 w-full flex-col items-center justify-center text-primary"
    >
      <LoadingComponent />
    </div>
    <div v-else class="mx-auto my-4 w-full min-w-0 overflow-x-hidden px-2 min-[375px]:px-4 md:px-8 lg:px-10 xl:px-12">
      <BreadcrumbSharedComponent :list-url="[]" current-page="Page de résultat" />

      <div
        v-if="resultNotFound || (count === 0 && internalAccordCadres.length === 0)"
        class="my-12 w-full rounded-md border p-2 text-center text-gray-500"
      >
        {{ emptyResultsMessage }}
      </div>

      <div v-else class="flex w-full flex-col">
        <h3 class="md:text-title-default-size mt-1 text-xl text-primary md:hidden">
          {{ resultsHeadlineLine }}
        </h3>
        <MobileFiltersProductsComponent />

        <div class="my-2 flex min-w-0 flex-col justify-between overflow-x-hidden lg:flex-row">
          <div class="hidden w-full max-w-[300px] lg:mr-6 lg:block xl:mr-8">
            <FiltersProductComponent
              v-if="productStore.products.filters"
              class="w-full"
            />
          </div>

          <div class="flex min-w-0 w-full grow flex-col overflow-x-hidden">
            <h3
              class="mb-2 hidden text-xl text-primary md:block"
            >
              {{ resultsHeadlineLine }}
            </h3>

            <AccordCadresContainer
              v-if="internalAccordCadres.length > 0"
              :key="`fat-${searchId}`"
              :accord-cadres="internalAccordCadres"
              :count="accordCadresCount"
              :default-expanded="internalProducts.length === 0"
              @show-showcase-modal="handleShowcaseModal"
            />

            <ProductsContainer
              v-if="internalProducts.length > 0"
              :products="internalProducts"
              :count="count"
              :has-more="count > currentCount"
              :load-more-loading="loadMoreLoading"
              :banner-search-img="bannerSearchImg"
              :banner-search-link="bannerSearchLink"
              :allow-banner-search="allowBannerSearch"
              @load-more="loadMore"
              @banner-click="handleBannerClick"
            />
          </div>
        </div>
      </div>
    </div>

    <ShowcaseModal
      v-if="showShowcaseModal"
      :accord="accordSelected"
      class="modal"
      @cancel="showShowcaseModal = false"
    />
  </BaseTemplate>
</template>

<script lang="ts" setup>
import { ref } from 'vue'

import { useProductStore } from '@/vuejs/stores/product'
import { useProductsList } from '@/vuejs/modules/products/composables/useProductsList'
import { sendGtmEvent } from '@/vuejs/services/gtm'
import router from '@/vuejs/router'
import { Product } from '@/vuejs/types/Product'

import AccordCadresContainer from '@/vuejs/modules/products/components/AccordCadresContainer.vue'
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import FiltersProductComponent from '@/vuejs/modules/products/components/filters/FiltersProductComponent.vue'
import MobileFiltersProductsComponent from '@/vuejs/modules/products/components/filters/MobileFiltersProductsComponent.vue'
import ProductsContainer from '@/vuejs/modules/products/components/ProductsContainer.vue'
import ShowcaseModal from '@/vuejs/modules/home/component/ShowcaseModal.vue'
import LoadingComponent from '@/vuejs/modules/shared/LoadingComponent.vue'

const productStore = useProductStore()

const {
  isLoading,
  resultNotFound,
  loadMoreLoading,
  allowBannerSearch,
  internalProducts,
  internalAccordCadres,
  accordCadresCount,
  currentCount,
  count,
  resultsHeadlineLine,
  emptyResultsMessage,
  bannerSearchImg,
  bannerSearchLink,
  loadMore,
  searchId,
} = useProductsList()

const showShowcaseModal = ref(false)
const accordSelected = ref<Product>(null)

const handleShowcaseModal = (accord: Product) => {
  accordSelected.value = accord
  showShowcaseModal.value = true
}

const handleBannerClick = (link: string) => {
  sendGtmEvent('promotion_click', {
    link_url: link,
    origin_url: router.currentRoute.value.fullPath,
  })
}
</script>
