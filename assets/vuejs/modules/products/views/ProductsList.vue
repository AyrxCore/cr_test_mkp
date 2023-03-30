<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div v-if="isLoading" class="flex h-16 w-full items-center justify-center">
      <LoaderSharedComponent
        class="text-secondary"
        classes="loader-xl loader"
      />
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
          <div
            v-if="count"
            class="mt-5 flex w-full items-center justify-start rounded-md p-2 lg:mt-0 lg:justify-end"
          >
            <div
              class="mr-2 flex h-[28px] w-auto flex-row items-center justify-between rounded-md border bg-white"
            >
              <button
                class="flex"
                :class="{
                  'disable cursor-not-allowed': pageNumber === 1,
                }"
                @click="pagePreview"
              >
                <ChevronLeftIconComponent
                  class="ml-1 h-4 stroke-gray-500"
                  :stroke-color="'#626262'"
                />
              </button>
              <span>{{ pageNumber }} / {{ numberPageTotal() }}</span>
              <button
                class="flex"
                :class="{
                  'disable cursor-not-allowed':
                    pageNumber === numberPageTotal(),
                }"
                @click="pageNext"
              >
                <ChevronRightIconComponent
                  class="ml-1 h-4"
                  :stroke-color="'#626262'"
                />
              </button>
            </div>
            <div class="mr-2 items-center">{{ count }} produit(s)</div>
          </div>
        </div>
        <div
          class="mt-5 flex h-[50%] flex-col gap-4 text-gray-600 xl:grid xl:grid-cols-4"
        >
          <FiltersComponent
            v-if="filters"
            :filters="filters"
            @filter-product="filterProduct"
          />
          <div class="col-span-3 flex flex-col rounded-lg pb-4 text-gray-500">
            <div class="">
              <div
                class="flex flex-col text-gray-600 md:grid md:grid-cols-2 md:gap-8 lg:grid-cols-3"
              >
                <div v-for="(product, key) in products" :key="product.id">
                  <AccordCadreComponent
                    v-if="
                      product.isAccordCadre &&
                      (currentPartenaire === null ||
                        currentPartenaire === product.seller.id)
                    "
                    :accord="product"
                    :key="`ac-${product.id}`"
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
          </div>
        </div>
      </div>
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import ProductComponent from '@/vuejs/modules/products/components/ProductComponent.vue'
import ChevronRightIconComponent from '@/vuejs/modules/shared/icon/ChevronRightIconComponent.vue'
import ChevronLeftIconComponent from '@/vuejs/modules/shared/icon/ChevronLeftIconComponent.vue'
import { useRoute } from 'vue-router'
import { computed, onBeforeMount, onMounted, ref, watch } from 'vue'
import { useProductStore } from '@/vuejs/stores/product'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import AccordCadreComponent from '@/vuejs/modules/home/component/AccordCadreComponent.vue'
import { toNumber } from '@vue/shared'
import router from '@/vuejs/router'
import { ProductPageList } from '@/vuejs/router/pages-list'
import FiltersComponent from '@/vuejs/modules/products/components/FiltersComponent.vue'
import SelectedFilterComponent from '@/vuejs/modules/products/components/filters/SelectedFilterComponent.vue'
import { storeToRefs } from 'pinia'
import { filterType } from '@/vuejs/modules/products'

const route = useRoute()
const productStore = useProductStore()
const { selectedCategoryId, selectedProperties, selectedCompanyId } =
  storeToRefs(productStore)
const resultProducts = ref([])
const isLoading = ref<boolean>()
const term = ref<string>('')
const currentPartenaire = ref<number>(null)
const perPage = ref<number>(42)
const pageNumber = ref<number>(1)
const resultNotFound = ref<boolean>(false)

const breadcrumbUrl = computed(() => {
  return []
})

const loadProducts = async (paramsProducts) => {
  isLoading.value = true

  try {
    resultProducts.value = await productStore.fetchProductsByParams(paramsProducts)
    pageNumber.value = resultProducts.value.page
    if (route.query.q) {
      const eventLabel = resultProducts.value.results_count > 0 ? 'view_search_results' : 'no_search_results'
      await window.dataLayer?.push({
        event: eventLabel,
        search_term: route.query.q,
      })
    }

  } catch (e) {
    resultNotFound.value = true
  }
  isLoading.value = false
}

const count = computed(() => {
  return resultProducts.value.results_count
})

const products = computed(() => {
  return resultProducts.value.results ?? []
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

const removeFilter = async (type) => {
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
  pageNumber.value = 1
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
  pageNumber.value = 1
  await loadPage()
}

const numberPageTotal = () => {
  return toNumber(Math.ceil(count.value / perPage.value))
}

const pagePreview = async () => {
  if (pageNumber.value > 1) {
    pageNumber.value--
  } else {
    pageNumber.value = 1
  }

  await loadPage()
}

const pageNext = async () => {
  if (pageNumber.value >= numberPageTotal()) {
    return false
  }
  pageNumber.value++
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

  queryValue.page = pageNumber.value

  await router.replace({
    name: ProductPageList.PRODUCTS,
    query: queryValue,
  })
}

watch(
  () => route.query,
  async (routeObject) => {
    currentPartenaire.value = null
    if (routeObject.page) {
      pageNumber.value = routeObject.page
    } else {
      pageNumber.value = 1
    }

    const paramsProducts = {
      with_filter: true,
      page: pageNumber.value,
      perPage: perPage.value,
      sort: null,
    }

    await removeFilterAll()

    if (routeObject.q) {
      term.value = routeObject.q
      paramsProducts.name = term.value
    }

    if (routeObject.category) {
      productStore.setSelectedCategory(routeObject.category)
      paramsProducts.categories = [routeObject.category]
    }

    if (routeObject.company) {
      productStore.setSelectedCompany(routeObject.company)
      paramsProducts.companies = [routeObject.company]
    }

    if (routeObject.property_id && routeObject.value) {
      const properties = {
        property_id: routeObject.property_id,
        value: routeObject.value,
      }
      productStore.setSelectedProperty(properties)
      paramsProducts.properties = [properties]
    }

    await loadProducts(paramsProducts)
  },
  { immediate: true },
)
</script>

<style scoped>
.bloc-content {
  @apply rounded-lg bg-white px-7.5 py-7.5 text-gray-500;
}
</style>
