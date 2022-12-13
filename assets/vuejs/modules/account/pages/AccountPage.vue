<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div class="xs:w-[100%] m-auto my-4 max-w-screen-2xl flex-1 sm:px-8">
      <breadcrumb-shared-component :current-page="'Mon compte'" />
      <div class="w-[100%] max-w-screen-2xl">
        <ContactUsButtonComponent />
      </div>
      <a
        href="/app/home"
        class="my-7 flex items-center text-[14px] text-purple-600"
      >
        <ArrowLeftIconComponent class="mr-2" />
        Retour sur la page d'accueil
      </a>
      <div class="m-auto max-w-screen-2xl">
        <slot name="header" />
        <div class="mt-10 grid grid-cols-4 gap-11">
          <div class="flex grid gap-4">
            <div class="rounded-lg bg-white p-7">
              <h3 class="primary mb-2 text-[20px]">
                {{ user.firstname }}
                <span class="uppercase">{{ user.lastname }}</span>
              </h3>
              <p class="font-bold text-gray-500">Qantis</p>
              <p class="mb-4 text-gray-500">Statut:</p>
              <a
                href="#"
                class="border-b-2 border-gray-500 text-gray-500 hover:border-b-2 hover:border-purple-600"
                @click="onLogout"
              >
                Deconnexion
              </a>
            </div>
            <div class="rounded-lg bg-white p-7">
              <h3 class="primary mb-2 text-[20px]">Mes contacts</h3>
              <p class="items-center text-[16px] text-gray-500">
                <span class="flex">
                  <ChevronRightIconComponent :stroke-color="'#5E6875'" />
                  Service adhérents
                </span>
                <span class="ml-6 font-bold">04 37 65 06 21</span>
              </p>
              <p class="mt-4 items-center text-[16px] text-gray-500">
                <span class="flex">
                  <ChevronRightIconComponent :stroke-color="'#5E6875'" />
                  Votre animateur:
                </span>
                <span class="ml-6 flex"> Nom Animateur</span>
                <span class="ml-6 flex font-bold">animation@qantis.co</span>
              </p>
            </div>
            <div
              v-for="(menu, key) in meenuItems"
              :key="key"
              class="rounded-lg bg-white py-7 pl-7 pr-4"
            >
              <h3 class="primary mb-2 text-[20px]">{{ menu.title }}</h3>
              <p
                v-for="(item, keyItem) in menu.items"
                :key="keyItem"
                class="mb-3 flex flex items-center"
              >
                <ChevronRightIconComponent :stroke-color="'#5E6875'" />
                <a
                  :href="item.url"
                  class="text-[16px] text-gray-500 underline decoration-2 underline-offset-4 hover:decoration-purple-600"
                  :class="{
                    'decoration-purple-600': selectedTab === item.id,
                    'text-purple-600': selectedTab === item.id,
                  }"
                >
                  {{ item.name }}
                </a>
              </p>
            </div>
          </div>

          <div class="col-span-3">
            <slot name="right-side" />
          </div>
        </div>
        <div class="items-center p-6 text-center">
          <p class="text-lg text-gray-500">
            Les informations liées à votre compte restent strictement
            confidentielles et ne sont utilisées que conformément à notre
            <a href="#" class="font-bold underline decoration-2">
              Politique de confidentialité</a
            >
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

import { AccountPageList, baseUrl } from '@/vuejs/modules/account/routerAccount'
import ArrowLeftIconComponent from '@/vuejs/modules/shared/icon/ArrowLeftIconComponent.vue'
import ChevronRightIconComponent from '@/vuejs/modules/shared/icon/ChevronRightIconComponent.vue'
import { ref } from 'vue'
import {useUserStore} from "@/vuejs/stores/user";
import {storeToRefs} from "pinia";

const props = defineProps({
  selectedTab: {
    required: true,
    type: String,
  },
})
const userStore = useUserStore()
const {user} = storeToRefs(userStore)
const meenuItems = ref([
  {
    title: 'Mes commandes',
    items: [
      {
        name: 'Historiques de commandes',
        url: baseUrl + AccountPageList.ORDERS_HISTORY,
        id: AccountPageList.ORDERS_HISTORY,
      },
      {
        name: 'Factures',
        url: '',
        id: '',
      },
      {
        name: 'Paniers sauvegardés',
        url: baseUrl + AccountPageList.SAVED_CARTS,
        id: AccountPageList.SAVED_CARTS,
      },
      {
        name: 'Bons de livraison',
        url: '',
        id: '',
      },
      {
        name: 'Validation de commandes',
        url: baseUrl + AccountPageList.ORDERS_VALIDATION,
        id: AccountPageList.ORDERS_VALIDATION,
      },
    ],
  },
  {
    title: 'Mon profil',
    items: [
      {
        name: 'Coordonnées',
        url: baseUrl + AccountPageList.CONTACT_INFORMATION,
        id: AccountPageList.CONTACT_INFORMATION,
      },
      {
        name: 'Liste de produits favoris',
        url: baseUrl + AccountPageList.FAVORIS_LIST,
        id: AccountPageList.FAVORIS_LIST,
      },
    ],
  },
  {
    title: 'Mon organisation',
    items: [
      {
        name: 'Utilisateurs',
        url: '',
        id: '',
      },
      {
        name: 'Adresses',
        url: baseUrl + AccountPageList.ADDRESSES,
        id: AccountPageList.ADDRESSES,
      },
      {
        name: 'Directions - circuits de validation',
        url: '',
        id: '',
      },
      {
        name: 'Statuts',
        url: '',
        id: '',
      },
    ],
  },
  {
    title: 'Statistiques',
    items: [
      {
        name: 'Statistiques de consommation',
        url: '',
        id: '',
      },
    ],
  },
])

const onLogout = async (e: Event): Promise<void> => {
  e.preventDefault()
  await userStore.logout() &&
  location.reload()
}
</script>

<style scoped></style>
