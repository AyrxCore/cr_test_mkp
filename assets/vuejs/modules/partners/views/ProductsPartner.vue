<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div
      v-if="seller"
      class="xs:w-[100%] m-auto my-4 max-w-screen-2xl px-5 sm:px-8"
    >
      <breadcrumb-shared-component
        :list-url="breadcrumbUrl"
        :current-page="seller.name"
      />
      <ContactUsButtonComponent />
      <div class="text-green mt-3.5 flex flex-col lg:flex-row lg:items-center">
        <h3 class="text-title-35 text-primary">
          {{ seller.name }}
        </h3>
      </div>

      <div class="mt-10 flex flex-col md:flex-row">
        <div class="flex items-center justify-center rounded-lg bg-white w-full lg:w-1/4 h-[338px] p-1 lg:mr-5">
          <img
            :src="getUpplerImage(seller.avatar)"
            :alt="'Logo ' + seller.name"
            class="items-center rounded-lg sm:mx-auto"
          />
        </div>
        <div class="col-span-3 mt-5 hidden items-center rounded-lg bg-white md:mt-0 md:flex lg:w-3/4"></div>
      </div>


      <div
        class="mt-10 mb-7.5 flex flex-row items-center justify-end text-[14px] text-gray-500"
      >
        <div
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
        class="mt-10 mt-5 flex flex-col gap-4 text-gray-600 xl:grid xl:grid-cols-5"
      >
        <FiltersProductComponent
          v-if="!isLoading"
          :filters="filters"
        />
        <div class="col-span-4 flex flex-col rounded-lg pb-4 text-gray-500">
          <div
            class=""
          >
            <div
              v-if="isLoading"
              class="flex flex-col text-gray-600 md:grid md:grid-cols-2 md:gap-8 lg:grid-cols-4"
            >
              <div
                v-for="number in 4"
                :key="number"
              >
                <ProductLoadingComponent
                  class="mt-5 h-[516px] !w-auto md:mt-0 md:w-[392px]"
                />
              </div>
            </div>

            <div
              v-else
              class="flex flex-col text-gray-600 md:grid md:grid-cols-2 md:gap-8 lg:grid-cols-3"
            >
              <div
                v-for="(accord, key) in accordsCadres"
                :key="key"
              >
                <AccordCadreComponent :accord="accord" />
              </div>

              <div v-for="(product, key) in products" :key="key">
                <ProductComponent
                  :product="product"
                  class="mt-5 h-[516px] !w-auto md:mt-0 md:w-[392px]"
                />
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
    <div v-else class="w-full flex h-16 justify-center items-center">
      <LoaderSharedComponent
        class="text-secondary"
        classes="loader-xl loader"
      />
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import ProductComponent from '@/vuejs/modules/products/components/ProductComponent.vue'
import ChevronRightIconComponent from '@/vuejs/modules/shared/icon/ChevronRightIconComponent.vue'
import ChevronLeftIconComponent from '@/vuejs/modules/shared/icon/ChevronLeftIconComponent.vue'
import { getUpplerImage, PRODUCT_ACCORD_PROPERTY, PRODUCT_WITHOUT_ACCORD_PROPERTY } from '@/vuejs/services/utils'
import { useRoute } from 'vue-router'
import { computed, onMounted, ref, watch } from 'vue'
import { useProductStore } from '@/vuejs/stores/product'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import { Seller } from '@/vuejs/types/Seller'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import ProductLoadingComponent from '@/vuejs/modules/products/components/ProductLoadingComponent.vue'
import { useSellerStore } from '@/vuejs/stores/seller'
import FiltersProductComponent from '@/vuejs/modules/partners/components/FiltersProductComponent.vue'
import { useAccordCadreStore } from '@/vuejs/stores/accord_cadre'
import AccordCadreComponent from '@/vuejs/modules/home/component/AccordCadreComponent.vue'

const route = useRoute()
const sellerStore = useSellerStore()
const productStore = useProductStore()
const accordCadreStore = useAccordCadreStore()
const seller = ref<Seller>()
const resultProducts = ref([])
const accordsCadres = ref([])
const isLoading = ref<boolean>(true)

const breadcrumbUrl = computed(() => {
  return []
})

onMounted(async () => {
  if (route.params.id) {
    const paramsAccordCadre =  {
      seller_id: route.params.id,
      properties: [
        PRODUCT_ACCORD_PROPERTY
      ],
    }

    accordsCadres.value = await accordCadreStore.findAccordsCadresByParams(paramsAccordCadre)

    const paramsProducts =  {
      seller_id: route.params.id,
      with_filter: true,
      properties: [
        PRODUCT_WITHOUT_ACCORD_PROPERTY
      ],
    }

    resultProducts.value = await productStore.getProductsByParams(paramsProducts)
    isLoading.value = false
  }
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
  () => route.params.id as string,
  async (id: string) => {
    if (id) {
      seller.value = await sellerStore.getSellerById(id)
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
