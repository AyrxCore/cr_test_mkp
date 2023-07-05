<template>
  <BaseTemplate :title="`${productTitle} Qantis - MarketPlace`">
    <LoadingComponent v-if="isLoading" />
    <div
      v-else-if="product && !isLoading"
      class="xs:w-[100%] m-auto my-4 max-w-screen-2xl flex-1 px-5 sm:px-8"
    >
      <BreadcrumbSharedComponent
        :list-url="breadcrumbUrl"
        :current-page="product.name"
      />
      <div class="flex w-[100%] max-w-screen-2xl justify-end">
        <ContactUsButtonComponent />
      </div>
      <div
        class="m-auto my-3.5 flex w-[100%] max-w-screen-2xl flex-col lg:grid lg:grid-cols-2 lg:gap-4"
      >
        <!-- Bloc image produit -->
        <div>
          <ProductTitleComponent
            class="mb-5 flex rounded-lg bg-white p-5 lg:hidden"
          >
            <template #name> {{ product.name }}</template>
            <template #partner> {{ product.seller.name }}</template>
            <template #reference> {{ product.reference }}</template>
          </ProductTitleComponent>

          <div class="relative">
            <CarouselListSharedComponent
              class="nav-mobile-only mx-auto h-[303px] items-center rounded-xl bg-white px-4 md:h-[590px]"
              :slides-per-view="1"
              :space-between="20"
              :breakpoints="{
                640: {
                  slidesPerView: 1,
                  spaceBetween: 20,
                },
              }"
              :navigation="true"
              :show-nav="true"
              :thumbs="{
                swiper:
                  thumbsSwiper && !thumbsSwiper.destroyed ? thumbsSwiper : null,
              }"
            >
              <SwiperSlide
                v-for="(img, key) in product.images"
                :key="key"
                class="flex items-center justify-center p-8 md:h-auto"
              >
                <img
                  :src="getUpplerImage(img)"
                  alt="Picture"
                  class="h-auto items-center sm:mx-auto"
                />
              </SwiperSlide>
            </CarouselListSharedComponent>
          </div>
          <div class="relative">
            <CarouselListSharedComponent
              class="mx-auto mt-5 hidden h-[150px] items-center px-4 py-4 md:flex md:justify-center"
              :space-between="10"
              watch-slides-progress
              :pagination="false"
              :loop="false"
              :navigation="false"
              :show-nav="false"
              :breakpoints="{
                640: {
                  slidesPerView: 4,
                  spaceBetween: 20,
                },
              }"
              @swiper="setThumbsSwiper"
            >
              <SwiperSlide
                v-for="(img, key) in product.images"
                :key="key"
                class="flex items-center rounded-xl bg-white"
              >
                <img
                  :src="getUpplerImage(img)"
                  alt="Picture"
                  class="items-center rounded-xl bg-white"
                />
              </SwiperSlide>
            </CarouselListSharedComponent>
          </div>
        </div>
        <!-- Fin Bloc image produit -->

        <!-- Bloc détails produit -->
        <div>
          <div
            class="mt-5 flex flex-col rounded-lg bg-white p-5 md:p-7 lg:mt-0"
          >
            <ProductTitleComponent class="hidden lg:flex">
              <template #name> {{ product.name }}</template>
              <template #partner> {{ product.seller.name }}</template>
              <template #reference> {{ product.reference }}</template>
            </ProductTitleComponent>
            <div
              v-if="isLoadingPrice"
              class="mt-5 flex h-10 w-full items-center justify-start"
            >
              <LoaderSharedComponent
                class="text-secondary"
                classes="loader-lg loader"
              />
            </div>
            <div v-else class="mt-14 hidden flex-col lg:flex">
              <div>
                <span
                  v-if="product.priceReference"
                  :class="{
                    'text-sm text-gray-500 line-through  md:text-base lg:text-lg':
                      product.price,
                    'text-[25px] font-bold text-primary':
                      product.price === null,
                  }"
                  >{{ product.priceReference }}€ HT
                </span>
                <span
                  v-if="product.percent > 0"
                  class="ml-2 rounded-lg bg-secondary px-2.5 py-1.5 text-white"
                  >{{ product.percent }}%</span
                >
              </div>
              <div
                v-if="product.price"
                class="mt-3 text-[25px] font-bold text-primary"
              >
                {{ product.price }}€ HT
              </div>
            </div>
            <div class="lg:mt-12">
              <div class="inline-flex items-center text-gray-500">
                <span
                  class="mr-2 text-sm text-gray-500 md:text-base lg:text-lg"
                >
                  Quantité
                </span>
                <ProductQuantityComponent
                  :quantity="product.quantity"
                  @update-quantity="updateQuantity"
                />

                <div class="relative ml-5 hidden lg:flex">
                  <AddFavoriteComponent
                    :product-id="product.id"
                    :product-name="product.name"
                    :variant-id="product.selectedVariantId"
                    :favorites-selected="product.favorites"
                  />
                  Ajouter ce produit à mes favoris
                </div>
              </div>
              <p class="mt-1">
                <span class="text-sm text-gray-500 md:text-base lg:text-lg"
                  >Conditionnement conseillé : {{ product.conditionnement }}
                </span>
              </p>
              <div class="mt-12">
                <div
                  v-for="(children, key, index) in product.options"
                  :key="key"
                  class="mt-2 w-full items-center text-gray-500"
                >
                  <span class="text-sm text-gray-500 md:text-base lg:text-lg">
                    {{ key }}
                  </span>
                  <select
                    v-if="key && children.length > 0"
                    v-model="product.optionVariant[index]"
                    class="right-0 float-right ml-2 h-[1.75rem] w-1/2 rounded-md border border-[#5E6875] pt-0"
                    @change="updateProductPrice"
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
            </div>
            <ProductAddToCartComponent
              v-if="product"
              class="hidden lg:flex"
              :product="product"
            />
          </div>
          <div
            v-if="product.seller.description"
            class="mt-[25px] h-[auto] rounded-lg bg-white p-5 md:p-7"
          >
            <h3 class="text-[19px] text-primary md:text-[25px] xl:text-[35px]">
              Livraison et retour
            </h3>
            <ul class="list-disc text-gray-500">
              <li class="mt-1 ml-7 text-sm md:text-base lg:text-lg">
                {{ product.seller.description }}
              </li>
            </ul>
          </div>
          <div
            class="mt-[20px] flex w-full flex-col rounded-lg bg-white p-5 text-center md:h-[158px] md:flex-row md:p-7 md:text-left"
          >
            <div class="flex justify-center md:w-[20%]">
              <img
                :src="helpImageFile"
                alt="Picture"
                class="h-[98px] w-[98px] items-center sm:mx-auto"
              />
            </div>
            <div class="flex flex-col justify-center md:w-[80%]">
              <h3 class="text-[19px] text-primary md:text-[25px]">
                Besoin d'aide pour votre commande ?
              </h3>
              <RouterLink
                :to="{ name: PageList.CONTACT_PAGE }"
                class="button button-gradient"
              >
                <ArrowRigntIconComponent
                  class="mt-1 mr-2 w-4 items-center"
                  :stroke-color="'#FFFFFF'"
                />
                Contactez notre Service Adhérents
              </RouterLink>
            </div>
          </div>
        </div>
        <!-- Fin Bloc détails produit -->
      </div>

      <!-- Bloc description -->
      <div class="mt-10 justify-center">
        <h3 class="home-subtitle mb-5 text-primary">Description</h3>
        <p
          class="whitespace-pre-line text-sm text-gray-500 md:text-base lg:text-lg"
          v-html="product.description"
        />
      </div>
      <!-- Fin Bloc description -->

      <!-- Bloc Caractéristiques techniques -->
      <div class="mt-10 justify-center">
        <h3 class="home-subtitle mb-5 text-primary">
          Caractéristiques techniques
        </h3>
        <table class="w-full table-auto border bg-white p-8">
          <tbody>
            <tr
              v-for="(property, key, index) in product.properties"
              :key="index"
              class="border text-sm text-primary md:text-base lg:text-lg"
              :class="{
                hidden:
                  property === 'home-top-vente' ||
                  property === 'home-selection',
              }"
            >
              <td class="w-[20%] border p-2">{{ key }}</td>
              <td class="p-2">
                <a v-if="isUrl(property)" :href="property" target="_blank">
                  Cliquez-ici</a
                >
                <span v-else>{{ property }}</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- Fin du bloc caractéristiques techniques -->

      <!-- Bloc produits similaire -->
      <div v-if="similarProducts.length > 0" class="mt-10 justify-center">
        <h3 class="home-subtitle text-primary">
          Produits de la même catégorie
        </h3>
        <ProductsCarouselComponent :products="similarProducts" class="mt-4" />
      </div>
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
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import { getImage, getUpplerImage, isUrl } from '@/vuejs/services/utils'
import helpImage from '@/vuejs/assets/img/samples/img-help-product.png'
import { computed, onMounted, ref, watch } from 'vue'
import { SwiperSlide } from 'swiper/vue'
import ArrowRigntIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import ProductAddToCartComponent from '@/vuejs/modules/products/components/ProductAddToCartComponent.vue'
import ProductTitleComponent from '@/vuejs/modules/products/components/ProductTitleComponent.vue'
import { useRoute } from 'vue-router'
import { useProductStore } from '@/vuejs/stores/product'
import { Product } from '@/vuejs/types/Product'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import { PageList } from '@/vuejs/router'
import { ProductPageList } from '@/vuejs/router/pages-list'
import AddFavoriteComponent from '@/vuejs/modules/products/components/AddFavoriteComponent.vue'
import { useFavoriteStore } from '@/vuejs/stores/favorite'
import ProductsCarouselComponent from '@/vuejs/modules/shared/ProductsCarouselComponent.vue'
import ProductQuantityComponent from '../../shared/ProductQuantityComponent.vue'
import LoadingComponent from '@/vuejs/modules/shared/LoadingComponent.vue'

const route = useRoute()
const productStore = useProductStore()
const favoriteStore = useFavoriteStore()
const helpImageFile = getImage(helpImage)

const thumbsSwiper = ref(null)
const isLoading = ref<boolean>(false)
const product = ref<Product>()
const isLoadingPrice = ref<boolean>(false)
const similarProducts = ref<Product[]>([])

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
          name: ProductPageList.PRODUCTS,
          query: { category: category.id, page: 1 },
        },
      })
    }
  }

  return breadcrumb
})

const setThumbsSwiper = (swiper) => {
  thumbsSwiper.value = swiper
}

const updateProductPrice = async () => {
  isLoadingPrice.value = true
  await productStore.changeVariant(product.value)
  isLoadingPrice.value = false
}

const productTitle = computed(() => {
  return product.value ? product.value.name + ' | ' : ''
})

const updateQuantity = (event) => {
  product.value.quantity = event.quantity
}

watch(
  () => route.params.id as string,
  async (id: string) => {
    isLoading.value = true
    try {
      const productId = id.split('-')
      const formattedProductId = parseInt(productId[productId.length - 1])
      product.value = await productStore.findProductById(formattedProductId)
      if (product.value.categories.length > 0) {
        const categoryId =
          product.value.categories[product.value.categories.length - 1].id
        similarProducts.value = await productStore.findSimilarProducts(
          categoryId,
          formattedProductId,
        )
      }

      isLoading.value = false
    } catch (error) {
    } finally {
      isLoading.value = false
    }
  },

  { immediate: true },
)
</script>

<style scoped></style>
