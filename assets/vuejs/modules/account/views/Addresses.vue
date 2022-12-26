<template>
  <AccountPage>
    <template #right-side>
      <h3 class="primary mb-2 text-[35px]">Adresses</h3>
      <!-- Bloc adresse de facturation par défaut -->
      <h3 class="primary mb-2 text-[25px]">Votre adresse de facturation</h3>
      <span class="text-lg text-gray-500"
        >Votre adresse de facturation par défaut est :
      </span>
      <div
        class="mb-8 flex items-center justify-between rounded-lg bg-white py-3 px-3 text-gray-500"
      >
        <div>
          <span>QANTIS, 185 allée des Cyprès, 69760 LIMONEST, FRANCE</span>
        </div>
        <div class="float-right w-fit px-2 py-1 text-white">
          <button><EditIconComponent /></button>
        </div>
      </div>
      <!-- Fin bloc adresse de facturation par défaut -->

      <!-- Bloc adresse de livraison préférée -->
      <h3 class="primary mb-2 text-[25px]">
        Votre adresse de livraison préférée
      </h3>
      <span class="text-lg text-gray-500"
        >Votre adresse de livraison par défaut est : :
      </span>
      <div
        class="mb-8 flex items-center justify-between rounded-lg bg-white py-3 px-3 text-gray-500"
      >
        <div>
          <span>QANTIS, 185 allée des Cyprès, 69760 LIMONEST, FRANCE</span>
        </div>
        <div class="float-right w-fit px-2 py-1 text-white">
          <button><EditIconComponent /></button>
        </div>
      </div>
      <!-- Fin bloc adresse de livraison préférée -->
      <!-- Bloc liste des adresses -->
      <h3 class="primary mb-2 text-[25px]">Autres adresses</h3>
      <div class="mb-8 flex grid rounded-lg bg-white py-3 px-3 text-gray-500">
        <div class="flex justify-between">
          <div class="inline-flex w-full rounded-md bg-white">
            <label class="flex-none text-gray-500">Recherche:</label>
            <input
              v-model="searchQuery"
              type="text"
              name="query"
              class="ml-2 h-[27px] w-3/6 rounded-md border border-gray-300"
            />
          </div>
          <div class="flex w-fit px-2 py-1 text-white">
            <DefaultButtonComponent
              :btn-text-color="'text-gray-500'"
              :btn-color="'bg-transparent'"
              :rounded="'rounded-md'"
              class="border border-gray-600 py-2 text-gray-500"
            >
              Précédent
            </DefaultButtonComponent>
            <div
              class="mr-2 h-[32px] items-center rounded-md border border-gray-300 px-4 py-2 text-gray-500"
            >
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
                <th></th>
              </tr>
            </thead>
            <tbody v-if="!isloading">
              <tr v-for="(address, key) in adresses" :key="key">
                <td class="p-5">{{address.id}}</td>
                <td class="p-5">{{address.company}}</td>
                <td class="p-5">{{address.street}}</td>
                <td class="p-5">{{address.postcode}}</td>
                <td class="p-5">{{address.city}}</td>
                <td>
                  <div v-if="!address.default" class="flex">
                    <button><EditIconComponent class="mr-2" /></button>
                    <button>
                      <TrashIconComponent :stroke-color="'#9866ff'" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
            <tbody v-else>
              <tr>
                <td colspan="6">
                  <LoaderSharedComponent/>
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
import {computed, onMounted, ref} from 'vue'
import TrashIconComponent from '@/vuejs/modules/shared/icon/TrashIconComponent.vue'
import DefaultButtonComponent from '@/vuejs/modules/shared/DefaultButtonComponent.vue'
import {useCompanyStore} from "@/vuejs/stores/company";
import {storeToRefs} from "pinia";
import LoaderSharedComponent from "@/vuejs/modules/shared/LoaderSharedComponent.vue";
const companyStore = useCompanyStore()
const {adresses, isloading} = storeToRefs(companyStore)

onMounted(async () => {
  companyStore.isloading = !companyStore.adresses.length
  await companyStore.getAdresses()
  companyStore.isloading = false
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
