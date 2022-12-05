<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div class="xs:w-[100%] m-auto my-4 max-w-screen-2xl flex-1 sm:px-8 text-cotext">
      <breadcrumb-shared-component :current-page="'Mon compte'" />
      <div class="w-[100%] max-w-screen-2xl">
        <ContactUsButtonComponent />
      </div>
      <a href="/app/home" class="text-purple-600 flex items-center my-7 text-[14px]">
        <ArrowLeftIconComponent class="mr-2"/>
        Retour sur la page d'accueil
      </a>
      <div class="m-auto max-w-screen-2xl">
        <slot name="header" />
        <div class="grid grid-cols-4 gap-11 mt-10">
          <div class="grid gap-4 flex">
            <div class="rounded-lg bg-white p-7">
              <h3 class="primary mb-2 text-[20px]">
                {{ user.firstname }} <span class="uppercase">{{ user.lastname }}</span>
              </h3>
              <p class="text-gray-500 font-bold"> Qantis </p>
              <p class="text-gray-500 mb-4"> Statut:  </p>
              <a href="#" class="text-gray-500 border-b-2 border-gray-500 hover:border-b-2 hover:border-purple-600"> Deconnexion </a>
            </div>
            <div class="rounded-lg bg-white p-7">
              <h3 class="primary mb-2 text-[20px]">
                Mes contacts
              </h3>
              <p class="text-gray-500 items-center text-[16px]">
                <span class="flex">
                  <ChevronRightIconComponent :stroke-color="'#5E6875'"/>
                  Service adhérents
                </span>
                <span class="font-bold ml-6">04 37 65 06 21</span>
              </p>
              <p class="text-gray-500 items-center text-[16px] mt-4">
                <span class="flex">
                  <ChevronRightIconComponent :stroke-color="'#5E6875'"/>
                  Votre animateur:
                </span>
                <span class="flex ml-6"> Nom Animateur</span>
                <span class="font-bold flex ml-6">animation@qanti.co</span>
              </p>
            </div>
            <div v-for="(menu, key) in meenuItems" :key="key" class="rounded-lg bg-white py-7 pl-7 pr-4">
              <h3 class="primary mb-2 text-[20px]">{{menu.title}}</h3>
              <p  v-for="(item , keyItem) in menu.items"
                  :key="keyItem"
                  class="flex items-center flex mb-3"
              >
                <ChevronRightIconComponent :stroke-color="'#5E6875'"/>
                <a
                  :href="item.url"
                  class="text-gray-500 underline decoration-2 underline-offset-4 text-[16px] hover:decoration-purple-600"
                  :class="{
                    'decoration-purple-600' : selectedTab === item.id,
                    'text-purple-600' : selectedTab === item.id,
                  }"
                >
                  {{item.name}}
                </a>
              </p>
            </div>
          </div>

          <div class="col-span-3">
            <slot name="right-side" />
          </div>
        </div>
        <div class="items-center text-center p-6">
          <p class="text-lg text-gray-500">
            Les informations liées à votre compte restent strictement confidentielles et ne sont utilisées que conformément à notre
            <a href="#" class="underline decoration-2 font-bold"> Politique de confidentialité</a>
          </p>
        </div>
      </div>
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import {user} from '@/vuejs/modules/account'
import {AccountPageList, baseUrl} from '@/vuejs/modules/account/routerAccount'
import ArrowLeftIconComponent from '@/vuejs/modules/shared/icon/ArrowLeftIconComponent.vue'
import ChevronRightIconComponent from '@/vuejs/modules/shared/icon/ChevronRightIconComponent.vue'
import { ref } from 'vue'

const props = defineProps({
  selectedTab: {
    required: true,
    type: String
  },
})

const meenuItems = ref ([
  {
    title: 'Mes commandes',
    items: [
      {
        name: 'Historiques de commandes',
        url: baseUrl + AccountPageList.ORDERS_HISTORY,
        id: AccountPageList.ORDERS_HISTORY
      },
      {
        name: 'Factures',
        url: '',
        id: ''
      },
      {
        name: 'Paniers sauvegardés',
        url: baseUrl + AccountPageList.SAVED_CARTS,
        id: AccountPageList.SAVED_CARTS
      },
      {
        name: 'Bons de livraison',
        url: '',
        id: ''
      },
      {
        name: 'Validation de commandes',
        url: baseUrl + AccountPageList.ORDERS_VALIDATION,
        id: AccountPageList.ORDERS_VALIDATION
      }
    ]
  },
  {
    title: 'Mon profil',
    items: [
      {
        name: 'Coordonnées',
        url: baseUrl + AccountPageList.CONTACT_INFORMATION,
        id: AccountPageList.CONTACT_INFORMATION
      },
      {
        name: 'Liste de produits favoris',
        url: baseUrl + AccountPageList.FAVORIS_LIST,
        id: AccountPageList.FAVORIS_LIST
      }
    ]
  },
  {
    title: 'Mon organisation',
    items: [
      {
        name: 'Utilisateurs',
        url: '',
        id: ''
      },
      {
        name: 'Adresses',
        url: baseUrl + AccountPageList.ADDRESSES,
        id: AccountPageList.ADDRESSES
      },
      {
        name: 'Directions - circuits de validation',
        url: '',
        id: ''
      },
      {
        name: 'Statuts',
        url: '',
        id: ''
      }
    ]
  },
  {
    title: 'Statistiques',
    items: [
      {
        name: 'Statistiques de consommation',
        url: '',
        id: ''
      }
    ]
  }
])
</script>

<style scoped></style>
