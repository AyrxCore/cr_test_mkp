<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div class="xs:w-[100%] m-auto my-4 max-w-screen-2xl">
      <HeaderPartnerComponent />

      <div class="mt-10 mt-5 flex grid grid-cols-9 gap-4 text-gray-600">
        <div class="col-span-5 rounded-lg bg-white px-7.5 pt-7.5 pb-4 text-lg">
          <p>
            <span class="uppercase">ALDA MAJUSCULE-OFFICE DEPOT</span> votre spécialiste en fournitures de bureau, mobiliers, consommables informatiques.
          </p>
          <p class="mt-3">
            <span class="uppercase">ALDA MAJUSCULE-OFFICE DEPOT</span> vous propose une offre de proximité régionale multicanale unique en France : BtoB et magasins Office Depot.
          </p>
        </div>
        <div
          class="col-span-4 rounded-lg bg-white px-4 pt-7 pb-4 text-gray-500"
        >
          <ul>
            <li
              v-for="(pointCle, key) in pointsCleTop"
              :key="key"
              class="mb-7.5 flex flex-row items-center text-lg"
            >
              <CheckIconComponent class="mr-1 h-[20px] w-[20px]" />
              <strong class="mr-1">Point clé n° {{ key + 1 }} : </strong> {{ pointCle }}
            </li>
          </ul>
        </div>
      </div>
      <div class="mt-10 mt-5 flex grid grid-cols-9 gap-4 text-lg">
        <div class="bloc-content flex flex-col col-span-5 h-full">
          <h3
            class="mb-[1.563rem] w-3/4 text-title-35 font-bold leading-9 text-primary"
          >
            Vos conditions négociées
          </h3>
          <p class="flex mb-10">
            Remise applicables sur XXXX / mentions légales conditions
          </p>
          <AutomobilePartnerComponent v-if="isAutomobile" />
          <ul v-else class="mx-7 list-disc flex flex-col">
            <li v-for="(condition, key) in listConditions" :key="key" class="mb-5">
              {{ condition }}
            </li>
          </ul>
          <div class="flex justify-center items-end">
            <ButtonComponent class="button-gradient mr-5 w-1/2">
              <DownloadIconComponent />
              Télécharger les conditions négociées
            </ButtonComponent>
          </div>
        </div>
        <div class="bloc-content col-span-4">
          <h3
            class="mb-[1.563rem] w-3/4 text-title-35 font-bold leading-9 text-primary"
          >
            Comment bénéficier des conditions ?
          </h3>
          <ButtonComponent class="button-gradient mb-7">
            <ArrowRightIconComponent /> Bénéficiez des conditions
          </ButtonComponent>
          <div class="condition-beneficiaire">
            <p>Contactez votre animateur dédié ou le service adhérent afin d’effectuer une ouverture de compte.</p>
            <p>Une fois vos codes d’accès en votre possession (délais 48h à 72h), commandez directement sur le site : https://shop.majuscule.com/alda/Default-Start</p>
            <p>Si vous souhaitez également acheter en magasin, téléchargez et complétez l’attestation d’adhésion ci-dessous pour bénéficier de la réduction de 10% en magasin.</p>
            <p>Pour toute question, n’hésitez pas à nous contacter.</p>
          </div>

          <div class="mt-6 flex justify-between">
            <ButtonComponent class="button-secondary mr-5 w-1/2">
              <PhoneLightIconComponent />
              Attestation d’adhésion
            </ButtonComponent>
            <ButtonComponent class="button-secondary w-1/2">
              Contactez le Service Adhérents
            </ButtonComponent>
          </div>
        </div>
      </div>
      <div class="relative">
        <CarouselListSharedComponent
          class="mx-auto mt-10 h-[443px] items-center rounded-xl bg-white px-4 py-4"
          :slides-per-view="1"
          :space-between="10"
          :pagination="true"
        >
          <SwiperSlide v-for="i in 5" :key="i">
            <div class="mt-5 flex grid grid-cols-9 gap-8">
              <div
                class="col-span-5 ml-10 flex h-[374px!important] items-center justify-center rounded-lg border py-8.5"
              >
                <img
                  :src="aldaImg"
                  alt="Picture"
                  class="object-cover"
                />
              </div>
              <div
                class="col-span-4 flex flex-col rounded-lg bg-white pr-12 text-lg text-gray-500"
              >
                <h3
                  class="mb-[1.563rem] w-3/4 text-title-35 font-bold leading-9 text-primary"
                >
                  Mise en avant partenaire
                </h3>
                <p class="mb-5">
                  Tout ce dont vous avez besoin auprès d’un seul fournisseur.
                  Un groupe d’envergure à taille humaine spécialisé dans la distribution de fournitures de bureau, papier,
                  consommables informatiques, ergonomie au travail et beaux-arts.
                  Mais aussi des offres exclusives de produits vert et fabrication française à votre service.
                </p>

<!--                <ButtonComponent class="button-secondary mt-6 w-1/2 hidden">-->
<!--                  Bouton CTA-->
<!--                </ButtonComponent>-->
              </div>
            </div>
          </SwiperSlide>
        </CarouselListSharedComponent>
      </div>

      <div class="mt-10 mt-5 flex grid h-[337px] grid-cols-3 gap-4">
        <div class="bloc-content flex flex-col col-span-2">
          <h3
            class="mb-[1.563rem] w-3/4 text-title-35 font-bold leading-9 text-primary"
          >
            Achats durables : <br />
            <span class="text-green">engagements RSE</span> du partenaire
          </h3>
          <p>
            Nos fournisseurs sont soumis à notre référentiel RSE, et obtiennent
            une note (affichée ici) qui constitue un des éléments clés de leur
            référencement. Nous avons co-construit ce référentiel sur-mesure
            avec nos adhérents, et fait valider par la société Tennaxia. Ce
            référentiel RSE est dédié aux PME et ETI. Vous pouvez également le
            soumettre à vos propres fournisseurs d’achats stratégiques pour
            progresser dans votre démarche RSE et créer ainsi un effet
            démultiplicateur qui touche de plus en plus de parties prenantes. Si
            vous souhaitez utiliser ce référentiel RSE, contactez-nous.
          </p>
        </div>
        <div class="rounded-lg bg-white px-3 pt-11 text-lg text-gray-500">
          <ul>
            <li
              v-for="(pointRse, key) in pointsRSE"
              :key="key"
              class="mb-8 flex flex-row items-center text-lg grid grid-cols-5 gap-2"
            >
              <span class="flex col-span-2 items-center">
                <CheckCircleIconComponent class="mr-2 h-[20px] w-[20px]" />
                <strong class="mr-1">Point clé n° {{ key + 1 }} : </strong>
              </span>
              <span class="flex col-span-3">
                 {{ pointRse}}
              </span>
            </li>
          </ul>
        </div>
      </div>
      <div class="mt-10 flex flex-col">
        <h3 class="home-subtitle text-primary">
          Trouvez l'agence la plus <br />près de chez vous
        </h3>
      </div>
      <div class="my-10 mt-5 flex grid h-[379px] grid-cols-3 gap-4">
        <div class="col-span-2 rounded-lg bg-white">
          <img
            :src="aldaMapImg"
            alt="Picture"
            class="object-cover h-[379px] rounded-lg"
          />
        </div>
        <div class="bloc-content">
          <h3
            class="mb-[1.563rem] w-3/4 text-title-35 font-bold leading-9 text-primary"
          >
            En savoir plus sur ce partenaire
          </h3>
          <diV class="flex flex-col">
            <ButtonComponent
              class="button-white button-white-secondary mt-6 w-2/3"
            >
              <ArrowRightIconComponent /> Documentation générique
            </ButtonComponent>
            <ButtonComponent
              class="button-white button-white-secondary mt-6 w-2/3"
            >
              <ArrowRightIconComponent /> Flyer
            </ButtonComponent>
            <ButtonComponent
              class="button-white button-white-secondary mt-6 w-2/3"
            >
              <ArrowRightIconComponent /> ECT
            </ButtonComponent>
          </diV>
        </div>
      </div>

      <div class="mt-11">
        <h3 class="home-subtitle text-primary">
          Ces partenaires peuvent aussi
          <span class="text-gradient">vous intéresser</span>
        </h3>
      </div>
      <PartnersCarouselComponent class="mt-5" />
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import HeaderPartnerComponent from '@/vuejs/modules/partners/components/HeaderPartnerComponent.vue'
import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import { getImage } from '@/vuejs/services/utils'
import { SwiperSlide } from 'swiper/vue'
import CheckIconComponent from '@/vuejs/modules/shared/icon/CheckIconComponent.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import PhoneLightIconComponent from '@/vuejs/modules/shared/icon/PhoneLightIconComponent.vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import DownloadIconComponent from '@/vuejs/modules/shared/icon/DownloadIconComponent.vue'
import CheckCircleIconComponent from '@/vuejs/modules/shared/icon/CheckCircleIconComponent.vue'
import { ref } from 'vue'
import AutomobilePartnerComponent from '@/vuejs/modules/partners/components/AutomobilePartnerComponent.vue'
import alda from '@/vuejs/assets/img/demo/alda-mise-en-avant.jpg'
import aldaMap from '@/vuejs/assets/img/demo/alda-map.png'
import PartnersCarouselComponent from '@/vuejs/modules/shared/PartnersCarouselComponent.vue'

const aldaImg = getImage(alda)
const aldaMapImg = getImage(aldaMap)
const isAutomobile = ref<Boolean>(false)

const pointsCleTop = [
  'Des conditions négociées grands-comptes',
  'Des remises sur le mobilier & en magasin',
  'Un délai de livraison entre 24 et 72h',
]

const listConditions = [
  'Bénéficiez de conditions négociées sur plus de 7000 références en fournitures de bureau, petits matériels informatiques, etc.',
  'Bénéficiez également de remise sur le mobilier de bureau, ainsi que dans tous les points de ventes Office Depot.',
]

const pointsRSE = [
  'Certifications ISO-9001, ISO-14-0001, et ISO-45-0001',
  'Membre de l’UFIPA et du Pacte Mondial des Nations',
  'Fournisseurs sélectionnés dans le respect des normes REACH',
]
</script>

<style scoped>
.bloc-content {
  @apply rounded-lg bg-white px-7.5 py-7.5 text-gray-500;
}

.condition-beneficiaire p {
  @apply mb-4
}
</style>
