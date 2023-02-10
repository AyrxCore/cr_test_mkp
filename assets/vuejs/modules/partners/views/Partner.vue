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

      <PointsClesPartnerComponent
        :description="accord.description"
        :points-cles="pointsCles"
      />

      <div
        class="mt-10 mt-5 flex flex-col text-sm md:text-base xl:grid xl:grid-cols-9 xl:gap-4 xl:text-lg"
      >

        <ConditionsNegocieesPartnerComponent :properties="accord.properties" />

        <div class="bloc-content col-span-4 mt-5 xl:mt-0">
          <h3
            class="text-title-35 mb-[1.563rem] mt-5 font-bold leading-9 text-primary xl:w-3/4"
          >
            Comment bénéficier des conditions ?
          </h3>
          <ConditionsNotActivatedPartnerComponent
            v-if="status.not_activated === currentStatus.status"
            :text="accord.properties.process_not_activated"
            :current-status="currentStatus"
          />
          <ConditionsPendingOrActivated
            v-else-if="status.pending === currentStatus.status"
            :current-status="currentStatus"
            :text="accord.properties.process_pending"
            :btn-contact="accord.properties.cta1_text_pending"
            :btn-link="{name: accord.properties.cta2_text_pending, url: cta2_link_pending}"
          />
          <ConditionsPendingOrActivated
            v-else
            :current-status="currentStatus"
            :text="accord.properties.process_activated"
            :btn-contact="accord.properties.cta1_text_activated"
            :btn-link="{name: accord.properties.cta2_text_activated, url: cta2_activated}"
          />
        </div>
      </div>

      <MiseEnAvantPartnerComponent :properties="accord.properties"/>

      <PointsClesRSEPartnerComponent
        v-if="accord.properties.texte_rse"
        :description="accord.properties.texte_rse"
        :note="accord.properties.note_rse"
        :points-cles-rse="pointsClesRSE"
      />

      <EnSavoirPlusPartnerComponent :properties="accord.properties" />

      <div class="mt-11">
        <h3 class="home-subtitle text-primary">
          Ces partenaires peuvent aussi
          <span class="text-gradient">vous intéresser</span>
        </h3>
      </div>
      <PartnersCarouselComponent class="mt-5" />
    </div>
    <div v-else class="w-full flex h-16 justify-center items-center">
      <LoaderSharedComponent
        class="text-secondary"
        classes="loader-xl loader"
      />
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import HeaderPartnerComponent from '@/vuejs/modules/partners/components/HeaderPartnerComponent.vue'
import { computed, ref, watch } from 'vue'
import PartnersCarouselComponent from '@/vuejs/modules/shared/PartnersCarouselComponent.vue'
import { AccordCadre } from '@/vuejs/types/AccordCadre'
import { useRoute } from 'vue-router'
import { useAccordCadreStore } from '@/vuejs/stores/accord_cadre'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import MiseEnAvantPartnerComponent from '@/vuejs/modules/partners/components/CarouselMiseEnAvantPartnerComponent.vue'
import ConditionsNegocieesPartnerComponent
  from '@/vuejs/modules/partners/components/ConditionsNegocieesPartnerComponent.vue'
import { status } from '@/vuejs/modules/partners/partner'
import ConditionsNotActivatedPartnerComponent
  from '@/vuejs/modules/partners/components/ConditionsNotActivatedPartnerComponent.vue'
import ConditionsPendingOrActivated
  from '@/vuejs/modules/partners/components/ConditionsPendingOrActivatedPartnerComponent.vue'
import PointsClesPartnerComponent from '@/vuejs/modules/partners/components/PointsClesPartnerComponent.vue'
import PointsClesRSEPartnerComponent from '@/vuejs/modules/partners/components/PointsClesRSEPartnerComponent.vue'
import EnSavoirPlusPartnerComponent from '@/vuejs/modules/partners/components/EnSavoirPlusPartnerComponent.vue'
import { useUserStore } from '@/vuejs/stores/user'

const route = useRoute()
const accordStore = useAccordCadreStore()

const userStore = useUserStore()

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

const currentStatus = computed(() => {
  return accord.value.accountAccordCadre
})

const pointsClesRSE = computed(() => {
  const list = [
    accord.value.properties.points_cles_rse_1,
    accord.value.properties.points_cles_rse_2,
    accord.value.properties.points_cles_rse_3,
  ]

  return list.filter(function (el) {
    return el != null
  })

})


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
