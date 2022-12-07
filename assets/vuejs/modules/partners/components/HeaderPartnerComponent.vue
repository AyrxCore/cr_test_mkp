<template>
  <breadcrumb-shared-component
    :list-url="listUrl"
    :current-page="'Nom du partenaire'"
  />
  <ContactUsButtonComponent />
  <div class="flex items-center text-green">
    <h3 class="text-[35px] text-primary">Nom du partenaire</h3>
    <div class="flex h-4 flex-row">
      <LeafIconComponent class="mx-2" />
      <span class="mr-2 flex text-lg font-bold">7,8/10</span>
      <span class="mt-2 flex text-xs text-gray-500"
        >Selon notre référentiel RSE</span
      >
      <span
        class="mt-1 flex cursor-pointer"
        title="Message explicatif du référentiel RSE"
        ><InformationIconComponent
      /></span>
    </div>
  </div>

  <div class="mt-10 grid grid-cols-4 gap-4">
    <div class="flex items-center justify-center rounded-lg bg-white">
      <img
        :src="defaultImageFile"
        alt="Picture"
        class="items-center sm:mx-auto"
      />
    </div>
    <div class="col-span-3 rounded-lg bg-white">
      <img
        :src="defaultImageFile"
        alt="Picture"
        class="items-center sm:mx-auto"
      />
    </div>
  </div>
</template>
<script lang="ts" setup>
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import { getImage } from '@/vuejs/services/utils'
import defaultImage from '@/vuejs/assets/img/default-image.png'
import { ref } from 'vue'
import LeafIconComponent from '@/vuejs/modules/shared/icon/LeafIconComponent.vue'
import InformationIconComponent from '@/vuejs/modules/shared/icon/InformationIconComponent.vue'

const defaultImageFile = getImage(defaultImage)

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
