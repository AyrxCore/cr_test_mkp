<template>
  <AccountPage>
    <template #right-side>
      <h3 class="primary mb-2 text-[35px]">Adresses</h3>
      <!-- Bloc adresse de facturation par défaut -->
      <h3 class="primary mb-2 text-[25px]">Votre adresse de facturation</h3>
      <span class="text-gray-500 text-lg">Votre adresse de facturation par défaut est : </span>
      <div class="py-3 px-3 justify-between bg-white rounded-lg flex text-gray-500 mb-8 items-center">
        <div>
          <span>QANTIS, 185 allée des Cyprès, 69760 LIMONEST, FRANCE</span>
        </div>
        <div class="float-right text-white px-2 py-1 w-fit">
          <button><EditIconComponent /></button>
        </div>
      </div>
      <!-- Fin bloc adresse de facturation par défaut -->

      <!-- Bloc adresse de livraison préférée -->
      <h3 class="primary mb-2 text-[25px]">Votre adresse de livraison préférée</h3>
      <span class="text-gray-500 text-lg">Votre adresse de livraison par défaut est : : </span>
      <div class="py-3 px-3 justify-between bg-white rounded-lg flex text-gray-500 mb-8 items-center">
        <div>
          <span>QANTIS, 185 allée des Cyprès, 69760 LIMONEST, FRANCE</span>
        </div>
        <div class="float-right text-white px-2 py-1 w-fit">
          <button><EditIconComponent /></button>
        </div>
      </div>
      <!-- Fin bloc adresse de livraison préférée -->
      <!-- Bloc liste des adresses -->
      <h3 class="primary mb-2 text-[25px]">Autres adresses</h3>
      <div class="py-3 px-3 grid bg-white rounded-lg flex text-gray-500 mb-8 ">
        <div class="flex justify-between">
          <div class="inline-flex bg-white  rounded-md w-full">
              <label class="text-gray-500 flex-none">Recherche:</label>
              <input v-model="searchQuery" type="text"  name="query" class="rounded-md border border-gray-300 ml-2 w-3/6 h-[27px]"/>
          </div>
          <div class="text-white px-2 py-1 w-fit flex">
            <DefaultButtonComponent
              :btn-text-color="'text-gray-500'"
              :btn-color="'bg-transparent'"
              :rounded="'rounded-md'"
              class="border border-gray-600 text-gray-500 py-2"
            >
              Précédent
            </DefaultButtonComponent>
            <div class="text-gray-500 px-4 py-2 border items-center border-gray-300 rounded-md h-[32px] mr-2">
              <span>1</span>
            </div>
            <DefaultButtonComponent
              :btn-text-color="'text-gray-500'"
              :btn-color="'bg-transparent'"
              :rounded="'rounded-md'"
              class="border border-gray-600 text-gray-500 py-2"
            >
              Suivant
            </DefaultButtonComponent>
          </div>
        </div>
        <div class="w-full">
          <table class="table-auto w-full">
            <thead>
              <tr class="text-left">
                <th class="p-5">N°</th>
                <th class="p-5">Raison sociale</th>
                <th class="p-5">Adresse postale</th>
                <th class="p-5">Code postal</th>
                <th class="p-5">Ville</th>
                <th class="p-5">Code service</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(address, key) in listAddresses" :key="key">
                <td class="p-5">{{address.number}}</td>
                <td class="p-5">{{address.enterprise}}</td>
                <td class="p-5">{{address.postal_address}}</td>
                <td class="p-5">{{address.postal_code}}</td>
                <td class="p-5">{{address.city}}</td>
                <td></td>
                <td>
                  <div v-if="!address.default" class="flex">
                    <button><EditIconComponent class="mr-2" /></button>
                    <button><TrashIconComponent :stroke-color="'#9866ff'"/></button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <!-- Fin bloc liste des adresses -->

    </template>
  </AccountPage>
</template>
<script lang="ts" setup>

import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'
import EditIconComponent from '@/vuejs/modules/shared/icon/EditIconComponent.vue'
import { computed, ref } from 'vue'
import TrashIconComponent from '@/vuejs/modules/shared/icon/TrashIconComponent.vue'
import DefaultButtonComponent from '@/vuejs/modules/shared/DefaultButtonComponent.vue'
import { AccountPageList } from '@/vuejs/modules/account/routerAccount'

const tab = computed(() => {
  return AccountPageList.ADDRESSES
})

const searchQuery = ref<string>('')
const listAddresses = ref<Array<any>>([
  {
    number: 1,
    enterprise: 'QANTIS',
    postal_address: '185 allées des Cyprès',
    postal_code: '69760',
    city: 'LIMONEST',
    service_code: '',
    default: true,
  },
  {
    number: 2,
    enterprise: 'QANTIS UK',
    postal_address: '10 Downing St',
    postal_code: 'SW1A 2AA',
    city: 'LONDON, UK',
    service_code: '',
    default: false,
  }
])

</script>

<style scoped></style>
