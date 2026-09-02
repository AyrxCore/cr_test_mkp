<template>
  <BaseTemplate :title="accordTitle">
    <LoadingComponent v-if="isLoading" />
    <div
      v-else-if="accordCadre?.accordCadreContent && !isLoading && !isInShowcase"
      class="w-full bg-white"
    >
      <Banner class="px-6 sm:px-8 lg:px-14" />
      <Navigation class="mx-2 mb-12 hidden sm:mx-4 md:block lg:mx-7" />
      <div class="flex flex-col gap-8 px-6 pb-12 pt-12 sm:px-8 lg:flex-row lg:px-14">
        <Presentation />
        <NegociatedTerms />
      </div>
      <template
        v-for="(block, index) in visibleBlocks"
        :key="block.key"
      >
        <div
          :class="getBlockContainerClass(index)"
          :id="block.key === 'map' ? 'mapBlock' : undefined"
        >
          <Steps
            v-if="block.key === 'steps'"
            class="pb-12 pt-12 px-6 sm:px-8 md:flex-row lg:px-14"
          />
          <PartnerStoresMap
            v-else-if="block.key === 'map'"
            :accord="accordCadre"
            @loaded="(hasStores) => { showMapBlock = hasStores }"
          />
          <SellersCarouselBlock
            v-else-if="block.key === 'sellers'"
            :params="sellersByCategoryParam"
          />
        </div>
      </template>
    </div>
    <div
      v-else-if="showError"
      class="m-auto my-12 w-5/6 rounded-md border p-2 text-center text-gray-500"
    >
      Impossible de charger l'accord-cadre.
    </div>

    <!-- Layers -->
    <MoreInformationsLayer v-model="layers.showMoreInformationsLayer.value" />
    <ConfirmationLayer v-model="layers.showConfirmationLayer.value" />
    <SuccessLayer v-model="layers.showSuccessLayer.value" />
    <NegociatedTermsLayer v-model="layers.showNegociatedTermsLayer.value" />
    <FatInterestModal
      v-if="accordCadre"
      v-model="layers.showFatInterestModal.value"
      :accord="accordCadre"
    />
  </BaseTemplate>
</template>

<script lang="ts" setup>
import { computed, provide, ref, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { useRoute, useRouter } from 'vue-router'

import { MainPageList } from '@/vuejs/router/pages-list.ts'
import { useUserStore } from '@/vuejs/stores/user.ts'
import { useAccordCadreStore } from '@/vuejs/stores/accordCadre.ts'
import { useAccordCadreLayers } from '@/vuejs/modules/products/composables/useAccordCadreLayers'

import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import Banner from '@/vuejs/modules/products/components/accord-cadre/blocks/Banner.vue'
import MoreInformationsLayer from '@/vuejs/modules/products/components/accord-cadre/MoreInformationsLayer.vue'
import ConfirmationLayer from '@/vuejs/modules/products/components/accord-cadre/ConfirmationLayer.vue'
import SuccessLayer from '@/vuejs/modules/products/components/accord-cadre/SuccessLayer.vue'
import NegociatedTermsLayer from '@/vuejs/modules/products/components/accord-cadre/NegociatedTermsLayer.vue'
import FatInterestModal from '@/vuejs/modules/products/components/accord-cadre/FatInterestModal.vue'
import Presentation from '@/vuejs/modules/products/components/accord-cadre/blocks/Presentation.vue'
import NegociatedTerms from '@/vuejs/modules/products/components/accord-cadre/blocks/NegociatedTerms.vue'
import LoadingComponent from '@/vuejs/modules/shared/LoadingComponent.vue'
import Navigation from '@/vuejs/modules/products/components/accord-cadre/Navigation.vue'
import Steps from '@/vuejs/modules/products/components/accord-cadre/blocks/Steps.vue'
import PartnerStoresMap from '@/vuejs/modules/products/components/accord-cadre/PartnerStoresMap.vue'
import SellersCarouselBlock from '@/vuejs/modules/products/components/accord-cadre/blocks/SellersCarouselBlock.vue'

const accordCadreStore = useAccordCadreStore()
const { accordCadre, showStepsBlock, showRseBlock } =
  storeToRefs(accordCadreStore)

const showMapBlock = ref<boolean | null>(null)
provide('showMapBlock', showMapBlock)

const getBlockContainerClass = (index: number): string =>
  index % 2 === 0 ? 'bg-gray-50 py-2' : 'bg-white'

const visibleBlocks = computed(() =>
  [
    { key: 'steps', visible: showStepsBlock.value },
    { key: 'map', visible: showMapBlock.value !== false },
    { key: 'rse', visible: showRseBlock.value },
    { key: 'sellers', visible: true },
  ].filter((b) => b.visible),
)

const sellersByCategoryParam = computed(() => {
  const categoryId = accordCadre.value?.categories?.[0]?.id
  return categoryId ? { categories: [categoryId] } : undefined
})

const { adherentTarifShowcases } = storeToRefs(useUserStore())

// Gestion des layers via composable (UI state local)
const layers = useAccordCadreLayers()
provide('accordCadreLayers', layers)

const route = useRoute()
const router = useRouter()
const isLoading = ref<boolean>(false)

const accordTitle = computed<string>(() => {
  return accordCadre.value ? accordCadre.value.name : ''
})

const isInShowcase = computed<boolean>(() =>
  accordCadre.value && accordCadre.value.properties
    ? adherentTarifShowcases.value.some(
        (showcase) => showcase.accordId === accordCadre.value!.accordId,
      )
    : false,
)

const showError = computed<boolean>(
  () =>
    !isLoading.value &&
    !isInShowcase.value &&
    !accordCadre.value?.accordCadreContent,
)

watch(
  () => isInShowcase.value,
  (newValue) => {
    if (newValue) {
      router.push({ name: MainPageList.HOME_PAGE })
    }
  },
  { immediate: true },
)

watch(
  () => route.params.slug as string,
  async (slug: string) => {
    isLoading.value = true
    showMapBlock.value = null
    try {
      if (slug) {
        await accordCadreStore.findAccordCadreById(slug)
      }
    } finally {
      isLoading.value = false
    }
  },
  { immediate: true },
)
</script>
