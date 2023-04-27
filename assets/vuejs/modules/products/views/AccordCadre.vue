<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div v-if="isLoading" class="flex h-16 w-full items-center justify-center">
      <LoaderSharedComponent
        class="text-secondary"
        classes="loader-xl loader"
      />
    </div>
    <div
      v-else-if="accord && !isLoading"
      class="xs:w-[100%] m-auto my-4 max-w-screen-2xl px-5 sm:px-8"
    >
      <HeaderPartnerComponent
        :name="accord.name"
        :note="accord.properties.note_rse ?? null"
        :logo="accord.properties.logo_partenaire"
        :barner="accord.properties.banniere_partenaire"
        :categories="accord.categories"
        @scroll-to="scrollTo('#sectionRse')"
      />

      <PointsClesComponent
        :description="accord.description"
        :points-cles="pointsCles"
      />

      <div
        class="mt-10 mt-5 flex flex-col text-sm md:text-base lg:grid lg:grid-cols-9 lg:gap-4 lg:text-lg"
      >
        <ConditionsNegocieesComponent :properties="accord.properties" />

        <div class="bloc-content col-span-4 mt-5 lg:mt-0">
          <h3
            class="mb-[1.563rem] mt-5 text-title-35 font-bold leading-9 text-primary xl:w-3/4"
          >
            Comment bénéficier des conditions ?
          </h3>
          <ConditionsNotActivatedComponent
            v-if="status.not_activated === currentStatus.status"
            :text="accord.properties.process_not_activated"
            :current-status="currentStatus"
            :accord-name="accord.name"
          />
          <ConditionsPendingOrActivated
            v-else
            :current-status="currentStatus"
            :properties="accord.properties"
          />
        </div>
      </div>
      <div id="sectionRse" />
      <MiseEnAvantComponent :properties="accord.properties" />
      <PointsClesRSEComponent
        v-if="accord.properties.texte_rse"
        :description="accord.properties.texte_rse"
        :note="accord.properties.note_rse"
        :points-cles-rse="pointsClesRSE"
      />
      <EnSavoirPlusComponent :properties="accord.properties" />
      <div class="mt-11">
        <h3 class="home-subtitle text-primary">
          Ces partenaires peuvent aussi
          <span class="text-gradient">vous intéresser</span>
        </h3>
      </div>
      <PartnersCarouselComponent class="mt-5" />
    </div>
    <div
      v-else
      class="xs:w-[100%] m-auto my-4 flex max-w-screen-2xl justify-center px-5 sm:px-8"
    >
      Aucun accord cadre n'a été trouvé avec cette référence
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import HeaderPartnerComponent from '@/vuejs/modules/products/components/accord-cadre/HeaderAccordCadreComponent.vue'
import { computed, ref, watch } from 'vue'
import PartnersCarouselComponent from '@/vuejs/modules/shared/PartnersCarouselComponent.vue'
import { Product } from '@/vuejs/types/Product'
import { useRoute } from 'vue-router'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import MiseEnAvantComponent from '@/vuejs/modules/products/components/accord-cadre/CarouselMiseEnAvantComponent.vue'
import ConditionsNegocieesComponent from '@/vuejs/modules/products/components/accord-cadre/ConditionsNegocieesComponent.vue'
import { status } from '@/vuejs/modules/products'
import ConditionsNotActivatedComponent from '@/vuejs/modules/products/components/accord-cadre/ConditionsNotActivatedComponent.vue'
import ConditionsPendingOrActivated from '@/vuejs/modules/products/components/accord-cadre/ConditionsPendingOrActivatedComponent.vue'
import PointsClesComponent from '@/vuejs/modules/products/components/accord-cadre/PointsClesComponent.vue'
import PointsClesRSEComponent from '@/vuejs/modules/products/components/accord-cadre/PointsClesRSEComponent.vue'
import EnSavoirPlusComponent from '@/vuejs/modules/products/components/accord-cadre/EnSavoirPlusComponent.vue'
import { useProductStore } from '@/vuejs/stores/product'

const route = useRoute()
const accordStore = useProductStore()
const accord = ref<Product>()
const isLoading = ref<boolean>(false)

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

const scrollTo = (selector) => {
  const element = document.querySelector(selector)
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' })
  }
}

watch(
  () => route.params.id as string,
  async (id: string) => {
    isLoading.value = true
    try {
      if (id) {
        const accordId = id.split('-')
        accord.value = await accordStore.findAccordCadreById(
          accordId[accordId.length - 1],
        )
      }
    } catch (error) {
    } finally {
      isLoading.value = false
    }
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
