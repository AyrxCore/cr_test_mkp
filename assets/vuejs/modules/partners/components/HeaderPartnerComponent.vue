<template>
  <breadcrumb-shared-component
    :list-url="listUrl"
    :current-page="'Nom du partenaire'"
  />
  <ContactUsButtonComponent />
  <div class="text-green mt-3.5 flex flex-col lg:flex-row lg:items-center">
    <h3 class="page-principal-title text-primary">
      ALDA MAJUSCULE-OFFICE DEPOT
    </h3>
    <div class="flex flex-row">
      <LeafIconComponent class="mx-2" />
      <span class="mr-2 flex text-sm font-bold md:text-lg">7,8/10</span>
      <span class="mt-1 flex text-xs text-gray-500 md:mt-2"
        >Selon notre référentiel RSE</span
      >
      <span
        class="flex cursor-pointer md:mt-1"
        title="Message explicatif du référentiel RSE"
        ><InformationIconComponent
      /></span>
    </div>
  </div>

  <div class="mt-10 flex flex-col md:grid md:grid-cols-4 md:gap-4">
    <div class="flex items-center justify-center rounded-lg bg-white">
      <img
        :src="aldaImg"
        alt="Picture"
        class="items-center rounded-lg sm:mx-auto"
      />
    </div>
    <div class="col-span-3 mt-5 rounded-lg bg-white md:mt-0">
      <img
        :src="aldaHeaderImg"
        alt="Picture"
        class="items-center rounded-lg sm:mx-auto"
      />
    </div>
  </div>
</template>
<script lang="ts" setup>
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import { getImage } from '@/vuejs/services/utils'
import { ref } from 'vue'
import LeafIconComponent from '@/vuejs/modules/shared/icon/LeafIconComponent.vue'
import InformationIconComponent from '@/vuejs/modules/shared/icon/InformationIconComponent.vue'
import alda from '@/vuejs/assets/img/demo/alda.png'
import aldaHeader from '@/vuejs/assets/img/demo/a-propos-header.jpg'
const aldaImg = getImage(alda)
const aldaHeaderImg = getImage(aldaHeader)

const listUrl = ref([
  {
    name: 'Catégories',
    url: '',
  },
  {
    name: 'Outillage',
    url: '',
  },
])
</script>

<style scoped></style>
