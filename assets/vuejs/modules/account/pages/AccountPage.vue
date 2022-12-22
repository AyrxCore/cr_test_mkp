<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div class="xs:w-[100%] m-auto my-4 max-w-screen-2xl flex-1 px-4 sm:px-8">
      <breadcrumb-shared-component :current-page="'Mon compte'" />
      <div class="w-[100%] max-w-screen-2xl">
        <ContactUsButtonComponent/>
      </div>
      <a
        href="/app/home"
        class="my-4 lg:my-7 flex items-center text-[14px] text-secondary"
      >
        <ArrowLeftIconComponent class="mr-2"/>
        Retour sur la page d'accueil
      </a>
      <div class="m-auto max-w-screen-2xl">
        <slot name="header"/>
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
                  <ChevronRightIconComponent :stroke-color="'#5E6875'"/>
                  Service adhérents
                </span>
                <span class="ml-6 font-bold">04 37 65 06 21</span>
              </p>
              <p class="mt-4 items-center text-[16px] text-gray-500">
                <span class="flex">
                  <ChevronRightIconComponent :stroke-color="'#5E6875'"/>
                  Votre animateur:
                </span>
                <span class="ml-6 flex"> Nom Animateur</span>
                <span class="ml-6 flex font-bold">animation@qantis.co</span>
              </p>
            </div>
            <AccountSidebarBlock
                :items="[
                  {name: 'Historiques de commandes',id: AccountPageList.ORDERS_HISTORY, url: AccountPageList.ORDERS_HISTORY},
                  {name: 'Factures',id: '', url:  AccountPageList.ORDERS_HISTORY},
                  {name: 'Paniers sauvegardés',id: AccountPageList.SAVED_CARTS, url:  AccountPageList.SAVED_CARTS},
                  {name: 'Bons de livraison', id: '',url:  AccountPageList.ORDERS_HISTORY},
                  {name: 'Validation de commandes',id: AccountPageList.ORDERS_VALIDATION,url:  AccountPageList.ORDERS_VALIDATION},
              ]"
                :title="'Mes Commandes'"
            />
            <AccountSidebarBlock
                :items="[
                  {name: 'Coordonnées',id: AccountPageList.CONTACT_INFORMATION, url: AccountPageList.CONTACT_INFORMATION},
                  {name: 'Liste de produits favoris',id: AccountPageList.FAVORIS_LIST, url: AccountPageList.FAVORIS_LIST},
              ]"
                title="Mon profil"
            />
            <AccountSidebarBlock
                :items="[
                  {name: 'Utilisateurs',id: '', url: AccountPageList.ACCOUNT},
                  {name: 'Adresses',id: AccountPageList.ADDRESSES, url: AccountPageList.ADDRESSES},
                  {name: 'Directions - circuits de validation',id: '', url: AccountPageList.ACCOUNT},
                  {name: 'Statuts',id: '', url: AccountPageList.ACCOUNT},
              ]"
                title="Mon organisation"
            />
            <AccountSidebarBlock
                :items="[
                  {name: 'Statistiques de consommation',id: '', url: AccountPageList.ACCOUNT},
              ]"
                title="Statistiques"
            />
          </div>

          <div class="col-span-3">
            <slot name="right-side"/>
          </div>
        </div>
        <div class="items-center p-6 text-center">
          <p class="text-sm md:text-base lg:text-lg text-gray-500">
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

import {AccountPageList, baseUrl} from '@/vuejs/modules/account/routerAccount'
import ArrowLeftIconComponent from '@/vuejs/modules/shared/icon/ArrowLeftIconComponent.vue'
import ChevronRightIconComponent from '@/vuejs/modules/shared/icon/ChevronRightIconComponent.vue'
import {ref} from 'vue'
import {useUserStore} from "@/vuejs/stores/user";
import {storeToRefs} from "pinia";
import AccountSidebarBlock from "@/vuejs/modules/account/components/AccountSidebarBlock.vue";

const userStore = useUserStore()
const {user} = storeToRefs(userStore)

</script>
<style>
.page-principal-title {
  @apply text-[23px] md:text-[29px] lg:text-[35px];
}
</style>
