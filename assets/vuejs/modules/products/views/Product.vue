<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div
      v-if="isLoading"
      class="mt-5 flex h-20 w-full items-center justify-center"
    >
      <LoaderSharedComponent
        class="text-secondary"
        classes="loader-xl loader"
      />
    </div>
    <div
      v-else
      class="xs:w-[100%] m-auto my-4 max-w-screen-2xl flex-1 px-5 sm:px-8"
    >
      <BreadcrumbSharedComponent
        :list-url="breadcrumbUrl(product)"
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
              :thumbs="{ swiper: thumbsSwiper }"
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
            class="mt-5 flex flex-col rounded-lg bg-white p-5 md:mt-0 md:p-7"
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
            <div
              v-else
              class="mt-14 hidden flex-col lg:flex"
            >
              <div>
                <span
                  v-if="priceReference"
                  :class="{
                    'text-sm text-gray-500 line-through  md:text-base lg:text-lg':
                      price,
                    'text-[25px] font-bold text-primary':
                      product.price === null,
                  }"
                >{{ priceReference }}€ HT
                </span>
                <span
                  v-if="percent > 0"
                  class="ml-2 rounded-lg bg-secondary px-2.5 py-1.5 text-white"
                >{{ percent }}%</span
                >
              </div>
              <div
                v-if="price"
                class="mt-3 text-[25px] font-bold text-primary"
              >
                {{ price }}€ HT
              </div>
            </div>
            <div class="lg:mt-12">
              <div class="inline-flex items-center text-gray-500">
                <span class="text-sm text-gray-500 md:text-base lg:text-lg">
                  Quantité
                </span>
                <select
                  v-model="quantity"
                  class="ml-2 h-[1.75rem] rounded-md border border-[#5E6875] pt-0"
                >
                  <option v-for="i in 5" :key="i" :value="i">
                    {{ i }}
                  </option>
                </select>
                <!--     <div class="hidden lg:flex">
             <HeartIconComponent class="ml-5" :stroke-color="'#5E6875'" />
                  <a href="#" class="ml-5 font-bold underline"
                    >Ajouter ce produit à mes favoris</a
                  >
                </div>-->
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
                    v-if="children.length > 1"
                    v-model="optionVariant[index]"
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
              class="hidden lg:flex"
              :product="product"
              :price="price"
              :quantity="quantity"
              :variant-id="variantId"
            />
          </div>
          <div
            v-if="product.seller.description"
            class="mt-[25px] h-[auto] rounded-lg bg-white p-5 md:p-7"
          >
            <h3 class="text-[19px] text-primary md:text-[25px] xl:text-[35px]">
              Livraison et retour
            </h3>
            <ul
              class="list-disc text-gray-500"
            >
              <li
                class="mt-1 ml-7 text-sm md:text-base lg:text-lg"
              >
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
                :to="{ name: PageList.CONTACT_PAGE}"
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
                'hidden':
                  property === 'home-top-vente' ||
                  property === 'home-selection',
              }"
          >
            <td class="w-[20%] border p-2">{{ key }}</td>
            <td class="p-2">{{ property }}</td>
          </tr>
          </tbody>
        </table>
      </div>
      <!-- Fin Bloc Caractéristiques techniques -->

      <!-- Bloc produits similaire -->
      <!-- <div class="mt-10 justify-center">
        <h3 class="home-subtitle text-primary">Produits similaires</h3>
      </div> -->
      <!-- Fin bloc produits similaire -->
    </div>
  </BaseTemplate>
  <ProductAddToCartComponent
    class="z-10 flex lg:hidden"
    :product="product"
    :show-price="true"
    :quantity="quantity"
    :variant-id="variantId"
    :price="price"
    :price-reference="priceReference"
    :percent="percent"
  />
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import { formatPrice, getImage, getUpplerImage } from '@/vuejs/services/utils'
import helpImage from '@/vuejs/assets/img/samples/img-help-product.png'
import { ref, watch } from 'vue'
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

const route = useRoute()
const productStore = useProductStore()
const helpImageFile = getImage(helpImage)
const thumbsSwiper = ref(null)

const isLoading = ref<boolean>(false)
const quantity = ref<number>(1)
const product = ref<Product>()
const optionVariant = ref([])
const variants = ref([])
const priceReference = ref()
const price = ref()
const percent = ref()
const variantId = ref()
const isLoadingPrice = ref<boolean>(false)

const breadcrumbUrl = (product: Product | null) => {
  const breadcrumb = []
  if (product) {
    Object.entries(product.categories).forEach(([key, value], index) => {
      breadcrumb.push({
        id: key,
        name: value,
        url: { name: ProductPageList.PRODUCTS, query: { category: key, page: 1 } },
      })
    })
  }

  return breadcrumb
}

const setThumbsSwiper = (swiper) => {
  thumbsSwiper.value = swiper
}

const updateProductPrice = async () => {
  let variantSelected = null
  Object.entries(product.value.variants).find(([key, value], index) => {
    if (arrayEqual(value, optionVariant.value)) {
      variantSelected = key
    }
  })

  isLoadingPrice.value = true
  if (variantSelected) {
    variantId.value = parseInt(variantSelected)
    let variant = await variants.value.find(v => {
      if (v.id === variantId.value) {
        return v
      }
      return null
    })
    if (!variant) {
      variant = await productStore.findVariantById(variantId.value)
      variants.value.push(variant)
    }

    price.value = (variant.price.display_price / 100)
    const priceDiff = priceReference.value - parseFloat(price.value)
    percent.value = Math.round((priceDiff * 100) / priceReference.value)
  } else {
    variantId.value = null
  }
  isLoadingPrice.value = false
}

const arrayEqual = (arr1, arr2) => {
  if (arr1.length !== arr2.length) {
    return false
  }

  for (let i = 0; i < arr1.length; i++) {
    if (arr1[i] !== arr2[i]) {
      return false
    }
  }

  return true
}
watch(
  () => route.params.id as string,
  async (id: string) => {
    isLoading.value = true
    if (id) {
      product.value = await productStore.findProductById(id)
      priceReference.value = product.value.priceReference
      price.value = product.value.price?.displayPrice
      percent.value = product.value.percent
      optionVariant.value = Object.values(Object.values(product.value.variants)[0])
      variantId.value = parseInt(Object.keys(product.value.variants)[0])
      if (Object.keys(product.value.variants).length > 2) {
        variants.value.push(await productStore.findVariantById(variantId.value))
      }
    }
    isLoading.value = false
  },

  { immediate: true },
)
</script>

<style scoped></style>
