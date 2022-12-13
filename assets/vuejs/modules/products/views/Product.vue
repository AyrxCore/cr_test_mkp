<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div class="xs:w-[100%] m-auto my-4 max-w-screen-2xl flex-1 sm:px-8">
      <breadcrumb-shared-component
        :list-url="listUrl"
        :current-page="'Produit'"
      />
      <div class="w-[100%] max-w-screen-2xl flex justify-end">
        <ContactUsButtonComponent />
      </div>
      <div class="m-auto my-3.5 grid w-[100%] max-w-screen-2xl grid-cols-2 gap-4">
        <!-- Bloc image produit -->
        <div>
          <div class="relative">
            <CarouselListSharedComponent
              class="mx-auto h-[590px] items-center rounded-xl bg-white px-4"
              :slides-per-view="1"
              :space-between="20"
              :breakpoints="{
                640: {
                  slidesPerView: 1,
                  spaceBetween: 20,
                },
              }"
              :pagination="true"
              :navigation="false"
              :show-nav="false"
              :thumbs="{ swiper: thumbsSwiper }"
              @on-slide-change="onSlideChange"
            >
              <swiperSlide v-for="(image, key) in product.images" :key="key">
                <img
                  :src="image.img"
                  alt="Picture"
                  class="items-center sm:mx-auto"
                />
              </swiperSlide>
            </CarouselListSharedComponent>
          </div>
          <div class="relative">
            <CarouselListSharedComponent
              class="mx-auto h-[150px] items-center rounded-xl px-4 mt-5 py-4"
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
                  :src="image.img"
                  alt="Picture"
                  class="items-center bg-white rounded"
                />
              </swiperSlide>
            </CarouselListSharedComponent>
          </div>
        </div>
        <!-- Fin Bloc image produit -->

        <!-- Bloc détails produit -->
        <div>
          <div class="h-[658px] rounded-lg bg-white p-7">
            <h3 class="text-title-35 text-primary">{{ product.name }}</h3>
            <h4 class="mb-1.5 text-lg font-bold text-gray-500">
              Vendu par : <span class="uppercase">{{ product.partner }}</span>
            </h4>
            <span class="text-lg text-gray-500"
              >Référence : {{ product.reference }}
            </span>
            <div class="mt-14">
              <div>
                <span class="text-lg text-gray-500 line-through"
                  >{{ product.priceReduce }}€ HT
                </span>
                <span
                  class="ml-2 rounded-lg bg-purple-600 px-2.5 py-1.5 text-white"
                  >{{ product.percent }}%</span
                >
              </div>
              <div class="mt-3 text-[25px] font-bold text-primary">
                {{ product.price }}€ HT
              </div>
            </div>
            <div class="mt-12">
              <div class="inline-flex items-center text-gray-500">
                <span class="text-lg text-gray-500">Quantité</span>
                <select
                  class="ml-2 h-[1.75rem] rounded-md border border-[#5E6875] pt-0"
                >
                  <option v-for="i in 5" :key="i" value="{{i}}">
                    {{ i }}
                  </option>
                </select>
                <HeartIconComponent class="ml-5" :stroke-color="'#5E6875'" />
                <a href="#" class="ml-5 font-bold underline"
                  >Ajouter ce produit à mes favoris</a
                >
              </div>
              <p class="mt-1">
                <span class="text-lg text-gray-500"
                  >Conditionnement conseillé : {{ product.conditionnement }}
                </span>
              </p>
              <div class="mt-12 w-[50%]">
                <div
                  v-for="(attr, key) in productAttrubutes"
                  :key="key"
                  class="mt-2 w-full items-center text-gray-500"
                >
                  <span class="text-lg text-gray-500">{{ attr }}</span>
                  <select
                    class="right-0 float-right ml-2 h-[1.75rem] w-[25%] rounded-md border border-[#5E6875] pt-0"
                  >
                    <option></option>
                  </select>
                </div>
              </div>
            </div>
            <ButtonComponent class="button-gradient mt-14 w-full">
              <ShoppingCartIconComponent class="mr-2 w-4" /> Ajouter
            </ButtonComponent>
          </div>
          <div class="mt-[25px] h-[auto] rounded-lg bg-white p-7">
            <h3 class="text-[35px] text-primary">Livraison et retour</h3>
            <ul class="list-disc text-gray-500">
              <li v-for="(livraison, key) in product.livraison" :key="key" class="mt-1 ml-7 text-lg">
                {{ livraison.label }}
              </li>
            </ul>
          </div>
          <div
            class="mt-[20px] inline-flex h-[158px] w-[100%] rounded-lg bg-white p-7"
          >
            <div class="w-[20%]">
              <img
                :src="helpImageFile"
                alt="Picture"
                class="h-[98px] w-[98px] items-center sm:mx-auto"
              />
            </div>
            <div class="w-[80%]">
              <h3 class="text-[25px] text-primary">
                Besoin d'aide pour votre commande ?
              </h3>
              <a
                href="#"
                class="default-button-gradient mt-2 inline-flex justify-center px-3.5 py-3 text-center font-bold text-white"
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
        <p class="text-lg text-gray-500 whitespace-pre-line">
          {{ product.description }}
        </p>
        <div class="mt-[60px] flex">
          <div class="mr-2 h-[180px] rounded-lg bg-white p-7">
            <h3 class="inline-flex text-[25px] text-primary">
              Certifications et éco-label
              <LeafIconComponent class="ml-2 items-center" />
            </h3>
            <ul class="list-disc text-gray-500">
              <li v-for="i in 3" :key="i" class="mt-1 ml-7 text-lg">
                Curabitur ac sem at enim convallis consectetur
              </li>
            </ul>
          </div>
          <div class="h-[180px] rounded-lg bg-white p-7">
            <h3 class="text-[25px] text-primary">Documentation</h3>
            <ul class="list-disc text-gray-500">
              <li
                v-for="(doc, key) in documentation"
                :key="key"
                class="mt-1 ml-7 text-lg"
              >
                <a href="#" class="underline">{{ doc }}</a>
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
              v-for="(caracteristique, key) in product.caracteristiques"
              :key="key"
              class="border text-lg text-primary"
            >
              <td class="w-[20%] border p-2">{{ caracteristique.name }}</td>
              <td class="p-2">{{ caracteristique.value }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- Fin Bloc Caractéristiques techniques -->

      <!-- Bloc produits similaire -->
      <div class="mt-10 justify-center">
        <h3 class="home-subtitle text-primary">Produits similaires</h3>
        <ProductsCarouselComponent :products="productsSimilaire" class="mt-4"/>
      </div>
      <!-- Fiin bloc produits similaire -->
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import { getImage } from '@/vuejs/services/utils'
import defaultImage from '@/vuejs/assets/img/default-image.png'
import helpImage from '@/vuejs/assets/img/samples/img-help-product.png'
import { ref } from 'vue'
import { SwiperSlide } from 'swiper/vue'
import HeartIconComponent from '@/vuejs/modules/shared/icon/HeartIconComponent.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import ShoppingCartIconComponent from '@/vuejs/modules/shared/icon/ShoppingCartIconComponent.vue'
import ArrowRigntIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import LeafIconComponent from '@/vuejs/modules/shared/icon/LeafIconComponent.vue'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import { product, productsSimilaire } from '@/vuejs/modules/products'
import ProductsCarouselComponent from '@/vuejs/modules/shared/ProductsCarouselComponent.vue'

const helpImageFile = getImage(helpImage)
const thumbsSwiper = ref(null)


const productAttrubutes = ref(['Taille', 'Couleur', 'Autre propriété produit'])

const listUrl = ref([
  {
    name: 'Catégories',
  },
  {
    name: 'Sous Catégories',
  },
])

const documentation = ref([
  'Fiche produit',
  'Fiche technique',
  "Guid d'utilisation",
])

const onSlideChange = () => {
  console.log('slide change depuis le produit')
}

const setThumbsSwiper = (swiper) => {
  thumbsSwiper.value = swiper
}
</script>

<style scoped></style>
