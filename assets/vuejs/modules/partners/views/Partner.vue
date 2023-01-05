<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div class="xs:w-[100%] m-auto my-4 max-w-screen-2xl px-5 sm:px-8">
      <HeaderPartnerComponent />

      <div
        class="mt-10 mt-5 flex flex-col text-gray-600 xl:grid xl:grid-cols-9 xl:gap-4"
      >
        <div
          class="rounded-lg bg-white px-4 pt-7.5 pb-4 text-sm md:text-base xl:col-span-5 xl:px-7.5 xl:text-lg"
        >
          <p>
            <span class="uppercase">ALDA MAJUSCULE-OFFICE DEPOT</span> votre
            spécialiste en fournitures de bureau, mobiliers, consommables
            informatiques.
          </p>
          <p class="mt-3">
            <span class="uppercase">ALDA MAJUSCULE-OFFICE DEPOT</span> vous
            propose une offre de proximité régionale multicanale unique en
            France : BtoB et magasins Office Depot.
          </p>
        </div>
        <div
          class="mt-5 rounded-lg bg-white px-4 pt-7 pb-4 text-gray-500 xl:col-span-4 xl:mt-0"
        >
          <ul>
            <li
              v-for="(pointCle, key) in pointsCleTop"
              :key="key"
              class="mb-7.5 text-sm md:text-base xl:text-lg"
            >
              <span class="flex flex-row">
                <CheckIconComponent
                  class="mr-2 !inline h-[20px] h-[28px] w-[20px] md:w-[24px] lg:mr-3.5"
                />
                <span>
                  <span class="mr-1 font-bold"
                    >Point clé n° {{ key + 1 }} : </span
                  >{{ pointCle }}
                </span>
              </span>
            </li>
          </ul>
        </div>
      </div>
      <div
        class="mt-10 mt-5 flex flex-col text-sm md:text-base xl:grid xl:grid-cols-9 xl:gap-4 xl:text-lg"
      >
        <div class="bloc-content col-span-5 flex h-full flex-col">
          <h3
            class="page-principal-title mb-[1.563rem] font-bold leading-9 text-primary xl:w-3/4"
          >
            Vos conditions négociées
          </h3>
          <p class="mb-10 flex">
            Remise applicables sur XXXX / mentions légales conditions
          </p>
          <AutomobilePartnerComponent v-if="isAutomobile" />
          <ul v-else class="mx-7 flex list-disc flex-col">
            <li
              v-for="(condition, key) in listConditions"
              :key="key"
              class="mb-5"
            >
              {{ condition }}
            </li>
          </ul>
          <div class="flex items-end justify-center">
            <ButtonComponent class="button-gradient md:mr-5 md:w-1/2">
              <DownloadIconComponent />
              Télécharger les conditions négociées
            </ButtonComponent>
          </div>
        </div>
        <div class="bloc-content col-span-4 mt-5 xl:mt-0">
          <h3
            class="page-principal-title mb-[1.563rem] mt-5 font-bold leading-9 text-primary xl:w-3/4"
          >
            Comment bénéficier des conditions ?
          </h3>
          <ButtonComponent class="button-gradient mb-7">
            <ArrowRightIconComponent /> Bénéficiez des conditions
          </ButtonComponent>
          <div class="condition-beneficiaire">
            <p>
              Contactez votre animateur dédié ou le service adhérent afin
              d’effectuer une ouverture de compte.
            </p>
            <p>
              Une fois vos codes d’accès en votre possession (délais 48h à 72h),
              commandez directement sur le site :
              https://shop.majuscule.com/alda/Default-Start
            </p>
            <p>
              Si vous souhaitez également acheter en magasin, téléchargez et
              complétez l’attestation d’adhésion ci-dessous pour bénéficier de
              la réduction de 10% en magasin.
            </p>
            <p>Pour toute question, n’hésitez pas à nous contacter.</p>
          </div>

          <div
            class="mt-6 flex flex-col justify-center md:flex-row md:justify-between"
          >
            <ButtonComponent class="button-secondary md:mr-5 md:w-1/2">
              <PhoneLightIconComponent />
              Attestation d’adhésion
            </ButtonComponent>
            <ButtonComponent class="button-secondary mt-5 md:mt-0 md:w-1/2">
              Contactez le Service Adhérents
            </ButtonComponent>
          </div>
        </div>
      </div>
      <div class="partner-carousel relative">
        <CarouselListSharedComponent
          class="mx-auto mt-10 items-center rounded-xl bg-white px-4 py-4 lg:h-[443px]"
          :slides-per-view="1"
          :space-between="10"
          :pagination="true"
          :breakpoints="{
            640: {
              slidesPerView: 1,
              spaceBetween: 10,
            },
          }"
        >
          <SwiperSlide v-for="i in 5" :key="i">
            <div class="mt-5 flex flex-col lg:flex-row">
              <div
                class="flex h-[217px!important] justify-center rounded-lg px-2 lg:ml-10 lg:h-[374px!important] lg:w-1/2 lg:items-center lg:border lg:px-0 lg:py-8.5"
              >
                <img :src="aldaImg" alt="Picture" class="object-cover" />
              </div>
              <div
                class="mt-5 flex flex-col rounded-lg bg-white p-5 text-lg text-gray-500 lg:mt-0 lg:w-1/2 lg:pr-12"
              >
                <h3
                  class="page-principal-title mb-[1.563rem] font-bold leading-9 text-primary lg:w-3/4"
                >
                  Mise en avant partenaire
                </h3>
                <p class="mb-5 text-sm md:text-base xl:text-lg">
                  Tout ce dont vous avez besoin auprès d’un seul fournisseur. Un
                  groupe d’envergure à taille humaine spécialisé dans la
                  distribution de fournitures de bureau, papier, consommables
                  informatiques, ergonomie au travail et beaux-arts. Mais aussi
                  des offres exclusives de produits vert et fabrication
                  française à votre service.
                </p>

                <!--                <ButtonComponent class="button-secondary mt-6 w-1/2 hidden">-->
                <!--                  Bouton CTA-->
                <!--                </ButtonComponent>-->
              </div>
            </div>
          </SwiperSlide>
        </CarouselListSharedComponent>
      </div>

      <div class="mt-10 mt-5 flex flex-col lg:grid lg:grid-cols-3 lg:gap-4">
        <div class="bloc-content col-span-2 flex flex-col">
          <h3
            class="page-principal-title mb-[1.563rem] font-bold leading-9 text-primary lg:w-3/4"
          >
            Achats durables : <br />
            <span class="text-green-qantis">engagements RSE</span> du partenaire
            <div class="mt-2 flex flex-row items-center">
              <LeafIconComponent class="mr-2" />
              <span class="mr-2 flex font-bold text-green-qantis">7,8/10</span>
            </div>
          </h3>
          <p class="text-sm md:text-base xl:text-lg">
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
          <a
            href="#"
            class="text-sm font-bold underline decoration-gray-500 decoration-1 md:text-base xl:text-lg"
            >Découvrir notre référentiel RSE</a
          >
        </div>
        <div
          class="mt-5 items-center rounded-lg bg-white px-3 pt-11 text-sm text-gray-500 md:text-base lg:mt-0 xl:text-lg"
        >
          <ul>
            <li
              v-for="(pointRse, key) in pointsRSE"
              :key="key"
              class="mb-7.5 items-center text-sm md:text-base xl:text-lg"
            >
              <span class="flex flex-row">
                <CheckCircleIconComponent
                  class="mr-2 !inline h-[20px] h-[28px] w-[20px] md:w-[24px] lg:mr-3.5"
                />
                <span>
                  <span class="mr-1 font-bold"
                    >Point clé n° {{ key + 1 }} : </span
                  >{{ pointRse }}
                </span>
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
      <div
        class="my-10 mt-5 flex flex-col lg:grid lg:h-[379px] lg:grid-cols-3 lg:gap-4"
      >
        <div class="col-span-2 rounded-lg bg-white">
          <img
            :src="aldaMapImg"
            alt="Picture"
            class="h-[379px] rounded-lg object-cover"
          />
        </div>
        <div class="bloc-content mt-5 lg:mt-0">
          <h3
            class="page-principal-title mb-[1.563rem] justify-center font-bold leading-9 text-primary lg:w-3/4 lg:justify-start"
          >
            En savoir plus sur ce partenaire
          </h3>
          <diV class="flex flex-col">
            <ButtonComponent
              class="button-white button-white-secondary mt-6 lg:w-2/3"
            >
              <ArrowRightIconComponent /> Documentation générique
            </ButtonComponent>
            <ButtonComponent
              class="button-white button-white-secondary mt-6 lg:w-2/3"
            >
              <ArrowRightIconComponent /> Flyer
            </ButtonComponent>
            <ButtonComponent
              class="button-white button-white-secondary mt-6 lg:w-2/3"
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
import LeafIconComponent from '@/vuejs/modules/shared/icon/LeafIconComponent.vue'

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
  @apply rounded-lg bg-white p-4 text-gray-500 md:p-7.5;
}

.condition-beneficiaire p {
  @apply mb-4 text-sm md:text-base xl:text-lg;
}

/*.partner-carousel > .swiper-button-direction-next, .partner-carousel > .swiper-button-direction-prev {*/
/*  @apply top-[-280px!important] md:top-[-230px!important] lg:top-0;*/
/*}*/
</style>
