<template>
  <BaseTemplate :title="productTitle" class="ff-roboto">
    <LoadingComponent v-if="isLoading" class="my-12" />
    <div
      v-else-if="product && !isLoading"
      class="xs:w-[100%] m-auto mt-4 max-w-screen-2xl flex-1 px-5 sm:px-8"
    >
      <BreadcrumbSharedComponent
        :list-url="breadcrumbUrl"
        :current-page="product.name"
      />
      <div class="m-auto mt-4 flex flex-col">
        <div class="flex flex-col md:flex-row">
          <!-- Slider all pictures, hidden on mobile -->
          <CarouselListSharedComponent
            v-if="product.images?.length > 1"
            direction="vertical"
            :space-between="10"
            :pagination="false"
            :show-nav="false"
            :breakpoints="{
              640: {
                slidesPerView: 4,
                slidesPerGroup: 4,
                slidesPerColumn: 4,
                spaceBetween: 20,
              },
            }"
            loop
            watch-slides-progress
            class="!ml-0 !mr-4 hidden xl:flex xl:h-[450px]"
            @swiper="setThumbsSwiper"
          >
            <SwiperSlide
              v-for="(img, key) in product.images"
              :key="key"
              class="cursor-pointer"
            >
              <img
                :src="getUpplerImage(img)"
                :alt="`${product.name} image ${key}`"
                class="bg-white p-1"
              />
            </SwiperSlide>
          </CarouselListSharedComponent>
          <!-- Fin slider all pictures -->
          <!-- Slider picture -->
          <div
            class="relative flex w-[100%] items-center bg-white md:!mr-6 md:!ml-0 md:h-[450px] md:max-w-[50%] xl:max-w-[40%]"
          >
            <CarouselListSharedComponent
              :slides-per-view="1"
              :space-between="20"
              :show-nav="product.images?.length > 1"
              :thumbs="{
                swiper:
                  thumbsSwiper && !thumbsSwiper.destroyed ? thumbsSwiper : null,
              }"
              loop
              class="h-[303px]"
            >
              <SwiperSlide
                v-for="(img, key) in product.images"
                :key="key"
                class="flex w-[100%] justify-center p-4 md:h-auto"
              >
                <img
                  :src="getUpplerImage(img)"
                  :alt="`${product.name} main image ${key}`"
                  class="h-auto sm:mx-auto"
                  @click.stop="
                    sendGtmEvent('click_product_img', {
                      product_name: product.name,
                      partner_name: product.seller.name,
                      partner_id: product.seller.id,
                    })
                  "
                />
              </SwiperSlide>
              <SwiperSlide v-if="!product.images?.length">
                <img
                  :src="getUpplerImage(sampleImg)"
                  alt="Produit sans image"
                  class="h-auto sm:mx-auto"
                />
              </SwiperSlide>
            </CarouselListSharedComponent>
          </div>
          <!-- Fin slider picture -->
          <div class="flex flex-col">
            <!-- Product details -->
            <div class="mt-4 md:mt-0">
              <img
                :src="getUpplerImage(product.seller.avatar)"
                :alt="`${product.seller.name} logo`"
                class="mb-1 h-10"
              />
              <h3
                class="flex items-center text-xl text-primary md:mb-3 md:text-3xl"
              >
                {{ product.name }}
                <AddFavoriteComponent
                  :product-id="product.id"
                  :product-name="product.name"
                  :variant-id="product.defaultVariantId"
                  :favorites-product="product.favorites"
                  class="ml-2 inline-flex"
                />
              </h3>
              Référence : {{ product.reference }}
              <!-- Bloc options -->
              <div v-if="hasOptions" class="my-4">
                <div class="mb-2 text-lg font-bold text-primary md:text-xl">
                  Mes options
                </div>
                <div
                  v-for="(children, key, index) in product.options"
                  :key="key"
                  class="mt-2 flex w-full items-center justify-between bg-white px-4 py-2"
                >
                  <span class="text-sm md:text-base lg:text-lg">
                    {{ key }}
                  </span>
                  <select
                    v-if="key && children.length > 0"
                    v-model="option[index]"
                    class="h-[1.75rem] w-1/2 border-none p-0"
                    @change="updateProductVariant"
                    @input="
                      sendGtmEvent('click_product_options', {
                        product_name: product.name,
                        partner_name: product.seller.name,
                        partner_id: product.seller.id,
                        option_id: option[index],
                      })
                    "
                  >
                    <option
                      v-for="child in children"
                      :key="child.id"
                      :value="child.id"
                    >
                      {{ child.value }}
                    </option>
                  </select>
                </div>
              </div>
              <!-- End Bloc options -->
            </div>
            <!-- Fin product details -->
            <!-- Quantité + prix -->
            <div class="flex justify-between md:flex-col">
              <div class="lg:my-6">
                <div class="relative inline-flex items-center">
                  <span class="mr-2 hidden md:block"> Quantité </span>
                  <ProductQuantityComponent
                    :quantity="product.quantity"
                    @update-quantity="updateQuantity"
                  />
                </div>
              </div>
              <LoaderSharedComponent
                v-if="isLoadingPrice"
                class="text-secondary"
                classes="loader-lg loader"
              />
              <div v-else class="mb-4 flex items-end">
                <div
                  v-if="product.price"
                  class="mr-2 text-xl font-bold text-primary md:text-3xl"
                >
                  {{ product.price }}€ HT
                </div>
                <div
                  v-if="product.priceReference"
                  :class="{
                    'text-sm text-gray-500 line-through md:text-base lg:text-lg':
                      product.price,
                    'text-xl font-bold text-primary': product.price === null,
                  }"
                >
                  {{ product.priceReference }}€ HT
                </div>
              </div>
            </div>
            <!-- Fin Quantité + prix -->
            <!-- Bloc livraison -->
            <div v-if="product.seller.description" class="mt-2">
              <h4 class="text-lg md:text-xl">Infos livraison</h4>
              <div class="mt-2 flex items-center">
                <TruckIconComponent class="mr-4 w-8 shrink-0 md:w-6" />
                {{ product.seller.description }}
              </div>
            </div>
            <!-- Fin Livraison -->
            <ProductAddToCartComponent
              v-if="product"
              class="mt-4 hidden lg:flex"
              :product="product"
            />
          </div>
        </div>
      </div>

      <Tabs class="mt-4" :options="{ disableScrollBehavior: true }">
        <Tab
          name="Description"
          @click.native="
            sendGtmEvent('click_product_view_description', {
              product_name: product.name,
            })
          "
        >
          <p
            class="whitespace-pre-line py-4 text-sm md:text-base"
            v-html="product.description"
          />
        </Tab>
        <Tab
          v-if="product.properties.length !== 0"
          name="Caractéristiques technique"
          @click.native="
            sendGtmEvent('click_product_view_caracteristics', {
              product_name: product.name,
            })
          "
        >
          <table class="w-full table-auto">
            <tbody>
              <tr
                v-for="(property, key, index) in product.properties"
                :key="index"
                class="border text-sm text-primary md:text-base lg:text-lg"
              >
                <td class="w-[20%] border p-2">{{ key }}</td>
                <td class="p-2">
                  <a v-if="isUrl(property)" :href="property" target="_blank">
                    Cliquez-ici
                  </a>
                  <span v-else>{{ property }}</span>
                </td>
              </tr>
            </tbody>
          </table>
        </Tab>
      </Tabs>

      <!-- Bloc produits similaire -->
      <template v-if="isLoadingSimilarProducts || similarProducts?.length >= 5">
        <h3 class="mt-4 mb-2 text-lg text-primary md:text-xl">
          Sélection de produits similaires
        </h3>
        <ProductsLoadingCarouselComponent
          v-if="!similarProducts && isLoadingSimilarProducts"
        />
        <ProductsCarouselComponent
          v-else-if="similarProducts.length > 0"
          :products="similarProducts"
          class="mt-4 mb-12"
        />
      </template>
      <!-- Fin bloc produits similaire -->
    </div>
    <div
      v-else
      class="xs:w-[100%] m-auto my-4 flex max-w-screen-2xl justify-center px-5 sm:px-8"
    >
      Aucun produit n'a été trouvé avec cette référence
    </div>
  </BaseTemplate>
  <ProductAddToCartComponent
    v-if="product"
    class="z-10 flex lg:hidden"
    :product="product"
    :show-price="true"
  />
</template>
<script lang="ts" setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { SwiperSlide } from 'swiper/vue'
import { Tab, Tabs } from 'vue3-tabs-component'

import AddFavoriteComponent from '@/vuejs/modules/products/components/AddFavoriteComponent.vue'
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import LoadingComponent from '@/vuejs/modules/shared/LoadingComponent.vue'
import ProductAddToCartComponent from '@/vuejs/modules/products/components/ProductAddToCartComponent.vue'
import ProductQuantityComponent from '@/vuejs/modules/shared/ProductQuantityComponent.vue'
import ProductsCarouselComponent from '@/vuejs/modules/shared/ProductsCarouselComponent.vue'
import ProductsLoadingCarouselComponent from '@/vuejs/modules/shared/ProductsLoadingCarouselComponent.vue'
import TruckIconComponent from '@/vuejs/modules/shared/icon/TruckIconComponent.vue'

import { getUpplerImage, isUrl } from '@/vuejs/services/utils'
import { PageList } from '@/vuejs/router'
import { Product } from '@/vuejs/types/Product'
import { useFavoriteStore } from '@/vuejs/stores/favorite'
import { useProductStore } from '@/vuejs/stores/product'
import { useChannelStore } from '@/vuejs/stores/channel'
import { sendGtmEvent } from '@/vuejs/services/gtm'
import sampleImg from '@/vuejs/assets/img/sample_product_img.png'
import { OPTIONAL_FRONT_BLOCKS } from '@/vuejs/services/const'

const route = useRoute()
const productStore = useProductStore()
const favoriteStore = useFavoriteStore()
const channelStore = useChannelStore()

const thumbsSwiper = ref(null)
const option = ref([])
const isLoading = ref<boolean>(false)
const isLoadingPrice = ref<boolean>(false)
const isLoadingSimilarProducts = ref<boolean>(false)
const similarProducts = ref<Product[]>([])
const product = ref<Product>()

onMounted(async () => {
  await favoriteStore.fetchFavorites()
})

const breadcrumbUrl = computed(() => {
  const breadcrumb = []
  if (product.value.categories.length > 0) {
    for (const [, category] of Object.entries(product.value.categories)) {
      breadcrumb.push({
        id: category.id,
        name: category.name,
        url: {
          name: PageList.PRODUCTS,
          query: { category: category.id },
        },
      })
    }
  }

  return breadcrumb
})

const productTitle = computed(() => {
  return product.value ? product.value.name + ' | ' : ''
})

const hasOptions = computed((): boolean => {
  return Object.keys(product?.value.options)[0].length > 0
})

const updateQuantity = (event) => {
  let gtmEvent = 'click_product_plus_qty'
  if (product.value.quantity > event.quantity) {
    gtmEvent = 'click_product_moins_qty'
  }
  sendGtmEvent(gtmEvent, {
    product_name: product.value.name,
    qty_value: event.quantity,
  })
  product.value.quantity = event.quantity
}

const setThumbsSwiper = (swiper) => {
  thumbsSwiper.value = swiper
}

const updateProductVariant = async () => {
  isLoadingPrice.value = true
  product.value = await productStore.changeVariant(product.value, option.value)
  isLoadingPrice.value = false
}

watch(
  () => route.params.slug as string,
  async (slug: string) => {
    isLoading.value = true
    isLoadingSimilarProducts.value = true
    try {
      const productId = slug.split('-')
      const formattedProductId = parseInt(productId[productId.length - 1])

      product.value = await productStore.initProduct(formattedProductId)
      if (product.value.variants.length > 2) {
        await productStore.findDefaultVariantProduct(product.value)
      }

      option.value = [...product.value.defaultVariantOptions]

      isLoading.value = false

      if (product.value.categories.length > 0) {
        const categoryId =
          product.value.categories[product.value.categories.length - 1].id
        similarProducts.value = await productStore.findSimilarProducts(
          categoryId,
          formattedProductId,
        )
      }
      isLoadingSimilarProducts.value = false
    } catch (error) {
    } finally {
      isLoading.value = false
    }
  },

  { immediate: true },
)
</script>

<style scoped></style>
