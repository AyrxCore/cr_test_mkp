<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div
      v-if="isLoading"
      class="w-full flex h-16 justify-center items-center"
    >
      <LoaderSharedComponent
        class="text-secondary"
        classes="loader-xl loader"
      />
    </div>
    <div
      v-else
      class="xs:w-[100%] m-auto my-4 max-w-screen-2xl px-5 sm:px-8"
    >
      <breadcrumb-shared-component
        :list-url="breadcrumbUrl"
        :current-page="`Recherche: ${term}`"
      />
      <ContactUsButtonComponent />

      <div
        class="mt-10 mb-7.5 w-full pt-5 flex flex-row items-center justify-between text-[14px] text-gray-500"
      >
        <div
          class="mr-2 flex p-2 w-2/3 flex-row items-start rounded-md border bg-white"
        >
          <span class="text-sm md:text-base lg:text-lg">
            résultats trouvés pour votre recherche "<span class="font-bold text-primary">{{ term }}</span>"
          </span>
        </div>
        <div
          v-if="count"
          class="mr-2 flex h-[28px] w-[84px] flex-row items-center justify-between rounded-md border bg-white"
        >
          <button class="flex">
            <ChevronLeftIconComponent
              class="ml-1 h-4"
              :stroke-color="'#A4A4A4'"
            />
          </button>
          <span>{{ pageNumber }}</span>
          <button class="flex">
            <ChevronRightIconComponent
              class="ml-1 h-4"
              :stroke-color="'#A4A4A4'"
            />
          </button>
        </div>
        <div class="mr-2">{{ count }} produits</div>
        <div class="h-[28px] rounded-md border bg-white">
          <select class="h-[28px] rounded-md py-0 text-[14px]">
            <option>Trier par produit</option>
          </select>
        </div>
      </div>
      <div
        class="mt-10 mt-5 flex flex-col gap-4 text-gray-600 xl:grid xl:grid-cols-4"
      >
        <!--<FiltersProductComponent
          :filters="filters"
        />-->
        <div class="col-span-4 flex flex-col rounded-lg pb-4 text-gray-500">
          <div class="">
            <div class="flex flex-col text-gray-600 md:grid md:grid-cols-2 md:gap-8 lg:grid-cols-4">
              <div v-for="(product, key) in products" :key="key">
                <AccordCadreComponent
                  v-if="product.isAccordCadre && (currentPartenaire === null || currentPartenaire === product.seller.id)"
                  :accord="product" />
                <ProductComponent
                  v-else-if="currentPartenaire === product.seller.id || currentPartenaire === null"
                  :product="product"
                  class="mt-5 h-[516px] !w-auto md:mt-0 md:w-[392px]"
                />
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
import { computed, ref, watch } from 'vue'
import { useProductStore } from '@/vuejs/stores/product'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import AccordCadreComponent from '@/vuejs/modules/home/component/AccordCadreComponent.vue'

const route = useRoute()
const productStore = useProductStore()
const resultProducts = ref([])
const isLoading = ref<boolean>()
const term = ref<string>('')
const currentPartenaire = ref<number>(null)


const breadcrumbUrl = computed(() => {
  return []
})

const loadProducts = (async () => {

  const paramsProducts = {
    with_filter: true,
  }

  if (route.query.q) {
    paramsProducts.name = route.query.q
    term.value = route.query.q
  }

  resultProducts.value = await productStore.getProductsByParams(paramsProducts)
  if (resultProducts.value.results[0].isAccordCadre && currentPartenaire.value === null) {
    currentPartenaire.value = resultProducts.value.results[0].seller.id
  }
  isLoading.value = false
})

const count = computed(() => {
  return resultProducts.value.results_count
})

const products = computed(() => {
  return resultProducts.value.results
})

const filters = computed(() => {
  return resultProducts.value.filters
})

const pageNumber = computed(() => {
  return resultProducts.value.page
})


watch(
  () => route.query.q,
  async (searchTerm: string) => {
    if (searchTerm) {
      isLoading.value = true
      currentPartenaire.value = null
      await loadProducts()
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
