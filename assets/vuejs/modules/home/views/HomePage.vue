
<template>
  <BaseTemplate
    title="Qantis - MarketPlace"
  >
    <div class="m-auto my-4 max-w-screen-2xl flex-1 sm:p-8 xs:w-[100%]">
      <div CLASS="flex md:justify-between text-cotext mt-7">
        <div class="md:w-[52rem] sm:w-[100%]">
          <h3 class="primary home-title">Bienvenue sur la
            <span class=" text-transparent bg-clip-text bg-gradient-to-r from-purple-500 via-blue-500 to-cyan-500">
            marketplace réservée</span>
            à nos 30 000 entreprises adhérentes
          </h3>
          <p class="mt-5 text-gray-400 text-lg">
            Cher adhérent, ce nouvel espace dédié vous permet d'acheter directement en
            ligne et de trouver vos accords-cadres, en quelques clics. Notre équipe,
            que vous connaissez, se tient à votre disposition pour répondre à toutes vos questions.
          </p>
        </div>
        <purple-button-component class="rounded-full">
          <MailIconLightComponent class="mr-1" />
          <PhoneLightIconComponent class="mr-1" />
          Contactez-nous
        </purple-button-component>
      </div>
      <div class="flex justify-center">
        <CarouselListSharedComponent
          class="mt-10 px-4 py-4 h-[303px] items-center mx-auto rounded-xl bg-white"
          :slides-per-view="1"
          :mobile-slides-per-view="1"
          :space-between="10"
          :pagination="true"
        >
          <swiper-slide v-for="i in 5" :key="i">
            <img :src="defaultImageFile" alt="Picture" class="sm:mx-auto items-center"/>
          </swiper-slide>
        </CarouselListSharedComponent>
      </div>
      <div class="w-[45rem] mt-10">
        <h3 class="primary home-subtitle">Notre sélection pour vous</h3>
        <p class="text-gray-400 text-lg text-cotext">
          Savez-vous que vous pouvez désormais acheter ces produits en quelques clics ?
        </p>
      </div>
      <div class="flex justify-center">
        <CarouselListSharedComponent
          class="mt-2 h-[450px] items-center mx-auto rounded-xl px-[50px!important]"
          :slides-per-view="4"
          :space-between="50"
          navigation=""
        >
          <swiper-slide v-for="(product, key) in products" :key="key">
            <ProductComponent :product="product"/>
          </swiper-slide>
        </CarouselListSharedComponent>
      </div>
      <div class="w-[45rem] mt-10">
        <h3 class="primary home-subtitle">Top ventes</h3>
        <p class="text-gray-400 text-lg text-cotext">
          D'autres adhérents ont déjà acheté ces produits
        </p>
      </div>
      <div class="flex justify-center">
        <CarouselListSharedComponent
          class="mt-2 h-[450px] items-center mx-auto rounded-xl px-[50px!important]"
          :slides-per-view="4"
        >
          <swiper-slide v-for="(product, key) in products2" :key="key">
            <ProductComponent :product="product"/>
          </swiper-slide>
        </CarouselListSharedComponent>
      </div>

      <div class="mt-10">
        <h3 class="primary home-subtitle">Les accords-cadres incontournables</h3>
        <p class="text-gray-400 text-lg text-cotext">
          Etes-vous certains de profiter des touts les économies incluses dans votre adhésion ?
        </p>
      </div>
      <div class="flex justify-center">
        <CarouselListSharedComponent
          class="mt-2 h-[470px] items-center mx-auto rounded-xl px-[50px!important]"
          :slides-per-view="4"
        >
          <swiper-slide v-for="(accord, key) in accords" :key="key">
            <div class="items-center mx-auto px-8  py-4 rounded-md  bg-purple-600 h-full">
              <div class="items-center mx-auto ">
                <img :src="accord.img" alt="Image produit" class="mx-auto h-[273px!important] w-[340px!important]"/>
              </div>
              <p class="text-left text-white mt-1 text-lg text-cotext">
                Découvrez ou téléchargez les conditions négociées de ce partenaire
              </p>

              <div class="mx-auto bg-white p-2 rounded-full mt-7 text-[#9553FF]">
                <a href="#" >
                  <i class="fas fa-arrow-right mr-2" /> Découvrir l'accord cadre
                </a>
              </div>
            </div>
          </swiper-slide>
        </CarouselListSharedComponent>
      </div>
      <div class="mt-10">
        <h3 class="primary home-subtitle">Nos partenaires fournisseurs</h3>
        <p class="text-gray-400 text-lg text-cotext">
          Plus de 200 partenaires fournisseurs, repartis en 26 catégories, sont référencés pour vos achats.
        </p>
      </div>
      <CarouselListSharedComponent
        class="mt-10 px-6 py-4 h-[107px] items-center mx-auto rounded-xl px-[50px!important]"
        :slides-per-view="8"
        :mobile-slides-per-view="4"
        :space-between="50"
      >
        <swiper-slide v-for="(partenaire, key) in partenaires" :key="key">
          <img
            :src="partenaire.img"
            :alt="partenaire.alt"
            class="sm:mx-auto items-center w-[180px!important] h-[107px!important] rounded-lg" />
        </swiper-slide>
      </CarouselListSharedComponent>

      <div class="mt-16 text-center">
        <h3 class="primary text-cotext text-[35px] font-bold">
          Nos catégories de produits et d'accords-cadres
        </h3>
      </div>
      <div class="inline-block w-[100%] text-center text-cotext h-[205px] text-lg mt-5">
      <span
        v-for="(categorie, id) in listCategories"
        :key="id" class="items-start bg-[#404FE6] rounded px-1.5 py-2.5 text-white  mr-6 leading-[3.5rem]"
      >
        {{ categorie }}
      </span>
      </div>
    </div>

    <div class="m-auto mt-16  flex-1 h-[338px] home-bloc-economie py-4 text-white">
      <div class="text-center">
        <h3 class="text-cotext text-[35px] font-bold">
          Vous faites des économies tout en <br /> contribuant à votre démarche RSE
        </h3>
        <p class="text-cotext text-lg w-[45%] mx-auto mt-2">
          Nos adhérents réalisent en moyenne 27 % d'économies, grâce à la mutualisation des achats.
          Nous notons et référençons nos partenaires fournisseurs à l’aide d’un référentiel RSE.
          Votre adhésion permet aussi de contribuer à la démarche RSE de votre entreprise.
        </p>
        <p class="mt-10 text-cotext">
          <a href="#" class="default-button-gradient px-3.5 py-3 ">
            <i class="fas fa-arrow-right mr-2" /> Toutes nos catégories d'achats
          </a>
        </p>

      </div>
    </div>

    <div class="m-auto my-6 max-w-screen-2xl flex-1 rounded-md shadow-md h-[634px] mt-10 bg-white">
      <div class="text-center pt-1">
        <h3 class="text-cotext text-[35px] font-bold primary mt-10">
          Contenus experts spécialement conçus <br /> pour la
          <span class=" text-transparent bg-clip-text bg-gradient-to-r from-purple-500 via-blue-500 to-cyan-500">
            communauté QANTIS
          </span>
        </h3>
        <div class="grid grid-cols-3 gap-4 mt-5 p-8">
          <div
            v-for="(contenu, id) in contenusExpert"
               :key="id" class="grid grid-cols-2 text-cotext">
            <div class="flex rounded-lg border-[#F3EDFE]">
              <img :src="contenu.img" alt="Picture" class="sm:mx-auto items-center"/>
            </div>
            <div class="text-left px-6">
              <h3 class="font-bold text-[23px] primary">{{contenu.titre}}</h3>
              <p class="mt-2">
                <a href="#" class="purple-button mr-2 mb-2 flex items-center rounded-md px-5 py-2.5 text-sm font-medium text-white">
                  {{contenu.btnNam}}
                </a>
              </p>
              <p class="text-lg">
                {{contenu.desc}}
              </p>
              <div class="bottom-0">
                <a href="#" class="flex items-center bottom-0 text-sm font-medium underline primary">
                  Lire l'article
                </a>
              </div>
            </div>
          </div>
        </div>
        <p class="mt-10 text-cotext">
          <a href="#" class="default-button-gradient px-3.5 py-3 text-white">
            <i class="fas fa-arrow-right mr-2" /> Toutes les contenus experts
          </a>
        </p>

      </div>
    </div>

  </BaseTemplate>
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import { getImage, listCategories } from '@/vuejs/services/utils'
import defaultImage from '@/vuejs/assets/img/default-image.png'
import { SwiperSlide } from 'swiper/vue'
import PurpleButtonComponent from '@/vuejs/modules/shared/PurpleButtonComponent.vue'
import ProductComponent from '@/vuejs/modules/products/components/ProductComponent.vue'
import { ref } from 'vue'
import imgCheville from '@/vuejs/assets/img/samples/cheville.png'
import imgAmpoules from '@/vuejs/assets/img/samples/ampoules.png'
import imgTapis from '@/vuejs/assets/img/samples/tapis.png'
import imgGomme from '@/vuejs/assets/img/samples/gomme.png'

import imgFrn1 from '@/vuejs/assets/img/samples/berner.png'
import imgFrn2 from '@/vuejs/assets/img/samples/mieko.png'
import imgFrn3 from '@/vuejs/assets/img/samples/sign.png'
import imgFrn4 from '@/vuejs/assets/img/samples/jpg.png'

import imgPeugeot from '@/vuejs/assets/img/samples/peugeot.png'
import imgLoxam from '@/vuejs/assets/img/samples/loxam.png'
import imgBusiness from '@/vuejs/assets/img/samples/business.png'
import imgAdecco from '@/vuejs/assets/img/samples/adecco.png'
import imgHertz from '@/vuejs/assets/img/samples/hertz.png'
import imgKiloutou from '@/vuejs/assets/img/samples/kiloutou.png'
import imgLdlc from '@/vuejs/assets/img/samples/ldlc.png'
import imgRenault from '@/vuejs/assets/img/samples/renault.png'
import imgWurth from '@/vuejs/assets/img/samples/wurth.png'

import imgMaps from '@/vuejs/assets/img/samples/img-maps.png'
import imgCentral from '@/vuejs/assets/img/samples/img-central.png'
import imgMagazine from '@/vuejs/assets/img/samples/img-magazine.png'
import MailIconComponent from '@/vuejs/modules/shared/icon/MailIconComponent.vue';
import PhoneIconComponent from '@/vuejs/modules/shared/icon/PhoneIconComponent.vue';
import MailIconLightComponent from '@/vuejs/modules/shared/icon/MailIconLightComponent.vue';
import PhoneLightIconComponent from '@/vuejs/modules/shared/icon/PhoneLightIconComponent.vue';

const defaultImageFile = getImage(defaultImage)

const products = ref([
  { imgFrn: getImage(imgFrn1), imgProduct: getImage(imgCheville)},
  { imgFrn: getImage(imgFrn2), imgProduct: getImage(imgAmpoules)},
  { imgFrn: getImage(imgFrn3), imgProduct: getImage(imgTapis)},
  { imgFrn: getImage(imgFrn4), imgProduct: getImage(imgGomme)},
])

const products2 = ref([
  { imgFrn: getImage(defaultImageFile), imgProduct: getImage(defaultImageFile)},
  { imgFrn: getImage(defaultImageFile), imgProduct: getImage(defaultImageFile)},
  { imgFrn: getImage(defaultImageFile), imgProduct: getImage(defaultImageFile)},
  { imgFrn: getImage(defaultImageFile), imgProduct: getImage(defaultImageFile)},
])

const accords = ref([
  { img: getImage(imgPeugeot), alt: 'Peugeot'},
  { img: getImage(imgLoxam), alt: 'Loxam'},
  { img: getImage(imgBusiness), alt: 'Business'},
  { img: getImage(imgAdecco), alt: 'Adecco'},
])

const partenaires = ref([
  { img: getImage(imgPeugeot), alt: 'Peugeot'},
  { img: getImage(imgRenault), alt: 'Renault'},
  { img: getImage(imgLoxam), alt: 'Loxam'},
  { img: getImage(imgKiloutou), alt: 'Kiloutou'},
  { img: getImage(imgAdecco), alt: 'Adecco'},
  { img: getImage(imgWurth), alt: 'Wurth'},
  { img: getImage(imgLdlc), alt: 'LDLC Pro'},
  { img: getImage(imgHertz), alt: 'Herts'},
])

const contenusExpert = ref([
  { img: getImage(imgMaps), titre: 'Loi montagne : êtes-vous concernés ? ', btnNam: 'Actualités', desc: 'A partir du 1er novembre 2022, la loi Montagne...'},
  { img: getImage(imgCentral), titre: 'Tout savoir sur le décret tertiaire', btnNam: 'Actualités', desc: 'Le décret tertiaire impose une réduction \nde consommation...'},
  { img: getImage(imgMagazine), titre: 'Comment entretenir votre véhicule ? ', btnNam: 'Conseil', desc: 'Les équipes de QANTIS ont conçu pour vous un guide...'},
])
</script>


<style lang="scss">
@import 'assets/style/_variables.scss';

.home-bloc-economie {
  background: linear-gradient(87.3deg, #050056 0%, #404FE6 100%);
  width: 100%;
}

.home-title {
  font-family: CoText, sans-serif;
  font-size: 42px;
  font-weight: 700;
  line-height: 49px;
  text-align: left;
  height: 98px;
  left: 144px;
  top: 281px;
}

.home-subtitle {
  font-family: CoText, sans-serif;
  font-size: 35px;
  font-weight: 700;
  text-align: left;
}
</style>
