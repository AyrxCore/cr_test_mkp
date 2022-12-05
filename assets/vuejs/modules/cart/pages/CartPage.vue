<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div class="xs:w-[100%] m-auto my-4 max-w-screen-2xl flex-1 sm:px-8">
      <breadcrumb-shared-component :current-page="'Panier'" />
      <div class="w-[100%] max-w-screen-2xl">
        <ContactUsButtonComponent />
      </div>
      <div class="m-auto my-2 w-[100%] max-w-screen-2xl">
        <div class="tabs clearfix flex w-max" data-tabgroup="first-tab-group">
          <div
              v-for="(tab, key) in tabs"
              :key="key"
              class="px-3 text-lg text-gray-500  border-b-2 border-gray-300 hover:border-b-2 hover:border-purple-500"
              :class="{'border-b-2 border-purple-500': tab.id === selectedTab.value}"
          >
            <a :href="tab.url" :class="{'primary': tab.id === selectedTab.value}">{{tab.name}}</a>
          </div>
        </div>
        <div class="grid grid-cols-4 gap-4 mt-10">
          <div class="col-span-3">
            <slot name="left-side" />
          </div>
          <div class="rounded-lg">
            <slot name="right-side" />
          </div>
        </div>

      </div>
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import { ref } from 'vue'
import { CartPageList } from '@/vuejs/modules/cart/routerCart'
import {
  TAB_ADRESSES,
  TAB_BON_COMMANDE,
  TAB_CONFIRMATION,
  TAB_PAIEMENT,
  TAB_PANIER,
} from '@/vuejs/modules/cart'

const props = defineProps({
  selectedTab: {
    required: true,
    type: String,
    default: TAB_PANIER
  },
})

const tabs = ref([
  {
    id: TAB_PANIER,
    name: 'Panier',
    url: '/app/cart/' + CartPageList.RECAP,
  },
  {
    id: TAB_BON_COMMANDE,
    name: 'Bon de commande',
    url: '/app/cart/'  + CartPageList.RECAP,
  },
  {
    id: TAB_ADRESSES,
    name: 'Adresses',
    url: '/app/cart/'  + CartPageList.ADDRESSES,
  },
  {
    id: TAB_PAIEMENT,
    name: 'Paiement',
    url: '/app/cart/'  + CartPageList.RECAP,
  },
  {
    id: TAB_CONFIRMATION,
    name: 'Confirmation',
    url: '/app/cart/'  + CartPageList.CONFIRMATION,
  },
])

</script>

<style scoped></style>
