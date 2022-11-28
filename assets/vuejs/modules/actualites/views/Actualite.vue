<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div class="m-auto my-4 max-w-screen-2xl flex-1 sm:px-8 xs:w-[100%]">
      Actualité
    </div>
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
import ArrowRigntIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
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
