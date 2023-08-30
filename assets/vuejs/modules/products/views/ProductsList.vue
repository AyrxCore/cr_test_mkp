<template>
  <BaseTemplate
    title="Résultat de recherche de produits | Qantis - MarketPlace"
  >
    <div
      v-if="isLoading"
      class="my-20 flex h-20 w-full flex-col items-center justify-center text-primary"
    >
      <LoadingComponent />
    </div>
    <div v-else class="m-auto my-4 w-full max-w-screen-2xl px-5 sm:px-8">
      <breadcrumb-shared-component
        :list-url="breadcrumbUrl"
        :current-page="`Listes des produits`"
      />
      <ContactUsButtonComponent />
      <div
        v-if="resultNotFound || count === 0 || products.length === 0"
        class="mt-10 mb-7.5 flex w-full flex-row items-center justify-between pt-5 text-[14px] text-gray-500"
      >
        <div
          class="mr-2 flex w-full flex-row items-start justify-center rounded-md border bg-white p-2"
        >
          Aucun résultat n'a été trouvé pour cette recherche
        </div>
      </div>
      <div v-else class="flex w-full flex-col items-center">
        <div
          class="flex w-full flex-col items-center justify-between pt-5 text-[14px] text-gray-500"
        >
          <div
            v-if="hasParameters"
            class="mr-2 flex w-full flex-row flex-col items-start rounded-md border bg-white p-2 lg:flex-row"
          >
            <div class="flex flex-col text-sm md:text-base lg:text-lg">
              Les résultats trouvés pour votre recherche
              <div class="flex">
                <div v-if="parameters.name" class="flex">
                  <SelectedFilterComponent
                    title="Mot clé"
                    :label="parameters.name"
                    @remove-filter="removeFilter(filterType.name)"
                  />
                </div>
                <div
                  v-if="parameters.companies.length"
                  class="mt-2 flex lg:mt-0"
                >
                  <SelectedFilterComponent
                    title="Partenaire"
                    :label="parameters.companies[0].name"
                    @remove-filter="removeFilter(filterType.company)"
                  />
                </div>
                <div
                  v-if="parameters.categories.length"
                  class="mt-2 flex lg:mt-0"
                >
                  <SelectedFilterComponent
                    title="Catégorie"
                    :label="parameters.categories[0].name.default"
                    @remove-filter="removeFilter(filterType.category)"
                  />
                </div>
                <div
                  v-if="parameters.properties.length"
                  class="mt-2 flex lg:mt-0"
                >
                  <SelectedFilterComponent
                    title="Propriété"
                    :property="
                      findPropertyData(
                        parameters.properties[0].property,
                        parameters.properties[0].value,
                      )
                    "
                    @remove-filter="removeFilter(filterType.property)"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div
          class="flex h-[50%] flex-col gap-4 text-gray-600 xl:grid xl:grid-cols-4"
        >
          <FiltersComponent
            v-if="filters"
            :filters="filters"
            @filter-product="filterProduct"
          />
          <div
            class="col-span-3 flex flex-col rounded-lg pb-4 text-gray-500 md:mt-5"
          >
            <div class="">
              <div
                class="flex flex-col text-gray-600 md:grid md:grid-cols-2 md:gap-8 lg:grid-cols-3"
              >
                <div v-for="product in products" :key="product.id">
                  <AccordCadreComponent
                    v-if="
                      product.isAccordCadre &&
                      (currentPartenaire === null ||
                        currentPartenaire === product.seller.id)
                    "
                    :accord="product"
                    :key="`ac-${product.id}`"
                    class="mt-5 md:mt-0"
                  />
                  <ProductComponent
                    v-else-if="
                      currentPartenaire === product.seller.id ||
                      currentPartenaire === null
                    "
                    :product="product"
                    :key="`p-{product.id}`"
                    class="mt-5 h-[516px] !w-auto md:mt-0 md:w-[392px]"
                  />
                </div>
              </div>
            </div>
            <div
              class="mt-5 flex w-full flex-col items-center justify-center space-y-3"
            >
              <div class="flex justify-center">
                Résultats {{ count > currentCount ? currentCount : count }} sur
                {{ count }}
              </div>
              <button
                v-if="count > currentCount"
                class="button w-1/2 border-2 border-secondary !text-secondary hover:!bg-white focus:!bg-white"
                @click="loadMore"
              >
                <LoaderSharedComponent v-if="loadMoreLoading" />
                <span v-else class="!text-lg"> Chargez plus </span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import ProductComponent from '@/vuejs/modules/products/components/ProductComponent.vue'
import { useRoute } from 'vue-router'
import { computed, onBeforeMount, ref, watch } from 'vue'
import { useProductStore } from '@/vuejs/stores/product'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import AccordCadreComponent from '@/vuejs/modules/home/component/AccordCadreComponent.vue'
import router from '@/vuejs/router'
import { ProductPageList } from '@/vuejs/router/pages-list'
import FiltersComponent from '@/vuejs/modules/products/components/FiltersComponent.vue'
import SelectedFilterComponent from '@/vuejs/modules/products/components/filters/SelectedFilterComponent.vue'
import { storeToRefs } from 'pinia'
import { filterType } from '@/vuejs/modules/products'
import { useFavoriteStore } from '@/vuejs/stores/favorite'
import LoadingComponent from '@/vuejs/modules/shared/LoadingComponent.vue'
import { Product } from '@/vuejs/types/Product'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'

const route = useRoute()
const productStore = useProductStore()
const { selectedCategoryId, selectedProperties, selectedCompanyId } =
  storeToRefs(productStore)
const resultProducts = ref([])
const isLoading = ref<boolean>()
const term = ref<string>('')
const currentPartenaire = ref<number>(null)
const perPage = ref<number>(30)
const currentCount = ref<number>(null)
const resultNotFound = ref<boolean>(false)
const favoriteStore = useFavoriteStore()
const breadcrumbUrl = computed(() => {
  return []
})
const products = ref<Array<Product>>([])
const loadMoreLoading = ref<boolean>(false)
const paramsProducts = ref(null)

onBeforeMount(async () => {
  await favoriteStore.fetchFavorites()
})

const pageLoad = async () => {
  isLoading.value = true
  currentPartenaire.value = null
  products.value = []

  paramsProducts.value = {
    with_filter: true,
    page: 1,
    perPage: perPage.value,
    sort: null,
  }

  currentCount.value = perPage.value
  await removeFilterAll()
}

const loadMore = async () => {
  loadMoreLoading.value = true
  paramsProducts.value.page++
  await loadProducts(paramsProducts.value)
  currentCount.value += currentCount.value
  loadMoreLoading.value = false
}

const loadProducts = async (paramsProducts: object) => {
  resultProducts.value = await productStore.fetchProductsByParams(
    paramsProducts,
  )
  products.value.push(...resultProducts.value.results)
  if (route.query.q) {
    const eventLabel =
      resultProducts.value.results_count > 0
        ? 'view_search_results'
        : 'no_search_results'
    await window.dataLayer?.push({
      event: eventLabel,
      search_term: route.query.q,
    })
  }
}

const count = computed(() => {
  return resultProducts.value.results_count
})

const filters = computed(() => {
  return resultProducts.value.filters
})

const parameters = computed(() => {
  return resultProducts.value.parameters
})

const hasParameters = computed(() => {
  return (
    resultProducts.value.parameters.name ||
    resultProducts.value.parameters.categories.length ||
    resultProducts.value.parameters.companies.length ||
    resultProducts.value.parameters.properties.length
  )
})

const removeFilterAll = async () => {
  term.value = null
  selectedCategoryId.value = null
  selectedCompanyId.value = null
  selectedProperties.value = null
}

const removeFilter = async (type: string) => {
  switch (type) {
    case filterType.name:
      term.value = null
      break
    case filterType.category:
      selectedCategoryId.value = null
      break
    case filterType.company:
      selectedCompanyId.value = null
      break
    case filterType.property:
      selectedProperties.value = null
      break
  }
  await loadPage()
}

const findPropertyData = (propertyId, value) => {
  const parentProperty = resultProducts.value.filters.properties.find(
    (p) => p.id === propertyId,
  )

  return {
    id: parentProperty.id,
    label: parentProperty.name,
    child: parentProperty.child[value],
  }
}

const filterProduct = async () => {
  await loadPage()
}

const loadPage = async () => {
  const queryValue = {}

  if (term.value) {
    queryValue.q = term.value
  }

  if (selectedCategoryId.value) {
    queryValue.category = selectedCategoryId.value
  }

  if (selectedCompanyId.value) {
    queryValue.company = selectedCompanyId.value
  }

  if (selectedProperties.value) {
    queryValue.property_id = selectedProperties.value.property_id
    queryValue.value = selectedProperties.value.value
  }

  await router.replace({
    name: ProductPageList.PRODUCTS,
    query: queryValue,
  })
}

watch(
  () => route.query,
  async (routeObject) => {
    await pageLoad()

    if (routeObject.q) {
      term.value = routeObject.q.toString()
      paramsProducts.value.name = term.value
    }

    if (routeObject.category) {
      productStore.setSelectedCategory(routeObject.category)
      paramsProducts.value.categories = [routeObject.category]
    }

    if (routeObject.company) {
      productStore.setSelectedCompany(routeObject.company)
      paramsProducts.value.companies = [routeObject.company]
    }

    if (routeObject.property_id && routeObject.value) {
      const properties = {
        property_id: routeObject.property_id,
        value: routeObject.value,
      }
      productStore.setSelectedProperty(properties)
      paramsProducts.value.properties = [properties]
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

<style scoped>
.bloc-content {
  @apply rounded-lg bg-white px-7.5 py-7.5 text-gray-500;
}
</style>
