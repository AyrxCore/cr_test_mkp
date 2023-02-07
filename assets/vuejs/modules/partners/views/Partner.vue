<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div
      v-if="accord"
      class="xs:w-[100%] m-auto my-4 max-w-screen-2xl px-5 sm:px-8"
    >
      <HeaderPartnerComponent
        :name="accord.name"
        :note="accord.properties.note_rse"
        :logo="accord.properties.logo_partenaire"
        :barner="accord.properties.banniere_partenaire"
        :categories="accord.categories"
      />

      <div
        class="mt-10 mt-5 flex flex-col text-gray-600 xl:grid xl:grid-cols-9 xl:gap-4"
      >
        <div
          class="rounded-lg bg-white px-4 pt-7.5 pb-4 text-sm md:text-base xl:col-span-5 xl:px-7.5 xl:text-lg"
        >
          <p v-html="accord.description" />
        </div>
        <div
          class="mt-5 rounded-lg bg-white px-4 pt-7 pb-4 text-gray-500 xl:col-span-4 xl:mt-0"
        >
          <ul>
            <li
              v-for="(pointCle, key) in pointsCles"
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

        <ConditionsNegocieesPartnerComponent
          :mentions-legales="accord.properties.mentions_legales_conditions"
          :images="imagesConditionsNegociees"
          :textes="textesConditionsNegociees"
          :buttons="buttonsConditionsNegociees"
        />
        <div class="bloc-content col-span-4 mt-5 xl:mt-0">
          <h3
            class="text-title-35 mb-[1.563rem] mt-5 font-bold leading-9 text-primary xl:w-3/4"
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

      <MiseEnAvantPartnerComponent />

      <div class="mt-10 mt-5 flex flex-col lg:grid lg:grid-cols-3 lg:gap-4">
        <div class="bloc-content col-span-2 flex flex-col">
          <h3
            class="text-title-35 mb-[1.563rem] font-bold leading-9 text-primary lg:w-3/4"
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
            class="text-title-35 mb-[1.563rem] justify-center font-bold leading-9 text-primary lg:w-3/4 lg:justify-start"
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
    <div v-else class="w-full flex h-16 justify-center items-center">
      <LoaderSharedComponent />
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import HeaderPartnerComponent from '@/vuejs/modules/partners/components/HeaderPartnerComponent.vue'
import { getImage } from '@/vuejs/services/utils'
import CheckIconComponent from '@/vuejs/modules/shared/icon/CheckIconComponent.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import PhoneLightIconComponent from '@/vuejs/modules/shared/icon/PhoneLightIconComponent.vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import CheckCircleIconComponent from '@/vuejs/modules/shared/icon/CheckCircleIconComponent.vue'
import { computed, ref, watch } from 'vue'
import aldaMap from '@/vuejs/assets/img/demo/alda-map.png'
import PartnersCarouselComponent from '@/vuejs/modules/shared/PartnersCarouselComponent.vue'
import LeafIconComponent from '@/vuejs/modules/shared/icon/LeafIconComponent.vue'
import { AccordCadre } from '@/vuejs/types/AccordCadre'
import { useRoute } from 'vue-router'
import { useAccordCadreStore } from '@/vuejs/stores/accord_cadre'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import MiseEnAvantPartnerComponent from '@/vuejs/modules/partners/components/CarouselMiseEnAvantPartnerComponent.vue'
import ConditionsNegocieesPartnerComponent
  from '@/vuejs/modules/partners/components/ConditionsNegocieesPartnerComponent.vue'

const route = useRoute()
const accordStore = useAccordCadreStore()
const aldaMapImg = getImage(aldaMap)

const accord = ref<AccordCadre>()

const breadcrumbUrl = computed(() => {
  return []
})

const pointsCles = computed(() => {
  const list = [
    accord.value.properties.points_cles_1,
    accord.value.properties.points_cles_2,
    accord.value.properties.points_cles_3,
  ]

  return list.filter(function (el) {
    return el != null
  })

})

const imagesConditionsNegociees = computed(() => {
  if (accord.value.properties.tableau_conditions_negocies) {
    return accord.value.properties.tableau_conditions_negocies.split(';')
  }

  return []
})

const textesConditionsNegociees = computed(() => {
  const textes = [
    accord.value.properties.text1_conditions_negocies,
    accord.value.properties.text2_conditions_negocies,
    accord.value.properties.text3_conditions_negocies,
  ]
  return textes.filter(function (el) {
    return el != null
  })
})

const buttonsConditionsNegociees = computed(() => {
  const buttons = [
    {
      name: accord.value.properties.cta1_text,
      url: accord.value.properties.cta1_link,
    },
    {
      name: accord.value.properties.cta2_text,
      url: accord.value.properties.cta2_link,
    }
  ]
  return buttons.filter(function (el) {
    return el.url != null
  })
})

const pointsRSE = [
  'Certifications ISO-9001, ISO-14-0001, et ISO-45-0001',
  'Membre de l’UFIPA et du Pacte Mondial des Nations',
  'Fournisseurs sélectionnés dans le respect des normes REACH',
]


watch(
  () => route.params.id as string,
  async (id: string) => {
    if (id) accord.value = await accordStore.findAccordCadreById(id)
  },
  { immediate: true },
)
</script>

<style scoped>
.bloc-content {
  @apply rounded-lg bg-white p-4 text-gray-500 md:p-7.5;
}

.condition-beneficiaire p {
  @apply mb-4 text-sm md:text-base xl:text-lg;
}

</style>
