<template>
  <BaseTemplate :title="productTitle">
    <LoadingComponent v-if="isLoading" class="my-12" />
    <div
      v-else-if="product && !isLoading"
      class="m-auto mt-4 max-w-screen-2xl flex-1 px-5 sm:px-8 xs:w-[100%]"
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
            <!-- Badge de réduction -->
            <div
              v-if="product.percent > 0 && productStore.isSellable(product)"
              class="absolute right-5 top-5 z-10 rounded-md bg-secondary px-3 py-1 text-lg font-bold text-white"
            >
              -{{ product.percent }}%
            </div>
            <CarouselListSharedComponent
              :show-nav="product.images?.length > 1"
              :slides-per-view="1"
              :space-between="20"
              :thumbs="{
                swiper:
                  thumbsSwiper && !thumbsSwiper.destroyed ? thumbsSwiper : null,
              }"
              class="h-[303px] md:h-full"
              :loop="product.images?.length > 1"
            >
              <SwiperSlide
                v-for="(img, key) in product.images"
                :key="key"
                class="!flex h-full w-[100%] items-center justify-center"
              >
                <img
                  :alt="`${product.name} main image ${key}`"
                  :src="getUpplerImage(img)"
                  class="h-full w-full object-contain"
                />
              </SwiperSlide>
              <SwiperSlide v-if="!product.images?.length">
                <img
                  :src="getUpplerImage(fallbackImage)"
                  alt="Produit sans image"
                  class="h-full w-full object-contain"
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
                    query: { seller: product.seller.externalId },
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
              </h3>
              Référence : {{ product.externalId }}
              <!-- Tags spéciaux -->
              <div v-if="visibleTags.length" class="mt-2 flex flex-wrap gap-2">
                <ProductTagComponent v-for="tag in visibleTags" :key="tag.key" :tag="tag" />
              </div>
              <!-- Bloc options -->
              <div v-if="!productStore.isSellable(product)">
                <NotSellableDetails :product="product" />
              </div>
              <div v-else>
                <ProductDetails
                  :product="product"
                  @update:product="product = $event"
                />
              </div>
            </div>
          </div>
        </div>

        <Tabs class="mt-4">
          <Tab name="Description">
            <p
              class="whitespace-pre-line py-4 text-sm md:text-base [&_ol]:mb-3 [&_ol]:list-decimal [&_ol]:pl-5 [&_ul]:mb-3 [&_ul]:list-disc [&_ul]:pl-5"
              v-html="product.description"
            />
          </Tab>
          <Tab
            v-if="technicalRows.length > 0"
            name="Caractéristiques techniques"
          >
            <table class="w-full table-auto">
              <tbody>
                <tr
                  v-for="(row, index) in technicalRows"
                  :key="index"
                  class="border text-sm text-primary md:text-base lg:text-lg"
                >
                  <td class="w-[20%] border p-2 font-medium">
                    {{ row.label }}
                  </td>
                  <td class="p-2">
                    <a
                      v-if="row.isLink"
                      :href="row.url"
                      target="_blank"
                      class="text-secondary underline hover:text-primary"
                    >
                      Cliquez-ici
                    </a>
                    <span v-else>{{ row.value }}</span>
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
          />
        </template>
        <!-- Fin bloc accords-cadres incontournables -->
        <div class="mb-8 mt-2 text-xs text-gray-500">
          Les références, photographies, remises et tarifs des produits fournis
          sur la marketplace n'ont qu'une valeur indicative. Pour toute
          confirmation d'information, nous vous invitons à nous contacter.
        </div>
      </div>
    </div>
    <div
      v-else
      class="m-auto my-4 flex max-w-screen-2xl justify-center px-5 sm:px-8 xs:w-[100%]"
    >
      Aucun produit n'a été trouvé avec cette référence
    </div>
  </BaseTemplate>
  <ProductAddToCartComponent
    v-if="product && productStore.isSellable(product)"
    :product="product"
    :show-price="true"
    class="z-10 flex lg:hidden"
  />
</template>

<script lang="ts" setup>
import { computed, ref, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { useRoute, useRouter } from 'vue-router'
import { SwiperSlide } from 'swiper/vue'
import { Tab, Tabs } from 'vue3-tabs-component'

import { PageList } from '@/vuejs/router'
import { ProductPageList } from '@/vuejs/router/pages-list'
import { useProductStore } from '@/vuejs/stores/product'
import { useUserStore } from '@/vuejs/stores/user'
import { getUpplerImage, isUrl } from '@/vuejs/services/utils'
import { formatProductGtmEvent, sendGtmEvent } from '@/vuejs/services/gtm'
import { Product, ProductProperties } from '@/vuejs/types/Product'

import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import LoadingComponent from '@/vuejs/modules/shared/LoadingComponent.vue'
import ProductAddToCartComponent from '@/vuejs/modules/products/components/ProductAddToCartComponent.vue'
import ProductsCarouselComponent from '@/vuejs/modules/shared/ProductsCarouselComponent.vue'
import NotSellableDetails from '@/vuejs/modules/products/components/NotSellableDetails.vue'
import ProductDetails from '@/vuejs/modules/products/components/ProductDetails.vue'
import ProductTagComponent from '@/vuejs/modules/products/components/ProductTagComponent.vue'
import AccordsCadreComponent from '@/vuejs/modules/home/component/AccordsCadreComponent.vue'
import { useProductTags } from '@/vuejs/modules/products/composables/useProductTags'

import sampleImg from '@/vuejs/assets/img/sample_product_img.png'

const route = useRoute()
const router = useRouter()

const productStore = useProductStore()
const { adherentTarifShowcases } = storeToRefs(useUserStore())

const thumbsSwiper = ref(null)
const fallbackImage = sampleImg
const isLoading = ref<boolean>(false)
const isLoadingSimilarProductsAndAccordsCadres = ref<boolean>(false)
const similarProducts = ref<Product[]>([])
const similarAccordsCadres = ref<Product[]>([])
const product = ref<Product>()

const { visibleTags } = useProductTags(computed(() => product.value?.tags), 'product')

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
  // Le filtrage est fait côté backend (DjustPropertyFilter)
  return { ...(product.value?.properties ?? {}) }
})

const technicalRows = computed(() => {
  const rows: Array<{
    label: string
    value?: string
    isLink?: boolean
    url?: string
    displayText?: string
  }> = []

  Object.entries(productProperties.value).forEach(([key, value]) => {
    // Exclure les tags (utilisés pour les badges)
    if (key.startsWith('tag_')) {
      return
    }

    // MKP-1411: Les mots clés sont volontairement masqués dans l'onglet "Caractéristiques techniques" (donnée Djust non souhaitée à l'affichage)
    if (key === 'Mots clés') {
      return
    }

    const stringValue = String(value)
    const urlCandidate = isUrl(stringValue)
    rows.push({
      label: key,
      value: stringValue,
      isLink: Boolean(urlCandidate),
      url: urlCandidate ? stringValue : undefined,
    })
  })

  if (product.value?.attachments?.length) {
    product.value.attachments.forEach((attachment) => {
      const label =
        attachment.name || getAttachmentFileName(attachment.url) || 'Document'
      rows.push({
        label,
        isLink: true,
        url: attachment.url,
        displayText: label,
      })
    })
  }

  return rows
})

const setThumbsSwiper = (swiper) => {
  thumbsSwiper.value = swiper
}

const getAttachmentFileName = (url: string): string => {
  try {
    const parsed = new URL(url)
    const pathname = parsed.pathname || ''
    const fileName = pathname.split('/').filter(Boolean).pop()
    return fileName ?? ''
  } catch {
    // URL invalide, on tente une extraction simple
    const parts = url.split('/').filter(Boolean)
    return parts.pop() ?? ''
  }
}

const isInShowcase = computed<boolean>(() =>
  adherentTarifShowcases.value.some(
    (showcase) => showcase.accordId === product.value.accordId,
  ),
)

watch(
  () => route.params.slug as string,
  async (slug: string) => {
    isLoading.value = true
    isLoadingSimilarProductsAndAccordsCadres.value = true
    try {
      product.value = await productStore.initProduct(slug)

      if (isInShowcase.value) {
        router.push({ name: PageList.HOME_PAGE })
      }

      if (product.value.variants.length > 2) {
        await productStore.findDefaultVariantProduct(product.value)
      }

      isLoading.value = false

      if (product.value.categories?.length > 0) {
        const categoryId =
          product.value.categories[product.value.categories.length - 1].id

        const productsAndAccordsCadres =
          await productStore.findSimilarProducts(categoryId)

        if (productsAndAccordsCadres?.results) {
          similarProducts.value = productsAndAccordsCadres.results.filter(
            (simProd) =>
              simProd.externalId !== slug &&
              !productStore.isAccordCadre(simProd),
          )
          similarAccordsCadres.value = productsAndAccordsCadres.results.filter(
            (simAccCad) => {
              return (
                simAccCad.seller?.id !== product.value.seller?.id &&
                productStore.isAccordCadre(simAccCad)
              )
            },
          )
        }
      }
      sendGtmEvent('view_item', {
        ecommerce: {
          currency: 'EUR',
          value: product.value.price * product.value.quantity,
          items: formatProductGtmEvent([product.value]),
        },
      })
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
