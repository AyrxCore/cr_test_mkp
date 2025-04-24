<template>
  <BaseTemplate :title="productTitle">
    <LoadingComponent v-if="isLoading" class="my-12" />
    <div
      v-else-if="product && !isLoading"
      class="xs:w-[100%] m-auto mt-4 max-w-screen-2xl flex-1 px-5 sm:px-8"
    >
      <BreadcrumbSharedComponent
        :current-page="product.name"
        :list-url="breadcrumbUrl"
        gtm-event-name="click_product_breadcrumb"
      />
      <div class="m-auto mt-4 flex flex-col">
        <div class="flex flex-col md:flex-row">
          <!-- Slider all pictures, hidden on mobile -->
          <CarouselListSharedComponent
            v-if="product.images?.length > 1"
            :breakpoints="{
              640: {
                slidesPerView: 4,
                slidesPerGroup: 4,
                slidesPerColumn: 4,
                spaceBetween: 20,
              },
            }"
            :pagination="false"
            :show-nav="false"
            :space-between="10"
            class="hide-swiper-to-xl !ml-0 !mr-4 xl:h-[450px]"
            direction="vertical"
            loop
            watch-slides-progress
            @swiper="setThumbsSwiper"
          >
            <SwiperSlide
              v-for="(img, key) in product.images"
              :key="key"
              class="cursor-pointer"
            >
              <img
                :alt="`${product.name} image ${key}`"
                :src="getUpplerImage(img)"
                class="bg-white p-1"
              />
            </SwiperSlide>
          </CarouselListSharedComponent>
          <!-- Fin slider all pictures -->
          <!-- Slider picture -->
          <div
            class="relative flex w-[100%] items-center bg-white md:!ml-0 md:!mr-6 md:h-[450px] md:max-w-[50%] xl:max-w-[40%]"
          >
            <CarouselListSharedComponent
              :show-nav="product.images?.length > 1"
              :slides-per-view="1"
              :space-between="20"
              :thumbs="{
                swiper:
                  thumbsSwiper && !thumbsSwiper.destroyed ? thumbsSwiper : null,
              }"
              class="h-[303px]"
              loop
            >
              <SwiperSlide
                v-for="(img, key) in product.images"
                :key="key"
                class="!flex w-[100%] justify-center p-4 md:h-auto"
              >
                <img
                  :alt="`${product.name} main image ${key}`"
                  :src="getUpplerImage(img)"
                  class="h-auto sm:mx-auto"
                  @click.stop="
                    sendGaEvent('click_product_img', {
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
              <div class="mb-2">
                <RouterLink
                  :to="{
                    name: ProductPageList.PRODUCTS,
                    query: { q: product.seller.name },
                  }"
                  class="font-bold text-secondary underline"
                >
                  {{ product.seller.name }}
                </RouterLink>
              </div>
              <h3
                class="flex items-center text-xl font-bold text-primary md:mb-3 md:text-3xl"
              >
                {{ product.name }}
                <AddFavoriteComponent
                  :favorites-product="product.favorites"
                  :product-id="product.id"
                  :product-name="product.name"
                  :variant-id="product.defaultVariantId"
                  class="ml-2 inline-flex"
                />
              </h3>
              Référence : {{ product.reference }}
              <!-- Bloc options -->
              <div v-if="!product.sellable">
                <NotSellableDetails :product="product" />
              </div>
              <div v-else>
                <ProductDetails :product="product" />
              </div>
            </div>
          </div>
        </div>

        <Tabs class="mt-4">
          <Tab
            name="Description"
            @click.native="
              sendGaEvent('click_product_view_description', {
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
            v-if="Object.values(productProperties).length > 0"
            name="Caractéristiques techniques"
            @click.native="
              sendGaEvent('click_product_view_caracteristics', {
                product_name: product.name,
              })
            "
          >
            <table class="w-full table-auto">
              <tbody>
                <tr
                  v-for="(property, key, index) in productProperties"
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
        <template
          v-if="
            isLoadingSimilarProductsAndAccordsCadres ||
            similarProducts?.length > 0
          "
        >
          <h3 class="text-title-primary mb-2 mt-4">
            Sélection de produits similaires
          </h3>
          <ProductsCarouselComponent
            :loading="isLoadingSimilarProductsAndAccordsCadres"
            :products="similarProducts"
            class="mt-4"
            @click-left="sendGaEvent('click_product_slider_left')"
            @click-right="sendGaEvent('click_product_slider_right')"
            @click-add-cart="
              sendGaEvent('click_product_slider_product_add_cart', $event)
            "
            @click-title="
              sendGaEvent('click_product_slider_product_title', $event)
            "
            @click-img="sendGaEvent('click_product_slider_product_img', $event)"
            @click-moins-qty="
              sendGaEvent('click_product_slider_products_moins_qty', $event)
            "
            @click-plus-qty="
              sendGaEvent('click_product_slider_products_plus_qty', $event)
            "
          />
        </template>
        <!-- Fin bloc produits similaire -->

        <!-- Bloc accords-cadres incontournables -->
        <template
          v-if="
            isLoadingSimilarProductsAndAccordsCadres ||
            similarAccordsCadres?.length > 0
          "
        >
          <h3 class="text-title-primary mb-2 mt-4">
            Les accords-cadres incontournables
          </h3>
          <AccordsCadreComponent
            :accords-cadres="similarAccordsCadres"
            :loading="isLoadingSimilarProductsAndAccordsCadres"
            @click-left="sendGaEvent('click_product_slider_fat_left')"
            @click-right="sendGaEvent('click_product_slider_fat_right')"
            @click-cta="sendGaEvent('click_product_slider_fat_cta', $event)"
            @click-title="sendGaEvent('click_product_slider_fat_title', $event)"
            @click-img="sendGaEvent('click_product_slider_fat_img', $event)"
          />
        </template>
        <!-- Fin bloc accords-cadres incontournables -->
        <div class="mb-8 mt-2 text-xs text-gray-500">
          Les références, photographies, remises et tarifs des produits fournis
          sur la marketplace n’ont qu’une valeur indicative. Pour toute
          confirmation d’information, nous vous invitons à nous contacter.
        </div>
      </div>
    </div>
    <div
      v-else
      class="xs:w-[100%] m-auto my-4 flex max-w-screen-2xl justify-center px-5 sm:px-8"
    >
      Aucun produit n'a été trouvé avec cette référence
    </div>
  </BaseTemplate>
  <ProductAddToCartComponent
    v-if="product && product.sellable"
    :product="product"
    :show-price="true"
    class="z-10 flex lg:hidden"
  />
</template>
<script lang="ts" setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { SwiperSlide } from 'swiper/vue'
import { Tab, Tabs } from 'vue3-tabs-component'
import { storeToRefs } from 'pinia'

import AddFavoriteComponent from '@/vuejs/modules/products/components/AddFavoriteComponent.vue'
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import LoadingComponent from '@/vuejs/modules/shared/LoadingComponent.vue'
import ProductAddToCartComponent from '@/vuejs/modules/products/components/ProductAddToCartComponent.vue'
import ProductsCarouselComponent from '@/vuejs/modules/shared/ProductsCarouselComponent.vue'
import NotSellableDetails from '@/vuejs/modules/products/components/NotSellableDetails.vue'
import ProductDetails from '@/vuejs/modules/products/components/ProductDetails.vue'
import AccordsCadreComponent from '@/vuejs/modules/home/component/AccordsCadreComponent.vue'

import { getUpplerImage, isUrl } from '@/vuejs/services/utils'
import { PageList } from '@/vuejs/router'
import { Product, ProductProperties } from '@/vuejs/types/Product'
import { useFavoriteStore } from '@/vuejs/stores/favorite'
import { useProductStore } from '@/vuejs/stores/product'
import { useUserStore } from '@/vuejs/stores/user'
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'
import sampleImg from '@/vuejs/assets/img/sample_product_img.png'

import { ProductPageList } from '@/vuejs/router/pages-list'

const route = useRoute()
const router = useRouter()

const productStore = useProductStore()
const favoriteStore = useFavoriteStore()
const { adherentTarifShowcases } = storeToRefs(useUserStore())

const thumbsSwiper = ref(null)
const option = ref([])
const isLoading = ref<boolean>(false)
const isLoadingSimilarProductsAndAccordsCadres = ref<boolean>(false)
const similarProducts = ref<Product[]>([])
const similarAccordsCadres = ref<Product[]>([])
const product = ref<Product>()

const FILTERED_PRODUCT_PROPERTIES = [
  'accord-id',
  'vendable',
  'pourcentage_de_remise',
  'Formulaire avec message',
  'accord-name',
]

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

const productTitle = computed((): string => {
  return product.value ? product.value.name : ''
})

const productProperties = computed((): ProductProperties => {
  const productProperties = { ...product.value.properties }
  FILTERED_PRODUCT_PROPERTIES.forEach((key) => {
    delete productProperties[key]
  })
  return productProperties
})

const setThumbsSwiper = (swiper) => {
  thumbsSwiper.value = swiper
}

const isInShowcase = computed<boolean>(() =>
  adherentTarifShowcases.value.some(
    (showcase) => showcase.accordId === product.value.properties['accord-id'],
  ),
)

watch(
  () => route.params.slug as string,
  async (slug: string) => {
    isLoading.value = true
    isLoadingSimilarProductsAndAccordsCadres.value = true
    try {
      const productId = slug.split('-')
      const formattedProductId = parseInt(productId[productId.length - 1])

      product.value = await productStore.initProduct(formattedProductId)

      if (isInShowcase.value) {
        router.push({ name: PageList.HOME_PAGE })
      }

      if (product.value.variants.length > 2) {
        await productStore.findDefaultVariantProduct(product.value)
      }

      option.value = [...product.value.defaultVariantOptions]

      isLoading.value = false

      if (product.value.categories.length > 0) {
        const categoryId =
          product.value.categories[product.value.categories.length - 1].id

        const productsAndAccordsCadres =
          await productStore.findSimilarProducts(categoryId)

        similarProducts.value = productsAndAccordsCadres.results.filter(
          (simProd) =>
            simProd.id !== formattedProductId && !simProd.isAccordCadre,
        )
        similarAccordsCadres.value = productsAndAccordsCadres.results.filter(
          (simAccCad) => {
            return (
              simAccCad.seller.id !== product.value.seller.id &&
              simAccCad.isAccordCadre
            )
          },
        )
      }
    } finally {
      isLoading.value = false
      isLoadingSimilarProductsAndAccordsCadres.value = false
    }
  },

  { immediate: true },
)
</script>

<style lang="postcss" scoped>
@media (max-width: 1280px) {
  .hide-swiper-to-xl {
    @apply hidden;
  }
}
</style>
