<template>
  <BaseTemplate  title="Qantis - MarketPlace">
    <div
      v-if="product"
      class="xs:w-[100%] m-auto my-4 max-w-screen-2xl flex-1 px-5 sm:px-8"
    >
      <breadcrumb-shared-component
        :list-url="breadcrumbUrl"
        :current-page="product.name"
      />
      <div class="w-[100%] max-w-screen-2xl justify-end">
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
            <template #partner> {{ product.company.name }}</template>
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
              @on-slide-change="onSlideChange"
            >
              <swiperSlide
                v-for="(image, key) in product.images"
                :key="key"
                class="md:h-auto flex items-center justify-center"
              >
                <img
                  :src="image"
                  alt="Picture"
                  class="items-center sm:mx-auto h-auto"
                />
              </swiperSlide>
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
              @on-slide-change="onSlideChange"
              @swiper="setThumbsSwiper"
            >
              <swiperSlide v-for="(image, key) in product.images" :key="key">
                <img
                  :src="image"
                  alt="Picture"
                  class="items-center rounded bg-white rounded-xl"
                />
              </swiperSlide>
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
              <template #partner> {{ product.company.name }}</template>
              <template #reference> {{ product.reference }}</template>
            </ProductTitleComponent>
            <div class="mt-14 hidden flex-col lg:flex">
              <div>
                <span
                  v-if="product.priceReference"
                  class="text-sm text-gray-500 line-through md:text-base lg:text-lg"
                  >{{ product.priceReference }} HT
                </span>
                <span
                  v-if="product.percent > 0"
                  class="ml-2 rounded-lg bg-purple-600 px-2.5 py-1.5 text-white"
                  >{{ product.percent }}%</span
                >
              </div>
              <div
                v-if="product.price?.formattedDisplayPrice"
                class="mt-3 text-[25px] font-bold text-primary">
                {{ product.price?.formattedDisplayPrice }} HT
              </div>
            </div>
            <div class="lg:mt-12">
              <div class="inline-flex items-center text-gray-500">
                <span class="text-sm text-gray-500 md:text-base lg:text-lg"
                  >Quantité</span
                >
                <select
                  class="ml-2 h-[1.75rem] rounded-md border border-[#5E6875] pt-0"
                >
                  <option v-for="i in 5" :key="i" value="{{i}}">
                    {{ i }}
                  </option>
                </select>
                <div class="hidden lg:flex">
                  <HeartIconComponent class="ml-5" :stroke-color="'#5E6875'" />
                  <a href="#" class="ml-5 font-bold underline"
                    >Ajouter ce produit à mes favoris</a
                  >
                </div>
              </div>
              <p class="mt-1">
                <span class="text-sm text-gray-500 md:text-base lg:text-lg"
                  >Conditionnement conseillé : {{ product.conditionnement }}
                </span>
              </p>
              <div class="mt-12">
                <div
                  v-for="(children, key) in product.options"
                  :key="key"
                  class="mt-2 w-full items-center text-gray-500"
                >
                  <span class="text-sm text-gray-500 md:text-base lg:text-lg">{{
                      key
                  }}</span>
                  <select
                    v-if="children.length > 1"
                    class="right-0 float-right ml-2 h-[1.75rem] w-1/2 rounded-md border border-[#5E6875] pt-0"
                  >
                    <option v-for="child in children" :key="child.id">
                        {{ child.value }}
                    </option>
                  </select>
                </div>
              </div>
            </div>
            <ButtonAddToCartComponent class="hidden lg:flex" />
          </div>
          <div class="mt-[25px] h-[auto] rounded-lg bg-white p-5 md:p-7">
            <h3 class="text-[19px] text-primary md:text-[25px] xl:text-[35px]">
              Livraison et retour
            </h3>
            <ul class="list-disc text-gray-500">
              <li
                v-for="(livraison, key) in product.livraisons"
                :key="key"
                class="mt-1 ml-7 text-sm md:text-base lg:text-lg"
              >
                {{ livraison }}
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
              <a
                href="#"
                class="default-button-gradient mt-2 inline-flex justify-center
                 px-3.5 py-3 text-center text-sm font-bold text-white md:text-base lg:text-lg"
              >
                <ArrowRigntIconComponent
                  class="mt-1 mr-2 w-4 items-center"
                  :stroke-color="'#FFFFFF'"
                />
                Contactez notre Service Adhérents
              </a>
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
        <div class="flex flex-col md:mt-[60px] md:flex-row">
          <div
            class="mt-5 rounded-lg bg-white p-5 md:mt-0 md:mr-2 lg:h-[180px] lg:p-7"
          >
            <h3 class="inline-flex text-[19px] text-primary md:text-[25px]">
              Certifications et éco-label
              <LeafIconComponent class="ml-2 items-center" />
            </h3>
            <ul class="list-disc text-gray-500">
              <li
                v-for="i in 3"
                :key="i"
                class="mt-1 ml-7 text-sm md:text-base lg:text-lg"
              >
                Curabitur ac sem at enim convallis consectetur
              </li>
            </ul>
          </div>
          <div class="mt-5 rounded-lg bg-white p-7 md:mt-0 lg:h-[180px]">
            <h3 class="text-[19px] text-primary md:text-[25px]">
              Documentation
            </h3>
            <ul class="list-disc text-gray-500">
              <li
                v-for="(documentation, key) in documentations"
                :key="key"
                class="mt-1 ml-7 text-sm md:text-base lg:text-lg"
              >
                <a href="#" class="underline">{{ documentation }}</a>
              </li>
            </ul>
          </div>
        </div>
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
              v-for="property in product.properties"
              :key="property.id"
              class="border text-sm text-primary md:text-base lg:text-lg"
            >
              <td class="w-[20%] border p-2">{{ property.name }}</td>
              <td class="p-2">{{ property.value }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- Fin Bloc Caractéristiques techniques -->

      <!-- Bloc produits similaire -->
      <!-- <div class="mt-10 justify-center">
        <h3 class="home-subtitle text-primary">Produits similaires</h3>
      </div> -->
      <!-- Fiin bloc produits similaire -->
    </div>
  </BaseTemplate>
  <ButtonAddToCartComponent :product="product" class="z-10 flex lg:hidden" />
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import { getImage } from '@/vuejs/services/utils'
import helpImage from '@/vuejs/assets/img/samples/img-help-product.png'
import { computed, ref, watch } from 'vue'
import { SwiperSlide } from 'swiper/vue'
import HeartIconComponent from '@/vuejs/modules/shared/icon/HeartIconComponent.vue'
import ArrowRigntIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import LeafIconComponent from '@/vuejs/modules/shared/icon/LeafIconComponent.vue'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import ButtonAddToCartComponent from '@/vuejs/modules/products/components/ButtonAddToCartComponent.vue'
import ProductTitleComponent from '@/vuejs/modules/products/components/ProductTitleComponent.vue'
import { useRoute } from 'vue-router'
import { useProductStore } from '@/vuejs/stores/product'
import Product from '@/vuejs/modules/products/views/Product.vue'


const route = useRoute()
const productStore = useProductStore()
const helpImageFile = getImage(helpImage)
const thumbsSwiper = ref(null)

const product = ref<Product>()

const breadcrumbUrl = computed(() => {
  const breadcrumb = []
  if (product.value) {
    Object.entries(product.value.categories).forEach(([key, value], index) => {
      breadcrumb.push({
        id: key,
        name: value,
      })
    })
  }

  return breadcrumb
})

const documentations = ref([
  'Fiche produit',
  'Fiche technique',
  "Guide d'utilisation",
])

const onSlideChange = () => {
  console.log('slide change depuis le produit')
}

const setThumbsSwiper = (swiper) => {
  thumbsSwiper.value = swiper
}

watch(
  () => route.params.id as string,
  async (id: string) => {
        if (id) product.value = await productStore.findProductById(id)
      },
  { immediate: true },
)
</script>

<style scoped></style>
