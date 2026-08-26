<template>
  <BaseTemplate>
    <div class="m-auto flex-1 xl:pt-5!">
      <div class="xs:w-[100%] m-auto my-10 max-w-screen-2xl px-5 sm:px-8">
        <div class="m-auto w-[100%] max-w-screen-2xl">
          <h3 class="mb-10 flex-col text-4xl font-bold text-primary">
            <span class="flex font-bold">Page introuvable</span>
          </h3>
          <p class="w-full text-lg md:text-xl">
            La page que vous recherchez n'existe pas ou n'existe plus.
          </p>
          <RouterLink
            :to="{ name: PageList.HOME_PAGE }"
            class="button button-primary mb-4 mt-10 w-full md:w-auto"
          >
            <ArrowRightIconComponent class="mr-2 w-4 stroke-white" />
            Retour à la page d'accueil
          </RouterLink>
        </div>
      </div>

      <div class="bg-white py-8">
        <div class="xs:w-[100%] m-auto my-4 max-w-screen-2xl px-5 sm:px-8">
          <div class="m-auto my-2 w-[100%] max-w-screen-2xl">
            <h3 class="text-title-primary mb-3">
              Les accords-cadres incontournables
            </h3>
            <p class="text-sm sm:text-lg">
              Découvrez les partenaires et leurs conditions&nbsp;!
            </p>
            <div class="mt-1 md:mt-5">
              <AccordsCadreComponent
                :accords-cadres="productsAccordsCadre?.results"
                :loading="!productsAccordsCadre"
                class="nav-mobile-only"
                @show-showcase-modal="handleShowcaseModal"
              />
              <ShowcaseModal
                v-if="showShowcaseModal"
                :accord="accordSelected"
                class="modal"
                @cancel="showShowcaseModal = false"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </BaseTemplate>
</template>

<script lang="ts" setup>
import { onBeforeMount, ref } from 'vue'
import { storeToRefs } from 'pinia'

import { PageList } from '@/vuejs/router'
import { useProductStore } from '@/vuejs/stores/product'
import { Product } from '@/vuejs/types/Product'

import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import AccordsCadreComponent from '@/vuejs/modules/home/component/AccordsCadreComponent.vue'
import ShowcaseModal from '@/vuejs/modules/home/component/ShowcaseModal.vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'

const productStore = useProductStore()
const { productsAccordsCadre } = storeToRefs(productStore)

const showShowcaseModal = ref<boolean>(false)
const accordSelected = ref<Product>(null)

const handleShowcaseModal = (accord) => {
  accordSelected.value = accord
  showShowcaseModal.value = true
}

onBeforeMount(async () => {
  await productStore.initSliderAccordsCadres()
})
</script>

<style scoped></style>
