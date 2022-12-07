<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div class="xs:w-[100%] m-auto my-4 max-w-screen-2xl flex-1 sm:p-8">
      <div class="mt-7 flex md:justify-between">
        <div class="sm:w-[100%] md:w-[52rem]">
          <h3 class="home-title text-primary">
            Bienvenue sur la
            <span
              class="bg-gradient-to-r from-purple-500 via-blue-500 to-cyan-500 bg-clip-text text-transparent"
            >
              marketplace réservée</span
            >
            à nos 30 000 entreprises adhérentes
          </h3>
          <p class="mt-5 text-lg text-gray-400">
            Cher adhérent, ce nouvel espace dédié vous permet d'acheter
            directement en ligne et de trouver vos accords-cadres, en quelques
            clics. Notre équipe, que vous connaissez, se tient à votre
            disposition pour répondre à toutes vos questions.
          </p>
        </div>
        <div>
          <ButtonComponent class="button-secondary">
            <MailIconLightComponent />
            <PhoneLightIconComponent />
            Contactez-nous
          </ButtonComponent>
        </div>
      </div>
      <div class="flex justify-center">
        <CarouselListSharedComponent
          class="mx-auto mt-10 h-[303px] items-center rounded-xl bg-white px-4 py-4"
          :slides-per-view="1"
          :space-between="10"
          :pagination="true"
        >
          <SwiperSlide v-for="i in 5" :key="i">
            <img
              :src="defaultImageFile"
              alt="Picture"
              class="items-center sm:mx-auto"
            />
          </SwiperSlide>
        </CarouselListSharedComponent>
      </div>
      <div class="mt-10 w-[45rem]">
        <h3 class="home-subtitle text-primary">Notre sélection pour vous</h3>
        <p class="text-lg text-gray-400">
          Savez-vous que vous pouvez désormais acheter ces produits en quelques
          clics ?
        </p>
      </div>
      <div class="flex justify-center">
        <CarouselListSharedComponent
          class="mx-auto mt-2 items-center rounded-xl px-[50px!important]"
          :slides-per-view="4"
          :space-between="50"
          navigation=""
        >
          <SwiperSlide v-for="(product, key) in products" :key="key">
            <ProductComponent :product="product" />
          </SwiperSlide>
        </CarouselListSharedComponent>
      </div>
      <div class="mt-10 w-[45rem]">
        <h3 class="home-subtitle text-primary">Top ventes</h3>
        <p class="text-lg text-gray-400">
          D'autres adhérents ont déjà acheté ces produits
        </p>
      </div>
      <div class="flex justify-center">
        <CarouselListSharedComponent
          class="mx-auto mt-2 items-center rounded-xl px-[50px!important]"
          :slides-per-view="4"
        >
          <SwiperSlide v-for="(product, key) in products2" :key="key">
            <ProductComponent :product="product" />
          </SwiperSlide>
        </CarouselListSharedComponent>
      </div>

      <div class="mt-10">
        <h3 class="home-subtitle text-primary">
          Les accords-cadres incontournables
        </h3>
        <p class="text-lg text-gray-400">
          Etes-vous certains de profiter des touts les économies incluses dans
          votre adhésion ?
        </p>
      </div>
      <div class="flex justify-center">
        <CarouselListSharedComponent
          class="mx-auto mt-2 rounded-xl px-[50px!important]"
          :slides-per-view="4"
        >
          <SwiperSlide v-for="(accord, key) in accords" :key="key">
            <div class="h-full rounded-md bg-primary p-8">
              <div class="mx-auto">
                <img :src="accord.img" alt="Image produit" />
              </div>
              <p class="my-8 text-lg font-normal text-white">
                Découvrez ou téléchargez les conditions négociées de ce
                partenaire
              </p>

              <a href="#" class="button button-white !px-2">
                <ArrowRightIconComponent />Découvrir l'accord cadre
              </a>
            </div>
          </SwiperSlide>
        </CarouselListSharedComponent>
      </div>
      <div class="mt-10">
        <h3 class="home-subtitle text-primary">Nos partenaires fournisseurs</h3>
        <p class="text-lg text-gray-400">
          Plus de 200 partenaires fournisseurs, repartis en 26 catégories, sont
          référencés pour vos achats.
        </p>
      </div>
      <CarouselListSharedComponent
        class="mx-auto mt-10 h-[107px] items-center rounded-xl px-6 py-4 px-[50px!important]"
        :slides-per-view="8"
        :space-between="50"
      >
        <SwiperSlide
          v-for="(partenaire, key) in partenaires"
          :key="key"
          class="flex items-center justify-center rounded-lg bg-white"
        >
          <img
            :src="partenaire.img"
            :alt="partenaire.alt"
            class="h-[auto!important]"
          />
        </SwiperSlide>
      </CarouselListSharedComponent>

      <div class="mt-16 text-center">
        <h3 class="text-[35px] font-bold text-primary">
          Nos catégories de produits et d'accords-cadres
        </h3>
      </div>
      <div
        class="mt-5 flex w-[100%] flex-wrap justify-center text-center text-lg"
      >
        <div
          v-for="(categorie, id) in listCategories"
          :key="id"
          class="mr-8 mt-2 items-center justify-center rounded bg-[#404FE6] px-1.5 py-1 text-white"
        >
          {{ categorie }}
        </div>
      </div>
    </div>

    <div
      class="home-bloc-economie text-cotext m-auto mt-16 h-[338px] flex-1 py-4 text-white"
    >
      <div class="text-center">
        <h3 class="text-[35px] font-bold">
          Vous faites des économies tout en <br />
          contribuant à votre démarche RSE
        </h3>
        <p class="mx-auto mt-2 w-[45%] text-lg">
          Nos adhérents réalisent en moyenne 27 % d'économies, grâce à la
          mutualisation des achats. Nous notons et référençons nos partenaires
          fournisseurs à l'aide d'un référentiel RSE. Votre adhésion permet
          aussi de contribuer à la démarche RSE de votre entreprise.
        </p>
        <p class="mt-10 flex justify-center">
          <a href="#" class="button button-gradient">
            <ArrowRightIconComponent :stroke-color="'#FFFFFF'" /> Toutes nos
            catégories d'achats
          </a>
        </p>
      </div>
    </div>

    <div
      class="m-auto my-6 mt-10 max-w-screen-2xl flex-1 rounded-md bg-white pb-4 shadow-md"
    >
      <div class="flex flex-col pt-1 text-center">
        <h3 class="primary mt-10 flex flex-col pl-8 text-[35px] font-bold">
          <p class="flex">Contenus experts spécialement conçus</p>
          <p class="flex">
            pour la
            <span
              class="ml-2 bg-gradient-to-r from-purple-500 via-blue-500 to-cyan-500 bg-clip-text text-transparent"
            >
              communauté QANTIS
            </span>
          </p>
        </h3>
        <div class="mt-5 grid grid-cols-3 gap-4 p-8">
          <div
            v-for="(contenu, id) in contenusExpert"
            :key="id"
            class="grid grid-cols-2"
          >
            <div class="flex rounded-lg border-[#F3EDFE]">
              <img
                :src="contenu.img"
                alt="Picture"
                class="items-center sm:mx-auto"
              />
            </div>
            <div class="px-6 text-left">
              <h3 class="text-[23px] font-bold text-primary">
                {{ contenu.titre }}
              </h3>
              <p class="mt-2">
                <a
                  href="#"
                  class="mr-2 mb-2 w-auto items-center rounded-md bg-purple-600 px-5 py-2.5 text-sm text-white"
                >
                  {{ contenu.btnNam }}
                </a>
              </p>
              <p class="text-lg">
                {{ contenu.desc }}
              </p>
              <div class="bottom-0">
                <a
                  href="#"
                  class="bottom-0 flex items-center text-sm font-medium text-primary underline"
                >
                  Lire l'article
                </a>
              </div>
            </div>
          </div>
        </div>
        <div class="flex justify-center">
          <p class="mt-10">
            <a href="#" class="button button-gradient">
              <ArrowRightIconComponent :stroke-color="'#FFFFFF'" /> Tous les
              contenus experts
            </a>
          </p>
        </div>
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
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
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
import MailIconLightComponent from '@/vuejs/modules/shared/icon/MailIconLightComponent.vue'
import PhoneLightIconComponent from '@/vuejs/modules/shared/icon/PhoneLightIconComponent.vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'

const defaultImageFile = getImage(defaultImage)

const products = ref([
  { imgFrn: getImage(imgFrn1), imgProduct: getImage(imgCheville) },
  { imgFrn: getImage(imgFrn2), imgProduct: getImage(imgAmpoules) },
  { imgFrn: getImage(imgFrn3), imgProduct: getImage(imgTapis) },
  { imgFrn: getImage(imgFrn4), imgProduct: getImage(imgGomme) },
])

const products2 = ref([
  { imgFrn: defaultImageFile, imgProduct: defaultImageFile },
  { imgFrn: defaultImageFile, imgProduct: defaultImageFile },
  { imgFrn: defaultImageFile, imgProduct: defaultImageFile },
  { imgFrn: defaultImageFile, imgProduct: defaultImageFile },
])

const accords = ref([
  { img: getImage(imgPeugeot), alt: 'Peugeot' },
  { img: getImage(imgLoxam), alt: 'Loxam' },
  { img: getImage(imgBusiness), alt: 'Business' },
  { img: getImage(imgAdecco), alt: 'Adecco' },
])

const partenaires = ref([
  { img: getImage(imgPeugeot), alt: 'Peugeot' },
  { img: getImage(imgRenault), alt: 'Renault' },
  { img: getImage(imgLoxam), alt: 'Loxam' },
  { img: getImage(imgKiloutou), alt: 'Kiloutou' },
  { img: getImage(imgAdecco), alt: 'Adecco' },
  { img: getImage(imgWurth), alt: 'Wurth' },
  { img: getImage(imgLdlc), alt: 'LDLC Pro' },
  { img: getImage(imgHertz), alt: 'Herts' },
])

const contenusExpert = ref([
  {
    img: getImage(imgMaps),
    titre: 'Loi montagne : êtes-vous concernés ? ',
    btnNam: 'Actualités',
    desc: 'A partir du 1er novembre 2022, la loi Montagne...',
  },
  {
    img: getImage(imgCentral),
    titre: 'Tout savoir sur le décret tertiaire',
    btnNam: 'Actualités',
    desc: 'Le décret tertiaire impose une réduction \nde consommation...',
  },
  {
    img: getImage(imgMagazine),
    titre: 'Comment entretenir votre véhicule ? ',
    btnNam: 'Conseil',
    desc: 'Les équipes de QANTIS ont conçu pour vous un guide...',
  },
])
</script>

<style lang="scss">
@import 'assets/style/_variables.scss';

.home-bloc-economie {
  background: linear-gradient(87.3deg, #050056 0%, #404fe6 100%);
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
