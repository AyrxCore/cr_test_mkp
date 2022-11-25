<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div class="m-auto my-4 max-w-screen-2xl flex-1 sm:px-8 xs:w-[100%]">
      <nav class="bg-grey-light rounded-md w-full text-xs ">
        <ol class="list-reset flex text-xs text-cotext">
          <li><a href="/app/home" class="text-gray-500">Accueil</a></li>
          <li><ChevronRightIconComponent class="text-gray-500 mx-1 w-4 h-4" /></li>
          <li><a href="#" class="text-gray-500 ">Catégories</a></li>
          <li><ChevronRightIconComponent class="text-gray-500 mx-1 w-4 h-4" /></li>
          <li><a href="#" class="text-gray-500 ">Sous catégories</a></li>
          <li><ChevronRightIconComponent class="text-gray-500 mx-1 w-4 h-4" /></li>
          <li class="text-gray-500">Produit</li>
        </ol>
      </nav>
      <div class="w-[100%] max-w-screen-2xl">
        <PurpleButtonComponent class="rounded-full right-0 float-right">
          <MailIconLightComponent class="mr-1" />
          <PhoneLightIconComponent class="mr-1" />
          Contactez-nous
        </PurpleButtonComponent>
      </div>
      <div class="m-auto w-[100%] my-2 grid grid-cols-2 gap-4 max-w-screen-2xl">
        <!-- Bloc image produit -->
        <div>
          <CarouselListSharedComponent
            class=" px-4 h-[590px] items-center mx-auto rounded-xl bg-white"
            :slides-per-view="1"
            :space-between="10"
            :pagination="true"
            :thumbs="{ swiper: thumbsSwiper }"
            @on-slide-change="onSlideChange"
          >
            <swiper-slide v-for="i in nbImageToShow" :key="i">
              <img :src="defaultImageFile" alt="Picture" class="sm:mx-auto items-center"/>
            </swiper-slide>
          </CarouselListSharedComponent>
          <CarouselListSharedComponent
            class=" px-4 py-4 h-[150px] items-center mx-auto rounded-xl"
            :space-between="10"
            watch-slides-progress
            :pagination="false"
            :navigation="false"
            @on-slide-change="onSlideChange"
            @swiper="setThumbsSwiper"
          >
            <swiper-slide v-for="i in nbImageToShow" :key="i">
              <img :src="defaultImageFile" alt="Picture" class="sm:mx-auto items-center"/>
            </swiper-slide>
          </CarouselListSharedComponent>
        </div>
        <!-- Fin Bloc image produit -->

        <!-- Bloc détails produit -->
        <div>
          <div class="bg-white rounded-lg p-7 text-cotext h-[658px]">
            <h3 class=" primary text-[35px]">{{product.name}}</h3>
            <h4 class="text-gray-500 font-bold text-lg mb-1.5">Vendu par : <span class="uppercase">{{product.partner}}</span> </h4>
            <span class="text-gray-500 text-lg">Référence : {{product.reference}} </span>
            <div class="text-cotext mt-14">
              <div>
                <span class="text-gray-500 line-through  text-lg">{{product.priceReduce}}€ HT </span>
                <span class="px-2.5 py-1.5 bg-purple-600 text-white rounded-lg ml-2">{{product.percent}}%</span>
              </div>
              <div class="primary text-[25px] mt-3 font-bold">{{product.price}}€ HT</div>
            </div>
            <div class="text-cotext mt-12">
              <div class="text-gray-500 inline-flex items-center">
                <span class="text-gray-500 text-lg">Quantité</span>
                <select class="rounded-md border border-[#5E6875] ml-2 h-[1.75rem] pt-0">
                  <option
                    v-for="i in 5" :key="i"
                    value="{{i}}">
                    {{ i }}
                  </option>
                </select>
                <HeartIconComponent class="ml-5" :stroke-color="'#5E6875'" />
                <a href="#" class="font-bold underline ml-5">Ajouter ce produit à mes favoris</a>
              </div>
              <p class="mt-1">
                <span class="text-gray-500 text-lg">Conditionnement conseillé : {{product.conditionnement}} </span>
              </p>
              <div class="mt-12 w-[50%]">
                <div v-for="(attr, key) in productAttrubutes" :key="key" class="text-gray-500 items-center mt-2 w-full">
                  <span class="text-gray-500 text-lg">{{attr}}</span>
                  <select class="rounded-md border border-[#5E6875] ml-2 h-[1.75rem] pt-0 right-0 float-right w-[25%]">
                    <option></option>
                  </select>
                </div>
              </div>
            </div>
            <DefaultButtonComponent type="button" class="mt-14 w-[50%] text-center justify-center">
              <ShoppingCartIconComponent class="mr-2 w-4" /> Ajouter
            </DefaultButtonComponent>
          </div>
          <div class="bg-white rounded-lg p-7 text-cotext h-[auto] mt-[25px]">
            <h3 class=" primary text-[35px]">Livraison et retour</h3>
            <ul class="list-disc text-gray-500 text-cotext">
              <li v-for="i in 3" :key="i" class="mt-1 ml-7 text-lg">
                Curabitur ac sem at enim convallis consectetur quis sed urabitur
              </li>
            </ul>
          </div>
          <div class="bg-white rounded-lg p-7 text-cotext h-[158px] mt-[20px] w-[100%] text-cotext inline-flex">
            <div class="w-[20%]">
              <img :src="helpImageFile" alt="Picture" class="sm:mx-auto items-center w-[98px] h-[98px]"/>
            </div>
            <div class="w-[80%]">
              <h3 class=" primary text-[25px]">Besoin d'aide pour votre commande ?</h3>
              <a href="#" class="mt-2 text-center justify-center font-bold default-button-gradient px-3.5 py-3 text-white inline-flex">
                <ArrowRigntIconComponent class="mt-1 mr-2 w-4 items-center" :stroke-color="'#FFFFFF'"/> Contactez notre Service Adhérents
              </a>
            </div>
          </div>
        </div>
        <!-- Fin Bloc détails produit -->
      </div>

      <!-- Bloc description -->
      <div class=" mt-10 justify-center">
        <h3 class="primary home-subtitle mb-5">Description</h3>
        <p class="text-gray-500 text-cotext text-lg">
          Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur ac sem at enim convallis consectetur quis sed diam.
          Curabitur consequat sagittis tempus. Nulla mollis felis erat, non tincidunt ligula mattis vulputate. Aenean cursus dictum tempor.
          Proin sit amet quam in diam tempor cursus. Curabitur aliquet ut odio at vehicula. Donec tristique gravida tristique.
          Sed ullam interdum vestibulum. Proin eu tincidunt justo.
        </p>
        <div class="flex mt-[60px]">
          <div class="bg-white rounded-lg p-7 text-cotext h-[180px] mr-2">
            <h3 class=" primary text-[25px] inline-flex">Certifications et éco-label <LeafIconComponent class="ml-2 items-center"/></h3>
            <ul class="list-disc text-gray-500 text-cotext">
              <li v-for="i in 3" :key="i" class="mt-1 ml-7 text-lg">
                Curabitur ac sem at enim convallis consectetur
              </li>
            </ul>
          </div>
          <div class="bg-white rounded-lg p-7 text-cotext h-[180px] ">
            <h3 class=" primary text-[25px]">Documentation</h3>
            <ul class="list-disc text-gray-500 text-cotext">
              <li v-for="(doc, key) in documentation" :key="key" class="mt-1 ml-7 text-lg">
                <a href="#" class="underline">{{doc}}</a>
              </li>
            </ul>
          </div>
        </div>

      </div>
      <!-- Fin Bloc description -->

      <!-- Bloc Caractéristiques techniques -->
      <div class=" mt-10 justify-center">
        <h3 class="primary home-subtitle mb-5">Caractéristiques techniques</h3>
        <table class="table-auto w-full bg-white p-8 border">
          <tbody>
            <tr v-for="(caracteristique, key) in product.caracteristiques" :key="key" class="border text-lg text-cotext primary">
              <td class="border w-[20%] p-2">{{caracteristique.name}}</td>
              <td class="p-2">{{caracteristique.value}}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- Fin Bloc Caractéristiques techniques -->

      <!-- Bloc produis similaire -->
      <div class=" mt-10 justify-center">
        <h3 class="primary home-subtitle">Produits similaires</h3>
        <CarouselListSharedComponent
          class="mt-2 h-[450px] items-center mx-auto rounded-xl px-[50px!important]"
        >
          <swiper-slide v-for="(prod, key) in products" :key="key">
            <ProductComponent :product="prod"/>
          </swiper-slide>
        </CarouselListSharedComponent>
      </div>
    </div>
    <!-- Fiin bloc produis similaire -->
  </BaseTemplate>
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import ChevronRightIconComponent from '@/vuejs/modules/shared/icon/ChevronRightIconComponent.vue'
import { getImage } from '@/vuejs/services/utils'
import defaultImage from '@/vuejs/assets/img/default-image.png'
import helpImage from '@/vuejs/assets/img/samples/img-help-product.png'
import { ref } from 'vue'
import { SwiperSlide } from 'swiper/vue'
import ProductComponent from '@/vuejs/modules/products/components/ProductComponent.vue'
import PurpleButtonComponent from '@/vuejs/modules/shared/PurpleButtonComponent.vue'
import PhoneLightIconComponent from '@/vuejs/modules/shared/icon/PhoneLightIconComponent.vue'
import MailIconLightComponent from '@/vuejs/modules/shared/icon/MailIconLightComponent.vue'
import HeartIconComponent from '@/vuejs/modules/shared/icon/HeartIconComponent.vue'
import DefaultButtonComponent from '@/vuejs/modules/shared/DefaultButtonComponent.vue'
import ShoppingCartIconComponent from '@/vuejs/modules/shared/icon/ShoppingCartIconComponent.vue'
import ArrowRigntIconComponent from '@/vuejs/modules/shared/icon/ArrowRigntIconComponent.vue'
import LeafIconComponent from '@/vuejs/modules/shared/icon/LeafIconComponent.vue';

const defaultImageFile = getImage(defaultImage)
const helpImageFile = getImage(helpImage)
const nbImageToShow = 5
const thumbsSwiper = ref(null)

const products = ref([
  { imgFrn: defaultImageFile, imgProduct: defaultImageFile},
  { imgFrn: defaultImageFile, imgProduct: defaultImageFile},
  { imgFrn: defaultImageFile, imgProduct: defaultImageFile},
  { imgFrn: defaultImageFile, imgProduct: defaultImageFile},
])

const product = ref({
  name: 'Nom du produit',
  partner: 'Partenaire',
  reference: 'XXXXXXXXXX',
  price: '29.90',
  priceReduce: '19.90',
  percent: 'XX',
  conditionnement: 'XXXXXXXX',
  caracteristiques: [
    {
      name: 'Marque',
      value: 'Qantis',
    },
    {
      name: 'Nom du produit',
      value: 'Test nom produit',
    },
    {
      name: 'Poids',
      value: '125 KG',
    },
  ]
})

const productAttrubutes = ref ([
  'Taille',
  'Couleur',
  'Autre propriété produit',
])

const documentation = ref ([
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
