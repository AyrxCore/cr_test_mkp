<template>
  <BaseTemplate :title="headTitle" class="ff-roboto">
    <div
      v-if="isLoading"
      class="my-20 flex h-20 w-full flex-col items-center justify-center text-primary"
    >
      <LoadingComponent />
    </div>
    <div v-else class="mx-auto my-4 w-full px-4 md:px-8 lg:px-10 xl:px-12">
      <BreadcrumbSharedComponent
        :list-url="breadcrumbUrl"
        current-page="Page de résultat"
      />
      <div
        v-if="resultNotFound || count === 0 || internalProducts.length === 0"
        class="my-12 w-full rounded-md border p-2 text-center text-gray-500"
      >
        Aucun résultat n'a été trouvé pour la recherche ”{{ route.query.q }}“
      </div>
      <div v-else class="flex w-full flex-col">
        <h3
          class="md:text-title-default-size mt-4 text-xl text-primary md:hidden"
        >
          {{ route.query.q }}
        </h3>
        <MobileFiltersProductsComponent />
        <div class="my-2 flex flex-col justify-between lg:flex-row">
          <div class="hidden w-full max-w-[300px] lg:mr-6 lg:block xl:mr-8">
            <FiltersProductComponent
              v-if="productStore.products.filters"
              class="w-full"
            />
          </div>
          <div class="flex w-full grow-0 flex-col">
            <h3
              class="md:text-title-default-size mb-4 hidden text-xl text-primary md:block"
            >
              Votre recherche : {{ route.query.q }}
            </h3>
            <div
              class="flex h-auto flex-col items-stretch justify-items-center md:grid md:grid-cols-2 md:gap-5 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4"
            >
              <template v-for="product in internalProducts" :key="product.id">
                <AccordCadreComponent
                  v-if="
                    product.isAccordCadre &&
                    (currentPartenaire === null ||
                      currentPartenaire === product.seller.id)
                  "
                  :key="`ac-${product.id}`"
                  :accord="product"
                  class="mt-5 !h-full !w-full bg-white md:mt-0 md:max-w-[350px]"
                  @show-showcase-modal="handleShowcaseModal"
                />
                <ProductComponent
                  v-else-if="
                    currentPartenaire === product.seller.id ||
                    currentPartenaire === null
                  "
                  :key="`p-${product.id}`"
                  :product="product"
                  class="!h-auto !w-full md:max-w-[350px]"
                />
              </template>
            </div>
            <div
              class="mt-5 flex w-full flex-col items-center justify-center space-y-3"
            >
              <div class="flex justify-center">
                Résultats {{ count > currentCount ? currentCount : count }} sur
                {{ count }}
              </div>
              <ButtonComponent
                v-if="count > currentCount"
                class="button button-primary-outline md:w-1/2"
                @click="loadMore"
              >
                <LoaderSharedComponent v-if="loadMoreLoading" />
                <span v-else class="!text-lg">Charger plus de résultats</span>
              </ButtonComponent>
            </div>
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
import { computed, onBeforeMount, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { storeToRefs } from 'pinia'

import AccordCadreComponent from '@/vuejs/modules/home/component/AccordCadreComponent.vue'
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import FiltersProductComponent from '@/vuejs/modules/products/components/filters/FiltersProductComponent.vue'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import LoadingComponent from '@/vuejs/modules/shared/LoadingComponent.vue'
import MobileFiltersProductsComponent from '@/vuejs/modules/products/components/filters/MobileFiltersProductsComponent.vue'
import ProductComponent from '@/vuejs/modules/products/components/ProductCardComponent.vue'

import router from '@/vuejs/router'
import { ProductPageList } from '@/vuejs/router/pages-list'
import { Product } from '@/vuejs/types/Product'
import { useProductStore } from '@/vuejs/stores/product'
import { useFavoriteStore } from '@/vuejs/stores/favorite'
import { useUserStore } from '@/vuejs/stores/user'
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'
import ShowcaseModal from '@/vuejs/modules/home/component/ShowcaseModal.vue'

const route = useRoute()
const productStore = useProductStore()
const { adherentTarifShowcases } = storeToRefs(useUserStore())

const {
  products,
  selectedCategoryId,
  selectedSearchCategory,
  selectedProperties,
  selectedCompanyId,
  searchTerms,
} = storeToRefs(productStore)
const isLoading = ref<boolean>()
const currentPartenaire = ref<number>(null)
const perPage = ref<number>(36)
const currentCount = ref<number>(null)
const resultNotFound = ref<boolean>(false)
const favoriteStore = useFavoriteStore()
const breadcrumbUrl = computed(() => {
  return []
})
const internalProducts = ref<Array<Product>>([])
const loadMoreLoading = ref<boolean>(false)
const paramsProducts = ref(null)
const showShowcaseModal = ref<boolean>(false)
const accordSelected = ref<Product>(null)

onBeforeMount(async () => {
  await favoriteStore.fetchFavorites()
})

const handleShowcaseModal = (accord) => {
  accordSelected.value = accord
  showShowcaseModal.value = true
}

const loadMore = async () => {
  loadMoreLoading.value = true
  paramsProducts.value.page++
  await loadProducts(paramsProducts.value)
  currentCount.value += perPage.value
  loadMoreLoading.value = false
  sendGaEvent('click_resultats_more')
}

const loadProducts = async (paramsProducts: object) => {
  await productStore.fetchProductsByParams(paramsProducts)

  internalProducts.value.push(
    ...products.value.results.filter(
      (product) =>
        product.isAccordCadre ||
        !adherentTarifShowcases.value.some(
          (showcase) => showcase.accordId === product.properties['accord-id'],
        ),
    ),
  )

  if (route.query.q) {
    const eventLabel =
      products.value?.resultsCount > 0
        ? 'view_search_results'
        : 'no_search_results'
    await window.dataLayer?.push({
      event: eventLabel,
      search_term: route.query.q,
    })
  }
}

const count = computed((): number => {
  return products.value?.resultsCount
})

const headTitle = computed((): string => {
  if (searchTerms.value) {
    return searchTerms.value
  }
  if (selectedSearchCategory.value) {
    return selectedSearchCategory.value.name
  }

  return 'Recherche'
})

watch(
  () => [
    searchTerms.value,
    selectedCategoryId.value,
    selectedCompanyId.value,
    selectedProperties.value,
  ],
  () => {
    const queryValue = { ...route.query }
    searchTerms.value ? (queryValue.q = searchTerms.value) : delete queryValue.q
    selectedCategoryId.value
      ? (queryValue.category = selectedCategoryId.value)
      : delete queryValue.category
    selectedCompanyId.value
      ? (queryValue.company = selectedCompanyId.value)
      : delete queryValue.company

    if (selectedProperties.value) {
      queryValue.property_id = selectedProperties.value.property_id
      queryValue.value = selectedProperties.value.value
    } else {
      delete queryValue.property_id
      delete queryValue.value
    }

    router.replace({
      name: ProductPageList.PRODUCTS,
      query: queryValue,
    })
  },
)

watch(
  () => route.query,
  async (routeObject) => {
    isLoading.value = true
    currentPartenaire.value = null
    internalProducts.value = []

    paramsProducts.value = {
      page: 1,
      perPage: perPage.value,
      withFilters: true,
    }
    productStore.clearFilters()
    currentCount.value = perPage.value
    if (routeObject.q) {
      productStore.setSearchTerms(routeObject.q)
      paramsProducts.value.name = routeObject.q
    }

    if (routeObject.category) {
      productStore.setSelectedCategory(routeObject.category)
      paramsProducts.value.categories = routeObject.category
    }

    if (routeObject.company) {
      productStore.setSelectedCompany(routeObject.company)
      paramsProducts.value.companies = routeObject.company
    }

    if (routeObject.property_id && routeObject.value) {
      const properties = {
        property_id: routeObject.property_id,
        value: routeObject.value,
      }
      productStore.setSelectedProperty(properties)
      paramsProducts.value.properties = properties
    }
    try {
      await loadProducts(paramsProducts.value)
    } catch (e) {
      resultNotFound.value = true
    } finally {
      isLoading.value = false
    }
  },
  { immediate: true },
)
</script>
